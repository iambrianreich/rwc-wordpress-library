<!DOCTYPE html>
<html>
    <head>
    </head>
    <body>
        <p>Hi <?php echo esc_html( $name ); ?>, thanks for requesting
            information from <a href="<?php bloginfo( 'url' ); ?>?utm_source=website&utm_campaign=contact_form&utm_medium=email"><?php bloginfo( 'sitename' ); ?></a>
            about the following property:</p>

        <?php $query = new WP_Query( array( 'post_type' => 'any', 'post__in' => array( $property_id ) ) ); ?>
        <?php if( $query->have_posts() ) : ?>
            <?php while( $query->have_posts() ) : $query->the_post(); ?>
                <?php $property = $GLOBALS[ 'property' ]; ?>
                <div class="property-details">
                    <a href="<?php the_permalink(); ?>?utm_source=website&utm_campaign=contact_form&utm_medium=email"><?php the_title(); ?></a>
                </div>
            <?php endwhile; ?>
            <?php wp_reset_postdata(); ?>
        <?php endif; ?>

        <p>We'll be in touch shortly to discuss your questions about this property.
        In the meantime you can refer back to this property's details at the link
        above. Talk to you soon!</p>
        
    </body>
</html>
