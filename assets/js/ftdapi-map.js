jQuery(document).ready(function() {
	jQuery.each(FTDapiMap, function( key, value ) {
  		var map = L.map('map_'+value.mapID, {});
		var markers = new L.MarkerClusterGroup({
			spiderfyOnMaxZoom : true,
			showCoverageOnHover : true,
			zoomToBoundsOnClick : true
		});
		
		if(value.dataProvider == 'google'){
			var googleLayer = new L.Google('ROADMAP');
	    	map.addLayer(googleLayer);
		} else {
			var OpenStreetMap_HOT = L.tileLayer('http://{s}.tile.openstreetmap.fr/hot/{z}/{x}/{y}.png', {
				attribution : '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>, Tiles courtesy of <a href="http://hot.openstreetmap.org/" target="_blank">Humanitarian OpenStreetMap Team</a>'
			}).addTo(map);
		}
		
		jQuery.each(value.data, function(key, val) {
			var startTime = new Date(parseInt(val.startTime * 1000));
			var day = startTime.getDate() + "." + (startTime.getMonth() + 1) + "." + startTime.getFullYear();
			var starthours = startTime.getHours();
			var startminutes = "0" + startTime.getMinutes();
			var formattedStartTime = starthours + ':' + startminutes.substr(startminutes.length - 2);
			var endTime = new Date(parseInt(val.endTime * 1000));
			var endhours = endTime.getHours();
			var endminutes = "0" + endTime.getMinutes();
			var formattedEndTime = endhours + ':' + endminutes.substr(endminutes.length - 2);
			var popupString = '<p>' + value.language.on + ' <strong>' + day + '</strong> ' + value.language.from + ' <strong>' + formattedStartTime + '</strong> ' + value.language.to + ' <strong>' + formattedEndTime + '</strong><br/>' + val.address.full + '<br/>' + val.locationname + ' (' + val.sponsorname + ')' + '</p>';
			var icon = L.icon({
				iconUrl : 'http://food-trucks-deutschland.de' + val.image,
				iconSize : [50, 38]
			});
			var marker = L.marker(new L.LatLng(val.map['latitude'], val.map['longitude']), {
				title : val.name,
				icon : icon
			});
			marker.bindPopup(popupString);
			markers.addLayer(marker);
		});
	
		map.addLayer(markers);
		map.fitBounds(markers.getBounds());
	});
});