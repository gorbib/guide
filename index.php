<?php

require 'vendor/autoload.php';

$config = include('config/config.php');

// Database connection
Flight::register('db', 'PDO', array(
    'mysql:host='.$config['db']['host'].';dbname='.$config['db']['name'],
    $config['db']['user'],
    $config['db']['password'],
    array(
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_CASE => PDO::CASE_LOWER,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
    )
));

// Some stuff for all pages
Flight::route('*', function () use ($config) {

    $sth = Flight::db()->prepare("
        SELECT *,
        (
            select count(*)
            from `places`
            where `places`.`category` = `categories`.`id`
        ) as 'count'
        FROM `categories` order by `name` asc
    ");
    $sth->execute();
    $categories = $sth->fetchAll();

    Flight::view()->set('categories', $categories);

    Flight::view()->set('description', $config['description']);


    Flight::view()->set('admin', $config['admin']);

    return true; // pass to next route
});


// Index page
Flight::route('/', function () {
    Flight::render('index', null, 'content');
    Flight::render('layout', [
        'title' => 'Путеводитель достопримечательностей Качканара'
    ]);
});


// JSON data for map
Flight::route('/json', function () {
    $sth = Flight::db()->prepare("SELECT
        id,
        X(`coordinates`) as 'long',
        Y(`coordinates`) as 'lat',
        category,
        alias,
        title,
        description,
        text,
        (select `url` from `images` where `place` = `places`.`id` order by `order` asc limit 1) as 'image'
    FROM `places`");

    $sth->execute();

    $places = $sth->fetchAll();

    Flight::json($places);
});

// Sitemap.xml
Flight::route('/sitemap.xml', function () {
    header("Content-Type: application/xml");

    $sth = Flight::db()->prepare("SELECT `alias` FROM `places` UNION SELECT `alias` FROM `categories`");

    $sth->execute();

    Flight::render('sitemap', ['aliases' => $sth->fetchAll()]);
});



// Place or category page
Flight::route('/@alias:[A-z0-9-]+', function ($alias) {
    $sth = Flight::db()->prepare("SELECT * from `places` where `alias` = :alias");
    $sth->bindValue(':alias', $alias);

    $sth->execute();

    $place = $sth->fetch();

    if ($place) {
        $sth = Flight::db()->prepare("SELECT * from `images` where `place` = :place order by `order` asc");
        $sth->bindValue(':place', $place['id']);
        $sth->execute();
        $place['images'] = $sth->fetchAll();

        Flight::render('place', ['place' => $place], 'content');
        Flight::render('layout', [
            'title' => $place['title'].' в Качканаре',
            'image'=> $place['images'][0]['url'],
            'description' => $place['description']
        ]);
    } else {
        $sth = Flight::db()->prepare("SELECT * from `categories` where `alias` = :alias");
        $sth->bindValue(':alias', $alias);

        $sth->execute();

        $category = $sth->fetch();

        if ($category) {
            $sth = Flight::db()->prepare("
                SELECT *,
                (select `url` from `images` where `place` = `places`.`id` order by `order` asc limit 1) as 'image'
                from `places`
                where `category` = :id
            ");
            $sth->bindValue(':id', $category['id']);
            $sth->execute();
            $places = $sth->fetchAll();

            Flight::render('list', [
                'places' => $places,
                'category' => $category
            ], 'content');

            Flight::render('layout', [
                'title' => $category['name'].' в Качканаре'
            ]);
        } else {
            Flight::notFound();
        }
    }
});


// All admin pages
Flight::route('/\+/*', function () use ($config) {
    if ($config['admin']) {
        return true;
    } else {
        Flight::redirect('/');
    }
});

// Edit form
Flight::route('GET /\+(/@id:[0-9]+)', function ($id) {
    if (isset($id)) {
        $sth = Flight::db()->prepare("SELECT
            id, X(`coordinates`) as 'long',
            Y(`coordinates`) as 'lat',
            category,
            alias,
            title,
            description,
            text
        FROM `places` where `id` = :id");

        $sth->bindValue(':id', $id);
        $sth->execute();
        $place = $sth->fetch();

        if ($place) {
            $sth = Flight::db()->prepare("SELECT * from `images` where `place` = :place order by `order` asc ");
            $sth->bindValue(':place', $place['id']);
            $sth->execute();
            $place['images'] = $sth->fetchAll();

            Flight::render('edit-place', ['place' => $place], 'content');
            Flight::render('layout', ['title' => 'Изменение места']);
        } else {
            Flight::notFound();
        }
    } else {
        Flight::render('edit-place', ['place' => $place], 'content');
        Flight::render('layout', ['title' => 'Добавление места']);
    }
});



// Save new or edited place
Flight::route('POST /\+(/@id:[0-9]+)', function ($placeId) {

    // If we have an ID, then update place information
    if (isset($placeId)) {
        $sth = Flight::db()->prepare("
            UPDATE `places`
            SET
                `coordinates` = GeomFromText(:coordinates),
                `alias` = :alias,
                `title` = :title,
                `description` = :description,
                `text` = :text,
                `category` = :category
            WHERE `id` = :id
        ");

        $sth->bindValue(':id', $placeId);
    } else {
        // Else create a new place
        $sth = Flight::db()->prepare("
            INSERT INTO `places` (`coordinates`, `alias`, `title`, `description`, `text`, `category`)
            VALUES (GeomFromText(:coordinates), :alias, :title, :description, :text, :category)
        ");
    }

    $sth->bindValue(':title', htmlspecialchars($_POST['title']));
    $sth->bindValue(':alias', $_POST['alias']);
    $sth->bindValue(':coordinates', 'POINT('.$_POST['coordinates'].')');
    $sth->bindValue(':description', $_POST['description']);
    $sth->bindValue(':text', $_POST['text']);
    $sth->bindValue(':category', $_POST['category']);
    $sth->execute();

    // Get id of added place
    if (empty($placeId)) {
        $placeId = Flight::db()->lastInsertId();
    }

    // Update images
    $images = json_decode($_POST['images']);
    if (is_array($images)) {
        // Remove all images from place
        // (Not actually removing, just set place to not existing — 0)
        // Why? Because on update this image need to be exist
        $sth = Flight::db()->prepare("UPDATE `images` SET `place` = 0 where `place`= :place_id");
        $sth->bindValue(':place_id', $placeId);
        $sth->execute();

        // Set place to current for all images
        foreach ($images as $i => $image) {
            $sth = Flight::db()->prepare("
                UPDATE `images` SET
                    `place` = :place_id,
                    `caption` = :caption,
                    `order` = :order
                WHERE `id` = :image_id
            ");
            $sth->bindValue(':place_id', $placeId);
            $sth->bindValue(':image_id', $image->id);
            $sth->bindValue(':caption', htmlspecialchars($image->caption));
            $sth->bindValue(':order', $i);
            $sth->execute();
        }
    }

    // Open place page
    Flight::redirect('/'.$_POST['alias']);
});

/**
 * List of all categories
 */
Flight::route('GET /\+/categories', function () {
    $sth = Flight::db()->prepare("SELECT * from `categories` order by `name` asc");
    $sth->execute();
    Flight::render('edit-categories', ['categoriesList' => $sth->fetchAll()], 'content');
    Flight::render('layout', ['title' => 'Правка категорий']);
});

/**
 * Add category
 */
Flight::route('POST /\+/categories', function () {
    $sth = Flight::db()->prepare("INSERT INTO `categories` (`name`, `alias`) VALUES (:name, :alias)");
    $sth->bindValue(':name', htmlspecialchars($_POST['name']));
    $sth->bindValue(':alias', $_POST['alias']);
    $sth->execute();

    Flight::redirect('/+/categories');
});

/**
 * Delete category
 */
Flight::route('DELETE /\+/categories/@id:[0-9]+', function ($id) {
    $sth = Flight::db()->prepare("SELECT count(*) as 'count' from `places` where `category` = :category");
    $sth->bindValue(':category', $id);
    $sth->execute();
    if ($sth->fetch()['count'] > 0) {
        Flight::json(['success' => false, 'error' => 'Category not empty']);
    } else {
        $sth = Flight::db()->prepare("delete from `categories` where `id`=:id");
        $sth->bindValue(':id', $id);
        $sth->execute();

        Flight::json(['success' => true]);
    }
});

/**
 * Uploading route
 *
 * Store image files, sended with $_FILES['image']
 *
 * Returns json with uploaded image information
 */
Flight::route('POST /\+/upload', function () use ($config) {

    /**
     * Initialization for Cloudinary
     *
     * http://cloudinary.com/
     *
     * Cloudinary — web service for store images, with extra functions, like
     * croping or face detection
     *
     * $config['cloudinary'] options need to be set in /config.php file
     */
    \Cloudinary::config(array(
        "cloud_name" => $config['cloudinary']['cloud'],
        "api_key" => $config['cloudinary']['key'],
        "api_secret" => $config['cloudinary']['api-secret']
    ));

    // Upload file to cloudinary server
    $upload = \Cloudinary\Uploader::upload(
        $_FILES['image']['tmp_name'],
        array(
            'folder' => 'guide',
            'crop' => 'limit',
            'width' => '2000'
        )
    );

    // Insert url of uploaded image in db
    $sth = Flight::db()->prepare("INSERT INTO `images` (`place`, `url`) VALUES (0, :url)");
    $sth->bindValue(':url', $upload['secure_url']);
    $sth->execute();

    // Add new image id to response
    $upload['id'] = Flight::db()->lastInsertId();

    Flight::json($upload);
});


// 404
Flight::map('notFound', function () {
    Flight::render('404', null, 'content');
    Flight::render('layout', [
        'title' => 'Нет такой страницы'
    ]);

    Flight::stop(404);
});


Flight::start();
