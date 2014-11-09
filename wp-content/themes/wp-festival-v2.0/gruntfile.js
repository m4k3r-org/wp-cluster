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
    composer: grunt.file.readJSON( 'composer.json' ),

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
          'static/styles/app.css' : [
            'static/styles/src/app.less'
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
          'static/styles/app.css' : [
            'static/styles/src/app.less'
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
      compile: {
        options: require( './composer' ).config.component
      }
    },

    // Monitor.
    watch: {
      options: {
        interval: 1000,
        debounceDelay: 500
      },
      styles: {
        files: [ 'gruntfile.js', 'static/styles/src/*', 'static/styles/src/colors/*' ],
        tasks: [ 'less' ]
      },
      scripts: {
        files: [
          'gruntfile.js',
          'composer.json',
          'static/scripts/src/*.js',
          'static/scripts/src/lib/*.js',
          'static/scripts/src/components/*.js'
        ],
        tasks: [ 'requirejs' ]
      },
      docs: {
        files: [ 'styles/app.*.css', 'composer.json', 'readme.md' ],
        tasks: [ 'markdown' ]
      }
    },

    // Markdown Generation.
    markdown: {
      all: {
        files: [
          {
            expand: true,
            src: 'readme.md',
            dest: 'static/',
            ext: '.html'
          }
        ],
        options: {
          markdownOptions: {
            gfm: true,
            codeLines: {
              before: '<span>',
              after: '</span>'
            }
          }
        }
      }
    },

    // Remove Things.
    clean: [
      "vendor"
    ],

    // Documentation
    yuidoc: {
      compile: {
        name: '<%= composer.name %>',
        description: '<%= composer.description %>',
        version: '<%= composer.version %>',
        url: '<%= composer.author.url %>',
        logo: 'http://media.usabilitydynamics.com/logo.png',
        options: {
          paths: './',
          outdir: 'static/codex'
        }
      }
    }

  });

  // Load tasks
  grunt.loadNpmTasks( 'grunt-contrib-requirejs');
  grunt.loadNpmTasks( 'grunt-contrib-uglify');
  grunt.loadNpmTasks( 'grunt-contrib-less' );
  grunt.loadNpmTasks( 'grunt-contrib-watch' );
  grunt.loadNpmTasks( 'grunt-contrib-yuidoc' );

  // Build Assets
  grunt.registerTask( 'default', [ 'compile' ] );

  // Install environment
  grunt.registerTask( 'compile', [ 'requirejs',  'less:production' ] );

  // Install Theme
  grunt.registerTask( 'install', [ 'compile' ], function() {

  });

  // Build Theme
  grunt.registerTask( 'build', [ 'compile' ], function() {

  });

  // Update Documentation
  grunt.registerTask( 'doc', [ 'yuidoc', 'markdown' ] );

};