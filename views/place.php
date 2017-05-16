<h1 class="page-title"><?=$place['title']?></h1>
<?php if ($admin) : ?>
	<p style="text-align:center"><a href="/+/<?=$place['id']?>">изменить</a></p>
<?php endif; ?>
<div class="fotorama" data-allowfullscreen="native" data-ratio="16/9" data-width="100%" data-fit="cover">
	<?php foreach ($images as $image) : ?>
		<img src="<?=$image['url']?>" data-caption="<?=$image['caption']?>" alt="<?=$place['title']?> в Качканаре. <?=$image['caption']?>">
	<?php endforeach;?>
</div>

<?=$place['text']?>

<div class="place__category-meta clearfix">
    <div class="place__category-meta__category">
        Категория: <a href="/<?=$category['alias']?>"><?=$category['name']?></a>
    </div>
    <div class="likely likely-dark place__category-meta__share">
        <div class="vkontakte">Поделитесь в ВК</div>
        <div class="facebook">фейсбуке</div>
        <div class="twitter" data-via="gorbib">твиттере</div>
        <div class="odnoklassniki">ставьте класс</div>
    </div>
</div>
