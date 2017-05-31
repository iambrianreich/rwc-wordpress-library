<?php

/**
 * This file contains the RWC\Feature\OpenWeathermap\Coordinate class.
 *
 * @author Brian Reich <breich@reich-consulting.net>
 * @copyright Copyright (C) 2017 Reich Web Consulting
 * @version 1.0
 * @package RWC\Features\OpenWeathermap
 */

namespace RWC\Features\OpenWeathermap {

    /**
     * Represents a latitude/longitude GPS coordinate.
     *
     * @author Brian Reich <breich@reich-consulting.net>
     * @copyright Copyright (C) 2017 Reich Web Consulting
     * @version 1.0
     * @package RWC\Features\OpenWeathermap
     */
    class Coordinate {

        /**
         * The latitude component of the Coordinate.
         *
         * @var    string
         * @access private
         */
        private $latitude;

        /**
         * The longitude component of the Coordinate.
         *
         * @var    string
         * @access private
         */
        private $longitude;

        /**
         * Creates a new Coordinate.
         *
         * @param string $latitude The latitude.
         * @param string $longitude The longitude.
         *
         * @throws OpenWeathermapException if the latitude and longitude are invalid.
         */
        public function __construct( $latitude, $longitude ) {

            $this->setLatitude( $latitude );
            $this->setLongitude( $longitude );
        }

        /**
         * Sets the latitude component of the Coordinate.
         *
         * @param string $latitude The latitude component of the coordinate.
         */
        public function setLatitude( $latitude ) {

            $this->latitude = $latitude;
        }

        /**
         * Returns the latitude component of the Coordinate.
         *
         * @return string Returns the latitude.
         */
        public function getLatitude() {

            return $this->latitude;
        }

        /**
         * Sets the longitude component of the Coordinate.
         *
         * @param string $longitude The longitude
         */
        public function setLongitude( $longitude ) {

            $this->longitude = $longitude;
        }

        /**
         * Returns the longitude component of the Coordinate.
         *
         * @return string Returns the Longitude.
         */
        public function getLongitude() {

            return $this->longitude;
        }

        /**
         * Creates a new Coordinate instance from a JSON-decoded object.
         *
         * @param \stdClass $json The JSON-decoded object.
         *
         * @return Weather Returns a Weather instance from the JSON.
         */
        public static function fromJson( \stdClass $json ) {

            return new Coordinate(
                $json->lat,
                $json->lon
            );
        }
    }
}
