/**
 * This is our base ViewModel, which extends the Knockback viewModel - every single viewModel should
 * be of this type
 */
define(
  [
    'global',
    'lodash',
    'knockback',
    'moment'
  ],
  function( _ddp, _, kb, moment ){
    'use strict';
    /** Define and return our object */
    return kb.ViewModel.extend( {
      /**
       * Override the construction to add some default computed observables
       *
       * @param model The Backbone model we're working with for the viewModel
       */
      constructor: function( model ){
        /** First, call the parent constructor */
        kb.ViewModel.prototype.constructor.apply( this, arguments );
        /** Return ourselves */
        return this;
      },
      /**
       * Ok, add our 'moment' function that becomes a computed observable
       *
       * @note In your template, you'd use this as such (this works with observables):
       * <div data-bind="text: moment( start_date(), 'ddd, D MMM YYYY' )"></div>
       *
       * @param string dateString The value we're going to be using for the format
       * @param string format The format that we're going for for the date/time
       */
      moment: function( dateString, format ){
        var error = '';
        /** Make sure we have a valid element and format */
        if( !_.isString( dateString ) || !_.isString( format ) ){
          error = 'Invalid arguments to BaseModel.moment()!';
          _ddp.log( error, 'error' );
          return error;
        }
        /** Ok, parse our value */
        var momentObject = moment( dateString, 'YYYY-MM-DDTHH:mm:ss+Z' );
        /** Return the formatted value */
        return momentObject.zone( '+0000' ).format( format );
      },
      /**
       * This function generates an image URL, based on whether or not we're using
       * resize.ly as a service
       *
       * @note In your template, you'd use this as such (this works with observables):
       * <img data-bind="attr: { src: imageUrl( imageSrc() ) }" />
       * <img data-bind="attr: { src: imageUrl( imageSrc(), 50 ) }" />
       * <img data-bind="attr: { src: imageUrl( imageSrc(), 50, 50 ) }" />
       *
       * @param string src The path to the original image URL
       * @param int width The specified width of the image
       * @param int height The specified height of the image
       */
      imageUrl: function( src, width, height ){
        var error = '', actualWidth, actualHeight;
        /** Make sure we have a valid element and format */
        if( !_.isString( src ) ){
          error = 'Invalid arguments to BaseModel.imageUrl()!';
          _ddp.log( error, 'error' );
          return;
        }
        /** Well, if we're not using resizely, just return the src */
        if( !_ddp.useResizely ){
          return src;
        }
        /** If we're here, figure out the width & height */
        actualWidth = _ddp.imageWidth( width );
        /** Ok, so if we have a height, we have to figure it out based on aspect ratio */
        if( isNaN( height ) ){
          actualHeight = '';
        }else{
          actualHeight = parseInt( actualWidth ) * parseInt( height ) / parseInt( width );
        }
        /** Ok, now return our URL with resizely appended */
        src = '//resize.ly/' + actualWidth.toString() + 'x' + actualHeight.toString() + '/' + src;
        return src;
      },
      /**
       * We're going to setup our function to grab state abbreviations from the long name
       *
       * @note In your template, you'd use this as such (this works with observables):
       * <div data-bind="text: stateAbbreviation( start_date() )"></div>
       *
       * @param string state The string that we're going to be using as the long state name
       */
      stateAbbreviation: function( state ){
        var error = '';
        /** Make sure we have a valid element and format */
        if( !_.isString( state ) ){
          error = 'Invalid arguments to BaseModel.stateAbbreviation()!';
          _ddp.log( error, 'error' );
          return error;
        }
        /** Ok, we made it here, we can try to find the state abbreviation */
        var ret = '', checkMe = _.invert( _ddp.datasets.states );
        ret = _.find( checkMe, function( value, key, object ){
          if( state.toLowerCase() == key.toLowerCase() ){
            return true;
          }
        }, this );
        /** If we have a value in ret, return it */
        if( _.isString( ret ) && !_.isEmpty( ret ) ){
          return ret;
        }
        /** If we made it here, just return what we were passed */
        return state;
      },
      /**
       * We use this function for 'if' statements, or for 'visible' statements, it can be used
       * in any bound template if it inherits this viewModel
       *
       * @param string element The element we're looking for locally
       * @param object context The object that we're searching
       */
      has: function( element, context ){
        /** If we're undefined, we're 'this' */
        if( _.isUndefined( context ) ){
          context = this;
        }
        /** If we're not defined */
        if( _.isUndefined( context[ element ] ) ){
          return false;
        }
        /** If we're empty */
        if( _.isEmpty( context[ element ] ) ){
          return false;
        }
        /** By default return the element */
        return context[ element ];
      }
    } );
  }
);