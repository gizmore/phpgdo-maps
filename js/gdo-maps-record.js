"use strict";
/**
 * On boot, detect user position.
 */
document.addEventListener('DOMContentLoaded', function() {
	setTimeout(window.GDO.positioning, 1);
}, false);

/**
 * Maps positioning
 */
var GDO = GDO || {};
GDO.Maps = GDO.Maps || {};
GDO.Maps.positioning = function() {
	console.log('GDO.Maps.positioning()');
	window.GDO.gdoxhr('Maps', 'Record', '&position_lat=11&position_lng=12').then(function(result) {
		console.log(result);
	});
};
