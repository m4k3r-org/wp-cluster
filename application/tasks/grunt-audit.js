module.exports = function( grunt ) {

  // Run our audit
  grunt.registerTask( 'audit', [
    'mochaTest:audit',
    'notify:audit'
  ] );

};