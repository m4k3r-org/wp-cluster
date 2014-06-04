/**
 * Splash
 *
 */
require( ['skrollr', 'jquery', 'udx.utility.imagesloaded' ], function( skrollr, jQuery, imagesloaded ) {
  console.log( 'ready' );

  skrollr.init();

  // Setup variables
  var $window = jQuery( window );
  var $slide = jQuery( '.homeSlide' );
  var $body = jQuery( 'body' );

  imagesloaded( $body, function() {
    // console.log( 'have images' );

    setTimeout( function() {

      // Resize sections
      adjustWindow();

      // Fade in sections
      $body.removeClass( 'loading' ).addClass( 'loaded' );

    }, 800 );

  } );

  function adjustWindow() {

    // Get window size
    var winH = $window.height();
    var winW = $window.width();

    // Keep minimum height 550
    if( winH <= 550 ) {
      winH = 550;
    }

    // Init Skrollr for 768 and up
    if( winW >= 768 ) {

      // Init Skrollr
      var s = skrollr.init( {
        forceHeight: false
      } );

      // Resize our slides
      $slide.height( winH );

      s.refresh( $( '.homeSlide' ) );

    } else {
      skrollr.init().destroy();
    }

    // Check for touch
    if( 'object' === typeof Modernizr ) {
      skrollr.init().destroy();
    }

  }

  function initAdjustWindow() {
    return {
      match: function() {
        adjustWindow();
      },
      unmatch: function() {
        adjustWindow();
      }
    };
  }

  if( 'function' === typeof enquire.register ) {
    enquire.register( "screen and (min-width : 768px)", initAdjustWindow(), false ); // .listen(100);
  }

});

window.enquire = function( t ) {
  "use strict";
  function i( t, i ) {
    var n, s = 0, e = t.length;
    for( s; e > s && (n = i( t[s], s ), n !== !1); s++ );
  }

  function n( t ) {
    return"[object Array]" === Object.prototype.toString.apply( t )
  }

  function s( t ) {
    return"function" == typeof t
  }

  function e( t ) {
    this.options = t, !t.deferSetup && this.setup()
  }

  function o( i, n ) {
    this.query = i, this.isUnconditional = n, this.handlers = [], this.mql = t( i );
    var s = this;
    this.listener = function( t ) {
      s.mql = t, s.assess()
    }, this.mql.addListener( this.listener )
  }

  function r() {
    if( !t )throw Error( "matchMedia not present, legacy browsers require a polyfill" );
    this.queries = {}, this.browserIsIncapable = !t( "only all" ).matches
  }

  return e.prototype = {setup: function() {
    this.options.setup && this.options.setup(), this.initialised = !0
  }, on: function() {
    !this.initialised && this.setup(), this.options.match && this.options.match()
  }, off: function() {
    this.options.unmatch && this.options.unmatch()
  }, destroy: function() {
    this.options.destroy ? this.options.destroy() : this.off()
  }, equals: function( t ) {
    return this.options === t || this.options.match === t
  }}, o.prototype = {addHandler: function( t ) {
    var i = new e( t );
    this.handlers.push( i ), this.mql.matches && i.on()
  }, removeHandler: function( t ) {
    var n = this.handlers;
    i( n, function( i, s ) {
      return i.equals( t ) ? (i.destroy(), !n.splice( s, 1 )) : void 0
    } )
  }, clear: function() {
    i( this.handlers, function( t ) {
      t.destroy()
    } ), this.mql.removeListener( this.listener ), this.handlers.length = 0
  }, assess: function() {
    var t = this.mql.matches || this.isUnconditional ? "on" : "off";
    i( this.handlers, function( i ) {
      i[t]()
    } )
  }}, r.prototype = {register: function( t, e, r ) {
    var h = this.queries, a = r && this.browserIsIncapable;
    return h[t] || (h[t] = new o( t, a )), s( e ) && (e = {match: e}), n( e ) || (e = [e]), i( e, function( i ) {
      h[t].addHandler( i )
    } ), this
  }, unregister: function( t, i ) {
    var n = this.queries[t];
    return n && (i ? n.removeHandler( i ) : (n.clear(), delete this.queries[t])), this
  }}, new r
}( window.matchMedia );

/*! HTML5 Shiv v3.6 stable | @afarkas @jdalton @jon_neal @rem | MIT/GPL2 Licensed */
(function( l, f ) {
  function m() {
    var a = e.elements;
    return"string" == typeof a ? a.split( " " ) : a
  }

  function i( a ) {
    var b = n[a[o]];
    b || (b = {}, h++, a[o] = h, n[h] = b);
    return b
  }

  function p( a, b, c ) {
    b || (b = f);
    if( g )return b.createElement( a );
    c || (c = i( b ));
    b = c.cache[a] ? c.cache[a].cloneNode() : r.test( a ) ? (c.cache[a] = c.createElem( a )).cloneNode() : c.createElem( a );
    return b.canHaveChildren && !s.test( a ) ? c.frag.appendChild( b ) : b
  }

  function t( a, b ) {
    if( !b.cache )b.cache = {}, b.createElem = a.createElement, b.createFrag = a.createDocumentFragment, b.frag = b.createFrag();
    a.createElement = function( c ) {
      return!e.shivMethods ? b.createElem( c ) : p( c, a, b )
    };
    a.createDocumentFragment = Function( "h,f", "return function(){var n=f.cloneNode(),c=n.createElement;h.shivMethods&&(" + m().join().replace( /\w+/g, function( a ) {
      b.createElem( a );
      b.frag.createElement( a );
      return'c("' + a + '")'
    } ) + ");return n}" )( e, b.frag )
  }

  function q( a ) {
    a || (a = f);
    var b = i( a );
    if( e.shivCSS && !j && !b.hasCSS ) {
      var c, d = a;
      c = d.createElement( "p" );
      d = d.getElementsByTagName( "head" )[0] || d.documentElement;
      c.innerHTML = "x<style>article,aside,figcaption,figure,footer,header,hgroup,nav,section{display:block}mark{background:#FF0;color:#000}</style>";
      c = d.insertBefore( c.lastChild, d.firstChild );
      b.hasCSS = !!c
    }
    g || t( a, b );
    return a
  }

  var k = l.html5 || {}, s = /^<|^(?:button|map|select|textarea|object|iframe|option|optgroup)$/i, r = /^<|^(?:a|b|button|code|div|fieldset|form|h1|h2|h3|h4|h5|h6|i|iframe|img|input|label|li|link|ol|option|p|param|q|script|select|span|strong|style|table|tbody|td|textarea|tfoot|th|thead|tr|ul)$/i, j, o = "_html5shiv", h = 0, n = {}, g;
  (function() {
    try {
      var a = f.createElement( "a" );
      a.innerHTML = "<xyz></xyz>";
      j = "hidden"in a;
      var b;
      if( !(b = 1 == a.childNodes.length) ) {
        f.createElement( "a" );
        var c = f.createDocumentFragment();
        b = "undefined" == typeof c.cloneNode || "undefined" == typeof c.createDocumentFragment || "undefined" == typeof c.createElement
      }
      g = b
    } catch( d ) {
      g = j = !0
    }
  })();
  var e = {elements: k.elements || "abbr article aside audio bdi canvas data datalist details figcaption figure footer header hgroup mark meter nav output progress section summary time video", shivCSS: !1 !== k.shivCSS, supportsUnknownElements: g, shivMethods: !1 !== k.shivMethods, type: "default", shivDocument: q, createElement: p, createDocumentFragment: function( a, b ) {
    a || (a = f);
    if( g )return a.createDocumentFragment();
    for( var b = b || i( a ), c = b.frag.cloneNode(), d = 0, e = m(), h = e.length; d < h; d++ )c.createElement( e[d] );
    return c
  }};
  l.html5 = e;
  q( f )
})( this, document );