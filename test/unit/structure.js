/**
 *
 */
module.exports = {
  '.htaccess exists.': fileExists( '.htaccess' ),
  '.gitignore.': fileExists( '.gitignore' ),
  'circle.yml exists.': fileExists( 'circle.yml' ),
  'wp-cli.yml exists.': fileExists( 'wp-cli.yml' ),
  'composer.json exists.': fileExists( 'composer.json' ),
  'vendor exists.': fileExists( 'vendor' )
};


function fileExists( path ) {
  var _path =  require( 'path' ).join( process.env.PWD, path );

  return function( done ) {
    // console.log( 'path', path );

    if( require( 'fs' ).existsSync( _path ) ) {
      return done();
    }

    return done( new Error( 'File not found.' ) );

  }

}