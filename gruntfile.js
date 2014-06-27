/**
 * Build Plugin
 *
 * @author potanin@UD
 * @version 1.1.2
 * @param grunt
 */
module.exports = function build( grunt ) {

  grunt.initConfig( {

    package: grunt.file.readJSON( 'composer.json' ),

    // Locale.
    pot: {
      options:{
        package_name: 'wp-amd',
        package_version: '<%= package.version %>',
        text_domain: 'wp-amd',
        dest: 'static/languages/',
        keywords: [ 'gettext', 'ngettext:1,2' ]
      },
      files:{
        src:  [ '**/*.php', 'lib/*.php' ],
        expand: true
      }
    },
    
    // Documentation.
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
          'static/styles/wp-amd.css': [ 'static/styles/src/wp-amd.less' ],
          'static/styles/wp.amd.editor.style.css': [ 'static/styles/src/wp.amd.editor.style.less' ]
        }
      },
      development: {
        options: {
          relativeUrls: true
        },
        files: {
          'static/styles/wp-amd.dev.css': [ 'static/styles/src/wp-amd.less' ]
        }
      }
    },

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

    uglify: {
      production: {
        options: {
          mangle: false,
          beautify: false
        },
        files: [
          {
            expand: true,
            cwd: 'static/scripts/src',
            src: [ '*.js' ],
            dest: 'static/scripts'
          }
        ]
      },
      staging: {
        options: {
          mangle: false,
          beautify: true
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
        "composer.lock"
      ]
    },

    shell: {
      coverageScrutinizer: {
        command: 'php ocular.phar code-coverage:upload --access-token="'+ process.env.SCRUTINIZER_ACCESS_TOKEN + '" --format=php-clover coverage.clover'
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
  grunt.loadNpmTasks( 'grunt-markdown' );
  grunt.loadNpmTasks( 'grunt-contrib-yuidoc' );
  grunt.loadNpmTasks( 'grunt-contrib-uglify' );
  grunt.loadNpmTasks( 'grunt-contrib-watch' );
  grunt.loadNpmTasks( 'grunt-contrib-less' );
  grunt.loadNpmTasks( 'grunt-contrib-concat' );
  grunt.loadNpmTasks( 'grunt-contrib-clean' );
  grunt.loadNpmTasks( 'grunt-pot' );

  // Register tasks
  grunt.registerTask( 'default', [ 'markdown', 'less' , 'yuidoc', 'uglify' ] );

  // Build Distribution
  grunt.registerTask( 'distribution', [ 'pot' ] );

  // Update Environment
  grunt.registerTask( 'update', [ "clean", "shell:update" ] );

  // Clean, preparing for update
  grunt.registerTask( 'clean', [  ] );

};
