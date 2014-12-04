/**
 * This is our base collection, which extends the Backbone collection - every single collection should
 * be of this type
 */
define(
  [
    'global',
    'backbone',
    'jquery',
    'elasticsearch'
  ],
  function( _ddp, Backbone, $, es ){
    'use strict';
    /** Define and return our object */
    return Backbone.Collection.extend( {
      _type: null, /** Our Elasticsearch 'type' */
      _mocksFile: null, /** If we need to specify a custom mock file, we do so here */
      /**
       * Ok, so we're overriding the sync method, because we're going to:
       * 1) Use ElasticSearch as our primary retrieval mechanism
       * 2) Also use localStorage for caching
       *
       * @see http://documentcloud.github.io/backbone/#Sync
       *
       * @param string method The method which we're going to be using (CRUD)
       * @param collection model The collection we're syncing
       * @param object options If we have callbacks and such you'll see them here
       */
      sync: function( method, collection, options ){
        options || ( options = {} );
        switch( method ){
          case 'create':
            /** Just call the default */
            return Backbone.sync( method, model, options );
            break;
          case 'update':
            /** Just call the default */
            return Backbone.sync( method, model, options );
            break;
          case 'delete':
            /** Just call the default */
            return Backbone.sync( method, model, options );
            break;
          case 'read':
            /** If we're mocking the requests, we just need to pull from our local contracts */
            if( _ddp.mockRequests ){
              var mocksFile = 'contract/collections/' + ( _.isString( collection._mocksFile ) ? collection._mocksFile : collection._type ) + '.json';
              require( [ 'json!' + mocksFile ], function( response ){
                /** Call the success function if it is defined */
                if( _.isFunction( options.success ) ){
                  /** Return the hits */
                  options.success( response );
                }
              } );
            }else{
              /** Setup our default body argument */
              var body = {};
              /** Determine if we need to add onto the query */
              if( _.isObject( options.body ) && !_.isEmpty( options.body ) ){
                $.extend( true, body, options.body );
              }
              /** Ok, we're going to use our ES client */
              es.search( {
                index: 'documents',
                type: collection._type,
                body: body
              }, function( error, response, status ){
                if( _.isUndefined( error ) ){
                  if( _.isArray( response.hits.hits ) && response.hits.hits.length ){
                    /** Call the success function if it is defined */
                    if( _.isFunction( options.success ) ){
                      options.success( response );
                    }
                  }else{
                    /** Just set our error */
                    error = 'Could not find any ' + collection._type + ' via search!';
                  }
                }else{
                  error = error.message;
                }
                /** Ok, we need to check again to see if we have an error */
                if( !_.isUndefined( error ) ){
                  /** Call the error function if it is defined */
                  if( _.isFunction( options.error ) ){
                    options.error( error );
                  }
                }
              } );
            }
            break;
        }
      },
      /**
       * Ok, so we override the parse function to properly get our models from out hits that we've pulled in
       */
      parse: function( response ){
        /** If we have hits, return those */
        if( !_.isUndefined( response ) && !_.isUndefined( response.hits ) && !_.isUndefined( response.hits.hits ) ){
          return response.hits.hits;
        }
        return response;
      }
    } );
  }
);