/**
 * This is basically the splash page, it shows a loading indicator, and then in the BG we'll check
 * and initialize any data that we're going to need to store offline
 */
define(
  [
    'global',
    'lodash',
    'knockback'
  ],
  function( _ddp, _, kb ){
    'use strict';
    
    /**
     * Ok, here is where we'll eventually preload all of the data. For now we'll sleep for 3 seconds
     * and go onto the next screen
     *
     * @todo Do this
     */
    _.delay( function(){
      /** Go to the next page */
      _ddp.router.navigate( 'worldwide/menu', { trigger: true } );
    }, 3000 );
    
    return new kb.ViewModel();

  }
);