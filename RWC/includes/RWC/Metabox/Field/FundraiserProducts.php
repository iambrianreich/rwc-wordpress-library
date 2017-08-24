<?php

/**
 * This file contains the RWC\Metabox\Field\FundraiserProducts class.
 *
 * @author Brian Reich <breich@reich-consulting.net>
 * @copyright Copyright (C) 2017 Reich Web Consulting https://www.reich-consulting.net/
 */
namespace RWC\Metabox\Field {

    /**
     * Assigns WooCommerce products to a Fundraiser.
     *
     * The FundraiserProducts Field has the very specific function of assigning
     * products to fundraisers. When a product is assigned to a Fundraiserm
     * fundraising supporters can visit that fundraiser's page and purchase
     * items those specific items.
     *
     * The FundraiserProducts field will also allow the user to allow or deny
     * specific product options. For example, a particular fundraiser may not
     * want to make all color options available, or only wants to sell shirts
     * in colors that match their teams branding.
     *
     * The FundraiserProducts field will allow the user to set a price
     * differential for each product option.
     *
     * @author Brian Reich <breich@reich-consulting.net>
     * @copyright Copyright (C) 2017 Reich Web Consulting https://www.reich-consulting.net/
     */
    class FundraiserProducts extends \RWC\Metabox\Field
    {
        /**
         * The deserialized field value. Access via get_deserialized_value().
         *
         * @var    array
         * @access private
         */
        private $deserializedValue;

        /**
         * Returns the deserialized field value.
         *
         * The value of a FundraiserProducts field is stored as a serialized
         * PHP array in the WordPress database. This helper method will convert
         * the serialized array back to a PHP array for simpler use.
         *
         * @return array Returns the deserialized field value.
         */
        private function get_deserialized_value()
        {
            // We only want to do this once, so let's lazy-load the deserialized
            // value.
            if( is_null( $this->deserializedValue ) ) {

                $this->deserializedValue = unserialize( $this->get_value() );
            }

            return $this->deserializedValue;
        }

        /**
         * Returns true if the specified attribute value is enabled.
         *
         * Each Fundraiser allows the user to enable/disable product attribute
         * values. For example, a fundraiser may not want to sell hats in red
         * so they could disable the "red" option for the "color" attribute.
         * This method will return true if a specified attribute value is
         * enabled, or false if it has been disabled.
         *
         * @param int $productId The unique id of the product.
         *
         * @return bool Returns true if the attribute value is enabled.
         */
        private function is_product_attribute_value_enabled( $productId, $attribute, $value )
        {
            $option = $this->get_product_attribute_option( $productId, $attribute, $value );

            // Attribute data exists, value data exists, enabled is true
            return
                ( ! is_null( $option ) ) &&
                ( isset( $option[ 'enabled' ] ) ) &&
                $option[ 'enabled' ];
        }

        /**
         * Returns true if the specified attribute value is the default.
         *
         * When a user is configuring a fundraiser, they can choose which
         * attribute option is the default selection. This method returns true
         * if the specified value is the default selection for the product and
         * attribute specified.
         *
         * @param int $productId The unique id of the product.
         *
         * @return bool Returns true if the attribute value is enabled.
         */
        private function is_product_attribute_value_default( $productId, $attribute, $value )
        {
            $option = $this->get_product_attribute_option( $productId, $attribute, $value );

            // Attribute data exists, value data exists, enabled is true
            return
                ( ! is_null( $option ) ) &&
                ( isset( $option[ 'isDefault' ] ) ) &&
                $option[ 'isDefault' ];
        }

        /**
         * Retrieves data associated with a product attribute.
         *
         * Each Fundraiser has an array of Products associated with it, and each
         * product has an array of attribute data, which describes which
         * attribute values are enabled for the Fundraiser, along with
         * additional information such as pricing for that option value. This
         * method will return an array of product attribute data for the
         * fundraiser for the specified product id and attribute.
         *
         * @param int    $productId The unique id of the product.
         * @param string $attribute The attribute.
         *
         * @return array|null Returns an array of attribute data.
         */
        private function get_product_attribute( $productId, $attribute ) {

            $value = $this->get_deserialized_value();

            // Ensure that the field has a value.
            if( is_null( $value ) ) {

                return null;
            }

            // Ensure that the product exists.
            if( ! isset( $value[ $productId ] ) ) {

                return null;
            }

            // Ensure that the attribute has data.
            if( ! isset( $value[ $productId ][ 'options' ][ $attribute ] ) ) {

                return null;
            }

            return $value[ $productId ][ 'options' ][ $attribute ];
        }

        /**
         * Retrieves the data associated with a particular attribute option.
         *
         * Each fundraiser product has an associated list of product attributes
         * and custom data associated with each attribute option, such as
         * whether that option is enabled, and price increase/decreased based
         * on whether or not that option is selected. This method returns an
         * array of custom information associated with a particular attribute
         * option for the given product.
         *
         * @param int    $productId The unique id of the product.
         * @param string $attribute The attribute.
         * @param string $option    The attribute value.
         *
         * @return array|null Returns an array of attribute data.
         */
        private function get_product_attribute_option( $productId, $attribute, $option )
        {
            // Get the attribute data.
            $attribute = $this->get_product_attribute( $productId, $attribute );

            // If the attribute doesn't exist for the product, return nothing.
            if( $attribute == null ) {

                return null;
            }

            // If the option doesn't exist for the attribute, return nothing.
            if( ! isset( $attribute[ $option ] ) ) {

                return null;
            }

            // Return the option data.
            return $attribute[ $option ];
        }

        /**
         * Returns the price differential for a specific product option.
         *
         * The FundraiserProducts field allows the user to specify an amount to
         * add or subtract from the base product price when that option is
         * selected. This method will return that value.
         *
         * @param int $productId The unique id of the WooCommerce product.
         * @param string $attribute The attribute name.
         * @param string $value     The attribute value.
         *
         * @return string Returns the price differential.
         */
        private function get_product_attribute_option_price_differential( $productId, $attribute, $value ) {


            $option = $this->get_product_attribute_option( $productId, $attribute, $value );

            // Attribute data exists, value data exists, enabled is true
            if( is_null( $option ) ) {
                return null;
            }

            if( ! isset( $option[ 'optionPrice' ] ) ) {
                return null;
            }

            // Convert the string value to a float value, and format it.
            return number_format_i18n( floatval(
                $option[ 'optionPrice' ] ), 2 );
        }

        /**
         * Returns true if the Fundraiser has a custom base price for the
         * product.
         *
         * Each fundraiser can override the base price of a product, so a single
         * product can have totally different prices across fundraisers. This
         * method will return true if the fundraiser specifies a base price.
         *
         * @param int $productId The unique ID of the WooCommerce product.
         *
         * @return bool Returns true if the product has a custom base price.
         */
        private function has_product_base_price( $productId )
        {
            $value = $this->get_deserialized_value();

            // Returns true if there is FundraiserProducts data, if it contains
            // an entry for the specified product, and the "enabled" flag for
            // that product is true.
            return
                ( ! is_null( $value ) ) &&
                ( ! empty( $value ) ) &&
                ( isset( $value[ $productId ] ) ) &&
                ( isset( $value[ $productId ][ 'basePrice' ] ) ) &&
                $value[ $productId ][ 'basePrice' ];
        }

        /**
         * Returns the custom base price for the product.
         *
         * Each fundraiser can override the base price of a product, so a single
         * product can have totally different prices across fundraisers. This
         * method will return the custom base price of the product for the
         * current Fundraiser, or null if none is specified.
         *
         * @param int $productId The unique ID of the WooCommerce product.
         *
         * @return float|null Returns the custom base price, or null.
         */
        private function get_product_base_price( $productId )
        {
            $value = $this->get_deserialized_value();

            if( $this->has_product_base_price( $productId ) ) {
                return $value[ $productId ][ 'basePrice' ];
            }

            return null;
        }

        /**
         * Returns true if the specified WooCommerce product is enabled for the
         * Fundraiser.
         *
         * @param int $productId The unique ID of the WooCommerce Product.
         *
         * @return bool Returns true if the product is enabled.
         */
        private function is_product_enabled( $productId ) {

            $value = $this->get_deserialized_value();

            // Returns true if there is FundraiserProducts data, if it contains
            // an entry for the specified product, and the "enabled" flag for
            // that product is true.
            return
                ( ! is_null( $value ) ) &&
                ( ! empty( $value ) ) &&
                ( isset( $value[ $productId ] ) ) &&
                ( isset( $value[ $productId ][ 'enabled' ] ) ) &&
                $value[ $productId ][ 'enabled' ];
        }

        /**
         * Returns a list of CustomizationFields for the current field value.
         *
         * Returns a array of CustomizationField objects based on the current
         * field state. The
         */
        private function get_customization_fields( $productId )
        {
            // Get the full field value.
            $value = $this->get_deserialized_value();

            $fields = [];

            // Ensure field had value.
            if( $value == null ) return $fields;

            // Ensure that there is a value for the product id.
            if( ! isset( $value[ $productId ] ) ) return $fields;

            // Ensure that there is a customizations array.
            if( ! isset( $value[ $productId ][ 'customizations' ] ) ) return $fields;

            // Walk the list and build CustomizationLabels
            array_walk( $value[ $productId ][ 'customizations' ], function( $field)
              use( & $fields ) {
                  $fields[] = new \RWC\Features\Fundraisers\Fundraiser\
                    CustomizationField( $field );
            });

            return $fields;
        }

        /**
         * Renders the input field
         *
         * @param \RWC\Metabox The Metabox to render.
         *
         * @return void
         */
        public function render( )
        {
            // Enqueue CSS and JavaScript.
            wp_enqueue_style( 'rwc-metabox-field-fundraiserproducts-css',
                $this->get_metabox()->get_library()->get_uri() .
                    '/css/rwc/metabox/field/fundraiser-products.css' );

            wp_enqueue_script( 'rwc-metabox-field-fundraiserproducts-js',
                $this->get_metabox()->get_library()->get_uri() .
                    '/js/rwc/metabox/field/fundraiser-products.js' );

            $products = $this->get_fundraising_products_query();

            $this->render_field( $products );
        }

        /**
         * Renders the FundraiserProducts field.
         *
         * @param \WP_Query $products The query containing all Products
         */
        private function render_field( $products ) { ?>
            <div class="rwc-metabox-field-fundraiser-products" id="<?php echo esc_attr( $this->get_id() ); ?>">
                <div class="container">
                    <?php if( $products->have_posts() ) : ?>
                        <?php $this->render_field_products( $products ) ?>
                    <?php else : ?>
                        <p class="message error">No products are available
                            for fundraising.</p>
                    <?php endif; ?>
                </div>
            </div>
        <?php }

        /**
         * Renders the list of WooCommerce products.
         *
         * @param \WP_Query $products The query containing all Products.
         */
        private function render_field_products( $products ) { ?>
            <?php while( $products->have_posts() ) : ?>
                <?php $products->the_post(); ?>
                <?php global $product; ?>
                <?php if( ! $product instanceof \WC_Product_Fundraiser_Product ) continue; ?>
                <?php $prefix = esc_attr( sprintf('%s[%s]', $this->get_id(), get_the_ID() ) ); ?>
                <div class="product" data-product-id"<?php the_ID(); ?>">
                    <header>
                        <h3> <?php the_title(); ?></h3>
                        <div class="enabled">
                            <input
                                type="checkbox"
                                name="<?php echo $prefix; ?>[enabled]"
                                id="<?php echo $prefix; ?>[enabled]"
                                <?php if( $this->is_product_enabled( get_the_ID() ) ) { echo 'checked="checked"'; } ?> />
                            <label for="<?php printf("%s[enabled]", $prefix ); ?>">Sell This Product</label>
                        </div>
                    </header>
                    <section class="options <?php if( $this->is_product_enabled( get_the_ID() ) ) { echo 'enabled'; } ?>">
                        <ul class="product-options">
                            <li>
                                <label for="<?php echo $prefix; ?>[basePrice]">Base Price:</label>
                                <input
                                    type="number"
                                    name="<?php echo $prefix; ?>[basePrice]"
                                    id="<?php echo $prefix; ?>[basePrice]"
                                    value="<?php echo esc_attr( $this->get_product_base_price( get_the_ID() ) ); ?>" />
                            </li>
                        </ul>
                        <?php $variations = $product->get_attributes()  ?>
                        <ul class="variations">
                            <?php foreach( $variations as $name => $variation ) : ?>
                                <li>
                                    <h4>
                                        <?php echo esc_html( $variation->get_name() ); ?>
                                    </h4>
                                    <ul>
                                        <?php foreach( $variation->get_options() as $value ) : ?>
                                            <?php $variantPrefix = sprintf("%s[options][%s][%s]", $prefix, $name, $value ); ?>
                                            <li>
                                                <h5>
                                                    <?php echo esc_html( $value ); ?>
                                                    <input
                                                        type="checkbox"
                                                        id="<?php printf("%s[enabled]", $variantPrefix ); ?>"
                                                        name="<?php printf("%s[enabled]", $variantPrefix ); ?>"
                                                        <?php if( $this->is_product_attribute_value_enabled( get_the_ID(), $name, $value ) ) { echo 'checked="checked"'; } ?> />
                                                </h5>
                                                <div class="container <?php if( $this->is_product_attribute_value_enabled( get_the_ID(), $name, $value ) ) { echo 'enabled'; } ?>"">
                                                    <div class="inputContainer checkbox">
                                                        <label for="<?php printf("%s[isDefault]", $variantPrefix ); ?>">Default Selection</label>
                                                        <input
                                                            type="checkbox"
                                                            id="<?php printf("%s[isDefault]", $variantPrefix ); ?>"
                                                            name="<?php printf("%s[isDefault]", $variantPrefix ); ?>"
                                                            <?php if( $this->is_product_attribute_value_default( get_the_ID(), $name, $value ) ) { echo 'checked="checked"'; } ?> />
                                                    </div>
                                                    <div class="inputContainer">
                                                        <label for="<?php printf("%s[optionPrice]", $variantPrefix ); ?>">Price Difference:</label>
                                                        <input
                                                            id="<?php printf("%s[optionPrice]", $variantPrefix ); ?>"
                                                            name="<?php printf("%s[optionPrice]", $variantPrefix ); ?>"
                                                            value="<?php echo esc_attr( $this->get_product_attribute_option_price_differential( get_the_ID(), $name, $value ) ); ?>" />
                                                    </div>
                                                </div>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </li>
                            <?php endforeach; ?>
                        </ul>

                        <div class="customizations">
                            <?php $customizations = $this->get_customization_fields( get_the_ID() ); ?>
                            <h3>Customization Fields</h3>
                            <table>
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Description</th>
                                        <th>Price Differential</th>
                                        <th>Options</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="template">
                                        <td>
                                            <input
                                                type="text"
                                                placeholder="Name"
                                                value=""
                                                id="<?php echo sprintf('%s[customizations][index][name]', $prefix ); ?>"
                                                name="<?php echo sprintf('%s[customizations][index][name]', $prefix ); ?>" />
                                        </td>
                                        <td>
                                            <input
                                                type="text"
                                                placeholder="Description"
                                                value=""
                                                id="<?php echo sprintf('%s[customizations][index][description]', $prefix ); ?>"
                                                name="<?php echo sprintf('%s[customizations][index][description]', $prefix ); ?>" />
                                        </td>
                                        <td>
                                            <input
                                                type="text"
                                                placeholder="Price Difference"
                                                value=""
                                                id="<?php echo sprintf('%s[customizations][index][price]', $prefix ); ?>"
                                                name="<?php echo sprintf('%s[customizations][index][price]', $prefix ); ?>" />
                                        </td>
                                        <td>
                                            <button class="remove">Remove</button>
                                        </td>
                                    </tr>
                                    <?php $i = 0; foreach( $customizations as $customization ) : ?>
                                        <?php if( empty( $customization->get_name() ) ) continue; ?>
                                        <tr>
                                            <td>
                                                <input
                                                    type="text"
                                                    placeholder="Name"
                                                    value="<?php echo $customization->get_friendly_name(); ?>"
                                                    id="<?php echo sprintf('%s[customizations][%s][name]', $prefix, $i + 1 ); ?>"
                                                    name="<?php echo sprintf('%s[customizations][%s][name]', $prefix , $i + 1 ); ?>" />
                                            </td>
                                            <td>
                                                <input
                                                    type="text"
                                                    placeholder="Description"
                                                    value="<?php echo $customization->get_friendly_description(); ?>"
                                                    id="<?php echo sprintf('%s[customizations][%s][description]', $prefix, $i + 1 ); ?>"
                                                    name="<?php echo sprintf('%s[customizations][%s][description]', $prefix, $i + 1 ); ?>" />
                                            </td>
                                            <td>
                                                <input
                                                    type="text"
                                                    placeholder="Price Difference"
                                                    value="<?php echo $customization->get_price_differential(); ?>"
                                                    id="<?php echo sprintf('%s[customizations][%s][price]', $prefix, $i + 1 ); ?>"
                                                    name="<?php echo sprintf('%s[customizations][%s][price]', $prefix, $i + 1 ); ?>" />
                                            </td>
                                            <td>
                                                <button class="remove">Remove</button>
                                            </td>
                                        </tr>
                                        <?php $i++; ?>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="4">
                                            <button class="add" type="button">Add New</button>
                                        </td>
                                    </tr>
                                </tfoot>
                            <table>
                        </div>
                    </section>
                </div>
            <?php endwhile; ?>
            <?php wp_reset_postdata(); ?>
        <?php }

        /**
         * Build an array of all variations.
         *
         * @param array $variations An array of variations for a  product.
         * @return array Returns an associative array of variations.
         */
        private function get_variations( $variations )
        {
            $options = array();

            // Iterate through all variations
            array_map( function( $variation ) use ( & $options ) {

                // Iterate through all attributes
                array_walk( $variation[ 'attributes' ], function( $v, $k ) use ( & $options ) {

                    // Add attribute name to options list.
                    if( ! isset( $options[ $k ] ) ) {
                        $options[ $k ] = array();
                    }

                    // Add attribute value to attribute.
                    if( ! in_array( $v, $options[ $k ])) {
                        $options[ $k ][] = $v;
                    }
                });
            }, $variations );

            return $options;
        }

        /**
         * Returns the default options for a text field.
         *
         * @return array Returns the default options for a text field.
         */
        public function get_default_options() {

            return array(
                'name'  => 'rwc-metabox-field-fundraiserproducts-name',
                'id'    => 'rwc-metabox-field-fundraiserproducts-name',
                'value' => null,
                'required' => false,
            );
        }

        /**
         * Returns a WP_Query for all products that are available for
         * fundraising.
         *
         * @return \WP_Query Returns a WP_Query for all fundraising products.
         */
        private function get_fundraising_products_query() {

            return new \WP_Query( array(
                'post_type' => 'product',
                'posts_per_page' => -1
            ) );
        }
    }

}
