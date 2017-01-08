<?php

/**
 * Contains the RWC\Metabox\Storage\MetadataFields class.
 *
 * @author Brian Reich <breich@reich-consulting.net>
 * @copyright Copyright (C) 2016 Reich Consulting
 * @package RWC\Metabox
 */

namespace RWC\Metabox\Storage {

  /**
   * The MetadataFields Storage implementation storage each field in the
   * metabox in an
   *
   * @author Brian Reich <breich@reich-consulting.net>
   * @copyright Copyright (C) 2016 Reich Consulting
   * @package RWC\Metabox
   */
  class MetadataFields extends \RWC\Metabox\Storage\StorageAbstract {

        /**
         * Stores the Metabox's fields in individual post meta fields.
         *
         * @param \RWC\Metabox $metabox The metabox to store.
         * @param int          $post_id The unique id of the post to store.
         * @param \WP_Post     $post    The WordPress Post object.
         *
         * @return void
         */
        public function store( $post_id, $post, \RWC\Metabox $metabox = null )
        {
            $metabox = $this->_metabox( $metabox );

            // Get the values of all of the Metabox fields.
            $values = $metabox->get_values();

            // Save array of values into post_meta under id of metabox.
            foreach( $values as $k => $v ) {

                // meta field name is <metabox_id>_<field_name>
                $metaname = $this->get_post_meta_name( $k, $metabox );

                // Save it in the post meta.
                update_post_meta( $post_id, $metaname, $v );
            }
        }

        /**
         * Returns the name of the post meta field for the specific Metabox and
         * field name.
         *
         * @param \RWC\Metabox $metabox The metabox to store.
         * @param string       $field   The name of the field.
         *
         * @return string Returns the full post meta field name.
         */
        private function get_post_meta_name( $field, \RWC\Metabox $metabox = null ) {

            $metabox = $this->_metabox( $metabox );

            return $metabox->get_option( 'id' ) . '_' . $field;
        }

        /**
         * Assigns data to all of the metabox fields from individual post meta
         * values.
         *
         * @param RWC\Metabox $metabox The metabox whose data should be loaded.
         * @param int         $post_id The unique id of the post to load.
         *
         * @return void
         */
        public function load( $post_id, \RWC\Metabox $metabox = null )
        {
            $metabox = $this->_metabox( $metabox );

            // Get the metadata blob for this metabox.
            $values = get_post_meta(
                $post_id,
                '',
                true
            );

            // Get all the metabox fields
            $fields = $metabox->get_fields();

            // Go through all fields
            foreach( $fields as $id => $field ) {

                // meta field name is <metabox_id>_<field_name>
                $metaname = $this->get_post_meta_name( $id, $metabox );

                // If there is a value for the field, set it.
                if( isset( $values[ $metaname ] ) ) {

                    $field->set_value( $values[ $metaname ][0], $values );
                }
            }
        }

        // TODO Add to parent abstract class and implement in other storage adapters.

        /**
         * Returns the value of a single metabox field.
         *
         * Returns the value of a single metabox field.
         */
        public function get( $post_id, $field, \RWC\Metabox $metabox = null ) {

            $metabox = $this->_metabox( $metabox );

            return get_post_meta(
                $post_id,
                $this->get_post_meta_name( $field, $metabox ),
                true );
        }
    }
}
