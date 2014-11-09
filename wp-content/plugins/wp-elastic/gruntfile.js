/**
 * Build Plugin
 *
 * @author potanin@UD
 * @version 1.1.2
 * @param grunt
 */
module.exports = function build( grunt ) {

  grunt.initConfig( {

    pkg: grunt.file.readJSON( 'composer.json' ),

    yuidoc: {
      compile: {
        name: '<%= pkg.name %>',
        description: '<%= pkg.description %>',
        version: '<%= pkg.version %>',
        url: '<%= pkg.homepage %>',
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
        files: [
          {
            expand: true,
            cwd: 'static/styles/src',
            src: '*.less',
            dest: 'static/styles',
            rename: function(dest, src, options) {
              return dest + '/' + src.replace( '.less', '.dev.css' );
            }
          }
        ]
      },
      development: {
        options: {
          relativeUrls: true
        },
        files: [
          {
            expand: true,
            cwd: 'static/styles/src',
            src: '*.less',
            dest: 'static/styles',
            rename: function(dest, src, options) {
              return dest + '/' + src.replace( '.less', '.css' );
            }
          }
        ]
      }
    },

    watch: {
      options: {
        interval: 100,
        debounceDelay: 500
      },
      less: {
        files: [
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

    uglify: {
      minified: {
        options: {
          preserveComments: false,
          mangle: { except: [ 'jQuery' ] }
        },
        files: [
          {
            expand: true,
            cwd: 'static/scripts/src',
            src: '*.js',
            dest: 'static/scripts'
          }
        ]
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

    clean: {
      all: [
        "composer.lock",
        "vendor/*",
        "build/*"
      ],
      "update": [
        "composer.lock",
        "vendor/*"
      ]
    },

    shell: {
      build: {
        options: {
          stdout: true
        },
        command: 'echo "Building..."'
      },
      update: {
        options: {
          stdout: true
        },
        command: 'composer update --prefer-source'
      }
    }

  });

  // Load tasks
  //grunt.loadNpmTasks( 'grunt-spritefiles' );
  grunt.loadNpmTasks( 'grunt-markdown' );
  grunt.loadNpmTasks( 'grunt-contrib-symlink' );
  grunt.loadNpmTasks( 'grunt-contrib-yuidoc' );
  grunt.loadNpmTasks( 'grunt-contrib-uglify' );
  grunt.loadNpmTasks( 'grunt-contrib-watch' );
  grunt.loadNpmTasks( 'grunt-contrib-less' );
  grunt.loadNpmTasks( 'grunt-contrib-concat' );
  grunt.loadNpmTasks( 'grunt-contrib-clean' );
  grunt.loadNpmTasks( 'grunt-shell' );

  // Default Build Task.
  grunt.registerTask( 'default', [ 'markdown', 'uglify', 'less' ] );

  // Install for development.
  grunt.registerTask( 'install', [ 'shell:build' ] );

  // Build Distribution.
  grunt.registerTask( 'build', [ 'shell:build' ] );

  grunt.registerTask( 'dev', [ 'watch' ] );

};