var isMobile = {
    Android: function() {
        return navigator.userAgent.match(/Android/i);
    },
    BlackBerry: function() {
        return navigator.userAgent.match(/BlackBerry/i);
    },
    iOS: function() {
        return navigator.userAgent.match(/iPhone|iPad|iPod/i);
    },
    Opera: function() {
        return navigator.userAgent.match(/Opera Mini/i);
    },
    Windows: function() {
        return navigator.userAgent.match(/IEMobile/i);
    },
    any: function() {
        return (isMobile.Android() || isMobile.BlackBerry() || isMobile.iOS() || isMobile.Opera() || isMobile.Windows());
    }
};

ymaps.ready(function(){
    if(!$('.map').length) return;

    window.kachkanarMap = new ymaps.Map(document.querySelector('.map'), {
        center: [58.70, 59.48],
        zoom: 14,
        controls: ["geolocationControl"]
    });

    kachkanarMap.behaviors.disable('scrollZoom');

    if(isMobile.any()) {
        kachkanarMap.behaviors.disable('drag');
    }

    $.ajax('/json', {
        dataType: "json",
        success: function(places) {
            places.forEach(function(place, i) {
                var placemark = new ymaps.Placemark( [place.long, place.lat], {
                    balloonContent: '<a href="/'+place.alias+'"><strong style="font-size:16px;">'+place.title+'</strong><br><img src="'+place.image+'"></a>',
                }, {
                    preset: 'islands#redCircleDotIcon'
                });

                // placemark.events.add('click', function(e) {
                //     // instanciate new modal
                //     var modal = new tingle.modal();

                //     // set content
                //     modal.setContent('<h1>'+place.title+'</h1>'+place.text);

                //     // open modal
                //     modal.open();
                // });

                kachkanarMap.geoObjects.add(placemark);
            });
        }
    });
});

$('.map-zoomin').on('click', function(){
    kachkanarMap.setZoom(kachkanarMap.getZoom()+1, {duration: 300, checkZoomRange: true});
});
$('.map-zoomout').on('click', function(){
    if(kachkanarMap.getZoom() <= 13 ) return;
    kachkanarMap.setZoom(kachkanarMap.getZoom()-1, {duration: 300, checkZoomRange: true});
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

                $('.image-previews').append(template({url: data.url}));
            }
        });
    }
})();

