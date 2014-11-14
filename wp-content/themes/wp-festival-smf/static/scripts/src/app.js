/**
 * Main entry point for Javascript. Loads several sub-modules and sets up the page.
 *
 * @module UMESouthPadre
 * @main UMESouthPadre
 */
require.config({

  baseUrl: "wp-content/themes/wp-festival-smf/static/scripts/src",
  paths: {
    "jquery": "vendor/jquery/jquery"
  }
});


require( [
	'jquery',
  'lib/share',
  'lib/countdown'
], function( $, share, countdown ){

	$(function() {
		share.init();
		countdown.init();
	});


});
