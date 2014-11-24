define( ['jquery', 'lib/swipe'], function( $, swipe ){

  var scmfStream = {

    swipe : null,

    init: function(){

      this.hideEmptyFilters();

      if( $( '.social-stream-module-home' ).length ){
        this.arrangeItems();
        this.initSwipe();

        var that = this;

        $( window ).on( 'resizeEnd', function(){
          that.swipe.destroy();
          that.initSwipe();
        } );
      }

    },

    hideEmptyFilters: function(){

      var hasTwitter = false;
      var hasFacebook = false;
      var hasInstagram = false;
      var hasYoutube = false;

      $( '.info-stream' ).children( 'div' ).each( function(){

        if( $( this ).hasClass( 'twitter-streams' ) ){
          hasTwitter = true;
        }

        if( $( this ).hasClass( 'facebook-streams' ) ){
          hasFacebook = true;
        }

        if( $( this ).hasClass( 'instagram-streams' ) ){
          hasInstagram = true;
        }

        if( $( this ).hasClass( 'youtube-streams' ) ){
          hasYoutube = true;
        }
      } );

      if( hasTwitter || hasFacebook || hasInstagram || hasYoutube ){
        $( '.stream-filters' ).show();
      }

      if( !hasTwitter ){
        $( '.social-stream-filters a.twitter' ).hide();
      }

      if( !hasFacebook ){
        $( '.social-stream-filters a.facebook' ).hide();
      }

      if( !hasInstagram ){
        $( '.social-stream-filters a.instagram' ).hide();
      }

      if( !hasYoutube ){
        $( '.social-stream-filters a.youtube' ).hide();
      }

      if( $( window ).width() >= 992 ){
        $( '.social-stream-filters a:visible' ).last().css( {
          'border-right': 'solid 1px #3a375c', 'margin-right': '20px'
        } );
      }

    },

    arrangeItems: function(){

      var tw_cnt = 0;

      $( '.info-stream .single-item' ).each( function(){

        if( $( this ).hasClass( 'twitter-streams' ) ){

          tw_cnt++;

          if( tw_cnt % 2 == 1 ){

            $( this ).next().andSelf().wrapAll( '<div class="social-item-column">' );

          }
        } else
          if( $( this ).hasClass( 'instagram-streams' ) ){

            $( this ).wrapAll( '<div class="social-item-column">' );

          }

      } );

    },

    initSwipe: function(){

      if( document.documentElement.clientWidth <= 480 ){
        this.swipe = swipe.init( '.wp-social-stream .stream-data', '.info-stream', '.single-item', '.wp-social-stream .stream-data .indicator-parent' );
      } else{
        this.swipe = swipe.init( '.wp-social-stream .stream-data', '.info-stream', '.social-item-column', '.wp-social-stream .stream-data .indicator-parent' );
      }
    },

    /**
     * We're going to check the current status of the loop, as we don't want to call init too early
     *
     * This is not called in our current context, so we'll have to just use the window variable
     */
    checkState: function( elements, data ){
      window.scmfStream.checkStateCount++;
      // If we're at the same number as our elements we're done
      if( this.foreach.length == window.scmfStream.checkStateCount ){
        window.scmfStream.init();
      }
    }, checkStateCount: 0

  };

  /** We need this so we can call it from within KO */
  window.scmfStream = scmfStream;

  /** Return ourself */
  return scmfStream;

} );