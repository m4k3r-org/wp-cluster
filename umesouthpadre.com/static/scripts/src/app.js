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
	},
	shim: {
		"vendor/fancybox-2.1.5/jquery.fancybox": ["jquery"],
		"vendor/fancybox-2.1.5/helpers/jquery.fancybox-media": ["jquery", "vendor/fancybox-2.1.5/jquery.fancybox"]
	}
});


require( [
	'lib/share',
	'lib/countdown',
	'lib/istouch',
	'vendor/fancybox-2.1.5/jquery.fancybox',
	'vendor/fancybox-2.1.5/helpers/jquery.fancybox-media'
], function( share, countdown, isTouch ){

	share.init();
	countdown.init();

	// Initialize top video popup
	if ( isTouch === false )
	{
		$('header .video-popup' ).fancybox({
			maxWidth: 800,
			maxHeight: 600,
			fitToView: false,
			autoSize: false,
			closeClick: false,
			padding: 0,
			margin: 0,

			helpers: {
				media: true
			},

			youtube: {
				autoplay: 1,
				hd: 1
			}
		});
	}


} );
