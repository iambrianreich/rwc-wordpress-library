<?php

/**
 * This file contains the RWC\Google\Places class, a wrapper for the Google
 * Places API which allows lookup of places in Google Maps and place-related
 * information.
 *
 * @author Brian Reich <breich@reich-consulting.net>
 * @copyright Copyright (C) 2017 Reich Web Consulting
 */

namespace RWC\Google {

    /**
     * A wrapper for the Google Places API.
     *
     * In order to use the Google Places API wrapper, you need to provide an
     * API key. You can get an API key from the Google Developer Console. The
     * API key can be passed manually via the "apiKey" configuration option,
     * or it can be set in the Reich Web Consulting PHP library options.
     *
     * @see https://console.developers.google.com/
     * @author Brian Reich <breich@reich-consulting.net>
     * @copyright Copyright (C) 2017 Reich Web Consulting
     */
    class Places {

        /**
         * The API key.
         */
        private $apiKey;

        /**
         * The base URL for querying the places API.
         *
         * @var    string
         * @access private
         */
        private $url = 'https://maps.googleapis.com/maps/api/place';

        /**
         * Create a new Places instance.
         *
         * The options array is completely optional.  However, the Places
         * instance needs to be able to find a Google API key somewhere. You
         * can set the API key directly by passing the "apiKey" option in the
         * configuration array. Alternatively, a "GoogleApiKey" option can be
         * specified in the libraries initialization options, or passed as a
         * callback method which returns the API key.
         *
         * @param array $options An array of configuration options.
         * @constructor
         */
        public function __construct( array $options = array() ) {

            $this->setOptions( $options );
        }

        /**
         * Sets the configuration options.
         *
         * @param array $options An array of configuration options.
         *
         * @return void
         */
        public function setOptions( array $options = array() ) {

            // Mix-in defaults.
            $options = \RWC\Utility::get_options( $options, [
                'apiKey' => null
            ] );

            $this->setApiKey( $options[ 'apiKey' ] );
        }

        /**
         * Sets the API key passed to setOptions().
         *
         * @param string $apiKey The Google API Key to use for queries.
         *
         * @return void
         */
        private function setApiKey( $apiKey ) {

            $this->apiKey = $apiKey;
        }

        /**
         * Returns the API key to use to query the Google Places API.
         *
         * If an API was configured, it will be used. If an API key was not
         * configured, the Library's configuration will be checked for an option
         * called "GoogleApiKey." If the option is a string it will be used as
         * an API key.
         *
         * If an API key cannot be found, an Exception will be thrown.
         *
         * @return string Returns the Google API key.
         * @throws \RWC\Google\MissingApiKeyException if no API is configured.
         */
        private function getApiKey() {

            // If a key was configured, use it.
            if( $this->apiKey !== null ) return $this->apiKey;

            // Get the GoogleApiKey library option.
            $apiKey = \RWC\Library::load()->get_option( 'GoogleApiKey' );

            // If it's a string, use it.
            if( is_string( $apiKey ) ) return $apiKey;

            // If it's callable, execute it and use return value as key.
            if( is_callable( $apiKey ) ) {

                return $apiKey();
            }

            throw new MissingApiKeyException(
                'No Google API key has been configured.' );
        }

        /**
         * Finds a Place given a search string.
         *
         * Finds a Place given a search string. Can be the name of a location,
         * an address, etc. If the query succeeds a PHP stdClass will be
         * returned by converting the Places JSON response to an object. If an
         * error occurs while making the request, an Exception will be thrown.
         *
         * @param string $place The place search stirng.
         *
         * @return stdClass Returns a PHP object containing matching places.
         * @throws Exception if an error occurs while making the request.
         */
        public function findPlaces( $place ) {

            // Make the request.
            $response = wp_remote_get( $this->url . sprintf(
                '/textsearch/json?key=%s&query=%s', $this->getApiKey(),
                    urlencode( $place ) ) );

            if( is_wp_error( $response ) ) {

                throw new Exception( 'An error occured while querying Google ' .
                    'Places for location data: ' .
                        $response->get_error_message() );
            }

            return json_decode( $response[ 'body' ] );
        }

        /**
         * Returns details about a particular Google Place, by placeId.
         *
         * The getDetails() method will return details about a specific location
         * in Google Places, by it's unique placeId. The placeId can be found
         * by first searching for the location by name or by address using
         * findPlaces.
         *
         * @param string $placeId The unique id of the place in Google Places.
         *
         * @return stdClass Returns a stdClass with the query results.
         * @throws Exception if an error occurs while making the request.
         */
        public function getDetails( $placeId ) {

            $response = wp_remote_get( $this->url . sprintf(
                '/details/json?key=%s&placeid=%s&', $this->getApiKey(),
                    urlencode( $placeId ) ) );

            if( is_wp_error( $response ) ) {

                throw new Exception( 'An error occured while querying Google ' .
                    'Places for location details: ' .
                        $response->get_error_message() );
            }

            return json_decode( $response[ 'body' ] );
        }
    }
}
