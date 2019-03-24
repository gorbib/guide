<h1 class="page-title"><?=$place['title']?></h1>
<?php if ($admin) : ?>
	<p style="text-align:center"><a href="/+/<?=$place['id']?>">изменить</a></p>
<?php endif; ?>
<div class="fotorama photo-gallery" data-allowfullscreen="native" data-nav="thumbs" data-ratio="16/9" data-width="100%" data-fit="contain">
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

<div class="place-location">
    <div class="place-location__map" data-lat="<?=$place['lat']?>" data-long="<?=$place['long']?>"></div>
    <div class="place-location__nearby-places-title">Места поблизости</div>
    <div class="place-location__nearby-places">
    <?php foreach ($nearbyPlaces as $nearbyPlace): ?>
    <a href="/<?=$nearbyPlace['alias']?>" class="nearby-place" style="background-image: url('<?=$nearbyPlace['image']?>')">
        <div class="nearby-place__title"><?=$nearbyPlace['title']?></div>
    </a>
    <?php endforeach; ?>
    </div>
</div>
