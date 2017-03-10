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

    var coordinates;

    if($.trim($('.edit-place-form__field_coordinates').val())) {
        var coordinates = $('.edit-place-form__field_coordinates').val().split(' ');
    } else {
        coordinates = [58.704105, 59.484148]; /* Default coordinates */
        updateFormCoordinates(coordinates);
    }

    var mapPreview = new ymaps.Map($('.map-preview')[0], {
        center: coordinates,
        zoom: 14,
        controls: ["zoomControl", "typeSelector"]
    }, {
        preset: 'islands#redCircleDotIcon'
    });

    mapPreview.behaviors.disable('scrollZoom');

    if(isMobile.any()) {
        mapPreview.behaviors.disable('drag');
    }

    var placemark = new ymaps.Placemark(coordinates, null, {
        preset: 'islands#redCircleDotIcon',
        draggable: true
    });
    mapPreview.geoObjects.add(placemark);

    placemark.events.add('drag', function (e) {
        updateFormCoordinates(placemark.geometry.getCoordinates());
    });

    function updateFormCoordinates(coordinates){
        var long = coordinates[0];
        var lat = coordinates[1];

        $('.edit-place-form__field_coordinates').val(long +' '+ lat);
    }
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


/* Auto aliases */
$('.edit-place-form__field_alias').on('input', function() {

    if ($(this).val()) {
        $(this).removeClass('auto-alias');
    } else {
        $(this).addClass('auto-alias');
    }

}).trigger('input');

$('.edit-place-form__field_title').on('input', function() {

    if(!$('.edit-place-form__field_alias').hasClass('auto-alias')) return;

    var title = $('.edit-place-form__field_title').val();

    var alias = translit(title);

    $('.edit-place-form__field_alias').val(alias);
});


function translit(str, spaceReplacement) {

    var associations = {
        1072: "a",
        1073: "b",
        1074: "v",
        1075: "g",
        1076: "d",
        1077: "e",
        1078: "zh",
        1079: "z",
        1080: "i",
        1081: "y",
        1082: "k",
        1083: "l",
        1084: "m",
        1085: "n",
        1086: "o",
        1087: "p",
        1088: "r",
        1089: "s",
        1090: "t",
        1091: "u",
        1092: "f",
        1093: "h",
        1094: "ts",
        1095: "ch",
        1096: "sh",
        1097: "sh",
        1099: "y",
        1101: "e",
        1102: "yu",
        1103: "ya",
        1105: "e",
        1098: "",
        1100: ""
    };

    str = str.toLowerCase();
    var translit = '',
        exp = str.split('');

    for (var i in exp) {
        var code = str.charCodeAt(i),
            symb = '';

        if (code == 32) {
            symb = '-';
        } else if (associations[code]) {
            symb = associations[code];
        } else if (code >= 97 && code <= 122) {
            symb = exp[i];
        }

        translit += symb;
    }

    return translit;
};
