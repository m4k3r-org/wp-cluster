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

  grunt.initConfig( {
    
    pkg: grunt.file.readJSON( 'package.json' ),
    
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
    
    yuidoc: {
      compile: {
        name: '<%= pkg.name %>',
        description: '<%= pkg.description %>',
        version: '<%= pkg.version %>',
        url: '<%= pkg.homepage %>',
        logo: 'http://media.usabilitydynamics.com/logo.png',
        options: {
          paths: './',
          outdir: 'static/codex'
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
    }
    
  });

  // Load tasks
  grunt.loadNpmTasks( 'grunt-markdown' );
  grunt.loadNpmTasks( 'grunt-requirejs' );
  grunt.loadNpmTasks( 'grunt-spritefiles' );
  grunt.loadNpmTasks( 'grunt-contrib-symlink' );
  grunt.loadNpmTasks( 'grunt-contrib-yuidoc' );
  grunt.loadNpmTasks( 'grunt-contrib-uglify' );
  grunt.loadNpmTasks( 'grunt-contrib-watch' );
  grunt.loadNpmTasks( 'grunt-contrib-less' );

  // Build Assets
  grunt.registerTask( 'default', [
    'less',
    'uglify'
  ]);

  grunt.registerTask( 'distribution', [
    'less',
    'requirejs'
  ]);

  // Run Tests
  grunt.registerTask( 'test', [] );

  // Update Documentation
  grunt.registerTask( 'document', [
    'yuidoc',
    'markdown'
  ]);

  // Update Environment
  grunt.registerTask( 'update', [] );

  // Automatically Rebuild
  grunt.registerTask( 'dev', [
    'watch'
  ]);

};