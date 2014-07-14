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
    
    // CLI Commands.
    shell: {
      install: {
        options: { stdout: true },
        command: 'composer install --prefer-dist --dev --no-interaction --quiet'
      },
      update: {
        options: { stdout: true },
        command: 'composer update --prefer-source --no-interaction --quiet'
      }
    }

  });

};