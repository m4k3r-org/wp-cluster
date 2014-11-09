/**
 * Trim long text blocks
 *
 * @class dotdotdot
 */
define( [
  'jquery',
  'components/dotdotdot/src/js/jquery.dotdotdot.min',
], function( $, ddd ){

  return {

    /**
     * Initialize the trimming for the news-slider
     *
     * @method init
     * @return void
     */
    init: function(){
      if( $( '.news-slider .card h3' ).length )
        $( '.news-slider .card h3' ).dotdotdot( {
          height: 60
        } );

      if( $( '.news-slider .card p' ).length )
        $( '.news-slider .card p' ).dotdotdot( {
          height: 48
        } );

      if( $( '.post .flip-container h3' ).length )
        $( '.post .flip-container h3' ).dotdotdot( {
          height: 110
        } );
    }

  }

} );