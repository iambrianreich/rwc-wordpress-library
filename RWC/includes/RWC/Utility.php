<?php

namespace RWC {

    class Utility {

        /**
         * Renders a template file.
         *
         * The get_include_content() method will search for a template file on a list of
         * paths and return it's output. By default the paths searched will be the
         * theme folder, the parent theme folder, and the Reich Web Consulting library
         * partials folder, in that order. The list of paths can be overridden by specifying
         * the "paths" option in the options array passed to the method.
         *
         * The paths will be searched in the order specified for the specified file. When
         * it is found, it will be rendered via PHP's include() method and the content
         * returned. If the template file cannot be found on any of the configured paths,
         * false will be returned to denote that the file could not be found.
         *
         * Note: get_include_content exports the contents of the $options array to the
         * current scope so they are available to the executed template.
         *
         * @param string $file    The relative path to the template file.
         * @param array  $options An array of configuration options.
         *
         * @return string|bool Returns the template content, or false if template not found.
         */
        public static function get_include_content( $file, $options = array() ) {

            $options = self::get_options( $options, array(
                'paths' => self::get_default_partials_path_list()
            ) );

            // Put options in local scope.
            extract( $options );

            ob_start();

            // Track whether or not rendering was successful.
            $rendered = false;

            // Iterate through configured paths.
            foreach( $options[ 'paths' ] as $path ) {

                // If the template file exists at the path, attempt to render it.
                if( file_exists( $path . $file ) ) {

                    // include() will return false if rendering failed
                    $rendered = include( $path . $file );

                    // Only render once.
                    if( $rendered !== false ) {
                        break;
                    }
                }
            }

            // Get HTML content and return it.
            $html= ob_get_contents();
            ob_end_clean();

            return ( $rendered ? $html : false );
        }

        /**
         * Returns the ordered list of paths to search for partial rendering
         * templates. This method will return a list with the theme directory,
         * the parent theme directory, and the path to the Reich Web Consulting
         * library's partial templates.
         *
         * @return array Returns an ordered list of paths to search for partials.
         */
        public static function get_default_partials_path_list() {

            return array(
                get_stylesheet_directory(),
                get_template_directory(),
                RWC_PATH . '/partials/rwc',
            );
        }

        /**
         * Returns an options array by merging options with defaults.
         *
         * Returns th options array by merging options with defaults. The values specified
         * in $options will override values specified in $defaults. The values in $defaults
         * will be present in the returned array when overrides are not specified in the
         * $options array.
         *
         * @return array Returns an array of merged options.
         */
        public static function get_options( array $options = array(),
          array $defaults = array() ) {

            // Return an array with defaults overridden with options from the options array.
            return array_merge( $defaults, $options );
        }
    }
}
