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

    // Generate POT file.
    makepot: {
      target: {
        options: {
          type: 'wp-theme',
          domainPath: 'static/locale',                   // Where to save the POT file.
          exclude: [ "static/**", "tasks/**", "vendor/**", "test" ],
          mainFile: 'style.css',
          potFilename: 'wp-festival-v2.0.pot',
          potHeaders: {
            poedit: true,
            language: 'en',
            'x-poedit-country': 'United States',
            'x-poedit-sourcecharset': 'UTF-8',
            'x-textdomain-support': 'yes',
            'x-poedit-keywordslist': true
          },
          updateTimestamp: true
        }
      }
    }

  });

  // Build Assets
  grunt.registerTask( 'default', [ 'compile', 'makepot' ] );

  // Install environment
  grunt.registerTask( 'compile', [ 'requirejs',  'less:production' ] );

  // Install Theme
  grunt.registerTask( 'install', [ 'compile' ], function() {

  });

  // Build Theme
  grunt.registerTask( 'build', [ 'compile', 'makepot', 'markdown' ], function() {

  });

};