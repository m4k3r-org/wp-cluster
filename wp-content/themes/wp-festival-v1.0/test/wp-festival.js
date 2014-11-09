module.exports = {
  
  'WP-Festival': {
    
    'has': {
      'style.css': function() {
        require( 'fs' ).existsSync( '../style.css' );
      },
      
      'index.php': function() {
        require( 'fs' ).existsSync( '../index.php' );
      },
      
      'functions.php': function() {
        require( 'fs' ).existsSync( '../functions.php' );
      }
    
    }
    
  }
  
}