define(
  [
    'global',
    'lodash',
    'baseViewModel'
  ],
  function( _ddp, _, BaseViewModel ){
    'use strict';
    return BaseViewModel.extend( {
      /** Override the construction to add some computed observables */
      constructor: function( model ){
        /** Call the parent constructor */
        BaseViewModel.prototype.constructor.apply( this, arguments );
        /** Build our active tab */
        this.activeTab = ko.observable( 'info' );
        /** Return */
        return this;
      },
      /** Changes our tabs */
      changeTab: function( viewModel, e ){
        e.preventDefault();
        var $ct = $( e.currentTarget ), newTab = $ct.attr( 'data-tab-target' );
        /** Setup the new tab */
        _ddp.log( 'WorldWideEvent ViewModel change to: ' + newTab );
        this.activeTab( newTab );
      }
    } );
  }
);