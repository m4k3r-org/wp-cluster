/**
 * Sets up the countDown timer, attaches to .count-down tag and calculates the remaining
 * time from today - data-todate attribute.
 *
 * If it reaches to 0 in the current session, it will reset the the date to 7 days again.
 *
 * @class countDown
 * @return {Object} init() function to bootstrap the countDown class
 */
define( ['jquery'], function( $ ){

  var countDown = {

    toDate: null,
    element: $( '.countdown' ),

    day: 0,
    hour: 0,
    minute: 0,
    second: 0,

    timer: null,
    _s: 1000,
    _m: 0,
    _h: 0,
    _d: 0,

    setToDate: function(){

      var dates = [];
      var future_dates = [];
      var todate = null;

      var widget_dates = countdown_data.dates;

      var now = new Date();
      var hoursToAdd = now.getTimezoneOffset() / 60 * -1 - countdown_data.timezone;

      $.each( widget_dates, function(){
        date_elm = new Date( this );
        date_elm.setHours( 0, 0, 0, 0 );
        date_elm.setHours( date_elm.getHours() + hoursToAdd );
        dates.push( new Date( date_elm ) );
      } );

      var date = new Date();

      $.each( dates, function(){
        if( this > date ){
          future_dates.push( this );
        }
      } );

      if( !$.isEmptyObject( future_dates ) ){
        this.toDate = new Date( Math.min.apply( Math, future_dates ) );
      } else{
        var max_future_date = new Date( Math.max.apply( Math, dates ) );

        while( max_future_date < date ){
          max_future_date = new Date( max_future_date.setDate( max_future_date.getDate() + 7 ) );
        }

        this.toDate = new Date( max_future_date );
      }
    },

    /**
     * Get the day, hour, minute, second elements
     *
     * @method getElements
     * @return void
     */
    getElements: function(){
      this.day = $( '.days', this.element );
      this.hour = $( '.hours', this.element );
      this.minute = $( '.minutes', this.element );
      this.second = $( '.seconds', this.element );
    },

    /**
     * Get the 'to date' from the count-down element
     *
     * @method getToDate
     * @return void
     */
    getToDate: function(){
      // this.toDate = new Date( this.element.data( 'todate' ) );
      var now = new Date();

      var hoursToAdd = now.getTimezoneOffset() / 60 * -1 - countdown_data.timezone;

      /** Calculate the time zone difference */
      this.toDate.setHours( this.toDate.getHours() + hoursToAdd );
    },

    /**
     * Calculate the remaining time to the end date
     *
     * @method calcRemaining
     * @return void
     */
    calcRemaining: function(){
      var now = new Date();
      var distance = this.toDate - now;

      if( distance < 0 ){
        this.toDate.setDate( now.getDate() + 7 );
        distance = this.toDate - now;
      }

      if( distance < 0 ){

        this.setToDate();
        this.getElements();
        this.getToDate();
        this.startTimer();

        /*   $( 'strong', this.day ).html( '0' );
         $( 'strong', this.hour ).html( '0' );
         $( 'strong', this.minute ).html( '0' );
         $( 'strong', this.second ).html( '0' );

         clearInterval( this.timer );*/

        return false;
      }

      var seconds = Math.floor( (distance % countDown._m) / countDown._s );
      var minutes = Math.floor( (distance % countDown._h) / countDown._m );
      var hours = Math.floor( (distance % countDown._d) / countDown._h );
      var days = Math.floor( distance / countDown._d );

      $( 'strong', this.day ).html( days );
      $( 'strong', this.hour ).html( hours );
      $( 'strong', this.minute ).html( minutes );
      $( 'strong', this.second ).html( seconds );
    },

    /**
     * Set up an interval to animate the count down.
     *
     * @method startTimer
     * @return void
     */
    startTimer: function(){
      this._s = 1000;
      this._m = this._s * 60;
      this._h = this._m * 60;
      this._d = this._h * 24;

      var that = this;
      this.timer = setInterval( function(){
        return that.calcRemaining();
      }, 1000 );
    }
  };

  return {



    init: function(){

      if( $( ".contest" ).length > 0 ){

        countDown.setToDate();
        countDown.getElements();
        countDown.getToDate();
        countDown.startTimer();
      }

    }

  }

} );
