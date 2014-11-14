/**
 * Sticky Footer
 *
 * @class stickyFooter
 */
define(['jquery'], function( $ ){

	var stickyFooter = {

		/**
		 * Initialize the sticky footer
		 *
		 * @method stick
		 */
		stick : function() {

			var wrapperHeight = $(document).height();
			var windowHeight = $(window).height();

			if ( windowHeight >= wrapperHeight )
			{
				$('footer').css({
					position : 'absolute',
					bottom : 0,
					width : '100%',
					overflow : 'hidden'
				});
			}
			else
			{
				$('footer').css({
					position: 'static'
				})
			}
		}
	};

	return {

		/**
		 * Bootstrap
		 *
		 * @method init
		 */
		init : function()
		{
			stickyFooter.stick();

			$( window).resize( function() {
				stickyFooter.stick();
			});
		}
	}

});