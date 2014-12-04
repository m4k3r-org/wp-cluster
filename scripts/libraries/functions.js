/**
 * This file will hold all of our globally available functions, which we'll use
 * as a part of the global '_ddp' object, so for example, to use one of these we'll:
 *  1) require( [ 'global' ] );
 *  2) _ddp.{function_name}
 */
define( {
  /** This function is for testing only */
  test: function() {
    alert( 'Successfully called _ddp.test()!' );
  },
  /**
   * This function checks for the existence of the console before logging, and also
   * makes sure we're in debug mode
   */
  log: function( msg, func ){
    /** Make sure that console exists, and we're in debug mode */
    if( typeof console == 'object' && this.debug === true ){
      if( typeof func != 'string' ){
        func = 'log';
      }
      if( typeof console[ func ] == 'function' ){
        /** Call the tyep of log we want */
        console[ func ]( msg );
      }else{
        /** Fallback to normal logging */
        console.log( msg );
      }
    }
  }.bind( window._ddp ),
  /**
   * This is our translation function, for now we're just going to return the string
   * we were already passed
   */
  _: function( str, group ){
    /** We're going to expand upon this later */
    return str;
  }.bind( window._ddp ),
  /**
   * The following 3 functions are a very small implementation of pubsub using jQuery
   *
   * @uses _ddp.pubsub
   *
   * To subscribe to a trigger:
   * _ddp.on( 'action', someFunc );
   * To unsubscribe from a trigger:
   * _ddp.off( 'action', someFunc );
   * To trigger an event:
   * _ddp.fire( 'action' );
   * _ddp.fire( 'action', args );
   */
  on: function(){
    /** Make sure we have an object first */
    if( _.isUndefined( this.pubsub ) ){
      this.pubsub = $( { } );
    }
    /** Bind the event */
    _ddp.log( 'Binding to event: ' + arguments[ 0 ] );
    this.pubsub.bind.apply( this.pubsub, arguments );
  }.bind( window._ddp ),
  off: function(){
    /** Make sure we have an object first */
    if( _.isUndefined( this.pubsub ) ){
      this.pubsub = $( { } );
    }
    /** Unbind the event */
    _ddp.log( 'Unbinding from event: ' + arguments[ 0 ] );
    this.pubsub.unbind.apply( this.pubsub, arguments );
  }.bind( window._ddp ),
  fire: function(){
    /** Make sure we have an object first */
    if( _.isUndefined( this.pubsub ) ){
      this.pubsub = $( { } );
    }
    /** Trigger the event */
    _ddp.log( 'Firing event: ' + arguments[ 0 ] );
    this.pubsub.trigger.apply( this.pubsub, arguments );
  }.bind( window._ddp ),
  /**
   * This function uses the screen width to get the max width/height for images based on the screen DPR, as
   * well as any passed value
   *
   * @param int maxWidth The max width we want for this image, without accounting for DPR
   */
  imageWidth: function( maxWidth ){
    /** If we don't specify a custom width, then it should be the screen width */
    if( isNaN( maxWidth ) ){
      maxWidth = this.screen.width;
    }
    /** Make sure we're an integer, and return */
    return( parseInt( maxWidth ) * parseInt( this.screen.dpr ) );
  }.bind( window._ddp ),
  /**
   * Handles grabbing query string variables in JS
   *
   * @param toGet Use this to get a singular item from the URL vars
   */
  getUrlVars: function( toGet ){
    var vars = [], hash;
    var hashes = window.location.href.slice( window.location.href.indexOf( '?' ) + 1 ).split( '&' );
    for( var i = 0; i < hashes.length; i++ ){
      hash = hashes[ i ].split( '=' );
      vars.push( hash[ 0 ] );
      vars[ hash[ 0 ] ] = hash[ 1 ];
    }
    /** See if we need to return a specific value */
    if( typeof toGet == 'undefined' ){
      return vars;
    }else if( typeof toGet == 'string' && typeof vars[ toGet ] != 'undefined' ){
      return vars[ toGet ];
    }else{
      return;
    }
  },
  /**
   * So, this function sets up our global event listeners, and gets called on initial init
   * only
   *
   * @uses this.swipeTimer
   * @uses this.resizeTimer
   * @uses this.orientationTimer
   */
  registerGlobalEvents: function(){
    /** Swipe event on body */
    this.swipeTimer = _.debounce( this.swipeHandler, 250 );
    $( 'body' ).hammer().bind( 'swipeleft swiperight', this.swipeTimer );
    /** Screen Resize */
    this.resizeTimer = _.debounce( this.screenResizeHandler, 250 );
    $( window ).on( 'resize', this.resizeTimer );
    /** Orientation Change */
    this.orientationTimer = _.debounce( this.orientationChangeHandler, 250 );
    $( window ).on( 'orientationchange', this.orientationTimer );
    /** Handle our function that happens when the page is changed */
    this.on( 'changingPage', this.handleChangingPage );
  }.bind( window._ddp ),
  /**
   * We use this function to change the stylesheet if we're changing
   * between festival and non-festival pages
   */
  handleChangingPage: function( event ){
    /** Ok, if we don't have the original stylesheet set, let's get it */
    if( _.isUndefined( this.$cssSkin ) ){
      this.$cssSkin = $( '#cssSkin' );
      this.originalCssSkin = this.$cssSkin.attr( 'href' );
    }
    /** Ok, now we're going to see if we have a current festival */
    if( _.isUndefined( this.data.currentFestival ) || _.isNull( this.router.currentPath ) ){
      return;
    }
    /** If we made it here, we have a festival, let's change the CSS based on the path */
    var currentCssSkin = this.$cssSkin.attr( 'href' ), festivalCssSkin = this.data.currentFestival.skin();
    if( this.router.currentPath.indexOf( 'festival/' ) === 0 ){
      if( currentCssSkin != festivalCssSkin ){
        /** Ok, we're going to switch skins from the worldwide to the festival */
        this.$cssSkin.attr( 'href', festivalCssSkin );
        /** Log it! */
        this.log( 'Changing from worldwide to festival skin: ' + festivalCssSkin );
      }
    }else{
      if( currentCssSkin != this.originalCssSkin ){
        /** Ok, we're going to switch skins from the festival to the worldwide */
        this.$cssSkin.attr( 'href', this.originalCssSkin );
        /** Log it! */
        this.log( 'Changing from festival to worldwide skin: ' + this.originalCssSkin );
      }
    }
  }.bind( window._ddp ),
  /**
   * The following function handles screen swiping, if we go left we're going back, if we go right we're going
   * forward
   */
  swipeHandler: function( e ){
    e.preventDefault();
    /** If we have only 1 page in history, we have nothing to go to */
    if( _.size( _ddp.pages.history ) == 0 || _.size( _ddp.pages.history ) == 1 ){
      return;
    }
    /** Ok, see if we're swiping right (for back) */
    if( e.type == 'swiperight' ){
      /** If the last page was the splash page, we have nothing to go to */
      if( _ddp.pages.history[ _ddp.pages.currentKey - 1 ] == '' ){
        return;
      }
      /** Ok, go back then */
      window.history.back();
    }
    /** Ok, see if we're swiping left (for forward) */
    if( e.type == 'swipeleft' ){
      /** If we don't have anything ahead of us, we have nothing to go to */
      if( _.isUndefined( _ddp.pages.history[ _ddp.pages.currentKey + 1 ] ) ){
        return;
      }
      /** Ok, go forward then */
      window.history.forward();
    }
  }.bind( window._ddp ),
  /**
   * The following function handles screen resizing, and triggers the global event
   */
  screenResizeHandler: function( e ){
    /** Ok, set our new screen width and height */
    this.screen.width = document.documentElement.clientWidth;
    this.screen.height = document.documentElement.clientHeight;
    /** Fire the event */
    this.fire( 'global::screenresize' );
  }.bind( window._ddp ),
  /**
   * The following function handles an orientation change, and triggers the global event, as well
   * as the screen resize function
   */
  orientationChangeHandler: function( e ){
    /** Fire the event */
    this.fire( 'global::orientationchange' );
    /** Go ahead trigger screen resize */
    this.doScreenResize();
  }.bind( window._ddp ),
  /**
   * This function takes a string, and similar to WordPress, it adds paragraph tags where they are needed
   *
   * @source http://ufku.com/personal/autop
   *
   * @param string s The string we're working with
   */
  autoAddParagraphs: function( s ){
    if (!s || s.search(/\n|\r/) == -1) {
      return s;
    }
    var  X = function(x, a, b) {return x.replace(new RegExp(a, 'g'), b)};
    var  R = function(a, b) {return s = X(s, a, b)};
    var blocks = '(table|thead|tfoot|caption|colgroup|tbody|tr|td|th|div|dl|dd|dt|ul|ol|li|pre|select'
    blocks += '|form|blockquote|address|math|style|script|object|input|param|p|h[1-6])';
    s += '\n';
    R('<br />\\s*<br />', '\n\n');
    R('(<' + blocks + '[^>]*>)', '\n$1');
    R('(</' + blocks + '>)', '$1\n\n');
    R('\r\n|\r', '\n'); // cross-platform newlines
    R('\n\n+', '\n\n');// take care of duplicates
    R('\n?((.|\n)+?)\n\\s*\n', '<p>$1</p>\n');// make paragraphs
    R('\n?((.|\n)+?)$', '<p>$1</p>\n');//including one at the end
    R('<p>\\s*?</p>', '');// under certain strange conditions it could create a P of entirely whitespace
    R('<p>(<div[^>]*>\\s*)', '$1<p>');
    R('<p>([^<]+)\\s*?(</(div|address|form)[^>]*>)', '<p>$1</p>$2');
    R('<p>\\s*(</?' + blocks + '[^>]*>)\\s*</p>', '$1');
    R('<p>(<li.+?)</p>', '$1');// problem with nested lists
    R('<p><blockquote([^>]*)>', '<blockquote$1><p>');
    R('</blockquote></p>', '</p></blockquote>');
    R('<p>\\s*(</?' + blocks + '[^>]*>)', '$1');
    R('(</?' + blocks + '[^>]*>)\\s*</p>', '$1');
    R('<(script|style)(.|\n)*?</\\1>', function(m0) {return X(m0, '\n', '<PNL>')});
    R('(<br />)?\\s*\n', '<br />\n');
    R('<PNL>', '\n');
    R('(</?' + blocks + '[^>]*>)\\s*<br />', '$1');
    R('<br />(\\s*</?(p|li|div|dl|dd|dt|th|pre|td|ul|ol)[^>]*>)', '$1');
    if (s.indexOf('<pre') != -1) {
      R('(<pre(.|\n)*?>)((.|\n)*?)</pre>', function(m0, m1, m2, m3) {
        return X(m1, '\\\\([\'\"\\\\])', '$1') + X(X(X(m3, '<p>', '\n'), '</p>|<br />', ''), '\\\\([\'\"\\\\])', '$1') + '</pre>';
      });
    }
    return R('\n</p>$', '</p>');
  },
  /**
   * This function slugifies any text
   *
   * @param string text The text we're working with
   */
  slugify: function( text ){
    if( !_.isString( text ) ){
      return text;
    }
    return text.toString().toLowerCase()
      .replace( /\s+/g, '-' ) // Replace spaces with -
      .replace( /\//g, '-' ) // Replace '/' with '-'
      .replace( /[^\w\-]+/g, '' ) // Remove all non-word chars
      .replace( /\-\-+/g, '-' ) // Replace multiple - with single -
      .replace( /^-+/, '' ) // Trim - from start of text
      .replace( /-+$/, '' ); // Trim - from end of text
  },
  /**
   * The following functions handle our page state transitions:
   * Source: https://github.com/ccoenraets/PageSlider/blob/master/pageslider.js
   *
   * @note If you only specify the container, we assume that the page is what you're passing in and the
   * body element is the container
   *
   * @param container object jQuery object of the container we're sliding into
   * @param page object jQuery object of the page we're sliding in
   * @param string direction If we want to force a direction (left or right)
   */
  slidePage: function( container, page, direction ){
    // If we don't have both of these defined, then we need to make the 'body' the default element
    if( ( typeof page == 'string' || typeof page == 'undefined' ) && typeof container == 'object' ){
      direction = page;
      page = container;
      container = $( 'body' );
    }
    // Make sure our variables are good
    if( typeof this.pages == 'undefined' ){
      this.pages = {
        current: undefined,
        history: {}
      }
    }
    var count = _.size( this.pages.history ), state = window.location.hash;
    /** If we don't have anything in the history, we know what to do */
    if( count === 0 ){
      this.pages.history[ count ] = state;
      this.pages.currentKey = count;
      this.slidePageFrom( container, page );
      return;
    }
    /** If we only have one page in the history, we know what to do */
    if( count === 1 ){
      this.pages.history[ count ] = state;
      this.pages.currentKey = count;
      this.slidePageFrom( container, page, 'right' );
      return;
    }
    /** If we have the previous page, we're going backwards */
    if( !_.isUndefined( this.pages.history[ this.pages.currentKey - 1 ] ) && this.pages.history[ this.pages.currentKey - 1 ] == state ){
      this.pages.currentKey = this.pages.currentKey - 1;
      this.slidePageFrom( container, page, _.isUndefined( direction ) ? 'left' : direction );
      return;
    }
    /** If we're on the next page, we're going forward */
    if( !_.isUndefined( this.pages.history[ this.pages.currentKey + 1 ] ) && this.pages.history[ this.pages.currentKey + 1 ] == state ){
      this.pages.currentKey = this.pages.currentKey + 1;
      this.slidePageFrom( container, page, _.isUndefined( direction ) ? 'right' : direction );
      return;
    }
    /** Ok, So we're here, we're just going forward and appending the item to the history */
    this.pages.currentKey = this.pages.currentKey + 1;
    this.pages.history[ this.pages.currentKey ] = state;
    /** Go ahead and perform the slide */
    this.slidePageFrom( container, page, _.isUndefined( direction ) ? 'right' : direction );
  }.bind( window._ddp ),
  slidePageFrom: function( container, page, from ){
    // Do our action
    this.fire( 'changingPage' );
    // Make sure our variables are good 
    if( typeof this.pages == 'undefined' ){
      this.pages = {
        current: undefined,
        history: []
      }
    }
    // Add the element
    container.append( page );
    // Add/remove the classes for the container
    if( !_.isUndefined( container.attr( 'class' ) ) ){
      var containerClasses = container.attr( 'class' ).split( /\s+/ );
      $.each( containerClasses, function( i, e ){
        if( e.indexOf( 'location-' ) !== -1 ){
          container.removeClass( e );
        }
      } );
    }
    container.addClass( 'location-' + this.slugify( _ddp.router.currentPath ) );
    // Calculate positioning
    if( !this.pages.current || !from ){
      page.attr( "class", "page center" );
      this.pages.current = page;
      return;
    }
    // Scroll to the top
    $( 'html, body' ).scrollTop( 0 );
    // Position the page at the starting position of the animation
    page.attr( "class", "page " + from );
    this.pages.current.on( 'webkitTransitionEnd', function( e ){
      $( e.target ).remove();
    } );
    // Force reflow. More information here: http://www.phpied.com/rendering-repaint-reflowrelayout-restyle/
    container[ 0 ].offsetWidth;
    // Position the new page and the current page at the ending position of their animation with a transition class indicating the duration of the animation
    page.attr( "class", "page transition center" );
    this.pages.current.attr( "class", "page transition " + ( from === "left" ? "right" : "left" ) );
    this.pages.current = page;
  }.bind( window._ddp )
} );