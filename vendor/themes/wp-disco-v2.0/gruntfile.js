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
            'css/src'
          ]
        },
        files: {
          'css/app.css' : [
            'css/src/app.less'
          ]
        }
      },
      development: {
        options: {
          yuicompress: false,
          relativeUrls: true,
          paths: [
            'css/src'
          ]
        },
        files: {
          'css/app.css' : [
            'css/src/app.less'
          ]
        }
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
    ]

  });

};