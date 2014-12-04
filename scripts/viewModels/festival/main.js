define(
  [
    'global',
    'knockout',
    'knockback',
    'baseViewModel'
  ],
  function( _ddp, ko, kb, BaseViewModel ){
    'use strict';
    return BaseViewModel.extend( {
      /**
       * Override the constructor so we can create an observable based on the collection passed
       */
      constructor: function( collection, activeTab ){
        /** Call the parent constructor */
        BaseViewModel.prototype.constructor.apply( this, arguments );
        return this;
      }
    } );
  }
);