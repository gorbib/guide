<div class="map"></div>
<h1 class="page-title"><?=$category['name']?></h1>
<div class="places-in-category">
	<?php foreach ($places as $place) : ?>
	<a class="places-in-category__place" href="/<?=$place['alias']?>" style="background-image: url('<?=$place['image']?>')">
		<div class="places-in-category__place__description"><?=$place['title']?></div>
	</a>
	<?php endforeach;?>

	<?php if(empty($places)) : ?>
		<p>В этой категории ничего нет</p>
	<?php endif; ?>
</div>
