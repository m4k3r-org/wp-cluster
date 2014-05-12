/**
 * Menufication Advanced.
 * Adds additional HTML elements to sidebar menu ( Logo, Social Links )
 *
 */
 
define( 'menufication.advanced', [ 'jquery.menufication' ], function() {
  console.log( 'menufication.advanced', 'loaded' );

  jQuery( document ).on( "menufication-done", function( e, status ){
    
    if( status == 'done' && jQuery( '#menufication-nav' ).length ) {
      
      var container = jQuery( '#menufication-nav' ),
          logo = jQuery( '#menufication_block_logo' ),
          tickets_url = jQuery( '#menufication_block_tickets_url' ),
          social = jQuery( '#menufication_block_social' );
      
      /* Append Logo */
      if( tickets_url.length ) {
        container.prepend( tickets_url );
      }
      
      /* Append Logo */
      if( logo.length ) {
        container.prepend( logo );
      }
      
      /* Append Logo */
      if( social.length ) {
        container.append( social );
      }
      
    }
    
  } );
  
});
