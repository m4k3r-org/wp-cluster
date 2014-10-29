/**
 * Gallery
 *
 * @todo Add imagesloaded module so Isotope isn't bound too early.
 *
 * @author potanin@UD
 */
define( 'udx.ui.gallery', [ 'jquery.isotope', 'jquery.fancybox' ], function Gallery() {
  console.debug( 'udx.ui.gallery', 'loaded' );

  document.addEventListener( 'DOMContentLoaded', function() {
    // console.debug( 'DOMContentLoaded' );
  });

  /**
   * Bind Fancybox.
   *
   */
  function bindFancybox( element ) {

    // data-fancybox-group

    jQuery( 'a', element ).fancybox({
      speedIn: 600,
      speedOut: 200,
      helpers:  {
        title : {
          type : 'inside'
        },
        overlay : {
          showEarly : false
        }
      }
    });

  }

  /**
   * Bind Isotpe.
   *
   */
  function bindIsotope( element ) {

    element.isotope({
      cellsByColumn: {
        columnWidth: 240,
        rowHeight: 360
      }
    });

  }

  /**
   * Execute on DOM Ready.
   *
   */
  return function domnReady() {
    console.debug( 'udx.ui.gallery', 'ready' );

    // Set default optiosn.
    this.options = this.options || {
      isotope: true,
      fancybox: true
    };

    if( this.options.isotope ) {
      bindIsotope( jQuery( this ) );
    }

    if( this.options.fancybox ) {
      bindFancybox( jQuery( this ) );
    }

    return this;

  }


});

