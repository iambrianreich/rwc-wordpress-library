<?php

namespace RWC\Features\Fundraisers {

    class Shortcodes {

        public function __construct() {

            add_shortcode( 'rwc_fundraiser_list', array( $this, 'render_fundraiser_list' ) );
        }

        private function normalize_fundraiser_list_options( $options ) {
            if( ! is_array( $options ) ) {
                $options = array( $options );
            }

            $options = \RWC\Utility::get_options( $options, array(
                'view' => 'unordered-list',
                'current' => true
            ) );

            // Make the "current" flag a boolean.
            $options[ 'current' ] = filter_var($options[ 'current' ],
                FILTER_VALIDATE_BOOLEAN);

            return $options;
        }

        /**
         * Renders the Fundraisers list.
         *
         * The following shortcode options are available to customize the list
         * output. The "view" option specifies the output template. By default
         * the "unordered-list" template is used to render the list. The
         * "current" option specifies whether or not to limit the list to
         * fundraisers that are currently running. The default value is "true".
         *
         * @param array $options An array of shortcode options.
         *
         * @return string Returns the shortcode output.
         */
        public function render_fundraiser_list( $options = array() )
        {
            // Normalize options. Drops-in defaults, etc.
            $options = $this->normalize_fundraiser_list_options( $options );

            // Setup base query options.
            $queryOptions = [
                'post_type' => \RWC\Features\Fundraisers::POST_TYPE,
                'posts_per_page' => -1
            ];

            // If the "current" option is true, add meta query to limit
            // returns fundraisers to those that are current.
            if( $options[ 'current' ] )
            {

                $queryOptions[ 'meta_query'] = array(
                    'relation' => 'AND',
                    'start_date' => array(
                        'key' => 'rwc-fundraiser-metabox_start-date',
                        'value' => date( 'Y-m-d' ),
                        'compare' => '<=',
                        'type' => 'DATE'
                    ),
                    'end_date' => array(
                        'key' => 'rwc-fundraiser-metabox_end-date',
                        'value' => date( 'Y-m-d' ),
                        'compare' => '>=',
                        'type' => 'DATE'
                    )
                );
            }

            // Execute the query.
            $query = new \WP_Query( $queryOptions );

            // Generate the HTML.
            $html = \RWC\Utility::get_include_content( sprintf(
                '/features/fundraisers/fundraiser-list-%s.php',
                    $options[ 'view' ] ), array( 'query' => $query ) );

            // Reset WP_Query.
            wp_reset_postdata();

            return $html;
        }
    }
}
