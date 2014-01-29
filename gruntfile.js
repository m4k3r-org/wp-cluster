/**
 * Build Theme
 *
 * @author Usability Dynamics
 * @version 0.1.0
 * @param grunt
 */
module.exports = function( grunt ) {

  grunt.initConfig({

    // LESS Compilation.
    pkg: grunt.file.readJSON( 'composer.json' ),

    // LESS Compilation.
    less: {
      'app.css': {
        options: {
          yuicompress: true,
          relativeUrls: true
        },
        files: {
          'styles/app.css': [ 'styles/src/app.less' ]
        }
      },
      'app.dev.css': {
        options: {
          relativeUrls: true
        },
        files: {
          'styles/app.dev.css': [ 'styles/src/app.less' ]
        }
      },
      'editor-style.dev.css': {
        options: {
          yuicompress: false,
          relativeUrls: true
        },
        files: {
          'styles/editor-style.dev.css': [ 'styles/src/editor-style.less' ]
        }
      },
      'editor-style.css': {
        options: {
          yuicompress: true,
          relativeUrls: true
        },
        files: {
          'styles/editor-style.css': [ 'styles/src/editor-style.less' ]
        }
      },

      // Color Schemas
      'color-default': {
        options: {
          yuicompress: true,
          relativeUrls: true
        },
        files: {
          'styles/default.css': [ 'styles/src/colors/default.less' ]
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
      all: [ 'test/*.js' ]
    },

    // Minify all JS Files.
    uglify: {
      production: {
        options: {
          preserveComments: false,
          wrap: false
        },
        files: [
          {
            expand: true,
            cwd: 'scripts/src',
            src: [ '*.js' ],
            dest: 'scripts'
          }
        ]
      }
    },

    // Require JS Tasks.
    requirejs: {
      fooxbox: {
        options: {
          name: 'foobox',
          paths: {
            "foobox": "scripts/src/foobox.dev"
          },
          include: [ 'foobox' ],
          out: 'scripts/utility/foobox.js',
          skipModuleInsertion: true,
          wrap: {
            start: "define( function(require, exports, module) {",
            end: "});"
          }
        }
      }
    },

    // Monitor.
    watch: {
      options: {
        interval: 1000,
        debounceDelay: 500
      },
      styles: {
        files: [
          'gruntfile.js', 'styles/src/*.*'
        ],
        tasks: [ 'less' ]
      },
      scripts: {
        files: [
          'gruntfile.js', 'scripts/src/*.js'
        ],
        tasks: [ 'uglify' ]
      },
      docs: {
        files: [
          'styles/app.*.css', 'composer.json', 'readme.md'
        ],
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
    }

  });

  // Load tasks
  grunt.loadNpmTasks( 'grunt-component' );
  grunt.loadNpmTasks( 'grunt-component-build' );
  grunt.loadNpmTasks( 'grunt-requirejs' );
  grunt.loadNpmTasks( 'grunt-markdown' );
  grunt.loadNpmTasks( 'grunt-mocha-cli' );
  grunt.loadNpmTasks( 'grunt-spritefiles' );
  grunt.loadNpmTasks( 'grunt-contrib-symlink' );
  grunt.loadNpmTasks( 'grunt-contrib-yuidoc' );
  grunt.loadNpmTasks( 'grunt-contrib-uglify' );
  grunt.loadNpmTasks( 'grunt-contrib-watch' );
  grunt.loadNpmTasks( 'grunt-contrib-less' );
  grunt.loadNpmTasks( 'grunt-contrib-concat' );
  grunt.loadNpmTasks( 'grunt-contrib-clean' );
  grunt.loadNpmTasks( 'grunt-shell' );

  // Build Assets
  grunt.registerTask( 'default', [ 'yuidoc', 'uglify', 'markdown', 'less' ] );

  // Install environment
  grunt.registerTask( 'install', [ 'yuidoc', 'uglify', 'markdown', 'less' ] );

  // Update Environment
  grunt.registerTask( 'update', [ 'yuidoc', 'uglify', 'markdown', 'less' ] );

  // Prepare distribution
  grunt.registerTask( 'dist', [ 'yuidoc', 'uglify', 'markdown', 'less' ] );

  // Update Documentation
  grunt.registerTask( 'doc', [ 'yuidoc', 'markdown' ] );

};