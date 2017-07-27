<?php

/**
 * This file contains the RWC\Feature\Reviews class.
 *
 * @author Brian Reich <breich@reich-consulting.net>
 * @copyright Copyright (C) 2016 Reich Web Consulting
 * @version 1.0
 * @package RWC\Features
 */

namespace RWC\Features {
	
	/**
	 * Provides the ability to create and render reviews in Schema.org format.
	 *
	 * Reviews provide a review of an item.  The item can be a restaraunt, a
	 * store, a movie, a website, etc. Reviews are useful to website owners
	 * because, when reviews are rendered correctly, they provide valuable
	 * SEO benefits as well as social validation to prospective customers.
	 *
	 * @author Brian Reich <breich@reich-consulting.net>
	 * @copyright Copyright (C) 2016 Reich Web Consulting
	 * @version 1.0
	 * @package RWC\Features
	 * @see http://schema.org/Review
	 */
	class Reviews extends \RWC\Feature {
		
		
		///////////////////// CLASS CONSTANTS /////////////////////////////////
		
		
		/**
		 * The post type.
		 *
		 * @var string
		 */
		const POST_TYPE = 'rwc_reviews';
		
		
		///////////////////// END CLASS CONSTANTS /////////////////////////////
		
		
		///////////////////// PRIVATE PROPERTIES //////////////////////////////
		
		/**
		 * The Metabox containing all of the Reviews fields.
		 * 
		 * @var    \RWC\Metabox
		 * @access private
		 */
		private $metabox = null;
		
		///////////////////// END PRIVATE PROPERTIES //////////////////////////
		
		
		////////////////////////// PUBLIC METHODS /////////////////////////////
		
		
		/**
		 * Custom feature initialization.
		 *
		 * @constructor
		 */
		public function __construct( $options = array(), \RWC\Library $library ) {
			
			// Load defaults and initialize Feature.
			parent::__construct( \RWC\Utility::get_options( $options, array(
					// Default options go here.
			) ), $library );
			
		}
		
		/**
		 * Returns the Metabox containing custom Review fields.
		 * 
		 * @return \RWC\Metabox Returns the Metabox.
		 */
		public function get_metabox()
		{
			return $this->metabox;
		}
		
		/**
		 * Creates the custom post type for Reviews.
		 *
		 * Creates the custom post type for Reviews. Reviews will be available
		 * through the WordPress Dashboard to be edited by editors, admins, and
		 * other users able to create and manage content.
		 *
		 * @return void
		 */
		public function create_post_type()
		{
			// Register the post type.
			register_post_type( self::POST_TYPE, [
					'labels' => [
							'name' => __( 'Reviews' ),
							'singular_name' => __( 'Review' )
					],
					'description' => 'Reviews',
					'menu_icon' => 'dashicons-admin-comments',
					'supports' => array( 'title', 'editor', 'thumbnail', 'excerpt',
							'revisions' ),
					'public' => true,
					'has_archive' => true,
					'rewrite' => array(
							'slug' => __( 'reviews' ),
							'with_front' => false
					)
			] );
		}
		
		/**
		 * Flush rewrite rules on activation.
		 *
		 * @return void
		 */
		public function rewrite_flush()
		{
			
			// Register the post type.
			$this->create_post_type();
			
			// ATTENTION: This is *only* done during plugin activation hook in this example!
			// You should *NEVER EVER* do this on every page load!!
			flush_rewrite_rules();
		}
		
		/**
		 * Registers WordPress hooks for the Review feature.
		 *
		 * @return void
		 */
		public function initialize()
		{
			// Load custom post type
			add_action( 'init', array( $this, 'create_post_type' ) );
			
			// Flush rewrites when the plugin/theme that loaded
			// the library activates.
			register_activation_hook(
					$this->get_library()->get_activation_file() ,
					array( $this, 'rewrite_flush' ) );
			
			// Filter the content.
			add_filter( 'the_content', array( $this,
					'render_single_review' ), 10, 1 );
			
			// Register an \RWC\Features\Reviews\Review object when appropriate.
			add_action( 'the_post', array( $this, 'register_review_object' ) );
			
			// Register a shortcode for rendering reviews.
			add_shortcode( 'rwc_reviews_aggregate', array( $this, 'render_reviews_aggregate' ) );
			
			// Register a shortcode for rendering reviews.
			add_shortcode( 'rwc_reviews', array( $this, 'render_reviews' ) );
			
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_css'  ) );
			
			// Register a metabox with review fields.
			$this->metabox = new \RWC\Metabox( $this->get_library(), array(
					'renderer' => 'vertical-tabs',
					'id'       => 'rwc-reviews-metabox',
					'title'    => 'Review Properties',
					'post_types' => array( self::POST_TYPE ),
					'sections' => array(
						array(
							'id' => 'author',
							'name' => 'Author'
						),
						array(
							'id' => 'rating',
							'name' => 'Rating'
						),
					),
					'fields' => array(
						'author-name' => array(
							'type' => 'text',
							'section' => 'author',
							'name' => 'Author Name'
						),
						'review-rating' => array(
							'type' => 'dropdown',
							'section' => 'rating',
							'name' => 'Rating',
							'options' => array(
									'1' => '1 Star (Worst)',
									'2' => '2 Stars',
									'3' => '3 Stars',
									'4' => '4 Stars',
									'5' => '5 Stars (Best)'
							)
						),
						'review-date'  => array(
							'type' => 'date',
							'section' => 'rating',
							'name' => 'Date'
						)
					)
			) );
		}
		
		/**
		 * Load reviews CSS.
		 * 
		 * @return void
		 */
		public function enqueue_css()
		{
			wp_enqueue_style( 'rwc-reviews-css', 
				$this->get_library()->get_uri() . 
				'/css/rwc/features/reviews/reviews.css' );
		}
		
		/**
		 * Renders an AggregateRating block for all of the reviews.
		 * 
		 * This shortcode renderer will return a Schema.org compatible
		 * AggregateRating block for all Reviews that match the shortcode's
		 * criteria.
		 * 
		 * @param array $options An array of shortcode options.
		 * @return string Returns the AggregateRating HTML.
		 */
		public function render_reviews_aggregate( $options )
		{
			// Merge shortcode options with defaults.
			$options = \RWC\Utility::get_options( (array) $options, [
				'post_type' => self::POST_TYPE,
				'posts_per_page' => -1
			] );
			
			// Use options to generate query.
			$query = new \WP_Query( $options );
			
			// Render the template.
			$html = \RWC\Utility::get_include_content(
				'/features/reviews/aggregate.php', [

					'query' => $query
			] );
			
			// Reset the query.
			wp_reset_postdata();
			
			return $html;
		}
		
		/**
		 * Renders a list of all Reviews that match the shortcode options.
		 *
		 * This shortcode renderer will return a list of Schema.org compatible
		 * Reviews for all Reviews that match the shortcode's criteria.
		 *
		 * @param array $options An array of shortcode options.
		 * @return string Returns the AggregateRating HTML.
		 */
		public function render_reviews( $options )
		{
			// Merge shortcode options with defaults.
			$options = \RWC\Utility::get_options( (array) $options, [
					'post_type' => self::POST_TYPE,
					'posts_per_page' => -1
			] );
			
			// Use options to generate query.
			$query = new \WP_Query( $options );
			
			// Render the template.
			$html = \RWC\Utility::get_include_content(
					'/features/reviews/loop.php', [
							
							'query' => $query
					] );
			
			// Reset the query.
			wp_reset_postdata();
			
			return $html;
		}
		
		/**
		 * Registers a global variable named "review" that will contain an
		 * RWC\Features\Reviews\Post object that wraps the current post.
		 
		 * @param \WP_Post|null $post The current post, or null to use the default.
		 *
		 * @return void
		 */
		public function register_review_object( $post = null ) {
			
			// If no post is specified, use the current global post.
			$post = is_null( $post ) ? $GLOBALS[ 'post' ] : $post;
			
			// If there's no post to wrap, don't bother.
			if( is_null( $post ) ) {
				return;
			}
			
			// If it's the right post type, register a Reviews\Post wrapper.
			if( $post->post_type == self::POST_TYPE ) {
				
				$GLOBALS[ 'review' ] = new \RWC\Features\Reviews\Post( $post );
			}
		}
		
		/**
		 * Renders a single review in schema.org compatible markup.
		 * 
		 * The render_single_review method executes as a filter on "the_content"
		 * and overrides the content with Schema.org compatible markup for the
		 * review.
		 * 
		 * @param string $content The default content for the Review.
		 * @return string Returns the modified content.
		 */
		public function render_single_review( $content )
		{
			global $review;
			
			// Ignore everything else.
			if( false === is_singular( self::POST_TYPE ) ) return $content;
			
			// Unregister myself
			remove_filter( 'the_content', array( $this, 'render_single_review'), 10, 1 );
			
			$html = \RWC\Utility::get_include_content(
				'/features/reviews/single-content.php', [
					'review' => $review,
				] );
			
			// Reregister myself
			add_filter( 'the_content', array( $this,
			'render_single_review' ), 10, 1 );
			
			return $html;
		}
		
		///////////////////// END PUBLIC METHODS //////////////////////////////
		
		
		///////////////////// PRIVATE METHODS /////////////////////////////////
		
		
		
		
		///////////////////// END PRIVATE METHODS /////////////////////////////
		
		
	}
}
