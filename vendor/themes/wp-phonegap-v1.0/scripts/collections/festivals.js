/**
 * Our basic festival collection
 */
define(
  [
    'global',
    'baseCollection',
    'model/festival'
  ],
  function( _ddp, BaseCollection, FestivalModel ){
    return BaseCollection.extend( {
      _type: 'festival',
      _mocksFile: 'festivals',
      model: FestivalModel
    } );
  }
);