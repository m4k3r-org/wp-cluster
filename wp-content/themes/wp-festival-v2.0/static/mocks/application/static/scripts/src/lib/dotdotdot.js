/**
 * Trim long text blocks
 *
 * @class dotdotdot
 */
define( ['jquery', 'components/dotdotdot/src/js/jquery.dotdotdot.min'], function( $, ddd ){

  return {

    /**
     * Initialize the trimming for the news-slider
     *
     * @method init
     * @return void
     */
    init: function(){
      $( '.news-slider .card h3' ).dotdotdot( {
        height: 60
      } );

      $( '.news-slider .card p' ).dotdotdot( {
        height: 48
      } );
    }
  }

} );