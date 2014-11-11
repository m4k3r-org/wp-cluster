/**
 * Animate scroll for header down arrow.
 *
 * @class smoothscroll
 */
define( ['jquery'], function( $ ){

  return {

    /**
     * Bootstrap the smooth scroll effect.
     *
     * @method init
     * @return void
     */
    init: function(){
      var href = $( '.nav-arrows' ).attr( 'href' );

      $( '.nav-arrows' ).click( function( e ){

        e.preventDefault();

        var target = $( href );
        if( target.length > 0 ){
          target = target.offset().top - 90;
        }

        $( 'html, body' ).animate( { scrollTop: target}, 400 );

      } );

    }
  }
} );
