"use strict";
/**
 * Maps positioning
 */
var GDO = GDO || {};
GDO.Maps = GDO.Maps || {};

GDO.Maps.positioning = function() {
	console.log('GDO.Maps.positioning()');
	navigator.geolocation.getCurrentPosition(GDO.Maps.gotPosition, GDO.error);
};

GDO.Maps.gotPosition = function(pos) {
	console.log('GDO.Maps.gotPosition()', pos);
	let c = pos.coords;
	window.GDO.gdoxhr('Maps', 'Record', '&_fmt=json&pos_lat='+c.latitude+'&pos_lng='+c.longitude).then(function(result) {
		console.log('wrote position', result);
	});
};

if (navigator.geolocation) {
    setInterval(GDO.Maps.positioning, window.GDO_MAPS_HISTORY * 1000);
}
