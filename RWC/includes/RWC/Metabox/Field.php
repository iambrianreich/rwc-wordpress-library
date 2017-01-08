<?php

/**
 * Contains the RWC\Metabox\Field class.
 *
 * @author Brian Reich <breich@reich-consulting.net>
 * @copyright Copyright (C) 2016 Reich Consulting
 * @package RWC\Metabox
 */

namespace RWC\Metabox {

    /**
	 * The RWC\Metabox\Field class is an abstract class that specifies the
     * methods required to render a metabox field. Concrete extensions of Field
     * will specify their own rendering implementations.
	 *
	 * @author Brian Reich <breich@reich-consulting.net>
	 * @copyright Copyright (C) 2016 Reich Consulting
	 * @package RWC\Metabox
	 */
    abstract class Field extends \RWC\Object
    {
        /**
         * Initialize the Field.
         *
         * Initialize the Field. The constructor will call the initialize()
         * method, which gives subclasses the chance to do custom initialization.
         *
         * @param array $options An array of configuration options.
         * @constructor
         */
        public function __construct( $options = array(), \RWC\Metabox $metabox = null ) {

            // Call parent constructor
            parent::__construct( $options );

            // Set the metabox, if one was specified.
            $this->set_metabox( $metabox );

            // Initialize the Renderer
            $this->initialize();
        }

        /**
         * Initializes the Field. This method can be overridden by subclasses
         * to perform custom initialization.
         *
         * @return void
         */
        public function initialize() {}


        public function set_metabox( \RWC\Metabox $metabox ) {

            $this->_options[ 'metabox' ] = $metabox;
        }

        public function get_metabox() {

            return $this->get_option( 'metabox', null );
        }

        /**
         * Sets the field value.
         *
         * @param mixed $value  The value of the field.
         * @param array $values All submitted values.
         *
         * @return void
         */
        public function set_value( $value, $values = array() ) {

            $this->set_option( 'value', $value );
        }

        /**
         * Returns the field value.
         *
         * Returns the field value. The field value is the value of the "value"
         * option specified in the Field configuration.
         *
         * @return string Returns the field value.
         */
        public function get_value() {

            return $this->get_option( 'value', null );
        }

        /**
         * Returns the id of the field.
         *
         * Returns the id of the field. The fielf id is returned as the Metabox
         * prefix plus the value of the "id" option.
         *
         * @return string Returns the id of the field.
         */
        public function get_id() {

            return $this->get_metabox()->get_field_id(
                $this->get_option( 'id'  ) );
        }

        /**
         * Renders the Metabox contents.
         *
         * @param \RWC\Metabox The Metabox to render.
         *
         * @return void
         */
        public abstract function render();

        /**
         * Returns an concrete field instance.
         *
         * @param \RWC\Metabox $metabox The metabox the field belongs to.
         * @param string       $id      The id of the field.
         * @param array        $options The options array.
         *
         * @return \RWC\Metabox\Field Returns the static field instance.
         */
        public static function get_instance(\RWC\Metabox $metabox, $id, $options ) {

            // Make sure a type is specified.
            if( ! isset( $options[ 'type' ] ) ) {
                throw new Exception( sprintf( 'No type specified for field %s.',
                    $id ) );
            }

            // If id option isn't set in options, automatically set it.
            if( ! isset( $options[ 'id' ] )) $options[ 'id' ] = $id;

            // Load the field class.
            $class = '\RWC\Metabox\Field\\' .
                \RWC\Strings::get_camel_case( $options[ 'type'] );

            return new $class( $options, $metabox );
        }
    }

}
