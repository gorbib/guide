<?='<?xml version="1.0" encoding="UTF-8"?>'?>

<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<?php foreach ($aliases as $alias) : ?>
    <url>
        <loc>https://<?=$_SERVER['SERVER_NAME']?>/<?=$alias['alias']?></loc>
        <changefreq>weekly</changefreq>
        <priority>1.0</priority>
    </url>
<?php endforeach;?>
</urlset>
