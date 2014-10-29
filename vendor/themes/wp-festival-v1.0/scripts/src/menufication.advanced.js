/**
 * Menufication Advanced.
 * Adds additional HTML elements to sidebar menu ( Logo, Social Links, Tickets URL button )
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
      
      /* Add +/- icons to dropdown menu items */
      container.find( 'li a' ).each( function( i, e ) {
        if( jQuery( e ).data( 'toggle' ) === 'dropdown' ) {
          jQuery( e ).append( '<em class="icon icon-plus"></em>' );
          if( jQuery( e ).next().is( ':visible' ) ) {
            jQuery( e ).find( 'em' ).removeClass( 'icon-plus' ).addClass( 'icon-minus' );
          }
        }
      } );
      container.find( 'li a' ).on( 'click', function() {
        setTimeout( function() {
          container.find( 'li a' ).each( function( i, e ) {
            if( jQuery( e ).next().is( ':visible' ) ) {
              jQuery( e ).find( 'em' ).removeClass( 'icon-plus' ).addClass( 'icon-minus' );
            } else {
              jQuery( e ).find( 'em' ).removeClass( 'icon-minus' ).addClass( 'icon-plus' );
            }
          } );
        }, 50 );            
      } );
      
    }
    
  } );
  
});
