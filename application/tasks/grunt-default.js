module.exports = function( grunt ) {

  grunt.registerTask( 'default', 'Default task to show help', function(){
    grunt.log.writeln( 'You can use this grunt file to do the following:' );
    grunt.log.writeln( ' * grunt build - builds production environment' );
    grunt.log.writeln( ' * Arguments:' );
    grunt.log.writeln( '    --environment={environment} - builds specific environment: (production**, development, staging, local)' );
    grunt.log.writeln( '    --system={system} - build for a specific system: (linux**, windows' );
  } );

};