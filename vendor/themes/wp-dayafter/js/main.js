if ( typeof jQuery === 'function' ) {
  jQuery( function () {

    /*================================================================================*/
    /* Remove no-js Class */
    /*================================================================================*/
    jQuery( 'html' ).removeClass( 'no-js' );

    /*================================================================================*/
    /* Fancybox */
    /*================================================================================*/
    jQuery( '#content' ).find( 'a' ).has( 'img' ).addClass( 'fancybox' );

    jQuery( '.fancybox' ).fancybox( {
      padding: 8,
      helpers: {
        overlay: {
          locked: false
        }
      }
    } );

    jQuery( '.fancybox-various' ).fancybox( {
      maxWidth: 800,
      maxHeight: 600,
      fitToView: false,
      autoSize: true,
      closeClick: false,
      openEffect: 'none',
      closeEffect: 'none'
    } );

  } );
}