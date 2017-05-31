<?php

/**
 * This file contains the RWC\Feature\OpenWeathermap\Main class.
 *
 * @author Brian Reich <breich@reich-consulting.net>
 * @copyright Copyright (C) 2017 Reich Web Consulting
 * @version 1.0
 * @package RWC\Features\OpenWeathermap
 */

namespace RWC\Features\OpenWeathermap {

    /**
     * Represents the main details about a weather result.
     *
     * @author Brian Reich <breich@reich-consulting.net>
     * @copyright Copyright (C) 2017 Reich Web Consulting
     * @version 1.0
     * @package RWC\Features\OpenWeathermap
     */
    class Main {

        /**
         * Atmospheric pressure at sea level.
         *
         * @var    int
         * @access private
         */
        private $pressure;

        /**
         * Humidity percentage
         *
         * @var    int
         * @access private
         */
        private $humidity;

        /**
         * The minimum temperature at the moment.
         *
         * @var    int
         * @access private
         */
        private $temperatureMinimum;

        /**
         * The maximum temperature at the moment.
         *
         * @var    int
         * @access private
         */
        private $temperatureMaximum;

        /**
         * Atmospheric pressure at sea level.
         *
         * @var    int
         * @access private
         */
        private $atmosphericPressureAtSeaLevel;

        /**
         * Atmospheric pressure at ground level.
         *
         * @var    int
         * @access private
         */
        private $atmosphericPressureAtGroundLevel;

        /**
         * Sets the temperature.
         *
         * @param int $temperature The temperature.
         */
        public function setTemperature( $temperature ) {
            $this->temperature = $temperature;
        }

        /**
         * Returns the temperature.
         *
         * @return int Returns the temperature.
         */
        public function getTemperature() {
            return $this->temperature;
        }

        /**
         * Sets the atmospheric pressure.
         *
         * @param int $pressure The atmospheric pressure.
         */
        public function setPressure( $pressure ) {
            $this->pressure = $pressure;
        }

        /**
         * Returns the atmospheric pressure.
         *
         * @return int Returns the atmospheric pressure.
         */
        public function getPressure() {
            return $this->pressure;
        }

        /**
         * Sets the humidity percentage.
         *
         * @param int $humidity The humidity percentage.
         */
        public function setHumidity( $humidity ) {

            $this->humidity = $humidity;
        }

        /**
         * Returns the humidity percentage.
         *
         * @return int Returns the humidity percentage.
         */
        public function getHumidity() {

            return $this->humidity;
        }

        /**
         * Sets the minimum temperature.
         *
         * @param int $temperatureMinimum The minimum temperature.
         */
        public function setTemperatureMinimum( $temperatureMinimum ) {

            $this->temperatureMinimum = $temperatureMinimum;
        }

        /**
         * returns the minimum temperature.
         *
         * @return int Returns the minimum temperature.
         */
        public function getTemperatureMinimum() {

            return $this->temperatureMinimum;
        }

        /**
         * Sets the maximum temperature.
         *
         * @param int $temperatureMaximum The maximum temperature.
         */
        public function setTemperatureMaximum( $temperatureMaximum ) {

            $this->temperatureMaximum = $temperatureMaximum;
        }

        /**
         * Returns the maximum temperature.
         *
         * @return int Returns the maximum temperature.
         */
        public function getTemperatureMaximum() {

            return $this->temperatureMaximum;
        }

        /**
         * Sets the atmospheric pressure at sea level.
         *
         * @param int $atmosphericTemperatureAtSeaLevel The atmospheric pressure.
         */
        public function setAtmosphericTemperatureAtSeaLevel( $atmosphericTemperatureAtSeaLevel ) {

            $this->atmosphericTemperatureAtSeaLevel = $atmosphericTemperatureAtSeaLevel;
        }

        /**
         * Returns the atmospheric pressure at sea level.
         *
         * @return int Returns the atmospheric pressure at sea level.
         */
        public function getAtmosphericTemperatureAtSeaLevel() {

            return $this->atmosphericTemperatureAtSeaLevel;
        }

        /**
         * Sets the atmospheric pressure at ground level.
         *
         * @param int $atmosphericTemperatureAtGroundLevel The atmospheric pressure.
         */
        public function setAtmosphericTemperatureAtGroundLevel( $atmosphericTemperatureAtGroundLevel ) {

            $this->atmosphericTemperatureAtGroundLevel = $atmosphericTemperatureAtGroundLevel;
        }

        /**
         * Returns the atmospheric pressure at ground level.
         *
         * @return int Returns the atmospheric pressure at ground level.
         */
        public function getAtmosphericTemperatureAtGroundLevel() {

            return $this->atmosphericTemperatureAtGroundLevel;
        }

        /**
         * Creates a new Main instance.
         *
         * @param int $temperature                      The temperature.
         * @param int $pressure                         The atmospheric pressure.
         * @param int $humidity                         The humidity percentage.
         * @param int $temperatureMinimum               The minimum temperature.
         * @param int $temperatureMaximum               The maximum temperature.
         * @param int $atmosphericTemperatureAtSeaLevel The pressure at sea level.
         * @param int $atmosphericTemperatureAtGroundLevel The pressure at ground level.
         */
        public function __construct( $temperature, $pressure, $humidity,
          $temperatureMinimum, $temperatureMaximum, $atmosphericTemperatureAtSeaLevel,
          $atmosphericTemperatureAtGroundLevel ) {

            $this->setTemperature( $temperature );
            $this->setPressure( $pressure );
            $this->setHumidity( $humidity );
            $this->setTemperatureMinimum( $temperatureMinimum );
            $this->setTemperatureMaximum( $temperatureMaximum );
            $this->setAtmosphericTemperatureAtSeaLevel( $atmosphericPressureAtSeaLevel );
            $this->setAtmosphericTemperatureAtGroundLevel( $atmosphericTemperatureAtGroundLevel );
        }

        /**
         * Creates a new Main instance from a JSON-decoded object.
         *
         * @param string $json The JSON-decoded object.
         *
         * @return Weather Returns a Weather instance from the JSON.
         */
        public static function fromJson( $json ) {

            return new Main(
                $json->temp,
                $json->pressure,
                $json->humidity,
                $json->temp_min,
                $json->temp_max,
                $json->sea_level,
                $json->grnd_level
            );
        }
    }
}
