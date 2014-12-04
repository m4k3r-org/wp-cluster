/**
 * Our wrapper for our ElasticSearch client, this will be included with the ddp global
 * object
 */
define( [
    'global',
    'jquery',
    'lodash',
    '../../components/elasticsearch/elasticsearch.jquery.min'
  ],
  function( _ddp, $, _, elasticsearch ){
    /** See if we already have an instance of the object */
    if( !_.isUndefined( _ddp.esClient ) ){
      return _ddp.esClient;
    }
    /** Setup the object */
    _ddp.esClient = new $.es.Client( {
      host: _ddp.elasticsearch.host
    } );
    /** Return it */
    return _ddp.esClient;
  }
);