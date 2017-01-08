<?php

/**
 * This file loads the Reich Web Consulting library from the main library
 * class, RWC\Library.
 *
 * @author Brian Reich <breich@reich-consulting.net>
 * @copyright Copyright (C) 2016 Reich Web Consulting
 * @version 1.0
 * @package RWC
 */
namespace RWC {

    /**
     * A base class for Reich Web Consulting WordPress library Features.
     *
     * @author Brian Reich <breich@reich-consulting.net>
     * @copyright Copyright (C) 2016 Reich Web Consulting
     * @version 1.0
     * @package RWC
     */
    class Feature extends \RWC\Object {

        /**
         * The \RWC\Library instance associated with the feature.
         *
         * @var   \RWC\Library
         * @ccess protected
         */
        protected $_library;

        /**
         * Initializes the Feature.
         *
         * Initializes the Feature. Sets the options passed to the Feature and
         * the Library reference that this Feature is associated with.
         *
         * @param array        $options An array of Feature options.
         * @param \RWC\Library $library The Library instance.
         *
         * @construct
         */
        public function __construct( $options = array(), \RWC\Library $library ) {

            parent::__construct( $options );

            $this->_library = $library;

            foreach( $this->get_dependancies() as $dependancy ) {
                $library->load_feature( $dependancy );
            }
        }

        /**
         * Initializes the plugin.
         *
         * Initializes the plugin. Override this method to specify WordPress
         * hooks and other integraiton points.
         *
         * @return void
         */
        public function initialize() { }

        /**
         * Returns an array of features on which this Feature depends.
         *
         * Returns an array of features on which this Feature depends. Override
         * this method in specific Feature instances to specify dependancies.
         *
         * @return array Returns an array of dependant features.
         */
        public function get_dependancies() {

            return array();
        }

        /**
         * Returns the Library associated with the Feature.
         *
         * @return \RWC\Library Returns the Library associated with the Feature.
         */
        protected function get_library() {

            return $this->_library;
        }

    }
}
