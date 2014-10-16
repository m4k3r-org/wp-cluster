jQuery(function($) {

	//'//raw.githubusercontent.com/jquery/jquery-ui/1.11.1/ui/datepicker.js'
	require(['//cdnjs.cloudflare.com/ajax/libs/jquery.selectboxit/3.8.0/jquery.selectBoxIt.min.js', '/vendor/themes/wp-festival-2/static/scripts/src/third-party/jquery-ui-1.11.1.custom/jquery-ui.min.js'], function(sbi, dp) {

		var hotelWidget = {

			init : function() {
				$('.custom-select').selectBoxIt({
					'autoWidth': false,
					'copyClasses': 'container'
				});

				// For some reason $ is not working with datepicker when noConflict
				// is enabled adn reassigned in the function scope
				var datepickerOpts = {
					dateFormat: 'yy-mm-dd',
					minDate: new Date()
				};

				jQuery('.hotel-widget .start-date').datepicker({
					dateFormat: 'yy-mm-dd',
					minDate: new Date(),

					onSelect : function(date) {
						// End Date cannot be smaller than start date
						// and cannot exceed 30 days into the future from the start date

						var endDateControl = jQuery('.hotel-widget .end-date');
						var startDate = jQuery(this).datepicker('getDate');

						// Add the same date to the end date if no date was set
						if ( endDateControl.datepicker('getDate') == null )
						{
							endDateControl.datepicker('setDate', startDate);
						}
						else
						{
							endDateControl.datepicker('setDate', '');
						}

						endDateControl.datepicker('option', 'minDate', startDate);
						endDateControl.datepicker('option', 'maxDate', startDate.getDate() + 30);
					}
				});

				jQuery('.hotel-widget .end-date').datepicker({
					dateFormat: 'yy-mm-dd',
				});
			},

			roomsOverlayOpen: function(){
				$( '.hotel-widget .hotel-rooms-container' ).on( 'change', 'select', function( e ){

					e.preventDefault();

					var selectValue = $(this).val();

					if ( selectValue == 'select-rooms' )
					{
						return false;
					}

					// Verify the number of rooms selected, and disable the rest
					selectValue = parseInt(selectValue);
					if ( (selectValue <= 0) || (selectValue > 4) )
					{
						$(this).val('select-rooms');
						$('.validation-error em').html('Invalid room number selected');
						$('.validation-error').show();

						return false;
					}
					else
					{
						// Disable all
						$('.hotel-rooms-overlay label, .hotel-rooms-overlay .select-container').addClass('disabled');
						$('.hotel-rooms-overlay select').attr('disabled', 'disabled');
						$('.hotel-rooms-overlay select').selectBoxIt('disable');

						// Re-enable
						for ( var i = 1; i <= selectValue; i++ )
						{
							$('.hotel-rooms-overlay label[for="hotel-room-' + i + '"]').removeClass('disabled');
							$('.hotel-rooms-overlay .hotel-room-' + i + '-container').removeClass('disabled');
							$('.hotel-rooms-overlay select.hotel-room-' + i).selectBoxIt('enable');
						}
					}


					$( '.hotel-rooms-overlay' ).css( 'display', 'block' );
					$( 'html, body' ).addClass( 'overlay-open' );

				});
			},

			roomsOverlayClose : function()
			{
				$('.hotel-widget .hotel-rooms-overlay').on('click', '.ok-button', function(e) {

					e.preventDefault();

					$( '.hotel-rooms-overlay' ).css( 'display', 'none' );
					$( 'html, body' ).removeClass( 'overlay-open' );

				});
			},

			/**
			 * Build the query based on the selected values
			 */
			buildQuery : function()
			{
				var that = this;

				$('.hotel-widget').on('click', '.search-button', function(e) {

					e.preventDefault();

					// Clear the validation
					$('.validation-error').hide();

					// Close the overlay if it's opened
					$( '.hotel-rooms-overlay' ).css( 'display', 'none' );
					$( 'html, body' ).removeClass( 'overlay-open' );

					// Validate first
					if ( that.validate() === true )
					{
						// Build the query
						var hotelWidget = $('.hotel-widget');
						$url = 'https://dayafter.findor.com/results/list?';

						// start date / end date
						$url += 'checkIn=' + $('.start-date', hotelWidget).val();
						$url += '&checkOut=' + $('.end-date', hotelWidget).val();

						// Add the type if the value is not hotel
						if ( $('.hotel-type', hotelWidget).val() == 'package' )
						{
							$url += '&type=package';
						}

						// Add the rooms and people
						var roomNr = parseInt( $('.hotel-rooms', hotelWidget).val() );
						for ( var i = 1; i <= roomNr; i++ )
						{
							$url += '&room' + i +'=' + $('.hotel-room-' + i, hotelWidget).val();
						}

						// Redirect
						window.open($url, "_blank");
					}
				});

			},

			validate : function()
			{
				var hotelWidget = $('.hotel-widget');

				if ( ($('.start-date', hotelWidget).val().length == 0) || ($('.end-date', hotelWidget).val().length == 0) )
				{
					$('.validation-error em').html('Please choose your dates');
					$('.validation-error').show();
					return false;
				}

				var roomsValue = $('.hotel-rooms', hotelWidget).val();
				if ( (roomsValue == 'select-rooms') || (parseInt(roomsValue) <= 0) || (parseInt(roomsValue) > 4) )
				{
					$('.validation-error em').html('Please select a valid room number');
					$('.validation-error').show();
					return false;
				}

				return true;
			}
		};

		// Initialize
		hotelWidget.init();
		hotelWidget.roomsOverlayOpen();
		hotelWidget.roomsOverlayClose();
		hotelWidget.buildQuery();

	});
})(jQuery);

