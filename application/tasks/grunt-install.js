module.exports = function( grunt ) {

  grunt.registerTask( 'installPlugins', 'Install all plugins declared in composer.extra.', function( task ) {

    var done = this.async();
    var async = require( 'async' );
    var composer = grunt.file.readJSON( 'composer.json' );
    var exec = require('child_process').exec;

    var cargo = async.queue(function (plugin, callback) {
      grunt.log.writeln( 'Installing plugin: ' + plugin.name );

      exec( 'wp plugin install ' + plugin.name + ' --version=' + plugin.version + ' --path=vendor/libraries/automattic/wordpress', function(error, stdout, stderr) {

        // console.log('stdout: ' + stdout);
        // console.log('stderr: ' + stderr);

        if (error !== null) {
          console.log('exec error: ' + error);
        }

      } ).on( 'close', function() {
        grunt.log.writeln( 'Installed plugin: ' + plugin.name );
        callback();
      })

    }, 10 );

    cargo.drain = done;

    for( var name in composer.extra[ 'active-plugins' ] ) {
      cargo.push({name: name, version: composer.extra[ 'active-plugins' ][ name ]});
    }

  });

  grunt.registerTask( 'install', 'Install application.', function() {
    var environment = grunt.option( 'environment' ) || 'production';
    var system = grunt.option( 'system' ) || 'linux';
    var copy_type = system == 'windows' ? 'copy' : 'symlink';

    // Run any task for the specific environment now
    // grunt.task.run( [ 'environment:' + environment, copy_type + ':' + environment ] );

  });

};