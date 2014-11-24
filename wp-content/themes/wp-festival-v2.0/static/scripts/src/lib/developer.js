/**
 * Developer Utility
 *
 * @class developer
 */
define( [ 'module', 'require', 'exports', 'jquery' ], function DeveloperModule( module, require, exports, $ ) {
  // console.debug( 'DeveloperModule', 'ping2' );

  var j = 4;
  var k = 50;

  function animateME() {
    var obj = {};
    if ( Math.round(Math.random()) ) {
      obj.width =  (j*k)+'px';
    } else {
      obj.height = (j*k)+'px';
    }
    $('div').animate(
      obj,
      500,
      function() {
        j = generateRand(10);
        k = generateRand(70);
        animateME();
      });
  }

  function generateRand(arg) {
    return Math.floor( Math.random()*arg );
  }

  // animateME();

  module.exports = {
    test: true
  }

});