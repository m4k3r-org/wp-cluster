/**
 * Build Plugin
 *
 * @author potanin@UD
 * @version 1.1.4
 * @param grunt
 */
module.exports = function build( grunt ) {

  grunt.initConfig( {

    // Read Composer File.
    package: grunt.file.readJSON( 'composer.json' ),

    // Generate Documentation.
    yuidoc: {
      compile: {
        name: '<%= package.name %>',
        description: '<%= package.description %>',
        version: '<%= package.version %>',
        url: '<%= package.homepage %>',
        options: {
          paths: 'lib',
          outdir: 'static/codex/'
        }
      }
    },

    // Compile LESS in app.css
    less: {
      production: {
        options: {
          yuicompress: true,
          relativeUrls: true
        },
        files: {
          'static/styles/wp-veneer.css': [ 'static/styles/src/wp-veneer.less' ],
          'static/styles/wp-login.css': [ 'static/styles/src/wp-login.less' ]
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

    // Clean for Development.
    clean: {
      all: [
        "vendor",
        "static/readme.md",
        "composer.lock",
        "static/styles/*.css",
        "static/scripts/*.js"
      ],
      vendor: [
        "composer.lock",
        "vendor/*"
      ]
    }

  });

  // Load tasks
  grunt.loadNpmTasks( 'grunt-spritefiles' );
  grunt.loadNpmTasks( 'grunt-markdown' );
  grunt.loadNpmTasks( 'grunt-requirejs' );
  grunt.loadNpmTasks( 'grunt-contrib-yuidoc' );
  grunt.loadNpmTasks( 'grunt-contrib-uglify' );
  grunt.loadNpmTasks( 'grunt-contrib-watch' );
  grunt.loadNpmTasks( 'grunt-contrib-less' );
  grunt.loadNpmTasks( 'grunt-contrib-concat' );
  grunt.loadNpmTasks( 'grunt-contrib-clean' );
  grunt.loadNpmTasks( 'grunt-shell' );

  // Register default task
  grunt.registerTask( 'default', [ 'markdown', 'less' , 'yuidoc', 'uglify' ] );

  grunt.registerTask( 'install', [ 'default' ] );
  grunt.registerTask( 'build', [ 'default' ] );

  // Build Distribution
  grunt.registerTask( 'distribution', [] );

};