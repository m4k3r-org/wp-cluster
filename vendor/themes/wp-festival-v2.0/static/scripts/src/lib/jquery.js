/**
 * We're going to load the real jQuery from the components, and then
 * just return window.$
 */
define( [ 'components/jquery/jquery' ], function(){

  return window.$;
   
} );
