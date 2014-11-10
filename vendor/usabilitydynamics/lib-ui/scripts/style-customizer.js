/**
 * Script Ran within iFrame when using Customizer.
 *
 * Logic here can be used to interact with the site preview in real-time.
 *
 * @author potanin@ud
 */
wp.customize( 'customized_css', function( css ) {
  var intent;

  /**
   * Create Element for Hot Swapping Styles
   *
   */
  function createStyleContainer() {

    if( jQuery( '#udx-dynamic-styles' ) ) {
      return;
    }

    // Create New Element and add to <head>
    jQuery( 'head' ).append( jQuery( '<style type="text/css" id="udx-dynamic-styles"></style>' ) );

  }

  /**
   * Update Dynamic Styles
   * 
   * @param css
   */
  function updateStyles( css ) {
    // console.log( 'updateStyles' );

    // Oue dynamically generated style element
    jQuery( 'head #udx-dynamic-styles' ).text( css );

  }

  /**
   * Update Styles
   * 
   * @param css
   */
  function stylesChanged( css ) {
    // console.log( 'stylesChanged', css );

    // Clear Intent
    window.clearTimeout( intent );

    // Pause for Intent Check
    intent = window.setTimeout( function() {
      updateStyles( css );
    }, 200 );

  }

  createStyleContainer();
  stylesChanged( css );

  // Listen for Changes.
  css.bind( stylesChanged );

});

