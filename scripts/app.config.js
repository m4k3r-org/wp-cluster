/**
 * This file is a prototype for the kind of file that will be generated automatically by extracting all Scripts
 * and removing them from HTML responses on-the-fly.
 *
 *
 * Libs declared in <head> - start loading right away via deps
 * Shims Will usually be blank and rely on UDX definitions.
 *
 * Config (locale) properties become instantly accessible to head and body scripts.
 *
 * Context can be set but it makes it more difficult to reference it later.
 *
 */
require.config( {
  baseUrl: '/assets',
  config: define( 'app.config', {
    yoast: window.yoast = {},
    wp_menufcation: window.wp_menufcation = {
      "element": "#wp_menufication",
      "enable_menufication": "on",
      "headerLogo": "http://umesouthpadre.com/media/2014/01/020a9ba2dd708c48353fe9bc3f4aecb190.png",
      "headerLogoLink": "http://umesouthpadre.com/",
      "menuLogo": "",
      "menuText": "",
      "triggerWidth": "770",
      "addHomeLink": null, "addHomeText": "",
      "addSearchField": null, "hideDefaultMenu": "on",
      "onlyMobile": null, "direction": "left",
      "theme": "dark",
      "disableCSS": "on",
      "childMenuSupport": "on",
      "childMenuSelector": "sub-menu, children",
      "activeClassSelector": "current-menu-item, current-page-item, active",
      "enableSwipe": "on",
      "doCapitalization": null, "supportAndroidAbove": "3.5",
      "disableSlideScaling": null, "toggleElement": "",
      "customMenuElement": "",
      "customFixedHeader": "",
      "addToFixedHolder": "",
      "page_menu_support": null, "wrapTagsInList": "",
      "allowedTags": "DIV, NAV, UL, OL, LI, A, P, H1, H2, H3, H4, SPAN, FORM, INPUT, SEARCH",
      "customCSS": "",
      "is_page_menu": "",
      "enableMultiple": "",
      "is_user_logged_in": ""
    },
    ajaxurl: window.ajaxurl = "http://discodonniepresents.com/manage/admin-ajax.php",
    SlideshowPluginSettings_0: window.SlideshowPluginSettings_0 = {"animation": "slide", "slideSpeed": "1", "descriptionSpeed": "0.4", "intervalSpeed": "5", "slidesPerView": "1", "maxWidth": "0", "aspectRatio": "3:1", "height": "400", "imageBehaviour": "natural", "showDescription": "true", "hideDescription": "true", "preserveSlideshowDimensions": "false", "enableResponsiveness": "true", "play": "true", "loop": "true", "pauseOnHover": "true", "controllable": "true", "hideNavigationButtons": "false", "showPagination": "true", "hidePagination": "true", "controlPanel": "false", "hideControlPanel": "true", "waitUntilLoaded": "true", "showLoadingIcon": "true", "random": "false", "avoidFilter": "true"},
    slideshow_jquery_image_gallery_script_adminURL: window.slideshow_jquery_image_gallery_script_adminURL = "http://www.tandemproperties.com/vendor/wordpress/core/wp-admin/"
  } ),
  paths: {
    'jquery': [ 'http://ajax.aspnetcdn.com/ajax/jQuery/jquery-2.1.0.min', 'http://umesouthpadre.com/wp-includes/js/jquery/jquery' ],
    'jquery.migrate': [ 'http://umesouthpadre.com/wp-includes/js/jquery/jquery-migrate.min' ],
    'jquery.menufication': [ 'http://umesouthpadre.com/vendor/usabilitydynamics/wp-menufication/scripts/jquery.menufication.min' ],
    'jquery.ui.widget': [ 'http://umesouthpadre.com/wp-includes/js/jquery/ui/jquery.ui.widget.min' ],
    'jquery.ui.accordion': [ 'http://umesouthpadre.com/wp-includes/js/jquery/ui/jquery.ui.accordion.min' ],
    'menufication-setup': [ '/vendor/usabilitydynamics/wp-menufication/scripts/menufication-setup' ],
    'admin-bar': [ 'http://discodonniepresents.com/wp-includes/js/admin-bar.min' ]
  },
  deps: [
    'jquery', 'jquery.migrate', 'jquery.menufication', 'menufication-setup', 'admin-bar', 'jquery.ui.widget', 'jquery.ui.accordion', 'app.bootstrap'
  ]
});

/**
 * Bootstraps Application, requiring <head> scripts
 *
 */
define( 'app.bootstrap', function() {
  console.debug( 'app.bootstrap', 'loaded' );

  // header script w/ closure and global variable
  define( '_gaq', function() {
    var _gaq = _gaq || [];
    _gaq.push( ['_setAccount', 'UA-31265686-7'] );
    _gaq.push( ['_setAllowLinker', true], ['_setDomainName', 'umesouthpadre.com'], ['_setCustomVar', 2, 'post_type', 'page', 3], ['_setCustomVar', 3, 'year', '2013', 3], ['_trackPageview'] );

    (function() {
      var ga = document.createElement( 'script' );
      ga.type = 'text/javascript';
      ga.async = true;
      ga.src = 'https://stats.g.doubleclick.net/dc.js';

      var s = document.getElementsByTagName( 'script' )[0];
      s.parentNode.insertBefore( ga, s );
    })();

    // @computed by wp-cluster
    return window._gaq = _gaq;

  } );

  // Standalone method from header - wrap into module. Globalized via the method name.
  define( 'w3tc_popupadmin_bar', function() {
    return window.w3tc_popupadmin_bar = function w3tc_popupadmin_bar( url ) {
      return window.open( url, '', 'width=800,height=600,status=no,toolbar=no,menubar=no,scrollbars=yes' );
    };
  } );

  // header script w/ closure and global variable
  define( '_prum', function() {
    var _prum = [
      ['id', '528c4342abe53dc362000000'],
      ['mark', 'firstbyte', (new Date()).getTime()]
    ];

    (function() {
      var s = document.getElementsByTagName( 'script' )[0]
        , p = document.createElement( 'script' );
      p.async = 'async';
      p.src = '//rum-static.pingdom.net/prum.min.js';
      s.parentNode.insertBefore( p, s );
    })();

    // @computed by wp-cluster
    return window._prum = _prum;

  } );

  // custom variable w/o any closure but a jquery dep.
  define( 'flawless', [ 'jquery' ], function() {
    var flawless = jQuery.extend( true, jQuery.parseJSON( "{\"ajax_url\":\"http:\\\/\\\/discodonniepresents.com\\\/manage\\\/admin-ajax.php\",\"message_submission\":\"Thank you for your message.\",\"header\":{\"header_text\":\"\",\"must_enter_search_term\":\"false\",\"search_input_placeholder\":\"Enter Artist, City, State, or Venue\"},\"location_name\":\"Our location.\",\"remove_empty_widgets\":true,\"location_coords\":{\"latitude\":null,\"longitude\":null},\"is_admin\":true,\"nonce\":\"00091ab84c\"}" ), typeof flawless === "object" ? flawless : {} );

    // @computed by wp-cluster
    return window.flawless = flawless;

  } );

  // Initialize All..
  require( [ '_gaq', 'w3tc_popupadmin_bar', 'flawless', '_prum', 'app.body' ] );

  return { ok: true };

});

define( 'app.body', function() {
  console.debug( 'app.main', 'loaded' );

  document.addEventListener( 'onDomReady', function() {
  } );

  /*
   if( typeof jQuery == 'function' ) {
   jQuery( document ).ready( function() {


   jQuery( '.hdp_share_button a' ).unbind( 'click' );
   jQuery( '.hdp_share_button a' ).click( function( e ) {

   e.preventDefault();
   jQuery( this ).parent().parent().find( '.hdp_share_links' ).toggle();

   } );


   jQuery( '.hdp_share_links' ).mouseleave( function( e ) {


   jQuery( this ).hide();

   } );

   } );
   }

   if( typeof jQuery == 'function' ) {
   jQuery( document ).ready( function() {

   jQuery( '.hdp_share_button a' ).unbind( 'click' );
   jQuery( '.hdp_share_button a' ).click( function( e ) {

   e.preventDefault();
   jQuery( this ).parent().parent().find( '.hdp_share_links' ).toggle();

   } );

   jQuery( '.hdp_share_links' ).mouseleave( function( e ) {

   jQuery( this ).hide();

   } );

   } );
   }

   jQuery( document ).ready( function() {
   if( typeof jQuery.fn.flexslider == "function" ) {
   jQuery( "#hdp_latest_posts-cfct-module-39839e4f449fe8c5c641b1ea92a1cfa0 .hdp_lp_slides_container" ).css( "visibility", "hidden" ).flexslider( {controlsContainer: "#hdp_latest_posts-cfct-module-39839e4f449fe8c5c641b1ea92a1cfa0 .controls_container", animation: "slide", animationSpeed: 500, prevText: "", nextText: "", start: function( slider ) {
   slider.flexAnimate( 1 );
   setTimeout( function() {
   slider.css( "visibility", "visible" );
   }, 500 );
   }} );
   }
   } );
   jQuery( document ).ready( function() {
   if( typeof jQuery.fn.flexslider == "function" ) {
   jQuery( "#hdp_latest_posts-cfct-module-e9dfad2c602db5786e2d24a1e55197cb .hdp_lp_slides_container" ).css( "visibility", "hidden" ).flexslider( {controlsContainer: "#hdp_latest_posts-cfct-module-e9dfad2c602db5786e2d24a1e55197cb .controls_container", animation: "slide", animationSpeed: 500, prevText: "", nextText: "", start: function( slider ) {
   slider.flexAnimate( 1 );
   setTimeout( function() {
   slider.css( "visibility", "visible" );
   }, 500 );
   }} );
   }
   } );
   jQuery( document ).ready( function() {
   if( typeof jQuery.fn.flexslider == "function" ) {
   jQuery( "#hdp_latest_posts-cfct-module-19b378aa3e220a354dfb1f4ad134f1c3 .hdp_lp_slides_container" ).css( "visibility", "hidden" ).flexslider( {controlsContainer: "#hdp_latest_posts-cfct-module-19b378aa3e220a354dfb1f4ad134f1c3 .controls_container", animation: "slide", animationSpeed: 500, prevText: "", nextText: "", start: function( slider ) {
   slider.flexAnimate( 1 );
   setTimeout( function() {
   slider.css( "visibility", "visible" );
   }, 500 );
   }} );
   }
   } );
   jQuery( document ).ready( function() {
   if( typeof jQuery.fn.flexslider == "function" ) {
   jQuery( "#hdp_latest_posts-cfct-module-185b635146d12c8a847825654592b7b0 .hdp_lp_slides_container" ).css( "visibility", "hidden" ).flexslider( {controlsContainer: "#hdp_latest_posts-cfct-module-185b635146d12c8a847825654592b7b0 .controls_container", animation: "slide", animationSpeed: 500, prevText: "", nextText: "", start: function( slider ) {
   slider.flexAnimate( 1 );
   setTimeout( function() {
   slider.css( "visibility", "visible" );
   }, 500 );
   }} );
   }
   } );

   (function( $ ) {
   $( function() {
   $( "#sponsors_scroller" ).simplyScroll();
   } );
   })( jQuery );

   (function() {
   var request, b = document.body, c = 'className', cs = 'customize-support', rcs = new RegExp( '(^|\\s+)(no-)?' + cs + '(\\s+|$)' );

   request = true;

   b[c] = b[c].replace( rcs, ' ' );
   b[c] += ( window.postMessage && request ? ' ' : ' no-' ) + cs;
   }());

   (function( d, s, id ) {
   var js, fjs = d.getElementsByTagName( s )[0];
   if( d.getElementById( id ) ) return;
   js = d.createElement( s );
   js.id = id;
   js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId=326164814179500";
   fjs.parentNode.insertBefore( js, fjs );
   }( document, 'script', 'facebook-jssdk' ));

   */

});

