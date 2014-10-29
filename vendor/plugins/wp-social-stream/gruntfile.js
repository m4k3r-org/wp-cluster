/**
 * Build Theme
 *
 * @author Usability Dynamics
 * @version 1.0.0
 * @param grunt
 */
module.exports = function( grunt ) {

  // Automatically Load Tasks
  require( 'load-grunt-tasks' )( grunt, {
    pattern: 'grunt-*',
    config: './package.json',
    scope: 'devDependencies'
  } );

  // Build Configuration.
  grunt.initConfig({

    // Get Project Package.
    // composer: grunt.file.readJSON( 'composer.json' ),

    // LESS Compilation.
    less: {
      production: {
        options: {
          yuicompress: true,
          relativeUrls: true,
          paths: [
            'static/styles/src'
          ]
        },
        files: {
          'static/styles/wp-social-stream.css' : [
            'static/styles/src/wp-social-stream.less'
          ]
        }
      },
      development: {
        options: {
          yuicompress: false,
          relativeUrls: true,
          paths: [
            'static/styles/src'
          ]
        },
        files: {
          'static/styles/wp-social-stream.css' : [
            'static/styles/src/wp-social-stream.less'
          ]
        }
      }
    },

    // Run Mocha Tests.
    mochacli: {
      options: {
        require: [ 'should' ],
        reporter: 'list',
        ui: 'exports'
      },
      all: [
        'test/*.js'
      ]
    },

    // Require JS Tasks.
    requirejs: {
      production: {
        // options: require( './composer' ).config.component
      },
      development: {
        // options: require( './composer' ).config.component
      }
    },

    // Monitor.
    watch: {
      options: {
        interval: 1000,
        debounceDelay: 500
      },
      styles: {
        files: [ 'static/styles/src/*.less' ],
        tasks: [ 'less:production' ]
      },
      scripts: {
        files: [
          'static/scripts/src/*.js'
        ],
        tasks: [ 'requirejs' ]
      }
    }

  } );

  // Load tasks
  grunt.loadNpmTasks( 'grunt-contrib-requirejs');
  grunt.loadNpmTasks( 'grunt-contrib-uglify');
  grunt.loadNpmTasks( 'grunt-contrib-less' );
  grunt.loadNpmTasks( 'grunt-contrib-watch' );

  // Build Assets
  grunt.registerTask( 'default', [ 'compile' ] );

  // Install environment
  // grunt.registerTask( 'compile', [ 'requirejs',  'less:production' ] );
  grunt.registerTask( 'compile', [ 'less:production' ] );

};