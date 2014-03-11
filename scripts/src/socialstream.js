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
          id: '#umesouthpadre',
          intro: '',
          search: '',
          out: 'intro,text,date',
          retweets: false,
          replies: false,
          images: 'small', // large w: 786 h: 346, thumb w: 150 h: 150, medium w: 600 h: 264, small w: 340 h 150
          url: that.data('path')+'/lib/modules/social-stream/lib/twitter.php',
          icon: 'twitter.png'
        },
//        instagram: {
//          id: 'UMEfestival',
//          intro: 'Posted',
//          search: 'Search',
//          out: 'intro,thumb',
//          accessToken: '',
//          redirectUrl: '',
//          clientId: '',
//          thumb: 'low_resolution',
//          comments: 3,
//          likes: 8,
//          icon: 'instagram.png'
//        }
      },
      wall: true,
      controls: false,
      rotate: {delay: 0},
      debug: true,
      iconPath: that.data('path')+'/images/'
    });

    return this;
  };

});