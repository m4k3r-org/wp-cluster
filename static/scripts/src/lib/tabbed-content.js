/**
 * Tabbed Content Module
 *
 */
define( ['jquery'], function( $ ){

	var tabbedContent = {

		eventTabs : function()
		{
			$('.tabbed-content').on('click', '.tab-header a', function(e) {

				e.preventDefault();

				var t = $(this);

				var tabID = t.attr('href');

				$('.tabbed-content .tab-header a').removeClass('selected');
				t.addClass('selected');

				$('.tab-content').hide();
				$( tabID ).show();

			});
		},

		load : function()
		{
			var tabID = $('.tabbed-content .tab-header .selected').attr('href');

			if (! tabID )
			{
				tabID = $('.tabbed-content .tab-header a:first').attr('href');
			}

			$('.tab-content').hide();
			$( tabID ).show();
		}
	};

	return {

		init: function(){

			tabbedContent.eventTabs();

			$(window).on('resizeEnd',function() {

				if ( document.documentElement.clientWidth < 768 )
				{
					$('.tabbed-content').show();
					tabbedContent.load();
				}
				else
				{
					$('.tabbed-content').hide();
					$('.tab-content').show();
				}
			});

			$(window).trigger('resize');
		}
	}

} );