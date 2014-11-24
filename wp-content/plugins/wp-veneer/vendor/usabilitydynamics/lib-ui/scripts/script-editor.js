/**
 * Script Ran on Customizer Side
 *
 *
 */
(function scriptEditor() {
  // console.log( 'scriptEditor' );

  //var ace = require("lib/ace");
  if( 'object' !== typeof ace ) {
    return console.log( 'scriptEditor is not ready.' );
  }

  var wrapper = jQuery( '#udx-script-editor-wrapper' );
  var realEditor = jQuery( '#udx-script-editor' );

  wrapper
    .css( 'position', 'absolute' )
    .css( 'top', wrapper.position().top )
    .css( 'left', wrapper.position().left )
    .css( 'width', '100%' )
    .css( 'height', '100%' );

  var editor = ace.edit( "udx-script-editor-wrapper" );

  editor.setTheme( "ace/theme/idleFingers" );
  //editor.setTheme( "ace/theme/dawn" );
  //editor.setTheme( "ace/theme/monokai" );

  editor.getSession().setMode( "ace/mode/css" );
  editor.getSession().setUseSoftTabs( true );

  editor.setHighlightActiveLine( false );
  editor.setShowPrintMargin( false );
  editor.getSession().setTabSize( 2 );

  // jQuery( '.wp-full-overlay.expanded' ).animate({ marginLeft: "400px" });
  // jQuery( '.wp-full-overlay.expanded #customize-controls' ).animate({ width: "400px" });

  // Get initial content.
  editor.setValue( realEditor.text() );

  editor.on( 'change', function() {
    realEditor.text( editor.getValue() ).trigger( 'change' );
  });

})();

