define(
  [
    'global',
    'lodash',
    'knockout',
    'baseViewModel'
  ],
  function( _ddp, _, ko, BaseViewModel ){
    'use strict';
    return BaseViewModel.extend( {
      /**
       * We're overriding this menu to grab the data from the current blog
       */
      constructor: function( collection, activeTab ){
        /** Call the parent constructor */
        BaseViewModel.prototype.constructor.apply( this, arguments );
        /** Apply this ID */
        this._id = new ko.observable( _.isUndefined( _ddp.data.currentFestival ) ? _ddp.defaultBlog : _ddp.data.currentFestival._id() );
        this.title = new ko.observable( _.isUndefined( _ddp.data.currentFestival ) ? _ddp._( 'Festival Menu' ) : _ddp.data.currentFestival.abbreviation() );
        return this;
      }
    } );
  }
);