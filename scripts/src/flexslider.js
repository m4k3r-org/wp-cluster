/**
 * Flex slider
 *
 */
define( 'flexslider', [ 'jquery.flexslider' ], function() {
  console.debug( 'flexslider', 'loaded' );

  return function domReady() {
    console.debug( 'flexslider', 'dom ready' );

    var that = jQuery(this);
    that.flexslider({
      animation : that.data('animation'),
      directionNav: false,
      slideshowSpeed: that.data('slideshowSpeed'),
      animationSpeed: that.data('animationSpeed')
    });

//    if ( !that.data('sliderHeight') ) {
//      var maxHeight = 0, lis = jQuery( 'ul li', that );
//      lis.each( function( i, e ){
//        var height = jQuery( e ).height();
//        if( height > maxHeight ){
//          maxHeight = height;
//        }
//      } );
//      lis.each( function( i, e ){
//        jQuery( 'div:first-child', e ).height( maxHeight + 'px' );
//      } );
//    }

    return this;
  };

});