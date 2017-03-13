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
