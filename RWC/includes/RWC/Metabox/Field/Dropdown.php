<?php

/**
 * Contains the RWC\Metabox\Field\Dropdown class.
 *
 * @author Brian Reich <breich@reich-consulting.net>
 * @copyright Copyright (C) 2016 Reich Consulting
 * @package RWC\Metabox
 */

namespace RWC\Metabox\Field {

    /**
	 * The RWC\Metabox\Field\Dropdown renders a Dropdown field in a metabox.
	 *
	 * @author Brian Reich <breich@reich-consulting.net>
	 * @copyright Copyright (C) 2016 Reich Consulting
	 * @package RWC\Metabox
	 */
    class Dropdown extends \RWC\Metabox\Field
    {
        /**
         * Returns the name of this Dropdown field.  If the Dropdown is a single
         * value dropdown (the default), the id will be the id specified in the
         * configuration. If the Dropdown is configured to accept multiple
         * values, the name will end with "[]", to make it an array.
         *
         * @return string Returns the name of the field.
         */
        public function get_name() {

            return $id = parent::get_id() .
                ($this->get_option( 'multiple' ) ? '[]' : '' );
        }

        /**
         * Renders the Metabox contents.
         *
         * @param \RWC\Metabox The Metabox to render.
         *
         * @return void
         */
        public function render( ) { ?>
            <select
              name="<?php echo esc_attr($this->get_name()); ?>"
              id="<?php echo esc_attr($this->get_id()); ?>"
              <?php if( $this->get_option( 'multiple', false ) ) : ?>multiple="multiple"<?php endif; ?>>
                <?php $this->get_select_options(); ?>
            </select>

        <?php }

        public function get_default_options() {

            return array(
                'name'  => 'rcw-metabox-field-dropdown-name',
                'id'    => 'rcw-metabox-field-dropdown-name',
                'value' => null,
                'options' => array(),
                'heading' => 'Select...',
                'required' => false,
                'dropdown' => false
            );
        }

        /**
         * Echoes the
         */
        private function get_select_options() {

            // Get select options from config.
            $options = $this->get_option( 'options', array() );

            // If this is a callable, execute it.
            if( is_callable( $options ) ) $options = $options();

            // If options array isn't associative, make it associative.
            if( ! $this->is_assoc( $this->get_option( 'options', array() ) ) ) {

                // Make value the key too.
                $tmp = array();
                foreach( $options as $option ) $tmp[ $option ] = $option;
                $options = $tmp;
            }

            foreach( $options as $k => $v ) { ?>
                <option value="<?php echo esc_attr( $k ); ?>"
                    <?php if( $this->is_selected( $k ) ) : ?>selected="selected"<?php endif; ?>><?php
                    echo esc_html( $v ); ?></option>
            <?php }
        }

        /**
         * Returns true if the specified value is selected in the Dropdown.
         *
         * @return boolean Returns true if the value is selected.
         */
        private function is_selected( $value ) {

            // If Dropdown value is an array, check if $value is in array.
            if( is_array( $this->get_value() ) ) {
                return in_array( $value, $this->get_value() );
            }

            // Not an array. Simple compare.
            return $this->get_value() == $value;
        }

        /**
         * Returns true if the specified array is associative.
         *
         * Returns true if the specified array is associative. This is
         * determined by checking that all of the array keys are integers.
         *
         * @param array $options The array to check for associativity.
         *
         * @return bool Returns true if $options is associative.
         */
        private function is_assoc( $options = array() ) {

            // Iterate through keys.
            foreach( $options as $k => $v ) {

                // Is one of them not an integer, it's an associative array.
                if( ! is_integer( $k ) ) return true;
            }

            // All were integers, so it's not associative.
            return false;
        }

    }

}
