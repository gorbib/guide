<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?=$title?></title>

    <meta name="description" content="<?=$description?>">
    <meta property="og:title" content="<?=$title?>">
    <meta property="og:type" content="site">
    <meta property="og:url" content="//<?=$_SERVER[HTTP_HOST] . $_SERVER[REQUEST_URI]?>">
    <meta property="og:image" content="<?=($image ? $image : '/images/og-index.jpg')?>">
    <meta property="og:site_name" content="Путеводитель по Качканару">
    <meta property="og:description" content="<?=$description?>">

    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" href="/images/favicon-32x32.png" sizes="32x32">
    <link rel="icon" type="image/png" href="/images/favicon-16x16.png" sizes="16x16">
    <link rel="manifest" href="/manifest.json">
    <link rel="mask-icon" href="/images/safari-pinned-tab.svg" color="#3c3c3c">
    <meta name="theme-color" content="#ffffff">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/5.0.0/normalize.min.css">

    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=PT+Serif+Caption&amp;subset=cyrillic">
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ilyabirman-likely/2.2.1/likely.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fotorama/4.6.4/fotorama.min.css">
    <?php if ($admin) : ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/medium-editor/5.22.2/css/medium-editor.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/medium-editor/5.22.2/css/themes/flat.min.css">
    <?php endif; ?>
</head>
<body>
    <aside class="sidebar">
        <img src="/images/mountain.svg" class="sidebar__mountain" alt="">
        <ul class="sidebar__navigation">

            <a class="logo" href="/">Качканар</a>
            <?php foreach ($categories as $category) : ?>
            <li><a href="/<?=$category['alias']?>"><?=$category['name']?><sup><?=$category['count']?></sup></a></li>
            <?php endforeach;?>

            <?php if ($admin) : ?>
            <li><a href="/+/categories" class="sidebar__navigation__button">изменить категории</a></li>
            <li><a href="/+/" class="sidebar__navigation__button">добавить место</a></li>
            <?php endif; ?>
        </ul>
        <ul class="sidebar__social">
            <li><a class="fb" target="_blank" href="https://www.facebook.com/gorbib"></a></li>
            <li><a class="vk" target="_blank" href="https://vk.com/gorbib"></a></li>
            <li><a class="ig" target="_blank" href="https://www.instagram.com/gorbib/"></a></li>
            <li><a class="yt" target="_blank" href="https://www.youtube.com/user/kchlib"></a></li>
        </ul>
    </aside>

    <main class="main">
        <article class="content">
            <?php echo $content; ?>
        </article>

        <footer class="footer">
            <div class="footer__something">
                <div id="vk_groups" style="height: 400px;"></div>
            </div>
            <div class="footer__main">
                <p class="copyright">Собрали фотографии и написали текст библиотекари <a href="http://gorbib.org.ru" target="_blank">качканарской библиотеки им. Селянина</a> к&nbsp;60-летию города</p>
                <div id="openstat4031444" class=" footer__stat-block"></div>
            </div>
        </footer>
    </main>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    <script src="https://api-maps.yandex.ru/2.1/?lang=ru_RU"></script>
    <script src="/js/main.js"></script>

    <?php
    if (!empty($js)) {
        echo $js;
    }
    ?>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/fotorama/4.6.4/fotorama.js"></script>
    <script src="https://vk.com/js/api/openapi.js?139"></script>
    <script>
    VK.Widgets.Group("vk_groups", {mode: 3, width: "auto"}, 52689769);
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ilyabirman-likely/2.2.1/likely.js"></script>


    <!--Openstat-->
    <script>
    var openstat = { counter: 4031444, image: 90, color: "828282", next: openstat };
    (function(d, t, p) {
    var j = d.createElement(t); j.async = true; j.type = "text/javascript";
    j.src = ("https:" == p ? "https:" : "http:") + "//openstat.net/cnt.js";
    var s = d.getElementsByTagName(t)[0]; s.parentNode.insertBefore(j, s);
    })(document, "script", document.location.protocol);
    </script>
    <!--/Openstat-->

    <!-- For old browsers -->
    <script src="https://yastatic.net/browser-updater/v1/script.js"></script>
    <script>
    var yaBrowserUpdater = new ya.browserUpdater.init({
        "lang":"ru",
        "browsers":{
            "yabrowser":"15.12",
            "chrome":"54",
            "ie":"10",
            "opera":"41",
            "safari":"8",
            "fx":"49",
            "iron":"35",
            "flock":"Infinity",
            "palemoon":"25",
            "camino":"Infinity",
            "maxthon":"4.5",
            "seamonkey":"2.3"
        },
        "theme":"yellow"
    });
    </script>
</body>
</html>
