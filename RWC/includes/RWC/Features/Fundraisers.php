<?php

/**
 * This file contains the RWC\Feature\Fundraiser class.
 *
 * @author Brian Reich <breich@reich-consulting.net>
 * @copyright Copyright (C) 2017 Reich Web Consulting
 * @version 1.0
 * @package RWC\Features
 */

namespace RWC\Features {

    /**
     * This feature expands on WooCommerce by putting "Fundraisers" in front
     * of products, so that products are associated with Fundraisers, and a
     * customer must choose a fundraiser before they are allowed to buy a
     * product.
     *
     * @author Brian Reich <bre ich@reich-consulting.net>
     * @copyright Copyright (C) 2016 Reich Web Consulting
     * @version 1.0
     * @package RWC\Features
     */
    class Fundraisers extends \RWC\Feature
    {
        /**
         * The post type.
         *
         * @var string
         */
        const POST_TYPE = 'rwc_fundraiser';

        /**
         * The URL to instructions on how to find PayPal API Credentials.
         *
         * @var string
         */
        const PAYPAL_INSTRUCTIONS_URL = 'https://developer.paypal.com/webapps/developer/docs/classic/api/apiCredentials/#creating-an-api-signature';

        /**
         * The Fundraisers metabox.
         *
         * @var    \RWC\Metabox
         * @access private
         */
        private $metabox = null;

        /**
         * Custom feature initialization.
         *
         * @constructor
         */
        public function __construct( $options = array(), \RWC\Library $library ) {

            parent::__construct( array(
                // Default config.
            ), $library );
        }

        /**
         * Initializes the fundraisers feature.
         *
         * Feature initialization will check that the website is ready to
         * support the feature by verifying that WooCommerce is active on the
         * site. It will initialize the custom post type for Fundraisers, and
         * register it's associated metaboxes.
         *
         * Numerous WooCommerce integrations will be added as well to provide
         * the custom Fundraiser Product functionality.
         *
         * @return void
         */
        public function initialize()
        {

            $wcHooks = new Fundraisers\WooCommerceHooks( array( 'fundraiser' => $this ) );

            // Use the Fundraisers\Shortcodes class to import shortcodes.
            $this->shortcodes = new Fundraisers\Shortcodes();

            $this->set_option( 'reporting-metabox',
                new \RWC\Features\Fundraisers\ReportingMetabox( array(
                    'fundraisersFeature' => $this,
                    'library' => $this->get_library()
            ) ) );

            // Register the custom post type.
            add_action( 'init', array( $this, 'create_post_type' ) );

            // Flush rewrites when the plugin/theme that loaded
            // the library activates.
            register_activation_hook(
                $this->get_library()->get_activation_file() ,
                   array( $this, 'rewrite_flush' ) );

            // Initialize the metabox for Fundraiser Management.
            $this->initializeMetabox();

            // Add content filter.
            add_filter( 'the_content', array( $this,
                'render_fundraiser_content' ), 100, 1 );

            // Register an \RWC\Features\Fundraisers\Fundraiser object
            // when appropriate.
            add_action( 'the_post', array( $this, 'register_fundraiser_object' ) );

            // For fundraiser products, assign them the user's fundraiser.
            add_action( 'the_post', array( $this,
                'assign_fundraiser_to_product' ), 11 );

            // If the user is viewing a Fundraiser, track it.
            add_action( 'init', array( $this, 'set_user_fundraiser' ) );
            add_action( 'wp', array( $this, 'set_user_fundraiser' ) );

            // If user is in a fundraiser, add body class.
            add_filter( 'body_class', array( $this, 'add_fundraiser_body_class' ) );

            // Add fundraising products to the dropdown.
            add_filter( 'product_type_selector', function( $types ) {

                // Key should be exactly the same as in the class product_type parameter
                $types[ 'fundraiser_product' ] = __( 'Fundraiser Product' );

                return $types;

            });

            // Register the FundraiserProduct type with WooCommerce.
            add_action( 'init', array( $this, 'register_fundraiser_product_type' ) );

            // Adds some jQuery to the Admin interface to display the same stuff
            // for Fundraiser products that displays for Simple products.
            add_action( 'admin_footer', array( $this, 'apply_simple_product_options_to_fundraiser_products' ) );

            // Add the fundraiser CSS
            add_action( 'wp_enqueue_scripts', array( $this,
                'add_frontend_css' ) );

            // Add the fundraiser JS
            add_action( 'wp_enqueue_scripts',
                array( $this, 'add_frontend_js' ) );
        }

        public function apply_simple_product_options_to_fundraiser_products()
        {
            if ( 'product' != get_post_type() ) :
                return;
            endif;

            ?><script type='text/javascript'>
                jQuery( document ).ready( function() {
                    jQuery( '.options_group.show_if_simple, .general_tab' )
                        .addClass( 'show_if_fundraiser_product' ).show();
                });
            </script><?php
        }

        public function get_product_price( $price, $cart_item, $item_key )
        {
            $fundraiser = $this->get_fundraiser( $this->get_user_fundraiser() );
            $product = $cart_item[ 'data' ];

            if( $product instanceof \WC_Product_Fundraiser_Product )
            {

                $product->set_fundraiser( $fundraiser );
                return wc_price( $product->get_adjusted_price( $cart_item ) );
            }
        }



        public function assign_fundraiser_to_product()
        {
            global $product;

            if( $this->has_user_fundraiser() ) {

              if( $product instanceof \WC_Product_Fundraiser_Product ) {
                  $product->set_fundraiser( $this->get_fundraiser(
                      $this->get_user_fundraiser() ) );
              }
            }
        }
        /**
         * Loads the Fundraiser Product product type class.
         *
         * Loads the WC_Product_Fundraiser_Product class. This is required
         * before WooCommerce does any work so that it's aware of the custom
         * product type.
         *
         * @return void
         */
        public function register_fundraiser_product_type()
        {
            // This is kind of dumb but neccessary. WooCommerce doesn't want
            // to load namespaced classes so we can't depend on our Autoloader.
            require_once( __DIR__ . '/Fundraisers/FundraiserProduct.php' );
        }

        /**
         * Enqueues Fundraiser CSS when appropriate.
         *
         * This action listener method will load the frontend CSS for the
         * Fundraisers feature when it is needed. For example, it will load
         * on WooCommerce Product pages.
         *
         * @return void
         */
        public function add_frontend_css()
        {
            if( is_singular( 'product' ) )
            {
                // Enqueue CSS and JavaScript.
                wp_enqueue_style( 'rwc-features-fundraiserproducts-css',
                    $this->get_metabox()->get_library()->get_uri() .
                        '/css/rwc/features/fundraisers.css' );
            }
        }

        public function add_frontend_js()
        {
            // Only load on product pages.
            if( is_singular( 'product' ) )
            {
                $product = wc_get_product();

                // Only load on Fundraiser Products
                if( $product instanceof \WC_Product_Fundraiser_Product ) {

                    // Enqueue CSS and JavaScript.
                    wp_enqueue_script( 'rwc-features-fundraiserproducts-js',
                        $this->get_metabox()->get_library()->get_uri() .
                            'js/rwc/features/fundraisers.js', array( 'jquery' ) );
                }
            }
        }

        /**
         * Returns a Fundraiser instance for the specified Fundraiser ID.
         *
         * Instantiates a new Fundraiser instance for the Fundraiser post with
         * the specified unique id.
         *
         * @param int $id The id of the Fundraiser post.
         *
         * @return Fundraisers\Fundraiser Returns the Fundraiser instance.
         * @throws Exception if $id is null.
         */
        public function get_fundraiser( $id ) {

            if( empty( $id ) ) {
              throw new Exception( 'Fundraiser ID cannot be null.');
            }

            return new Fundraisers\Fundraiser( $id );
        }

        /**
         * Automatically assigns the fundraiser associated with the user.
         *
         * When a user navigates to a particular fundraiser page this method
         * will automatically assign the user a cookie that associates them with
         * the fundraiser. So when the user browsers store products, the store
         * is aware of which fundraiser they came from and can apply
         * customizations accordingly.
         *
         * @return void
         */
        public function set_user_fundraiser()
        {
            global $_COOKIE, $woocommerce;

            // If current request is for a fundraiser, set the ID of the
            // the fundraiser as a cookie.
            if( is_singular( self::POST_TYPE ) ) {

                // Get the ID of the fundraiser we're on.
                $fundraiserId = get_the_ID();

                // If the fundraiser is already set, we may need to do things.
                if( isset( $_COOKIE[ 'rwc_fundraiser' ] ) )
                {
                    /*
                     * If the fundraiser being viewed is different than the
                     * fundraiser that's set, we need to clear the cart.
                     */
                    if( $_COOKIE[ 'rwc_fundraiser' ] != $fundraiserId )
                    {
                        $woocommerce->cart->empty_cart();
                    }
                }

                // Set cookie for 1 day, and store in an option. It will not
                // be available in _COOKIES on first set.
                $this->set_option( 'userFundraiser', $fundraiserId );

                setcookie( 'rwc_fundraiser', $fundraiserId,
                    time() + ( 60 * 60 * 24 ), '/' );

            } else {


              global $_COOKIE;

              // If the user fundraiser is set, use it.
              if( isset( $_COOKIE[ 'rwc_fundraiser' ] ) ) {

                  $this->set_option( 'userFundraiser',
                    $_COOKIE[ 'rwc_fundraiser' ] );
              }
            }
        }

        /**
         * Returns the unique id user's selected fundraiser.
         *
         * When a user navigates to a particular fundraiser, a cookie is set
         * which tracks which fundraiser the user has selected, so any time the
         * user is viewing a product it can remain associated with the selected
         * fundraiser.
         *
         * @return int|false The unique id of the selected fundraiser.
         */
        public function get_user_fundraiser()
        {
            return $this->get_option( 'userFundraiser' );
        }

        /**
         * Returns true if the user has selected a fundraiser.
         *
         * @return bool Returns true if the user has selected a Fundraiser.
         */
        public function has_user_fundraiser()
        {
          return ( ! empty( $this->get_user_fundraiser() ) );
        }

        /**
         * Registers a global variable named "fundraiser".
         *
         * On "the_post" action, creates a global variable called "fundraiser"
         * which references an RWC\Features\Fundraiser\Fundraiser object that
         * wraps the current post.
         *
         * @param WP_Post|null $post The current post, or null to use the default.
         *
         * @return void
         */
        public function register_fundraiser_object( $post = null ) {

            // If no post is specified, use the current global post.
            $post = is_null( $post ) ? $GLOBALS[ 'post' ] : $post;

            // If there's no post to wrap, don't bother.
            if( is_null( $post ) ) {
                return;
            }

            if( $post->post_type == self::POST_TYPE ) {

                $GLOBALS[ 'fundraiser' ] =
                    new \RWC\Features\Fundraisers\Fundraiser( $post );
            }
        }

        /**
         * Renders the content of a Fundraiser using the Fundraiser View.
         *
         * When the_content() is executed for a Fundraiser post this method will
         * execute as a filter to the_content and instead render the Fundraiser
         * based on the "detail" Fundraiser template.
         *
         * @param string $content The initial Fundraiser content.
         *
         * @retur string Returns the modified Fundraiser content.
         */
        public function render_fundraiser_content( $content )
        {
            global $fundraiser;

            // Don't filter content for other types/pages.
            if( ! is_singular( self::POST_TYPE ) ) return $content;

            // Don't filter twice.
            remove_filter( 'the_content', array( $this,
                'render_fundraiser_content' ), 100 );

            // Do processing.
            $content = (new Fundraisers\View( ) )->render(array(
                'view' => 'detail',
                'fundraiser' => $fundraiser
            ));

            // Add content filter.
            add_filter( 'the_content', array( $this,
                'render_fundraiser_content' ), 100, 1);

            return $content;
        }

        /**
         * Initializes the Fundraisers metabox.
         *
         * The Fundraisers metabox provides all of the customization options for
         * fundraisers that allow the site owner to set special pricing for
         * items based on the fundraiser they are bought through, enable and
         * disable products and product variations, etc.
         *
         * @return void
         */
        private function initializeMetabox()
        {

            $this->_metabox = new \RWC\Metabox( $this->get_library(), array(
                'renderer' => 'vertical-tabs',
                'id'       => 'rwc-fundraiser-metabox',
                'title'    => 'Fundraiser Options',
                'post_types' => array( self::POST_TYPE ),
                'sections' => array(
                    array(
                        'id' => 'payment',
                        'name' => 'Payment'
                    ),
                    array(
                        'id' => 'customer',
                        'name' => 'Customer Details'
                    ),
                    array(
                        'id'    => 'dates',
                        'name'  => 'Open/Close Dates'
                    ),
                    array(
                        'id'    => 'products',
                        'name'  => 'Products'
                    )
                ),
                'fields' => array(

                    'customer-name' => array(
                        'type' => 'text',
                        'section' => 'customer',
                        'name' => 'Customer Name'
                    ),

                    'customer-business' => array(
                        'type' => 'text',
                        'section' => 'customer',
                        'name' => 'Business Name'
                    ),

                    'address1' => array(
                        'type' => 'text',
                        'section' => 'customer',
                        'name' => 'Address 1'
                    ),

                    'address2' => array(
                        'type' => 'text',
                        'section' => 'customer',
                        'name' => 'Address 2'
                    ),

                    'city' => array(
                        'type' => 'text',
                        'section' => 'customer',
                        'name' => 'City'
                    ),

                    'state' => array(
                        'type' => 'text',
                        'section' => 'customer',
                        'name' => 'State'
                    ),

                    'zipcode' => array(
                        'type' => 'text',
                        'section' => 'customer',
                        'name' => 'Zipcode'
                    ),

                    'paypal-api-username' => array(
                        'type'      => 'text',
                        'section'   => 'payment',
                        'name'      => 'PayPal API Username',
                        'description' => 'The email address associated with ' .
                            'the PayPal account that should receive payment ' .
                            'for this fundraiser.'
                    ),
                    'paypal-api-key' => array(
                        'type'          => 'text',
                        'section'       => 'payment',
                        'name'          => 'PayPal API Key',
                        'description'   => 'The account\'s PayPal API password.'
                    ),
                    'paypal-api-signature' => array(
                        'type'          => 'text',
                        'section'       => 'payment',
                        'name'          => 'PayPal API Signature',
                        'description'   => 'The account\'s PayPal API signature.'
                    ),

                    'start-date' => array(
                        'type'          => 'date',
                        'section'       => 'dates',
                        'name'          => 'Start Date',
                    ),

                    'end-date' => array(
                        'type'          => 'date',
                        'section'       => 'dates',
                        'name'          => 'End Date',
                    ),

                    'products' => array(
                        'type'      => 'fundraiser-products',
                        'section'   => 'products',
                        'name'      => 'Product Setup'
                    )
                )
            ));
        }

        /**
         * Returns the Fundraisers metabox.
         *
         * @return \RWC\Metabox Returns the fundraisers metabox.
         */
        public function get_metabox()
        {
            return $this->_metabox;
        }

        /**
         * Flush rewrite rules on activation.
         *
         * @return void
         */
        public function rewrite_flush() {

            // Register the post type.
            $this->create_post_type();

            // ATTENTION: This is *only* done during plugin activation hook in this example!
            // You should *NEVER EVER* do this on every page load!!
            flush_rewrite_rules();
        }

        /**
         * Creates the custom post type for Fundraisers.
         *
         * @return void
         */
        public function create_post_type() {

            register_post_type( self::POST_TYPE,
                array(

                    'labels' => array(
                        'name' => __( 'Fundraisers' ),
                        'singular_name' => __( 'Fundraiser' )
                    ),
                    'description' => 'Fundraisers that allow users to purchase items through the WooCommerce Stores',
                    'menu_icon' => 'dashicons-chart-line',
                    'supports' => array( 'title', 'editor', 'thumbnail', 'excerpt',
                        'revisions' ),
                    'public' => true,
                    'has_archive' => true,
                    'rewrite' => array(
                        'slug' => __( 'fundraisers' ),
                        'with_front' => false
                    )
                )
            );
        }

        /**
         * Adds body classes for fundraisers.
         *
         * When the user is shopping a particular fundraiser body classes are
         * added called "in-rwc-fundraiser" which specifies that the user is
         * currently shopping a fundraiser, and "in-rwc-fundraiser-<id>" where
         * <id> is the unique id of the fundraiser being browsed.
         *
         * @param array $bodyClass existing list of body classes.
         * @return array Returns the modified body class array.
         */
        public function add_fundraiser_body_class( $bodyClass ) {

            // Get the unique id of the fundraiser
            $fundraiserId = $this->get_user_fundraiser();

            // If the user is currently in a Fundraiser, set body classes
            // appropriately.
            if( $fundraiserId )
            {
                $bodyClass[] = 'in-rwc-fundraiser';
                $bodyClass[] = 'in-rwc-fundraiser-' . esc_attr( $fundraiserId );
            }

            // If the user is browsing a product and that product is not in
            // the fundraiser, assign classes.
            if( is_singular( 'product' ) )
            {
                $post_id = get_the_ID();
                $fundraiser = $this->get_fundraiser( $fundraiserId );

                // This product is not in the fundraiser.
                if( ! $fundraiser->is_product_enabled( $post_id ) ) {
                    $bodyClass[] = 'product-not-in-fundraiser';
                }

            }
            return $bodyClass;
        }

        public function get_post_type()
        {
            return self::POST_TYPE;
        }
    }
}
