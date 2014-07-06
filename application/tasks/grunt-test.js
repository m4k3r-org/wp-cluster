module.exports = function( grunt ) {

  // Run All Tests
  grunt.registerTask( 'test', [
    'mochaTest',
    'notify:testSuccess'
  ] );

  // Run Visual Regression Tests
  grunt.registerTask( 'test:visual', [
    'phantomcss:desktop',
    'notify:testSuccess'
  ] );

};