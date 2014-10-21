/**
 * Main entry point for Javascript. Loads several sub-modules and sets up the page.
 *
 * @module UMESouthPadre
 * @main UMESouthPadre
 */
require.config({

	baseUrl: "static/scripts/src",
	paths: {
		"jquery": "vendor/jquery/jquery"
	}
});


require( [
	'lib/share'
], function( share ){

	share.init();
} );
