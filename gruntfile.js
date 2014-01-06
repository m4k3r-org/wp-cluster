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
          baseUrl: 'scripts/src',
          out: "scripts/app.js"
        }
      },
      build: {
        options: {
          name: 'app',
          baseUrl: 'scripts/src',
          out: "scripts/app.dev.js",
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
          yuicompress: true,
          relativeUrls: true
        },
        files: {
          'styles/app.css': [
            'styles/src/app.less'
          ]
        }
      },
      development: {
        options: {
          relativeUrls: true
        },
        files: {
          'styles/app.dev.css': [
            'styles/src/app.less'
          ]
        }
      },
      editor: {
        options: {
          relativeUrls: true
        },
        files: {
          'styles/editor-style.css': [
            'styles/src/editor-style.less'
          ]
        }
      }
    },
    
    uglify: {
      production: {
        files: {
          'scripts/app.js': [
            'scripts/src/app.js'
          ]
        }
      },
      development: {
        options: {
          mangle: false,
          beautify: true
        },
        files: {
          'scripts/app.dev.js': [
            'scripts/src/app.js'
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
          'styles/src/*.less'
        ],
        tasks: [ 'less' ]
      },
      js: {
        files: [
          'scripts/src/*.js'
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
    //'yuidoc',
    //'markdown',
    'less',
    'requirejs'
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