<?php

namespace RWC {

    /**
     * Load RWC\Exception
     */
    require_once( 'Exception.php' );
    require_once( 'Autoloader.php' );
    require_once( 'Object.php' );

    class Library extends \RWC\Object {

        /**
         * The RWC\Library instance.
         *
         * @var    \RWC\Library
         * @access private
         */
        private static $_library = null;

        /**
         * The RWC\Autoloader instance used for automatic loading of classes
         * in the RWC namespace.
         *
         * @var    \RWC\Autoloader
         * @ccess private
         */
        private $_autoloader = null;

        /**
         * The URI to the library.
         *
         * @var    string
         * @access private
         */
        private $uri = null;

        /**
         * The file from which the library was activated. Must be set to
         * utilize activation hooks.
         *
         * @var    string
         * @access private
         */
        private $activation_file = null;

        /**
         * A list of features passed to the library.
         *
         * @var    array
         * @access protected
         */
        private $_features = array();

        /**
         * A list of leaded featured.
         *
         * @var    array
         * @access protected
         */
        private $_loaded_features = array();

        /**
         * Creates and returns an instance of the RWC\Library class.
         *
         * @param array $options The options for the Library.
         *
         * @return \RWC\Library Returns a Library instance.
         */
        public static function load( $options = array() ) {

            // Lazy load the Library instance.
            if( self::$_library == null ) {

                self::$_library = new Library( $options );
            }

            return self::$_library;
        }

        /**
         * Returns the default options for initializing RWC\Library.
         *
         * @return array Returns an array of default options.
         */
        public function get_default_options() {

            return array(
                'uri' => null,
                'activation_file' => null,
                'features' => array(),
                'admin_page_title' => __( 'Reich Web Consulting Options', 'RWC_Library' ),
                'admin_page_menu' => __( 'RWC Options', 'RWC_Library' )
            );
        }

        /**
         * Creates an instance of the RWC\Library class.
         *
         * @param array $options The options for the library.
         */
        public function __construct( $options = array() ) {

            // Constructor must be public, but don't instantiate twice.
            if( self::$_library != null ) {
                throw new Exception( 'Library already initialized. ' .
                    'Do not call RWC\Library::__construct() directly. ' );
            }

            parent::__construct( $options );

            // Create and register the autoloader.
            $this->_autoloader = new Autoloader( array(
                'register' => true
            ) );

            $this->set_uri( $options[ 'uri' ] );
            $this->set_activation_file( $options[ 'activation_file' ] );
            $this->set_features( $options[ 'features' ] );

            foreach( $this->_features as $name => $params ) {
                $this->load_feature( $name, $params );
            }

            // Add Menu Page
            add_action( 'admin_menu', array( $this, 'plugin_admin_add_page') );

            // Register scripts
            add_action( 'admin_enqueue_scripts',
                array( $this, 'register_admin_scripts' ), 0 );
        }

        /**
         * Registers admin scripts for use by features.
         *
         * @return void
         */
        public function register_admin_scripts() {

            // Register scripts
            wp_register_script( 'rwc-vertical-tabs-js', $this->get_uri() .
                '/js/vertical-tabs.js', false );

            // Register styles
            wp_register_style( 'rwc-vertical-tabs-css', $this->get_uri() .
                '/css/vertical-tabs.css', false );
        }

        /**
         * Adds the options page for the RWC Library.
         *
         * @return void
         */
        public function plugin_admin_add_page() {

            add_options_page(
                $this->get_option( 'admin_page_title' ),
                $this->get_option( 'admin_page_menu'),
                'manage_options',
                'rwc-library',
                array( $this, 'plugin_options_page' )
            );
        }

        public function plugin_options_page() { ?>
            <div>
                <h2><?php echo esc_html( $this->get_option( 'admin_page_title' ) ); ?></h2>
                <form action="options.php" method="post">
                    <?php settings_fields('plugin_options'); ?>
                    <?php do_settings_sections('plugin'); ?>
                    <input name="Submit" type="submit" value="<?php esc_attr_e('Save Changes'); ?>" />
                </form></div>
        <?php }

        /**
         * Sets the path to the file that activated the library. This can be
         * utilized to register activation hooks.
         *
         * @param string|null $activation_file The path to the activation file.
         *
         * @return void
         */
        public function set_activation_file( $activation_file = null ) {

            $this->_activation_file = $activation_file;
        }

        /**
         * Returns the path to the activation file.
         *
         * @return string|null Returns the path to the activation file.
         */
        public function get_activation_file() {

            return $this->_activation_file;
        }

        /**
         * Sets the list of Features passed to the constructor.
         *
         * @param array $features an Array of Feature class names.
         *
         * @return void
         */
        public function set_features( $features = array() ) {

            $this->_features = $features;
        }

        /**
         * Loads a Feature by class name.
         *
         * Loads a Feature by class name. When the feature is loaded,
         * load_feature() first checks the Feature to see if it has any
         * dependancies by calling it's
         *
         * @param string $feature The name of the Feature class name.
         *
         * @return void
         */
        public function load_feature( $name, $options = null ) {

            // If no options, see if they are set in the global Features config.
            if( empty( $options ) ) {
                if( isset( $this->_features[ $name ] ) ) {
                    $options = $this->_features[ $name ] ;
                }
            }

            // Don't re-run if the feature is already leaded.
            if( isset( $this->_loaded_features[ $name ] ) ) return;

            try {
                $options      = \apply_filters(
                                'rwc_feature_options', $options, $name );
                $class        = "RWC\Features\\$name";
                $feature      = new $class( $options, $this );
                $dependancies = $feature->get_dependancies();

                /*
                 * Load dependancies first, if any.
                 */
                if( count( $dependancies ) > 0 ) {

                    foreach( $dependancies as $dependancy ) {

                        // Load the dependancy.
                        $this->load_feature( $dependancy );
                    }
                }

                // Run the rwc_plugin_initialize_before action
                \do_action( 'rwc_plugin_initialize_before', $feature, $options );

                // Initialize the Feature.
                $feature->initialize();

                // Run the rwc_plugin_initialize_after action
                \do_action( 'rwc_plugin_initialize_after', $feature, $options );

                $this->_loaded_features[ $name ] = $feature;

            } catch( \Exception $e ) {

                throw new \Exception(
                    "Failed to load RWS Library Feature $name: "
                        . $e->getMessage() );
            }
        }

        /**
         * Sets the URI for the library.
         *
         * Sets the URI for the library. By setting the URI to the library, it
         * can be used via plug-in or via theme.
         *
         * @param string $uri The URI to the library.
         */
        public function set_uri( $uri ) {

            if( is_null( $uri ) ) {

                throw new Exception( 'No URI to RWC Library files was found.' );
            }

            $this->_uri = $uri;
        }

        /**
         * Returns the URI for the library.
         *
         * @return string|null Returns the URI for the library.
         */
        public function get_uri() {

            return $this->_uri;
        }

        /**
         * Returns the specified feature.
         *
         * Returns the specified loaded Feature object. If the Feature is not
         * loaded, null is returned.
         *
         * @return \RWC\Feature Returns the specified Feature, or null.
         */
        public function get_loaded_feature( $feature ) {

            return isset( $this->_loaded_features[ $feature ] ) ?
                $this->_loaded_features[ $feature ] : null;
        }
    }
}
