define(
  [
    'global',
    'lodash',
    'baseViewModel'
  ],
  function( _ddp, _, BaseViewModel ){
    'use strict';
    return BaseViewModel.extend( {
      /**
       * Override the constructor so we can create an observable based on the collection passed
       */
      constructor: function( model, pathPrefix ){
        /** Call the parent constructor */
        BaseViewModel.prototype.constructor.apply( this, arguments );
        /** Setup our observable */
        this.pathPrefix = ko.observable( _.isUndefined( pathPrefix ) ? 'worldwide' : pathPrefix );
        return this;
      },
      /**
       * This function is going to format our post content
       */
      formatPostContent: function( content ){
        /** First auto add the paragraphs */
        content = _ddp.autoAddParagraphs( content );
        /** Then remove the shortcodes */
        content = content.replace( /\[.*?\]/ig, '' );
        /** Return the content */
        return content;
      }
    } );
  }
);