/**
 * Custom carousel
 *
 * @class carousel
 */
define( ['jquery', 'components/caroufredsel/caroufredsel-built'], function( $, caroufredsel ){

  var carouselHelper = {

    items: null,
    timeInterval: null,

    /**
     * Helper hover event for the carousel plugin
     *
     * @method eventHover
     * @return void
     */
    eventHover: function(){

      var that = this;

      this.items.on( 'mouseover', function( e ){

        var t = $( this );
        var offset = t.offset();

        // Clone the element and place it under the mouse cursor to create the effect of hover
        var newElem = t.clone();

        newElem.addClass( 'artist-out' );

        newElem.css( {

          position: 'absolute',
          top: offset.top - 295,
          left: offset.left,
          zIndex: 99999,
          height: '370px',
          opacity: 1

        } );

        $( 'img', newElem ).css( 'margin-top', '-5px' );
        $( '#doc' ).append( newElem );

      } );

      this.items.on( 'mouseout', function( e ){

        that.timeInterval = window.setTimeout( function(){

          $( '.artist-out' ).remove();

        }, 250 );

      } );

      $( '#doc' ).on( 'mouseover', '.artist-out', function( e ){

        e.stopPropagation();

        window.clearTimeout( that.timeInterval );

        $( '.artists-carousel' ).trigger( 'pause', true );

      } );

      $( '#doc' ).on( 'mouseout', '.artist-out', function(){

        $( this ).remove();

        $( '.artists-carousel' ).trigger( 'resume', true );

      } );

    }

  };

  return {

    /**
     * Initialize the carousel
     *
     * @method init
     * @return void
     */
    init: function(){

      carouselHelper.items = $( '.artists-carousel .artist' );
      carouselHelper.eventHover();

      $( '.artists-carousel' ).carouFredSel( {

        width: '100%',

        items: {
          visible: 'odd+2'
        },

        scroll: {
          pauseOnHover: 'immediate-resume'
        },

        auto: {
          items: 1,
          easing: 'linear',
          duration: 10000,
          timeoutDuration: 0
        }
      } );
    }
  }
} );