<?php

/**
 * This file contains the RWC\Feature\OpenWeathermap\CurrentWeatherResult class.
 *
 * @author Brian Reich <breich@reich-consulting.net>
 * @copyright Copyright (C) 2017 Reich Web Consulting
 * @version 1.0
 * @package RWC\Features\OpenWeathermap
 */
namespace RWC\Features\OpenWeathermap {

    /**
     * Represents a weather result for a query for the current date and time.
     *
     * @author Brian Reich <breich@reich-consulting.net>
     * @copyright Copyright (C) 2017 Reich Web Consulting
     * @version 1.0
     * @package RWC\Features\OpenWeathermap
     */
    class CurrentWeatherResult {

        /**
         * The coordinate associated with the result.
         *
         * @var    Coordinate
         * @access  private
         */
        private $coordinate;

        /**
         * The Weather instance describing the basic weather details.
         *
         * @var    Weather
         * @access private
         */
        private $weather;

        /**
         * The base contains internal search parameters.
         *
         * @var    string
         * @access private
         */
        private $base;

        /**
         * The Main object provides the primary details about the Weather.
         *
         * @var    Main
         * @access private
         */
        private $main;

        /**
         * The Wind object describes wind speed and direction.
         *
         * @var    Wind
         * @access private
         */
        private $wind;

        /**
         * The percentage of cloudiness.
         *
         * @var    int
         * @access private
         */
        private $clouds;

        /**
         * Rain volume in the last three hours.
         *
         * @var    string
         * @access private
         */
        private $rain;

        /**
         * Snow volume in the last three hours.
         *
         * @var    string
         * @access private
         */
        private $snow;

        /**
         * UTC Unix timestamp when this result was retrieved.
         *
         * @var    int
         * @access private
         */
        private $timestamp;

        /**
         * UTC Unix timestamp of sunrise on the current date.
         *
         * @var    int
         * @access private
         */
        private $sunrise;

        /**
         * UTC Unix timestmap of the sunset on the current date.
         *
         * @var    int
         * @access private
         */
        private $sunset;

        /**
        * Sets the Coordinate for the CurrentWeatherResult.
        *
        * @param Coordinate $coordinate The Coordinate for the CurrentWeatherResult.
        */
        public function setCoordinate( Coordinate $coordinate ) {

            $this->coordinate = $coordinate;
        }

        /**
         * Returns the Coordinate for the CurrentWeatherResult.
         *
         * @return Coordinate Returns the Coordinate for the CurrentWeatherResult.
         */
        public function getCoordinate() {

            return $this->coordinate;
        }

        /**
         * Sets the Weather object for the CurrentWeatherResult.
         *
         * @param Weather $weather The Weather object for the CurrentWeatherResult.
         */
        public function setWeather( Weather $weather ) {

            $this->weather = $weather;
        }

        /**
         * Returns the Weather object for the CurrentWeatherResult.
         *
         * @return Weather Returns the Weather object for the CurrentWeatherResult.
         */
        public function getWeather() {

            return $this->weather;
        }

        /**
         * Sets the Base, which describes internal search parameters.
         *
         * @param string $base The base for the CurrentWeatherResult.
         */
        public function setBase( $base ) {

            $this->base = $base;
        }

        /**
         * Returns the base for the CurrentWeatherResult.
         *
         * @return CurrentWeatherResult Returns the base for the CurrentWeatherResult.
         */
        public function getBase() {

            return $this->base;
        }

        /**
         * Sets the Main object for the CurrentWeatherResult.
         *
         * @param Main $main The Main section of the CurrentWeatherResult.
         */
        public function setMain( Main $main ) {

            $this->main = $main;
        }

        /**
         * Reterns the Main section of the CurrentWeatherResult.
         *
         * @return Main Returns the Main section of the CurrentWeatherResult.
         */
        public function getMain() {

            return $this->main;
        }

        /**
         * Sets the Wind section of the CurrentWeatherResult.
         *
         * @param Wind $wind Sets the Wind section of the CurrentWeatherResult.
         */
        public function setWind( Wind $wind ) {
            $this->wind = $wind;
        }

        /**
         * Returns the Wind section of the CurrentWeatherResult.
         *
         * @retur Wind Returns the Wind section.
         */
        public function getWind() {

            return $this->wind;
        }

        /**
         * Sets the cloud percentage as an integer.
         *
         * @param int $cloud The percentage of clouds.
         */
        public function setClouds( $clouds ) {

            $this->clouds = $clouds;
        }

        /**
         * Returns the cloud percentage.
         *
         * @return int Returns the cloud percentage.
         */
        public function getClouds() {

            return $this->clouds;
        }

        /**
         * Sets the volume of rain in the last three hours.
         *
         * @param string $rain The volume of rain in the past three hours.
         */
        public function setRain( $rain ) {

            $this->rain = $rain;
        }

        /**
         * Returns the volume of rain in the past three hours.
         *
         * @return string Returns the volume of rain in the past three hours.
         */
        public function getRain() {

            return $this->rain;
        }

        /**
         * Sets the volume of snow in the past three hours.
         *
         * @param string $snow The volume of snow in the past three hours.
         */
        public function setSnow( $snow ) {

            $this->snow = $snow;
        }

        /**
         * Returns the volume of snow in the past three hours.
         *
         * @return string Returns the volume of snow in the past three hours.
         */
        public function getSnow() {

            return $this->snow;
        }

        /**
         * Sets the UTC Unix timestamp when this result was retrieved.
         *
         * @param int $timestamp The UTC Unix timestamp when the result was retrieved.
         */
        public function setTimestamp( $timestamp ) {

            $this->timestamp = $this->offsetTimestampForWordPressTimezone(
                $timestamp );
        }

        /**
         * Returns the UTC Unix timestamp when this result was retrieved.
         *
         * @return int Returns the UTC Unix timestamp when this result was retrieved.
         */
        public function getTimestamp() {

            return $this->timestamp;
        }

        /**
         * Sets the UTC Unix timestamp of the sunrise on the current date.
         *
         * @param int $sunrise The UNIX timestamp of the sunrise.
         */
        public function setSunrise( $sunrise ) {

            $this->sunrise = $this->offsetTimestampForWordPressTimezone(
                $sunrise );
        }

        /**
         * Returns the UTC Unix timestamp of the sunrise on the current date.
         *
         * @return int Returns the UNIX timestamp of the sunrise.
         */
        public function getSunrise() {

            return $this->sunrise;
        }

        /**
         * Sets the UTC Unix timestamp of the sunset on the current date.
         *
         * @param int $sunset The Unix timestamp of the sunset on the current date.
         */
        public function setSunset( $sunset ) {

            $this->sunset = $this->offsetTimestampForWordPressTimezone(
                $sunset );
        }

        /**
         * Returns the UTC Unix timestamp of the sunset on the current date.
         *
         * @return int Returns the timestamp of the sunset on the current date.
         */
        public function getSunset() {

            return $this->sunset;
        }

        /**
         * Returns a CSS class for the Icon associated with the current weather
         * conditions.
         *
         * @return string Returns a CSS class for the icon for current conditions.
         */
        public function getIconClass() {

            $iconList = new IconList();
            return $iconList->getIconClass( $this->getWeather()->getId());
        }

        /**
         * Creates a CurrentWeatherResult from a JSON object.
         *
         * @param stdClass|string $jsonInput The JSON object or JSON string.
         *
         * @return CurrentWeatherResult Returns the converted CurrentWeatherResult.
         */
        public static function fromJson( $jsonInput ) {

            // If it was passed as a json string, make it an object.
            if( is_string( $jsonInput ) ) {
                $json = json_decode( $jsonInput );
                $arr = json_decode( $jsonInput , true );

                // Make sure the response was valid.
                if( $json == null ) {
                    throw new OpenWeathermapException( 'Response could not ' .
                        'be decoded as a JSON object.' );
                }

                // If an error occured, throw an exception.
                if( $json->cod != 200 ) {

                    throw new OpenWeathermapException( 'An error occured ' .
                        'while querying Open Weathermap API. ' .
                            $json->message );
                }
            }

            return new CurrentWeatherResult(
                Coordinate::fromJson( $json->coord ),
                Weather::fromJson( $json->weather[ 0 ] ),
                $json->base,
                Main::fromJson( $json->main ),
                Wind::fromJson( $json->wind ),
                $json->clouds->all,
                (isset( $arr[ 'rain' ] ) ? $arr[ 'rain' ][ '3h' ] : ''),
                (isset( $arr[ 'snow' ] ) ? $arr[ 'snow' ][ '3h' ]  : ''),
                $json->dt,
                $json->sys->sunrise,
                $json->sys->sunset
            );
        }

        public function __construct( Coordinate $coordinate, Weather $weather,
          $base, Main $main, Wind $wind, $clouds, $rain, $snow, $timestamp,
          $sunrise, $sunset ) {

            $this->setCoordinate( $coordinate );
            $this->setWeather( $weather );
            $this->setBase( $base );
            $this->setMain( $main );
            $this->setWind( $wind );
            $this->setClouds( $clouds );
            $this->setRain( $rain );
            $this->setSnow( $snow );
            $this->setTimestamp( $timestamp );
            $this->setSunrise( $sunrise );
            $this->setSunset( $sunset );
        }

        /**
         * Converts the specified UTC timestamp into a timestamp for the
         * timezone configured in WordPress.
         *
         * @param int $timestamp The UTC timestamp.
         *
         * @return int Returns the timestamp offset for the site's timezone.
         */
        private function offsetTimestampForWordPressTimezone( $timestamp )
        {
            return $timestamp;
            
            // Get timezone offset in seconds for configured timezone.
            $offset = timezone_offset_get(
                new \DateTimezone( get_option('timezone_string') ),
                new \DateTime( date("c", $timestamp ))
            );

            return $timestamp + $offset;
        }
    }
}
