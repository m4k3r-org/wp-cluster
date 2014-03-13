/**
 * Gallery masonry
 *
 */
define( 'gallery-masonry', [ 'jquery.masonry', 'jquery.colorbox' ], function() {
  console.debug( 'masonry', 'loaded' );

  return function domReady() {
    console.debug( 'masonry', 'dom ready' );

    if ( jQuery(this).parents('.use-masonry').length ) {
      jQuery(this).masonry({
        itemSelector: '.gallery-item'
      });
    }

    if ( jQuery(this).parents('.use-colorbox').length ) {
      jQuery(".gallery-icon a", jQuery(this)).colorbox({rel:'gallery',maxWidth:"95%",maxHeight:"95%"});
    }

    return this;
  };

});