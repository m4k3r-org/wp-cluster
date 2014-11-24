/**
 * Multi-language popup
 *
 * @class buytickets
 */
define(['jquery'], function( $ ){

	var multiLanguage = {


		/**
		 * Event for showing the overlay
		 *
		 * @method eventOpen
		 */
		eventOpen : function()
		{
			$( '#doc' ).on('click', '.language-switcher .active-language', function(e) {

				e.preventDefault();

				$( '.language-overlay' ).css('display', 'block');
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
			$( '.language-overlay' ).on('click', '.icon-close', function(e) {

				e.preventDefault();

				$( '.language-overlay' ).css('display', 'none');
				$( 'html, body' ).removeClass('overlay-open');

			});

			// On ESC key close the overlay
			$( document ).keyup( function(e) {

				if ( e.keyCode == 27 )
				{
					$( '.language-overlay' ).css('display', 'none');
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
			multiLanguage.eventOpen();
			multiLanguage.eventClose();
		}

	}

});