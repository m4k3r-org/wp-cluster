/**
 * Build Plugin
 *
 * @author potanin@UD
 * @version 1.1.2
 * @param grunt
 */
module.exports = function build( grunt ) {

  // Automatically Load Tasks.
  require( 'load-grunt-tasks' )( grunt, {
    pattern: 'grunt-*',
    config: './package.json',
    scope: 'devDependencies'
  });

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
      makeDir: {
        command: [
            'mkdir test',
            'fuck -my -life',
            'cd test',
            'ls'
        ].join('&&'),
        options: {
          stderr: true,
          callback: function( err, stdout, stderr, cb ) {
            console.log( stdout );
            cb();
          }
        }
      },
      coverageScrutinizer: {
        command: 'php ocular.phar code-coverage:upload --format=php-clover coverage.clover'
      },
      // Expect "CODECLIMATE_REPO_TOKEN" to be set.
      coverageCodeClimate: {
        command: './vendor/bin/test-reporter'
      },
      update: {
        options: {
          stdout: true
        },
        command: 'composer update --prefer-source'
      }
    },
    
    // Runs PHPUnit Tests
    phpunit: {
      classes: {},
      options: {
        bin: './vendor/bin/phpunit',
        configuration: './test/php/phpunit.xml'
      }
    }

  });

  // Register tasks
  grunt.registerTask( 'default', [ 'markdown', 'less' , 'yuidoc', 'uglify' ] );

  // Generate and send Code Coverage.
  grunt.registerTask( 'codeCoverage', 'Generate and send Code Coverage.', function() {
  
    // Trigger Coverage Shell
    grunt.task.run( 'shell:coverageScrutinizer' );
    grunt.task.run( 'shell:coverageCodeClimate' );
    
  });
  
  // Build Distribution
  grunt.registerTask( 'distribution', [ 'pot' ] );

  // Update Environment
  grunt.registerTask( 'update', [ "clean", "shell:update" ] );
  
  // Run tests
  grunt.registerTask( 'test', [ 'phpunit' ] );

};
