<?php

/**
 * Contains the RWC\Features\ContentSecurityPolicy class
 *
 * @author Brian Reich <breich@reich-consulting.net>
 * @copyright Copyright (C) 2016 Reich Consulting
 * @package RWC\Features
 */

namespace RWC\Features {

    /**
	 * The RWC\Feature\ContentSecurityPolicy Feature will render CSP headers
     * to the browser which provide specify from where it can safely download
     * and execute external resources. Providing strong CSP rules will help
     * prevent XSS attacks against your site. There are several options
     * available in the CSP Feature. The "unsafe-inline" option can be used to
     * output the "unsafe-inline" rule, which allows for execution of inline
     * styles. This rule essentially negates the protection of
     * provided by a CSP rule, but does make it compatible with sites that
     * require inline styles. The "unsafe-eval" option can be used to output
     * the "unsafe-eval" rule, which allows for execution of inline scripts. The
     * "img-src" rule can be used to specify valid locations for images (default
     * is "self").
     *
     * Example configuration:
     * array(
     *     'unsafe-inline' => true,
     *     'unsafe-eval'   => true,
     *     'img-src'       => 'self'
     * )
	 *
	 * @author Brian Reich <breich@reich-consulting.net>
	 * @copyright Copyright (C) 2016 Reich Consulting
	 * @package RWC\Features
	 */
    class ContentSecurityPolicy extends \RWC\Feature {


        /**
		 * Returns an array of dependancies that loads the GoogleMaps Feature.
		 *
		 * @return array Returns an array of dependancies.
		 */
		public function get_dependancies() {

			return array( );
		}

        /**
         * Returns default ContentSecurityPolicy options.
         *
         * @return array Returns default ContentSecurityPolicy options.
         */
        public function get_default_options( $options = array() ) {

            return array(
                'unsafe-inline' => false,
                'unsafe-eval' => false,
                'img-src' => 'self'
            );
        }

        /**
		 * Initializes theContentSecurityPolicy feature.
		 *
		 * @return void
		 */
		public function initialize() {

            add_action( 'send_headers', array( $this, 'send_csp_header' ) );
        }

        /**
         * Sends the CSP header to the client.
         *
         * @return void
         */
        public function send_csp_header() {

            $csp = 'Content-Security-Policy: default-src https:' .
                ($this->_options[ 'unsafe-inline' ] ? " 'unsafe-inline' " : '') .

                // unsafe-eval: Allow evalucation of inline JS.
                ( $this->_options[ 'unsafe-eval' ] ? " 'unsafe-eval' " : '' ) .

                // img-src: Specifies where images can come from.
                ( $this->_options[ 'img-src' ] ? sprintf( "; img-src %s ",
                    $this->_options[ 'img-src' ] ) : '' );

            header( $csp );
        }
    }

}
