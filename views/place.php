<h1 class="place-title"><?=$place['title']?></h1>
<?php if ($admin) : ?>
	<p style="text-align:center"><a href="/+/<?=$place['id']?>">изменить</a></p>
<?php endif; ?>
<div class="fotorama" data-allowfullscreen="native" data-width="100%" class="place__photos">
	<?php foreach ($place['images'] as $image) : ?>
		<img src="<?=$image['url']?>" data-caption="<?=$image['caption']?>">
	<?php endforeach;?>
</div>

<?=$place['text']?>
