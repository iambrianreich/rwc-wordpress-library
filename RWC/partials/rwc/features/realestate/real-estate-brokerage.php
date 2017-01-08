<?php

/**
 * This template renders the details of the real estate agent associated with
 * a property. The details are rendered in Schema.org Organization format. If you
 * choose to override this template, take care to maintain schema.org formats.
 *
 * @author Brian Reich <breich@reich-consulting.net>
 * @copyright Copyright (C) 2016 Reich Web Consulting
 * @see https://schema.org/Organization
 */
 ?>
 <section id="brokerage" itemScope itemType="https://schema.org/RealEstateAgent">

     <?php if( $property->has_office_brokerage_image() ) : ?>
         <?php $image = json_decode( $property->get_office_brokerage_image() ); ?>
         <div class="image">
             <img itemprop="image" src="<?php echo esc_attr( $image[0]->url ); ?>"
                 alt="<?php echo (isset( $image[0]->alt) ? esc_attr( $image[0]->alt) : '' ); ?>" />
         </div>
     <?php endif; ?>

    <?php if( $property->has_office_brokerage_name() ): ?>
        <div class="office-brokerage-name">
            <span itemProp="name"><?php echo esc_html( $property->get_office_brokerage_name() ); ?></span>
        </div>
    <?php endif; ?>

    <?php if( $property->has_office_brokerage_email() ): ?>
        <div class="office-brokerage-email">
            <a href="mailto:<?php echo esc_html( $property->get_office_brokerage_email() ); ?>"><?php echo esc_html( $property->get_office_brokerage_email() ); ?></a>
        </div>
    <?php endif; ?>

    <?php if( $property->has_office_brokerage_phone() ): ?>
        <div class="office-brokerage-phone">
            <strong>Phone:</strong> <span itemprop="telephone"><?php echo esc_html( $property->get_office_brokerage_phone() ); ?></span>
        </div>
    <?php endif; ?>

    <?php if( $property->has_office_brokerage_website() ): ?>
        <div class="office-brokerage-website">
            <strong>Website:</strong><a href="<?php echo esc_html( $property->get_office_brokerage_website() ); ?>"><?php echo esc_html( $property->get_office_brokerage_website() ); ?></a>
        </div>
    <?php endif; ?>
    <div itemprop="address" itemScope itemType="https://schema.org/PostalAddress">
        <span itemprop="streetAddress">
            <?php echo esc_html( $property->get_office_brokerage_street_address() ); ?>
            <?php echo esc_html( $property->get_office_brokerage_unit_number() ); ?>
        </span>
        <span itemprop="addressLocality"><?php echo esc_html( $property->get_office_brokerage_city() ); ?></span>,
        <span itemprop="addressRegion"><?php echo esc_html( $property->get_office_brokerage_state() ); ?></span>
        <span itemprop="postalCode"><?php echo esc_html( $property->get_office_brokerage_zip() ); ?></span>
    </div>

    <div itemprop="priceRange" class="priceRange">
        <?php $min = $property->get_office_brokerage_price_range_min(); ?>
        <?php $max = $property->get_office_brokerage_price_range_max(); ?>
        <?php if( ! empty( $min ) ) : ?>
            <?php echo esc_html( $min ); ?>
            <?php if( $min != $max ) : ?>
                - <?php echo esc_html( $max ); ?>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</section>
