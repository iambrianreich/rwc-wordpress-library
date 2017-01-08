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
    class Media extends \RWC\Metabox\Field
    {
        /**
         * Renders the Metabox contents.
         *
         * @param \RWC\Metabox The Metabox to render.
         *
         * @return void
         */
        public function render( ) { ?>
            <div id="<?php echo esc_attr($this->get_id()); ?>-container" class="rwc-metabox-field-media">
                <input type="hidden"
                  name="<?php echo esc_attr($this->get_id()); ?>"
                  id="<?php echo esc_attr($this->get_id()); ?>"
                  value="<?php echo esc_attr( $this->get_value() ); ?>" />
                  <div class="image-container">
                      <span class="add-new-image-btn">+</span>
                  </div>
            </div>
            <?php wp_enqueue_media(); ?>
            <?php wp_enqueue_script( 'rwc-metabox-field-media',
                $this->get_metabox()->get_library()->get_uri() .
                    '/js/rwc/metabox/field/media.js', array( 'jquery' ) ); ?>
                    <?php wp_enqueue_style( 'rwc-metabox-field-media-css',
                        $this->get_metabox()->get_library()->get_uri() .
                            '/css/rwc/metabox/field/media.css' ); ?>
        <?php }

        /**
         * Returns the default options for the Media field.
         *
         * @return array Returns the default options for the Media field.
         */
        public function get_default_options() {

            return array(
                'name'  => 'rcw-metabox-field-media-name',
                'id'    => 'rcw-metabox-field-media-name',
                'value' => null,
                'required' => false,
            );
        }

    }

}
