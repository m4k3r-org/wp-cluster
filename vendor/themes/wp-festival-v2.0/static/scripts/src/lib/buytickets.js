/**
 * Buy tickets popup
 *
 * @class buytickets
 */
define(['jquery'], function( $ ){

  var buyTickets = {


    /**
     * Event for showing the overlay
     *
     * @method eventOpen
     */
    eventOpen : function()
    {
      $( '#doc' ).on('click', '.buy-tickets', function(e) {

        e.preventDefault();

        $( '.buy-tickets-overlay' ).css('display', 'block');
        $( 'html, body' ).addClass('overlay-open');

      });

    },

    /**
     * Event for closing the overlay
     *
     * @method eventClose
     */
    eventClose : function()
    {
      $( '.buy-tickets-overlay' ).on('click', '.icon-close', function(e) {

        e.preventDefault();

        $( '.buy-tickets-overlay' ).css('display', 'none');
        $( 'html, body' ).removeClass('overlay-open');

      });

      // On ESC key close the overlay
      $( document ).keyup( function(e) {

        if ( e.keyCode == 27 )
        {
          $( '.buy-tickets-overlay' ).css('display', 'none');
          $( 'html, body' ).removeClass('overlay-open');
        }

      });
    }

  };


  return {

    /**
     * Bootstrap the buy tickets plugin
     *
     * @method init
     */
    init : function()
    {
      buyTickets.eventOpen();
      buyTickets.eventClose();
    }

  }

});