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

    // Clean Directories.
    clean: {
      files: [
        '.environment',
        '.htaccess',
        'advanced-cache.php',
        'db.php',
        'object-cache.php',
        'sunrise.php',
        'w3tc-config',
        'vendor/libraries/automattic/wp-config.php',
        'wp-cli.yml'
      ],
      symlinks: [
        '.htaccess',
        'advanced-cache.php',
        'db.php',
        'object-cache.php',
        'sunrise.php',
        'w3tc-config',
        'vendor/libraries/automattic/wp-config.php',
        'wp-cli.yml'
      ],
      junk: [
        'cgi-bin',
        'uploads'
      ],
      test: []
    },

    // Build Our Less Assets
    less: {
      development: {
        options: {
          paths: [
            'application/static/styles/src'
          ],
          relativeUrls: true
        },
        files: {
          'application/static/styles/app.css' : [
            'application/static/styles/src/app.less'
          ]
        }
      },
      production : {
        options : {
          compress: true,
          yuicompress: true,
          relativeUrls: true,
          paths: [
            'application/static/styles/src'
          ]
        },
        files: {
          'application/static/styles/app.css' : [
            'application/static/styles/src/app.less'
          ]
        }
      }
    },

    // Build our JavaScript Assets
    requirejs: {
      production: {
        options: {
          "name": "app",
          "out": "application/static/scripts/app.js",
          "baseUrl": "application/static/scripts/src",
          "paths": {
          },
          "map": {
          },
          uglify : {
            max_line_length: 1000,
            no_mangle: true
          }
        }
      }
    },

    // Symbolic Links.
    symlink: {
      essential: {
        files: {
          '.htaccess': 'vendor/plugins/wp-veneer/lib/local/.htaccess',
          'vendor/libraries/automattic/wp-config.php': 'vendor/plugins/wp-veneer/lib/class-config.php'
        }
      },
      vendor: {
        files: {
          'vendor/libraries/automattic/wp-config.php': 'vendor/plugins/wp-veneer/lib/class-config.php'
        }
      },
      standalone: {
        files: {
          '.htaccess': 'vendor/plugins/wp-veneer/lib/local/.htaccess',
          'vendor/libraries/automattic/wp-config.php': 'vendor/plugins/wp-veneer/lib/class-config.php'
        }
      },
      network: {
        files: {
          '.htaccess': 'vendor/plugins/wp-veneer/lib/local/.htaccess',
          'vendor/libraries/automattic/wp-config.php': 'vendor/plugins/wp-veneer/lib/class-config.php',
          'sunrise.php': 'vendor/plugins/wp-cluster/lib/class-sunrise.php'
        }
      },
      cluster: {
        files: {
          '.htaccess': 'vendor/plugins/wp-veneer/lib/local/.htaccess',
          'vendor/libraries/automattic/wp-config.php': 'vendor/plugins/wp-veneer/lib/class-config.php',
          'db.php': 'vendor/plugins/wp-cluster/lib/class-database.php',
          'sunrise.php': 'vendor/plugins/wp-cluster/lib/class-sunrise.php'
        }
      },
      production: {
        files: {
          'wp-cli.yml': 'application/static/etc/wp-cli.yml',
          'object-cache.php': 'vendor/plugins/w3-total-cache/wp-content/object-cache.php',
          'w3tc-config': 'application/static/etc/w3tc-config'
        }
      },
      development: {
        files: {
          'wp-cli.yml': 'application/static/etc/wp-cli.yml'
        }
      }
    }

  });

};

/**
 * Match WordPress media naming convention.
 *
 */
function eliminateResizedImages(filepath) {
  return !filepath.match( /(.+?)-(\d*)x(\d*)\.[^\.]*/ );
}
