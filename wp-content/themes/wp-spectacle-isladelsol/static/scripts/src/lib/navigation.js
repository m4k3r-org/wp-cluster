/**
 * Navigation Popup
 *
 * @class navigation
 */
define( ['jquery'], function( $ ){

  var navigation = {

    overlay: null,

    /**
     * Event for showing the overlay
     *
     * @method eventOpen
     */
    eventOpen: function(){
      var that = this;

      $( '#doc' ).on( 'click', '.main-menu', function( e ){

        e.preventDefault();

        that.overlay.css( 'display', 'block' );
        $( 'html, body' ).addClass( 'overlay-open' );

      } );

    },

    /**
     * Event for closing the overlay
     *
     * @method eventClose
     */
    eventClose: function(){
      var that = this;

      this.overlay.on( 'click', '.overlay-close', function( e ){

        e.preventDefault();

        that.overlay.css( 'display', 'none' );
        $( 'html, body' ).removeClass( 'overlay-open' );

      } );
    },

    /**
     * Accordion effect showing/hiding sub-menus.
     * Effect is only available in mobile view.
     *
     * @method initAccordion
     */
    initAccordion: function(){
      var that = this;

      var panels = $( 'nav .dropdown-menu', this.overlay );
      panels.hide();

      $( this.overlay ).on( 'click', '.dropdown .dropdown-toggle', function( e ){

        e.preventDefault();

        var pid = $( this ).data( 'pid' );
        var dropdownMenu = $('.dropdown-menu[data-pid="' + pid + '"]');

        if ( dropdownMenu.length == 0 )
        {
          window.location.href = $( this ).attr( 'href' );
          return;
        }

        $( '.dropdown .dropdown-toggle', that.overlay ).removeClass( 'selected' );
        $( this ).addClass( 'selected' );

        panels.slideUp();
        dropdownMenu.slideDown();
      } );
    }
  };

  return {

    /**
     * Bootstrap the buy tickets plugin
     *
     * @method init
     */
    init: function(){
      navigation.overlay = $( '.navigation-overlay' );

      // Initialize events
      navigation.eventOpen();
      navigation.eventClose();

      // Initialize accordion effect
      navigation.initAccordion();
    }

  }

} );