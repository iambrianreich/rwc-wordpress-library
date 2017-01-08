<?php

/**
 * Contains the RWC\Strings class.
 *
 * @author Brian Reich <breich@reich-consulting.net>
 * @copyright Copyright (C) 2016 Reich Consulting
 * @package RWC
 */

namespace RWC {

    /**
	 * Contains string-related helper functions.
	 *
	 * @author Brian Reich <breich@reich-consulting.net>
	 * @copyright Copyright (C) 2016 Reich Consulting
	 * @package RWC
	 */
    class Strings
    {
        /**
         * Given a string of dash separated words, converts them to a camel
         * case string (ex. "the-class-name" returns "TheClassName").
         *
         * @param string $string The string to convert to camel case.
         *
         * @return string Returns the converted string
         */
        public static function get_camel_case( $string ) {

            $class = '';
            $words = explode('-', $string);
            foreach( explode( '-', $string) as $word ) {
                $class .= ucfirst($word);
            }

            return $class;
        }
    }

}
