/**
 * Library Build.
 *
 * @author peshkov@UD
 * @version 1.1.2
 * @param grunt
 */
module.exports = function build( grunt ) {

  // Require Utility Modules.
  var joinPath      = require( 'path' ).join;
  var resolvePath   = require( 'path' ).resolve;
  var findup        = require( 'findup-sync' );

  // Automatically Load Tasks.
  require( 'load-grunt-tasks' )( grunt, {
    pattern: 'grunt-*',
    config: './package.json',
    scope: 'devDependencies'
  });
  
  // Determine Paths.
  var _paths = {
    composer: findup( 'composer.json' ),
    package: findup( 'package.json' ),
    vendor: findup( 'vendor' ),
    languages: findup( 'static/languages' ),
    styles: findup( 'static/styles' ),
    scripts: findup( 'static/scripts' ),
    phpTests: findup( 'test/php' )
  };

  grunt.initConfig({
    
    // Compile LESS.
    less: {
      production: {
        options: {
          yuicompress: true,
          relativeUrls: true
        },
        files: [
          {
            expand: true,
            cwd: joinPath( resolvePath( _paths.styles ), 'src' ),
            src: [ 'admin.global.less' ],
            dest: _paths.styles,
            rename: function renameLess( dest, src ) {
              return joinPath( dest, src.replace( '.less', '.css' ) );
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
    
    // Minify Javascript
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
      }
    },
    
    clean: {
      all: [
        "composer.lock"
      ]
    },
    
    // CLI Commands.
    shell: {
      install: {
        options: { stdout: true },
        command: 'composer install --prefer-dist --dev --no-interaction'
      },
      update: {
        options: { stdout: true },
        command: 'composer update --prefer-source --no-interaction'
      }
    }

  });
  
  // Install Environment
  grunt.registerTask( 'install', [ "clean", "shell:install" ] );
  
  // Update Environment
  grunt.registerTask( 'update', [ "clean", "shell:update" ] );

};