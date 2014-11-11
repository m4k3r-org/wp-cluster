/**
 * Video Module
 *
 * @class videoModule
 */
define( ['jquery', 'http://www.youtube.com/iframe_api', '//wurfl.io/wurfl.js'], function( $ ){

  var videoModule = {

    player: null,

    initPlayer: function(){

      var that = this;

      this.player = new YT.Player( 'video-module-frame', {
        playerVars: {
          html5: 1, showinfo: 0, autohide: 1, wmode: 'transparent', iv_load_policy: 3
        }, videoId: $( '.video-content' ).attr( 'data-ytcode' ), events: {
          onReady: function(){
            that.unMutePlayer();
          }, onStateChange: function(){
            if( that.player.getPlayerState() == 0 ){
              that.player.playVideo();
            }
          }
        }
      } );

    },

    unMutePlayer: function(){
      this.player.setVolume( 100 );
    },

    /**
     * Event for showing the overlay
     *
     * @method eventOpen
     */
    eventPlay: function(){

      var that = this;

      $( '.video-container' ).on( 'click', '.watch-video', function( e ){

        if( WURFL.is_mobile === false ){
          e.preventDefault();

          $( '.video-container .background-content' ).hide();

          $( '.video-container .video-content' ).slideToggle( 500, function(){

            $( 'html, body' ).animate( { scrollTop: $( '#video-module-container' ).offset().top - $( '.top-nav' ).outerHeight() - $('#wpadminbar' ).outerHeight() }, 500 );
            $( '#video-module-container' ).focus();
            that.playFromStart();

          } );

        }

      } );

    },

    playFromStart: function(){
      this.player.seekTo( 0 );
      this.player.playVideo();
    },

    eventClose: function(){

      var that = this;

      $( '.video-container' ).on( 'click', '.close-video', function( e ){

        e.preventDefault();

        $( '.video-container .video-content' ).slideUp( 500, function(){

          $( '.video-container .background-content' ).show();
          $( 'html, body' ).animate( { scrollTop: $( '#video-module-container' ).offset().top - $( '.top-nav' ).outerHeight() - $('#wpadminbar' ).outerHeight() }, 500 );
          that.player.stopVideo();
          
        } );

      } );
    },

    initHTMLVideoSize: function(){

      vwidth = $( window ).innerWidth();
      vheight = vwidth * 9 / 16;

      $( '.bgvideo' ).css( 'width', vwidth );
      $( '.bgimage' ).css( 'width', vwidth );

      // If you want to keep full screen on window resize
      $( window ).resize( function(){
        vwidth = $( window ).innerWidth();
        vheight = vwidth * 9 / 16;

        $( '.bgvideo' ).css( 'width', vwidth );
        $( '.bgimage' ).css( 'width', vwidth );
      } );
    },

    initFrameVideoSize: function(){

      vheight = $(window ).outerHeight() - $( '.top-nav' ).outerHeight() - $('#wpadminbar' ).outerHeight();
      vwidth = vheight * 16 / 9;

      if ( vwidth > $( window ).innerWidth() )
      {
        vwidth = $( window ).innerWidth();
        vheight = vwidth * 9 / 16;
      }

      $( '#video-module-frame' ).css( {
        width: vwidth + 'px', height: vheight + 'px'
      } );

      $( '.video-content' ).css( {
        height: vheight + 'px'
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

      videoModule.initHTMLVideoSize();

      YT.ready( function(){
        videoModule.initPlayer();
      } );

      videoModule.initFrameVideoSize();

      videoModule.eventPlay();
      videoModule.eventClose();

      // If you want to keep full screen on window resize
      $( window ).resize( function(){

        videoModule.initFrameVideoSize();

      });

    }
  }

});