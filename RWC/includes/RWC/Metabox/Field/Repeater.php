<?php

/**
 * Contains the RWC\Metabox\Field\Date class.
 *
 * @author Brian Reich <breich@reich-consulting.net>
 * @copyright Copyright (C) 2016 Reich Consulting
 * @package RWC\Metabox
 */

namespace RWC\Metabox\Field {

    /**
	 * The RWC\Metabox\Field\Date renders a date field in a metabox.
	 *
	 * @author Brian Reich <breich@reich-consulting.net>
	 * @copyright Copyright (C) 2016 Reich Consulting
	 * @package RWC\Metabox
	 */
    class Repeater extends \RWC\Metabox\Field
    {
        /**
         * An array of initialized fields.
         *
         * @var    array
         * @access private
         */
        private $_fields;

        /**
         * Renders the input field
         *
         * @param \RWC\Metabox The Metabox to render.
         *
         * @return void
         */
        public function render( ) { ?>
            <div class="rwc-metabox-field-repeater">
                <div class="item-template">
                    <?php foreach( $this->get_repeated_fields() as $field ) : ?>
                        <?php $field->render(); ?>
                    <?php endforeach; ?>
                    <input class="remove"  type="button" value="Remove"/>
                </div>
                <?php foreach( $this->get_repeated_fields() as $id => $field ) : ?>
                    <?php $field->set_option( 'id', $field->get_option( 'id' ) . '[]' ); ?>
                <?php endforeach; ?>
                <?php $rows = $this->get_value(); ?>
                <?php if( ! empty( $rows ) ) : ?>
                    <?php foreach( $rows as $row ) :?>
                        <div class="rwc-metabox-repeater-row">
                            <?php foreach( $this->get_repeated_fields() as $id => $field ) : ?>
                                <?php $field->set_value( $row[ $id ] ); ?>
                                <?php $field->render(); ?>
                            <?php endforeach; ?>
                            <input class="remove"  type="button" value="Remove"/>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
                <input class="add" type="button" value="Add New" />
            </div>
            <?php wp_enqueue_script( 'rwc-metabox-field-repeater',
                $this->get_metabox()->get_library()->get_uri() .
                    '/js/rwc/metabox/field/repeater.js', array( 'jquery' ) ); ?>
            <?php wp_enqueue_style( 'rwc-metabox-field-repeater-css',
                $this->get_metabox()->get_library()->get_uri() .
                    '/css/rwc/metabox/field/repeater.css' ); ?>
        <?php }

        /**
         * Returns the default options for a text field.
         *
         * @return array Returns the default options for a text field.
         */
        public function get_default_options() {

            return array(
                'name'  => 'rcw-metabox-field-repeater-name',
                'id'    => 'rcw-metabox-field-repeater-name',
                'value' => null,
                'required' => false,
                'fields' => array()
            );
        }

        /**
         * Returns the repeater fields.
         *
         * @return array Returns a list of repeater fields.
         */
        private function get_repeated_fields() {

            $this->initialize_fields();
            return $this->_fields;
        }

        /**
         * Initializes the fields option to an array of Field objects.
         *
         * @return void
         */
        private function initialize_fields() {

            // Don't double-initialize.
            if( is_array( $this->_fields ) ) return;

            // Initialize the array.
            $this->_fields = array();

            // Iterate through all fields.
            foreach( $this->get_option( 'fields', array() ) as $id => $options ) {

                // Set on $fields list.
                $this->_fields[ $id ] = \RWC\Metabox\Field::get_instance(
                    $this->get_metabox(), $id, $options );
            }
        }

        private function set_from_meta( $value = array() ) {

            $this->set_option( 'value', $value );
        }
        /**
         * Sets the values for the repeater field.
         */
        public function set_value( $value, $values = array(), $abs = false ) {

            if( is_array( $value ) ) {
                $this->set_from_meta( $value );
                return;
            }

            // Retrieve array of all Field instances.
            $fields = $this->get_repeated_fields();

            // Will hold value for the repeater field.
            $repeaterValue = array();

            // Iterate through fields
            foreach( $fields as $id => $field ) {

                // Current field value.
                $current = array();

                // If the absolute flag is specified, get the absolute field
                // id within the metabox. Otherwise use the base field id.
                $fieldId = ( $abs ) ?
                    $this->get_metabox()->get_field_id( $id ) :
                    $id;

                // If the full list of submitted values has a field id,
                // use it.
                if( isset( $values[ $fieldId ] )) {

                    $fieldVal = $values[ $fieldId ];

                    // If the field value is an array, set values for all rows
                    // in repeater.
                    if( is_array( $fieldVal ) ) {

                        // Iterate through rows
                        for( $i = 0; $i < count( $fieldVal ); $i++ ) {

                            // If we don't already have a row for this index,
                            // create it.
                            if( count( $repeaterValue ) < $i ) {
                                $repeaterValue[$i] = array();
                            }

                            // Set the value on the field.
                            $field->set_value(  $fieldVal[$i] );

                            // Read it back into the repeater's value.
                            $repeaterValue[$i][ $id ] = $field->get_value();
                        }
                    }
                }
            }

            $this->set_option( 'value', $repeaterValue );
        }
    }

}
