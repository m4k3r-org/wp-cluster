/**
 * Gallery masonry
 *
 */
define( 'gallery-masonry', [ 'jquery.masonry' ], function() {
  console.debug( 'masonry', 'loaded' );

  return function domReady() {
    console.debug( 'masonry', 'dom ready' );

    if ( jQuery(this).parents('.use-masonry').length ) {
      jQuery(this).masonry({
        itemSelector: '.gallery-item'
      });
    }

    return this;
  };

});