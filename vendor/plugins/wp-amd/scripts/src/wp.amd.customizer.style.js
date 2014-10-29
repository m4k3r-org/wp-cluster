/**
 * Script Ran within iFrame when using Customizer.
 *
 * Logic here can be used to interact with the site preview in real-time.
 *
 * @author potanin@ud
 */
 
( function( $, args ) {

  // console.log( args );

  /**
   * Create Element for Hot Swapping Styles
   */
  function createStyleContainer() {
    // console.log( 'createStyleContainer' );
    if( $( '#wp_amd_style_preview_container' ).length ) {
      return null;
    }
    var _element = $( '<style type="text/css" id="wp_amd_style_preview_container"></style>' );
    // Create New Element and add to <head>
    $( 'head' ).append( _element );
    // console.log( '_element', _element );
  }

  /**
   * Update Dynamic Styles
   *
   * @param style
   */
  function updateStyles( style ) {
    // Remove original CSS link from head
    var d = document.getElementById( args.link_id );
    if( d ) {
      d.parentNode.removeChild( d );
    }
    // Oue dynamically generated style element
    $( 'head #wp_amd_style_preview_container' ).text( style );
  }

  // Update Styles Live.
  wp.customize( args.name, function( style ) {
    var intent;
    createStyleContainer();
    
    // Listen for Changes.
    style.bind( function ( style ) {
      //console.log( 'stylesChanged', style );
      // Clear Intent
      window.clearTimeout( intent );
      // Pause for Intent Check
      intent = window.setTimeout( function() {
        updateStyles( style );
      }, 200 );
    });

  });

} )( jQuery, wp_amd_themecustomizer );

