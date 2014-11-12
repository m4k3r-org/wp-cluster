/**
 * Build WP-Site
 *
 * @author potanin@UD
 * @version 2.0.0
 * @param grunt
 */
module.exports = function( grunt ) {

  // Automatically Load Tasks
  require( 'load-grunt-tasks' )( grunt, {
    pattern: 'grunt-*',
    config: './package.json',
    scope: 'devDependencies'
  } );

  // Build Configuration.
  grunt.initConfig({

    // Get Project Package.
    composer: grunt.file.readJSON( 'composer.json' ),

    // Generate Markdown Documentation.
    markdown: {
      all: {
        files: [
          {
            expand: true,
            src: 'readme.md',
            dest: 'application/static',
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

    // Symbolic Links.
    symlink: {
      production: {
        files: {
          '.htaccess': 'wp-content/plugins/wp-veneer/lib/local/.htaccess',
          'wp-config.php': 'wp-content/plugins/wp-cluster/lib/class-config.php',
          'wp-content/db.php': 'wp-content/plugins/wp-cluster/lib/class-database.php',
          'wp-content/sunrise.php': 'wp-content/plugins/wp-cluster/lib/class-sunrise.php'
        }
      }
    }

  });

  grunt.registerTask( 'default', function() {
    console.log( 'Done.');
  })

};

/**
 * Match WordPress media naming convention.
 *
 */
function eliminateResizedImages(filepath) {
  return !filepath.match( /(.+?)-(\d*)x(\d*)\.[^\.]*/ );
}
