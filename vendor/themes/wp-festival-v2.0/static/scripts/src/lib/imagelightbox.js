/**
 * Handles the initialization of our imagelightbox
 */
define( [ 'jquery', 'third-party/imagelightbox' ], function( $, imagelightbox ){

  function ImageLightBox(){
  
    /** This is our initialization funciton */
    this.init = function(){
      
      this.instance = $( 'a.imagelightbox' ).imageLightbox( {
        onStart: function(){
          this.toggleOverlay( 'on' );
        }.bind( this ),
        onEnd: function(){ 
          this.toggleOverlay( 'off' );
          this.toggleIndicator( 'off' );
        }.bind( this ),
        onLoadStart: function(){
          this.toggleIndicator( 'on' );
        }.bind( this ),
        onLoadEnd: function(){
          this.toggleIndicator( 'off' );
        }.bind( this ),
        quitOnDocClick: false
      } );
      
      /** Ok, attach to our image overlay button */
      $( '.imagelightbox-overlay .icon-close' ).on( 'click touchend', function( e ){
        e.preventDefault();
        e.stopPropagation();
        this.instance.quitImageLightbox();
      }.bind( this ) );

    }
    
    /** This function turns our overlay on or off */
    this.toggleOverlay = function( state ){
      if( typeof state != 'string' ){
        state = 'off';
      }
      if( state != 'off' && state != 'on' ){
        state = 'off';
      }
      var $overlay = $( '.imagelightbox-overlay' );
      /** Ok, if our state is off and we have an overlay */
      if( state == 'off' ){
        $overlay.hide();
      }
      /** If our state is on, and we don't have an overlay */
      if( state == 'on' ){
        $overlay.show();
      }
    };

    /** This function turns our loading indicator on or off */
    this.toggleIndicator = function( state ){
      if( typeof state != 'string' ){
        state = 'off';
      }
      if( state != 'off' && state != 'on' ){
        state = 'off';
      }
      var $loading = $( '#imagelightbox-loading' );
      /** Ok, if our state is off and we have an overlay */
      if( state == 'off' ){
        $loading.hide();
      }
      /** If our state is on, and we don't have an overlay */
      if( state == 'on' ){
        $loading.show();
      }
    };
    
  };
  
  return new ImageLightBox();
  
} );