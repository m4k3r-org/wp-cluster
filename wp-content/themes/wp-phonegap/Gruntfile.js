module.exports = function (grunt) {

    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),

        less: {
            skin: {
                options: {
                    paths: ['www/styles/src/skin'],
                    ieCompat: false
                },
                files: {
                    "styles/skin-festival.css": "styles/src/skin/skin-festival.less",
                    "styles/skin-worldwide.css": "styles/src/skin/skin-worldwide.less"
                }
            },

            structure: {
                options: {
                    paths: ['styles/src/structure'],
                    ieCompat: false
                },
                files: {
                    "styles/structure.css": "styles/src/structure/structure.less"
                }
            }

        },

        watch: {
            lessStructure: {
                files: ['styles/src/structure/**/*.less'],
                tasks: ['less:structure'],
                options: {
                    interrupt: true
                }
            },
            lessSkin: {
                files: ['styles/src/skin/**/*.less'],
                tasks: ['less:skin'],
                options: {
                    interrupt: true
                }
            }
        }
    });

    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-contrib-less');

    grunt.registerTask('install', ['less']);
    grunt.registerTask('build', ['less']);
    grunt.registerTask('release', ['less']);

};