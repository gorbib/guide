<form action="/+/<?=$place['id']?>" method="post" style="padding: 30px" class="edit-place-form" data-place-id="<?=$place['id']?>">
    <p>
        <label>Название</label>
        <input type="text" name="title" placeholder="Название" class="edit-place-form__field edit-place-form__field_title" value="<?=$place['title']?>" required>
    </p>
    <p style="font-size: 16px;color:#555;display:flex">Адрес страницы: /<input type="text" name="alias" class="edit-place-form__field edit-place-form__field_alias" value="<?=$place['alias']?>" pattern="[a-z0-9-]+" placeholder="По английски, без пробелов" style="flex:1" required></p>
    <p>
        <label for="category">Раздел</label>
        <select name="category">
            <?php foreach ($categories as $category) : ?>
                <option value="<?=$category['id']?>" <?=($category['id'] == $place['category'])?'selected':''?>><?=$category['name']?></option>
            <?php endforeach; ?>
        </select>
    </p>
    <div class="map-preview"></div>
    <input type="hidden" name="coordinates" class="edit-place-form__field_coordinates" value="<?=$place['long']?> <?=$place['lat']?>">

    <div class="image-previews clearfix">
        <div class="image-previews__add-image-button upload-dropzone">
            <span>Добавь ещё фоток</span>
            <input type="file" class="upload-dropzone__input" multiple hidden>
        </div>
        <?php if (!empty($place['images'])) : ?>
            <?php foreach ($place['images'] as $image) : ?>
            <figure class="image-preview" data-image-id="<?=$image['id']?>">
                <span class="image-preview__remove-button">×</span>
                <img src="<?=$image['url']?>" class="image-preview__image">
                <figcaption>
                    <input placeholder="Подпись" class="image-preview__caption" value="<?=$image['caption']?>">
                </figcaption>
            </figure>
        <?php endforeach; ?>
        <?php endif; ?>
        <input type="hidden" name="images">
    </div>

    <p><input type="text" name="description" class="edit-place-form__field" placeholder="Краткое описание" value="<?=$place['description']?>" maxlength="150"></p>

    <textarea name="text" placeholder="Текст" class="edit-place-form__field edit-place-form__field_text"><?=$place['text']?></textarea>

    <div style="text-align: right;">
        <button class="btn"><?php if (isset($place['id'])) : ?>Обновить<?php else: ?>Добавить<?php endif;?></button>
    </div>
</form>

<script id="place-image-preview-template" type="text/x-handlebars-template">
    <figure class="image-preview" data-image-id="{{id}}">
        <span class="image-preview__remove-button">×</span>
        <img src="{{url}}" class="image-preview__image">
        <figcaption>
            <input placeholder="Подпись к фотографии" class="image-preview__caption" value="">
        </figcaption>
    </figure>
</script>

<?php ob_start() ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/medium-editor/5.22.2/js/medium-editor.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/handlebars.js/4.0.6/handlebars.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.5.1/Sortable.min.js"></script>
<script src="/js/admin.js"></script>
<?php Flight::view()->set('js', ob_get_clean()) ?>
