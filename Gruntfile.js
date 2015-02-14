/**
 * Build Plugin
 *
 * @author potanin@UD
 * @version 1.1.4
 * @param grunt
 */
module.exports = function build( grunt ) {

  // Automatically Load Tasks
  require( 'load-grunt-tasks' )( grunt, {
    pattern: 'grunt-*',
    config: './package.json',
    scope: 'devDependencies'
  } );

  grunt.initConfig( {

    // Read Composer File.
    package: grunt.file.readJSON( 'composer.json' ),

    // Compile LESS in app.css
    less: {
      production: {
        options: {
          yuicompress: true,
          relativeUrls: true
        },
        files: {
          'static/styles/wp-cluster.css': [ 'static/styles/src/wp-cluster.less' ],
          'static/styles/wp-cluster-login.css': [ 'static/styles/src/wp-cluster-login.less' ]
        }
      },
      development: {
        options: {
          yuicompress: false,
          relativeUrls: true
        },
        files: {
        }
      }
    },

    // Development Watch.
    watch: {
      options: {
        interval: 100,
        debounceDelay: 500
      },
      less: {
        files: [
          'static/styles/src/*.*'
        ],
        tasks: [ 'less' ]
      },
      js: {
        files: [
          'static/scripts/src/*.*'
        ],
        tasks: [ 'uglify' ]
      }
    },

    // Uglify Scripts.
    uglify: {
      production: {
        options: {
          preserveComments: false,
          wrap: false,
          mangle: {
            except: [ 'jQuery', 'Bootstrap' ]
          }
        },
        files: [
          {
            expand: true,
            cwd: 'static/scripts/src',
            src: [ '*.js' ],
            dest: 'static/scripts'
          }
        ]
      }
    },

    // Generate Markdown.
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
          type: 'wp-plugin',
          domainPath: 'static/locale',                   // Where to save the POT file.
          exclude: [ "node_modules/**", "vendor/**", "static/**" ],
          mainFile: 'wp-cluster.php',
          potFilename: 'wp-cluster.pot',
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

  // Register default task
  grunt.registerTask( 'default', [ 'markdown', 'less', 'uglify', 'makepot' ] );

  grunt.registerTask( 'install', [ 'default' ] );
  grunt.registerTask( 'build', [ 'default' ] );

  // Build Distribution
  grunt.registerTask( 'release', [ 'default' ] );

};