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

      $( '#doc' ).on( 'click', '.menu', function( e ){

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

      this.overlay.on( 'click', '.icon-close', function( e ){

        e.preventDefault();

        that.overlay.css( 'display', 'none' );
        $( 'html, body' ).removeClass( 'overlay-open' );

      } );
    },

    /**
     * Transforms the HTML structure of the navigation to
     * differentiate between mobile and desktop
     *
     * @method eventTransformStructure
     */
    eventTransformStructure: function(){
      var that = this;

      $( window ).on( 'resizeEnd', function(){

        // Desktop view
        if( (document.documentElement.clientWidth >= 992) ){
          // If transformation already done, don't do it anymore
          if( $( '.right-column', that.overlay ).length == 0 ){
            var rightColumn = $( '<div class="right-column"></div>' );
            rightColumn.appendTo( $( 'nav', that.overlay ) );

            var leftColumn = $( '<div class="left-column"></div>' );
            leftColumn.prependTo( $( 'nav', that.overlay ) );

            $( 'nav .panel', that.overlay ).appendTo( rightColumn );
            $( 'nav > a', that.overlay ).appendTo( leftColumn );
          }
        }
        // Mobile view
        else{
          // If transformation already done, don't do it anymore
          if( $( '.right-column', that.overlay ).length > 0 ){
            var nav = $( 'nav', that.overlay );

            $( 'nav .left-column > a', that.overlay ).each( function(){

              var t = $( this );

              t.appendTo( nav );

              $( 'nav .right-column .panel[data-pid="' + t.data( 'pid' ) + '"]' ).appendTo( nav );

            } );

            $( 'nav .left-column, nav .right-column', that.overlay ).remove();
          }
        }

      } );

      $( window ).trigger( 'resize' );
    },

    /**
     * Accordion effect showing/hiding sub-menus.
     * Effect is only available in mobile view.
     *
     * @method initAccordion
     */
    initAccordion: function(){
      var that = this;

      var panels = $( 'nav .panel', this.overlay );
      panels.hide();

      $( this.overlay ).on( 'click', 'nav > a, nav .left-column > a', function( e ){

        e.preventDefault();

        var pid = $( this ).data( 'pid' );

        $( 'nav > a, .left-column > a', that.overlay ).removeClass( 'selected' );
        $( this ).addClass( 'selected' );

        // Desktop view
        if( document.documentElement.clientWidth >= 992 ){
          $( '.right-column .panel' ).hide();
          $( '.right-column .panel[data-pid="' + pid + '"]' ).show();
        }
        // Mobile view
        else{
          panels.slideUp();
          $( '.panel[data-pid="' + pid + '"]' ).slideDown();
        }
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
      navigation.eventTransformStructure();

      // Initialize accordion effect
      navigation.initAccordion();
    }

  }

} );