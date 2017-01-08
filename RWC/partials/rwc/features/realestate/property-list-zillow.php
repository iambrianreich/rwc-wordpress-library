<div class="property-list zillow">
    <?php if( $query->have_posts() ) : ?>
        <ul>
            <?php while( $query->have_posts() ) : $query->the_post(); ?>
                <?php $property = $GLOBALS[ 'property' ]; ?>
                <li itemscope itemtype="https://schema.org/Apartment">
                    <?php if( has_post_thumbnail() ) : ?>
                        <a href="<?php the_permalink();?>"><?php the_post_thumbnail( 'real-estate' ); ?></a>
                    <?php endif; ?>
                    <div class="image-count"><?php echo count( $property->get_image_ids() ); ?> photos</div>
                    <div class="content">

                        <div class="property-type">
                            <?php echo esc_html( $property->get_property_type_text() ); ?> |
                            <?php if( $property->get_status() == 'Coming Soon' ) : ?>
                                <?php if( $property->has_coming_soon_on_market_date() ) : ?>
                                    Available <?php echo esc_html( date_i18n( 'M j, Y', strtotime( $property->get_coming_soon_on_market_date() ) ) ); ?>
                                <?php else : ?>
                                    Coming Soon
                                <?php endif; ?>
                            <?php else : ?>
                                <?php echo esc_html( $property->get_status() ); ?>
                            <?php endif; ?>
                        </div>

                        <div class="price">
                            <?php echo esc_html( $property->get_price_text() ); ?>
                        </div>
                        <div class="bedrooms"><?php echo esc_html( $property->get_bedrooms() ); ?> bds</div>
                        <div class="baths"><?php echo esc_html( $property->get_bathrooms() ); ?> ba</div>
                        <h3><a href="<?php the_permalink(); ?>">
                            <span  itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">
                                <span itemprop="streetAddress"><?php echo esc_html( $property->get_address_1() ); ?></span>,
                                <span itemprop="addressLocality"><?php echo esc_html( $property->get_city() ); ?></span>,
                                <span itemprop="addressRegion"><?php echo esc_html( $property->get_state() ); ?></span>
                                <span itemprop="postalCode"><?php echo esc_html( $property->get_zip_code() ); ?></span>
                            </span></a></h3>
                    </div>
                </li>
            <?php endwhile; ?>
        </ul>
    <?php else : ?>
        <p>No properties found.</p>
    <?php endif; ?>
</div>
