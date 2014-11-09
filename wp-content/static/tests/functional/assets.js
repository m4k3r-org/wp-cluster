/**
 *
 */
module.exports = {
  //'http://discodonniepresents.com/favicon.ico': checkAsset( 'http://www.usabilitydynamics.com/favicon.ico' ),
  //'http://discodonniepresents.com/sitemap.xml': checkAsset( 'http://www.usabilitydynamics.com/sitemap.xml' )
};

/**
 *
 * @param url
 * @returns {Function}
 * @param callback
 */
function checkAsset( url, callback ) {

  var request = require( 'request' );

  /**
   *
   */
  return function done( _done ) {
    //console.log( 'http://www.usabilitydynamics.com' + url );

    request({
      method: 'head',
      followRedirect: false,
      url: url
    }, function( error, res, body ) {

      if( error ) {
        return _done( error );
      }

      if( res.statusCode !== 200 ) {
        return _done( new Error( 'Response status code incorrect.' ) );
      }

      res.headers.should.have.property( 'etag' );
      res.headers.should.have.property( 'age' );
      res.headers.should.have.property( 'content-length' );

      // UDS specific headers, may not be there on CCI
      //res.headers.should.have.property( 'x-cache' );
      //res.headers.should.have.property( 'x-powered-by' );
      //res.headers.should.have.property( 'x-cacheable' );

      if( 'function' == typeof callback ) {
        return callback( error, res, _done );
      }

      _done();

    });


  }

}

