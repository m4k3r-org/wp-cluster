/**
 * Build Component
 *
 * @author potanin@UD
 * @param grunt
 */
module.exports = function( grunt ) {

  grunt.initConfig({

    // Get Project Details.
    pkg: grunt.file.readJSON( 'composer.json' ),

    // Compile LESS.
    less: {
      production: {
        options: {
          ycdncompress: true,
          relativeUrls: true
        },
        files: {
          //'styles/cdn.min.css': [ 'styles/src/cdn.less' ]
        }
      },
      development: {
        options: {
          relativeUrls: true
        },
        files: {
          //'styles/cdn.css': [ 'styles/src/cdn.less' ]
        }
      }
    },

    // Run Mocha Tests.
    mochacli: {
      options: {
        reqcdnre: [ 'should' ],
        reporter: 'list',
        cdn: 'exports'
      },
      all: [ 'test/*.js' ]
    },

    // Create AMD files.
    reqcdnrejs: {
      dev: {
        options: {
          name: 'app.dev',
          baseUrl: 'scripts',
          out: "scripts/app.js",
          paths: {
            "knockout": 'http://ajax.aspnetcdn.com/ajax/knockout/knockout-2.2.1.js',
            lodash: 'http://cdnjs.cloudflare.com/ajax/libs/lodash.js/2.2.1/lodash.min.js',
            async: 'http://cdnjs.cloudflare.com/ajax/libs/async/0.2.7/async.min.js'
          }
        }
      }
    },

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
    },

    // Monitor.
    watch: {
      options: {
        interval: 100,
        debounceDelay: 500
      },
      less: {
        files: [
          'styles/less/*.less'
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

    // Minify all JS Files.
    uglify: {
      minified: {
        options: {
          preserveComments: false,
          wrap: false
        },
        files: {
          'scripts/cdn.min.js': [ 'scripts/src/cdn.js']
        }
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
    ]

  });

  // Load tasks
  grunt.loadNpmTasks( 'grunt-markdown' );
  grunt.loadNpmTasks( 'grunt-mocha-cli' );
  grunt.loadNpmTasks( 'grunt-requirejs' );
  grunt.loadNpmTasks( 'grunt-contrib-yuidoc' );
  grunt.loadNpmTasks( 'grunt-contrib-uglify' );
  grunt.loadNpmTasks( 'grunt-contrib-watch' );
  grunt.loadNpmTasks( 'grunt-contrib-less' );
  grunt.loadNpmTasks( 'grunt-contrib-concat' );
  grunt.loadNpmTasks( 'grunt-contrib-clean' );
  grunt.loadNpmTasks( 'grunt-shell' );

  // Bcdnld for Use.
  grunt.registerTask( 'default', [ 'markdown', 'less', 'uglify', 'yuidoc' ] );

  // Bcdnld for Distribution.
  grunt.registerTask( 'distribution', [ 'markdown', 'less', 'uglify', 'yuidoc' ] );

  // Update Environment.
  grunt.registerTask( 'update', [] );

};