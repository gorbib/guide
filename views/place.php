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

<div class="place__category-meta">
    <div class="place__category-meta__left">
        Категория: <a href="/<?=$category['alias']?>"><?=$category['name']?></a>
    </div>
    <div class="likely likely-dark place__category-meta__right" data-via="gorbib">
        <div class="vkontakte">Расскажите в ВК</div>
        <div class="facebook">Фейсбуке</div>
        <div class="twitter">Твиттере</div>
        <div class="odnoklassniki">Ставьте класс</div>
    </div>
</div>
