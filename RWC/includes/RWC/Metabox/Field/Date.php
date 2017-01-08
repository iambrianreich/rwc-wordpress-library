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
    class Date extends \RWC\Metabox\Field
    {
        /**
         * Renders the input field
         *
         * @param \RWC\Metabox The Metabox to render.
         *
         * @return void
         */
        public function render( ) { ?>
            <input type="date"
              name="<?php echo esc_attr($this->get_id()); ?>"
              id="<?php echo esc_attr($this->get_id()); ?>"
              value="<?php echo esc_attr( $this->get_option( 'value', '' ) ); ?>" />
        <?php }

        /**
         * Returns the default options for a text field.
         *
         * @return array Returns the default options for a text field.
         */
        public function get_default_options() {

            return array(
                'name'  => 'rcw-metabox-field-text-name',
                'id'    => 'rcw-metabox-field-text-name',
                'value' => null,
                'required' => false,
            );
        }
    }

}
