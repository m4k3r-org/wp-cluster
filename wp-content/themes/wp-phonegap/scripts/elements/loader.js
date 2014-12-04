/**
 * Ok, we're going to load all the elements in this file, and then register them
 * as components in knockout
 */
define(
  [
    'global',
    'lodash',
    'knockout',
    'element/toolbar/toolbar'
  ],
  function( _ddp, _, ko, Toolbar ){
    /** So, we're just going to register our elemnts with KO */
    ko.components.register( 'toolbar', Toolbar );
  }
);