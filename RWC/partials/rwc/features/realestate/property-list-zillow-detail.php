<?php global $property; ?>
<?php /* TODO Use correct schema for different types of properties. */ ?>
<div class="property zillow-detail" itemscope itemtype="https://schema.org/Apartment">
    <div class="images">
        <ul>
            <?php
                if( has_post_thumbnail() ) {
                    $id = get_post_thumbnail_id();

                    echo '<li>';
                    printf( '<a class="thickbox" href="%s" title="%s" rel="property-gallery">%s</a>',
                        esc_url( wp_get_attachment_image_url( $id, 'fullsize') ),
                        esc_html( get_the_title( $id ) ),
                        wp_get_attachment_image( $id,
                            'real-estate-zillow-primary' )
                    );
                    echo '</li>';

                }
            ?>
            <?php
                foreach( $property->get_image_ids() as $id ) {
                    echo '<li>';

                    printf( '<a class="thickbox" href="%s" title="%s" rel="property-gallery">%s</a>',
                        esc_url( wp_get_attachment_image_url( $id, 'fullsize') ),
                        esc_html( get_the_title( $id ) ),
                        wp_get_attachment_image( $id,
                            'real-estate-zillow-thumbnail' )
                    );

                    echo '</li>';
                }
            ?>
        </ul>
        <div class="navigation">
            <div class="previous">&lt;</div>
            <div class="next">&gt;</div>
        </div>
    </div>
    <div class="primary-details">
        <h3 class="address">
            <span  itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">
                <span itemprop="streetAddress"><?php echo esc_html( $property->get_address_1() ); ?></span>
                <span itemprop="addressLocality"><?php echo esc_html( $property->get_city() ); ?></span>
                <span itemprop="addressRegion"><?php echo esc_html( $property->get_state() ); ?></span>
                <span itemprop="postalCode"><?php echo esc_html( $property->get_zip_code() ); ?></span>
            </span></h3>

        <ul class="summary-details horizontal-list">
            <li class="bedrooms"><?php echo esc_html( $property->get_bedrooms() ); ?> Bedrooms</li>
            <li class="bathrooms">
                <?php $baths = $property->get_bathrooms(); ?>
                <?php echo esc_html( $baths . ( $baths > 1  ? ' Baths' : ' Bath' ) ); ?>
            </li>
            <?php if( ! empty( $property->get_living_area() ) ) : ?>
                <li class="living-area"><?php echo esc_html( $property->get_living_area() ); ?> sqft</li>
            <?php endif; ?>
        </ul>

        <div class="offer">
            <span class="offer-type <?php echo esc_attr( $property->get_status_class() ); ?>">
                <?php if( $property->get_status() == 'Coming Soon' ) : ?>
                    <?php if( $property->has_coming_soon_on_market_date() ) : ?>
                        Available <?php echo esc_html( date_i18n( 'M j, Y', strtotime( $property->get_coming_soon_on_market_date() ) ) ); ?>
                    <?php else : ?>
                        Coming Soon
                    <?php endif; ?>
                <?php else : ?>
                    <?php echo esc_html( $property->get_status() ); ?>
                <?php endif; ?>
            </span>
            <?php if( $property->get_status() == 'ForRent' ) : ?>
                <span class="rent"><?php echo esc_html( $property->get_price_text() ); ?></span>
            <?php else : ?>
                <span class="price"><?php echo esc_html( $property->get_price_text() ); ?></span>
            <?php endif; ?>
        </div>

        <div class="description">
            <?php the_content(); ?>
        </div>

        <div class="section collapse expanded services-included">
            <h3>Services Included</h3>
            <ul class="content horizontal-list">
                <?php if( $property->get_utilities_water_included() == 'Yes' ) : ?>
                    <li>Water</li>
                <?php endif; ?>
                <?php if( $property->has_utilities_sewage_included() ) : ?>
                    <li>Sewer</li>
                <?php endif; ?>
                <?php if( $property->has_utilities_garbage_included() ) : ?>
                    <li>Garbage Collection</li>
                <?php endif; ?>
                <?php if( $property->has_utilities_electricity_included() ) : ?>
                    <li>Electicity</li>
                <?php endif; ?>
                <?php if( $property->has_utilities_gas_included() ) : ?>
                    <li>Gas</li>
                <?php endif; ?>
                <?php if( $property->has_utilities_internet_included() ) : ?>
                    <li>Internet</li>
                <?php endif; ?>
                <?php if( $property->has_utilities_cable_included() ) : ?>
                    <li>Cable</li>
                <?php endif; ?>
                <?php if( $property->has_utilities_sattv_included() ) : ?>
                    <li>Satellite TV</li>
                <?php endif; ?>
            </ul>
        </div>
        <div class="section collapse expanded property-details">
            <h3>Room Details</h3>
            <ul class="content horizontal-list">
                <?php if( $property->has_bedrooms() ) : ?>
                    <li><strong>Bedrooms:</strong> <?php echo esc_html( $property->get_bedrooms() ); ?></li>
                <?php endif; ?>

                <?php if( $property->has_bathrooms() ) : ?>
                    <li><strong>Bathrooms:</strong> <?php echo esc_html( $property->get_bathrooms() ); ?></li>
                <?php endif; ?>

                <?php if( $property->has_full_bathrooms() ) : ?>
                    <li><strong>Full Baths:</strong> <?php echo esc_html( $property->get_full_bathrooms() ); ?></li>
                <?php endif; ?>

                <?php if( $property->has_half_bathrooms() ) : ?>
                    <li><strong>Half Baths:</strong> <?php echo esc_html( $property->get_half_bathrooms() ); ?></li>
                <?php endif; ?>

                <?php if( $property->has_quarter_bathrooms() ) : ?>
                    <li><strong>1/4 Baths:</strong> <?php echo esc_html( $property->get_quarter_bathrooms() ); ?></li>
                <?php endif; ?>

                <?php if( $property->has_three_quarter_bathrooms() ) : ?>
                    <li><strong>3/4 Baths:</strong> <?php echo esc_html( $property->get_three_quarter_bathrooms() ); ?></li>
                <?php endif; ?>

                <?php if( $property->has_rooms() ) : ?>
                    <?php foreach( $property->get_rooms() as $room ) : ?>
                        <li><?php echo esc_html( $room ); ?></li>
                    <?php endforeach ?>
                <?php endif; ?>

                <?php if( $property->get_attic() == 'Yes' ): ?>
                    <li>Attic</li>
                <?php endif; ?>

                <?php if( $property->get_barbequeue_area() == 'Yes' ) : ?>
                    <li>Barbequeue Area</li>
                <?php endif; ?>

                <?php if( $property->get_basement() == 'Yes' ) : ?>
                    <li>Basement</li>
                <?php endif; ?>

                <?php if( $property->get_deck() == 'Yes' ) : ?>
                    <li>Deck</li>
                <?php endif; ?>

                <?php if( $property->get_disabled_access() == 'Yes' ) : ?>
                    <li>Handicap Accessible</li>
                <?php endif; ?>

                <?php if( $property->get_dock() == 'Yes' ) : ?>
                    <li>Dock</li>
                <?php endif; ?>

                <?php if( $property->get_mother_in_law() == 'Yes' ) : ?>
                    <li>Mother-in-Law Suite</li>
                <?php endif; ?>

                <?php if( $property->get_storage() == 'Yes' ) : ?>
                    <li>Stroage</li>
                <?php endif; ?>
            </ul>
        </div>
        <div class="section collapse expanded hvac">
            <h3>Heating &amp; Cooling Details</h3>
            <ul class="content horizontal-list">
                <?php if( $property->has_heating_fuels() ) : ?>
                    <li><strong>Heating Fuels:</strong>
                    <?php echo esc_html( implode( ', ', $property->get_heating_fuels() ) ); ?></li>
                <?php endif; ?>
                <?php if( $property->has_heating_systems() ) : ?>
                    <li><strong>Heating Systems:</strong>
                    <?php echo esc_html( implode( ', ', $property->get_heating_systems() ) ); ?></li>
                <?php endif; ?>
                <?php if( $property->has_cooling_system() ) : ?>
                    <li><strong>Cooling Systems:</strong>
                    <?php echo esc_html( implode( ', ', $property->get_cooling_system() ) ); ?></li>
                <?php endif; ?>

                <?php if( $property->get_double_pane_windows() == 'Yes' ) : ?>
                    <li>Double-Pane Windows</li>
                <?php endif; ?>

                <?php if( $property->get_fireplace() == 'Yes' ) : ?>
                    <li>Fireplace</li>
                <?php endif; ?>
            </ul>
        </div>
        <div class="section collapse expanded appliances">
            <h3>Appliances Included</h3>
            <ul class="content horizontal-list">
                <?php if( $property->has_dishwasher() ) : ?><li>Dishwasher</li><?php endif; ?>
                <?php if( $property->has_dryer() ) : ?><li>Dryer</li><?php endif; ?>
                <?php if( $property->has_freezer() ) : ?><li>Freezer</li><?php endif; ?>
                <?php if( $property->has_garbage_disposal() ) : ?><li>Garbage Disposal</li><?php endif; ?>
                <?php if( $property->has_microwave() ) : ?><li>Microwave</li><?php endif; ?>
                <?php if( $property->has_range_oven() ) : ?><li>Range Oven</li><?php endif; ?>
                <?php if( $property->has_refrigerator() ) : ?><li>Refrigerator</li><?php endif; ?>
                <?php if( $property->has_trash_compactor() ) : ?><li>Trash Compactor</li><?php endif; ?>
                <?php if( $property->has_washer() ) : ?><li>Washer</li><?php endif; ?>
            </ul>
        </div>
        <?php if( $property->has_additional_features() ) : ?>
            <div class="section collapse expanded additional-features">
                <h3>Additional Features</h3>
                <p class="content"><?php echo esc_html( $property->get_additional_features() ); ?></p>
            </div>
        <?php endif; ?>

        <div class="section collapse expanded parking">
            <h3>Parking</h3>
            <ul class="content horizontal-list">
                <?php if( $property->has_num_parking_spaces() ) : ?>
                    <li><?php echo esc_html( $property->get_num_parking_spaces() ); ?> Parking Spaces</li>
                <?php endif; ?>

                <?php if( $property->has_parking_types() ) : ?>
                    <li><?php echo esc_html( implode( ', ', $property->get_parking_types() ) ); ?></li>
                <?php endif; ?>

                <?php if( $property->get_rv_parking() ) : ?>
                    <li>RV Parking</li>
                <?php endif; ?>
            </ul>
        </div>

        <div class="section collapse expanded building-detail">
            <h3>Building Detail</h3>
            <ul class="content horizontal-list">

                <?php if( $property->has_roof_types() ) : ?>
                    <li><strong>Roofing:</strong>
                        <?php echo esc_html( implode( ', ', $property->get_roof_types() ) ); ?>
                    </li>
                <?php endif; ?>

                <?php if( $property->has_year_updated() ) : ?>
                    <li>Updated
                        <?php echo esc_html( $property->get_year_updated() ); ?>
                    </li>
                <?php endif; ?>

            </ul>
        </div>
        <?php if( $property->has_what_owners_love() ) : ?>
            <div class="section collapse expanded owner-loves">
                <h3>What the Owner Loves</h3>
                <p><?php echo esc_html( $property->get_what_owners_love() ); ?></p>
            </div>
        <?php endif; ?>

        <div class="section collapse expanded other">
            <h3>Other Details</h3>
            <ul class="content horizontal-list">
                <?php if( $property->has_pets_allowed() ) : ?>
                    <li class="pets"><?php echo esc_html( $property->get_pets_allowed() ); ?></li>
                <?php endif; ?>

                <?php if( ! empty( $property->get_mls_id() ) ) : ?>
                    <li><strong>MLS ID:</strong> <?php echo esc_html( $property->get_mls_id() ); ?></li>
                <?php endif; ?>

                <?php if( ! empty( $property->has_lot_size() ) ) : ?>
                    <li><strong>Lot Size:</strong> <?php echo esc_html( $property->get_lot_size() ); ?></li>
                <?php endif; ?>

                <?php if( ! empty( $property->has_year_built() ) ) : ?>
                    <li><strong>Year Built:</strong> <?php echo esc_html( $property->get_year_built() ); ?></li>
                <?php endif; ?>
                <?php if( ! empty( $property->get_architecture_style() ) ) : ?>
                    <li><strong>Style:</strong> <?php echo esc_html( $property->get_architecture_style() ); ?></li>
                <?php endif; ?>

                <?php if( $property->get_cable_ready() == 'Yes' ) : ?>
                    <li>Cable-Ready</li>
                <?php endif; ?>

                <?php if( $property->get_ceiling_fan() == 'Yes' ) : ?>
                    <li>Ceiling Fan(s)</li>
                <?php endif; ?>

                <?php if( $property->get_Doorman() == 'Yes' ) : ?>
                    <li>Doorman</li>
                <?php endif; ?>

                <?php if( $property->get_elevator() == 'Yes' ) : ?>
                    <li>Elevator</li>
                <?php endif; ?>

                <?php if( $property->get_garden() == 'Yes' ) : ?>
                    <li>Garden</li>
                <?php endif; ?>

                <?php if( $property->get_gated_entry() == 'Yes' ) : ?>
                    <li>Gated Entry</li>
                <?php endif; ?>

                <?php if( $property->get_greenhouse() == 'Yes' ) : ?>
                    <li>Greenhouse</li>
                <?php endif; ?>

                <?php if( $property->get_hot_tub_spa() == 'Yes' ) : ?>
                    <li>Hot tub</li>
                <?php endif; ?>

                <?php if( $property->get_intercom() == 'Yes' ) : ?>
                    <li>Intercom</li>
                <?php endif; ?>

                <?php if( $property->get_jetted_bathtub() == 'Yes' ) : ?>
                    <li>Jetted Bathtub</li>
                <?php endif; ?>

                <?php if( $property->get_lawn() == 'Yes' ) : ?>
                    <li>Lawn</li>
                <?php endif; ?>

                <?php if( $property->has_num_floors() ) : ?>
                    <li><?php echo esc_html( $property->get_num_floors() . ' Floor(s)'); ?></li>
                <?php endif; ?>

                <?php if( $property->get_patio() == 'Yes' ) : ?>
                    <li>Patio</li>
                <?php endif; ?>

                <?php if( $property->get_pond() == 'Yes' ) : ?>
                    <li>Pond</li>
                <?php endif; ?>

                <?php if( $property->get_pool() == 'Yes' ) : ?>
                    <li>Pool</li>
                <?php endif; ?>

                <?php if( $property->get_porch() == 'Yes' ) : ?>
                    <li>Porch</li>
                <?php endif; ?>

                <?php if( $property->get_sauna() == 'Yes' ) : ?>
                    <li>Sauna</li>
                <?php endif; ?>

                <?php if( $property->get_security_system() == 'Yes' ) : ?>
                    <li>Security System</li>
                <?php endif; ?>

                <?php if( $property->get_skylight() == 'Yes' ) : ?>
                    <li>Skylight</li>
                <?php endif; ?>

                <?php if( $property->get_sports_court() == 'Yes' ) : ?>
                    <li>Sports Court(s)</li>
                <?php endif; ?>

                <?php if( $property->get_sprinkler_system() == 'Yes' ) : ?>
                    <li>Sprinkler System</li>
                <?php endif; ?>

                <?php if( $property->get_vaulted_ceiling() == 'Yes' ) : ?>
                    <li>Vaulted Ceiling</li>
                <?php endif; ?>

                <?php if( $property->get_fitness_center() == 'Yes' ) : ?>
                    <li>Fitness Center</li>
                <?php endif; ?>

                <?php if( $property->get_basketball_court() == 'Yes' ) : ?>
                    <li>Basketball Court</li>
                <?php endif; ?>

                <?php if( $property->get_tennis_court() == 'Yes' ) : ?>
                    <li>Tennis Court</li>
                <?php endif; ?>

                <?php if( $property->get_near_transportation() == 'Yes' ) : ?>
                    <li>Near Transportation</li>
                <?php endif; ?>

                <?php if( $property->get_controlled_access() == 'Yes' ) : ?>
                    <li>Controlled Access</li>
                <?php endif; ?>

                <?php if( $property->get_over_55_active_community() == 'Yes' ) : ?>
                    <li>Active Over 55 Community</li>
                <?php endif; ?>

                <?php if( $property->get_assisted_living_community() == 'Yes' ) : ?>
                    <li>Assisted Living Community</li>
                <?php endif; ?>

                <?php if( $property->get_fenced_yard() == 'Yes' ) : ?>
                    <li>Fenced-In Yard</li>
                <?php endif; ?>

                <?php if( $property->get_furnished() == 'Yes' ) : ?>
                    <li>Furnished</li>
                <?php endif; ?>

                <?php if( $property->get_high_speed_internet() == 'Yes' ) : ?>
                    <li>High-Speed Internet</li>
                <?php endif; ?>

                <?php if( $property->get_on_site_laundry() == 'Yes' ) : ?>
                    <li>On-Site Laundry</li>
                <?php endif; ?>

                <?php if( $property->get_cable_sat_tv() == 'Yes' ) : ?>
                    <li>Cable/Satellite TV</li>
                <?php endif; ?>

                <?php if( $property->has_view_types()) : ?>
                    <li>View: <?php echo esc_html( implode(', ', $property->get_view_types() ) ); ?></li>
                <?php endif; ?>

                <?php if( $property->get_waterfront() == 'Yes' ) : ?>
                    <li>Waterfront</li>
                <?php endif; ?>

                <?php if( $property->get_wetbar() == 'Yes' ) : ?>
                    <li>Wetbar</li>
                <?php endif; ?>

                <?php if( $property->get_wired() == 'Yes' ) : ?>
                    <li>Network Wiring</li>
                <?php endif; ?>
            </ul>
        </div>

        <div class="section collapse expanded map">
            <h3>Map &amp; Directions</h3>
            <div class="content">
                <div
                  id="google-map"
                  data-latitude="<?php echo esc_attr( $property->get_latitude() ); ?>"
                  data-longitude="<?php echo esc_attr( $property->get_longitude() ); ?>"></div>
            </div>
        </div>
    </div>
    <div id="contact-form">
        <h2>Contact Us</h2>
        <form id="real-estate-contact-form" method="POST">

            <p>Fill out this brief form to contact us for more information about
                this property.</p>

            <label for="name">Name: <input type="text" name="name" id="name" placeholder="Your Name" /></label>
            <label for="email">Email: <input type="text" name="email" id="email" placeholder="Your Email" /></label>
            <label for="phone">Phone: <input type="text" name="phone" id="phone" placeholder="555-555-5555"/></label>
            <label for="message">Questions/Comments
                <textarea name="message" id="message"></textarea>
            </label>
            <input type="hidden" name="property_id" id="property_id" value="<?php the_ID(); ?>" />
            <input type="hidden" name="action" value="rwc_features_realestate_contact_form"/>
            <input type="submit" name="submit" id="submit" value="Send Message" />
        </form>
        <div id="agent-details">
            <h3>Agent Details</h3>
            <?php include( 'real-estate-agent.php' ); ?>
        </div>
        <div id="office-details">

            <h3>Office Details</h3>
            <?php include( 'real-estate-brokerage.php' ); ?>
        </div>
    </div>
</div>
