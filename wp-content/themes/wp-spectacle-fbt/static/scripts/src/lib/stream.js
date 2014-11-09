define(['jquery', 'vendor/caroufredsel-6.2.1/jquery.carouFredSel-6.2.1', 'vendor/masonry-3.1.5/dist/masonry.pkgd', 'vendor/imagesloaded-3.1.8/imagesloaded.pkgd.min' ], function( $, caroufredsel, Masonry, ImagesLoaded) {

  var scmfStream = {

    init : function() {

      // Must do this so padding won't be messed up 
      var msrny, $masonryContainer = jQuery( '.masonry-container' );

      // initialize Masonry after all images have loaded
      ImagesLoaded( $masonryContainer[ 0 ], function() {

        // Do masonry
        msnry = new Masonry( $masonryContainer[ 0 ], {

          columnWidth: '.item',
          itemSelector: '.item',
          gutter: 20,
          isOriginLeft: true,
          isFitWidth: true

        } );

        // Do the carousel
        $masonryContainer.carouFredSel( {

          width: $masonryContainer.width(),
          height: 650,

          direction: 'up',

          circular: true,
          infinite: true,

          items: {
            visible: 'odd+2',
            start: 0,
            height: 'variable'
          },

          scroll: {
            pauseOnHover: 'immediate-resume'
          },

          auto: {
            items: 'page',
            easing: 'linear',
            duration: 0.01,
            timeoutDuration: 0
          }

        } );



      }.bind( this ) );

    },

    /**
     * We're going to check the current status of the loop, as we don't want to call init too early
     *
     * This is not called in our current context, so we'll have to just use the window variable
     */
    checkState : function( elements, data ) {
      window.scmfStream.checkStateCount++;
      // If we're at the same number as our elements we're done */
      if( this.foreach.length == window.scmfStream.checkStateCount ){
        window.scmfStream.init();
      }
    },
    checkStateCount : 0

  };

  /** We need this so we can call it from within KO */
  window.scmfStream = scmfStream;

  /** Return ourself */
  return scmfStream;

});