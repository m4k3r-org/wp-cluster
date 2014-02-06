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

    // Compress PNG Files.
    tinypng: {
      options: {
        apiKey: "D3_kNVgKtPXTkfpx6X9SDZ5XTGch9vu_",
        showProgress: true,
        stopOnImageError: true
      },
      production: {
        expand: true,
        cwd: 'images/src',
        src: [ '*.png' ],
        dest: 'images',
        ext: '.png'
      }
    },

    // Generate Sprite.
    sprite:{

      all: {
        src: 'images/src/*.png',
        destImg: 'images/sprite.png',
        destCSS: 'styles/src/sprites.less',
        engine: 'canvas',
        cssFormat: 'less'
      }
    },

    // LESS Compilation.
    less: {
      'production': {
        options: {
          yuicompress: true,
          relativeUrls: true
        },
        files: {
          'styles/app.main.css': [ 'styles/src/app.main.less' ],
          'styles/app.bootstrap.css': [ 'styles/src/app.bootstrap.less' ],
          'styles/default.css': [ 'styles/src/colors/default.less' ],
          'styles/editor-style.css': [ 'styles/src/editor-style.less' ]
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
      main: {
        options: {
          baseUrl: "scripts/src",
          skipModuleInsertion: true, // important to avoid "app.main" being created
          locale: "en-us",
          optimize: 'none', // uglify|none
          uglify: {
            toplevel: true,
            ascii_only: true,
            beautify: true,
            max_line_length: 1000,
            defines: {
              DEBUG: ['name', 'false']
            },
            no_mangle: true
          },
          include: [
            'modules/html.picture',
            'modules/html.video',
            'app.bootstrap'
          ],
          out: "scripts/app.bootstrap.js"
        }
      },
      foobox: {
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
          'gruntfile.js', 'styles/src/*', 'styles/src/colors/*'
        ],
        tasks: [ 'less' ]
      },
      scripts: {
        files: [
          'gruntfile.js',
          'scripts/src/*.js',
          'scripts/src/modules/*.js'
        ],
        tasks: [ 'uglify', 'requirejs:main' ]
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
  grunt.loadNpmTasks( 'grunt-tinypng' );
  grunt.loadNpmTasks( 'grunt-spritesmith' );
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
  grunt.registerTask( 'default', [ 'yuidoc', 'uglify', 'requirejs:main', 'markdown', 'less' ] );

  // Install environment
  grunt.registerTask( 'install', [ 'yuidoc', 'uglify', 'requirejs:main', 'markdown', 'less' ] );

  // Update Environment
  grunt.registerTask( 'update', [ 'yuidoc', 'uglify', 'requirejs:main', 'markdown', 'less' ] );

  // Prepare distribution
  grunt.registerTask( 'dist', [ 'yuidoc', 'uglify', 'requirejs:main', 'markdown', 'less' ] );

  // Update Documentation
  grunt.registerTask( 'doc', [ 'yuidoc', 'markdown' ] );

};