<?php

/**
 * Contains the RWC\Metabox\Storage\Abstract class.
 *
 * @author Brian Reich <breich@reich-consulting.net>
 * @copyright Copyright (C) 2016 Reich Consulting
 * @package RWC\Metabox
 */

namespace RWC\Metabox\Storage {

    /**
    * An RWC\Metabox\Storage specifies a storage strategy for Metabox data
    * which can be used store and retrieve fields specified in a particular
    * metabox.
    *
    * @author Brian Reich <breich@reich-consulting.net>
    * @copyright Copyright (C) 2016 Reich Consulting
    * @package RWC\Metabox
    */
   abstract class StorageAbstract extends \RWC\Object {

        /**
        * Sets the default metabox on which this Storage instance will operate.
        *
        * @param \RWC\Metabox $metabox The Metabox to store/retrieve.
        */
        public function set_metabox( \RWC\Metabox $metabox ) {

          $this->set_option( 'metabox', $metabox );
        }

        /**
        * Returns the metabox on hich this Storage instance will operate.
        *
        * @return \RWC\Metabox Returns the Metabox to store and retrieve.
        */
        public function get_metabox() {

          return $this->get_option( 'metabox' );
        }

        /**
         * Stores the POST data for the specified post id and Metabox.
         *
         * The store() method's job is to take an array of POST data and store
         * all of the data associated with the specified (or previously set)
         * metabox for an individual post. The $post_id parameter specifeis the
         * WordPress post the data belongs to. The $post parameter specifies an
         * array of data, potentially from $_POST, that contains the Metabox
         * values to store. The $metabox parameter specified the Metabox used
         * to determine which values should be stored. If no Metabox is
         * specified, the preconfigured Metabox (set via configuration or in
         * set_metabox()) will be used.
         *
         * @param int              $post_id The unique id of the WordPress post.
         * @param array            $post    The POST data containing metabox values.
         * @param RWC\Metabox|null $metabox The metabox to store, or null.
         *
         * @return void
         * @throws \RWC\Metabox\Exception If storage error or no Metabox specified.
         */
        public abstract function store( $post_id, $post, \RWC\Metabox $metabox = null );

        /**
         * Loads data from storage into the specified Metabox for the specified
         * WordPress Post.
         *
         * The $post_id parameter specifies the WordPress post whose metabox
         * values should be retrieved. The $metabox parameter specifies the
         * Metabox to load. If no Metabox is specified, the Metabox set (either
         * via configuration or set_metabox()) will be used. If no Metabox is
         * specified via either method, an Exception will be thrown. If data
         * retrieval fails, an Exception will be thrown. If retrieval is
         * successful the retrieved Metabox values will be assigned to the
         * Metabox.
         *
         * @param int              $post_id The unique id of the WordPress post.
         * @param RWC\Metabox|null $metabox The metabox to store, or null.
         *
         * @return void
         * @throws \RWC\Metabox\Exception if retrieval fails, or no Metabox specified.
         */
        public abstract function load( $post_id, \RWC\Metabox $metabox = null );

        /**
         * Helper method which returns the metabox parameter or, if not
         * specified, the configured Metabox. If a Metabox has not been assigned
         * through either method, an Exception will be thrown. specifying that
         * there is no Metabox configured.
         *
         * @param \RWC\Metabox $metabox The Metabox to use if not null.
         *
         * @return \RWC\Metabox Returns a Metabox.
         * @throws \RWC\Metabox\Storage\Exception if no Metabox.
         */
        protected function _metabox( \RWC\Metabox $metabox = null ) {

            // If none was specified, grab configured metabox.
            if( $metabox == null) {
                $metabox = $this->get_metabox();
            }

            // If no configured metabox, we got bigger issues.
            if( $metabox == null ) {
                throw new Exception( 'No Metabox specified in parameter and ' .
                    'no Metabox has been configured for this storage adapter.' );
            }

            // Return metabox.
            return $metabox;
        }
    }
}
