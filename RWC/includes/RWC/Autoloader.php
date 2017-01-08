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
            // Don't do anything if it's not one of our classes.
            if( substr( $className, 0, 3) !== 'RWC' ) return;

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

            // Verify that the file loads successfully.
            require( $fileName  );
        }
    }
}
