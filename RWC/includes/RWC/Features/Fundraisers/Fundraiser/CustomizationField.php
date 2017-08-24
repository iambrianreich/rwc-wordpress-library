<?php

/**
 * This file contains the RWC\Features\Fundraisers\Fundraiser\CustomizationField
 * class.
 *
 * @author Brian Reich <breich@reich-consulting.net>
 * @copyright Copyright (C) 2017 Reich Web Consulting
 * @version 1.0
 * @package RWC\Features
 * @subpackage Fundraiser
 */

namespace RWC\Features\Fundraisers\Fundraiser {

    /**
     * A Customization Field for a Fundraiser Product.
     *
     * A Customization Field allows the user setting up a Fundraiser to add
     * custom fields to a fundraising product that can provide customization
     * instructions and update the item's price by a specified differential
     * when the field is included.
     *
     * @author Brian Reich <breich@reich-consulting.net>
     * @copyright Copyright (C) 2017 Reich Web Consulting
     * @version 1.0
     * @package RWC\Features
     * @subpackage Fundraiser
     */
    class CustomizationField extends \RWC\Object
    {
        /**
         * Creates a new CustomizationField instance.
         *
         * The constructor accepts the following options. The "name" field
         * specifies the name of the customization field. The the "description"
         * option provides a description of the field that tells the user what
         * sort of data to input. The "price" option specifies a numeric price
         * differential that applies to the base price when the field is given
         * a value.
         *
         * @constructor
         */
        public function  __construct( $options = array() ) {

            parent::__construct( $options );
        }

        /**
         * Sets the WC_Product associated with this Attribute.
         *
         * Setting the Product associated with the Attribute allows for the
         * attribute's methods to provide more detailed information about the
         * attribute, such as the display name for the attribute for that
         * specific Product.  If a Product is not set, then the ugly internal
         * name of the attribute is used.
         *
         * @param \WC_Product $product The associated Product.
         *
         * @return void
         */
        public function set_product( \WC_Product $product )
        {
            $this->set_option( 'product', $product );
        }

        /**
         * Returns the associated Product.
         *
         * If no Product has been associated either via the constructor or via
         * the set_product() method, then the current global $product instance
         * will be used, if one is set. If a Product cannot be retrieved through
         * either method, null is returned.
         *
         * @return \WC_Product|null Returns the associated Product, or null.
         */
        public function get_product()
        {
            // Use as a backup.
            global $product;

            // Pull the Product from the options list.
            $theProduct = $this->get_option( 'product', null);

            // If there was not a product in the options list use the global
            // product.
            if($theProduct == null)
            {
                $this->set_option( 'product', $product );
                $theProduct = $product;
            }

            return $theProduct;
        }

        /**
         * Returns an array of default options.
         *
         * @return array Returns the default options.
         */
        public function get_default_options() {

            return array(
                'name' => 'Custom Field Name',
                'description' => 'Custom Field Description',
                'price' => 0
            );
        }

        /**
         * Returns the name of the CustomizationField.
         *
         * @return string Returns the name of the CustomizationField.
         */
        public function get_name()
        {
            return $this->get_option( 'name' );
        }

        /**
         * Returns the name of the field ready to display in HTML.
         *
         * @return string Returns the friendly CustomiztionField name.
         */
        public function get_friendly_name()
        {
            return esc_html( $this->get_name() );
        }

        /**
         * Returns the CustomizationField's field name (for use in an input).
         *
         * @return string Returns the CustomizationField field name.
         */
        public function get_field_name()
        {
            return 'customization-' . str_replace(' ', '-', strtolower(
                $this->get_name() ) );
        }

        /**
         * Returns the description of the field ready to insert into HTML.
         *
         * @return string Returns the field ready to insert into HTML.
         */
        public function get_friendly_description()
        {
            return esc_html( $this->get_option( 'description' ) );
        }

        /**
         * Returns the price differential of the CustomzationField.
         *
         * @return float Returns the price differential.
         */
        public function get_price_differential()
        {
            return number_format_i18n (
                floatval( $this->get_option( 'price' ) ), 2 );
        }

        public function get_friendly_price_differential()
        {
            $price = $this->get_price_differential();

            if( $price == 0 ) {
                return $price;
            } elseif ($price > 0 ) {
                return '+' . wc_price( $price );
            } else {
                return '-' . wc_price( $price );
            }
        }
    }
}
