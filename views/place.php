<h1 class="page-title"><?=$place['title']?></h1>
<?php if ($admin) : ?>
	<p style="text-align:center"><a href="/+/<?=$place['id']?>">изменить</a></p>
<?php endif; ?>
<div class="fotorama" data-allowfullscreen="native" data-ratio="16/9" data-width="100%" data-fit="cover">
	<?php foreach ($place['images'] as $image) : ?>
		<img src="<?=$image['url']?>" data-caption="<?=$image['caption']?>" alt="<?=$place['title']?> в Качканаре. <?=$image['caption']?>">
	<?php endforeach;?>
</div>

<?=$place['text']?>
