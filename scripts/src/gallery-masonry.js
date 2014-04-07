/**
 * Gallery masonry
 *
 */
define( 'gallery-masonry', [ 'jquery.masonry', 'jquery.colorbox' ], function() {
  console.debug( 'masonry', 'loaded' );

  return function domReady() {
    console.debug( 'masonry', 'dom ready' );

    var _this = this;

    function init( _this ) {
      if ( jQuery( _this ).parents('.use-masonry').length ) {
      jQuery(_this).masonry({
          itemSelector: '.gallery-item'
        });
      }

      if ( jQuery( _this ).parents('.use-colorbox').length ) {
        jQuery(".gallery-icon a", jQuery(_this)).colorbox({rel:'gallery',maxWidth:"95%",maxHeight:"95%"});
      }
    }

    init( this );
    jQuery(window).load(function() {
      init( _this );
    });

    return this;
  };

});