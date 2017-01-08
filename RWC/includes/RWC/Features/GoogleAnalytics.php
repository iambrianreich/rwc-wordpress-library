<?php

/**
 * Contains the RWC\Features\RealEstate class.
 *
 * @author Brian Reich <breich@reich-consulting.net>
 * @copyright Copyright (C) 2016 Reich Consulting
 * @package RWC\Features
 */

namespace RWC\Features {

    /**
	 * The RWC\Feature\RealEstate plugin provides features for Real Estate sites
     * that will register a custom post type for Real Estate properties, as well
     * as all of the appropriate administrative infrastructure for assigning
     * real estate data to them.
	 *
	 * @author Brian Reich <breich@reich-consulting.net>
	 * @copyright Copyright (C) 2016 Reich Consulting
	 * @package FC_Client
	 */
    class RealEstate extends \RWC\Feature {

        /**
		 * Initializes the RealEstate Feature.
		 *
		 * @return void
		 */
		public function initialize() {

            // Only do stuff if a tracking code is configured.
            if( $this->has_tracking_code() ) {
                add_action( 'wp_head', array( $this, 'add_tracking_code' ), 999 );
            }
        }

        /**
         * Returns true if a tracking code has been configured.
         *
         * @return bool Returns true if a tracking code has been configured.
         */
        public function has_tracking_code() {

            return ( $this->get_tracking_code() !== false );
        }

        /**
         * Returns the configured tracking code or false if no tracking code
         * has been configured.
         *
         * @return string Returns the tracking code, or false if none.
         */
        public function get_tracking_code() {

            return $this->get_option( 'tracking_code', false );
        }

        /**
         * Adds the Google Tracking code JavaScript.
         *
         * @return void
         */
        public function add_tracking_code() {

            if( ! $this->has_tracking_code() ) return;
            ?>
            <script>
              (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
              (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
              m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
              })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

              ga('create', '<?php esc_attr( $this->get_tracking_code() ); ?>', 'auto');
              ga('send', 'pageview');

            </script>
        <? }
    }

}
