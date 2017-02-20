<form action="/+/<?=$place['id']?>" method="post" style="padding: 30px" class="edit-place-form" data-place-id="<?=$place['id']?>">
    <p>
        <label>Название</label>
        <input type="text" name="title" placeholder="Название" class="edit-place-form__field" value="<?=$place['title']?>" required>
    </p>
    <p style="font-size: 16px;color:#555">Адрес: <?=$_SERVER['SERVER_NAME']?>/<input type="text" name="alias" class="edit-place-form__field edit-place-form__field_alias" value="<?=$place['alias']?>" placeholder="По английски, без пробелов" required></p>
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

    <?php if(!empty($place['id'])) : ?>
    <div class="image-previews clearfix">
        <div class="image-previews__add-image-button upload-dropzone">
            <span>Добавь ещё фоток</span>
            <input type="file" class="upload-dropzone__input" multiple hidden>
        </div>
        <?php if(!empty($place['images'])) : ?>
            <?php foreach ($place['images'] as $image) : ?>
            <figure class="edit-place-form__image-preview" data-image-id="<?=$image['id']?>">
                <span class="edit-place-form__image-preview__remove-button">×</span>
                <img src="<?=$image['url']?>" class="edit-place-form__image-preview__image">
                <figcaption>
                    <input placeholder="Подпись" class="edit-place-form__image-preview__caption" value="<?=$image['caption']?>">
                </figcaption>
            </figure>
        <?php endforeach; ?>
        <?php endif; ?>

    </div>
    <?php endif; ?>

    <p><input type="text" name="description" class="edit-place-form__field" placeholder="Краткое описание" value="<?=$place['description']?>" maxlength="150"></p>

    <textarea name="text" placeholder="Текст" class="edit-place-form__field edit-place-form__field_text"><?=$place['text']?></textarea>

    <div style="text-align: right;">
        <button class="btn"><?php if (isset($place['id'])) : ?>Обновить<?php else: ?>Добавить<?php endif;?></button>
    </div>
</form>

<script id="place-image-preview-template" type="text/x-handlebars-template">
    <figure class="edit-place-form__image-preview">
        <span class="edit-place-form__image-preview__remove-button">×</span>
        <img src="{{url}}" class="edit-place-form__image-preview__image">
        <figcaption>
            <input placeholder="Подпись к фотографии" class="edit-place-form__image-preview__caption" value="">
        </figcaption>
    </figure>
</script>

<?php ob_start() ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/medium-editor/5.22.2/js/medium-editor.min.js"></script>
<script type="text/javascript">
var editor = new MediumEditor('.edit-place-form__field_text', {
    placeholder: {
        text: 'Сюда нужно написать текст об этом месте'
    },
    paste: {
        forcePlainText: false,
        cleanPastedHTML: true
    },
    autoLink: true
});

ymaps.ready(function(){

    var initialLat = <?=($place['lat']? $place['lat']: 59.484148) ?>;
    var initialLong = <?=($place['long']? $place['long'] : 58.704105) ?>;


    var mapPreview = new ymaps.Map($('.map-preview')[0], {
        center: [initialLong, initialLat],
        zoom: 14,
        controls: ["zoomControl", "typeSelector"]
    }, {
        preset: 'islands#redCircleDotIcon'
    });

    mapPreview.behaviors.disable('scrollZoom');

    if(isMobile.any()) {
        mapPreview.behaviors.disable('drag');
    }

    var placemark = new ymaps.Placemark( [initialLong, initialLat], null, {
        preset: 'islands#redCircleDotIcon',
        draggable: true
    });
    mapPreview.geoObjects.add(placemark);

    placemark.events.add('drag', function (e) {
        var coordinates = placemark.geometry.getCoordinates();
        $('.edit-place-form__field_coordinates').val(coordinates[0]+' '+coordinates[1]);
    });
});

$('.image-previews').on('click', '.edit-place-form__image-preview__remove-button', function(e) {
    e.preventDefault();

    var $preview = $(this).parents('.edit-place-form__image-preview');
    var imageId = $preview.data('image-id');

    $.ajax({
        url: '/+/remove-image',
        type: 'post',
        data: {id: imageId},
        dataType: 'json',
        success: function(data){
            $preview.hide();
        }
    });
});

$('.image-previews').on('blur', '.edit-place-form__image-preview__caption', function(e) {

    var $preview = $(this).parents('.edit-place-form__image-preview');
    var imageId = $preview.data('image-id');
    var caption = $(this).val();

    $.ajax({
        url: '/+/edit-image',
        type: 'post',
        data: {id: imageId, caption:caption},
        dataType: 'json'
    });
});

</script>
<?php Flight::view()->set('js', ob_get_clean()) ?>
