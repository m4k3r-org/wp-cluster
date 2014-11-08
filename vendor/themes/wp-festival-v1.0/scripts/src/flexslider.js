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

    return this;
  };

});