function initMap() {
	var divMap = document.getElementById('map');
	var mapPannel = document.getElementById('map_pannel')
	var map = new google.maps.Map(divMap, {
		center: {lat: 0, lng: 0},

		zoom: 16
	});
	var infoWindow = new google.maps.InfoWindow({map: map});

}

function handleLocationError(browserHasGeolocation, infoWindow, pos) {
	infoWindow.setPosition(pos);
	infoWindow.setContent(browserHasGeolocation ?
														'Error: The Geolocation service failed.' :
														'Error: Your browser doesn\'t support geolocation.');
	}