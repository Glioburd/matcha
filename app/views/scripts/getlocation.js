function getCity(lat, lng) {

	latlng = new google.maps.LatLng(lat, lng);

	new google.maps.Geocoder().geocode({'latLng' : latlng}, function(results, status) {
		if (status == google.maps.GeocoderStatus.OK) {
			if (results[1]) {
				var country = null, countryCode = null, city = null, cityAlt = null;
				var c, lc, component;
				for (var r = 0, rl = results.length; r < rl; r += 1) {
					var result = results[r];

					if (!city && result.types[0] === 'locality') {
						for (c = 0, lc = result.address_components.length; c < lc; c += 1) {
							component = result.address_components[c];

							if (component.types[0] === 'locality') {
								city = component.long_name;
								break;
							}
						}
					}
					else if (!city && !cityAlt && result.types[0] === 'administrative_area_level_1') {
						for (c = 0, lc = result.address_components.length; c < lc; c += 1) {
							component = result.address_components[c];

							if (component.types[0] === 'administrative_area_level_1') {
								cityAlt = component.long_name;
								break;
							}
						}
					} else if (!country && result.types[0] === 'country') {
						country = result.address_components[0].long_name;
						countryCode = result.address_components[0].short_name;
					}

					if (city && country) {
						break;
					}
				}
				document.getElementById("city").value = city;
			}
		}
	});
}

function getCoordinatesByIp() {
	$.get("http://ipinfo.io", function (response) {
		var coord = response['loc'].split(",", 2);
		var lat = parseFloat(coord[0]);
		var lng = parseFloat(coord[1]);
		$('#latitudeIP').val(lat);
		$('#longitudeIP').val(lng);
		$('#latitude').val(lat);
		$('#longitude').val(lng);
	}, "jsonp");
}

function ForceCoordinatesByIp() {
	$('#allowGeolocDiv').hide();
	$('#submitButton').prop("disabled", false);
	$.get("http://ipinfo.io", function (response) {
		var coord = response['loc'].split(",", 2);
		var lat = parseFloat(coord[0]);
		var lng = parseFloat(coord[1]);
		$('#latitudeG').val(lat);
		$('#longitudeG').val(lng);
		$('#latitudeIP').val(lat);
		$('#longitudeIP').val(lng);
		$('#latitude').val(lat);
		$('#longitude').val(lng);
		$('#map').val('0')
	}, "jsonp");
}


function getCoordinates() {

	if (navigator.geolocation) {
		$('#submitButton').prop("disabled", true);
		navigator.geolocation.getCurrentPosition(function(position) {
			// If geolocate with google API
			getCoordinatesByIp();
			var pos = {
				lat: position.coords.latitude,
				lng: position.coords.longitude,
			};
			latitude = document.getElementById("latitudeG").value = pos['lat'];
			longitude = document.getElementById("longitudeG").value = pos['lng'];
			$('#submitButton').prop("disabled", false);

		}, function() {
			ForceCoordinatesByIp();
		});
	} else {
		alert("Browser doesn't support Geolocation");
	}
}

// document.getElementById("allowGeoloc").addEventListener("click", function(){
// 	getCoordinates();
// });

document.getElementById("allowGeoloc").addEventListener("change", function() {
	// console.log(typeof(document.getElementById("latitude").value));
	// console.log(typeof(document.getElementById("longitude").value));

	if (document.getElementById("allowGeoloc").checked) {
		latitudeG = document.getElementById("latitudeG").value;
		longitudeG = document.getElementById("longitudeG").value;
		$('#latitude').val(latitudeG);
		$('#longitude').val(longitudeG);
		$('#map').val('1');
		// $('#latitude').val("Lat google");
		// $('#longitude').val("Long google");
	}
	else if (document.getElementById("allowGeoloc").checked == false) {
		latitudeIP = document.getElementById("latitudeIP").value;
		longitudeIP = document.getElementById("longitudeIP").value;
		$('#latitude').val(latitudeIP);
		$('#longitude').val(longitudeIP);
		$('#map').val('0')
	}
});

if (document.getElementById("allowGeoloc").checked) {
		latitudeG = document.getElementById("latitudeG").value;
		longitudeG = document.getElementById("longitudeG").value;
		$('#latitude').val(latitudeG);
		$('#longitude').val(longitudeG);
}