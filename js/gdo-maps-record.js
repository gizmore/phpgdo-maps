"use strict";
/**
 * Maps positioning
 */
var GDO = GDO || {};
GDO.Maps = GDO.Maps || {};

GDO.Maps.positioning = function() {
	console.log('GDO.Maps.positioning()');
	navigator.geolocation.getCurrentPosition(GDO.Maps.gotPosition, GDO.Maps.positioningError);
};

GDO.Maps.gotPosition = function(pos) {
	console.log('GDO.Maps.gotPosition()', pos);
	let c = pos.coords;
	window.GDO.gdoxhr('Maps', 'Record', '&_fmt=json&pos_lat='+c.latitude+'&pos_lng='+c.longitude).then(function(result) {
		console.log('wrote position', result);
		setTimeout(GDO.Maps.positioning, window.GDO_MAPS_HISTORY * 1000);
	});
};

GDO.Maps.positioningError = function(error) {
	console.log('GDO.Maps.positioningError()', error);
	debugger;
	return GDO.error(error.message, t('positioning')).then(() => setTimeout(GDO.Maps.positioning, window.GDO_MAPS_HISTORY * 1000));
};

if (navigator.geolocation) {
	setTimeout(GDO.Maps.positioning, window.GDO_MAPS_HISTORY * 1000);
}
