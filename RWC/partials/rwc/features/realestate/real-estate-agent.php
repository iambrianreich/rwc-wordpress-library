<?php
/**
 * This template renders the real estate agent details assocaited with a Property in
 * Schema.org Person format. If you choose to override this template, take care to
 * maintain Schema.org compatibility.
 *
 * @author Brian Reich <breich@reich-consulting.net>
 * @copyright Copyright (C) 2016 Reich Web Consulting
 * @see https://schema.org/Person
 */
?>
<section id="agent" itemscope itemtype="http://schema.org/Person">
    <?php if( $property->has_agent_picture_url() ) : ?>
        <div class="agent-picture">
            <?php $images = json_decode( $property->get_agent_picture_url()); ?>
            <img src="<?php echo $images[0]->url; ?>" alt="<?php echo $images[0]->alt; ?>" />
        </div>
    <?php endif; ?>
    <div class="agent-name" itemprop="name">
        <span class="first" itemprop="givenName"><?php echo esc_html( $property->get_agent_first_name() ); ?></span>
        <span class="last" itemprop="familyName"><?php echo esc_html( $property->get_agent_last_name() ); ?></span>
    </div>
    <?php if( $property->has_agent_email() ) : ?>
        <div class="agent-email">
            <a itemprop="email" href="mailto:<?php echo esc_attr( $property->get_agent_email() ); ?>"><?php echo esc_attr( $property->get_agent_email() ); ?></a>
        </div>
    <?php endif; ?>
    <?php if( $property->has_agent_office_line_number() ): ?>
        <div class="agent-office-phone">
            <strong>Office:</strong>
            <span itemprop="telephone"><?php echo esc_html( $property->get_agent_office_line_number() ); ?></span>
        </div>
    <?php endif; ?>

    <?php if( $property->has_agent_mobile_phone_line() ): ?>
        <div class="agent-mobile-phone">
            <strong>Mobile:</strong>
            <span itemprop="telephone"><?php echo esc_html( $property->get_agent_mobile_phone_line() ); ?></span>
        </div>
    <?php endif; ?>
    <?php if( $property->has_agent_fax_number() ): ?>
        <div class="agent-fax-phone">
            <strong>Fax:</strong>
            <span itemprop="fax"><?php echo esc_html( $property->get_agent_fax_number() ); ?></span>
        </div>
    <?php endif; ?>
    <?php if( $property->has_agent_license_number() ): ?>
        <div class="agent-license-no">
            License #<?php echo esc_html( $property->get_agent_license_number() ); ?>
        </div>
    <?php endif; ?>
    <span itemprop="worksFor" itemRef="brokerage">
</section>
