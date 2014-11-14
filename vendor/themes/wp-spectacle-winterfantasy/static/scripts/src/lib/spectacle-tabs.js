/**
 * Add tab functionality if it's found on the page.
 *
 * @class spectacleTabs
 */
define( ['jquery'], function( $ ){

  var spectacleTabs = {

    /**
     * Register the tab navigation items click event
     *
     * @method registerHeader
     * @return void
     */
    registerHeader : function()
    {
      $( '#doc' ).on('click', '.spectacle_navigation_header > .spectacle_navigation_tab', function(e) {

        e.preventDefault();

        var t = $(this);
        var href = t.attr('href');

        // Change the active tab style
        $('.spectacle_navigation_header > .spectacle_navigation_tab' ).removeClass('spectacle_navigation_tab_active');
        t.addClass('spectacle_navigation_tab_active');

        // Show the tab content
        $( '.spectacle_tab_content' ).hide();
        $( href ).show();

      });
    },

    /**
     * Activate the first tab in the tab navigation header
     *
     * @method activateFirstTab
     * @return void
     */
    activateFirstTab : function()
    {
      $('.spectacle_navigation_header > .spectacle_navigation_tab:first' ).trigger('click');
    }

  };

  return {

    init: function(){

      // No tabs were found on the page
      if ( $( '.spectacle_navigation_header' ).length == 0 )
      {
        return false;
      }


      spectacleTabs.registerHeader();
      spectacleTabs.activateFirstTab();

    }
  }

} );
