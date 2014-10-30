/**
 * Build Theme
 *
 * @author Usability Dynamics
 * @version 1.0.0
 * @param grunt
 */
module.exports = function(grunt) {
  grunt.initConfig({
    less: {
      development: {
        options: {
          compress: true,
          yuicompress: true,
          optimization: 2
        },
        files: {
          'static/styles/app.css' : [
            'static/styles/src/app.less'
          ]
        }
      }
    },
	
    // Monitor.
    watch: {
      styles: {
        files: [ 'gruntfile.js', 'static/styles/src/*', 'static/styles/src/colors/*' ],
        tasks: [ 'less' ]
      }
    }
  });

  grunt.loadNpmTasks('grunt-contrib-less');
  grunt.loadNpmTasks('grunt-contrib-watch');

  grunt.registerTask('default', ['less']);
};