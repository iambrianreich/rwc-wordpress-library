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

namespace RWC\Features\Fundraisers\Fundraiser
{
    /**
     * An Attribute value assocaited with a Fundraiser Product.
     *
     * Every Fundraiser has a set of Products associated with it, and the
     * attributes associated with those products can be used to customize the
     * product price, and other product features based on the Fundraiser through
     * which the product is being purchased. Each attribute has a set of values
     * which can be used to customize the product and adjust the price. The
     * AttributeValue class represents a mapping of an attribute value to a
     * Fundraiser, and specifies various customizations that apply to the value
     * such as whether or not it is enabled for a particular fundraiser, and
     * how much the price gets adjusted when it is selected.
     *
     * @author Brian Reich <breich@reich-consulting.net>
     * @copyright Copyright (C) 2017 Reich Web Consulting
     * @version 1.0
     * @package RWC\Features
     * @subpackage Fundraiser
     */
    class AttributeValue extends \RWC\Object
    {
        /**
         * Creates a new AttributeValue with the specified options.
         *
         * The AttributeValue class supports the following options. The "name"
         * option specifies the name of the option, which is the value that
         * the user selects in order to add the item to the cart. The "enabled"
         * field specifies whether or not the AttributeValue is enabled for the
         * current Product and Fundraiser combination. The "isDefault" option
         * specifies whether the option is the default selected option in form
         * fields rendered for the Attribute.
         *
         * @param array $options A list of AttributeValue options.
         */
        public function  __construct( $options = array() ) {

            parent::__construct( $options );
        }

        /**
         * Returns a list of default options for AttributeValue objects.
         *
         * @return array Returns a list of default options.
         */
        public function get_default_options() {

            return array(
                'name' => 'attribute_value_name',
                'enabled' => false,
                'isDefault' => false
            );

        }

        /**
         * Returns the name of the AttributeValue.
         *
         * @return string Returns the name of the AttributeValue
         */
        public function get_name() {

            return esc_html( $this->get_option( 'name' ) );
        }

        /**
         * Returns the friendly name of the AttributeValue.
         *
         * The friendly name of the AttributeValue is the string that can be
         * displayed in form fields for this value. It will be the name of the
         * field with the price differential added to the end.
         *
         * @return string Returns the friendly name of the AttributeValue.
         */
        public function get_friendly_name() {

            $price = $this->get_friendly_price_differential();

            return esc_html( $this->get_option( 'name' ) ) . ( empty( $price ) ?
                '' : ' (' . $price . ')');
        }

        /**
         * Returns the value of the AttributeValue.
         *
         * The value for the AttributeValue is the string that can be used to
         * set the value in form fields rendered for the Attribute. This will
         * be the same as the name of the AttributeValue.
         *
         * @return string Returns the value of the AttributeValue.
         */
        public function get_value()
        {

                return esc_attr( $this->get_option( 'name' ) );
        }

        /**
         * Returns true if the AttributeValue is enabled for the Fundraiser.
         *
         * @return bool Returns true if the AttributeValue is enabled.
         */
        public function get_enabled()
        {

            return filter_var( $this->get_option( 'enabled', false ),
                FILTER_VALIDATE_BOOLEAN );
        }

        /**
         * Returns true if the AttributeValue is enabled for the Fundraiser.
         *
         * @return bool Returns true if the AttributeValue is enabled.
         */
        public function is_enabled() {

            return $this->get_enabled();
        }

        /**
         * Returns true if the AttributeValue is the default selection.
         *
         * When a form field is rendered for Attributes containing this
         * AttributeValue, get_default() can be used to determine if this
         * value should be the default selection.
         *
         * @return bool Returns true if the AttributeValue is the default selection.
         */
        public function get_default()
        {
            return filter_var( $this->get_option( 'isDefault', false ),
                FILTER_VALIDATE_BOOLEAN );
        }

        /**
         * Returns true if the AttributeValue is the default selection.
         *
         * When a form field is rendered for Attributes containing this
         * AttributeValue, get_default() can be used to determine if this
         * value should be the default selection.
         *
         * @return bool Returns true if the AttributeValue is the default selection.
         */
        public function is_default()
        {
            return $this->get_default();
        }

        /**
         * Returns the price differential for the AttributeValue.
         *
         * When this AttributeValue is selected, the specified price
         * differential will be added to the base price of the product.
         *
         * @return float Returns the price differential for the AttributeValue.
         */
        public function get_price_differential()
        {

            return filter_var( $this->get_option( 'optionPrice', 0 ),
                FILTER_VALIDATE_FLOAT );
        }

        /**
         * Returns the friendly version of the price differential for display.
         *
         * The price differential is a floating-point value which can be used
         * to do price calculation. This method will format it has currency and
         * add a "+/-" sign to show that the differential is an increase or a
         * decrease in the base price.
         *
         * @return float Returns the price differential for the AttributeValue.
         */
        public function get_friendly_price_differential()
        {

            $price = $this->get_price_differential();
            $friendlyPrice = wc_price( $price );

            if( $price > 0 ) {
                $friendlyPrice = '+' . $friendlyPrice;
            } else if ( $price == 0 ) {
                $friendlyPrice = '';
            }

            return $friendlyPrice;
        }
    }
}
