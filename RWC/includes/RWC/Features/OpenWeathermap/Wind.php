<?php

/**
 * This file contains the RWC\Feature\OpenWeathermap\Wind class.
 *
 * @author Brian Reich <breich@reich-consulting.net>
 * @copyright Copyright (C) 2017 Reich Web Consulting
 * @version 1.0
 * @package RWC\Features\OpenWeathermap
 */

namespace RWC\Features\OpenWeathermap {

    /**
     * Represents the Wind speed and direction.
     *
     * @author Brian Reich <breich@reich-consulting.net>
     * @copyright Copyright (C) 2017 Reich Web Consulting
     * @version 1.0
     * @package RWC\Features\OpenWeathermap
     */
    class Wind {

        /**
         * The wind speed.
         *
         * @var     int
         * @access  private
         */
        private $speed;

        /**
         * The  wind direction in degrees.
         *
         * @var    int
         * @access private
         */
        private $direction;

        /**
         * Sets the wind speed.
         *
         * @param int $speed The wind speed.
         */
        public function setSpeed( $speed ) {

            $this->speed = $speed;
        }

        /**
         * Returns the wind speed.
         *
         * @return int Returns the wind speed.
         */
        public function getSpeed() {

            return $this->speed;
        }

        /**
         * Sets the wind direction in degrees.
         *
         * @param int $direction The wind direction in degrees.
         */
        public function setDirection( $direction ) {

            $this->direction = $direction;
        }

        /**
         * Returns the wind direction in degrees.
         *
         * @return int Returns the wind direction in degrees.
         */
        public function getDirection() {

            return $this->direction;
        }

        /**
         * Creates a new Wind.
         *
         * @param int $speed The wind speed.
         * @param int $direction The wind direction.
         */
        public function __construct( $speed, $direction ) {

            $this->setSpeed( $speed );
            $this->setDirection( $direction );
        }

        /**
         * Creates a new Wind instance from a JSON-decoded object.
         *
         * @param string $json The JSON-decoded object.
         *
         * @return Weather Returns a Wind instance from the JSON.
         */
        public static function fromJson( $json ) {

            return new Wind(
                $json->speed,
                $json->deg
            );
        }
    }
}
