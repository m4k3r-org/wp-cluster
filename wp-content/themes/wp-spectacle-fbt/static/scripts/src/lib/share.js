/**
 * Share popup
 *
 * @class buytickets
 */
define(['jquery'], function( $ ){

  var share = {


    /**
     * Event for showing the overlay
     *
     * @method eventOpen
     */
    eventOpen : function()
    {
      $( '#doc' ).on('click', '.share-popup', function(e) {

        e.preventDefault();

        $( '.share-overlay' ).css('display', 'block');
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
      $( '.share-overlay' ).on('click', '.overlay-close', function(e) {

        e.preventDefault();

        $( '.share-overlay' ).css('display', 'none');
        $( 'html, body' ).removeClass('overlay-open');

      });

      // On ESC key close the overlay
      $( document ).keyup( function(e) {

        if ( e.keyCode == 27 )
        {
          $( '.share-overlay' ).css('display', 'none');
          $( 'html, body' ).removeClass('overlay-open');
        }

      });
    },

		/**
		 * Event for opening the individual share windows
		 *
		 * @method eventOpenShareWindow
		 */
		eventOpenShareWindow : function()
		{
			$('.share-overlay').on('click', '.share-wrapper a', function(e) {

				e.preventDefault();

				var popUp = window.open( $( this ).attr( 'href' ), 'popupwindow', 'scrollbars=yes,width=800,height=400' );
				popUp.focus();

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
      share.eventOpen();
      share.eventClose();

			share.eventOpenShareWindow();
    }

  }

});