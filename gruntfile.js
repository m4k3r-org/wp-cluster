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

    // Sync storage with S3
    aws_s3: {
      options: {
        accessKeyId: process.env.AWS_ACCESS_KEY_ID || 'AKIAJCDAT2T7FESLH3IQ',
        secretAccessKey: process.env.AWS_SECRET_ACCESS_KEY || '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',
        bucket: process.env.AWS_STORAGE_BUCKET || 'storage.discodonniepresents.com',
        region: 'us-east-1',
        uploadConcurrency: 20,
        downloadConcurrency: 20,
        differential: true
      },
      static: {
        files: [
          {
            expand: true,
            cwd: 'storage/public',
            src: [ '**' ],
            dest: 'public/'
          }
        ]
      },
      media: {
        options: {
          bucket: process.env.AWS_STORAGE_BUCKET || 'storage.discodonniepresents.com',
          differential: true
        },
        params: {
          ContentEncoding: 'gzip'
        },
        files: [
          {
            expand: true,
            cwd: 'storage/public',
            src: [ '**' ],
            dest: 'public/',
            filter: eliminateResizedImages
          }
        ]
      },
      assets: {
        options: {
          bucket: process.env.AWS_STORAGE_BUCKET || 'storage.discodonniepresents.com',
          differential: true
        },
        files: [
          {
            expand: true,
            cwd: 'storage/public',
            src: [ '**' ],
            dest: 'public/'
          }
        ]
      }
    },

    // Visual Regression.
    phantomcss: {
      options: {
        logLevel: 'warning'
      },
      desktop: {
        options: {
          screenshots: 'application/tests/visual/original/desktop',
          results: 'application/static/screenshots/desktop',
          viewportSize: [1024, 768]
        },
        src: [
          'application/tests/visual/*.js'
        ]
      },
      mobile: {
        options: {
          screenshots: 'application/tests/visual/original/mobile',
          results: 'application/static/screenshots/mobile',
          viewportSize: [450, 600]
        },
        src: [
          'application/tests/visual/*.js'
        ]
      }
    },

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
          'advanced-cache.php': 'vendor/plugins/w3-total-cache/wp-content/advanced-cache.php',
          'object-cache.php': 'vendor/plugins/w3-total-cache/wp-content/object-cache.php',
          'w3tc-config': 'application/static/etc/w3tc-config'
        }
      },
      development: {
        files: {
          'wp-cli.yml': 'application/static/etc/wp-cli.yml'
        }
      },
      staging: {},
      local: {}
    },

    // Copying files (for Windows)
    copy: {
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
          'sunrise.php': 'vendor/plugins/wp-cluster/lib/class-sunrise.php',
        }
      },
      production: {
        files: {
          'wp-cli.yml': 'application/static/etc/wp-cli.yml',
          'advanced-cache.php': 'vendor/plugins/w3-total-cache/wp-content/advanced-cache.php',
          'object-cache.php': 'vendor/plugins/w3-total-cache/wp-content/object-cache.php',
          'w3tc-config': 'application/static/etc/w3tc-config'
        }
      },
      development: {
        files: {
          'wp-cli.yml': 'application/static/etc/wp-cli.yml'
        }
      },
      staging: {},
      local: {}
    },

    // Shell commands
    shell: {
      // This just configures the environment file
      configure: {
        options: {
          stdout: true
        },
        command: function( environment ){
          var cmd = 'echo ' + environment + ' > ./.environment';
          grunt.log.writeln( 'Running command: ' + cmd );
          return cmd;
        }
      }
    },

    // Server Mocha Tests.
    mochaTest: {
      test: {
        options: {
          ui: 'exports',
          timeout: 'exports',
          require: [
            'should',
            'request'
          ],
          reporter: 'list'
        },
        src: [
        ]
      },
      audit: {
        options: {
          ui: 'exports',
          timeout: 'exports',
          require: [
            'should',
            'request'
          ],
          reporter: 'list'
        },
        src: [
        ]
      }
    },

    // Notifications
    notify: {
      options: {
        title: "WP-Site Notifications",
        enabled: true,
        max_jshint_notifications: 5
      },
      pluginsInstalling: {
        options: {
          title: 'WP-Site',
          message: 'Starting to install plugins.'
        }
      },
      pluginsInstalled: {
        options: {
          title: 'WP-Site',
          message: 'All plugins have been installed.'
        }
      },
      watch: {
        options: {
          title: 'WP-Site: Task Complete',
          message: 'LESS and Uglify finished running'
        }
      },
      testSuccess: {
        options: {
          title: 'WP-Site: Tests',
          message: 'Tests completed, no issues.'
        }
      },
      audit: {
        options: {
          title: 'WP-Site: Audits',
          message: 'Audits completed, no issues.'
        }
      }
    }

  } );



  /**
   * Installs plugins for WordPress, uses the 'wp' command line tool
   */
  grunt.registerTask( 'installPlugins', 'Install all plugins declared in composer.extra.', function( task ) {
    grunt.log.writeln( 'installPlugins is depreciated, use http://wpackagist.org/ and composer instead' );
    return;
    var done = this.async();
    var async = require( 'async' );
    var composer = grunt.file.readJSON( 'composer.json' );
    var exec = require( 'child_process' ).exec;
    var plugins = [];

    /** Make sure we have a valid object */
    if( typeof composer.extra[ 'plugins' ] != 'object' ){
      return;
    }

    /** Create an array of the plugins */
    for( var x in composer.extra[ 'plugins' ] ){
      plugins.push( {
        'name': x,
        'version': composer.extra[ 'plugins' ][ x ]
      } );
    }

    /** Ok, loop through the plugins installing them one at a time */
    async.eachSeries( plugins, function( plugin, callback ){
      grunt.log.writeln( 'Installing plugin: ' + plugin.name );
      var cmd = 'wp plugin install ' + plugin.name + ' --version=' + plugin.version + ' --path=vendor/libraries/automattic/wordpress';
      grunt.log.writeln( 'Running command: ' + cmd );
      exec( cmd, function(error, stdout, stderr) {

        //console.log('stdout: ' + stdout);
        //console.log('stderr: ' + stderr);

        if (error !== null) {
          console.log('exec error: ' + error);
        }

      } ).on( 'close', function() {
        grunt.log.writeln( 'Installed plugin: ' + plugin.name );
        callback();
      } )
    }, function( final ){
      grunt.log.writeln( 'Finished installing plugins.' );
    } );

  } );

  /**
   * Install procedure, basically compiles/builds all assets, and sets up the environment
   */
  grunt.registerTask( 'install', 'Install application and environment.', function() {
    var environment = grunt.option( 'environment' ) || 'production';
    var system = grunt.option( 'system' ) || 'linux';
    var copy_type = system == 'windows' ? 'copy' : 'symlink';
    var site_type = grunt.option( 'type' ) || 'standalone';

    // Run our clean routines
    grunt.task.run( [
      'clean:files',
      'clean:symlinks',
    ] );

    // Ok, we have a good environment, lets go
    grunt.log.writeln( 'Building environment for : ' + environment + ' on ' + system );

    // Run any task for the specific environment now
    grunt.task.run( [ copy_type + ':' + environment ] );

    // Now run the shell script to configure .htaccess for the specific environment
    grunt.task.run( [ 'shell:configure:' + environment ] );

    // Run the task for the site type
    grunt.task.run( [ copy_type + ':' + site_type ] );

    // Build all of our docs/assets
    grunt.task.run( [
      'markdown',
      'yuidoc'
    ] );

    // Compile our assets
    grunt.task.run( [
      'less:' + ( environment == 'production' ? 'production' : 'development' ),
      'requirejs:production'
    ] );

    // Run our plugin install stuff
    grunt.task.run( [
      'notify:pluginsInstalling',
      'installPlugins',
      'notify:pluginsInstalled'
    ] );

  } );


  // Run All Tests
  grunt.registerTask( 'test', [
    'mochaTest',
    'notify:testSuccess'
  ] );

  // Run Visual Regression Tests
  grunt.registerTask( 'test:visual', [
    'phantomcss:desktop',
    'notify:testSuccess'
  ] );

};

/**
 * Match WordPress media naming convention.
 *
 */
function eliminateResizedImages(filepath) {
  return !filepath.match( /(.+?)-(\d*)x(\d*)\.[^\.]*/ );
}
