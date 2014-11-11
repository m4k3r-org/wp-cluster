module.exports = {

  before: function() {

    module.credentials = require(process.cwd() + '/wp-content/static/keys/gcs.json');

    module.gcloud = require( 'gcloud' )({
      projectId: 'client-ddp',
      credentials: module.credentials
    })

  },

  'Can get buckets.':  function( done ) {

    var fs = require('fs');

    // Or from elsewhere:
    var storage = module.gcloud.storage();

    storage.getBuckets({ maxResults: 3 },callback );

    function callback(err, buckets) {

      buckets[0].should.have.property( 'metadata' );
      buckets[0].should.have.property( 'name' );
      buckets[0 ].metadata.should.have.property( 'kind' );
      buckets[0 ].metadata.should.have.property( 'id' );
      buckets[0 ].metadata.should.have.property( 'location' );
      buckets[0 ].metadata.should.have.property( 'name' );

      // console.log( require( 'util' ).inspect( buckets[0], { colors: true, depth: 1, showHidden: false } ) );

      done();
    }

    //var bucket = storage.bucket('media.dayafter.com');

  },

  'Can write a single file.':  function( done ) {

    var fs = require('fs');

    var file = module.gcloud.storage().bucket('discodonniepresents.com').file('test-file.md');

    fs.createReadStream( process.cwd() + '/wp-content/static/tests/fixtures/test-file.md' )
      .pipe(file.createWriteStream(), { my: 'custom', properties: 'blah' })
      .on('complete', callbackComplete )
      .on('error', callbackError );

    function callbackError(error) {
      console.log( 'error', error );
      done( error );
    }

    function callbackComplete(result) {
      // console.log( 'success', result );

      result.should.have.property( 'selfLink' );
      result.should.have.property( 'updated' );
      result.should.have.property( 'contentType', 'text/plain' );
      result.should.have.property( 'bucket', 'discodonniepresents.com' );
      result.should.have.property( 'name', 'test-file.md' );

      done();
    }

  },

  'Can get metadata from uploaded file.':  function( done ) {

    var fs = require('fs');

    module.gcloud.storage().bucket('discodonniepresents.com').file( 'test-file.md' ).getMetadata( callback );

    function callback(error, metadata) {
      // console.log( 'success', result );

      if( error ) {
        return done( error );
      }

      metadata.should.have.property( 'id' );
      metadata.should.have.property( 'selfLink' );
      metadata.should.have.property( 'md5Hash' );
      metadata.should.have.property( 'size' );

      done();

    }

  },

  'Can delete a single file.':  function( done ) {

    var fs = require('fs');

    module.gcloud.storage().bucket('discodonniepresents.com').file( 'test-file.md' ).delete( callback );

    function callback(error) {
      // console.log( 'success', result );

      if( error ) {
        return done( error );
      }

      done();

    }

  }

};

