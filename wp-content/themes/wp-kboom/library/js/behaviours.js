/**
 * File Modified by potanin@UD to add fault-tolerance
 *
 */
jQuery.noConflict();

jQuery( document ).ready( function() {

  /*AUDIO / VIDEO PLAYER STARTS*/
  if( 'undefined' !== typeof jQuery.fn.mediaelementplayer ) {
    jQuery( 'audio,video' ).mediaelementplayer();
  }
  /*AUDIO / VIDEO PLAYER ENDS*/

  /*ACCORDION JQUERY STARTS*/
  /********** jquery toogle function **********/
  jQuery( '#toggle-view li' ).click( function() {
    var text = jQuery( this ).children( 'p' );

    if( text.is( ':hidden' ) ) {
      text.slideDown( '200' );
      jQuery( this ).find( '.toggle-indicator' ).addClass( 'toggle-indicator-minus' );
      jQuery( this ).find( '.toggle-indicator' ).removeClass( 'toggle-indicator-plus' );
    } else {
      text.slideUp( '200' );
      jQuery( this ).find( '.toggle-indicator' ).addClass( 'toggle-indicator-plus' );
      jQuery( this ).find( '.toggle-indicator' ).removeClass( 'toggle-indicator-minus' );
    }
  } );
  /*ACCORDION JQUERY ENDS*/

  /*GOOGLE MAPS STARTS*/
  if( jQuery( '#map' ).length && jQuery() ) {
    var jQuerymap = jQuery( '#map' );
    jQuerymap.gMap( {
      address: 'Level 13, 2 Elizabeth St, Melbourne Victoria 3000 Australia',
      zoom: 18,
      markers: [
        { 'address': 'Level 13, 2 Elizabeth St, Melbourne Victoria 3000 Australia' }
      ]
    } );
  }
  /*GOOGLE MAPS STARTS*/

  /*FIT VIDEOS STARTS*/
  if( 'undefined' !== typeof jQuery.fn.fitVids ) {
    jQuery( ".container" ).fitVids();
  }
  /*FIT VIDEOS ENDS*/

  /*WIDTH RESIZE*/
  var currentWindowWidth = jQuery( window ).width();
  jQuery( window ).resize( function() {
    currentWindowWidth = jQuery( window ).width();
  } );
  /*WIDTH RESIZE*/

  /*FULLWIDTH BACKGROUND IMAGE*/
  //jQuery.backstretch("images/main-bg.jpg");
  /*FULLWIDTH BACKGROUND IMAGE*/

  /*PORTFOLIO ITEM HOVER*/
  if( jQuery( '.portfolio-item-hover-content' ).length && jQuery() ) {
    function hover_effect() {
      jQuery( '.portfolio-item-hover-content' ).hover( function() {
        jQuery( this ).find( 'div,a' ).stop( 0, 0 ).removeAttr( 'style' );
        jQuery( this ).find( '.hover-options' ).animate( {opacity: 0.9}, 'fast' );
        jQuery( this ).find( 'a.zoom' ).animate( {"top": "55%" } );
      }, function() {
        jQuery( this ).find( '.hover-options' ).stop( 0, 0 ).animate( {opacity: 0}, "fast" );
        jQuery( this ).find( 'a.zoom' ).stop( 0, 0 ).animate( {"top": "150%"}, "slow" );
        jQuery( this ).find( 'a.zoom' ).stop( 0, 0 ).animate( {"top": "150%"}, "slow" );
      } );
    }

    hover_effect();
  }

  /*PORTFOLIO ITEM HOVER*/

  /*HOMEPAGE CAROUSEL STARTS*/
  (function() {
    var jQuerycarousel = jQuery( '#projects-carousel' );
    if( jQuerycarousel.length ) {
      var scrollCount;
      if( jQuery( window ).width() < 300 ) {
        scrollCount = 1;
      } else if( jQuery( window ).width() < 300 ) {
        scrollCount = 1;
      } else if( jQuery( window ).width() < 300 ) {
        scrollCount = 1;
      } else {
        scrollCount = 1;
      }
      jQuerycarousel.jcarousel( {
        animation: 600,
        easing: 'easeOutCirc',
        scroll: scrollCount,
        initCallback: function() {
          jQuerycarousel.removeClass( 'loading' )
        }
      } );
    }
  })();
  /*HOMEPAGE CAROUSEL ENDS*/

  /*RESPONSIVE MAIN NAVIGATION STARTS*/
  mainNavChildren( jQuery( "#main-navigation > ul" ), 0 );
  function mainNavChildren( parent, level ) {
    jQuery( parent ).children( "li" ).each( function( i, obj ) {
      var label = "";
      for( var k = 0; k < level; k++ ) {
        label += "&nbsp;&nbsp;&nbsp;&nbsp;";
      }
      label += jQuery( obj ).children( "a" ).text();
      jQuery( "#responsive-main-nav-menu" ).append( "<option value = '" + jQuery( obj ).children( "a" ).attr( "href" ) + "'>" + label + "</option>" );

      if( jQuery( obj ).children( "ul" ).size() == 1 ) {
        mainNavChildren( jQuery( obj ).children( "ul" ), level + 1 );
      }
    } );
  }

  /*RESPONSIVE MAIN NAVIGATION STARTS*/

  /*RESPONSIVE SOCIAL NAVIGATION STARTS*/
  mainNavSocial( jQuery( "#social-icons > ul" ), 0 );
  function mainNavSocial( parent, level ) {
    jQuery( parent ).children( "li" ).each( function( i, obj ) {
      var label = "";
      for( var k = 0; k < level; k++ ) {
        label += "&nbsp;&nbsp;&nbsp;&nbsp;";
      }
      label += jQuery( obj ).children( "a" ).text();
      jQuery( "#responsive-social-menu" ).append( "<option value = '" + jQuery( obj ).children( "a" ).attr( "href" ) + "'>" + label + "</option>" );

      if( jQuery( obj ).children( "ul" ).size() == 1 ) {
        mainNavSocial( jQuery( obj ).children( "ul" ), level + 1 );
      }
    } );
  }

  /*RESPONSIVE SOCIAL NAVIGATION STARTS*/

  /*CAMERA SLIDERS STARTS*/
  if( jQuery( '#camera_wrap_1' ).length && jQuery() ) {
    jQuery( '#camera_wrap_1' ).camera( {
      height: '400px',
      loader: 'bar',
      pagination: false,
      thumbnails: true
    } );
  }
  if( jQuery( '#camera_wrap_2' ).length && jQuery() ) {
    jQuery( '#camera_wrap_2' ).camera( {
      height: '400px',
      thumbnails: true
    } );
  }
  /*CAMERA SLIDERS ENDS*/

  /*TWITTER FEEDS STARTS (CHANGE USERNAME TO YOUR OWN USERNAME)*/
  if( jQuery( '.tweet' ).length && jQuery() ) {
    jQuery( ".tweet" ).tweet( {
      username: "trendywebstar",
      join_text: null,
      avatar_size: null,
      count: 1,
      auto_join_text_default: "we said,",
      auto_join_text_ed: "we",
      auto_join_text_ing: "we were",
      auto_join_text_reply: "we replied to",
      auto_join_text_url: "we were checking out",
      loading_text: "loading tweets..."
    } );
  }
  /*TWITTER FEEDS ENDS*/

  /*TIPSY STARTS*/
  if( jQuery().tipsy ) {
    jQuery( "#social-01" ).tipsy( {gravity: 'n'} );
    jQuery( "#social-02" ).tipsy( {gravity: 'n'} );
    jQuery( "#social-03" ).tipsy( {gravity: 'n'} );
    jQuery( "#social-04" ).tipsy( {gravity: 'n'} );
    jQuery( "#social-05" ).tipsy( {gravity: 'n'} );
    jQuery( "#social-06" ).tipsy( {gravity: 'n'} );
    jQuery( "#social-07" ).tipsy( {gravity: 'n'} );
    jQuery( "#social-07" ).tipsy( {gravity: 'n'} );
    jQuery( "#social-08" ).tipsy( {gravity: 'n'} );
    jQuery( "#social-09" ).tipsy( {gravity: 'n'} );
    jQuery( "#social-10" ).tipsy( {gravity: 'n'} );
    jQuery( "#social-11" ).tipsy( {gravity: 'n'} );
    jQuery( "#social-12" ).tipsy( {gravity: 'n'} );
    jQuery( "#social-13" ).tipsy( {gravity: 'n'} );
    jQuery( "#social-14" ).tipsy( {gravity: 'n'} );
    jQuery( "#social-15" ).tipsy( {gravity: 'n'} );
    jQuery( "#social-16" ).tipsy( {gravity: 'n'} );
    jQuery( "#social-17" ).tipsy( {gravity: 'n'} );
    jQuery( "#social-18" ).tipsy( {gravity: 'n'} );
    jQuery( "#social-19" ).tipsy( {gravity: 'n'} );
    jQuery( "#social-20" ).tipsy( {gravity: 'n'} );
    jQuery( "#social-21" ).tipsy( {gravity: 'n'} );
    jQuery( "#team-01" ).tipsy( {gravity: 's'} );
  }
  /*TIPSY ENDS*/

  /* NAVIGATION JQUERY STARS */
  if( jQuery( '#main-navigation' ).length && jQuery() ) {
    var arrowimages = {down: ['downarrowclass', './images/plus.png', 23], right: ['rightarrowclass', './images/plus-white.png']}
    var jqueryslidemenu = {
      animateduration: {over: 200, out: 100}, //duration of slide in/ out animation, in milliseconds
      buildmenu: function( menuid, arrowsvar ) {
        jQuery( document ).ready( function( jQuery ) {
          var jQuerymainmenu = jQuery( "#" + menuid + ">ul" )
          var jQueryheaders = jQuerymainmenu.find( "ul" ).parent()
          jQueryheaders.each( function( i ) {
            var jQuerycurobj = jQuery( this )
            var jQuerysubul = jQuery( this ).find( 'ul:eq(0)' )
            this._dimensions = {w: this.offsetWidth, h: this.offsetHeight, subulw: jQuerysubul.outerWidth(), subulh: jQuerysubul.outerHeight()}
            this.istopheader = jQuerycurobj.parents( "ul" ).length == 1 ? true : false
            jQuerysubul.css( {top: this.istopheader ? this._dimensions.h + "px" : 0} )
            jQuerycurobj.children( "a:eq(0)" ).css( this.istopheader ? {paddingRight: arrowsvar.down[2]} : {} ).append( '<span class="' + (this.istopheader ? arrowsvar.down[0] : arrowsvar.right[0]) + '" />' )
            jQuerycurobj.hover( function( e ) {
                var jQuerytargetul = jQuery( this ).children( "ul:eq(0)" )
                this._offsets = {left: jQuery( this ).offset().left, top: jQuery( this ).offset().top}
                var menuleft = this.istopheader ? 0 : this._dimensions.w
                menuleft = (this._offsets.left + menuleft + this._dimensions.subulw > jQuery( window ).width()) ? (this.istopheader ? -this._dimensions.subulw + this._dimensions.w : -this._dimensions.w) : menuleft
                if( jQuerytargetul.queue().length <= 1 ) //if 1 or less queued animations
                  jQuerytargetul.css( {left: menuleft + "px", width: this._dimensions.subulw + 'px'} ).slideDown( jqueryslidemenu.animateduration.over )
              }, function( e ) {
                var jQuerytargetul = jQuery( this ).children( "ul:eq(0)" )
                jQuerytargetul.slideUp( jqueryslidemenu.animateduration.out )
              } ) //end hover
            jQuerycurobj.click( function() {
              jQuery( this ).children( "ul:eq(0)" ).hide()
            } )
          } ) //end jQueryheaders.each()
          jQuerymainmenu.find( "ul" ).css( {display: 'none', visibility: 'visible'} )
        } ) //end document.ready
      }
    }
    jqueryslidemenu.buildmenu( "main-navigation", arrowimages )

  }

  jQuery( '#social-links a' ).attr( 'target', '_blank' );

  /* MixItUp PLUGIN JQUERY STARS */
  var jQueryHolder = jQuery( 'ul.portfolio-items-one-fourth, ul.portfolio-items-one-third, ul.portfolio-items-one-half' );

  if( jQueryHolder.mixitup ) {
    jQueryHolder.mixitup( {
      targetSelector: '.item',
      filterSelector: '.filter',
      effects: ['fade'],
      transitionSpeed: 300
    } );
  }

  jQuery( '#filterable li a' ).click( function( e ) {
    jQuery( '#filterable li' ).removeClass( 'active' );

    jQuery( this ).parent().addClass( 'active' );
  } );

  /*PRETTY PHOTO STARTS*/
  jQuery( "a[data-rel^='prettyPhoto'], a.prettyPhoto, a[rel^='lightbox']" ).prettyPhoto( {
    overlay_gallery: false
  } );
  /*PRETTY PHOTO ENDS*/

} );
/* JQUERY ENDS */

// -------------------------------------------------------------------------------------------------------
// Audio
// -------------------------------------------------------------------------------------------------------

// Added by potanin@UD
if( "undefined" !== typeof audiojs ) {

  var audiosplayer = document.getElementsByTagName( 'audio' );
  var a = audiojs.create( audiosplayer, {
      css: false,
      createPlayer: {
        markup: false,
        playPauseClass: 'play-pauseW',
        scrubberClass: 'scrubberW',
        progressClass: 'progressW',
        loaderClass: 'loadedW',
        timeClass: 'timeW',
        durationClass: 'durationW',
        playedClass: 'playedW',
        errorMessageClass: 'error-messageW',
        playingClass: 'playingW',
        loadingClass: 'loadingW',
        errorClass: 'errorW'
      }
    }

  );

  // Load in the first track
  var audio = a[0];
  first = jQuery( 'ol a' ).attr( 'data-src' );

  audio.load( first );

  // Load in a track on click
  jQuery( 'ol li' ).click( function( e ) {
      jQuery( this ).addClass( 'playing' ).siblings().removeClass( 'playing' );
      audio.load( jQuery( 'a', this ).attr( 'data-src' ) );
      audio.play();

    } );

}

