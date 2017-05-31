<?php

namespace RWC\Features\OpenWeathermap {

    class Service {

        /**
         * The base URL of all calls to the OpenWeathermap service.
         *
         * @var string
         */
        const SERVICE_ROOT = 'http://api.openweathermap.org/data/2.5/weather?';

        /**
         * The API Key used to access the API.
         *
         * @var    string
         * @access private
         */
        private $apiKey;

        /**
         * The number of seconds to keep results in cache.
         *
         * @var    int
         * @access private
         */
        private $cacheLifetime;

        /**
         * The units of measurement. Either standard, metric, or imperial.
         *
         * @var    string
         * @access private
         */
        private $units;

        /**
         * Creates a new Service instance.
         *
         * @param string $key           The API key used to access the API.
         * @param int    $cacheLifetime The number of seconds to cache results.
         * @param string $units         The type of units to return.
         *
         * @throws OpenWeathermapException If the API key is invalid.
         */
        public function __construct( $key, $cacheLifetime = 0, $units = 'imperial' ) {

            $this->setApiKey( $key );
            $this->setCacheLifetime( $cacheLifetime );
            $this->setUnits( $units );
        }

        /**
         * Returns the type of units to use for search results.
         *
         * @param string $units The type of units (imperial, metric, standard)
         *
         * @throws OpenWeathermapException If $units is an invalid value.
         */
        public function setUnits( $units )
        {

            $valid = array( 'imperial', 'metric', 'standard' );

            // Make sure it's a valid option.
            if( ! in_array( $units, $valid ) ) {

                throw new OpenWeathermapException( 'The units option must be ' .
                    'one of ' . explode( ', ', $valid ) );
            }

            $this->units = $units;
        }

        public function getUnits() {

            return $this->units;
        }
        /**
         * Sets the number of seconds to keep results in cache.
         *
         * @param int $cacheLifetime The number of seconds to cache results.
         */
        public function setCacheLifetime( $cacheLifetime ) {

            $this->cacheLifetime = $cacheLifetime;
        }

        /**
         * Returns the number of seconds to keep results in cache.
         *
         * @return int Returns the number of seconds to keep results in cache.
         */
        public function getCacheLifetime() {

            return $this->cacheLifetime;
        }

        /**
         * Sets the API key used to access the API.
         *
         * @param string $key The API key used to access the API.
         *
         * @throws OpenWeathermapException If the key is null or empty.
         */
        public function setApiKey( $key ) {

            if( empty( $key ) ) {

                throw new OpenWeathermapException(
                    'API key cannot be null or empty.');
            }

            $this->apiKey = $key;
        }

        /**
         * Returns the API key used to access the OpenWeathermap API.
         *
         * @return string Returns the OpenWeathermap API key.
         */
        public function getApiKey() {

            return $this->apiKey;
        }

        /**
         * Returns a CurrentWeatherResult object for the specified zipcode and
         * country.
         *
         * @param string $zipcode The zipcode to search for.
         * @param string $country The country code.
         *
         * @return CurrentWeatherResult Returns the CurrentWeatherResult.
         */
        public function getCurrentWeatherByZipcode( $zipcode, $country = 'us' )
        {
            // TODO CACHING

            // Generate the full URL to query by zipcode
            $url = self::SERVICE_ROOT . sprintf('zip=%s,%s&appid=%s&units=%s', $zipcode,
                $country, $this->getApiKey(), $this->getUnits() );

            // Get a response from the API.
            $response = wp_remote_get( $url );

            // If the API response was invalid throw an exception.
            if( $response instanceof \WP_Error ) {

                throw new OpenWeathermapException( 'An error occured while ' .
                    'accessing the OpenWeathermap API: ' .
                    $response->get_error_message() );
            }

            // Convert the response to an object.
            return CurrentWeatherResult::fromJson( $response[ 'body' ] );
        }

        /**
         * Returns a CurrentWeatherResult object for the specified zipcode and
         * country.
         *
         * @param string $zipcode The zipcode to search for.
         * @param string $country The country code.
         *
         * @return CurrentWeatherResult Returns the CurrentWeatherResult.
         */
        public function getCurrentWeatherByCity( $city, $country = 'us' )
        {
            // TODO CACHING

            // Generate the full URL to query by zipcode
            $url = self::SERVICE_ROOT . sprintf('q=%s,%s&appid=%s&units=%s',
                esc_attr( $zipcode ),
                esc_attr( $country ),
                esc_attr( $this->getApiKey() ),
                esc_attr( $this->getUnits() ) );

            // Get a response from the API.
            $response = wp_remote_get( $url );

            // If the API response was invalid throw an exception.
            if( $response instanceof \WP_Error ) {

                throw new OpenWeathermapException( 'An error occured while ' .
                    'accessing the OpenWeathermap API: ' .
                    $response->get_error_message() );
            }

            // Convert the response to an object.
            return CurrentWeatherResult::fromJson( $response[ 'body' ] );
        }
    }
}
