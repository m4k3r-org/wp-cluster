/**
 * Core Tests
 *
 * @author potanin@UD
 */
module.exports = {
  
  'Has readme.txt file': function() {
    require( 'fs' ).existsSync( '../readme.txt' );    
  }
  
}