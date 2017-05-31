<?php

/**
 * This file contains the RWC\Feature\OpenWeathermap\Weather class.
 *
 * @author Brian Reich <breich@reich-consulting.net>
 * @copyright Copyright (C) 2017 Reich Web Consulting
 * @version 1.0
 * @package RWC\Features\OpenWeathermap
 */

namespace RWC\Features\OpenWeathermap {

    /**
     * Describes the basic details of the weather.
     *
     * @author Brian Reich <breich@reich-consulting.net>
     * @copyright Copyright (C) 2017 Reich Web Consulting
     * @version 1.0
     * @package RWC\Features\OpenWeathermap
     */
    class Weather {

        /**
         * The unique id of the weather condition.
         *
         * @var    int
         * @access private
         */
        private $id;

        /**
         * Group of weather parameters (rain, snow, etc.).
         *
         * @var    string
         * @access private
         */
        private $main;

        /**
         * Weather conditions within the group.
         *
         * @var    string
         * @access private
         */
        private $description;

        /**
         * Weather icon ID.
         *
         * @var    string
         * @access private
         */
        private $icon;

        /**
         * Sets the unique id of the weather condition.
         *
         * @param int $id The unique id of the weather condition.
         */
        public function setId( $id ) {

            $this->id = $id;
        }

        /**
         * Returns the unique id of the weather condition.
         *
         * @return int Returns the unique id of the weather condition.
         */
        public function getId() {

            return $this->id;
        }

        /**
         * Sets the main group of weather parameters (rain, snow, etc.);.
         *
         * @param string $main The maon group of weather parameters.
         */
        public function setMain( $main ) {

            $this->main = $main;
        }

        /**
         * Returns the main group of weather parameters.
         *
         * @return string Returns the main group of weather parameters.
         */
        public function getMain() {

            return $this->main;
        }

        /**
         * Sets the description of the weather condition within the group.
         *
         * @param string $description The weather condition with the group.
         */
        public function setDescription( $description ) {

            $this->description = $description;
        }

        /**
         * Returns the description of the weather condition within the group.
         *
         * @return string Returns the weather condition within the group.
         */
        public function getDescription() { return $this->description; }

        /**
         * Sets the icon id for the weather condition.
         *
         * @param string $icon The icon id for the weather condition.
         */
        public function setIcon( $icon ) { $this->icon = $icon; }

        /**
         * Returns the icon id for the weather condition.
         *
         * @return string Returns the icon id for the weather condition.
         */
        public function getIcon() { return $this->icon; }

        /**
         * Creates a new Weather instance from a JSON-decoded object.
         *
         * @param \stdClass $json The JSON-decoded object.
         *
         * @return Weather Returns a Weather instance from the JSON.
         */
        public static function fromJson( \stdClass $json ) {

            return new Weather(
                $json->id,
                $json->main,
                $json->description,
                $json->icon
            );
        }

        /**
        * Creates a new Weather instance.
        *
        * @param string $id          The unique id of the weather condition.
        * @param string $main        The main weather category.
        * @param string $description The condition within the weather group.
        * @param string $icon        The weather icon id.
        */
        public function __construct( $id, $main, $description, $icon ) {

            $this->setId( $id );
            $this->setMain( $main );
            $this->setDescription( $description );
            $this->setIcon( $icon );
        }
    }
}
