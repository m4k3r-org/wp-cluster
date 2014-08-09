define(
  [ 
    window.wp_social_stream.base_url + 'src/jquery.social.stream-1.5.5.custom.js', 
    window.wp_social_stream.base_url + 'src/jquery.social.stream.wall-1.3.js',
    window.wp_social_stream.base_url + 'src/jquery-2.1.1.min.amd.js'
  ],
  function( social_stream, social_stream_wall, jQuery ){
    // Use jQuery
    var $ = jQuery;
    // Setup our that varaible
    var that = this;
    // Hold our common settings
    var s = {
      debug: true,
      name: 'wp-social-stream'
    };
    // Setup our functions
    var f = {
      dom_ready: function( args ){
        if( s.debug ) console.debug( s.name, "dom ready" );
        social_stream.init( args );
        // Now to Fancybox - jQuery( ".section-image a, .section-thumb a" ).fancybox( { helpers: { media: !0 } } ), this } } ); 
      }
    }
    // We're loaded
    if( s.debug ) console.debug( s.name, "loaded" );
    // Just launch the function when we're ready
    jQuery( document ).ready( function(){
      f.dom_ready.apply( that, [ jQuery( '.wp-social-stream' ) ] );
    } );
  }
);