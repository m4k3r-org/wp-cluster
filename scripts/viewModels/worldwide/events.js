define(
  [
    'global',
    'knockback',
    'baseViewModel'
  ],
  function( _ddp, kb, BaseViewModel ){
    'use strict';
    return BaseViewModel.extend( {
      /**
       * Override the constructor so we can create an observable based on the collection passed
       */
      constructor: function( collection ){
        /** Call the parent constructor */
        BaseViewModel.prototype.constructor.apply( this, arguments );
        /** Set it up! */
        this.events = kb.collectionObservable( collection );
        return this;
      }
    } );
  }
);