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
    class Text extends \RWC\Metabox\Field
    {
        /**
         * Renders the input field
         *
         * @param \RWC\Metabox The Metabox to render.
         *
         * @return void
         */
        public function render( ) { ?>
            <input type="text"
              name="<?php echo esc_attr( $this->get_id()); ?>"
              id="<?php echo esc_attr( $this->get_id()); ?>"
              value="<?php echo esc_attr( $this->get_option( 'value', '' ) ); ?>"
              <?php if( $this->get_option( 'placeholder' ) ) : ?>
                  placeholder="<?php echo esc_attr( $this->get_option( 'placeholder' ) ); ?>"
              <?php endif; ?> />
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
