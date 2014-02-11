/**
 * Global Frontend JavaScript
 *
 * @version 1.0.0
 * @author Insidedesign
 *
 * jshint bitwise:true, curly:true, eqeqeq:true,  browser:true, jquery:true, indent: 2, global  $:false, jQuery:false, moment:false
 */
require( [ '/assets/models/locale', '/assets/models/settings' ], function( locale, settings ) {
  console.log( 'app.bootstrap', 'loaded' );

  require.loadStyle( '/assets/styles/app.main.css' );

});
