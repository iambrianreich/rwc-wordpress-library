<?php

/**
 * This file contains the RWC\Feature\GoogleMaps class.
 *
 * @author Brian Reich <breich@reich-consulting.net>
 * @copyright Copyright (C) 2016 Reich Web Consulting
 * @version 1.0
 * @package RWC\Features
 */

namespace RWC\Features {

	/**
	 * This feature provides Google Maps functionality to the website.
	 *
	 * @author Brian Reich <breich@reich-consulting.net>
	 * @copyright Copyright (C) 2016 Reich Web Consulting
	 * @version 1.0
	 * @package RWC\Features
	 */
	class GoogleMaps extends \RWC\Feature {

		// TODO Configure API key.

		/**
		 * Google Maps API Key.
		 *
		 * @var string
		 */
		const MAPS_API_KEY = 'AIzaSyDDSiN9tcjXG-QwHtd73nZ5lXV5K9j6jVY';

		/**
		 * Initialize the Google Maps Feature.
		 *
		 * @return void
		 */
		public function initialize() {

			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue' ) );
		}

		/**
		 * Enqueues the Google Maps JavaScript API.
		 *
		 * @return void
		 */
		public function enqueue() {

			// Google Maps
			if( ! wp_script_is( 'avia-google-maps-api'  ) ) {
				wp_enqueue_script(
					'google-maps-api',
					'https://maps.googleapis.com/maps/api/js?key=' .
						$this->get_google_maps_api_key(),
					array(),
					false,
					true
				);
			}
		}

		/**
		 * Returns the Google Maps API key.
		 *
		 * @return string Returns the Google Maps API key.
		 */
		protected function get_google_maps_api_key() {

			return self::MAPS_API_KEY;
		}
	}
}
