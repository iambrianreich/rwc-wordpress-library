<?php

namespace RWC {

    /**
     * PostWrapper provides base functionality for WP_Post wrappers.
     *
     * The PostWrapper class provides base functionaility for classes that wrap
     * a WP_Post.  Do not directly instantiate PostWrapper. Instead, extend
     * PostWrapper as a subclass and extend it with features specific to a
     * particular type of WordPress post.
     */
    class PostWrapper {

        private $_post;

        /**
         * Creates a new PostWrapper that will wrap the WP_Post instance
         * passed to the constructor.
         *
         * @param WP_Post|int $post The WP_Post, or the post id of the post.
         */
        public function __construct( $post = null ) {

            $this->set_post( $post );
        }

        /**
         * Sets the WP_Post that this PostWrapper will wrap around. You can
         * pass either a WP_Post instance or the unique id of the post to wrap.
         *
         * @param WP_Post|int $post The WP_Post, or the unique id of the post.
         *
         * @return void
         */
        public function set_post( $post ) {

            if( ! $post instanceof \WP_Post ) {

                $post = get_post( $post );

                if( empty( $post ) ) {

                    throw new \RWC\Exception( sprintf(
                        'Cannot create PostWrapper because post "%s" ' .
                        'does not exist.', $post ) );
                }
            }

            $this->_post = $post;
        }

        /**
         * Returns the WP_Post wrapped by the PostWrapper instance.
         *
         * @return WP_Post Returns the wrapped WP_Post.
         */
        public function get_post() {

            return $this->_post;
        }
    }
}
