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

            $result = wp_remote_get( $url );

            if( ! is_array( $result ) ) {
                throw new Exception( 'HTTP Get request for affiliate URL ' .
                    $url . ' failed.');
            }

            $body = $response[ 'body' ];

            $dom = new DOMDocument( $body );
            $xpath = new DOMXPath( $dom );


            $this->update_price( $xpath, $atts );

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
