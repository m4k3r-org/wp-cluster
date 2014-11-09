/**
 * wpElastic Core.
 *
 * @example
 *
 *    require( [ 'wp-elastic' ], function getElastic( data ) {
 *      console.log( data.version );
 *    })
 *
 * @module wp-elastic
 * @author potanin@UD
 */
define( [ 'knockout' ], function wpElastic( ko ) {
  console.debug( 'wp-elastic', ko );

  return function domReady() {

    var context = this;

    function ViewModel() {
      console.debug( 'ViewModel' );

      this.title = 'wpElastic';
      this.version = '3.3.2';

      jQuery( context ).attr( 'data-view-model', 'ready' );

    }

    ko.applyBindings( new ViewModel, context );

  };

});

