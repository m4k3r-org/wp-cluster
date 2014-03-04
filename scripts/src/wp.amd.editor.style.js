/**
 * Script Ran on Customizer Side
 *
 * Handles editor and UI.
 *
 */
define( 'ui.wp.editor.style', function styleEditor( require, exports, module ) {
  console.log( module.id, 'initialized from', module.uri );

  return function callbackOfEditor() {
    console.log( module.id, 'in context' );

    /*

     // Ace Editor
     var wrapper = jQuery( '#udx-style-editor-wrapper' );

     // WordPress Editor
     var realEditor = jQuery( '#udx-style-editor' );

     wrapper
     .css( 'position', 'absolute' )
     .css( 'top', wrapper.position().top )
     .css( 'left', wrapper.position().left )
     .css( 'width', '100%' )
     .css( 'height', '100%' );

     // Instantiate Ace.
     var editor = ace.edit( "udx-style-editor-wrapper" );

     editor.setTheme( "ace/theme/dawn" );
     editor.getSession().setMode( "ace/mode/css" );
     editor.getSession().setUseSoftTabs( true );
     editor.setHighlightActiveLine( false );
     editor.setShowPrintMargin( false );
     editor.getSession().setTabSize( 2 );

     // Get initial content.
     editor.setValue( realEditor.text() );

     // Trigger changes in actual editor.
     editor.on( 'change', function() {
     realEditor.text( editor.getValue() );
     realEditor.trigger( 'change' );
     });

   */

  };

});
