<?php

namespace RWC\Features\Fundraisers {

    class Fundraiser extends \RWC\PostWrapper {

        /**
         * The RWC\Library associated with the Fundraiser.
         *
         * @var    \RWC\Library
         * @access private
         */
        private $library;

        /**
         * The value of the products fields. We store this so we don't have to
         * repeatedly query Metabox storage (slow). Retrieve via get_products().
         *
         * @var    array
         * @access private
         */
        private $products_value = null;

        /**
         * Sets the RWC\Library instance that craeted the Fundraiser.
         *
         * @param \RWC\Library $library The Library instance.
         */
        public function set_library( \RWC\Library $library = null ) {

            $this->library = $library;
        }

        /**
         * Returns the Library instance used to create the Fundraiser.
         *
         * @return \RWC\Library Returns the Library instance.
         */
        private function get_library() {

            // Lazy load if we don't have one.
            if( $this->library == null ) {

                $this->library = \RWC\Library::load();
            }

            return $this->library;
        }

        /**
         * Returns the Fundraisers feature.
         *
         * @return \RWC\Features\Fundraisers Returns the Fundraisers Feature.
         * @throws \RWC\Features\Fundraisers\Exception if feature isn't loaded.
         */
        private function get_fundraiser_feature()
        {
            $feature = $this->get_library()->get_loaded_feature( 'Fundraisers' );

            if( $feature == null ) {
                throw new Exception( 'The Fundraisers feature is not loaded.' );
            }

            return $feature;
        }

        /**
         * Returns the Fundraisers metabox.
         *
         * @return \RWC\Metabox Returns the Fundraisers metabox.
         */
        private function get_fundraiser_metabox()
        {
            return $this->get_fundraiser_feature()->get_metabox();
        }

        /**
         * Returns the Fundraisers metabox.
         *
         * We can use the metabox to easily query for the data stored in the
         * metabox for this Fundraiser.
         *
         * @return RWC\Metabox Returns the Fundraisers metabox.
         */
        private function get_fundraiser_metabox_storage()
        {
            return $this->get_fundraiser_metabox()->get_storage();
        }

        /**
         * Returns the product data associated with this Fundraiser.
         *
         * @return array|null Returns the product data for the Fundraiser.
         */
        private function get_products()
        {
            if( $this->products_value == null )
            {
                // Get the value of the products field.
                $this->products_value =
                    $this->get_fundraiser_metabox_storage()->get(
                        $this->get_post()->ID, 'products');

                if( $this->products_value == null ) $this->products_value = array();
            }

            foreach( $this->products_value as $id => $options)
            {
                $products[] = new \RWC\Features\Fundraisers\Fundraiser\Product( $id, $options);
            }

            return $products;
        }

        /**
         * Returns the PayPal username (email address) for the Fundraiser.
         *
         * @return string Returns the PayPal username.
         */
        public function get_paypal_username()
        {
            return $this->get_fundraiser_metabox_storage()->get(
                    $this->get_post()->ID, 'paypal-api-username' );
        }

        public function get_title()
        {
            return get_the_title( $this->get_post()->ID );
        }

        public function get_customer_name()
        {
            return $this->get_fundraiser_metabox_storage()->get(
                $this->get_post()->ID, 'customer-name' );
        }

        public function get_business_name()
        {
            return $this->get_fundraiser_metabox_storage()->get(
                  $this->get_post()->ID, 'customer-business' );
        }

        public function get_address1()
        {
            return $this->get_fundraiser_metabox_storage()->get(
                  $this->get_post()->ID, 'address1' );
        }

        public function get_address2()
        {
            return $this->get_fundraiser_metabox_storage()->get(
                  $this->get_post()->ID, 'address2' );
        }

        public function get_city()
        {
            return $this->get_fundraiser_metabox_storage()->get(
                  $this->get_post()->ID, 'city' );
        }

        public function get_state()
        {
            return $this->get_fundraiser_metabox_storage()->get(
                  $this->get_post()->ID, 'state' );
        }

        public function get_zipcode()
        {
            return $this->get_fundraiser_metabox_storage()->get(
                  $this->get_post()->ID, 'zipcode' );
        }

        /**
         * Returns the specified Product associated with the Fundraiser.
         *
         * Each Fundraiser can have many Products associated with it. If the
         * Fundraiser has the specified Product associated with it, this method
         * will return that Product. Otherwise, it will return null.
         *
         * @param int|WC_Product $productId The unique id of the product.
         *
         * @return Fundraiser\Product|null Returns the matching product.
         */
        public function get_fundraiser_product( $productId )
        {
            // If we have an object, convert it back.
            if( $productId instanceof \WC_Product ) {
                $productId = $productId->get_id();
            }

            $products = $this->get_products();

            // Reduce the products array to the matching product, or null.
            $product = array_reduce( $products, function( $carry, $item )
              use ( $productId ) {

                return ( $item->get_id() == $productId) ? $item : $carry;
            });

            return $product;
        }

        /**
         * Returns the enabled Product associated with the Fundraiser.
         *
         * Each Fundraiser can have many Products associated with it. If the
         * Fundraiser has the specified Product associated with it, this method
         * will return that Product if it is enabled. Otherwise, it will return
         * null.
         *
         * @param int|WC_Product $productId The unique id of the product.
         *
         * @return Fundraiser\Product|null Returns the matching product.
         */
        public function get_enabled_product( $productId )
        {

            $product = $this->get_fundraiser_product( $productId );

            if( is_null( $product ) || ( $product->is_enabled() == false) ) {
                return null;
            }

            return $product;
        }

        /**
         * Returns true if the specified product is enabled for the Fundraiser.
         *
         * @param int|WC_Product $productId The unique id of the Product.
         *
         * @return bool Returns true if the product is enabled for the fundraiser.
         */
        public function is_product_enabled( $productId )
        {
            $product = $this->get_enabled_product( $productId );

            return (false == is_null( $product ) );
        }

        /**
         * Returns the Fundraiser's settings for the specified product.
         *
         * Each fundraiser contains a list of products that are available for
         * purchase through the fundraiser. This method will return the
         * available options for the specified fundraiser.
         *
         * @deprecated
         */
        public function get_product_options( $productId )
        {
            // If we have an object, convert it back.
            if( $productId instanceof \WC_Product ) {
                $productId = $productId->get_id();
            }

            $products = $this->get_products();

            // If no products data, return null.
            if( false == is_array( $products ) ) return null;

            // If no data for the specified product, return null.
            if( false == isset( $products[ $productId ] ) ) return null;

            return $products[ $productId ];
        }

        /**
         * Returns an array of all products enabled for this Fundraiser.
         *
         * The Fundraiser details are retrieved from the database and used to
         * build a list of the unique id's of all WooCommerce products that are
         * associated with this fundraiser.
         *
         * @return array Returns an array of enabled products.
         */
        public function get_enabled_products()
        {
            // Get the value of the products field.
            $products = $this->get_products();

            $enabledProducts = [];

            // Add product if it is enabled.
            array_walk( $products, function( $item )
              use ( & $enabledProducts ) {

                if( $item->is_enabled() ) {
                    $enabledProducts[] = $item;
                }
            });

            // Return the list.
            return $enabledProducts;
        }

        public function get_enabled_product_ids() {

            $ids = [];

            array_walk( $this->get_enabled_products(), function( $product )
              use ( & $ids ) {
                  $ids[] = $product->get_id();
            } );

            return $ids;
        }

        public function get_supported_options( $productId, $attributeName )
        {

            // If we have an object, convert it back.
            if( $productId instanceof \WC_Product ) {
                $productId = $productId->get_id();
            }

            $details = $this->get_products();

            // If it has a value, iterate through products.
            if( is_array( $details ) ) {

                $options = array();

                // Walk through
                array_walk( $details, function( $item, $currentProductId ) use
                  ( & $options, $productId, $attributeName) {

                    if( $productId == $currentProductId ) {

                        if( isset( $item[ $attributeName ] ) ) {
                            foreach( $item[ $attributeName ] as $name => $value ) {
                                if( $value[ 'enabled' ] ) {
                                    $options[ $name ] = $value;
                                }
                            }
                        }
                    }
                });

                // Return the list.
                return $options;
            }
            // No data, just return an empty array.
            return array();
        }
        /**
         * Returns a \WP_Query for all WooCommerce products in the fundraiser.
         *
         * @return \WP_Query Returns a Query for all products in the fundraiser.
         */
        public function get_products_query()
        {
            // Get a list of all product id's associated with the fundraiser.
            $productIds = $this->get_enabled_product_ids();

            // Workaround for empty post__in array. If it's empty give it a
            // value that will never come up in the database.
            if( empty( $productIds ) )
            {
                $productIds[] = -1;
            }

            // Return the configured WP_Query.
            return new \WP_Query( array(
                'post__in' => $productIds,
                'post_type' => 'product',
                'posts_per_page' => -1
            ) );
        }

        /**
         * Returns the unique id of this Fundraiser.
         *
         * @return int Returns the unique id of this Fundraiser.
         */
        public function get_fundraiser_id()
        {
            return $this->get_post()->ID;
        }

        /**
         * Returns the unique id of all Orders purchased in the Fundraiser.
         *
         * Returns an array of Order ids for all Orders purchased in the
         * Fundraiser. This method does a manual query against the WordPress
         * posts table. In the future of WooCommerce abstracts Orders out to
         * a separate table this method will become invalid. However we still
         * need to do it this way instead of using WP_Order_Query because the
         * new query class does not provide a method for querying on post meta.
         *
         * @return array Returns an array of the ids for all fundraiser orders.
         */
        public function get_order_ids()
        {
            global $wpdb;

            // Query for all orders in the fundraiser.
            $query = $wpdb->prepare(
                "
                SELECT
                    POSTS.ID
                        FROM
                            {$wpdb->posts} AS POSTS
                        INNER JOIN
                            {$wpdb->postmeta} AS META
                                ON POSTS.ID = META.POST_ID
                    WHERE
                        POSTS.POST_TYPE = 'shop_order' AND
                        META.META_KEY = 'fundraiser_id' AND
                        META.META_VALUE = %s
                ",
                $this->get_fundraiser_id()
            );

            return $wpdb->get_col( $query );
        }
    }
}
