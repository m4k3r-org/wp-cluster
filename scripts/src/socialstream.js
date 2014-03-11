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
        instagram: {
          id: '#UMEfestival',
          intro: '',
          search: '',
          out: 'intro,thumb',
          accessToken: '44220099.ec4c95b.d2c3acc28b1f432884be1ddcd8733499',
          redirectUrl: 'http://umesouthpadre.com/?page_id=1904&preview=true',
          clientId: 'ec4c95bf89754e1d8fd3581edba04808',
          thumb: 'low_resolution',
          comments: 0,
          likes: 0,
          icon: 'instagram.png'
        }
      },
      wall: that.data('wall'),
      controls: false,
      rotate: {
        delay: parseInt(that.data('rotate_delay'))
      },
      iconPath: that.data('path')+'/images/',
      imagePath: that.data('path')+'/images/'
    });

    return this;
  };

});