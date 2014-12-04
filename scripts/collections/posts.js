/**
 * Our basic posts collection
 */
define(
  [
    'global',
    'baseCollection',
    'model/post'
  ],
  function( _ddp, BaseCollection, PostModel ){
    return BaseCollection.extend( {
      _type: 'post',
      _mocksFile: 'posts',
      model: PostModel
    } );
  }
);