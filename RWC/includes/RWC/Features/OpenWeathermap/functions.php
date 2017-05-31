<?php

/**
 * This file contains functions for easily rendering Open Weathermap data via
 * PHP functions instead of shortcodes.
 *
 * @author Brian Reich <breich@reich-consulting.net>
 * @copyright Copyright (C) 2016 Reich Web Consulting
 * @version 1.0
 * @package RWC\Features
 */

 /**
  * Renders and returns a current weather result for a zipcode location.
  *
  * @param array $options An array of shortcode options.
  *
  * @return string Returns the HTML for the shortcode.
  */
function openweathermap_for_zip( $options = array() ) {

    echo do_shortcode( '[openweathermap_for_zip ' .
        http_build_query( $options, '', ' ' ) . ']');
}

/**
 * Renders and returns a current weather result for a city.
 *
 * @param array $options An array of shortcode options.
 *
 * @return string Returns the HTML for the shortcode.
 */
function openweathermap_for_city( $options = array() ) {

   echo do_shortcode( '[openweathermap_for_city ' .
       http_build_query( $options, '', ' ' ) . ']');
}
