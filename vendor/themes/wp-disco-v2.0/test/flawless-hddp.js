module.exports = {

  "Flawless HDDP": {

    "has expected theme files": {
      "style.css": fileExists( 'style.css' ),
      "functions.php": fileExists( 'functions.php' ),
      "composer.json": fileExists( 'composer.json' )
    }

  }

}

function fileExists( path ) {

  return function() {

    if( !require( 'fs' ).existsSync( './' + path ) ) {
      throw new Error( "Missing " + path + " file." )
    }

  }

}