module.exports = function( grunt ) {

  grunt.registerTask( 'build', 'Build application.', function() {
    var environment = grunt.option( 'environment' ) || 'production';
    var system = grunt.option( 'system' ) || 'linux';
    var copy_type = system == 'windows' ? 'copy' : 'symlink';

    // Ok, we have a good environment, lets go
    grunt.log.writeln( 'Building environment for : ' + environment + ' on ' + system );

    // First, run the common tasks
    grunt.task.run( [ 'environment:common', copy_type + ':common' ] );

    // Run any task for the specific environment now
    grunt.task.run( [ 'environment:' + environment, copy_type + ':' + environment ] );

    // Now run the shell script to configure .htaccess for the specific environment
    grunt.task.run( [ 'shell:configure:' + environment ] );

  });

};