<?php

/**
 * This file contains the RWC\Features\OpenWeathermap\View class.
 *
 * @author Brian Reich <breich@reich-consulting.net>
 * @copyright Copyright (C) 2016 Reich Web Consulting
 * @version 1.0
 * @package RWC
 */
namespace RWC\Features\Fundraisers {

    /**
     * OpenWeathermap\View is responsible for rendering views and templates the
     * OpenWeathermap feature.
     *
     * @author Brian Reich <breich@reich-consulting.net>
     * @copyright Copyright (C) 2017 Reich Web Consulting
     * @version 1.0
     * @package RWC
     */
    class View extends \RWC\Object {

        /**
         * Renders a Fundraisers View.
         *
         * The template output will be filtered through the
         * "rwc_features_fundraisers_view" filter before being returned.
         *
         * @param array $options The array of view rendering options.
         *
         * @return string Returns the rendered view.
         */
        public function render( array $options = array() ) {

            $options = \RWC\Utility::get_options( $options, array(
                'view' => 'default',
                'data' => null
            ) );

            /*
             * In a single step, get the view contents for the real estate view specified
             * in the configuration, and apply the filter to it so the contents can be
             * modified as-needed.
             */
            return apply_filters(
                'rwc_features_fundraisers_view',
                \RWC\Utility::get_include_content(
                    sprintf("/features/fundraisers/%s.php",
                        $options[ 'view' ] ), $options ),
                $options );
        }
    }
}
