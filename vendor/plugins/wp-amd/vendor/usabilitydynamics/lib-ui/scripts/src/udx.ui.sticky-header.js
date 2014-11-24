/**
 * Sticky Header.
 *
 *    header.cloned {
 *      position:fixed;
 *      top:-60px;
 *      transition:0.2s top ease-in;
 *    }
 *
 *    body.down header.clone {
 *      top: 0;
 *      left: 0;
 *      right: 0;
 *      z-index:999;
 *    }
 *
 * @example
 *
 *    <div data-requires="udx.ui.sticky-header" data-class-cloned="is-cloned" class="nav"></div>
 *
 * @example http://www.onlywebpro.com/2013/04/03/make-a-jquery-sticky-header-in-5-minutes/
 * @source http://jsfiddle.net/XyVAG/9/
 */
define( 'udx.ui.sticky-header', [ 'jquery' ], function stickyHeader() {
  console.debug( 'udx.ui.sticky-header', 'Loaded.' );

  return function domnReady() {
    console.debug( 'udx.ui.sticky-header', 'Initialized.' );

    // Get Options.
    var options = {
      cloned: this.getAttribute( 'data-class-cloned' ) || 'udx-cloned',
      down: this.getAttribute( 'data-class-down' ) || 'udx-down'
    };

    var _header = jQuery( this );
    var _clone  = _header.before( _header.clone().addClass( options.cloned ) );
    var _window = jQuery( window );
    var _body   = jQuery( 'body' );

    _window.on( 'scroll', function() {
      _body.toggleClass( options.cloned, ( _window.scrollTop() > 200 ) );
    });

    return this;

  };

});

