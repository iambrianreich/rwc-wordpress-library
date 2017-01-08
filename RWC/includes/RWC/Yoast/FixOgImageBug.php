<?php

/**
 * A theme helper that gets around the bug with serving og:image images over
 * https://.
 *
 * @author Brian Reich <breich@reich-consulting.net>
 * @copyright Copyright (C) 2016 Reich Consulting
 * @package RWC_Yoast
 */

/**
 * A theme helper that gets around the bug with serving og:image images over
 * https://.
 *
 * @author Brian Reich <breich@reich-consulting.net>
 * @copyright Copyright (C) 2016 Reich Consulting
 * @package RWC_Yoast
 */
class RWC_Yoast_FixOgImageBug {

	/**
	 * Registers the Yoast og:image filter.
	 *
	 * @constructor
	 */
	public function __construct() {

		add_filter( 'wpseo_og_og_image', array( $this, 'rewrite_to_http' ), 10, 1);
	}

	/**
	 * Replace https urls with http.
	 *
	 * @param string $url The image URL.
	 *
	 * @return string Returns the URL with the scheme forced to http.
	 */
	public function rewrite_to_http( $url ) {

		return preg_replace( '/^https:\/\/(.+)$/', 'http://$1', $url );
	}
}
