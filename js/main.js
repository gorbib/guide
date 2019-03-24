let isMobile = {
    Android: function () {
        return navigator.userAgent.match(/Android/i);
    },
    BlackBerry: function () {
        return navigator.userAgent.match(/BlackBerry/i);
    },
    iOS: function () {
        return navigator.userAgent.match(/iPhone|iPad|iPod/i);
    },
    Opera: function () {
        return navigator.userAgent.match(/Opera Mini/i);
    },
    Windows: function () {
        return navigator.userAgent.match(/IEMobile/i);
    },
    any: function () {
        return (isMobile.Android() || isMobile.BlackBerry() || isMobile.iOS() || isMobile.Opera() || isMobile.Windows());
    }
};

ymaps.ready(function () {
    if (!$('.map').length) return;

    window.kachkanarMap = new ymaps.Map(document.querySelector('.map'), {
            center: [58.720, 59.493],
            zoom: 12,
            controls: ["geolocationControl"]
        },
        {
            restrictMapArea: [[58.581, 59.101], [58.858, 59.884]]
        });

    kachkanarMap.behaviors.disable('scrollZoom');

    if (isMobile.any()) {
        kachkanarMap.behaviors.disable('drag');
    }

    $.ajax('/json', {
        dataType: "json",
        success: function (places) {
            var placemarks = [];
            places.forEach(function (place, i) {
                var placemark = new ymaps.Placemark([place.long, place.lat], {
                    balloonContent: '<a href="/' + place.alias + '" style="text-decoration:none;color:#333"><strong style="font-size:16px;">' + place.title + '</strong><br><img src="' + place.image + '"></a>',
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

ymaps.ready(function () {
    if (!$('.place-location__map').length) return;

    let lat = $('.place-location__map').data('lat');
    let long = $('.place-location__map').data('long');

    if (!lat || !long) return;

    let placeMap = new ymaps.Map(document.querySelector('.place-location__map'), {
        center: [long, lat],
        zoom: 15,
        controls: ["zoomControl", "typeSelector"],
        type: 'yandex#hybrid'
    });
    placeMap.behaviors.disable('scrollZoom');
    let placemark = new ymaps.Placemark([long, lat], {}, {
        preset: 'islands#redCircleDotIcon'
    });
    if (isMobile.any()) {
        placeMap.behaviors.disable('drag');
    }
    placeMap.geoObjects.add(placemark);
});

$('.map-zoomin').on('click', function (e) {
    e.preventDefault();
    kachkanarMap.setZoom(kachkanarMap.getZoom() + 1, {duration: 300, checkZoomRange: true});
});
$('.map-zoomout').on('click', function (e) {
    e.preventDefault();
    if (kachkanarMap.getZoom() <= 12) return;
    kachkanarMap.setZoom(kachkanarMap.getZoom() - 1, {duration: 300, checkZoomRange: true});
});

