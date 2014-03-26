/**
 * Main Application Scripts
 *
 * @example
 *
 *      // Some Locale String.
 *      require( 'site.locale' ).someWord
 *
 *      // AJAX URL.
 *      require( 'site.model' ).ajax
 *
 */
define( 'app.main', [ 'jquery', 'skrollr' ], function( jQuery ) {
  console.debug( 'app.main', 'loaded' );

  // ghetto fix because w/o it breaks mobile by preventing scrolling - potanin
  if( window.innerWidth > 700 ) {
    //console.log( 'window.screen.availWidth', window.screen.availWidth );

    //window.skrollr.init( { forceHeight: false });

    // Sticky elements implementation
    require( [ 'sticky' ], function() {

      jQuery( ".navbar-top" ).sticky();
      
      //** Inits sticky for all modules which have .sticky selector */
      var st = parseInt( jQuery( ".navbar-top" ).height() );
      jQuery( ".module.sticky" ).each( function( i, e ) {
        jQuery( e ).css( 'width', jQuery( e ).innerWidth() + 'px' );
        jQuery( e ).sticky({
          topSpacing: st
        });
      
      } );

    });

  }

  // Bind Cross Domain Tracking for EventBrite.
  jQuery( 'a[data-track], a[href*=eventbrite]' ).click( function( e ) {
    e.preventDefault();
    _gaq.push([ '_link', e.target.href ]);
    return true;
  });

});