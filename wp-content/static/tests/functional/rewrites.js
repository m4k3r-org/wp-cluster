/**
 *
 */
module.exports = {
  'http://discodonniepresents.com': checkURL( 'http://discodonniepresents.com' ),
  //'http://discodonniepresents.com': checkURL( 'http://discodonniepresents.com' ),
  //'http://discodonniepresents.com/sitemap.xml': checkURL( 'http://discodonniepresents.com/sitemap.xml' ),
  //'http://discodonniepresents.com/usability-dynamics-logo.png': checkURL( 'http://discodonniepresents.com/usability-dynamics-logo.png' ),
  //'http://discodonniepresents.com/forums': checkURL( 'http://discodonniepresents.com/forums' ),
  //'http://discodonniepresents.com/products/wp-property': checkURL( 'http://discodonniepresents.com/products/wp-property' )
};

/**
 *
 * @param url
 * @returns {Function}
 * @param callback
 */
function checkURL( url, callback ) {

  var request = require( 'request' );

  /**
   *
   */
  return function done( _done ) {
    //console.log( 'http://discodonniepresents.com' + url );

    request({
      method: 'get',
      followRedirect: false,
      url: url
    }, function( error, res, body ) {
      console.log( 'done', url );

      if( error ) {
        return _done( error );
      }

      if( res.statusCode !== 200 ) {
        return _done( new Error( 'Response status code incorrect: ' + res.statusCode ) );
      }

      // res.headers.should.have.property( 'x-branch', 'production' );
      // res.headers.should.have.property( 'x-varnish', 'true' );
      // res.headers.should.have.property( 'x-cache', 'MISS' );

      if( 'function' == typeof callback ) {
        return callback( error, res, _done );
      }

      _done();

    });


  }

}

