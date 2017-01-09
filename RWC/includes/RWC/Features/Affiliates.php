<?php

/**
 * This file contains the RWC\Feature\Affiliates class.
 *
 * @author Brian Reich <breich@reich-consulting.net>
 * @copyright Copyright (C) 2016 Reich Web Consulting
 * @version 1.0
 * @package RWC\Features
 */

namespace RWC\Features {

	/**
	 * This feature provides affiliate linking features to the website.
     *
	 * @author Brian Reich <breich@reich-consulting.net>
	 * @copyright Copyright (C) 2016 Reich Web Consulting
	 * @version 1.0
	 * @package RWC\Features
	 */
	class Affiliates extends \RWC\Feature {

        /**
         * Custom feature initialization.
         *
         * @constructor
         */
        public function __construct( $options = array(), \RWC\Library $library ) {

            parent::__construct( array(

                'affiliates' => array(
                    'highland-woodworking' => new Affiliates\HighlandWoodworking( array(
                        'name' => 'Highland Woodworking',
                        'url'  => '%item_url%?id=%affiliate_code%'
                    ) )
                )
            ), $library );
        }

        /**
         * Returns the named affiliate. If no affiliate implementation can be
         * found by the specified name then null is returned.
         *
         * @return RWC\Features\Affiliates\Affiliate Returns the affiliate.
         */
        public function get_affiliate( $name ) {

            // Gets the list of supported affiliates.
            $affiliates = $this->get_option( 'affiliates');

            // If it exists, return it. If not, null.
            return isset( $affiliates[ $name ] ) ? $affiliates[ $name ] : null;
        }

		/**
		 * Initializes the affiliates feature.
         *
		 * @return void
		 */
		public function initialize() {

			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue' ) );
            add_shortcode( 'affiliate_box', array( $this, 'render_affiliate_box' ) );
		}

		/**
		 * Load CSS and JS.
		 *
		 * @return void
		 */
		public function enqueue() {

			wp_enqueue_style( 'rwc_features_affiliates_affiliate-block',
                $this->get_library()->get_uri() .
                'css/rwc/features/affiliates/affiliate-block.css' );
		}

        /**
         * Generates an affiliate product box.
         *
         * The following shortcode attributes are available. The "affiliate"
         * option specifies the name of the Affiliate, for example "Amazon",
         * and it should map to one of the Affiliates supported by the Affiliate
         * feature. The "affiliate_code" option specifies the affiliate code
         * which should be applied to the final URL to provide the site author
         * with affiliate income. The "image" attribute specifies the URL of
         * an image of the product. The "url" attribute specifies the URL to
         * the affiliate product. The "price" attribute specifies the price of
         * the product. It should be numeric, no currency symbols applied. The
         * "rating" attribute should specifies the average product rating
         * on a scale from 0-5. The "currency" attribute specifies the currency
         * type. Currently only "USD" (US Dollars) is supported. The "availability"
         * attribute specifies whether or not the product is in stock. If it
         * is "InStock" the product is in stock. Otherwise, it is out of stock.
         * The "price_timestamp" attribute specifies the UNIX timesatamp when
         * the price was most recently updated from the affiliate's website.
         * The "description" attribute specifies a short product description.
         * Finally, the "dynamic_update" attribute specifies that all of the
         * other shortcode attributes should be dynamically updated with live
         * data from the affiliate's website when it's available.
         *
         * @param array $atts The shortcode attributes.
         *
         * @return string Returns the Affiliate Product Box as a schema.org Product.
         */
        public function render_affiliate_box( $atts ) {

            $atts = shortcode_atts( array(
                'name' => null,
                'affiliate' => 'invalid',
                'affiliate_code' => 'invalid',
                'image' => null,
                'url' => null,
                'price' => null,
                'price_timestamp' => null,
                'rating' => null,
                'currency' => 'USD',
                'dynamic_update' => 'true',
                'availability' => 'InStock',
                'description' => null
            ), $atts );

            // Is dynamic_update on? If so, apply dynamic update.
            if( $atts[ 'dynamic_update' ] == 'true' ) {
                $atts = $this->dynamic_update( $atts );
            }

            // Output buffer so we can save HTML output and return it.
            ob_start();
            ?>
            <div class="affiliate-product" itemscope itemtype="http://schema.org/Product">
                <span itemprop="name"><?php echo esc_html( $atts[ 'name' ] ); ?></span>
                <img itemprop="image" src="<?php echo esc_attr( $atts[ 'image' ] ); ?>" alt='<?php echo esc_attr( $atts[ 'name' ] ); ?>' />
                <?php if( $atts[ 'rating' ] ) : ?>
                    <div itemprop="aggregateRating"
                      itemscope itemtype="http://schema.org/AggregateRating">
                      Rated <span itemprop="ratingValue">3.5</span>/5
                      based on <span itemprop="reviewCount">11</span> customer reviews
                    </div>
                <?php endif; ?>
                <?php if( $atts[ 'price' ] !== null ) : ?>
                    <div itemprop="offers" itemscope itemtype="http://schema.org/Offer">
                        <?php if( $atts[ 'currency' ] == 'USD' ) : ?>
                            <span itemprop="priceCurrency" content="USD">$</span><?php endif; ?><span itemprop="price" content="<?php echo esc_attr( $atts[ 'price' ] ); ?>"><?php echo esc_html( $atts[ 'price' ] ); ?></span>
                        <?php if( $atts[ 'availability' ] == 'InStock' ) : ?>
                            <link itemprop="availability" href="http://schema.org/InStock" />In stock
                        <?php endif; ?>
                        <?php if( isset( $atts[ 'price_timestamp' ] ) ) : ?>
                            <date class="price_timestamp" datetime="<?php echo esc_attr( $atts[ 'price_timestamp' ] ); ?>">Updated <?php
                                echo date( get_option( 'date_format' ), $atts[ 'price_timestamp' ] );
                                echo ' at ' . date( get_option( 'time_format' ), $atts[ 'price_timestamp' ] );
                            ?></date>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                <?php if( $atts[ 'description' ] != null ) : ?>
                    <span itemprop="description"><?php echo esc_html( $atts[ 'description' ] ); ?></span>
                <?php endif; ?>
                <a class="button" target="_blank" href="<?php echo esc_attr( $atts[ 'url' ] ); ?>">Buy Now</a>
                <?php if( $atts[ 'dynamic_update'] == 'true' && isset( $atts[ 'dynamic_update_error' ] ) ) : ?>
                    <!-- <?php echo esc_html( $atts[ 'dynamic_update_error' ] ); ?> -->
                <?php endif; ?>
            </div>
            <?php

            return ob_get_clean();
        }

        /**
        * Dynamically updates the shortcode attributes array with dynamic
        * results from the affiliate's website.
        *
        * @param array $atts The shortcode attributes array.
        * @return array Returns the modified shortcode attributes array.
         */
		public function dynamic_update( $atts = array() ) {

            // Grab the affiliate implementat.

            $affiliate = $this->get_affiliate( $atts[ 'affiliate' ] );

            // If no affiliate, record error and return original aray.
            if( $affiliate == null ) {

                $atts[ 'dynamic_update_error' ] = sprintf( 'No affiliate ' .
                    'configured named "%s".', $atts[ 'affiliate' ] );

                return $atts;
            }

            // Update array from affiliate.
            $atts = $affiliate->dynamic_update( $atts );

            return $atts;
        }
	}
}
