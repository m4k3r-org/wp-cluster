/**
 * Our basic event collection
 */
define(
  [
    'global',
    'baseCollection',
    'model/event'
  ],
  function( _ddp, BaseCollection, EventModel ){
    return BaseCollection.extend( {
      _type: 'event',
      _mocksFile: 'events',
      model: EventModel
    } );
  }
);