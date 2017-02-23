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
            var placemarks = [];
            places.forEach(function(place, i) {
                var placemark = new ymaps.Placemark( [place.long, place.lat], {
                    balloonContent: '<a href="/'+place.alias+'" style="text-decoration:none;color:#333"><strong style="font-size:16px;">'+place.title+'</strong><br><img src="'+place.image+'"></a>',
                    hintContent: place.title
                }, {
                    preset: 'islands#redCircleDotIcon'
                });

                placemarks.push(placemark);
            });
            var clusterer = new ymaps.Clusterer({
                preset: 'islands#redClusterIcons'
            });
            clusterer.add(placemarks);
            kachkanarMap.geoObjects.add(clusterer);
        }
    });
});

$('.map-zoomin').on('click', function(e){
    e.preventDefault();
    kachkanarMap.setZoom(kachkanarMap.getZoom()+1, {duration: 300, checkZoomRange: true});
});
$('.map-zoomout').on('click', function(e){
    e.preventDefault();
    if(kachkanarMap.getZoom() <= 13 ) return;
    kachkanarMap.setZoom(kachkanarMap.getZoom()-1, {duration: 300, checkZoomRange: true});
});

