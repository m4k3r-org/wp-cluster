/**
 * Script Ran within iFrame when using Customizer.
 *
 * Logic here can be used to interact with the site preview in real-time.
 *
 * @author potanin@ud
 */
 
( function( $, args ) {

  define( 'wp-elastic.customizer', function customizerStyle() {

  });

  wp.customize( args.name, function( style ) {
    var intent;

    // Listen for Changes.
    style.bind( require( 'wp-elastic.customizer' ) );

  });

} )( jQuery, wp_elastic_customizer );

