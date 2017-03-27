<div itemscope itemtype="http://schema.org/LocalBusiness">
    <a itemprop="url" href="<?php echo esc_html( $url ); ?>"><div itemprop="name"><?php echo esc_html( $name ); ?></div></a>
    <div itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">
        <?php if( $pobox !== null ) : ?>
            P.O. Box: <span itemprop="postOfficeBoxNumber"><?php echo esc_html( $pobox ); ?></span>,
        <?php endif; ?>
        <span itemprop="addressLocality"><?php echo esc_html( $city ); ?></span>
        <span itemprop="addressRegion"><?php echo esc_html( $state ); ?></span>
        <span itemprop="postalCode"><?php echo esc_html( $zipcode ); ?></span>
        <span itemprop="addressCountry"><?php echo esc_html( $country ); ?></span>
    </div>
    <?php if( $tel !== null ) : ?>
        <span class="tel">Tel :
            <span itemprop="telephone"><a href="tel:<?php echo esc_attr( $tel ); ?>"><?php echo esc_html( $tel ); ?></a></span>
        </span>
    <?php endif; ?>
    <?php if( $fax !== null ) : ?>
        <span class="fax">Fax :
            <span itemprop="faxNumber">555-555-5555</span>
        </span>
    <?php endif; ?>
    <?php if( $email !== null ) : ?>
        <span class="email">Email :
            <span itemprop="email"><a href="mailto:breich@reich-consulting.net">breich@reich-consulting.net</a></span>
        </span>
    <?php endif; ?>
    <span itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating">
        Rated <span itemprop="ratingValue"><?php echo esc_html( $rating ); ?></span>/<span itemprop="bestRating"><?php echo esc_html( $maxRating ); ?></span>
        based on <span itemprop="reviewCount"><?php echo esc_html( $reviewCount ); ?></span>
        customer reviews.
    </span>
</div>
