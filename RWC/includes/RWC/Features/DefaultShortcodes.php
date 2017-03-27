<?php

/**
 * Contains the RWC\Features\DefaultShortcodes class.
 *
 * @author Brian Reich <breich@reich-consulting.net>
 * @copyright Copyright (C) 2016 Reich Consulting
 * @package RWC\Features
 */

namespace RWC\Features {

	/**
	 * The DefaultShortcodes Feature provides a series of useful shortcodes
     * enabled by default on any site using the Reich Web Consulting WordPress
     * Library.
	 *
	 * @author Brian Reich <breich@reich-consulting.net>
	 * @copyright Copyright (C) 2016 Reich Consulting
	 * @package RWC\Features
	 */
	class DefaultShortcodes extends \RWC\Feature {

		/**
		 * Initializes the PostLocations Feature.
		 *
		 * @return void
		 */
		public function initialize() {

			add_shortcode( 'the-year', array( $this, 'render_the_year' ) );
			add_shortcode( 'google-reviews-badge', array( $this, 'google_reviews_badge' ) );
		}

		/**
		 * Renders a Badge which will provide Rich Data about a business listed
		 * on Google Places.
		 *
		 * The shortcode accepts the following options. The "apiKey" option
		 * can be used to manually specify an API key.  If the API Key is
		 * omitted, the API will attempt to see if it can find an API key in
		 * the library configuration. The "query" option can be used to render
		 * a badge based on a Google Places query, based on a place name or
		 * an address. If this option is used, a badge will be rendered for
		 * all matching locations.  The "placeId" option can be used to manually
		 * specify the id of the place to be rendered.
		 *
		 * @param array $options The shortcode options.
		 */
		public function google_reviews_badge( $option ) {

			try {

				$options = shortcode_atts( [
					'apiKey' => null,
					'query'  => null,
					'placeId' => null
				], $option );

				$places = new \RWC\Google\Places( $options );

				// A query was specified, which may return multiple locations.
				if( $options[ 'query' ] !== null ) {

					// Do the query.
					$place = $places->findPlaces( $options[ 'query' ]  );

					// If no results, show message stating it.
					if( count( $place->results ) == 0 ) {
						return sprintf( "No Google Places found matching %s",
							$options[ 'query' ] );
					}

					// Show badge for each.
					$html = '';
					foreach( $place->results as $result )
						$html .= $this->get_place_badge_html(
							$result->place_id, $places);

					return $html;
				}

				if( $options[ 'placeId' ] !== null ) {

					return $this->get_place_badge_html( $options[ 'placeId' ],
						$places );
				}

				return 'No query or placeId attributes specified on shortcode.';

			} catch( \Exception $e ) {

				return $e->getMessage();
			}
		}

		/**
		 * Renders the badge HTML for specific Google Place.
		 *
		 * @param string $place The unique id of the place in Google Places.
		 * @param \RWC\Google\Places $places The Places API wrapper.
		 *
		 * @return string Returns an HTML string for the badge.
		 */
		private function get_place_badge_html( $placeId, $places ) {

			// Store the HTML in a transient so we don't need to do this often.
			$transient = 'places_badge_html_' . $placeId;
			$html      = get_transient( $transient );

			// If transient has value, use it.
			if( $html !== false ) return $html;

			$places = $places->getDetails( $placeId );

			if( count( $places->result ) != 1 ) {

				return sprintf( "No Google Places found matching placeId %s",
					$placeId );
			}

			$name = $places->result->name;
			$url = $places->result->website;
			$city = array_reduce( $places->result->address_components, function( $c, $i ) {

				if( in_array( 'locality', $i->types ) ) {
					$c = $i->long_name;
				}

				return $c;
			});

			$state = array_reduce( $places->result->address_components, function( $c, $i ) {

				if( in_array( 'administrative_area_level_1', $i->types ) ) {
					$c = $i->short_name;
				}

				return $c;
			});

			$zipcode = array_reduce( $places->result->address_components, function( $c, $i ) {

				if( in_array( 'postal_code', $i->types ) ) {
					$c = $i->short_name;
				}

				return $c;
			});

			$country = array_reduce( $places->result->address_components, function( $c, $i ) {

				if( in_array( 'country', $i->types ) ) {
					$c = $i->short_name;
				}

				return $c;
			});
			$phone = $places->result->formatted_phone_number;
			$rating = $places->result->rating;
			$reviewCount = count( $places->result->reviews );

			$html = \RWC\Utility::get_include_content( '/google/local-business-badge.php', [
				'name' => $name,
				'url' => $url,
				'pobox' => null,
				'city' => $city,
				'state' => $state,
				'zipcode' => $zipcode,
				'country' => $country,
				'tel' => $phone,
				'fax' => null,
				'email' => null,
				'rating' => $rating,
				'reviewCount' => $reviewCount,
				'maxRating' => 5
			] );

			set_transient( $transient, $html, 60 * 60 ); // One Hour
			return $html;
		}

        /**
         * Render the current year.
         *
         * @return string Renders the current year.
         */
        public function render_the_year() {

            return date( 'Y' );
        }
	}
}
