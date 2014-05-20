/**
 * This file is a prototype for the kind of file that will be generated automatically by extracting all Scripts
 * and removing them from HTML responses on-the-fly.
 *
 * Libs declared in <head> - start loading right away via deps
 * Shims Will usually be blank and rely on UDX definitions.
 *
 * Config (locale) properties become instantly accessible to head and body scripts.
 *
 * Context can be set but it makes it more difficult to reference it later.
 *
 */

(function( c ){
  
  console.debug( 'app configuration', c );
  
  require({
    baseUrl: '/assets/scripts',
    config: define( 'app.config', {
      analytics: window.analytics = {},
      wp_menufication: window.wp_menufication = c.menufication,
      ajaxurl: window.ajaxurl = "http://" + window.location.hostname + "/manage/admin-ajax.php"
    } ),
    paths: {
      'jquery': [ 'http://ajax.aspnetcdn.com/ajax/jQuery/jquery-2.1.0.min' ],
      'jquery.migrate': [ '/wp-includes/js/jquery/jquery-migrate.min' ],
      'jquery.ui.widget': [ '/wp-includes/js/jquery/ui/jquery.ui.widget.min' ],
      'jquery.ui.accordion': [ '/wp-includes/js/jquery/ui/jquery.ui.accordion.min' ],
      'admin-bar': [ '/wp-includes/js/admin-bar.min' ],
      'jquery.flexslider' : [ '/assets/scripts/jquery.flexslider' ],
      'jquery.socialstream' : [ '/vendor/usabilitydynamics/wp-festival/lib/modules/social-stream/scripts/jquery.social.stream.1.5.5.custom' ],
      'jquery.socialstream.wall' : [ '/vendor/usabilitydynamics/wp-festival/lib/modules/social-stream/scripts/jquery.social.stream.wall.1.3' ],
      'jquery.masonry' : [ '/wp-includes/js/jquery/jquery.masonry.min' ],
      'jquery.colorbox' : [ '/assets/scripts/jquery.colorbox' ],
      /* Menufication files */
      'jquery.menufication': [ '/vendor/usabilitydynamics/wp-menufication/scripts/jquery.menufication.min' ],
      'menufication-setup': [ '/vendor/usabilitydynamics/wp-menufication/scripts/menufication-setup' ],
      'menufication.advanced': [ '/vendor/usabilitydynamics/wp-festival/scripts/menufication.advanced' ]
    },
    deps: [ 'jquery', 'app.bootstrap' ],
    shim: {
      'menufication-setup': {
        deps: [ 'jquery', 'jquery.menufication', 'menufication.advanced' ]
      },
      'jquery.menufication': {
        deps: [ 'jquery' ]
      },
      'menufication.advanced': {
        deps: [ 'jquery.menufication' ]
      },
      'jquery.flexslider': {
        deps: [ 'jquery' ]
      },
      'jquery.socialstream': {
        deps: [ 'jquery' ]
      },
      'jquery.socialstream.wall': {
        deps: [ 'jquery.socialstream' ]
      },
      'jquery.masonry': {
        deps: [ 'jquery' ]
      }
    }
  });

  /**
   * Bootstraps Application, requiring <head> scripts
   *
   */
  define( 'app.bootstrap', [ 'menufication-setup' ], function() {
    console.debug( 'app.bootstrap', 'loaded' );

    window._gaq = window._gaq || [];

    window._gaq.push( [ '_setAccount', 'UA-31265686-7' ] );

    window._gaq.push(
      [ '_setAllowLinker', true ],
      [ '_setDomainName', window.location.hostname ],
      [ '_setCustomVar', 3, 'year', '2013', 3 ],
      [ '_trackPageview' ]
    );

    (function() {
      var ga = document.createElement( 'script' );
      ga.type = 'text/javascript';
      ga.async = true;
      ga.src = 'https://stats.g.doubleclick.net/dc.js';
      var s = document.getElementsByTagName( 'script' )[0];
      s.parentNode.insertBefore( ga, s );
    })();

    window._prum = [
      [ 'id', '528c4342abe53dc362000000' ],
      [ 'mark', 'firstbyte', (new Date()).getTime() ]
    ];

    (function() {
      var s = document.getElementsByTagName( 'script' )[0];
      var p = document.createElement( 'script' );
      p.async = 'async';
      p.src = '//rum-static.pingdom.net/prum.min.js';
      s.parentNode.insertBefore( p, s );
    })();

    jQuery('#menufication-nav li li a').on('click',function(e){
      e.stopPropagation();
    });

    // Load main theme logic.
    require( [ 'app.main', 'twitter.bootstrap', 'udx.wp.spa' ] );

  });
  
})( _theme_app_config );
 

