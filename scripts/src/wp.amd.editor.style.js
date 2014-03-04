/**
 * Script Ran on Customizer Side
 *
 * Handles editor and UI.
 *
 */

( function( $, ace ) {

  return;

  // Ace Editor
  if( !$( '#wp-amd-style-editor' ).length ) {
    $( 'body' ).append( '<div id="wp_amd_style_editor_wrapper"><div id="wp_amd_style_editor"></div></div>' );
  }
  
  // WordPress Editor
  var defEditor = $( '#wp_amd_default_style_editor' );
  
  var editor = ace.edit( "wp_amd_style_editor" );
  
  editor.setTheme( "ace/theme/dawn" );
  editor.getSession().setMode( "ace/mode/css" );
  editor.getSession().setUseSoftTabs( true );
  editor.setHighlightActiveLine( false );
  editor.setShowPrintMargin( false );
  editor.getSession().setTabSize( 2 );
  
  // Get initial content.
  editor.setValue( defEditor.text() );

  // Trigger changes in actual editor.
  editor.on( 'change', function() {
    defEditor.text( editor.getValue() );
    defEditor.trigger( 'change' );
  });
  
  $( '#wp_amd_style_editor_wrapper' ).resizable({
    handles: 'e',
    start: function(event, ui) {
      //$( '.customize-preview' )
    },
    stop: function(event, ui) {
      console.log( 'FINISH' );
    }
  });

} )( jQuery, ace );