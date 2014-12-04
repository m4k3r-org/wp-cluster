/**
 * This returns the object with all our settings, also it sets up the global window variable, as
 * well as incorporates all our functions
 */
define(
  [
    'jquery',
    'config',
    'library/functions',
    'dataset/states'
  ],
  function( $, config, functions, states ){
    var _ddp = window._ddp;
    /** Ok, so first, check to see if we're loaded, and if we are, return our self */
    if( typeof _ddp.loaded == 'boolean' && _ddp.loaded === true ){
      return _ddp;
    }
    /** If we made it here, let's extend that object with our settings */
    $.extend( _ddp, config.ddp );
    /** Now, we're going to extend it with our functions */
    $.extend( _ddp, functions );
    /** Implement our blank data objects */
    _ddp.data = {
      'models': {},
      'viewModels': {},
      'collections': {}
    };
    /** Implement our datasets */
    _ddp.datasets = {
      'states': states
    };
    /** We're loaded, log it */
    _ddp.loaded = true;
    _ddp.log( 'Initialized the global object' );
    /** Ok, return that thing */
    return _ddp;
  }
);