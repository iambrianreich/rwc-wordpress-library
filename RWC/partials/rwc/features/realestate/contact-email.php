<!DOCTYPE html>
<html>
    <head>
    </head>
    <body>
        <h1><?php bloginfo( 'sitename' ); ?></h1>
        <p><?php echo esc_html( $name ); ?> has requested details about the
        following property:</p>
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

        <div class="contact-details">
            <h2>Contact Details</h2>

            <div class="name"><b>Name:</b> <?php echo esc_html( $name ); ?></div>
            <div class="phone"><b>Phone:</b> <?php echo esc_html( $phone ); ?></div>
            <div class="email"><a href="mailto:<?php echo esc_attr( $email); ?>"><?php echo esc_html( $email ); ?></a></div>
        </div>

        <div class="message">
            <h2>Message</h2>

            <p><?php echo esc_html( $message ); ?></p>
        </div>
    </body>
</html>
