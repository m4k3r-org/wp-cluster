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

      $( '#doc' ).on( 'click', '.main-navigation .menu', function( e ){

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
		 * Transform the navigation HTML structure what the WP plugin throws back to
		 * a usable form on desktop view.
		 *
		 * @method transformNavforDesktop
		 */
		transformNavforDesktop: function(){

			var that = this;

			// If transformation already done, don't do it anymore
			if( $( '.right-column', that.overlay ).length == 0 ){

				$( 'nav > ul > li.dropdown', that.overlay ).each(function(key, value){
					$( '.dropdown-toggle', $(value) ).attr('data-pid', $(value).attr('id'));
					$( '.dropdown-menu', $(value) ).attr('data-pid', $(value).attr('id'));
				});

				var rightColumn = $( '<div class="right-column"></div>' );
				rightColumn.appendTo( $( 'nav', that.overlay ) );

				var leftColumn = $( '<div class="left-column"></div>' );
				leftColumn.prependTo( $( 'nav', that.overlay ) );

				$( 'nav > ul > li > ul', that.overlay ).appendTo( rightColumn );
				$( 'nav > ul > li > a', that.overlay ).appendTo( leftColumn );

				$( 'nav > ul' ).remove();
			}
		},

		/**
		 * Transform the navigation HTML structure what the WP plugin throws back to
		 * a usable form on mobile view.
		 *
		 * @method transformNavforMobile
		 */
		transformNavforMobile: function(){

			var that = this;

			// If transformation already done, don't do it anymore
			if( $( '.right-column', that.overlay ).length > 0 ){
				var nav = $( 'nav', that.overlay );

				$( 'nav .left-column > a', that.overlay ).each( function(){

					var t = $( this );

					t.appendTo( nav );

					$( 'nav .right-column .dropdown-menu[data-pid="' + t.data( 'pid' ) + '"]' ).appendTo( nav );

				} );

				$( 'nav .left-column, nav .right-column', that.overlay ).remove();
			}
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

					that.transformNavforDesktop();

        }
        // Mobile view
        else{

					that.transformNavforMobile();

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

      var panels = $( 'nav .dropdown-menu', this.overlay );
      panels.hide();

      $( this.overlay ).on( 'click', 'nav > a, nav .left-column > a', function( e ){

        e.preventDefault();

        var pid = $( this ).data( 'pid' );

        if( !$( this ).hasClass( 'opened-nav-mobile' ) ){
          $( this ).addClass( 'opened-nav-mobile' );
        } else{
          $( this ).removeClass( 'opened-nav-mobile' );
        }

        $( 'nav > a, .left-column > a', that.overlay ).removeClass( 'selected' );
        $( this ).addClass( 'selected' );

        // Desktop view
        if( document.documentElement.clientWidth >= 992 ){
          $( '.right-column .dropdown-menu' ).hide();
          $( '.right-column .dropdown-menu[data-pid="' + pid + '"]' ).show();
        }
        // Mobile view
        else{
          panels.slideUp();

          if( $( this ).hasClass( 'opened-nav-mobile' ) ){
            $( '.dropdown-menu[data-pid="' + pid + '"]' ).slideDown();
          }

          $( 'nav > a, .left-column > a', that.overlay ).each( function(){
            if( $( this ).data( 'pid' ) != pid ){
              $( this ).removeClass( 'opened-nav-mobile' );
            }
          } );

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

			// Always transform to desktop mode when initialized
			navigation.transformNavforDesktop();

      navigation.eventTransformStructure();

      // Initialize accordion effect
      navigation.initAccordion();
    }

  }

} );