<?php

/**
 * This file contains the RWC\Features\Fundraisers\Fundraiser\Product class.
 *
 * @author Brian Reich <breich@reich-consulting.net>
 * @copyright Copyright (C) 2017 Reich Web Consulting
 * @version 1.0
 * @package RWC\Features
 * @subpackage Fundraiser
 */

namespace RWC\Features\Fundraisers\Fundraiser {

    /**
     * Maps a Fundraiser onto a WooCommerce product.
     *
     * The Fundraiser\Product class maps a Fundraiser to a WooCommerce product
     * and provides access to the custom setting for that product based on the
     * selected Fundraiser.
     *
     * @author Brian Reich <breich@reich-consulting.net>
     * @copyright Copyright (C) 2017 Reich Web Consulting
     * @version 1.0
     * @package RWC\Features
     * @subpackage Fundraiser
     */
    class Product extends \RWC\Object {

        /**
         * Creates a new Product with the specified options.
         *
         * Creates a new Product with the specified options. The $id parameter
         * specifies the unique id of the WooCommerce product. The $options
         * array can contain the following options. The "enabled" option
         * specifies that the Product is enabled for the Fundraiser. The "id"
         * parameter specifies the unique id of the WooCommerce Product, if it
         * was not passed as the first parameter. The "options" option specifies
         * an array of Attribute data for the WooCommerce product when it's
         * being sold through a Fundraiser. The "customizations" option
         * specifies an array of customization field data for the WooCommerce
         * product when it's being sold through a Fundraiser.
         *
         * @param int   $id      The unique id of the WooCommerce product.
         * @param array $options The array of Attribute data.
         *
         * @constructor
         */
        public function __construct( $id, $options = array() )
        {
                $options[ 'id' ] = $id;
                parent::__construct( $options );
        }

        /**
         * Returns an array of default configuration options.
         *
         * @return array Returns an array of default configuration options.
         */
        public function get_default_options()
        {
            return array(
                'enabled' => false,
                'id' => $id,
                'options' => array(),
                'customizations' => array()
            );
        }

        /**
         * Returns true if the Product is enabled.
         *
         * @return bool Returns true if the Product is enabled.
         */
        public function get_enabled() {

            return filter_var( $this->get_option( 'enabled', false ),
                FILTER_VALIDATE_BOOLEAN );
        }

        /**
         * Returns true if the Product is enabled (synonym for get_enabled).
         *
         * @return bool Returns true if the Product is enabled.
         */
        public function is_enabled() {

            return $this->get_enabled();
        }

        /**
         * Returns the ID of the WooCommerce Product.
         *
         * @return int Returns the ID of the WooCommerce Product.
         */
        public function get_id() {

            return $this->get_option( 'id' );
        }

        /**
         * Returns a list of options associated with this Product.
         *
         * @return array Returns an array of Options.
         * @deprecated
         */
        public function get_options() {

            return $this->get_attributes();
        }

        /**
         * Returns the base price of the Product.
         *
         * A Product can have a custom base price specified when it is being
         * sold as part of a fundraiser. This method will return that base
         * price. If none is specified, then null is returned and the base
         * price of the product setup in WooCommerce should be used.
         *
         * @return float|null Returns the base price of the product.
         */
        public function get_base_price() {

            return $this->get_option( 'basePrice', null );
        }

        /**
         * Returns a list of options associated with this Product.
         *
         * @return array Returns an array of Options.
         */
        public function get_attributes()
        {
            $options = $this->get_option( 'options' );

            $productOptions = [];

            array_walk( $options, function( $options, $name )
              use ( & $productOptions ){

                $productOptions[] = new Attribute( array(
                    'name' => $name,
                    'options' => $options
                ) );
            });

            return $productOptions;
        }

        /**
         * Returns a list of customizations.
         *
         * @return array Returns a list of Customization fields.
         */
        public function get_customizations()
        {
            $customizationsConfig = $this->get_option( 'customizations' );

            $customizations = [];

            array_walk( $customizationsConfig, function( $customization )
              use ( & $customizations ) {

                $c = new \RWC\Features\Fundraisers\Fundraiser\
                    CustomizationField( $customization );

                if( ! empty( $c->get_name() ) )
                {
                    $customizations[] = $c;
                }
            });

            return $customizations;
        }
    }
}
