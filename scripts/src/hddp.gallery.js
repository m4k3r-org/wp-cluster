/**
 * hdp_gallery Shortcode JavaScript
 *
 * @version 1.0.0
 * @author peshkov@UD
 */
 
( function( $ ){
  
  $( window ).load( function(){
  
    function hdp_init_gallery( el ) {
      
      if( typeof $.fn.flexslider !== "function" ) {
        return false;
      }
      
      $( '.tabs_anchor', el ).click( function() {
        $( '.hdp_gallery_tab', el ).removeClass( 'active' );

        switch ( $( this ).data( 'anchor' ) ) {
          case 'gallery':
            $( '.tabs_anchor', el ).data( 'anchor', 'list' );
            $( '.tabs_anchor .icon', el ).removeClass( 'icon-events' ).addClass( 'icon-hdp_photo_gallery' );
            $( '.tabs_anchor .text', el ).html( 'Show As Gallery' );
            break;
          case 'list':
            $( '.tabs_anchor', el ).data( 'anchor', 'gallery' );
            $( '.tabs_anchor .icon', el ).removeClass( 'icon-hdp_photo_gallery' ).addClass( 'icon-events' );
            $( '.tabs_anchor .text', el ).html( 'Show As List' );
            break;
        }
        
        $( '.hdp_gallery_tab.tab-' + $( this ).data( 'anchor' ), el ).addClass( 'active' );
        
        return false;
        
      } );
          
      $( '.hdp_gallery_slider', el ).css( 'visibility', 'hidden' ).flexslider( {
        selector: '.slides > li',
        controlsContainer: $( '.hdp_gallery_slider .controls_container', el ).get(0),
        animation: "slide", 
        prevText: "Prev", 
        nextText: "Next",
        startAt: 0,
        slideshow: false,
        animationSpeed: 600,
        // Hack. https://github.com/woothemes/FlexSlider/issues/292
        start: function( slider ) {
          slider.flexAnimate( 1 );
          setTimeout( function() {
            slider.css( 'visibility', 'visible' );
          }, 600 );
        }
      } );
      
      el.show();
      
      $( '.hdp_gallery_tab.active', el ).show();
      
      return true;
      
    }
    
    // Loop all hdp_gallery items and init them
    $( '.hdp_gallery' ).each( function( i, e ) {
      hdp_init_gallery( $( this ) );
    } );
  
  } );

}( jQuery ) );