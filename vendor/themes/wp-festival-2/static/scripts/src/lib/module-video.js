/**
 * Video Module
 *
 * @class videoModule
 */
define( ['jquery', 'http://www.youtube.com/player_api'], function( $ ){

  var videoModule = {

    player: null,

    muted: false,

    initPlayer: function(){
      var that = this;

      this.player = new YT.Player( 'video-module-frame', {
        events: {
          onReady: function(){
            if( that.muted ){
              that.unMutePlayer();
              that.muted = false;
            } else{
              that.mutePlayer();
              that.muted = true;
            }
          }, onStateChange: function(){
            if( that.player.getPlayerState() == 0 ){
              console.log( 'restart' );
              that.player.playVideo();
            }
          }
        }
      } );
    },

    mutePlayer: function(){
      this.player.mute();
    },

    unMutePlayer: function(){
      this.player.setVolume( 50 );
    },

    /**
     * Event for showing the overlay
     *
     * @method eventOpen
     */
    eventPlay: function(){
      var that = this;

      $( '.video-container' ).on( 'click', '.watch-video', function( e ){

        e.preventDefault();

        $( '.video-container .background-content' ).hide();

        $( ".background-content iframe" ).appendTo( ".video-content" );
        that.player.seekTo( 0 );

        $( '.video-container .video-content' ).fadeIn( 400 );

      } );

    },

    eventClose: function(){
      var that = this;

      $( '.video-container' ).on( 'click', '.close-video', function( e ){

        e.preventDefault();

        //     $( '.video-container .background-content' ).fadeIn("slow");
        $( '.video-container .video-content' ).fadeOut( 400, function(){
          $( ".video-content iframe" ).appendTo( ".background-content" );
          $( '.video-container .background-content' ).show();
        } );

      } );
    },

    initSize: function(){

      vwidth = $( window ).innerWidth() + 15;
      vheight = vwidth * 9 /16;

      $( '.background-frame' ).css( {
        width: vwidth + 'px', height: vheight + 'px'
      } );

      // If you want to keep full screen on window resize
      $( window ).resize( function(){
        vwidth = $( window ).innerWidth() + 15;
        vheight = vwidth * 9 /16;

        $( '.background-frame' ).css( {
          width: vwidth + 'px', height: vheight + 'px'
        } );
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
      videoModule.initSize();

      YT.ready( function(){
        videoModule.initPlayer();
      } );

      videoModule.eventPlay();
      videoModule.eventClose();
    }

  }

} );