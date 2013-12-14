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
        files: {
          'styles/app.css': [ 'styles/src/app.less' ],
          'styles/login.css': [ 'styles/src/login.less' ]
        }
      },
      development: {
        options: {
          relativeUrls: true
        },
        files: {
          'styles/login.dev.css': [ 'styles/src/login.less' ],
          'styles/app.dev.css': [ 'styles/src/app.less' ]
        }
      }
    },

    requirejs: {
      /*
       dev: {
       options: {
       name: 'app.dev',
       baseUrl: 'scripts',
       out: "scripts/app.js",
       paths: {
       "knockout": 'http://ajax.aspnetcdn.com/ajax/knockout/knockout-2.2.1.js',
       lodash: 'http://cdnjs.cloudflare.com/ajax/libs/lodash.js/2.2.1/lodash.min.js',
       async: 'http://cdnjs.cloudflare.com/ajax/libs/async/0.2.7/async.min.js',
       'lib/engine': "vendor/usabilitydynamics/lib-layout-engine/scripts/layout-engine",
       'lib/utility': "vendor/usabilitydynamics/lib-utility/scripts/utility",
       'lib/settings': "vendor/usabilitydynamics/lib-settings/scripts/settings"
       }
       }
       }
       */
    },

    watch: {
      options: {
        interval: 100,
        debounceDelay: 500
      },
      less: {
        files: [
          'styles/src/*.*'
        ],
        tasks: [ 'less' ]
      },
      js: {
        files: [
          'scripts/src/*.*'
        ],
        tasks: [ 'uglify' ]
      }
    },

    uglify: {
      minified: {
        options: {
          preserveComments: false,
          wrap: true,
          mangle: {
            except: [ 'jQuery', 'Bootstrap' ]
          }
        },
        files: {
          'scripts/veneer.js': [
            'scripts/src/veneer.js'
          ]
        }
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
        "scripts/app.js",
        "scripts/contact-form-7.js",
        "scripts/foobox.js",
        "scripts/require.js",
        "scripts/utility.js",
        "components/*",
        "vendor/*",
        "styles/*.css",
        "scripts/emitter",
        "scripts/event",
        "scripts/indexof",
        "scripts/ui",
        "scripts/utility"
      ],
      "update": [
        "composer.lock",
        "vendor/*"
      ]
    },

    symlink: {

      explicit: {
        dest: 'vendor/usabilitydynamics',
        src: '/vendor/usabilitydynamics'
      }

    },

    shell: {
      update: {
        options: {
          stdout: true
        },
        command: 'composer update --prefer-source'
      }
    }

  });

  // Load tasks
  grunt.loadNpmTasks( 'grunt-spritefiles' );
  grunt.loadNpmTasks( 'grunt-markdown' );
  grunt.loadNpmTasks( 'grunt-contrib-symlink' );
  grunt.loadNpmTasks( 'grunt-requirejs' );
  grunt.loadNpmTasks( 'grunt-contrib-yuidoc' );
  grunt.loadNpmTasks( 'grunt-contrib-uglify' );
  grunt.loadNpmTasks( 'grunt-contrib-watch' );
  grunt.loadNpmTasks( 'grunt-contrib-less' );
  grunt.loadNpmTasks( 'grunt-contrib-concat' );
  grunt.loadNpmTasks( 'grunt-contrib-clean' );
  grunt.loadNpmTasks( 'grunt-shell' );

  // Register tasks
  grunt.registerTask( 'default', [ 'markdown', 'less' , 'yuidoc', 'uglify' ] );

  // Build Distribution
  grunt.registerTask( 'distribution', [] );

  // Update Environment
  // grunt.registerTask( 'update', [ "clean:update", "shell:update" ] );

  // Clean, preparing for update
  //grunt.registerTask( 'clean', [  ] );

  grunt.registerTask( 'dev', [ 'symlink', 'watch' ] );

};