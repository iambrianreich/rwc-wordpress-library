<?php

/**
 * This template renders a notice about which Fundraiser is selected.
 *
 * The following view variables must be in scope when the template is rendered.
 * The $fundraiser variable specifies the unique id of the fundraiser currently
 * selected by the user.
 *
 * @author Brian Reich <breich@reich-consulting.net>
 * @copyright Copyright (C) 2017 Reich Web Consulting
 * @version 1.0
 * @package RWC\Features\Fundraiser
 * @subpackage Templates
 */

$query = new \WP_Query( array(
    'post_type' => \RWC\Features\Fundraisers::POST_TYPE,
    'post__in' => [ $fundraiser ]
) ); ?>
<?php if( $query->have_posts() ) : ?>
    <?php while( $query->have_posts() ) : $query->the_post(); ?>
        <div id="rwc-fundraiser-banner">
            You are currently shopping the
            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
            fundraiser. Product options and pricing are fundraiser-specific.
        </div>
    <?php endwhile; ?>
<?php endif; ?>
<?php wp_reset_postdata(); ?>
