<?php

/**
 * Contains the RWC_Post_Location class.
 *
 * @author Brian Reich <breich@reich-consulting.net>
 * @copyright Copyright (C) 2016 Reich Consulting
 * @package RWC\Features
 */

namespace RWC\Features {

	/**
	 * The RWC\Feature\PostLocation allows the user to associate a location with a post,
	 * page, or custom post type.
	 *
	 * The following configuration options are available for the PostLocation
	 * feature. The "post_types" option is used to specify an array of post
	 * types that will utilized the PostLocation feature. The metabox_title
	 * option is used to specify the title of the Post Location metabox for
	 * specific post types. The metabox_default_title option is used to specify
	 * the default title of the metabox, when one is not specified for a
	 * particular post type.
	 *
	 * Example configuration:
	 * array (
	 *     'post_types ' => array( 'rwc_real_estate', 'post' ),
	 *     'metabox_title' => array( 'rwc_real_estate' => 'Property Location' ),
	 *	   'metabox_default_title' => 'Location'
	 * )
	 *
	 * @author Brian Reich <breich@reich-consulting.net>
	 * @copyright Copyright (C) 2016 Reich Consulting
	 * @package FC_Client
	 */
	class PostLocation extends \RWC\Feature {

		/**
		 * The GoogleMaps instance.
		 *
		 * @var    RWC_GoogleMaps
		 * @access protected
		 */
		protected $_google_maps;

		/**
		 * Returns an array of dependancies that loads the GoogleMaps Feature.
		 *
		 * @return array Returns an array of dependancies.
		 */
		public function get_dependancies() {

			return array(
				'GoogleMaps'
			);
		}

		/**
		 * Returns the metabox title for a particularl post type.
		 *
		 * Returns the metabox title for a particular post type.  If a custom
		 * title has been specified for the post type via the "metabox_title"
		 * option, it will be used. If not, the default metabox title in the
		 * metabox_default_title option will be used.
		 *
		 * @param string|null $post_type The post type.
		 *
		 * @return string Returns the metabox title.
		 */
		private function get_metabox_title( $post_type = null ) {

			// Has a post type been specified?
			if( ! empty( $post_type ) ) {

				// Get custom titles.
				$titles = $this->get_option( 'metabox_title' );

				// Has a specific title been set for this post type?
				if( isset( $titles[ $post_type ]) ) {

					// Yes: return custom title.
					return __( $titles[ $post_type ], 'RWC_Features_PostLocation' );
				}
			}

			// No custom title specified.
			return __( $this->get_option( 'metabox_default_title' ),
				'RWC_Features_PostLocation' );
		}

		/**
		 * Initializes the PostLocations Feature.
		 *
		 * @return void
		 */
		public function initialize() {

			// Merge in default options.
			$this->_options = array_merge( array(

				// A list of post types that the PostLocation Feature applies to
				'post_types' => array(),

				// Default title for Post Location metabox.
				'metabox_default_title' => 'Location',

				// An array of metabox titles for specific post types
				'metabox_title' => array(),

			), $this->_options );

			// Ensure that post_types is an array.
			if( ! is_array( $this->_options[ 'post_types' ] ) ) {
				$this->_options[ 'post_types' ] = array( $this->_options[ 'post_types' ] );
			}

			add_action( 'add_meta_boxes', array( $this, 'add_location_metabox' ) );
			add_action( 'save_post', array( $this, 'save_location_metabox' ) );

			add_action( 'admin_enqueue_scripts', array( $this, 'load_admin_styles' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'load_admin_scripts' ) );
		}

		/**
		 * Returns the Google Maps instance.
		 *
		 * @return \RWC\Features\GoogleMaps Returns the GoogleMaps Feature.
		 */
		public function get_google_maps() {

			$googleMaps = $this->get_library()->get_loaded_feature( 'GoogleMaps' );

			// Make sure Google Maps feature is loaded.
			if( is_null( $googleMaps ) ) {
				throw new \RWC\Exception( 'Google Maps Feature has not been loaded.' );
			}

			return $googleMaps;
		}

		/**
		 * Loads admin scripts for the Post Location feature.
		 *
		 * @return void
		 */
		public function load_admin_scripts() {

			$screen = get_current_screen();

			// If the current screen is add or edit for any of the supported post
			// types, load the scripts.
			if( in_array( $screen->post_type, $this->_options[ 'post_types' ] ) &&
			  ( $screen->action == 'edit' || $screen->base == 'post' ) ) {

				wp_enqueue_script(
					'rc-admin-location-js',
					$this->get_library()->get_uri() . '/js/rwc/features/post-location-admin.js',
					array( 'jquery', 'jquery-ui-tabs', 'jquery-ui-tooltip' )
				);
			}
		}

		/**
		 * Load styles for WordPress Dashboard.
		 *
		 * @return void
		 */
		public function load_admin_styles() {

			// Load CSS for Client Metabox.
	        wp_enqueue_style(
				'fc-admin-clients-css',
				$this->get_library()->get_uri() .
				'/css/rwc/features/post-location-admin.css', false, '1.0.0' );
		}

		/**
		 * Adds the metabox.
		 *
		 * Adds the metabox for each of the post types that has been registered
		 * with the PostLocation feature via the post_types option.
		 *
		 * @return void
		 */
		public function add_location_metabox() {

			// Add metabox for each post type.
			foreach( $this->get_option( 'post_types', array() ) as $post_type ) {

				add_meta_box(
					'rc_location_meta',
					$this->get_metabox_title( $post_type ),
					array( $this, 'render_location_metabox' ),
					$post_type
				);
			}
		}

		/**
		 * Renders the Post Location metabox.
		 *
		 * The post location metabox will have a nonce that must be validated
		 * on save. It will also provide fields for latitude, longitude, and
		 * an address that can be used to automatically calculate determine
		 * the latitude and longitude using Google Maps.
		 *
		 * @param WP_Post $post The post being edited.
		 */
		public function render_location_metabox( $post ) {

			wp_nonce_field( basename( __FILE__ ), 'post_location_nonce' );

			printf('
				<section>
					<section class="fields">
						<section class="post-location">
							<label for="post_address">Address:</label>
							<textarea name="post_address" id="post_address" title="%s">%s</textarea>
							<button class="geolocate button button-large" title="%s">Geolocate</button>
						</section>
						<section id="geolocate">
							<label for="post_latitude">Latitude:</label></th>
							<input type="text" name="post_latitude" id="post_latitude" value="%s" title="%s"/>
							<label for="post_longitude">Longitude:</label>
							<input type="text" name="post_longitude" id="post_longitude" value="%s" />
						</section>
					</section>
					<section class="map">
						<div id="rwc-feature-postlocation-google-map"></div>
					</section>
					<section class="instructions">
						<div><strong>Instructions</strong></div>
						<p>Use the <em>Address</em> field to specify the street
						address for this post, then click <em>Geolocate</em>
						to find the map coordinates of the location. If the
						address cannot be geocoded, you can manually enter the
						latutide and longitude into the fields provided.</p>
					</section>
				</section>',
				esc_html( __( 'Use the address field to specify a location for this post.', 'RWC_Features_PostLocation' ) ),
				get_post_meta( $post->ID, 'post_address', true ),
				esc_html( __( 'Click Geocode to convert your address to a map location.' ) ),
				get_post_meta( $post->ID, 'post_latitude', true ),
				esc_html( __( 'Change this field to manually set the latitude.', 'RWC_Features_PostLocation') ),
				get_post_meta( $post->ID, 'post_longitude', true ),
				esc_html( __( 'Change this field to manually set the longitude.', 'RWC_Features_PostLocation') )
			);
		}

		/**
		 * Saves the Location metabox.
		 *
		 * If the Location metabox nonce is verified and we're not on an autosave
		 * or a revision, the address, latitude, and longitude fields will be saved
		 * to the metadata for the current post.
		 *
		 * @param int $post_id The unique id of the post.
		 */
		public function save_location_metabox( $post_id ) {

			// Verify that we should be saving.
			$is_autosave = wp_is_post_autosave( $post_id );
			$is_revision = wp_is_post_revision( $post_id );
			$is_valid_nonce = ( isset( $_POST[ 'post_location_nonce' ] )
			  && wp_verify_nonce( $_POST[ 'post_location_nonce' ],
			  basename( __FILE__ ) ) ) ? true : false;

			// Exits script depending on save status
			if ( $is_autosave || $is_revision || ! $is_valid_nonce ) {
				return;
			}

			global $post;

			update_post_meta( $post_id, 'post_address',  $_POST[ 'post_address' ] );
			update_post_meta( $post_id, 'post_latitude',  $_POST[ 'post_latitude' ] );
			update_post_meta( $post_id, 'post_longitude',  $_POST[ 'post_longitude' ] );
		}

	}
}
