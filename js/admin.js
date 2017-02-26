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


var imagePreviews = {
    images:[],
    update: function(){
        imagePreviews.images = [];
        $('.image-preview').each(function(i,el){
            imagePreviews.images.push({
                id: $(el).data('image-id'),
                caption: $(el).find('.image-preview__caption').val()
            });
        });
        $('input[name=images]').val(JSON.stringify(imagePreviews.images));
    }
};
imagePreviews.update();


$('.image-previews').on('click', '.image-preview__remove-button', function(e) {
    e.preventDefault();

    $(this).parents('.image-preview').remove();

    imagePreviews.update();
});

$('.image-previews').on('keyup', '.image-preview__caption', function(e) {
    imagePreviews.update();
});

var sort = Sortable.create(document.querySelector('.image-previews'), {
    animation: 150,
    onUpdate: function (evt){
        imagePreviews.update();
    }
});


// Uploader
(function(){
    var $dropzone = $('.upload-dropzone');
    var fileInput = $dropzone.find('.upload-dropzone__input');

    if (typeof(window.FileReader) == 'undefined') {
        $dropzone.addClass('upload-dropzone_not-supported');
    }

    $dropzone.on('drag dragstart dragend dragover dragenter dragleave drop', function(e) {
        e.preventDefault();
        e.stopPropagation();
    });

    $dropzone.on("dragover", function(e){
        $dropzone.addClass('upload-dropzone_drop');
    });
    $dropzone.on("dragleave", function(e){
        $dropzone.removeClass('upload-dropzone_drop');
    });
    $dropzone.on("drop", function(e){
        $dropzone.removeClass('upload-dropzone_drop');

        var files = e.originalEvent.dataTransfer.files;

        Array.prototype.forEach.call(files, function(file) {
            upload(file);
        });
    });
    $dropzone.on('click', function(e){
        e.preventDefault();
        fileInput.click();
    });
    fileInput.on('click', function(e) {
        e.stopPropagation(); // !
    });

    fileInput.on('change', function(e){

        var files = $(this)[0].files;

        Array.prototype.forEach.call(files, function(file) {
            upload(file);
        });

    });

    function upload(file){
        var formData = new FormData();
        formData.append("image", file);
        formData.append("place", $('.edit-place-form').data('place-id'));

        $.ajax({
            url: '/+/upload',
            type: 'post',
            contentType: false, // !
            processData: false, // !
            data: formData,
            dataType: 'json',
            beforeSend: function(){
                $dropzone.addClass('upload-dropzone_loading');
            },
            complete: function(){
                $dropzone.removeClass('upload-dropzone_loading');
            },
            success: function(data){
                var template = Handlebars.compile($("#place-image-preview-template").html());

                $('.image-previews').append(template({
                    url: data.url,
                    id: data.id
                }));

                imagePreviews.update();
            }
        });
    }
})();
