<?php

/**
 * Contains the RWC\Features\RealEstate\Shortcodes\PropertyList class.
 *
 * @author Brian Reich <breich@reich-consulting.net>
 * @copyright Copyright (C) 2016 Reich Consulting
 * @package RWC\Features\RealEstate\Shortcodes
 */

namespace RWC\Features\RealEstate\Shortcodes {

    /**
     * Renders the PropertyList shortcode.
     *
     * @author Brian Reich <breich@reich-consulting.net>
     * @copyright Copyright (C) 2016 Reich Consulting
     * @package RWC\Features\RealEstate\Shortcodes
     */
    class PropertyList {

        /**
         * Initializes the shortcode.
         *
         * @constructor
         */
        public function __construct() {

            add_shortcode( 'rwc_realestate_property_list',
                array( $this, 'do_shortcode' ) );
        }

        /**
         * Renders and returns the property list shortcode.
         *
         * The property list shortcode will be rendered by creating and
         * rendering a RealEstate\View by passing the shortcode options directly
         * to the View::render() method. For more information in supported
         * options, see RWC\Features\RealEstate\View::render().
         *
         * @param array $atts The attributes passed to the shortcode.
         *
         * @return string Returns the generated shortcode markup.
         * @see RWC\Features\RealEstate\View::render()
         */
        public function do_shortcode( $atts ) {

            // Create the view.
            $view = new \RWC\Features\RealEstate\View();

            // Render and return.
            $query = new \WP_Query( array(
                'post_type' => 'rwc_real_estate'
            ) );

            $options = shortcode_atts(
                array( 'view' ),
                $atts,
                'rwc_realestate_property_list' );

            // Pass in the query.
            $options[ 'query' ] = $query;
            
            $html = $view->render( $options );

            wp_reset_postdata();

            return $html;
        }
    }
}
