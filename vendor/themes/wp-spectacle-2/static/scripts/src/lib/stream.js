define(['jquery', 'vendor/caroufredsel-6.2.1/jquery.caroufredsel-6.2.1', 'vendor/masonry-3.1.5/dist/masonry.pkgd'], function( $, caroufredsel, Masonry) {

  return {

    init : function() {

      var msnry = new Masonry( '.masonry-container', {

        columnWidth: '.item',
        itemSelector: '.item',
        gutter: 20,
        isOriginLeft: true,
        isFitWidth: true

      } );


      $( '.masonry-container' ).carouFredSel( {

        width: $('.masonry-container' ).width(),
        height: '350px',

        direction: 'up',

        items: {
          visible: 'odd+2'
        },

        scroll: {
          pauseOnHover: 'immediate-resume'
        },

        auto: {
          items: 1,
          easing: 'linear',
          duration: 30000,
          timeoutDuration: 0
        }
      } );


    }

  }

});