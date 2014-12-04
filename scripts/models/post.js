/**
 * Our basic post model
 */
define(
  [
    'global',
    'baseModel'
  ],
  function( _ddp, BaseModel ){
    return BaseModel.extend( {
      _type: 'post'
    } );
  }
);