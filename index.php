<?php

require 'vendor/autoload.php';

$config = include('config.php');

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

// Some stuf for all pages
Flight::route('*', function () use ($config) {

    $sth = Flight::db()->prepare("
        SELECT *,
        (select count(*)
        from `places`
        where `places`.`category` = `categories`.`id`
        ) as 'count'
        FROM `categories`
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
        (select `url` from `images` where `place` = `places`.`id` limit 1) as 'image'
    FROM `places`");

    $sth->execute();

    $places = $sth->fetchAll();

    Flight::json($places);
});



// Place or category page
Flight::route('/@alias:[A-z0-9-]+', function ($alias) {
    $sth = Flight::db()->prepare("SELECT * from `places` where `alias` = :alias");
    $sth->bindValue(':alias', $alias);

    $sth->execute();

    $place = $sth->fetch();

    if ($place) {
        $sth = Flight::db()->prepare("SELECT * from `images` where `place` = :place");
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
                (select `url` from `images` where `place` = `places`.`id` limit 1) as 'image'
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
            $sth = Flight::db()->prepare("SELECT * from `images` where `place` = :place");
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
Flight::route('POST /\+(/@id:[0-9]+)', function ($id) {

    // If we have an ID, then update place information
    if (isset($id)) {
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

        $sth->bindValue(':id', $id);
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

    Flight::redirect('/'.$_POST['alias']);
});

Flight::route('POST /\+/upload', function () {
    \Cloudinary::config(array(
        "cloud_name" => $config['cloudinary']['cloud'],
        "api_key" => $config['cloudinary']['key'],
        "api_secret" => $config['cloudinary']['api-secret']
    ));

    $upload = \Cloudinary\Uploader::upload(
        $_FILES['image']['tmp_name'],
        array(
            'folder' => 'guide',
            'crop' => 'limit',
            'width' => '2000'
        )
    );

    $sth = Flight::db()->prepare("
        INSERT INTO `images` (`id`, `place`, `url`, `caption`)
        VALUES (NULL, :place, :url, '')
    ");
    $sth->bindValue(':place', intval($_POST['place']));
    $sth->bindValue(':url', $upload['url']);
    $sth->execute();

    Flight::json($upload);
});

Flight::route('POST /\+/remove-image', function () {
    $sth = Flight::db()->prepare("
        DELETE FROM `images` WHERE `id` = :id
    ");
    $sth->bindValue(':id', intval($_POST['id']));
    $sth->execute();

    Flight::json(['status' => 'ok']);
});
Flight::route('POST /\+/edit-image', function () {
    $sth = Flight::db()->prepare("
        UPDATE `images` SET `caption` = :caption WHERE `id` = :id
    ");
    $sth->bindValue(':id', intval($_POST['id']));
    $sth->bindValue(':caption', htmlspecialchars($_POST['caption']));
    $sth->execute();

    Flight::json(['status' => 'ok']);
});

// 404
Flight::map('notFound', function () {
    Flight::render('404', null, 'content');
    Flight::render('layout', [
        'title' => 'Нет такой страницы'
    ]);
});


Flight::start();
