define(
  [
    'global',
    'knockback',
    'baseViewModel',
    'moment'
  ],
  function( _ddp, kb, BaseViewModel, moment ){
    'use strict';
    return BaseViewModel.extend( {
      /**
       * Override the constructor so we can create an observable based on the collection passed
       */
      constructor: function( collection ){
        /** Call the parent constructor */
        BaseViewModel.prototype.constructor.apply( this, arguments );
        /** Set it up! */
        this.festivals = kb.collectionObservable( collection );
        return this;
      },
      /**
       * This function formats our display, as we need some logic there
       */
      displayCombinedDate: function( startDate, endDate ){
        var startMoment = moment( startDate, 'YYYY-MM-DDTHH:mm:ss+Z' );
        var endMoment = moment( endDate, 'YYYY-MM-DDTHH:mm:ss+Z' );
        /** Ok, we return 2 different things if the start/end moment aren't the same */
        return startMoment.format( 'MMM D' ) + '-' + endMoment.format( startMoment.month() != endMoment.month() ? 'MMM D' : 'D' ) + ', ' + endMoment.format( 'YYYY' );
      },
      /**
       * This function is going to set our currently active festival based on the element
       * that is clicked
       */
      changeFestival: function( item, event ){
        /** Ok, first we're going to set the _ddp.data item to show the current festival object */
        _ddp.data.currentFestival = item;
        /** Ok, go ahead and return */
        return true;
      }
    } );
  }
);