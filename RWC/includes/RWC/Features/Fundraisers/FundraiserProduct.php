<?php

/**
 * This file contains the WC_Product_Fundraiser_Product class.
 *
 * @author Brian Reich <breich@reich-consulting.net>
 * @copyright Copyright (C) 2017 Reich Web Consulting
 * @version 1.0
 * @package RWC\Features
 * @subpackage Fundraiser
 */

 /**
  * A WooCommerce Product Type for Products associated with Fundraisers.
  *
  * A Fundraiser Product inherits all of the same features as as WooCommerce
  * Simple product.  When purchased from within a Fundraiser, the Product will
  * automatically be customizable based on the Product's configured attributes
  * which have been enabled for the Fundraiser.
  *
  * @author Brian Reich <breich@reich-consulting.net>
  * @copyright Copyright (C) 2017 Reich Web Consulting
  * @version 1.0
  * @package RWC\Features
  * @subpackage Fundraiser
  */
class WC_Product_Fundraiser_Product extends WC_Product_Simple {

    /**
     * The Fundraiser being browsed.
     *
     * @var    \RWC\Features\Fundraisers\Fundraiser
     * @access private
     */
    private $fundraiser = null;

    /**
     * Creates a new Fundraiser
     *
     * @param int $product The unique id of the WooCommerce product
     *
     * @constructor
     */
    public function __construct( $product ) {

        parent::__construct( $product );
    }

    /**
     * Get internal type.
     *
     * @return string
     */
    public function get_type() {

        return 'fundraiser_product';
    }

    /**
     * Returns the price HTML for the FundraiserProduct.
     *
     * Much like a Variable Product, a FundraiserProduct's price varies based
     * on the selected options. The get_price_html() method will analyze the
     * options associated with the product in the current Fundraiser and
     * generate price HTML that displays the minimum and maximum price range.
     *
     * @param string $deprecated No idea what this does.
     *
     * @return string Returns the price HTML for the FundRaiserProduct.
     */
    public function get_price_html( $deprecated = '' )
    {
        $min = $this->get_min_price();
        $max = $this->get_max_price();

        if( $min == $max ) return wc_price( $min );

        return wc_price( $min ) . ' - ' . wc_price( $max );
    }

    /**
     * Returns the minimum price of the FundraiserProduct.
     *
     * Returns the minimum price of the FundraiserProduct. The minimum price is
     * determined by iterating through all options associated with the product
     * for the configured Fundraiser and selecting the lowest-possible
     * combination of options.
     *
     * If no Fundraiser is configured, the default price will be used.
     *
     * @return float Returns the minimum price.
     */
    public function get_min_price()
    {
        $base = $this->get_base_price();

        $min = $base;

        $fundraiser = $this->get_fundraiser();

        // If no fundraiser is set then we can't calculate the price.
        if( $fundraiser == null ) return $base;

        //  Get the mapping between the Fundraiser and Product
        $fundraisingProduct = $fundraiser->get_enabled_product( $this );

        // If no mapping, that means that the product is not enabled in the
        // fundraiser.
        if( $fundraisingProduct == null ) return $base;

        // Drop in maximum cost of customizations.
        array_walk( $fundraisingProduct->get_options(), function( $attribute )
          use ( & $base, & $max ) {

            $minOption = PHP_INT_MAX;

            array_walk( $attribute->get_enabled_attribute_options(), function( $option )
              use ( & $base, & $maxOption ) {

                  // Store the highest value for this option.
                  $minOption = min( $minOption,
                    $option->get_price_differential() );
            });

            // Add the maximum option price.
            $min += $minOption;
        });

        // Don't factor in customizations.

        return $min;
    }

    /**
     * Returns the maximum price of the FundraiserProduct.
     *
     * Returns the maximum price of the FundraiserProduct. The maximum price is
     * determined by iterating through all options associated with the product
     * for the configured Fundraiser and selecting the highest-possible
     * combination of options.
     *
     * If no Fundraiser is configured, the default price will be used.
     *
     * @return float Returns the minimum price.
     */
    public function get_max_price()
    {
        $base = $this->get_base_price();

        $max = $base;

        $fundraiser = $this->get_fundraiser();

        // If no fundraiser is set then we can't calculate the price.
        if( $fundraiser == null ) return $base;

        $fundraisingProduct = $fundraiser->get_enabled_product( $this );

        // If no mapping, that means that the product is not enabled in the
        // fundraiser.
        if( $fundraisingProduct == null ) return $base;

        // Drop in maximum cost of customizations.
        array_walk( $fundraisingProduct->get_options(), function( $attribute )
          use ( & $base, & $max ) {

            $maxOption = 0;
            array_walk( $attribute->get_enabled_attribute_options(), function( $option )
              use ( & $base, & $maxOption ) {

                  // Store the highest value for this option.
                  $maxOption = max( $maxOption,
                    $option->get_price_differential() );
            });

            // Add the maximum option price.
            $max += $maxOption;
        });

        // Drop in maximum cost of customizations.
        array_walk( $fundraisingProduct->get_customizations(),
          function( $customization ) use ( & $base, & $max ) {

            // Add the price of the customization.
            $max += $customization->get_price_differential();
        });

        return $max;
    }

    /**
     * Returns the base price of the FundraiserProduct.
     *
     * Returns the base price of the FundraiserProduct. The base price is
     * determined by checking to see if the product has a base price set for
     * the current Fundraiser. If not, the price set on the WooCommerce Product
     * is used.
     *
     * If no Fundraiser is configured, the default price will be used.
     *
     * @return float Returns the minimum price.
     */
    public function get_base_price()
    {
        $base = $this->get_price();

        $fundraiser = $this->get_fundraiser();

        // If no fundraiser is set then we can't calculate the price.
        if( $fundraiser == null ) return $base;

        //  Get the mapping between the Fundraiser and Product
        $fundraisingProduct = $fundraiser->get_enabled_product( $this );

        // If no mapping, that means that the product is not enabled in the
        // fundraiser.
        if( $fundraisingProduct == null ) return $base;


        // If the base price is not null, use it. Otherwise just return the
        // original base price.
        if( $fundraisingProduct->get_base_price() != null ) {

            $base = $fundraisingProduct->get_base_price();
        }

        return $base;
    }

    public function set_fundraiser( \RWC\Features\Fundraisers\Fundraiser $fundraiser ) {

        $this->fundraiser = $fundraiser;
    }

    /**
     * Returns the fundraiser associated with the Product.
     *
     * Returns the Fundraiser associated with the product. The Fundraiser should
     * automatically be set by the Fundraisers feature directly after the
     * Product is created and registered in the global scope.
     *
     * @return \RWC\Features\Fundraisers\Fundraiser Returns the fundraiser.
     */
    public function get_fundraiser()
    {
        return $this->fundraiser;
    }

    /**
     * Returns the adjusted price for the Fundraiser Product.
     *
     * The price is adjusted based on the options selected when the Product was
     * added to the cart. The method will iterate through the selected product
     * options and apply the price differential for the selected options to the
     * base price.
     *
     * @param array $cart_item The cart data used to calculate the price.
     *
     * @return float Returns the adjusted item price.
     */
    public function get_adjusted_price( $cart_item )
    {
        // Get the base price.
        $base = $this->get_price();

        // Get the Fundraiser\Product which describes how this product is
        // configured for the user's fundraiser.
        $fundraisingProduct = $this->get_fundraiser()
            ->get_enabled_product( $this );

		// The cart item is not in the fundraiser! Note that we're returning
		// zero, but this shouldn't matter because all items will be removed
		// from the cart when switching fundraisers.
		if ($fundraisingProduct == null) {
			return 0;
		}
		
        if( $fundraisingProduct->get_base_price() ) {
            $base = $fundraisingProduct->get_base_price();
        }

        // Walk through all configured attributes.
        array_walk( $fundraisingProduct->get_options(), function( $attribute )
          use ( & $base, $cart_item ) {

            // Walk through all options for the current attribute.
            array_walk( $attribute->get_enabled_attribute_options(), function( $option )
              use ( & $base, $attribute, $cart_item ) {

                // Get the field name.
                $name = $attribute->get_field_name();

                // Check the field name against the cart item's data.
                if( array_key_exists( $name, $cart_item[ 'fundraiser' ] ) ) {

                    // If the cart item's data contains a value for the field,
                    // Use that value to add a price differential.
                    if( $cart_item[ 'fundraiser'][ $name ] == $option->get_value() ) {
                        $base += $option->get_price_differential();
                    }
                }
            });
        });

        // Walk through all configured customizations.
        array_walk( $fundraisingProduct->get_customizations(), function( $customization )
          use ( & $base, $cart_item ) {

            // Get the field name.
            $name = $customization->get_field_name();

            // Check the field name against the cart item's data.
            if( array_key_exists( $name, $cart_item[ 'fundraiser' ]) ) {

                // If the customization has a value, apply the price
                // differential.
                if( ! empty ( $cart_item['fundraiser' ][ $name ] ) ) {
                    $base += $customization->get_price_differential();
                }
            }
        });

        // Reset the price to the recalculated price.
        return $base;
    }

    /**
     * Updates the price of this FundraiserProduct based on item selections.
     *
     * This method is used to update the price of a FundraiserProduct in the
     * shopping cart based on attribute selections and other customizations. The
     * cart_item parameter will be analyzed to check for attribute values that
     * were selected when the Product was added to the cart. The attribute
     * configuration for the Fundraiser will be used add the price differentials
     * for the configuration attribute values.
     *
     * @param array $cart_item The FundraiserProduct in the cart.
     *
     * @return void
     */
    public function update_cart_price_from_fundraiser( $cart_item )
    {
        // Reset the price to the recalculated price.
        $this->set_price( $this->get_adjusted_price( $cart_item ) );
    }
}
