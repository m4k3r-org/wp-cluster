/**
 * Flex slider
 *
 */
define( 'socialstream', [ 'jquery.socialstream', 'jquery.socialstream.wall' ], function() {
  console.debug( 'socialstream', 'loaded' );

  return function domReady() {
    console.debug( 'socialstream', 'dom ready' );

    jQuery(this).dcSocialStream({
      feeds: {
        youtube: {
          id: 'wired'
        }
      }
    });

    return this;
  };

});