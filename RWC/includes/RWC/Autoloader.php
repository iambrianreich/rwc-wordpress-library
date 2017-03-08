<?php

/**
 * This file contains the RWC\Autoloader class.
 *
 * @author Brian Reich <breich@reich-consulting.net>
 * @copyright Copyright (C) 2016 Reich Web Consulting
 * @version 1.0
 * @package RWC
 */

namespace RWC {

    /**
     * The Autoloader class registers a PSR-0 Compatible autoloader for classes
     * in the Reich Web Consulting library.
     *
     * @author Brian Reich <breich@reich-consulting.net>
     * @copyright Copyright (C) 2016 Reich Web Consulting
     * @version 1.0
     * @package RWC
     */
    class Autoloader {

        /**
         * A boolean flag that specifies whether or not the Autoloader should
         * automatically register. Set via the "register" configuration option.
         *
         * @var    boolean
         * @access private
         */
        private $_register = true;

        /**
         * The namespaces assigned to the
         */
        private $_namespaces = array();

        /**
         * Adds a namespace to the list of supported namespaces.
         *
         * @param string $namespace The namespace to add to the Autoloader.
         * @param string $path      The path to the namespace's classes.
         */
        public function add_namespace( $namespace, $path ) {

            // Only add it if it's not already registered.
            if( ! array_key_exists( $namespace, $this->_namespaces ) ) {

                $this->_namespaces[ $namespace ] = $path;
            }
        }

        /**
         * Returns the list of supported namespaced.
         *
         * @return array Returns the list of Autoloaded namespaces.
         */
        public function get_namespaces() {

            return $this->_namespaces;
        }

        /**
         * Sets the list of supported namespaces.
         *
         * Sets the list of supported namespaces. set_namespaces() is
         * destructive. It will remove any namespaces that are already
         * registered.
         *
         * @param array $namespaces The namespaces to set.
         */
        public function set_namespaces( $namespaces ) {

            $this->_namespaces = [];
            foreach( $namespaces as $namespace => $path ) {
                $this->add_namespace( $namespace, $path );
            }
        }

        /**
         * Returns true if this Autoloader supports the specified namespace.
         *
         * @param string $namespace The namespace to check for support.
         *
         * @return bool Returns true if Autoloader supports the namespace.
         */
        public function is_namespace_supported( $namespace ) {

            return array_key_exists( $namespace, $this->get_namespaces() );
        }

        /**
         * Creates a new Autoloader.
         *
         * Creates a new Autoloader. The Autoloader supports the following
         * options. The "register" option specifies whether or not the new
         * Autoloader should automatically register itself with the PHP
         * Autoloader (defaults to "true").
         *
         * @param array $options An array of Autoloader options.
         */
        public function __construct( $options = array() ) {

            // Merge in defaults.
            $options = array_merge( array(
                'register' => true
            ), $options );

            // If the register option is set to true, automatically register
            // the autoloader.
            if( $options[ 'register' ] ) {

                $this->register();
            }
        }

        /**
         * Registers the Autoloader.
         *
         * @return void
         */
        public function register() {

            spl_autoload_register( array( $this, 'autoload' ) );
        }

        /**
         * Autoloads the specified class.
         *
         * Autoloads the specified class in the Reich Web Consulting library.
         * The Autoloader follows PSR-0 class naming conventions. For more
         * information, see http://www.php-fig.org/psr/psr-0/
         *
         * @param string $className The name of the class to automatically load.
         *
         * @return void
         */
        public function autoload($className)
        {

            // Don't do anything if it's not a supported namespace.
            $namespace = explode( '\\', $className )[0];

            if( ! $this->is_namespace_supported( $namespace ) ) return;

            $namespacePath = $this->get_namespaces()[ $namespace ];

            // Don't do anything if class is already loaded.
            if( class_exists( $className ) ) {
                return;
            }

            $className = ltrim($className, '\\');
            $fileName  = '';
            $namespace = '';
            if ($lastNsPos = strrpos($className, '\\')) {
                $namespace = substr($className, 0, $lastNsPos);
                $className = substr($className, $lastNsPos + 1);
                $fileName  = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
            }

            $fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';


            // Include the file from the path registered with the namespace.
            @$success = include( $namespacePath  .'/' .$fileName  );

            // Verify that the file loads successfully.
            if( ! $success ) {

                throw new \RWC\Exception( "Failed to autoload $fileName");
            }
        }
    }
}
