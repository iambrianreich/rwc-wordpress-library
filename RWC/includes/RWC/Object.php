<?php

namespace RWC {

    class Object {

        /**
         * A list of options associated with this Feature.
         *
         * @var    array
         * @access protected
         */
        protected $_options;


        /**
         * Creates a new RWC\Object.
         *
         * Creates a new RWC\Object. Merges in options, retrieved from the
         * get_default_options() method, and sets the resulting array using
         * set_options().
         *
         * @param array $options An array of configuration options.
         *
         * @constructor
         */
        public function __construct( $options = array() ) {

            // Set the options.
            $this->set_options( $options );
        }

        /**
         * Returns an array of default options.
         *
         * Returns an array of default options. Override this method in
         * subclasses to specify custom default objects for specific types.
         *
         * @return array Returns an array of default options.
         */
        public function get_default_options() {

            return array();
        }

        /**
         * Sets a list of configuration options.
         *
         * Sets a list of configuration options. Merges in options, retrieved
         * from the get_default_options() method, and sets the resulting array.
         *
         * @param array $options An array of configuration options.
         *
         * @return void
         */
        public function set_options( $options = array() ) {

            // If its not an array, make it an array.
            if( ! is_array( $options ) ) {

                $options = array( $options );
            }

            // Merge in options with default.
            $options = array_merge( $this->get_default_options(), $options );

            // Sets the options array.
            $this->_options = $options;
        }

        /**
         * Returns the specified option, or $default if it has not
         * been specified (null by default).
         *
         * @param string $name The name of the option to retrieve.
         *
         * @return mixed Returns the option value, or the default return value.
         */
        public function get_option( $name, $default = null ) {

            return isset( $this->_options[ $name ] ) ?
                $this->_options[ $name ] : $default;
        }

        /**
         * Sets the value of the specified option.
         *
         * @param string $name  The name of the option.
         * @param mixed  $value The new value for the option.
         *
         * @return void
         */
        public function set_option( $name, $value ) {

            $this->_options[ $name ] = $value;
        }
    }
}
