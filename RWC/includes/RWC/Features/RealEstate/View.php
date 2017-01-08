<?php

/**
 * This file contains the RWC\Features\RealEstate\View class.
 *
 * @author Brian Reich <breich@reich-consulting.net>
 * @copyright Copyright (C) 2016 Reich Web Consulting
 * @version 1.0
 * @package RWC
 */
namespace RWC\Features\RealEstate {

    /**
     * RealEstate\View is responsible for rendering views and templates for the RealEstate
     * Feature.
     *
     * @author Brian Reich <breich@reich-consulting.net>
     * @copyright Copyright (C) 2016 Reich Web Consulting
     * @version 1.0
     * @package RWC
     */
    class View extends \RWC\Object {

        /**
         * Renders a RealEstate View.
         *
         * The render() method is responsible for returning views for Real Estate content.
         * The view can be detailed display for a single property or a list of properties
         * for an archive or search results page. The view itself specifies the format of
         * the rendered content by executing the WordPress loop and applying markup.
         *
         * The render() method expects a "view" option to be specified, but the "zillow"
         * view will be used by default, which renders the view that mimics Zillow search
         * results. The template that will be used by render() is determined first by
         * looking for "<theme>/partials/rwc/features/relestate/property-list-<view>.php"
         * where <theme> is the path to the current theme and <view> is the value passed in
         * the "view" option. If this custom template file does not exist, the default
         * plugin implementation will be used.
         *
         * The template output will be filtered through the "rwc_features_realestate_view"
         * filter before being returned.
         *
         * @param array $options The array of view rendering options.
         *
         * @return string Returns the rendered view.
         */
        public function render( array $options = array() ) {

            $options = \RWC\Utility::get_options( $options, array(
                'view' => 'zillow',
                'query' => null
            ) );

            /*
             * In a single step, get the view contents for the real estate view specified
             * in the configuration, and apply the filter to it so the contents can be
             * modified as-needed.
             */
            return apply_filters(
                'rwc_features_realestate_view',
                \RWC\Utility::get_include_content(
                    sprintf("/features/realestate/property-list-%s.php",
                        $options[ 'view' ] ), $options ),
                $options );
        }
    }
}
