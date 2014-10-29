/**
 * Handles the initialization of our fancybox
 */
define( [ 'jquery', 'components/fancybox2/fancybox2-built' ], function( $, fancybox ){
  
  function MyFancybox(){
  
    /** This is our initialization funciton */
    this.init = function(){

      $( 'a.fancybox' ).fancybox({
          type: 'iframe'
      });
      
      
    };
  
  };
  
  return new MyFancybox();
} );