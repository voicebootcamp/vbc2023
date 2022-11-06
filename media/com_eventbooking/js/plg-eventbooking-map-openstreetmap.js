(function (document) {
    document.addEventListener('DOMContentLoaded', function(){
        var zoomLevel = Joomla.getOptions('mapZoomLevel');
        var location = Joomla.getOptions('mapLocation');

        var mymap = L.map('map_canvas', {
            center: [location.lat, location.long],
            zoom: zoomLevel,
            zoomControl: true,
            attributionControl: false,
            scrollWheelZoom: false
        });

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            id: 'mapbox.streets'
        }).addTo(mymap);

        var marker = L.marker([location.lat, location.long], {
            draggable: false,
            autoPan: true,
            title: location.name
        }).addTo(mymap);

        marker.bindPopup(location.popupContent);
        marker.openPopup();
    });
})(document);