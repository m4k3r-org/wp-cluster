/**
 * Handle collapse module
 *
 */
define( ['jquery'], function( $ ){

  var artistCallout = {

    /**
     * Event for flipping the artist by click on share icon
     *
     * @method eventOpenShareWindow
     */
    eventFlipBox: function(){

      $( 'body' ).on( 'click', '.artist-share', function( e ){

        // Share should appear without flip in case of IE
        if( navigator.appName == 'Microsoft Internet Explorer' ){
          $( this ).closest( '.front' ).hide().siblings( '.back' ).show().css( 'backface-visibility', 'visible' ).css( 'transform', 'none' );

        } else{
          $( this ).closest( '.flipper' ).addClass( 'flipped' );
        }

        e.preventDefault();

      } );

      $( 'body' ).on( 'click', '.share-close', function( e ){

        // Share should disappear without flip in case of IE
        if( navigator.appName == 'Microsoft Internet Explorer' ){
          $( this ).closest( '.back' ).hide().siblings( '.front' ).show();
        } else{
          $( this ).closest( '.flipper' ).removeClass( 'flipped' );
        }

        e.preventDefault();

      } );

    },

    /**
     * Event for opening the individual share windows
     *
     * @method eventOpenShareWindow
     */
    eventOpenShareWindow: function(){
      $( '.artist-image .social-share-overlay' ).on( 'click', '.share-wrapper a', function( e ){

        e.preventDefault();

        var popUp = window.open( $( this ).attr( 'href' ), 'popupwindow', 'scrollbars=yes,width=800,height=400' );
        popUp.focus();

      } );
    }


  };

  return {

    init: function(){


      artistCallout.eventFlipBox();
      artistCallout.eventOpenShareWindow();

    }
  }

} );