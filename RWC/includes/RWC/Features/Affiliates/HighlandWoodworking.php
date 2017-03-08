<?php

namespace RWC\Features\Affiliates {

    class HighlandWoodworking extends Affiliate {

        /**
         * Dyamically updates the details of a Highland Woodworking affiliate
         * block with data from the affiliate website.
         *
         * @param array $atts The array of affiliate product data.
         *
         * @return array Returns the updated affiliate product data.
         */
        public function dynamic_update( $atts = array() ) {

            // Make sure a URL was specified.
            if( ! isset( $atts[ 'url' ] ) ) {

                $atts[ 'dynamic_update_error' ] = 'No URL specified';
                return $atts;
            }

            // Get URL from affiliate site and request it.
            $url = $atts[ 'url' ];

            // If this data has already been cached in a transient, don't go to
            // the original source to update it.
            $transientName = 'affiliate_data_' . $url;
            $transient = get_transient( $transientName );

            // Return the transient if it's available.
            if( $transient ) return $transient;

            $result = wp_remote_get( $url );

            // If HTTP request failed, return dyanmic update error.
            if( ! is_array( $result ) ) {
                $atts[ 'dynamic_update_error' ] = sprintf( 'Failed to ' .
                    'retrieve affiliate URL %s', $url );
                return $atts;
            }

            $body = $result[ 'body' ];

            $dom = new \DOMDocument();

            // Load HTML, suppress errors.
            libxml_use_internal_errors(true);
            $dom->loadHTML( $body );
            libxml_clear_errors();

            $xpath = new \DOMXPath( $dom );


            $this->update_price( $xpath, $atts );

            // Set the transient data to expire in 10 minutes.
            set_transient( $transientName, $atts, 10 * 60 );

            // Return updated attributes.
            return $atts;
        }

        private function update_price( $xpath, & $atts = array() ) {

            // Grab price from Highland Woodworking markup
            $price = $xpath->query( "//span[contains( @class, " .
                "'prod-detail-cost-value')]" );

            // If there is no price in the HTML, record the problem.
            if( $price->length == 0 ) {

                $atts[ 'dynamic_update_error' ] =
                    'Could not locate a price in the affiliate page markup.';

                return;
            }

            // Store price, store timestamp it was updated.
            $atts[ 'price' ] =  str_replace( '$', '', $price[0]->nodeValue);
            $atts[ 'price_timestamp' ] = time();
        }
    }
}
