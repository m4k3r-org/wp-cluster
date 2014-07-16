/**
 * Library Build.
 *
 * @author peshkov@UD
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

  grunt.initConfig({
    
    // Compile LESS
    less: {
      options: {
        yuicompress: true,
        relativeUrls: true
      },
      files: {
        'static/styles/admin.organizers.css': [ 'static/styles/src/admin.organizers.less' ]
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