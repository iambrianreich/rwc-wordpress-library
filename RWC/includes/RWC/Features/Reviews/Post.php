<?php

/**
 * This file contains the RWC\Feature\Reviews\Post class.
 *
 * @author Brian Reich <breich@reich-consulting.net>
 * @copyright Copyright (C) 2016 Reich Web Consulting
 * @version 1.0
 * @package RWC\Features
 */

namespace RWC\Features\Reviews {
	
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
	class Post extends \RWC\PostWrapper 
	{
	
		/**
		 * Returns the name of the author.
		 *
		 * @return string Returns the name of the author.
		 */
		public function get_author_name()
		{
			return $this->get_meta_value( 'author-name' );
		}
		
		/**
		 * Returns the review rating.
		 * 
		 * @return int Returns the review rating.
		 */
		public function get_review_rating()
		{
			return intval( $this->get_meta_value( 'review-rating' ) );
		}
		
		/**
		 * Returns the review rating label ("X Stars").
		 *
		 * @return int Returns the review rating.
		 */
		public function get_review_rating_label()
		{
			return $this->get_review_rating() . ' Stars';
		}
		
		/**
		 * Returns the maximum review rating.
		 * 
		 * @return int Returns the maximum review rating.
		 */
		public function get_max_rating()
		{
			return 5;
		}
		
		/**
		 * Returns the minimum review rating.
		 * 
		 * @return int
		 */
		public function get_min_rating()
		{
			return 1;
		}
		
		///////////////////// PRIVATE METHODS /////////////////////////////////
		
		/**
		 * Returns a field from the real estate metadata.
		 *
		 * @param string $name    The name of the real estate metadata field.
		 * @param mixed  $default The value to return if the meta is not set.
		 *
		 * @return mixed Returns the metadata value.
		 */
		private function get_meta_value( $name, $default = false )
		{
			
			// Get the value from the metabox storage.
			$value = $this->get_storage()->get( $this->get_post()->ID, $name );
			
			// If no value set, return default.
			if( $value === false ) {
				
				return $default;
			}
			
			// Return value.
			return $value;
		}
		
		/**
		 * Returns the metabox storage.
		 * 
		 * @throws Exception
		 * @return \RWC\Metabox\Storage\StorageAbstract Returns the storage.
		 */
		private function get_storage()
		{
			// Get the RealEstate feature
			$reviews = \RWC\Library::load()->get_loaded_feature( 'Reviews' );
			
			// If we can't find it, well.. that's a major issue.
			if( $reviews== null ) {
				
				throw new Exception( 'Cannot get meta value because the ' .
					'Reviews feature is not loaded.' );
			}
			
			// Get the metabox and metabox storage.
			$metabox = $reviews->get_metabox();
			
			return $metabox->get_storage();
		}
		
		
		///////////////////// END PRIVATE METHODS /////////////////////////////
		
		
	}
}