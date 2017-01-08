<?php

/**
 * This file loads the Reich Web Consulting library from the main library
 * class, RWC\Library.
 *
 * @author Brian Reich <breich@reich-consulting.net>
 * @copyright Copyright (C) 2016 Reich Web Consulting
 * @version 1.0
 */

 // Prevent direct access.
 defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

// Don't load twice.
if( defined( 'RWC_PATH' ) ) {
    return;
}

/**
 * Define the path to the Reich Web Consulting library.
 *
 * @var string
 */
define( 'RWC_PATH', dirname( __FILE__ )  );

// Add the includes/ folder to the include path.
set_include_path( get_include_path() . PATH_SEPARATOR . RWC_PATH . '/includes/' );

/**
 * Load the Reich Web Consulting Library.
 */
require_once( 'includes/RWC/Library.php' );
