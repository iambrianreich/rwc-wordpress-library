module.exports = function(grunt) {

    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        phplint: {
            good: ["RWC/includes/RWC/**/*.php"],
            options: {
                phpArgs: {
                    "-l": null,
                    "-d": null,
                    "-f": null,
                    "-d": ["display_errors", "display_startup_errors"]
                }
            },
        },
        phpcs : {
            application: {
                src: ['RWC/includes/**/*.php']
            },
        }

    });

    // Load the plugin that provides the "uglify" task.
    grunt.loadNpmTasks("grunt-phplint");
    grunt.loadNpmTasks('grunt-phpcs');

    // Default task(s).
    grunt.registerTask('default', [ 'phplint:good', /*'phpcs:application'*/ ]);
};
