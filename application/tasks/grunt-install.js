module.exports = function( grunt ) {

  /**
   * Installs plugins for WordPress, uses the 'wp' command line tool
   */
  grunt.registerTask( 'installPlugins', 'Install all plugins declared in composer.extra.', function( task ) {
    grunt.log.writeln( 'installPlugins is depreciated, use http://wpackagist.org/ and composer instead' );
    return;
    var done = this.async();
    var async = require( 'async' );
    var composer = grunt.file.readJSON( 'composer.json' );
    var exec = require( 'child_process' ).exec;
    var plugins = [];

    /** Make sure we have a valid object */
    if( typeof composer.extra[ 'plugins' ] != 'object' ){
      return;
    }

    /** Create an array of the plugins */
    for( var x in composer.extra[ 'plugins' ] ){
      plugins.push( {
        'name': x,
        'version': composer.extra[ 'plugins' ][ x ]
      } );
    }

    /** Ok, loop through the plugins installing them one at a time */
    async.eachSeries( plugins, function( plugin, callback ){
      grunt.log.writeln( 'Installing plugin: ' + plugin.name );
      var cmd = 'wp plugin install ' + plugin.name + ' --version=' + plugin.version + ' --path=vendor/libraries/automattic/wordpress';
      grunt.log.writeln( 'Running command: ' + cmd );
      exec( cmd, function(error, stdout, stderr) {

        //console.log('stdout: ' + stdout);
        //console.log('stderr: ' + stderr);

        if (error !== null) {
          console.log('exec error: ' + error);
        }

      } ).on( 'close', function() {
        grunt.log.writeln( 'Installed plugin: ' + plugin.name );
        callback();
      } )
    }, function( final ){
      grunt.log.writeln( 'Finished installing plugins.' );
    } );

  } );

  /**
   * Install procedure, basically compiles/builds all assets, and sets up the environment
   */
  grunt.registerTask( 'install', 'Install application and environment.', function() {
    var environment = grunt.option( 'environment' ) || 'production';
    var system = grunt.option( 'system' ) || 'linux';
    var copy_type = system == 'windows' ? 'copy' : 'symlink';
    var site_type = grunt.option( 'type' ) || 'standalone';

    // Run our clean routines
    grunt.task.run( [
      'clean:files',
      'clean:symlinks',
    ] );

    // Ok, we have a good environment, lets go
    grunt.log.writeln( 'Building environment for : ' + environment + ' on ' + system );

    // Run any task for the specific environment now
    grunt.task.run( [ copy_type + ':' + environment ] );

    // Now run the shell script to configure .htaccess for the specific environment
    grunt.task.run( [ 'shell:configure:' + environment ] );

    // Run the task for the site type
    grunt.task.run( [ copy_type + ':' + site_type ] );

    // Build all of our docs/assets
    grunt.task.run( [
      'markdown',
      'yuidoc'
    ] );

    // Compile our assets
    grunt.task.run( [
      'less:' + ( environment == 'production' ? 'production' : 'development' ),
      'requirejs:production'
    ] );

    // Run our plugin install stuff
    grunt.task.run( [
      'notify:pluginsInstalling',
      'installPlugins',
      'notify:pluginsInstalled'
    ] );

  } );

};