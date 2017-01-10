<?php

/**
 * Contains the RWC\Metabox\Renderer class.
 *
 * @author Brian Reich <breich@reich-consulting.net>
 * @copyright Copyright (C) 2016 Reich Consulting
 * @package RWC\Metabox
 */

namespace RWC\Metabox {

    /**
	 * The RWC\Metabox\Renderer class is an abstract class that specifies the
     * methods required to render a metabox. Concrete extensions of Renderer
     * will specify their own rendering implementations.
	 *
	 * @author Brian Reich <breich@reich-consulting.net>
	 * @copyright Copyright (C) 2016 Reich Consulting
	 * @package RWC\Metabox
	 */
    abstract class Renderer extends \RWC\Object
    {
        /**
         * The Library associated with the Renderer.
         *
         * @var    \RWC\Library
         * @access private
         */
        private $_library;

        /**
         * The Metabox associated with the Render.
         *
         * @var    \RWC\Metabox
         * @access private
         */
        private $_metabox;

        /**
         * Initialize the renderer.
         *
         * Initialize the Renderer. The constructor will call the initialize()
         * method, which gives subclasses the chance to do custom initialization.
         *
         * @param array $options An array of configuration options.
         * @constructor
         */
        public function __construct( $options = array() ) {

            // Call parent constructor
            parent::__construct( $options );

            // Initialize the Renderer
            $this->initialize();
        }

        /**
         * Initializes the Renderer. This method can be overridden by subclasses
         * to perform custom initialization.
         *
         * @return void
         */
        public function initialize() {}

        /**
         * Sets the Metabox associated with the Renderer.
         *
         * @param \RWC\Metabox $metabox The Metabox.
         *
         * @return void
         */
        public function set_metabox( \RWC\Metabox $metabox ) {

            $this->_metabox = $metabox;
        }

        /**
         * Returns the Metabox associated with the Renderer.
         *
         * @return \RWC\Metabox Returns the Metabox.
         */
        public function get_metabox() {

            return $this->_metabox;
        }

        /**
         * Sets the Library instance used by the Renderer.
         *
         * @param \RWC\library $library The Library.
         *
         * @return void
         */
        public function set_library( \RWC\Library $library ) {

            $this->_library = $library;
        }

        /**
         * Returns the Library instance used by the Renderer.
         *
         * @return \RWC\Library Returns the Library instance.
         */
        public function get_library() {

            return $this->_library;
        }

        /**
         * Renders the Metabox contents.
         *
         * @param \RWC\Metabox The Metabox to render.
         *
         * @return void
         */
        public abstract function render( \RWC\Metabox $metabox );
    }

}
