/**
 * Build WP-Site
 *
 * @author potanin@UD
 * @version 2.0.0
 * @param grunt
 */
module.exports = function( grunt ) {

  // Automatically Load Tasks.
  require( 'load-grunt-tasks' )( grunt, {
    pattern: 'grunt-*',
    config: './package.json',
    scope: 'devDependencies'
  });

  // Build Configuration.
  grunt.initConfig({

    // Runtime Meta.
    job: {
      build: process.env.CIRCLE_BUILD_NUM,
      artifacts: process.env.CIRCLE_ARTIFACTS,
      branch: process.env.CIRCLE_BRANCH
    },

    // Get Project Package.
    composer: grunt.file.readJSON( 'composer.json' ),

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

    // Generate YUIDoc documentation.
    yuidoc: {
      compile: {
        name: '<%= composer.name %>',
        description: '<%= composer.description %>',
        url: '<%= composer.homepage %>',
        version: '<%= composer.version %>',
        options: {
          paths: [ 'vendor/plugins', 'vendor/libraries', 'vendor/themes' ],
          outdir: 'application/static/codex/'
        }
      }
    },

    // Generate Markdown Documentation.
    markdown: {
      all: {
        files: [
          {
            expand: true,
            src: 'readme.md',
            dest: 'application/static/markdown',
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
      build: [
        'advanced-cache.php',
        'db.php',
        'object-cache.php',
        'sunrise.php',
        'vendor/libraries/automattic/wordpress/wp-config.php'
      ],
      files: [
        '.environment',
        '.htaccess',
        'advanced-cache.php',
        'db.php',
        'object-cache.php',
        'sunrise.php',
        'vendor/libraries/automattic/wordpress/wp-config.php'
      ],
      symlinks: [
        '.htaccess',
        'advanced-cache.php',
        'db.php',
        'object-cache.php',
        'sunrise.php',
        'vendor/libraries/automattic/wordpress/wp-config.php'
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
          ],
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
      build: {
        files: {
          'vendor/libraries/automattic/wordpress/wp-config.php': 'vendor/plugins/wp-veneer/lib/class-config.php'
        }
      },
      standalone: {
        files: {
          'vendor/libraries/automattic/wordpress/wp-config.php': 'vendor/plugins/wp-veneer/lib/class-config.php'
        }
      },
      network: {
        files: {
          'db.php': 'vendor/plugins/wp-cluster/lib/class-database.php',
          'sunrise.php': 'vendor/plugins/wp-cluster/lib/class-sunrise.php',
          'vendor/libraries/automattic/wordpress/wp-config.php': 'vendor/plugins/wp-veneer/lib/class-config.php'
        }
      },
      production: {
        files: {
          'advanced-cache.php': 'vendor/plugins/wp-veneer/lib/class-advanced-cache.php',
          'object-cache.php': 'vendor/plugins/wp-veneer/lib/class-object-cache.php'
        }
      },
      development: {
        files: {
          'wp-cli.yml': 'application/static/wp-cli.yml',
          'advanced-cache.php': 'vendor/plugins/wp-veneer/lib/class-advanced-cache.php',
          'object-cache.php': 'vendor/plugins/wp-veneer/lib/class-object-cache.php'
        }
      },
      staging: {},
      local: {}
    },

    // Copying files (for Windows)
    copy: {
      build: {
        files: {
          '.htaccess': 'vendor/plugins/wp-veneer/lib/local/.htaccess'
        }
      },
      standalone: {
        files: {
          '.htaccess': 'vendor/plugins/wp-veneer/lib/local/.htaccess',
          'vendor/libraries/automattic/wordpress/wp-config.php': 'vendor/plugins/wp-veneer/lib/class-config.php'
        }
      },
      network: {
        files: {
          '.htaccess': 'vendor/plugins/wp-veneer/lib/local/.htaccess',
          'db.php': 'vendor/plugins/wp-cluster/lib/class-database.php',
          'sunrise.php': 'vendor/plugins/wp-cluster/lib/class-sunrise.php',
          'vendor/libraries/automattic/wordpress/wp-config.php': 'vendor/plugins/wp-veneer/lib/class-config.php'
        }
      },
      production: {
        files: {
          'advanced-cache.php': 'vendor/plugins/wp-veneer/lib/class-advanced-cache.php',
          'object-cache.php': 'vendor/plugins/wp-veneer/lib/class-object-cache.php'
        }
      },
      development: {},
      staging: {},
      local: {}
    },

    // Shell commands
    shell: {
      optimize: {
        options: { stdout: true },
        command: 'composer update --optimize-autoloader  --no-interaction'
      },
      startProxy: {
        options: { stdout: true },
        command: 'haproxy -D -f '
      },

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
          require: [ 'should', 'request' ],
          reporter: 'list'
        },
        src: [ 'application/tests/api.js' ]
      },
      audit: {
        options: {
          ui: 'exports',
          timeout: 'exports',
          require: [ 'should', 'request' ],
          reporter: 'list'
        },
        src: [ 'application/tests/api.js' ]
      }
    },

    // Notification.
    notify: {
      options: {
        title: "UsabilityDynamics.com",
        enabled: true,
        max_jshint_notifications: 5,
        image: 'application/static/images/ud-icon.png'
      },
      pluginsInstalling: {
        options: {
          title: 'UsabilityDynamics.com',
          message: 'Starting to install plugins.'
        }
      },
      pluginsInstalled: {
        options: {
          title: 'UsabilityDynamics.com',
          message: 'All plugins have been installed.'
        }
      },
      watch: {
        options: {
          title: 'Task Complete',  // optional
          message: 'SASS and Uglify finished running'
        }
      },
      testSuccess: {
        options: {
          title: 'UsabilityDynamics.com - Tests',
          message: 'Tests completed, no issues.'
        }
      },
      audit: {
        options: {
          title: 'UsabilityDynamics.com - Audits',
          message: 'Audits completed, no issues.'
        }
      }
    }

  });

  grunt.task.loadTasks( 'application/tasks' );

  // Default Task.
  grunt.registerTask( 'default', [
    'mochaTest',
    'notify:testSuccess'
  ]);

  // Start Server.
  grunt.registerTask( 'start', [
    'shell:startProxy'
  ]);

  // Build Assets.
  grunt.registerTask( 'build', [
    'markdown',
    'clean:build',
    'copy:build',
    'symlink:build',
    'shell:optimize',
    'notify:pluginsInstalling',
    'installPlugins',
    'notify:pluginsInstalled'
  ]);

  // Run Tests.
  grunt.registerTask( 'test', [
    'mochaTest',
    'notify:testSuccess'
  ]);

  grunt.registerTask( 'test:visual', [
    'phantomcss:desktop',
    'notify:testSuccess'
  ]);
  
  grunt.registerTask( 'audit', [
    'mochaTest:audit',
    'notify:audit'
  ]);

  // Install Site.
  grunt.registerTask( 'install:standalone', [
    'clean:files',
    'clean:symlinks',
    'markdown',
    'yuidoc',
    'symlink:standalone'
  ]);

  grunt.registerTask( 'install:network', [
    'clean:files',
    'clean:symlinks',
    'markdown',
    'yuidoc',
    'symlink:network'
  ]);

  // Set Development or Production Environments.
  grunt.registerTask( 'environment:development', [
    'symlink:development'
  ]);

  grunt.registerTask( 'environment:production', [
    'symlink:production'
  ]);

};