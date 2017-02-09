<?php

namespace RWC\Features\RealEstate\Zillow {

    class Feed extends \RWC\Object {

        /**
         * Creates and returns a WP_Query for querying all properties.
         *
         * @return \WP_Query Returns the query.
         */
        private static function get_query() {

            return new \WP_Query( array(
                'post_type' => 'rwc_real_estate',
                'posts_per_page' => -1
            ) );
        }

        /**
         * Renders the current post in the Loop.
         *
         * @param \WP_Query $query The current query being processed.
         *
         * @return void
         */
         private static function render_current( $query ) { ?>
                 <?php if ( self::get_meta( 'status' ) == 'For Rent' ) : ?>
                 <Listing>
                     <?php self::render_location( $query ); ?>
                     <?php self::render_listing_details( $query ); ?>
                     <?php self::render_rental_details( $query ); ?>
                     <?php self::render_basic_details( $query ); ?>
                     <?php self::render_pictures( $query ); ?>
                     <?php self::render_agent( $query ); ?>
                     <?php self::render_office( $query ); ?>
                     <?php self::render_open_houses( $query ); ?>
                     <?php self::render_neighborhood( $query ); ?>
                     <?php self::render_fees( $query ); ?>
                     <?php self::render_rich_details( $query ); ?>
                 </Listing>
             <?php endif; ?>
         <?php }

        /**
         * Renders the Fees section.
         *
         * @param \WP_Query $query The WordPress query.
         *
         * @return void
         */
        private static function render_rich_details( \WP_Query $query ) { ?>
            <RichDetails>
                <?php self::echo_meta( 'additional-features', 'AdditionalFeatures' ); ?>
                <Appliances>
                    <?php if( self::get_meta('dishwasher')) : ?><Appliance>Dishwasher</Appliance><?php endif; ?>
                    <?php if( self::get_meta('dryer')) : ?><Appliance>Dryer</Appliance><?php endif; ?>
                    <?php if( self::get_meta('freezer')) : ?><Appliance>Freezer</Appliance><?php endif; ?>
                    <?php if( self::get_meta('garbage-disposal')) : ?><Appliance>GargbageDisposal</Appliance><?php endif; ?>
                    <?php if( self::get_meta('microwave')) : ?><Appliance>Microwave</Appliance><?php endif; ?>
                    <?php if( self::get_meta('range-oven')) : ?><Appliance>RangeOven</Appliance><?php endif; ?>
                    <?php if( self::get_meta('refrigerator')) : ?><Appliance>Refrigerator</Appliance><?php endif; ?>
                    <?php if( self::get_meta('trash-compactor')) : ?><Appliance>TrashCompactor</Appliance><?php endif; ?>
                    <?php if( self::get_meta('washer')) : ?><Appliance> Washer</Appliance><?php endif; ?>
                </Appliances>
                <?php self::echo_meta( 'architecture-style', 'ArchitecturalStyle'); ?>
                <?php self::echo_meta( 'attic', 'Attic'); ?>
                <?php self::echo_meta( 'barbequeue-area', 'BarbecueArea'); ?>
                <?php self::echo_meta( 'basement', 'Basement' ); ?>

                <?php self::echo_meta( 'building-unit-count', 'BuildingUnitCount' ); ?>
                <?php self::echo_meta( 'cable-ready', 'CableReady' ); ?>
                <?php self::echo_meta( 'ceiling-fan', 'CeilingFan' ); ?>
                <?php self::echo_meta( 'CondoFloorNum', 'CondoFloorNum' ); ?>
                <?php  self::echo_values( 'cooling-system', 'CoolingSystems', 'CoolingSystem' ); ?>
                <?php self::echo_meta( 'deck', 'Deck' ); ?>
                <?php self::echo_meta( 'disabled-access', 'DisabledAccess' ); ?>
                <?php self::echo_meta( 'dock', 'Dock' ); ?>
                <?php self::echo_meta( 'Doorman', 'Doorman' ); ?>
                <?php self::echo_meta( 'double-pane-windows', 'DoublePaneWindows' ); ?>
                <?php self::echo_meta( 'elevator', 'Elevator' ); ?>
                <?php self::echo_values( 'exterior-types', 'ExteriorTypes', 'ExteriorType' ); ?>
                <?php self::echo_meta( 'fireplace', 'Fireplace' ); ?>
                <?php self::echo_values( 'floor-coverings', 'FloorCoverings', 'FloorCovering' ); ?>
                <?php self::echo_meta( 'garden', 'Garden'); ?>
                <?php self::echo_meta( 'gated-entry', 'GatedEntry' ); ?>
                <?php self::echo_meta( 'greenhouse', 'Greenhouse' ); ?>
                <?php self::echo_values( 'heating-fuels', 'HeatingFuels', 'HeatingFuel' ); ?>
                <?php self::echo_values( 'heating-systems', 'HeatingSystems', 'HeatingSystem' ); ?>
                <?php self::echo_meta( 'hot-tub-spa', 'HottubSpa'); ?>
                <?php self::echo_meta( 'intercom', 'Intercom' ); ?>
                <?php self::echo_meta( 'jetted-bathtub', 'JettedBathTub' ); ?>
                <?php self::echo_meta( 'lawn', 'Lawn' ); ?>
                <?php self::echo_meta( 'mother-in-law', 'MotherInLaw' ); ?>
                <?php self::echo_meta( 'num-floors', 'NumFloors' ); ?>
                <?php self::echo_meta( 'num-parking-spaces', 'NumParkingSpaces' ); ?>
                <?php self::echo_values( 'parking-types', 'ParkingTypes', 'ParkingType' ); ?>
                <?php self::echo_meta( 'patio', 'Patio' ); ?>
                <?php self::echo_meta( 'pond', 'Pond' ); ?>
                <?php self::echo_meta( 'pool', 'Pool' ); ?>
                <?php self::echo_meta( 'porch', 'Porch' ); ?>
                <?php self::echo_values( 'roof-types', 'RoofTypes', 'RoofType' ); ?>
                <?php self::echo_meta( 'room-count', 'RoomCount' ); ?>
                <?php self::echo_values( 'rooms', 'Rooms', 'Room' ); ?>
                <?php self::echo_meta( 'rv-parking', 'RvParking' ); ?>
                <?php self::echo_meta( 'sauna', 'Sauna' ); ?>
                <?php self::echo_meta( 'security-system', 'SecuritySystem' ); ?>
                <?php self::echo_meta( 'skylight', 'Skylight' ); ?>
                <?php self::echo_meta( 'sports-court', 'SportsCourt' ); ?>
                <?php self::echo_meta( 'sprinkler-system', 'SprinklerSystem' ); ?>
                <?php self::echo_meta( 'vaulted-ceiling', 'VaultedCeiling' ); ?>
                <?php self::echo_meta( 'fitness-center', 'FitnessCenter' ); ?>
                <?php self::echo_meta( 'basketball-court', 'BasketballCourt' ); ?>
                <?php self::echo_meta( 'tennis-court', 'TennisCourt' ); ?>
                <?php self::echo_meta( 'near-transportation', 'NearTransportation' ); ?>
                <?php self::echo_meta( 'controlled-access', 'ControlledAccess' ); ?>
                <?php self::echo_meta( 'over-55-active-community', 'Over55ActiveCommunity' ); ?>
                <?php self::echo_meta( 'assisted-living-community', 'AssistedLivingCommunity' ); ?>
                <?php self::echo_meta( 'storage', 'Storage' ); ?>
                <?php self::echo_meta( 'fenced-yard', 'FencedYard' ); ?>
                <?php self::echo_meta( 'property-name', 'PropertyName' ); ?>
                <?php self::echo_meta( 'furnished', 'Furnished' ); ?>
                <?php self::echo_meta( 'high-speed-internet', 'HighspeedInternet' ); ?>
                <?php self::echo_meta( 'on-site-laundry', 'OnsiteLaundry' ); ?>
                <?php self::echo_meta( 'cable-sat-tv', 'CableSatTV' ); ?>
                <?php self::echo_values( 'view-types', 'ViewTypes', 'ViewType' ); ?>
                <?php self::echo_meta( 'waterfront', 'Waterfront' ); ?>
                <?php self::echo_meta( 'wetbar', 'Wetbar' ); ?>
                <?php self::echo_meta( 'what-owners-love', 'WhatOwnerLoves' ); ?>
                <?php self::echo_meta( 'wired', 'Wired' ); ?>
                <?php self::echo_meta( 'year-updated', 'YearUpdated' ); ?>
            </RichDetails>
        <?php }

        private static function echo_values( $field, $container, $single ) { ?>
            <?php $values = self::get_meta( $field ); ?>
            <?php if( $values !== false && count( $values ) > 0 ) : ?>
                <?php echo "<$container>"; ?>
                    <?php foreach( $values as $value ) : ?>
                        <?php echo "<$single>"; ?><?php echo esc_html( $value ); ?><?php echo "</$single>"; ?>
                    <?php endforeach; ?>
                <?php echo "</$container>"; ?>
            <?php endif; ?>


        <?php }

        /**
         * Renders the Fees section.
         *
         * @param \WP_Query $query The WordPress query.
         *
         * @return void
         */
        private static function render_fees( \WP_Query $query ) { ?>
            <?php $fees = self::get_meta( 'fees' ); ?>
            <?php if( ! empty( $fees )) : ?>
                <Fees>
                    <?php foreach( $fees as $fee ) : ?>
                        <Fee>
                            <FeeType><?php echo esc_html( $fee[ 'fee-type' ] ); ?></FeeType>
                            <FeeAmount><?php echo esc_html( $fee[ 'fee-amount' ] ); ?></FeeAmount>
                            <FeePeriod><?php echo esc_html( $fee[ 'fee-period' ] ); ?></FeePeriod>
                        </Fee>
                    <?php endforeach; ?>
                </Fees>
            <?php endif; ?>
        <?php }

        /**
         * Renders the Neighborhood section.
         *
         * @param \WP_Query $query The WordPress query.
         *
         * @return void
         */
        private static function render_neighborhood( \WP_Query $query ) { ?>
            <Neighborhood>
                <?php self::echo_meta( 'listing-neighborhood', 'Name' ); ?>
            </Neighborhood>
        <?php }

        /**
         * Renders the Open Houses section.
         *
         * @param \WP_Query $query The WordPress query.
         *
         * @return void
         */
        private static function render_open_houses( \WP_Query $query ) { ?>
            <?php $openHouses = self::get_meta( 'open-houses' ); ?>
            <?php if( ! empty( $openHouses )) : ?>
                <OpenHouses>
                    <?php foreach( self::get_meta( 'open-houses' ) as $openHouse ) : ?>
                        <OpenHouse>
                            <Date><?php echo date( 'Y-m-d', strtotime( $openHouse['open-house-date'] ) ); ?></Date>
                            <StartTime><?php echo esc_html( $openHouse[ 'open-house-start-time' ] ); ?></StartTime>
                            <EndTime><?php echo esc_html( $openHouse[ 'open-house-start-time' ] ); ?></EndTime>
                        </OpenHouse>
                    <?php endforeach; ?>
                </OpenHouses>
            <?php endif; ?>
        <?php }

        /**
         * Renders the Office block
         *
         * @param \WP_Query $query The query.
         *
         * @return void
         */
        private static function render_office( \WP_Query $query ) { ?>
            <Office>
                <?php self::echo_meta( 'office-brokerage-name', 'BrokerageName' ); ?>
                <?php self::echo_meta( 'office-brokerage-phone', 'BrokerPhone' ); ?>
                <?php self::echo_meta( 'office-brokerage-email', 'BrokerEmail' ); ?>
                <?php self::echo_meta( 'office-brokerage-website', 'BrokerWebsite' ); ?>
                <?php self::echo_meta( 'office-brokerage-street-address', 'StreetAddress' ); ?>
                <?php self::echo_meta( 'office-brokerage-unit-number', 'UnitNumber' ); ?>
                <?php self::echo_meta( 'office-brokerage-city', 'City' ); ?>
                <?php self::echo_meta( 'office-brokerage-state', 'State' ); ?>
                <?php self::echo_meta( 'office-brokerage-zip', 'Zip' ); ?>
                <?php self::echo_meta( 'office-brokerage-office-name', 'OfficeName' ); ?>
                <?php self::echo_meta( 'office-brokerage-franchise-name', 'FranchiseName' ); ?>
            </Office>
        <?php }

        /**
         * Renders the Agent block.
         *
         * @param \WP_Query $query The query.
         *
         * @return void
         */
        private static function render_agent( \WP_Query $query ) { ?>
            <Agent>
                <?php self::echo_meta( 'agent-first-name', 'FirstName' ); ?>
                <?php self::echo_meta( 'agent-last-name', 'LastName' ); ?>
                <?php self::echo_meta( 'agent-email', 'EmailAddress' ); ?>
                <?php $pictureUrl = self::get_meta( 'agent-picture-url' ); ?>
                <?php if( ! empty( $pictureUrl ) ) : ?>
                    <?php $pictureUrl = json_decode( $pictureUrl ); ?>
                    <PictureUrl><?php echo esc_html( $pictureUrl[0]->url ); ?></PictureUrl>
                <?php endif; ?>
                <?php self::echo_meta( 'agent-office-line-number', 'OfficeLineNumber' ); ?>
                <?php self::echo_meta( 'agent-mobile-phone-line', 'MobilePhoneLineNumber' ); ?>
                <?php self::echo_meta( 'agent-fax-number', 'FaxLineNumber' ); ?>
                <?php self::echo_meta( 'agent-license-number', 'LicenseNum'  ); ?>
            </Agent>
        <?php }

        /**
         * Renders the Pictures block.
         *
         * @param \WP_Query $query The query to render.
         *
         * @return void
         */
        private static function render_pictures( \WP_Query $query ) { ?>
            <?php $pictures = self::get_meta( 'images' ); ?>
            <?php if( ! empty( $pictures )  ) : ?>
                <?php $pictures = json_decode( $pictures ); ?>
                <Pictures>
                    <?php foreach( $pictures as $picture ) : ?>
                        <?php self::render_picture( $picture ); ?>
                    <?php endforeach; ?>
                </Pictures>
            <?php endif; ?>
        <? }

        /**
         * Renders an individual Picture.
         *
         * @param Object $picture The picture to render.
         *
         * @return void
         */
        private static function render_picture( $picture) { ?>
            <Picture>
                <PictureUrl><?php echo esc_html( wp_get_attachment_url(  $picture->id ) ); ?></PictureUrl>
                <Caption><?php echo esc_html(get_the_title( $picture->id ) ); ?></Caption>
            </Picture>
        <?php }

        /**
         * Renders the BasicDetails block for the current post in the Loop.
         *
         * @param \WP_Query $query The current query being processed.
         *
         * @return void
         */
        private static function render_basic_details( $query ) { ?>
            <BasicDetails>
                <?php self::echo_meta( 'property-type', 'PropertyType' ); ?>
                <?php self::echo_meta( 'title', 'Title' ); ?>
                <?php self::echo_meta( 'description', 'Description' ); ?>
                <?php self::echo_meta( 'bedrooms', 'Bedrooms' ); ?>
                <?php self::echo_meta( 'bathrooms', 'Bathrooms' ); ?>
                <?php self::echo_meta( 'full-bathrooms', 'FullBathrooms' ); ?>
                <?php self::echo_meta( 'half-bathrooms', 'HalfBathrooms' ); ?>
                <?php self::echo_meta( 'quarter-bathrooms', 'QuarterBathrooms' ); ?>
                <?php self::echo_meta( 'three-quarter-bathrooms', 'ThreeQuarterBathrooms' ); ?>
                <?php self::echo_meta( 'living-area', 'LivingArea' ); ?>
                <?php self::echo_meta( 'lot-size', 'LotSize' ); ?>
                <?php self::echo_meta( 'year-built', 'YearBuilt' ); ?>
            </BasicDetails>
        <?php }

        /**
         * Renders the ListingDetails block.
         *
         * @param \WP_Query $query The WordPress query.
         *
         * @return void
         */
        private static function render_listing_details( $query ) { ?>
            <ListingDetails>
                <?php self::echo_meta( 'status', 'Status'); ?>
                <?php self::echo_meta( 'price', 'Price' ); ?>
                <?php self::echo_meta( 'listing-url', 'ListingUrl' ); ?>
                <?php self::echo_meta( 'mls-id', 'MlsId' ); ?>
                <?php self::echo_meta( 'mls-name', 'MlsName' ); ?>
                <?php self::echo_meta( 'provider-listing-id', 'ProviderListingId' ); ?>
                <?php self::echo_meta( 'virtual-tour-url', 'VirtualTourUrl' ); ?>
                <?php self::echo_meta( 'listing-email', 'ListingEmail' ); ?>
                <?php self::echo_meta( 'always-email-agent', 'AlwaysEmailAgent' ); ?>
                <?php self::echo_meta( 'short-sale', 'ShortSale' ); ?>
                <?php self::echo_meta( 'reo', 'REO' ); ?>
                <?php self::echo_meta( 'coming-soon-on-market-date', 'ComingSoonOnMarketDate' ); ?>
            </ListingDetails>
        <?php }

        /**
         * Renders the RentalDetails block.
         *
         * @param \WP_Query $query The WordPress query.
         *
         * @return void
         */
        private static function render_rental_details( $query ) { ?>
            <RentalDetails>
                <?php self::echo_availability(); ?>
                <?php self::echo_meta( 'lease-term', 'LeaseTerm' ); ?>
                <?php self::echo_meta( 'deposit-fees', 'DepositFees' ); ?>
                <UtilitiesIncluded>
                    <?php self::echo_meta( 'utilities-water-included', 'Water' ); ?>
                    <?php self::echo_meta( 'utilities-sewage-included', 'Sewage' ); ?>
                    <?php self::echo_meta( 'utilities-garbage-included', 'Garbage' ); ?>
                    <?php self::echo_meta( 'utilities-electricity-included', 'Electricity' ); ?>
                    <?php self::echo_meta( 'utilities-gas-included', 'Gas' ); ?>
                    <?php self::echo_meta( 'utilities-internet-included', 'Internet' ); ?>
                    <?php self::echo_meta( 'utilities-cable-included', 'Cable' ); ?>
                    <?php self::echo_meta( 'utilities-sattv-included', 'SatTv' ); ?>
                </UtilitiesIncluded>
                <?php self:: echo_meta( 'pets-allowed', 'PetsAllowed' ); ?>
            </RentalDetails>
        <?php }

        /**
         * Renders the Location block.
         *
         * @param \WP_Query $query The WordPress query.
         *
         * @return void
         */
        private static function render_location( $query ) { ?>
            <Location>
                <?php self::echo_meta( 'street-address', 'StreetAddress'); ?>
                <?php self::echo_meta( 'unit-number', 'UnitNumber' ); ?>
                <?php self::echo_meta( 'city', 'City' ); ?>
                <?php self::echo_meta( 'state', 'State' ); ?>
                <?php self::echo_meta( 'zip', 'Zip' ); ?>
                <?php self::echo_meta( 'latitude', 'Lat' ); ?>
                <?php self::echo_meta( 'longitude', 'Long' ); ?>
                <?php self::echo_meta( 'display-address', 'DisplayAddress' ); ?>
            </Location>
        <?php }

        public static function render_feed() {

            // Output the Content type.
            header('Content-Type: text\xml' );

            // Get Transient
            $trans = get_transient( 'rwc_features_real_estate_zillow_feed' );

            // If the transient isn't set, create it.
            if( ! $trans ) {


                $dom = new \DomDocument();
                ob_start(); ?>
                <?php $query = self::get_query(); ?>
                <Listings>
                    <?php if( $query->have_posts()) : ?>
                        <?php while($query->have_posts() ) : $query->the_post(); ?>
                            <?php self::render_current( $query ); ?>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </Listings>
                <?php wp_reset_postdata(); ?>
                <?php
                $dom->loadXML( ob_get_clean() );
                $trans = $dom->saveXML();
                set_transient(  'rwc_features_real_estate_zillow_feed' , $trans, 60 * 10 ); // 10 minutes
            }
            echo $trans;
        }

        /**
         * Renders the Availability tag for the current Property.
         *
         * @return void
         */
        private static function echo_availability() {

            // Get the base Availability field.
            $availability = self::get_meta( 'availability' );

            // If it's set to custom, check the custom availability value.
            if( 'Custom' == $availability ) {

                $value = self::get_meta( 'custom-availability' );

                // Was a custom availability set?
                if( $value ) {

                    // Yes, convert it to the valid date format.
                    $availability = date( 'Y-m-d', strtotime( $value ));
                }

            }

            // Finally, if we have an availability date, use it.
            if( $availability ) { ?>
                <Availability><?php echo esc_html( $availability ); ?></Availability>
            <?php }
        }

        /**
         * Returns a meta value.
         *
         * @param string $option The meta value to return.
         *
         * @return mixed Returns the meta value.
         */
        private static function get_meta( $option ) {

            $meta = get_post_meta( get_the_ID(),
                'rwc-real-estate-metabox', true );

            return (isset( $meta[ $option ])) ? $meta[ $option ] : false;
        }

        /**
         * Echoes a meta option if it's set.
         *
         * @param string $option The option to echo.
         * @param string $tag    The name of the tag to render.
         *
         * @return void
         */
        public static function echo_meta( $option, $tag ) {

            $meta = self::get_meta( $option );

            // Don't continue if it's not set.
            if( $meta == false ) return;

            printf( '<%s>%s</%s>',
                esc_attr( $tag),
                esc_html( $meta),
                esc_attr( $tag )
            );
        }

        /**
         * Echos a metavalue as a data formatted for the Zillow feed.
         *
         * @param string $option The metabox option to render.
         * @param string $tag    The tag to render.
         *
         * @return void
         */
        public static function echo_meta_date( $option, $tag ) {

            $meta = get_post_meta( get_the_ID(), 'rwc-real-estate-metabox', true );


            // Don't continue if it's not set.
            if( ! isset( $meta[ $option ])) return;

            $time = strtotime( $meta[ $option ] );

            printf( '<%s>%s</%s>',
                esc_attr( $tag),
                esc_html( date( 'Y-m-d', $time ) ),
                esc_attr( $tag )
            );
        }
    }
}
