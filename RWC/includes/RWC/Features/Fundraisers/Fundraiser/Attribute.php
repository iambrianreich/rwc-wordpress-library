<?php

/**
 * This file contains the RWC\Features\Fundraisers\Product class.
 *
 * @author Brian Reich <breich@reich-consulting.net>
 * @copyright Copyright (C) 2017 Reich Web Consulting
 * @version 1.0
 * @package RWC\Features
 * @subpackage Fundraiser
 */

namespace RWC\Features\Fundraisers\Fundraiser {

    /**
     * An Attribute assocaited with a Fundraiser Product.
     *
     * Every Fundraiser has a set of Products associated with it, and the
     * attributes associated with those products can be used to customize the
     * product price, and other product features based on the Fundraiser through
     * which the product is being purchased. The Attribute class provides
     * insight into all of the attributes associated with a particular
     * FundraiserProduct and Fundraiser and can be used to calculate prices
     * display options, etc.
     *
     * @author Brian Reich <breich@reich-consulting.net>
     * @copyright Copyright (C) 2017 Reich Web Consulting
     * @version 1.0
     * @package RWC\Features
     * @subpackage Fundraiser
     */
    class Attribute extends \RWC\Object
    {
        /**
         * Creates a new Attribute instance.
         *
         * The constructor accepts an array of options which may contain the
         * following values.  The "name" option specifies the name of the
         * attribute. The "options" option specifies an array of attribute
         * option data that has been assigned to the Attribute for the
         * specific Fundraiser.
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
                'name' => 'attribute_name',
                'options' => array()
            );
        }

        /**
         * Returns a list of AttibuteValues for the Attribute.
         *
         * Each Attribute will have a set of values associated with it that a
         * customer can choose from. This method will return an array of all
         * attribute values. Keep in mind that some may be disabled for a
         * particular fundraiser. To return only enabled options, use the
         * get_enabled_attribute_options() method.
         *
         * @return array Returns an array of AttributeValues.
         * @see Attribute::get_enabled_attribute_options()
         */
        public function get_attribute_options()
        {
            // Get the raw option values.
            $optionList = $this->get_option( 'options', array() );

            // Build a list of AttributeValues
            $optionValues = [];
            array_walk( $optionList, function( $options, $name )
              use ( & $optionValues ) {

                  $options[ 'name' ] = $name ;
                  $optionValues[] = new AttributeValue( $options );
              });

              return $optionValues;
        }

        /**
         * Returns a list of enabled AttibuteValues for the Attribute.
         *
         * Each Attribute will have a set of values associated with it that a
         * customer can choose from. This method will return an array of all
         * attribute values that have been set to enabled for the fundraiser.
         *
         * @return array Returns an array of AttributeValues.
         */
        public function get_enabled_attribute_options()
        {
            // Get all attributes
            $optionList = $this->get_attribute_options();

            // Filter out disabled
            $optionValues = [];
            array_walk( $optionList, function( $option)
              use( & $optionValues ) {

                  if( $option->is_enabled() ) $optionValues[] = $option;
            });

            return $optionValues;
        }

        /**
         * Returns the name for the attribute's form field.
         *
         * @return string Returns the name of the attribute's form field.
         */
        public function get_field_name()
        {
            return $this->get_option( 'name' ) ;
        }

        /**
         * Returns the friendly name to display in the attribute's field label.
         *
         * @return string Returns the friendly name to display in the field label.
         */
        public function get_field_label()
        {
            return wc_attribute_label( $this->get_option( 'name' ),
                $this->get_product() );
        }
    }
}
