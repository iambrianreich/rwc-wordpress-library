<?php

/**
 * Contains the RWC\Metabox\Field\Checkbox class.
 *
 * @author Brian Reich <breich@reich-consulting.net>
 * @copyright Copyright (C) 2016 Reich Consulting
 * @package RWC\Metabox
 */

namespace RWC\Metabox\Field {

    /**
	 * The RWC\Metabox\Field\Checkbox renders a checkbox field in a metabox.
	 *
     * A Checkbox field has the following options. The "checkbox-values" field
     * specifies an an associative array of values for the checkbox. The
     * "checked" value will be used when the field is submitted checked. The
     * "unchecked" value will be used when the field is submitted unchecked.
     *
	 * @author Brian Reich <breich@reich-consulting.net>
	 * @copyright Copyright (C) 2016 Reich Consulting
	 * @package RWC\Metabox
	 */
    class Checkbox extends \RWC\Metabox\Field
    {
        /**
         * Renders the Metabox contents.
         *
         * @param \RWC\Metabox The Metabox to render.
         *
         * @return void
         */
        public function render( ) { ?>

            <input type="checkbox"
              name="<?php echo esc_attr($this->get_id()); ?>"
              id="<?php echo esc_attr($this->get_id()); ?>"
              value="<?php echo esc_attr( $this->get_option( 'checkbox-values' )[ 'checked' ] ); ?>"
              <?php if( $this->is_checked()) : ?>checked="checked"<?php endif; ?> />
        <?php }

        public function get_default_options() {

            return array(
                'name'  => 'rcw-metabox-field-checkbox-name',
                'id'    => 'rcw-metabox-field-checkbox-name',
                'value' => null,
                'checkbox-values' => array(
                    'checked' => '1',
                    'unchecked' => '0'
                ),
                'required' => false,
            );
        }

        /**
         * Returns true if the value specified for this field is equal to the
         * "checked" value for the checkbox.
         *
         * @return boolean Returns true if the checkbox is checked.
         */
        public function is_checked() {

            return $this->get_option( 'value' ) ==
                $this->get_option( 'checkbox-values' )[ 'checked' ];
        }
    }

}
