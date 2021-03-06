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
    <meta property="og:url" content="//<?=$_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']?>">
    <meta property="og:image" content="<?=($image ? $image : '/images/og-index.jpg')?>">
    <meta property="og:site_name" content="Путеводитель по Качканару">
    <meta property="og:description" content="<?=$description?>">

    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">
    <link rel="mask-icon" href="/safari-pinned-tab.svg" color="#183f98">
    <meta name="msapplication-TileColor" content="#183f98">
    <meta name="theme-color" content="#ffffff">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/5.0.0/normalize.min.css">

    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=PT+Serif+Caption&amp;subset=cyrillic">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ilyabirman-likely/2.2.1/likely.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fotorama/4.6.4/fotorama.min.css">
    <?php if ($admin) : ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/medium-editor/5.22.2/css/medium-editor.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/medium-editor/5.22.2/css/themes/flat.min.css">
    <?php endif; ?>

    <link rel="stylesheet" href="/css/style.css?v=<?=filemtime('./css/style.css')?>">
</head>
<body>
    <aside class="sidebar">
        <img src="/images/mountain.svg" class="sidebar__mountain" alt="">
        <div class="sidebar__body">

            <header class="header">
                <label for="navigation-toggler" class="navigation-toggle"></label>
                <a class="logo" href="/">Качканар</a>
            </header>

            <input type="checkbox" id="navigation-toggler" hidden>

            <ul class="navigation">
            <?php foreach ($categories as $category) : ?>
            <li><a href="/<?=$category['alias']?>"><?=$category['name']?><sup><?=$category['count']?></sup></a></li>
            <?php endforeach;?>

            <?php if ($admin) : ?>
            <li><a href="/+/categories" class="navigation__button">изменить категории</a></li>
            <li><a href="/+/" class="navigation__button">добавить место</a></li>
            <?php endif; ?>
            </ul>
        </div>
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
                <p class="copyright">Информация собрана сотрудниками <a href="http://gorbib.org.ru" target="_blank">качканарской библиотеки им. Селянина</a> к&nbsp;60-летию города.
                <a href="mailto:gorbib@yandex.ru?subject=Путеводитель">Напишите нам</a>, если есть вопросы или предложения.</p>
                <div id="openstat4031444" class="footer__stat-block" title="Сколько человек было у нас на сайте в прошлом месяце; Сколько было вчера; И сколько прямо сейчас"></div>
                <div>Расскажите о путеводителе</div>
                <div class="likely likely-light" data-via="gorbib" data-url="https://вкачканаре.рф" data-title="Путеводитель по Качканару">
                    <div class="vkontakte">ВКонтакте</div>
                    <div class="facebook">фейсбук</div>
                    <div class="twitter">твиттер</div>
                    <div class="odnoklassniki">одноклассники</div>
                </div>
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
