/**
 * Our basic festival model
 */
define(
  [
    'global',
    'baseModel'
  ],
  function( _ddp, BaseModel ){
    return BaseModel.extend( {
      _type: 'festival'
    } );
  }
);