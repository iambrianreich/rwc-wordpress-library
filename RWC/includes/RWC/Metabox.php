<?php

/**
 * Contains the RWC\Features\RealEstate class.
 *
 * @author Brian Reich <breich@reich-consulting.net>
 * @copyright Copyright (C) 2016 Reich Consulting
 * @package RWC\Features
 */

namespace RWC {

    /**
	 * RWC\Metabox is a special class for easily generating complex metaboxes
     * via configuration. The following configuration options are available.
     * The 'renderer' option specifies the display mode for the Metabox,
     * and can specify single, vertical-tabs, or horizontal-tabs. For more
     * information on each display mode, see the associated class documentation.
     * The "id" option specifies the unique id for this metabox which will be
     * passed to the add_meta_box() call. The "title" option specifies the
     * title of the metabox, which is passed to the add_meta_box() call. The
     * "post_types" option specifies the post type or types that will use this
     * metabox. This value will be passed to the add_meta_mox() call. The
     * "field_prefix" option is used to specify a string that will be used to
     * prefix before all field names when stored and rendered (defaults to
     * "%metabox_id%_").
     *
     * Example configuration:
     * array(
     *     'renderer' => 'vertical-tabs',
     *     'id'           => 'my-sample-metabox',
     *     'title'        => 'My Sample Metabox',
     *     'post_types'   => 'post',
     *     'field_prefix' => 'my-sample-metabox'
     )
	 *
	 * @author Brian Reich <breich@reich-consulting.net>
	 * @copyright Copyright (C) 2016 Reich Consulting
	 * @package FC_Client
	 */
    class Metabox extends \RWC\Object
    {
        /**
         * A counter for auto-generated metaboxes.
         *
         * @var    int
         * @access private
         */
        private static $_staticCounter = 0;

        /**
         * The Library instance that created the Metabox.
         *
         * @var    \RWC\Library
         * @access private
         */
        private $_library;

        /**
         * The counter value for this metabox.
         *
         * @var    int
         * @access private
         */
        private $_counter = 0;

        /**
         * The renderer.
         *
         * @var    \RWC\Metabox\Renderer
         * @access private
         */
        private $_renderer = null;

        /**
         * An array of Fields assigned to this Metabox.
         *
         * @var    array
         * @access private
         */
        private $_fields = null;

        /**
         * Creates a new Metabox.
         *
         * @param array $options An array of options for the Metabox.
         */
        public function __construct(\RWC\Library $library, $options = array() ) {

            parent::__construct( $options );

            // Sets the library instance.
            $this->set_library( $library );

            // Set this Metabox's count.
            $this->_counter = self::$_staticCounter;

            // Increment the counter
            self::$_staticCounter++;

            add_action( 'add_meta_boxes', array( $this, 'register_metabox' ) );
            add_action( 'save_post', array( $this, 'save_metabox' ), 10, 2 );

            // Force initialization of the renderer
            $this->get_renderer_instance();
        }

        /**
         * Returns the Metabox Storage for this Metabox.
         *
         * @return \RWC\Metabox\Storage\StorageAbstract Returns the Storage.
         */
        public function get_storage() {

            return $this->get_option( 'storage' );
        }

        /**
         * Sets the Library instance used to create the Metabox.
         *
         * @param \RWC\Library $library The Library used to create the Metabox.
         *
         * @return void
         */
        public function set_library( \RWC\Library $library ) {

            $this->_library = $library;
        }

        /**
         * Returns the Library instance used to create the Metabox.
         *
         * @return \RWC\Library Returns the Library instance.
         */
        public function get_library() {

            return $this->_library;
        }

        /**
         * Registers this metabox with WordPress.
         *
         * @return void
         */
        public function register_metabox() {

            \add_meta_box(
                $this->get_id(),
                __( $this->get_title(), 'RWC_Metabox' ),
                array( $this, 'render' ),
				$this->get_post_types()
			);
        }

        /**
         * Saves the Metabox.
         *
         * Saves the Metabox. First, the metabox nonce will be verified. If the
         * metabox nonce is valid, the field values will be saved into the
         * post's metadata as an associative array under the name of the
         * metabox.
         *
         * @param int     $post_id The unique id of the Post.
         * @param WP_Post $post    The WP_Post object.
         *
         * @return void
         */
        public function save_metabox( $post_id, $post ) {


            /* Verify the nonce before proceeding. */
            $nonce = $this->get_field_id( 'nonce' );
            if ( !isset( $_POST[ $nonce] ) || ! wp_verify_nonce(
                $_POST[ $nonce ], basename( __FILE__ ) ) )
                    return $post_id;

            $this->set_field_values_from_post();

            $this->get_storage()->store( $post_id, $post );
        }

        public function get_default_options() {

            return array(
                'renderer' => 'single', // single|vertical-tab|horizontal-tab
                'id' => 'rwc_metabox_%counter%',
                'title' => 'Reich Web Consulting Metabox',
                'post_types' => '',
                'field_prefix' => '%metabox_id%_',
                'sections' => array(),
                'fields' => array(),

                // 2016-12-16 : Automatically specify the metabox.
                'storage' => new Metabox\Storage\MetadataFields(array(
                    'metabox' => $this
                ) )
            );
        }

        /**
         * Returns the display mode configured for this metabox.
         *
         * @return string Returns the metabox display mode.
         */
        public function get_renderer() {

            return $this->_options[ 'renderer' ];
        }

        /**
         * Returns the full id of the specified field.
         *
         * Returns the full id of the specified field by adding it to the
         * field prefix for this Metabox.
         *
         * @param string $id The relative id of the field.
         *
         * @return string Returns the full id of the specified field.
         */
        public function get_field_id( $id ) {

            return $this->get_field_prefix() . $id;
        }

        /**
         * Returns the field prefix for this Metabox.
         *
         * Returns the field prefix for this Metabox. The field prefix is
         * specified by setting the "field_prefix" option when creating the
         * Metabox.
         *
         * @return string Returns the field prefix for this metabox.
         */
        public function get_field_prefix() {

            return str_replace( '%metabox_id%', $this->get_id(),
                $this->_options[ 'field_prefix' ] );
        }

        /**
         * Returns the metabox id. This is the id specified in the metabox
         * configuration, with "%counter%" replaced with the Metabox's internal
         * counter value.
         *
         * @return string Returns the metabox id.
         */
        public function get_id() {

            return str_replace( '%counter%', $this->get_counter(),
                $this->_options[ 'id' ] );
        }

        /**
         * Returns the title of this Metabox.
         *
         * Returns the title of this Metabox. The Metabox title is set by
         * specifying the "title" option when creating the Metabox.
         *
         * @return string Returns the Metabox title.
         */
        public function get_title() {

            return $this->_options[ 'title' ];
        }

        /**
         * Returns the post types on which this Metabox appears.
         *
         * Returns the post types on which this Metabox appears. This option is
         * set by specifying an array of post types as the "post_types" option
         * when creating the metabox.
         *
         * @return array Returns an array of post types for this Metabox.
         */
        public function get_post_types() {

            return $this->_options[ 'post_types' ];
        }

        /**
         * Returns the current counter for this Metabox.
         *
         * Returns the current counter value for this Metabox. The counter is
         * used to auto-name Metaboxes that don't have a name explicitly
         * assigned.
         *
         * @return int Returns the current counter for this Metabox.
         */
        public function get_counter() {

            return $this->_counter;
        }

        /**
         * Renders the Metabox content.
         *
         * @return void
         */
        public function render() {

            global $post;

            wp_nonce_field( basename( __FILE__ ),
                $this->get_field_id( 'nonce' ) );

            // Load this post's meta values into fields.
            $this->set_field_values_from_storage( $post->ID );

            // Render the metabox using the configured renderer.
            $this->get_renderer_instance()->render( $this );
        }

        /**
         * Returns the Renderer for this Metabox.
         *
         * @return \RWC\Metabox\Renderer Returns the Renderer
         */
        protected function get_renderer_instance() {

            if( $this->_renderer == null ) {
                $class = '\RWC\Metabox\Renderer\\' .
                    \RWC\Strings::get_camel_case( $this->get_renderer() );

                $this->_renderer = new $class();

            }

            return $this->_renderer;
        }

        /**
         * Returns an array of Field objects associated with this Metabox.
         *
         * @return array Returns an array of Field objects for the Metabox.
         */
        public function get_fields() {

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
                    $this, $id, $options );
            }
        }

        private function set_field_values_from_post() {

            $fields = $this->get_fields();

            foreach( $fields as $id => $field ) {

                // The absolute id within the metabox
                $absId = $this->get_field_id( $id );

                // Is there a value in the POST for this field? Then set it.
                if( ! isset( $_POST[ $absId ] ) ) {

                    // Instantiate if it doesn't already exist.
                    $_POST[ $absId ] = null;
                }

                $field->set_value( $_POST[ $absId ], $_POST, true );
            }
        }

        private function set_field_values_from_storage( $post_id ) {

            $storage = $this->get_storage()->load( $post_id );
        }

        public function get_values() {

            $values = array();

            foreach( $this->get_fields() as $id => $field ) {
                $values[ $id ] = $field->get_value();
            }

            return $values;
        }
    }

}
