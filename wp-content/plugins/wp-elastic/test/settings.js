
module.exports = {

  'wpElastic API': {
    '/settings save': function( done ) {

      require( 'request' )({
        url: 'http://localhost/api/elastic/settings',
        auth: {
          'user': 'username',
          'pass': 'password'
        },
        method: 'POST',
        json: true,
        body: {}
      }, function( error, res, body ) {
        console.log( body );

        done( error );

      });

    }
  }

};