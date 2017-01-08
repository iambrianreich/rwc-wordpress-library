<?php

/**
 * Contains the RWC\Metabox\Renderer\VerticalTabs class.
 *
 * @author Brian Reich <breich@reich-consulting.net>
 * @copyright Copyright (C) 2016 Reich Consulting
 * @package RWC\Metabox
 */

namespace RWC\Metabox\Renderer {

    /**
	 * The RWC\Metabox\Renderer\VerticalTabs class is a Metabox renderer that will
     * render a metabox as series of vertical tabs with sections associated
     * with them that appear to the right.
	 *
	 * @author Brian Reich <breich@reich-consulting.net>
	 * @copyright Copyright (C) 2016 Reich Consulting
	 * @package RWC\Metabox
	 */
    class VerticalTabs extends \RWC\Metabox\Renderer
    {
        /**
         * Load scripts and styles.
         *
         * @return void
         */
        public function initialize() {

            add_action( 'admin_enqueue_scripts',
                array( $this, 'enqueue_scripts' ) );
        }

        /**
         * Enqueue scripts and styles required by VerticalTabs.
         *
         * @return void
         */
        public function enqueue_scripts() {

            // Load Vertical Tabs
            wp_enqueue_script( 'rwc-vertical-tabs-js' );
            wp_enqueue_style( 'rwc-vertical-tabs-css' );
        }

        /**
         * Renders a Metabox as a set of vertical tabs.
         *
         * @param \RWC\Metabox $metabox The metabox to render.
         *
         * @return void
         */
        public function render( \RWC\Metabox $metabox ) {

            $tabs = $metabox->get_option( 'sections', array() );
            $id   = $metabox->get_id();
            ?>
            <div class="vertical-tabs">
                <ul>
                <?php foreach( $tabs as $tab ) : ?>
                    <li><a href="#<?php echo esc_attr( $id . '_' . $tab[ 'id' ] ); ?>"><?php echo esc_html( $tab[ 'name' ] ); ?></a></li>
                <?php endforeach; ?>
                </ul>
                <?php foreach( $tabs as $tab ) : ?>
                    <div id="<?php echo esc_attr( $id . '_' . $tab[ 'id' ] ); ?>">
                        <?php $this->render_tab( $metabox, $tab ) ?>
                    </div>
                <?php endforeach; ?>
            </div><?php
        }

        /**
         * Returns a list of fields for the specified section.
         *
         * Returns a list of fields for the specified section.  The method will
         * search through all of the fields assigned to the metabox and return
         * an array of all of the fields that have been specified a "section"
         * equal to the name of the specified tab.
         *
         * @param \RWC\Metabox The metabox being rendered.
         * @param string $tab The id of the tab.
         *
         * @param return array Returns an array of fields for the section.
         * @throws \RWC\Metabox\Renderer\Exception if configuration is invalid.
         */
        protected function get_tab_fields( \RWC\Metabox $metabox, $tab ) {

            // Temporary array for tabs.
            $fields = array();

            // Iterate through all fields.
            foreach( $metabox->get_fields() as $id => $field ) {

                // We've got problems if we're rendering a VerticalTabs and
                // a field doesn't specify a section.
                $section = $field->get_option( 'section', false );
                if( $section === false ) {

                    throw new Exception( sprintf( 'Field %s in metabox with ' .
                        'VerticalTabs renderer does not specify a section.',
                        $id ) );
                }

                // Skip if not this tab.
                if(  $section != $tab[ 'id' ] ) continue;

                // Set on $fields list.
                $fields[ $id ] = $field;
            }

            return $fields;
        }

        /**
         * Renders a specific tab.
         *
         * Renders a specific tab in this VerticalTab Renderer. The tab content
         * will be rendered as a table with a th containing a label, and the
         * rendered control in the td.
         *
         * @param \RWC\Metabox $metabox The Metabox being rendered.
         * @param string       $tab     The id of the tab being generated.
         *
         * @return void
         */
        protected function render_tab( \RWC\Metabox $metabox, $tab ) {

            $fields = $this->get_tab_fields( $metabox, $tab ); ?>
            <table>
                <?php foreach( $fields as $id => $field ) : ?>
                <tr>
                    <th><label for="<?php echo esc_attr( $metabox->get_field_id( $id ) ); ?>"><?php echo esc_html( $field->get_option( 'name', 'undefined') ); ?></th>
                    <td>
                        <?php $field->render(); ?>
                        <?php $description = $field->get_option( 'description', null ); ?>
                        <?php if( $description ) : ?>
                            <p class="description"><?php echo esc_html( $description ); ?></p>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </table>
        <?php }

        /**
         * Returns an array of default options.
         *
         * @return array Returns an array of default options.
         */
        public function get_default_options() {

            return array(
                'sections' => 'Default'
            );
        }
    }

}
