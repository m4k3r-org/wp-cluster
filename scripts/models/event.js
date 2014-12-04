/**
 * Our basic event model
 */
define(
  [
    'global',
    'baseModel'
  ],
  function( _ddp, BaseModel ){
    return BaseModel.extend( {
      _type: 'event'
    } );
  }
);