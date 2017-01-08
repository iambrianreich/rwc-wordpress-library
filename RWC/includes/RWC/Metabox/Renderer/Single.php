<?php

/**
 * Contains the RWC\Metabox\Renderer\Single class.
 *
 * @author Brian Reich <breich@reich-consulting.net>
 * @copyright Copyright (C) 2016 Reich Consulting
 * @package RWC\Metabox
 */

namespace RWC\Metabox\Renderer {

    /**
	 * The RWC\Metabox\Renderer\Single class is a Metabox renderer that will
     * render a metabox as a single column of values, with no organization.
	 *
	 * @author Brian Reich <breich@reich-consulting.net>
	 * @copyright Copyright (C) 2016 Reich Consulting
	 * @package RWC\Metabox
	 */
    class Single extends \RWC\Metabox\Renderer
    {
        public function render( \RWC\Metabox $metabox ) {

            // TODO Implement single renderer.
            echo __CLASS__ . '::' . __METHOD__;
        }
    }

}
