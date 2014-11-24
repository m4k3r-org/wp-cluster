/**
 *
 */
module.exports = {
  // 'http://api.discodonniepresents.com/search/':   checkURL( 'http://api.discodonniepresents.com/search' ),
};

/**
 *
 * @param url
 * @returns {Function}
 * @param target
 */
function checkURL( url, target ) {

  var request = require( 'request' );

  return function done( _done ) {

    request({
      method: 'get',
      followRedirect: false,
      url: url,
      q: {
        'knifehand': true,
        'no_cache': true
      }
    }, function( error, res ) {

      if( error ) {
        return _done( new Error( error.message ) );
      }

      // res.should.have.property( 'statusCode', 301 );

      res.headers.should.have.property( 'location', target );
      res.headers.should.have.property( 'server' );

      // res.headers.should.have.property( 'cache-control' );

      //res.headers.should.have.property( 'x-cacheable' );
      //res.headers.should.have.property( 'x-powered-by' );
      //res.headers.should.have.property( 'x-response-time' );
      //res.headers.should.have.property( 'x-environment' );
      //res.headers.should.have.property( 'x-branch' );

      _done();

    });


  }

}

