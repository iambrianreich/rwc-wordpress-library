<?php

/**
 * This file contains the RWC\Feature\Fundraiser\WooCommerceHooks class.
 *
 * @author Brian Reich <breich@reich-consulting.net>
 * @copyright Copyright (C) 2017 Reich Web Consulting
 * @version 1.0
 * @package RWC\Features
 */

namespace RWC\Features\Fundraisers {

	class WooCommerceHooks extends \RWC\Object {

		////////////////////// PUBLIC FUNCTIONS ////////////////////////////////

		/**
		 * Loads all WooCommerce hooks for the Fundraiser feature.
		 *
		 * The options array must specify the "fundraiser" option at minimum,
		 * which provides a reference to the Fundraiser Feature.
		 *
		 * @param array $options An array of config options.
		 *
		 * @throws Exception if no WooCommerce or Fundraiser feature.
		 */
		public function __construct( $options = array() )
		{
			parent::__construct( $options );

			// Make sure the caller set the feature.
			if( $this->get_feature() == null ) {

				throw new Exception( 'Fundraiser feature has not been set.' );
			}
			// Make sure WooCommerce is installed.
			if( $this->is_woocommerce_installed() == false )
			{
			   throw new Exception( 'Woocommerce must be ' .
				   'active in order to use the Fundraisers feature.' );
			}
			
			$this->initialize();
		}

		/**
         * Generates HTML for product option fields.
         *
         * The render_add_to_cart_options() method will generate a table
         * containing dropdown lists of all attributes and values enabled for
         * the current product based on the Fundraiser that the user has
         * selected. The generated HTML is based on the HTML generated for
         * Variable products in the WooCommerce core.
         *
         * @return void
         */
        public function render_add_to_cart_options( $renderProduct = null ) {

            global $product;

            // Only render for Fundraiser products.
            if( ! $product instanceof \WC_Product_Fundraiser_Product ) return;

            if( empty( $renderProduct ) ) {
                $renderProduct = $product;
            }

            echo \RWC\Utility::get_include_content(
                '/features/fundraisers/product-attribute-options.php', array(

                    'product' => $renderProduct,
                    'fundraiser' => $this->get_feature()->get_fundraiser(
							$this->get_feature()->get_user_fundraiser() )
            ) );
        }

		/**
		 * Renders a banner for the selected Fundraiser.
		 *
		 * @return void
		 */
		public function render_fundraiser_banner()
        {
            global $product;

            // Only render for Fundraiser Products
            if( ! $product instanceof \WC_Product_Fundraiser_Product ) return;

            echo \RWC\Utility::get_include_content(
                '/features/fundraisers/fundraiser-notice.php', array(
                    'fundraiser' => $this->get_feature()->get_user_fundraiser()
            ) );
        }

		/**
         * Drops in the Simple Product add to cart feature.
         *
         * @return void
         */
        public function render_add_to_cart()
        {
            //global $product;
            do_action( 'woocommerce_simple_add_to_cart' );
        }

		/**
         * Adds FundraiserProduct attributes to cart item data.
         *
         * @param array $cart_item_data The existing array of cart item data.
         * @param int   $product_id     The unique id of the product id.
         * @param int   $variation_id   The variation id for variable products.
         *
         * @return array Returns the modified cart data array.
         */
        public function add_fundraiser_cart_item_data( $cart_item_data, $product_id, $variation_id )
        {
            // We'll store custom fundraiser options here.
            $fundraiserOptions = [];

            // We don't care if it's not a Fundraiser product
            $product = wc_get_product( $product_id );
            if( ! $product instanceof \WC_Product_Fundraiser_Product ) {
                return $cart_item_data;
            }

            // Store properties associated with the fundraiser.
            $fundraiser = $this->get_feature()->
				get_fundraiser( $this->get_feature()->get_user_fundraiser() );

            // Make sure a fundraiser is selected.
            if( $fundraiser == null )
            {
                throw new Exception( 'Cannot add a fundraiser item to your ' .
                    'without selecting a fundraiser.' );
            }

            // Get the Fundraiser/Product mapping
            $product = $fundraiser->get_enabled_product( $product_id );

            // Make sure there was a mapping.
            if( $product == null )
            {
                throw new Exception( sprintf( 'Product #%s is not associated ' .
                    'with the fundraiser.', $product_id ) );
            }

            $options = $product->get_options();

            // Iterate through all enabled attributes and add all submitted
            // attributes to cart item data.
            array_walk( $options, function( $item )
              use ( & $options, & $fundraiserOptions ) {

                $attribute = $item->get_field_name();

                if( in_array( $attribute, array_keys( $_POST ) ) ) {
                    $fundraiserOptions[ $attribute ] = $_POST[ $attribute ];
                }

            } );

            // Grab all Customizations for the product
            $customizations = $product->get_customizations();

            // Walk through customizations
            array_walk( $customizations, function( $item )
              use ( & $fundraiserOptions ) {

                // Get field name of current customization.
                $name = $item->get_field_name();

                // If a post variable for the customization exists, store it.
                if( in_array( $name, array_keys( $_POST ) ) ) {

                    $fundraiserOptions[ $name ] = $_POST[ $name ];
                }
            });

            // Associate with the Fundraiser
            $fundraiserOptions[ 'fundraiser' ] =
                $fundraiser->get_fundraiser_id();

            // Dump in fundraiser data.
            $cart_item_data[ 'fundraiser' ] = $fundraiserOptions;

            return $cart_item_data;
        }

		/**
		 * Calculates product prices based on fundraiser customizations.
		 *
		 * @return void
		 */
		public function calculate_product_price()
        {
            global $woocommerce;

            foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {

              $userFundraiser = $this->get_feature()->get_user_fundraiser();

              // User user fundraiser selected. Makes no sense to be here...
              if( empty( $userFundraiser ) ) {
                return;
              }

                $fundraiser = $this->get_feature()->get_fundraiser(
					$this->get_feature()->get_user_fundraiser() );

                $product = $cart_item[ 'data' ];

                if( $product instanceof \WC_Product_Fundraiser_Product ) {

                    $product->set_fundraiser( $fundraiser );
                    $product->update_cart_price_from_fundraiser( $cart_item );
                }
            }
        }

		/**
		 * Adds Fundraiser data to items when they are added to the cart.
		 *
		 * @param array $item_data The item data.
		 * @param array $cart_item The cart item.
		 *
		 * @return void
		 */
		public function add_cart_item_data( $item_data, $cart_item )
        {
            // Only continue if it's a Fundraiser Product.
            if( ! $cart_item[ 'data' ] instanceof \WC_Product_Fundraiser_Product ) {
                return $item_data;
            }

            $fundraiser = $this->get_feature()->get_fundraiser(
				$this->get_feature()->get_user_fundraiser() );

            if( $fundraiser == null)
            {
                throw new Exception( 'The user has no fundraiser selected.' );
            }

            $fp = $fundraiser->get_enabled_product( $cart_item[ 'data' ] );

            if( $fp == null )
            {
                throw new Exception( 'The selected fundraiser is not enabled ' .
                    'for a product in the shopping cart.' );
            }

            array_walk( $fp->get_options(), function( $attribute )
              use ( & $item_data, & $cart_item ) {

                array_walk( $attribute->get_enabled_attribute_options(), function( $option )
                  use( & $item_data, & $cart_item, & $attribute ) {

                    // This allows for pretty labels.
                    $attribute->set_product( $cart_item[ 'data' ] );

                    $name = $attribute->get_field_name();

                    if( array_key_exists( $name, $cart_item[ 'fundraiser'] ) ) {
                        if( $option->get_value() == $cart_item[ 'fundraiser'][ $name ]) {

                            $key = $attribute->get_field_label();
                            $item_data[ $key ] = array(
                                'key' => $attribute->get_field_label(),
                                'display' => $option->get_value()
                            );

                        }
                    }
                });
            });

            // Add customizations
            array_walk( $fp->get_customizations(), function( $customization )
              use ( & $item_data, & $cart_item ) {

                $name = $customization->get_field_name();

                if( array_key_exists( $name, $cart_item[ 'fundraiser'] ) )
                {
                    // If the value in the cart has a name, add it to the
                    // cart data.
                    if( ! empty( $cart_item[ 'fundraiser'][ $name ] ) )
                    {
                        $key = $customization->get_friendly_name();
                        $item_data[ $key ] = array(
                            'key' => $customization->get_friendly_name(),
                            'display' => $cart_item[ 'fundraiser'][ $name ]
                        );
                    }
                }
            });


            return $item_data;
        }

		/**
		 * Puts a "View Details" button in listings.
		 *
		 * @param string      $html    The original button HTML.
		 * @param \WP_Product $product The product.
		 *
		 * @return string Returns the modified HTML.
		 */
        public function use_view_details_button( $html, $product ) {

            // Skip if not a Fundraiser Product
            if( ! $product instanceof \WC_Product_Fundraiser_Product) {
                return $html;
            }


            return sprintf( '<a rel="nofollow" href="%s" data-product_id="%s" data-product_sku="%s" class="%s">%s</a>',
                esc_url( get_permalink( $product->get_id() ) ),
                esc_attr( $product->get_id() ),
                esc_attr( $product->get_sku() ),
                esc_attr( isset( $class ) ? $class : 'button' ),
                esc_html( 'Select options' )
            );
        }

		/**
		 * Replaces the PayPal username with the one assigned to the Fundraiser.
		 *
		 * @param array     $options The paypal options.
		 * @param \WC_Order $order   The WooCommerce order.
		 *
		 * @return arrya Returns the modified options.
		 */
        public function replace_paypal_recipient_with_client( $options, $order )
        {
            $fundraiser = $this->get_feature()->get_fundraiser(
				$this->get_feature()->get_user_fundraiser() );

            if( $fundraiser ) {
                $options[ 'business' ] = $fundraiser->get_paypal_username();
            }

            return $options;
        }

		/**
		 * Adds the unique id of the fundraiser to the Order.
		 *
		 * @return void
		 */
		public function add_fundraiser_id_to_order( $orderId, $postedData, $order )
        {
            update_post_meta( $orderId, 'fundraiser_id',
            	$this->get_feature()->get_user_fundraiser() );


        }

		/**
		 * Adds order metadata.
		 *
		 * This method is the secret sauce that moves cart item metdata fields
		 * over to the individual items in an order. If we don't do this, the
		 * pricing will be right in the order but there will be no record of
		 * the individual attribute selections associated with the items.
		 *
		 * @param int    $item_id The unique id of the item.
		 * @param array  $values  The meta values.
		 *
		 * @return void
		 */
		public function update_order_meta($item_id, $values)
        {
			// If there are values...
            if( ! empty( $values ) ) {

				// If the values have fundraiser data...
				if( isset( $values[ 'fundraiser' ] ) ) {

					// Add metadata to the order item.
              		wc_add_order_item_meta( $item_id, 'fundraiser_options',
						$values[ 'fundraiser' ] );
				}
            }
        }

		////////////////////// END PUBLIC FUNCTIONS ////////////////////////////

		////////////////////// PRIVATE FUNCTIONS ///////////////////////////////

		/**
		 * Initializes WooCommerce Hooks.
		 *
		 * @return void
		 */
		private function initialize()
		{
			// Add form fields for product options
            add_action( 'woocommerce_before_add_to_cart_quantity',
                array( $this, 'render_add_to_cart_options' ) );

			// Display the fundraiser banner above the add to cart stuff.
            add_action( 'woocommerce_before_add_to_cart_form',
                array( $this, 'render_fundraiser_banner' ) );

			// Let's just use the simple product checkout for now.
            add_action( 'woocommerce_single_product_summary', array( $this,
                'render_add_to_cart' ), 30 );


            // Adds the fundraiser data to the FundraiserProduct in the cart.
            add_filter( 'woocommerce_add_cart_item_data', array( $this,
                'add_fundraiser_cart_item_data' ), 10, 3 );

			// Applies custom calculation for FundraiserProducts
            add_action( 'woocommerce_before_calculate_totals', array( $this,
                'calculate_product_price' ) );

			// Applies custom calculation for FundraiserProducts on mini-cart.
            add_action( 'woocommerce_before_mini_cart_contents', array( $this,
                'calculate_product_price' ) );

			// Adds item data to cart items
            add_filter( 'woocommerce_get_item_data', array( $this,
                'add_cart_item_data' ), 10, 2 );

			// For Fundraiser Products, use the "Select Options" button in
            // loops instead of Add to Cart
            add_filter( 'woocommerce_loop_add_to_cart_link', array( $this,
                'use_view_details_button' ), 10, 2 );

			//add_action( '', array( $this, 'replace_paypal_credentials'  ) );
            add_filter( 'woocommerce_paypal_args',
                array( $this, 'replace_paypal_recipient_with_client' ), 10, 2 );

			add_action ('woocommerce_checkout_order_processed', array( $this,
                'add_fundraiser_id_to_order' ), 10, 3 );

			add_action( 'woocommerce_add_order_item_meta', array( $this,
              'update_order_meta' ), 10, 2 );
		}

		/**
		 * Returns the Fundraiser Feature.
		 *
		 * @return \RWC\Features\Fundraisers|null Returns the Fundraiser feature.
		 */
		private function get_feature()
		{
			return $this->get_option( 'fundraiser' );
		}

		/**
		 * Returns true if WooCommerce is installed and active.
		 *
		 * @return bool Returns true if WooCommerce is installed and active.
		 */
		private function is_woocommerce_installed()
		{
			// Make sure WooCommerce is active.
            return function_exists( 'get_woocommerce_api_url' );
		}

		////////////////////// END PRIVATE FUNCTIONS ///////////////////////////

	}
}
