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
      constructor: function( collection, activeTab, pathPrefix ){
        /** Call the parent constructor */
        BaseViewModel.prototype.constructor.apply( this, arguments );
        /** Set it up! */
        this.featured = kb.collectionObservable( collection );
        /** Setup our observable */
        this.activeTab = ko.observable( activeTab );
        this.pathPrefix = ko.observable( _.isUndefined( pathPrefix ) ? 'worldwide' : pathPrefix );
        return this;
      }
    } );
  }
);