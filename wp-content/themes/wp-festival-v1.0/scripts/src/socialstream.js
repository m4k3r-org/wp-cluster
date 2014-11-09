/**
 * Social Stream
 *
 */
define( 'socialstream', [ 'jquery.socialstream', 'jquery.socialstream.wall', 'jquery.fancybox', 'jquery.fancybox-media' ], function() {
  console.debug( 'socialstream', 'loaded' );

  return function domReady() {
    console.debug( 'socialstream', 'dom ready' );

    socialStreamInit(jQuery(this));

    jQuery('.section-image a, .section-thumb a').fancybox({
      helpers : {
        media: true
      }
    });

    return this;
  };

});