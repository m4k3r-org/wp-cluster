define(
  [
    'global',
    'lodash',
    'knockout'
  ],
  function( _ddp, _, ko ){
    'use strict';

    return function( params ){
      /** Setup our initial observables */
      this.title = ko.observable( _.isUndefined( params.title ) ? _ddp._( 'Drop Network' ) : params.title );
      this.menuLink = ko.observable( _.isUndefined( params.menuLink ) ? null : params.menuLink );
      this.showClose = ko.observable( _.isUndefined( params.showClose ) ? true : params.showClose );

      /** Setup some functions to see if we sould show the profile button */
      this.isLoggedIn = function(){
        return false;
      };

      /** Our function to determine if we should show the back button */
      this.shouldShowClose = function(){
        /** If we have only 1 page in history, we have nothing to go to */
        if( _.size( _ddp.pages.history ) == 0 || _.size( _ddp.pages.history ) == 1 ){
          return false;
        }
        /** If the last page was the splash page, we have nothing to go to */
        if( _ddp.pages.history[ _ddp.pages.currentKey - 1 ] == '' ){
          return false;
        }
        /** By default, return our observable */
        return this.showClose();
      }

      /** Our function to determine if we should show the worldwide menu */
      this.shouldShowGlobalLink = function(){
        if( _ddp.router.currentPath.indexOf( 'festival/' ) === 0 ){
          return true;
        }else{
          return false;
        }
      }

      return this;
    };
  }
);