jQuery( document ).ready(function() {

  /* Emulate TB Dropdown Menu for BuddyPress */
  jQuery( '.navbar-fixed-top .nav li.dropdown' ).mouseenter( function() {    
    jQuery( '.dropdown-menu:first', this).show();
  
  }).mouseleave( function() {
    jQuery( '.dropdown-menu', this).hide();
    
  });

  /** start block. Moved from bubbypress signup form */
  if ( jQuery('div#blog-details').length && !jQuery('div#blog-details').hasClass('show') )
    jQuery('div#blog-details').toggle();

  jQuery( 'input#signup_with_blog' ).click( function() {
    jQuery('div#blog-details').fadeOut().toggle();
  });
  /** end block */

});

/* Helper Functions */

if( typeof bp_clear_profile_field == 'undefined' ) {
  /**
   * Clear values for multiselectbox and radio buttton fields.
   * Based on default BudyPress theme's clear() function.
   * Used by profile form
   *
   * @author peshkov@UD
   */
  bp_clear_profile_field = function ( container ) {
    if( !document.getElementById(container) ) return;
    var container = document.getElementById(container);
    if ( radioButtons = container.getElementsByTagName('INPUT') ) {
      for(var i=0; i<radioButtons.length; i++) {
        radioButtons[i].checked = '';
      }
    }
    if ( options = container.getElementsByTagName('OPTION') ) {
      for(var i=0; i<options.length; i++) {
        options[i].selected = false;
      }
    }
    return;
  }
}

