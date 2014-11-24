/**
 * Build script for UsabilityDynamics SCMF
 */
module.exports = function(grunt) {

	grunt.initConfig({

		pkg: grunt.file.readJSON( 'package.json' ),

		less: {
			development: {
				options: {
					paths: ['src/assets/build'],
					relativeUrls: true
				},
				files: {
					'src/assets/build/app.css' : [
						'src/assets/core/less/app.less'
					]
				}
			},
			production : {
				options : {
					compress: true,
					yuicompress: true,
					relativeUrls: true,
					paths: ['src/assets/build']
				},
				files: {
					'application/static/styles/app.css' : [
						'application/static/styles/src/app.less'
					]
				}
			}
		},

		requirejs: {

			production: {
				options: {
					"name": "app",
					"out": "application/static/scripts/app.js",
					"baseUrl": "components",
					"mainConfigFile": "vendor/components/require.config.js",
					"baseUrl": "application/static/scripts/src",
					"paths": {
						"components": "../../../../vendor/components"
					},
					"map": {
						"*": { "jquery": "lib/jquery-private" }
					},
					uglify : {
						max_line_length: 1000,
						no_mangle: true
					}
				}
			}

		},

    yuidoc: {

      compile: {
        name: "<%= pkg.name %>",
        description: "<%= pkg.description %>",
        version: "<%= pkg.version %>",
        url: "<%= pkg.homepage %>",
        logo: 'http://media.usabilitydynamics.com/logo.png',
        options: {
          paths: "./",
          outdir: "application/static/codex"
        }
      }

    },

		watch: {

			less: {
				files: [
					'application/static/styles/src/*.less'
				],
				tasks: [ 'less' ]
			}
		}

	}); // grunt.initConfig


	// Load tasks
	grunt.loadNpmTasks( 'grunt-contrib-requirejs');
	grunt.loadNpmTasks( 'grunt-contrib-uglify');
	grunt.loadNpmTasks( 'grunt-contrib-less' );
	grunt.loadNpmTasks( 'grunt-contrib-watch' );
  grunt.loadNpmTasks( 'grunt-contrib-yuidoc' );


	// Default behaviour
	grunt.registerTask( 'default', [
		'less:development'
	]);

	grunt.registerTask( 'deploy', [
		'less:production',
		'requirejs:production'
	]);

  grunt.registerTask( 'docs', [
    'yuidoc'
  ]);

};
