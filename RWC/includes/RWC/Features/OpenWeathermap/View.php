<?php

/**
 * This file contains the RWC\Features\OpenWeathermap\View class.
 *
 * @author Brian Reich <breich@reich-consulting.net>
 * @copyright Copyright (C) 2016 Reich Web Consulting
 * @version 1.0
 * @package RWC
 */
namespace RWC\Features\OpenWeathermap {

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
         * Renders a RealEstate View.
         *
         * The render() method is responsible for returning views for
         * OpenWeathermap data. The view itself specifies the format of the
         * rendered content.
         *
         * The render() method expects the following options to be specified.
         * The "view" option specifies the template to render, with the
         * "default" template being used by default. The "data" option specifies
         * The OpenWeathermap\CurrentWeatherResult to be rendered by the
         * template. The template used by render() is determined first by
         * looking for "<theme>/partials/rwc/features/openweathermap/<view>.php"
         * where <theme> is the path to the current theme and <view> is the
         * value passed in the "view" option. If this custom template file does
         * not exist, the default plugin implementation will be used.
         *
         * The template output will be filtered through the
         * "rwc_features_openweathermap_view" filter before being returned.
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
                'rwc_features_openweathermap_view',
                \RWC\Utility::get_include_content(
                    sprintf("/features/openweathermap/%s.php",
                        $options[ 'view' ] ), $options ),
                $options );
        }
    }
}
