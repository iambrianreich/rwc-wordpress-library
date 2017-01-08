<?php

/**
 * Contains the RWC_Post_Location class.
 *
 * @author Brian Reich <breich@reich-consulting.net>
 * @copyright Copyright (C) 2016 Reich Consulting
 * @package RWC_Post_Location
 */

namespace RWC\Post {

	/**
	 * Require the Google Maps plugin.
	 */
	require_once( 'RWC/GoogleMaps.php' );

	/**
	 * The RWC_Post_Location allows the user to associate a location with a post,
	 * page, or custom post type.
	 *
	 * @author Brian Reich <breich@reich-consulting.net>
	 * @copyright Copyright (C) 2016 Reich Consulting
	 * @package FC_Client
	 */
	class Location {

		/**
		 * An array of post types that will get the Location metabox.
		 *
		 * @var    array
		 * @access protected
		 */
		protected $post_types;

		/**
		 * The GoogleMaps instance.
		 *
		 * @var    RWC_GoogleMaps
		 * @access protected
		 */
		protected $_google_maps;

		/**
		 * Initialize WordPress hooks.
		 *
		 * @constructor
		 */
		public function __construct( \RWC\GoogleMaps $google_maps = null ) {

			$this->post_types = array();
			$this->set_google_maps( $google_maps );

			add_action( 'add_meta_boxes', array( $this, 'add_location_metabox' ) );
			add_action( 'save_post', array( $this, 'save_location_metabox' ) );

			add_action( 'admin_enqueue_scripts', array( $this, 'load_admin_styles' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'load_admin_scripts' ) );
		}

		/**
		 * Sets the RWC\GoogleMaps instance. If none, one will be created.
		 *
		 * @param RWC\GoogleMaps $google_maps The Google Maps instance.
		 *
		 * @return void
		 */
		public function set_google_maps( $google_maps = null ) {

			// If one isn't specified, create a default.
			if( is_null( $google_maps ) ) {

				$google_maps = new \RWC\GoogleMaps();
			}

			$this->_google_maps = $google_maps;
		}

		/**
		 * Returns the Google Maps instance.
		 *
		 * @return RWC_GoogleMaps Returns the GoogleMaps instance.
		 */
		public function get_google_maps() {

			return $this->_google_maps;
		}

		/**
		 * Adds a post type to the list of post types that will get the Location
		 * metabox.
		 *
		 * @param string $type The post type to get the Location metabox.
		 */
		public function add_post_type( $type ) {

			$this->post_types[] = $type;
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
			if( in_array( $screen->post_type, $this->post_types) &&
			  ( $screen->action == 'edit' || $screen->base == 'post' ) ) {

				wp_enqueue_script(
					'rc-admin-location-js',
					get_stylesheet_directory_uri() . '/js/post-location-admin.js',
					array( 'jquery' )
				);

				$this->get_google_maps()->enqueue();
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
				get_stylesheet_directory_uri() . '/css/post-location-admin.css', false, '1.0.0' );
		}

		/**
		 * Adds the metabox for Project data.
		 *
		 * @return void
		 */
		public function add_location_metabox() {

			add_meta_box(
				'rc_location_meta',
				__( 'Location' ),
				array( $this, 'render_location_metabox' ),
				$this->post_types
			);
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

							<h2>Post Location</h2>

							<table>
								<tr>
									<th><label for="post_address">Address:</label></th>
									<td>
										<textarea name="post_address" id="post_address">%s</textarea>

										<p>Use the address field to specify a lcoation for this post. We\'ll
										try to convert the post\'s location to an address. If we have trouble you
										can use the geolocation fields to set the post\'s latitude and longitude
										manually.</p>
									</td>
								</tr>
							</table>
						</section>
						<section id="geolocate">

							<h2>Geolocation</h2>

							<table>
								<tr>
									<th><label for="post_latitude">Latitude:</label></th>
									<td>
										<input type="text" name="post_latitude" id="post_latitude" value="%s" />
									</td>
								</tr>
								<tr>
									<th><label for="post_longitude">Longitude:</label></th>
									<td>
										<input type="text" name="post_longitude" id="post_longitude" value="%s" />

										<p>Use these fields to specify the global positioning of the post. Click
										<a id="geocode" href="#">here</a> to attempt to
										geocode the post from it\'s address, or manually specify the
										latitude and longitude below by typing, or by moving their
										marker around the map.</p>
									</td>
								</tr>
							</table>
						</section>
					</section>
					<section id="map">
						<div id="google-map"></div>
					</section>
				</section>',
				get_post_meta( $post->ID, 'post_address', true ),
				get_post_meta( $post->ID, 'post_latitude', true ),
				get_post_meta( $post->ID, 'post_longitude', true )
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
