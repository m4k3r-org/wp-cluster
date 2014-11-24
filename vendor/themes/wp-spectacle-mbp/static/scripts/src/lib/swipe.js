/**
 * Initialize the swipe functionality for different sliders on the page.
 *
 * @class swipe
 */
define( ['jquery', 'vendor/iscroll-5.1.1/build/iscroll'], function( $, iscroll ){

  return {

    /**
     *
     * Bootstrap the swipe.
     *
     * @method init
     * @param {String} container Element which contains the slider, must specify whether class or ID (ex. '.news-slider-container')
     * @param {String} slider Element class or ID name, which contains the items
     * @param {String} elems Element class or ID name of the items
     * @param {String} indicatorElem Parent element of the scroll bar
     * @returns {IScroll} IScroll object
     */
    init: function( container, slider, elems, indicatorElem ){

      var $slider = $( slider );

      var totalWidth = 0;
      $( elems, $slider ).each( function(){
        totalWidth += $( this ).outerWidth( true );
      } );

      $slider.width( totalWidth );

      var scroller = new IScroll( container, {
        scrollX: true,
        scrollY: false,
        eventPassthrough: true,

        keyBindings: {
          left: 37,
          right: 39
        },

        indicators: {
          el: indicatorElem,
          listenX: true,
          listenY: false,
          interactive: true
        }
      } );

      return scroller;
    },

    /**
     * Destroy the scroller
     *
     * @method destroy
     * @param {IScroll} scroller The IScroll object returned from the init() method
     * @return {Boolean} true is successful
     */
    destroy: function( scroller ){
      if( scroller !== null ){
        scroller.destroy();

        return true;
      }

      return false;
    }
  }
} );
