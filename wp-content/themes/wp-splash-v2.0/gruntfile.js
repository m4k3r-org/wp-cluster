/**
 * Build Theme
 *
 *
 *
 * @author potanin@UD
 * @version 1.1.2
 * @param grunt
 */
module.exports = function( grunt ) {

  // Automatically Load Tasks
  require( 'load-grunt-tasks' )( grunt, {
    pattern: 'grunt-*',
    config: './package.json',
    scope: 'devDependencies'
  } );

  grunt.initConfig( {

    // Get Project Package.
    composer: grunt.file.readJSON( 'composer.json' ),

    requirejs: {
      dev: {
        options: {
          name: 'app',
          baseUrl: 'static/scripts/src',
          out: "static/scripts/app.js"
        }
      },
      build: {
        options: {
          name: 'app',
          baseUrl: 'static/scripts/src',
          out: "static/scripts/app.js",
          uglify: {
            beautify: true,
            max_line_length: 1000,
            no_mangle: true
          }
        }
      }
    },
    
    less: {
      production: {
        options: {
          compress: true,
          yuicompress: true,
          relativeUrls: true,
          modifyVars: {}
        },
        files: {
          'static/styles/app.css': [
            'static/styles/src/app.less'
          ]
        }
      },
      editor: {
        options: {
          relativeUrls: true
        },
        files: {
          'static/styles/editor-style.css': [
            'static/styles/src/editor-style.less'
          ]
        }
      }
    },
    
    uglify: {
      production: {
        files: {
          'static/scripts/app.js': [
            'static/scripts/src/app.js'
          ]
        }
      }
    },
    
    watch: {
      options: {
        interval: 1000,
        debounceDelay: 500
      },
      less: {
        files: [
          'style.css',
          'static/styles/src/*.less'
        ],
        tasks: [ 'less' ]
      },
      js: {
        files: [
          'static/scripts/src/*.js'
        ],
        tasks: [ 'uglify' ]
      }
    },
    
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

    // Generate POT file.
    makepot: {
      target: {
        options: {
          type: 'wp-theme',
          domainPath: 'static/locale',                   // Where to save the POT file.
          exclude: [ "static/**", "tasks/**", "vendor/**", "test" ],
          mainFile: 'style.css',
          potFilename: 'wp-splash.pot',
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
  grunt.registerTask( 'default', [
    'less',
    'uglify',
    'makepot'
  ]);

  grunt.registerTask( 'distribution', [
    'less',
    'requirejs'
  ]);

  // Run Tests
  grunt.registerTask( 'test', [] );

  // Update Documentation
  grunt.registerTask( 'document', [
    'markdown'
  ]);

  // Update Environment
  grunt.registerTask( 'update', [] );

  // Automatically Rebuild
  grunt.registerTask( 'dev', [
    'watch'
  ]);

};