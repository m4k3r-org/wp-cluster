/**
 * Sticks the artist lineup content within its block
 *
 * @class stickem
 */
define( ['jquery', 'components/sticky/jquery.sticky'], function( $ ){

  return {

    /**
     * Bootstrap the module
     *
     * @module init
     *
     */
    init: function(){


      // Stick the artist lineup section inside the container for smaller screens

      $( '.callout-content' ).sticky( {

        parent: '.callout'

      } );


      // Register window resize event, so content will always be the same width as the container
      $( window ).on( 'resizeEnd', function(){

        $( '.callout-content' ).width( $( '.callout' ).width() );

      } );

      $(window ).trigger('resize');

    }
  }
} );