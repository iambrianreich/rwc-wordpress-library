<?php

/**
 * This file contains the RWC\Feature\OpenWeathermap class.
 *
 * @author Brian Reich <breich@reich-consulting.net>
 * @copyright Copyright (C) 2016 Reich Web Consulting
 * @version 1.0
 * @package RWC\Features
 */

namespace RWC\Features {

    /**
     * This feature provides access to the OpenWeathermap API which provides
     * free weather data.
     *
     * For more information see https://openweathermap.org/
     *
     * @author Brian Reich <breich@reich-consulting.net>
     * @copyright Copyright (C) 2016 Reich Web Consulting
     * @version 1.0
     * @package RWC\Features
     */
    class OpenWeathermap extends \RWC\Feature {

        /**
         * The default number of seconds to keep OpenWeathermap data cached in
         * a transient.
         *
         * @var int
         */
        const DEFAULT_CACHE_LIFETIME = 600; // 10 Minutes

        /**
         * The default unit measurement (imperial for English).
         *
         * @var string
         */
        const DEFAULT_UNITS = 'imperial';

        /**
         * The Service instance used to communicate with the Open WeatherMap
         * API.
         *
         * @var    Service
         * @access private
         */
        private $service = null;

        /**
         * Custom feature initialization.
         *
         * @constructor
         */
        public function __construct( $options = array(), \RWC\Library $library ) {

            parent::__construct( \RWC\Utility::get_options( $options, array(
                'apiKey' => null,
                'cacheLifetime' => self::DEFAULT_CACHE_LIFETIME,
                'enableCache' => true,
                'units' => self::DEFAULT_UNITS
            ) ), $library );

            // Load functions
            require_once('OpenWeathermap/functions.php' );
        }

        /**
         * Initializes the OpenWeathermap feature.
         *
         * @return void
         */
        public function initialize() {

            add_shortcode( 'openweathermap_for_zip', array( $this,
                'render_openweathermap_for_zip' ) );

            add_shortcode( 'openweathermap_for_city', array( $this,
                'render_openweathermap_for_city' ) );

            wp_enqueue_style( 'openweathermap_icons',
                $this->get_library()->get_uri() .
                '/css/rwc/features/openweathermap/css/weather-icons.min.css' );
        }

        /**
         * A shortcode renderer that renders a current weather result for a
         * zipcode location.
         *
         * @param array $options An array of shortcode options.
         *
         * @return string Returns the HTML for the shortcode.
         */
        public function render_openweathermap_for_zip( $options ) {

            // Mixin defaults.
            $options = shortcode_atts(  array(
                'zipcode' => null,
                'country' => 'us',
                'view' => 'default'
            ), $options, 'openweathermap_for_zip' );

            // Look for transient first
            $cacheId = 'render_openweathermap_for_zip_' .
                http_build_query( $options, '', '_' );
            $cache = $this->getCache( $cacheId );

            if( $cache != false ) return $cache;

            // Get the service instance.
            $service = $this->get_service();

            // This could throw an exception. Let's let WordPress handle it.
            $options[ 'data' ] = $service->getCurrentWeatherByZipcode(
                $options[ 'zipcode' ],
                $options[ 'country' ]
            );

            $result = (new OpenWeathermap\View())->render( $options );

            $this->setCache( $cacheId, $result );

            return $result;
        }

        /**
         * A shortcode renderer that renders a current weather result for a
         * city.
         *
         * @param array $options An array of shortcode options.
         *
         * @return string Returns the HTML for the shortcode.
         */
        public function render_openweathermap_for_city( $options ) {

            // Mixin defaults.
            $options = shortcode_atts(  array(
                'city' => null,
                'country' => 'us',
                'view' => 'default'
            ), $options, 'openweathermap_for_city' );


            // Look for transient first
            $cacheId = 'render_openweathermap_for_city_' .
                http_build_query( $options, '', '_' );
            $cache = $this->getCache( $cacheId );

            if( $cache !== false ) return $cache;

            // Get the service instance.
            $service = $this->get_service();

            // This could throw an exception. Let's let WordPress handle it.
            $options[ 'data' ] = $service->getCurrentWeatherByCity(
                $options[ 'city' ],
                $options[ 'us' ]
            );

            $result = (new OpenWeathermap\View())->render( $options );

            $this->setCache( $cacheId, $result );

            return $result;
        }

        /**
         * Returns the Service instance used to communicate with the Open
         * Weathermap API.
         *
         * @return Service Returns the Service instance.
         */
        private function get_service() {

            // Lazy-load
            if( $this->service == null ) {

                // Initialize Service with API key.
                $this->service = new OpenWeathermap\Service(
                    $this->get_option( 'apiKey' ),
                    $this->get_option( 'cacheLifetime' ),
                    $this->get_option( 'units' )
                );
            }

            return $this->service;
        }

        /**
         * Returns the specified cache value if caching is enabled and it is
         * not expired. Otherwise, returns false.
         *
         * @param string $name The name of the cached value.
         *
         * @return mixed Returns the cached value or false.
         */
        private function getCache( $name )
        {
            return  ($this->isCacheEnabled() ?
                get_transient( $name ) :
                false );
        }

        /**
         * Adds or updates the specified cache value, if caching is enabled.
         *
         * @param string $name  The name of the cached value.
         * @param mixed  $value The value to cache.
         */
        private function setCache( $name, $value )
        {
            if( $this->isCacheEnabled() )
            {
                set_transient( $name, $value, $this->getCacheLifetime() );
            }
        }

        /**
         * Returns the lifetime for transient objects cached by the
         * OpenWeathermap Feature.
         *
         * @return int Returns the cache lifetime in seconds.
         */
        private function getCacheLifetime() {

            return $this->get_option( 'cacheLifetime' );
        }

        /**
         * Returns true if caching is enabled.
         *
         * @return bool Returns true if caching is enabled.
         */
        private function isCacheEnabled() {

            return $this->get_option( 'enableCache' );
        }
    }
}
