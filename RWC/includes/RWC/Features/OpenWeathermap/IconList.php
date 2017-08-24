<?php

/**
 * This file contains the RWC\Features\Openweathermap\IconList class.
 *
 * @author Brian Reich <breich@reich-consulting.net>
 * @copyright Copyright (C) 2016 Reich Web Consulting
 * @version 1.0
 * @package RWC\Features
 */
namespace RWC\Features\OpenWeathermap {

    /**
     * IconList provides an easy way to map an OpenWeathermap weather condition
     * code to a CSS icon class in the provided icon font.
     *
     * This class is taken from the GIST by user tbranyen at
     * https://gist.github.com/tbranyen/62d974681dea8ee0caa1 and the icon font
     * is a defunct Github project at https://github.com/erikflowers/weather-icons
     * by Erik Flowers. All credit for mapping weather codes to CSS classes and
     * for creation of the webfont pack go to these individuals. We just added
     * this PHP wrapper to make the icon pack easy to use with our PHP Library.
     *
     * @author Brian Reich <breich@reich-consulting.net>
     * @copyright Copyright (C) 2016 Reich Web Consulting
     * @version 1.0
     * @package RWC\Features
     */
    class IconList {

        /**
         * An arrociative array of icon data.
         *
         * @var    array
         * @access private
         */
        private $list = null;

        /**
         * Returns the icon CSS class fot the specified icon code.
         *
         * @return string Returns the CSS class for the icon code.
         */
        public function getIconClass( $iconCode )
        {
            $list = $this->getIconList();
            if( isset( $list[ $iconCode ]) ) {

                return $list[ $iconCode ][ 'icon' ];
            }
        }

        /**
         * Returns the associative array of icon data used to map an
         * OpenWeathermap icon code to a CSS class.
         *
         * @return array Returns an associative array of icon data.
         */
        public function getIconList() {

            if( is_null( $this->list ) ) {
                $this->list = json_decode( $this->getIconListJsonString(), true );
            }

            return $this->list;
        }

        /**
         * Returns the icon mapping JSON.
         *
         * @return string Returns the icon mapping JSON code.
         */
        private function getIconListJsonString() {

            ob_start(); ?>

            {
              "200": {
                "label": "thunderstorm with light rain",
                "icon": "storm-showers"
              },

              "201": {
                "label": "thunderstorm with rain",
                "icon": "storm-showers"
              },

              "202": {
                "label": "thunderstorm with heavy rain",
                "icon": "storm-showers"
              },

              "210": {
                "label": "light thunderstorm",
                "icon": "storm-showers"
              },

              "211": {
                "label": "thunderstorm",
                "icon": "thunderstorm"
              },

              "212": {
                "label": "heavy thunderstorm",
                "icon": "thunderstorm"
              },

              "221": {
                "label": "ragged thunderstorm",
                "icon": "thunderstorm"
              },

              "230": {
                "label": "thunderstorm with light drizzle",
                "icon": "storm-showers"
              },

              "231": {
                "label": "thunderstorm with drizzle",
                "icon": "storm-showers"
              },

              "232": {
                "label": "thunderstorm with heavy drizzle",
                "icon": "storm-showers"
              },

              "300": {
                "label": "light intensity drizzle",
                "icon": "sprinkle"
              },

              "301": {
                "label": "drizzle",
                "icon": "sprinkle"
              },

              "302": {
                "label": "heavy intensity drizzle",
                "icon": "sprinkle"
              },

              "310": {
                "label": "light intensity drizzle rain",
                "icon": "sprinkle"
              },

              "311": {
                "label": "drizzle rain",
                "icon": "sprinkle"
              },

              "312": {
                "label": "heavy intensity drizzle rain",
                "icon": "sprinkle"
              },

              "313": {
                "label": "shower rain and drizzle",
                "icon": "sprinkle"
              },

              "314": {
                "label": "heavy shower rain and drizzle",
                "icon": "sprinkle"
              },

              "321": {
                "label": "shower drizzle",
                "icon": "sprinkle"
              },

              "500": {
                "label": "light rain",
                "icon": "rain"
              },

              "501": {
                "label": "moderate rain",
                "icon": "rain"
              },

              "502": {
                "label": "heavy intensity rain",
                "icon": "rain"
              },

              "503": {
                "label": "very heavy rain",
                "icon": "rain"
              },

              "504": {
                "label": "extreme rain",
                "icon": "rain"
              },

              "511": {
                "label": "freezing rain",
                "icon": "rain-mix"
              },

              "520": {
                "label": "light intensity shower rain",
                "icon": "showers"
              },

              "521": {
                "label": "shower rain",
                "icon": "showers"
              },

              "522": {
                "label": "heavy intensity shower rain",
                "icon": "showers"
              },

              "531": {
                "label": "ragged shower rain",
                "icon": "showers"
              },

              "600": {
                "label": "light snow",
                "icon": "snow"
              },

              "601": {
                "label": "snow",
                "icon": "snow"
              },

              "602": {
                "label": "heavy snow",
                "icon": "snow"
              },

              "611": {
                "label": "sleet",
                "icon": "sleet"
              },

              "612": {
                "label": "shower sleet",
                "icon": "sleet"
              },

              "615": {
                "label": "light rain and snow",
                "icon": "rain-mix"
              },

              "616": {
                "label": "rain and snow",
                "icon": "rain-mix"
              },

              "620": {
                "label": "light shower snow",
                "icon": "rain-mix"
              },

              "621": {
                "label": "shower snow",
                "icon": "rain-mix"
              },

              "622": {
                "label": "heavy shower snow",
                "icon": "rain-mix"
              },

              "701": {
                "label": "mist",
                "icon": "sprinkle"
              },

              "711": {
                "label": "smoke",
                "icon": "smoke"
              },

              "721": {
                "label": "haze",
                "icon": "day-haze"
              },

              "731": {
                "label": "sand, dust whirls",
                "icon": "cloudy-gusts"
              },

              "741": {
                "label": "fog",
                "icon": "fog"
              },

              "751": {
                "label": "sand",
                "icon": "cloudy-gusts"
              },

              "761": {
                "label": "dust",
                "icon": "dust"
              },

              "762": {
                "label": "volcanic ash",
                "icon": "smog"
              },

              "771": {
                "label": "squalls",
                "icon": "day-windy"
              },

              "781": {
                "label": "tornado",
                "icon": "tornado"
              },

              "800": {
                "label": "clear sky",
                "icon": "sunny"
              },

              "801": {
                "label": "few clouds",
                "icon": "cloudy"
              },

              "802": {
                "label": "scattered clouds",
                "icon": "cloudy"
              },

              "803": {
                "label": "broken clouds",
                "icon": "cloudy"
              },

              "804": {
                "label": "overcast clouds",
                "icon": "cloudy"
              },


              "900": {
                "label": "tornado",
                "icon": "tornado"
              },

              "901": {
                "label": "tropical storm",
                "icon": "hurricane"
              },

              "902": {
                "label": "hurricane",
                "icon": "hurricane"
              },

              "903": {
                "label": "cold",
                "icon": "snowflake-cold"
              },

              "904": {
                "label": "hot",
                "icon": "hot"
              },

              "905": {
                "label": "windy",
                "icon": "windy"
              },

              "906": {
                "label": "hail",
                "icon": "hail"
              },

              "951": {
                "label": "calm",
                "icon": "sunny"
              },

              "952": {
                "label": "light breeze",
                "icon": "cloudy-gusts"
              },

              "953": {
                "label": "gentle breeze",
                "icon": "cloudy-gusts"
              },

              "954": {
                "label": "moderate breeze",
                "icon": "cloudy-gusts"
              },

              "955": {
                "label": "fresh breeze",
                "icon": "cloudy-gusts"
              },

              "956": {
                "label": "strong breeze",
                "icon": "cloudy-gusts"
              },

              "957": {
                "label": "high wind, near gale",
                "icon": "cloudy-gusts"
              },

              "958": {
                "label": "gale",
                "icon": "cloudy-gusts"
              },

              "959": {
                "label": "severe gale",
                "icon": "cloudy-gusts"
              },

              "960": {
                "label": "storm",
                "icon": "thunderstorm"
              },

              "961": {
                "label": "violent storm",
                "icon": "thunderstorm"
              },

              "962": {
                "label": "hurricane",
                "icon": "cloudy-gusts"
              }
            }<?php

            $json = ob_get_contents();
            ob_end_clean();

            return $json;
        }
    }
}
