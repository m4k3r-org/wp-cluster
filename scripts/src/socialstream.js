/**
 * Social Stream
 *
 */
define( 'socialstream', [ 'jquery.socialstream', 'jquery.socialstream.wall' ], function() {
  console.debug( 'socialstream', 'loaded' );

  return function domReady() {
    console.debug( 'socialstream', 'dom ready' );

    var that = jQuery(this);
    that.dcSocialStream({
      feeds: {
        twitter: {
          id: String(that.data('twitter_search_for')),
          intro: '',
          search: '',
          out: 'intro,image,'+String(that.data('twitter_show_text')),
          retweets: false,
          replies: false,
          images: 'small', // large w: 786 h: 346, thumb w: 150 h: 150, medium w: 600 h: 264, small w: 340 h 150
          url: that.data('callback'),
          icon: 'twitter.png'
        },
        instagram: {
          id: String(that.data('instagram_search_for')),
          intro: '',
          search: '',
          out: 'intro,thumb',
          accessToken: that.data('instagram_access_token'),
          redirectUrl: that.data('instagram_redirect_url'),
          clientId: that.data('instagram_client_id'),
          thumb: 'low_resolution',
          comments: 0,
          likes: 0,
          icon: 'instagram.png'
        },
        facebook: {
          id: String(that.data('facebook_search_for')),
          intro: '',
          out: 'intro,title,text',
          text: 'content',
          comments: 0,
          image_width: 5,
          icon: 'facebook.png'
        },
        youtube: {
          id: String(that.data('youtube_search_for')),
          intro: '',
          search: '',
          out: 'intro,thumb,title',
          feed: 'uploads',
          thumb: '0',
          icon: 'youtube.png'
        }
      },
      wall: that.data('wall'),
      controls: false,
      height: parseInt(that.data('height')),
      rotate: {
        delay: parseInt(that.data('rotate_delay')),
        direction: String(that.data('rotate_direction'))
      },
      iconPath: that.data('path')+'/images/',
      imagePath: that.data('path')+'/images/',
      cache: true,
      limit: parseInt(that.data('limit')),
      max: 'limit'
    });

    jQuery(window).load(function(){
      console.debug('stream loaded');
      if ( that.data('moderate') == '1' ) {
        jQuery('.stream .dcsns-li').prepend('<a href="javascript:;">x</a>');
      }
    });

    return this;
  };

});