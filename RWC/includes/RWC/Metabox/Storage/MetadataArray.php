<?php

/**
 * Contains the RWC\Metabox\Storage\MetadataArray class.
 *
 * @author Brian Reich <breich@reich-consulting.net>
 * @copyright Copyright (C) 2016 Reich Consulting
 * @package RWC\Metabox
 */

namespace RWC\Metabox\Storage {

  /**
   * The MetadataArray Storage implementation storage all of the metabox's
   * values in a single post meta field as an associative array. This storage
   * implementation is fast and efficient and useful for large blobs of data
   * that don't need to be individually queried or sorted-on later.
   *
   * @author Brian Reich <breich@reich-consulting.net>
   * @copyright Copyright (C) 2016 Reich Consulting
   * @package RWC\Metabox
   */
  class MetadataArray extends \RWC\Metabox\Storage\StorageAbstract {

    /**
     * Stores the
     */
    public function store( $post_id, $post, \RWC\Metabox $metabox = null )
    {
        $metabox = $this->_metabox( $metabox );

        // Get the values of all of the Metabox fields.
        $values = $metabox->get_values();

        // Save array of values into post_meta under id of metabox.
        update_post_meta(
            $post_id,
            $metabox->get_option( 'id' ),
            $values
        );

    }

    /**
     * Assigns data to all of the metabox fields from a single array stored in
     * the post meta for this metabox.
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
            $metabox->get_option( 'id' ),
            true
        );

        // Get all the metabox fields
        $fields = $metabox->get_fields();

        // Go through all fields
        foreach( $fields as $id => $field ) {

            // If there is a value for the field, set it.
            if( isset( $values[ $id ] ) ) {

                $field->set_value( $values[ $id ], $values );
            }
        }
    }

  }
}
