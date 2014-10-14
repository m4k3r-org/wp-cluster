jQuery(function() {

	require(['//cdnjs.cloudflare.com/ajax/libs/jquery.selectboxit/3.8.0/jquery.selectBoxIt.min.js'], function() {

		var hotelWidget = {

			init : function() {

				jQuery('.custom-select').selectBoxIt({
					'autoWidth': false,
					'copyClasses': 'container'
				});

			}

		};

		// Initialize
		hotelWidget.init();
	});
});

