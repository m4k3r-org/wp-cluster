/**
 *
 *
 * @todo check http://suncitymusicfestival.com/media/2014/04/2568fa9f52b34de6328f5044555fe7b659-150x150.jpg
 * @todo check http://media.suncitymusicfestival.com/media/2014/04/2568fa9f52b34de6328f5044555fe7b659-150x150.jpg
 *
 *
 */

module.exports = {
  
  'WP-Festival': {
    
    'has': {
      'makefile':               function() { require( 'fs' ).existsSync( '../makefile' );},
      '.htaccess':              function() { require( 'fs' ).existsSync( '../.htaccess' );},
      'circle.yml':             function() { require( 'fs' ).existsSync( '../circle.yml' );},
      'style.css':              function() { require( 'fs' ).existsSync( '../style.css' );},
      'index.php':              function() { require( 'fs' ).existsSync( '../index.php' );},
      'templates/header.php':   function() { require( 'fs' ).existsSync( '../templates/header.php' ); },
      'templates/footer.php':   function() { require( 'fs' ).existsSync( '../templates/footer.php' ); },
      'functions.php':          function() { require( 'fs' ).existsSync( '../functions.php' ); },
      'vendor/bin/composer':    function() { require( 'fs' ).existsSync( '../vendor/bin/composer' ); }
    }
    
  }
  
}