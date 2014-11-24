/**
 * Handles the initialization of our hotel widget
 */
define( [
    'jquery',
    'components/jquery-selectBoxIt/jquery-selectBoxIt-built',
    'components/moment/moment',
    '//wurfl.io/wurfl.js'
  ],
  function( jQuery, sbi, moment ){

    function HotelWidget(){

      this.init = function(){

        var self = this;

        this.selectBoxIt = jQuery( '.custom-select' ).selectBoxIt( {
          'autoWidth': false,
          'copyClasses': 'container'
        } );

        // Setup our dates @todo make this based off widget options
        var festivalStartDate = new Date( 2015, 0, 16 );
        var festivalEndDate = new Date( 2015, 0, 18 );

        jQuery( '.hotel-widget .start-date' ).datepicker( {
          dateFormat: 'yy-mm-dd',
          defaultDate: festivalStartDate,

          onSelect: function( date ){
            moment = window.moment;

            // Setup some of our vars
            var endDateControl = jQuery( '.hotel-widget .end-date' );
            var startDate = jQuery( this ).datepicker( 'getDate' );
            var minDate = moment( startDate ).add( 1, 'days' );

            // Add the same date to the end date if no date was set
            if( endDateControl.datepicker( 'getDate' ) == null ){
              endDateControl.datepicker( 'setDate', minDate.format( 'YYYY-MM-DD' ) );
            }else if( moment( endDateControl.datepicker( 'getDate' ) ).isBefore( startDate ) ){
              endDateControl.datepicker( 'setDate', minDate.format( 'YYYY-MM-DD' ) );
            }

            // Set the min max dates for this date picker
            endDateControl.datepicker( 'option', 'minDate', minDate.format( 'YYYY-MM-DD' ) );
            endDateControl.datepicker( 'option', 'maxDate', minDate.add( 29, 'days' ).format( 'YYYY-MM-DD' ) );
          },

          beforeShowDay: function( date ){
            if( date >= festivalStartDate && date <= festivalEndDate ){
              return [ true, 'hotel-widget-date-highlighted', '' ];
            }
            return [ true, '', '' ];
          }
        } );

        jQuery( '.hotel-widget .end-date' ).datepicker( {
          dateFormat: 'yy-mm-dd',
          defaultDate: festivalEndDate,

          onSelect: function( date ){
            moment = window.moment;

            // Setup some of our vars
            var startDateControl = jQuery( '.hotel-widget .start-date' );
            var endDate = jQuery( this ).datepicker( 'getDate' );
            var maxDate = moment( endDate ).subtract( 1, 'days' );

            // Add the same date to the end date if no date was set
            if( startDateControl.datepicker( 'getDate' ) == null ){
              startDateControl.datepicker( 'setDate', maxDate.format( 'YYYY-MM-DD' ) );
            }else if( moment( startDateControl.datepicker( 'getDate' ) ).isAfter( endDate ) ){
              startDateControl.datepicker( 'setDate', maxDate.format( 'YYYY-MM-DD' ) );
            }

            // Set the min max dates for this date picker
            startDateControl.datepicker( 'option', 'maxDate', maxDate.format( 'YYYY-MM-DD' ) );
          },

          beforeShowDay: function( date ){
            if( date >= festivalStartDate && date <= festivalEndDate ){
              return [ true, 'hotel-widget-date-highlighted', '' ];
            }
            return [ true, '', '' ];
          }
        } );

        /** Alright, show the div now */
        jQuery( '.hotel-widget' ).show( function(){
          self.selectBoxIt.each( function( i, e ){
            jQuery( e ).data( 'selectBox-selectBoxIt' ).refresh();
          } );
        });

        this.roomsOverlayOpen();
        this.roomsOverlayClose();
        this.buildQuery();
      };

      this.roomsOverlayOpen = function(){
        $( '.hotel-widget .hotel-rooms-container' ).on( 'change', 'select', function( e ){

          e.preventDefault();

          var selectValue = $( this ).val();

          if( selectValue == 'select-rooms' ){
            return false;
          }

          // Verify the number of rooms selected, and disable the rest
          selectValue = parseInt( selectValue );
          if( (selectValue <= 0) || (selectValue > 4) ){
            $( this ).val( 'select-rooms' );
            $( '.validation-error em' ).html( 'Invalid room number selected' );
            $( '.validation-error' ).show();

            return false;
          } else{
            // Disable all
            $( '.hotel-rooms-overlay label, .hotel-rooms-overlay .select-container' ).addClass( 'disabled' );
            $( '.hotel-rooms-overlay select' ).attr( 'disabled', 'disabled' );
            $( '.hotel-rooms-overlay select' ).selectBoxIt( 'disable' );

            // Re-enable
            for( var i = 1; i <= selectValue; i++ ){
              $( '.hotel-rooms-overlay label[for="hotel-room-' + i + '"]' ).removeClass( 'disabled' );
              $( '.hotel-rooms-overlay .hotel-room-' + i + '-container' ).removeClass( 'disabled' );
              $( '.hotel-rooms-overlay select.hotel-room-' + i ).selectBoxIt( 'enable' );
            }
          }

          $( '.hotel-rooms-overlay' ).css( 'display', 'block' );
          $( 'html, body' ).addClass( 'overlay-open' );

        } );
      };

      this.roomsOverlayClose = function(){
        $( '.hotel-widget .hotel-rooms-overlay' ).on( 'click', '.ok-button', function( e ){

          e.preventDefault();

          $( '.hotel-rooms-overlay' ).css( 'display', 'none' );
          $( 'html, body' ).removeClass( 'overlay-open' );

        } );
      };

      /**
       * Build the query based on the selected values
       */
      this.buildQuery = function(){
        var that = this;

        $( '.hotel-widget' ).on( 'click', '.search-button', function( e ){

          e.preventDefault();

          // Clear the validation
          $( '.validation-error' ).hide();

          // Close the overlay if it's opened
          $( '.hotel-rooms-overlay' ).css( 'display', 'none' );
          $( 'html, body' ).removeClass( 'overlay-open' );

          // Validate first
          if( that.validate() === true ){
            // Build the query
            var hotelWidget = $( '.hotel-widget' );
            $url = 'https://dayafter.findor.com/results/list?';

            // start date / end date
            $url += 'checkIn=' + $( '.start-date', hotelWidget ).val();
            $url += '&checkOut=' + $( '.end-date', hotelWidget ).val();

            // Add the type if the value is not hotel
            if( $( '.hotel-type', hotelWidget ).val() == 'package' ){
              $url += '&type=package';
            }

            // Add the rooms and people
            var roomNr = parseInt( $( '.hotel-rooms', hotelWidget ).val() );
            for( var i = 1; i <= roomNr; i++ ){
              $url += '&room' + i + '=' + $( '.hotel-room-' + i, hotelWidget ).val();
            }

            // Redirect
            window.open( $url, "_blank" );
          }
        } );

      };

      this.validate = function(){
        var hotelWidget = $( '.hotel-widget' );

        if( ($( '.start-date', hotelWidget ).val().length == 0) || ($( '.end-date', hotelWidget ).val().length == 0) ){
          $( '.validation-error em' ).html( 'Please choose your dates' );
          $( '.validation-error' ).show();
          return false;
        }

        var roomsValue = $( '.hotel-rooms', hotelWidget ).val();
        if( (roomsValue == 'select-rooms') || (parseInt( roomsValue ) <= 0) || (parseInt( roomsValue ) > 4) ){
          $( '.validation-error em' ).html( 'Please select a valid room number' );
          $( '.validation-error' ).show();
          return false;
        }

        return true;
      };
    };

    // Initialize
    return new HotelWidget();

  }
);