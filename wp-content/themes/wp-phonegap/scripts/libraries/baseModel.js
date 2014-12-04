/**
 * This is our base model, which extends the Backbone model - every single model should
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
    return Backbone.Model.extend( {
      _type: null, /** Our Elasticsearch 'type' */
      _mocksFile: null, /** If we need to specify a custom mock file, we do so here */
      idAttribute: '_id', /** Our Elasticsearch id, which we send, this is specified by Backbone.js */
      /**
       * Ok, so we're overriding the sync method, because we're going to:
       * 1) Use ElasticSearch as our primary retrieval mechanism
       * 2) Also use localStorage for caching
       *
       * @see http://documentcloud.github.io/backbone/#Sync
       *
       * @param string method The method which we're going to be using (CRUD)
       * @param object model The model we're syncing
       * @param object options If we have callbacks and such you'll see them here
       */
      sync: function( method, model, options ){
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
              var mocksFile = 'contract/models/' + ( _.isString( model._mocksFile ) ? model._mocksFile : model._type ) + '.json';
              require( [ 'json!' + mocksFile ], function( response ){
                /** Call the success function if it is defined */
                if( _.isFunction( options.success ) ){
                  options.success( response );
                }
              } );
            }else{
              /** Ok, we're going to use our ES client */
              es.get( {
                index: 'documents',
                type: model._type,
                id: model.id
              }, function( error, response, status ){
                if( _.isUndefined( error ) ){
                  if( response.found === true ){
                    /** Call the success function if it is defined */
                    if( _.isFunction( options.success ) ){
                      options.success( response );
                    }
                  }else{
                    /** Just set our error */
                    error = 'Could not find ' + model._type + ' with ID of: ' + model.id;
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
       * Override the parse function so we can properly get the attributes from the response object
       */
      parse: function( response ){
        var fields = {};
        /** Ok, we assume the data's good here, so we'll check for both type of items (search results, and regular ES calls */
        if( !_.isUndefined( response._source ) ){
          fields = response._source;
          delete response._source;
        }
        if( !_.isUndefined( response.fields ) ){
          fields = response.fields;
          delete response.fields;
        }
        /** Ok, return it with the extended fields */
        return $.extend( response, fields );
      }
    } );
  }
);