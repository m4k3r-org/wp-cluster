/**
 * Build Library.
 *
 * @author potanin@UD
 * @param grunt
 */
module.exports = function( grunt ) {

  grunt.initConfig({

    // LESS Compilation.
    pkg: grunt.file.readJSON( 'composer.json' ),

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

    // Monitor.
    watch: {
      options: {
        interval: 100,
        debounceDelay: 500
      },
      js: {
        files: [
          'scripts/src/*.js'
        ],
        tasks: [ 'uglify' ]
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

    // Documentation.
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
    }

  });

  // Load NPM Tasks.
  grunt.loadNpmTasks( 'grunt-markdown' );
  grunt.loadNpmTasks( 'grunt-mocha-cli' );
  grunt.loadNpmTasks( 'grunt-contrib-yuidoc' );
  grunt.loadNpmTasks( 'grunt-contrib-uglify' );
  grunt.loadNpmTasks( 'grunt-contrib-watch' );
  grunt.loadNpmTasks( 'grunt-contrib-concat' );
  grunt.loadNpmTasks( 'grunt-contrib-clean' );
  grunt.loadNpmTasks( 'grunt-shell' );

  // Build for Use.
  grunt.registerTask( 'default', [ 'markdown', 'uglify', 'yuidoc' ] );

  // Build for Distribution.
  grunt.registerTask( 'distribution', [ 'markdown', 'uglify:production', 'yuidoc' ] );

  // Update Environment.
  grunt.registerTask( 'update', [] );

};