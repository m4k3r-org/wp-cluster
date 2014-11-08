/**
 * ServiceBus
 *
 * https://stats.g.doubleclick.net/dc.js
 * http://www.google-analytics.com/analytics.js
 *
 * @version 0.1.0
 * @returns {Object}
 */
define( 'udx.analytics', [ '//www.google-analytics.com/analytics.js' ], function() {
  console.debug( 'udx.analytics', 'loaded' );

  function Analytics( options ) {
    console.debug( 'udx.analytics', 'Analytics()', arguments );

    //var _gaq = window._gaq = window._gaq || [];

    //window._gaq.push( ['_setAccount', id ]);
    //window._gaq.push( ['_trackPageview' ]);
    //window._gaq.push( ['_setAllowLinker', true], ['_setDomainName', 'umesouthpadre.com'], ['_trackPageview'] );

    ga( 'create', options.id, 'auto' );

    ga( 'send', 'pageview' );

    //require( ["https://stats.g.doubleclick.net/dc.js"] );

  }

  Object.defineProperties( Analytics.prototype, {
    method: {
      value: function method() {

      },
      enumerable: true,
      configurable: true,
      writable: true
    }
  });

  Object.defineProperties( Analytics, {
    create: {
      value: function create( settings ) {
        return new Analytics( settings );
      },
      enumerable: true,
      configurable: true,
      writable: true
    }
  })

  return Analytics;

});

