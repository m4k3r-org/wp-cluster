/**
 * Account Overlay
 *
 * @class account
 */
define( ['jquery'], function( $ ){

  var accountOverlay = {


    /**
     * Event for showing the overlay
     *
     * @method eventOpen
     */
    eventOpen: function(){
      $( '#doc' ).on( 'click', '.my-account', function( e ){

        e.preventDefault();

        $( '.account-overlay' ).css( 'display', 'block' );
        $( 'html, body' ).addClass( 'overlay-open' );

      } );

    },

    /**
     * Event for closing the overlay
     *
     * @method eventClose
     */
    eventClose: function(){
      $( '.account-overlay' ).on( 'click', '.icon-close', function( e ){

        e.preventDefault();

        $( '.account-overlay' ).css( 'display', 'none' );
        $( 'html, body' ).removeClass( 'overlay-open' );

      } );

      // On ESC key close the overlay
      $( document ).keyup( function( e ){

        if( e.keyCode == 27 ){
          $( '.account-overlay' ).css( 'display', 'none' );
          $( 'html, body' ).removeClass( 'overlay-open' );
        }

      } );
    }

  };

  return {

    /**
     * Bootstrap the account plugin
     *
     * @method init
     */
    init: function(){
      accountOverlay.eventOpen();
      accountOverlay.eventClose();
    }

  }

} );