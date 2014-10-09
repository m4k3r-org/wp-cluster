/**
 * Handle collapse module
 *
 */
define( ['jquery'], function( $ ){

  var blogMain = {

    /**
     * Event for showing the overlay
     *
     * @method eventOpen
     */
    handle: function(){

      var that = this;

      $( 'body' ).on( 'click', '.selected-category', function( e ){

        e.preventDefault();

        if( $( this ).hasClass( 'nav-closed' ) ){
          $( this ).removeClass( 'nav-closed' );

          $( '.mobile-nav a' ).css( 'display', 'block' );
        } else{
          $( this ).addClass( 'nav-closed' );

          $( '.mobile-nav a' ).css( 'display', 'none' );

          $( this ).css( 'display', 'block' );
        }
      } );

    },

    /**
     * Event for flipping the news box by click on share icon
     *
     * @method eventOpenShareWindow
     */
    eventFlipBox: function(){

      $( 'body' ).on( 'click', '.news-single-share', function( e ){

        // Share should appear without flip in case of IE
        if( navigator.appName == 'Microsoft Internet Explorer' ){
          $( this ).closest( '.front' ).hide().siblings( '.back' ).show().css( 'backface-visibility', 'visible' ).css( 'transform', 'none' );

        } else{
          $( this ).closest( '.flipper' ).addClass( 'flipped' );
        }

        e.preventDefault();

      } );

      $( 'body' ).on( 'click', '.share-close', function( e ){

        // Share should disappear without flip in case of IE
        if( navigator.appName == 'Microsoft Internet Explorer' ){
          $( this ).closest( '.back' ).hide().siblings( '.front' ).show();
        } else{
          $( this ).closest( '.flipper' ).removeClass( 'flipped' );
        }

        e.preventDefault();

      } );

    },

    /**
     * Event for opening the individual share windows
     *
     * @method eventOpenShareWindow
     */
    eventOpenShareWindow: function(){
      $( '.social-share-overlay' ).on( 'click', '.share-wrapper a', function( e ){

        e.preventDefault();

        var popUp = window.open( $( this ).attr( 'href' ), 'popupwindow', 'scrollbars=yes,width=800,height=400' );
        popUp.focus();

      } );
    },

    /**
     * Event for adding commentform hashtag before redirect to the article
     * So the comment counts will link it directly to the comment area
     *
     * @method eventClickComments
     */
    eventClickComments: function(){

      $( 'body' ).on( 'click', '.comments-count', function( e ){

        e.preventDefault();

        url = $( this ).closest( 'a' ).attr( 'href' );

        window.location = url + '#comments';

      } );
    }

  };

  return {

    init: function(){

      blogMain.handle();

      blogMain.eventFlipBox();

      blogMain.eventOpenShareWindow();
      blogMain.eventClickComments();

    }
  }

} );