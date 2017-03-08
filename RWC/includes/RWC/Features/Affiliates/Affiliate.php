<?php

namespace RWC\Features\Affiliates {

    abstract class Affiliate extends \RWC\Object {

        /**
         * Dynamically updates shortcode attributes for an Affiliate Product
         * from the affiliate website's live data.
         *
         * @param array $atts The shortcode attributes.
         *
         * @return array Returns the modified shortcode attributes.
         */
        public abstract function dynamic_update( $atts = array() );
    }
}
