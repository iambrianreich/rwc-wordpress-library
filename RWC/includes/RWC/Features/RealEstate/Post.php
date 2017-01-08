<?php

namespace RWC\Features\RealEstate {

    class Post extends \RWC\PostWrapper {

        /**
         * Prefixed used for all metadata.
         *
         * @var string
         */
        const  PREFIX = 'rwc_real_estate_';

        /**
         * The WP_Post wrapped by this Property.
         *
         * @var    WP_Post
         * @access protected
         */
        private $_post;

        /**
         * The RealEstate Metabox storage. Set on the first call to retrieve
         * metabox data.
         *
         * @var    \RWC\Metabox\Storage\StorageAbstract
         * @access private
         */
        private $_re_storage = null;

        /**
         * Initializes the Property.
         *
         * @param WP_Post $post The WP_Post wrapped by this Property.
         */
        public function __construct( $post ) {

            $this->_post = $post;
        }

        /**
         * Returns the first line of the street address.
         *
         * @return string Returns the first line of the street address.
         */
        public function get_address_1() {

            return $this->get_re_meta_value( 'street-address' );
        }

        /**
         * Returns the zipcode.
         *
         * @return string Returns the zipcode.
         */
        public function get_zip_code() {

            return $this->get_re_meta_value( 'zip' );
        }

        /**
         * Returns the property's status as an attribute-safe string which can
         * be used as a CSS class.
         *
         * @return string Returns the status as an attribute-safe string.
         */
        public function get_status_class() {

            // Replace spaces with dashes, make lowercase.
            return esc_attr( strtolower( str_replace(
                ' ', '-', $this->get_status() ) ) );
        }

        /**
         * Returns a list of attachment ids for all of the images associated
         * with the rental property.
         *
         * @return array Returns the image list.
         */
        public function get_image_ids() {

            $images = $this->get_re_meta_value( 'images', null );

            // If no images, return an empty array.
            if( $images == null ) return array();

            // Decode the JSON array.
            $images = json_decode( $images );

            // We're going to create a new array with the featured image first.
            $fixed = array();

            // If the featured image has been set, add it.
            $post_thumbnail_id = get_post_thumbnail_id( $this->_post->ID );
            if( $post_thumbnail_id ); $fixed[] = $post_thumbnail_id;
            foreach( $images as $image ) {
                if( $image->id == $post_thumbnail_id ) continue;
                $fixed[] = $image->id;
            }

            return $fixed;
        }

        /**
         * Returns the user friendly property type. For example if the property
         * type is single family, the value returned is "Single Famility"
         *
         * @return string Returns the user friendly property type.
         */
        public function get_property_type_text() {

            // TODO Pull from Metabox
            $options = [
                'SingleFamily' => 'Single Family',
                'Condo' => 'Condo',
                'Townhouse' => 'Townhouse',
                'Coop' => 'Coop',
                'MultiFamily' => 'Multi-Family',
                'Manufactured' => 'Manufactured',
                'VacantLand' => 'Vacant Land',
                'Other' => 'Other',
                'Apartment' => 'Apartment'
            ];

            $type = $this->get_property_type();

            if( isset( $options[ $type ] ) ) {

                return $options[ $type ];
            }

            return '';
        }

        /**
         * Returns the Lease Terms for a rental property (Monthly, etc.).
         *
         * @return string Returns the Lease Terms for a Rental Property.
         */
        public function get_lease_terms() {

            return $this->get_re_meta_value( 'lease-term' );
        }

        public function get_lease_terms_abbr() {

                return [
                    ''  => '',
                    'ContactForDetails' => '',
                    'Monthly' => 'mo',
                    'SixMonths' => '6 mo',
                    'OneYear' => 'yr',
                    'RentToOwn' => 'RTO'
                ][ $this->get_lease_terms() ];
        }

        /**
         * Returns price text block, which specifies both the price and the
         * terms. For "For Sale" (Active) properties, this will be the price.
         * For Contingent and Pending properties, it will be the price plus
         * a note that they are under agreement but not sold. For Rentals,
         * this will be the rental price plus rental term (Monthly, etc.).
         *
         * @return string Returns the price text block.
         */
        public function get_price_text() {

            switch( $this->get_status() ) {

                // For Sale
                case 'Active' :
                    return sprintf( '$%s', $this->get_price() );

                case 'Contingent' :
                case 'Pending' :

                    return sprintf( '$%s (Pending Agreement)',
                        $this->get_price() );

                // For Rent
                case 'For Rent' :

                    return sprintf( '$%s / %s',
                        $this->get_price(),
                        $this->get_lease_terms_abbr() );
            }
        }

        public function get_latitude() {

            return get_post_meta($this->_post->ID, 'post_latitude',  true );

        }

        public function get_longitude() {

            return get_post_meta( $this->_post->ID, 'post_longitude',  true );
        }

        /**
         * Returns a field from the real estate metadata.
         *
         * @param string $name    The name of the real estate metadata field.
         * @param mixed  $default The value to return if the meta is not set.
         *
         * @return mixed Returns the metadata value.
         */
        public function get_re_meta_value( $name, $default = false ) {

            // Retrieve the storage once.
            if( $this->_re_storage == null ) {

                // Get the RealEstate feature
                $re = \RWC\Library::load()->get_loaded_feature( 'RealEstate' );

                // If we can't find it, well.. that's a major issue.
                if( $re == null ) {

                    throw new Exception( 'Cannot get meta value because the ' .
                        'RealEstate feature is not loaded.' );
                }

                // Get the metabox and metabox storage.
                $mb = $re->get_realestate_metabox();
                $this->_re_storage = $mb->get_storage();
            }

            // Get the value.
            $value = $this->_re_storage ->get( $this->_post->ID, $name );

            // If no value set, return default.
            if( $value === false ) {
                return $default;
            }

            // Return value.
            return $value;
        }

        /**
         * Returns a list of email addresses to contact when information is
         * requests on this property.
         *
         * If the listing email property has been specified, that email address
         * will be returned. If the always email agent flag has been set to
         * "Yes", the listing agent's email address will be included as well.
         * if the listing email property is not set, the listing agent will
         * always be emailed.
         *
         * @return array Returns a list of email addresses.
         */
        public function get_contact_recipient_list() {

            $emails = [];

            // If there is a listing email, go ahead and use it.
            if( $this->has_listing_email() && $this->get_listing_email() != '' ) {

                // Add listing email
                $emails[] =  $this->get_listing_email();

                // If always email agent, add the agent
                if( $this->has_always_email_agent() && $this->get_always_email_agent() == 'Yes' ) {

                    // make sure agent email address is set.
                    if( $this->has_agent_email() && $this->get_agent_email() != '' ) {
                        $emails[] = $this->get_agent_email();
                    }
                }
            } else {

                // Email agent instead, if specified.
                if( $this->has_agent_email() && $this->get_agent_email() != '' ) {
                    $emails[] = $this->get_agent_email();
                }
            }

            // If no other emails specified, use site admin.
            if( empty( $emails ) ) {
                $emails[] = get_option( 'admin_email' );
            }

            return $emails;
        }

        /**
         * Returns the lowest Property price in the database.
         *
         * @return string Returns the lowest propery price in the database.
         */
        public function get_office_brokerage_price_range_min() {

            global $wpdb;

            return $wpdb->get_var( "SELECT MIN(meta_value) FROM " .
                "$wpdb->postmeta WHERE " .
                    "meta_key = 'rwc-real-estate-metabox_price' " .
                    "AND meta_value is not null AND meta_value != '';" );
        }

        /**
         * Returns the highest Property price in the database.
         *
         * @return string Returns the highest property price in the database.
         */
        public function get_office_brokerage_price_range_max() {

             global $wpdb;

             return $wpdb->get_var( "SELECT MAX(meta_value) FROM " .
                 "$wpdb->postmeta WHERE " .
                     "meta_key = 'rwc-real-estate-metabox_price' " .
                     "AND meta_value is not null AND meta_value != '';" );
         }

        public function generate_class() {

            // Get the RealEstate feature
            $re = \RWC\Library::load()->get_loaded_feature( 'RealEstate' );

            // If we can't find it, well.. that's a major issue.
            if( $re == null ) {

                throw new Exception( 'Cannot get meta value because the ' .
                    'RealEstate feature is not loaded.' );
            }

            // Get the metabox and metabox storage.
            $mb = $re->get_realestate_metabox();

            $code = "class Post extends \RWC\PostWrapper {\r\n\r\n";

            $fields = $mb->get_fields();

            foreach( $fields as $id => $value ) {

                $cleanId = str_replace('-', '_', $id );
                $code .= "\tpublic function get_" . $cleanId . "() {\r\n";
                $code .= sprintf( "\t\treturn \$this->get_re_meta_value( '%s' );\r\n", $id );
                $code .= "\t}\r\n\r\n";

                $code .= "\tpublic function has_" . $cleanId . "() {\r\n";
                $code .= sprintf( "\t\treturn (! empty(\$this->get_%s() ));\r\n", $cleanId );
                $code .= "\t}\r\n\r\n";
            }
            $code .= "}";

            return $code;
        }

        /************** AUTO-GENERATED METHODS *****************/

        public function get_street_address() {
    		return $this->get_re_meta_value( 'street-address' );
    	}

    	public function has_street_address() {
    		return (! empty($this->get_street_address() ));
    	}

    	public function get_unit_number() {
    		return $this->get_re_meta_value( 'unit-number' );
    	}

    	public function has_unit_number() {
    		return (! empty($this->get_unit_number() ));
    	}

    	public function get_city() {
    		return $this->get_re_meta_value( 'city' );
    	}

    	public function has_city() {
    		return (! empty($this->get_city() ));
    	}

    	public function get_state() {
    		return $this->get_re_meta_value( 'state' );
    	}

    	public function has_state() {
    		return (! empty($this->get_state() ));
    	}

    	public function get_zip() {
    		return $this->get_re_meta_value( 'zip' );
    	}

    	public function has_zip() {
    		return (! empty($this->get_zip() ));
    	}

    	public function get_display_address() {
    		return $this->get_re_meta_value( 'display-address' );
    	}

    	public function has_display_address() {
    		return (! empty($this->get_display_address() ));
    	}

    	public function get_status() {
    		return $this->get_re_meta_value( 'status' );
    	}

    	public function has_status() {
    		return (! empty($this->get_status() ));
    	}

    	public function get_price() {
    		return $this->get_re_meta_value( 'price' );
    	}

    	public function has_price() {
    		return (! empty($this->get_price() ));
    	}

    	public function get_listing_url() {
    		return $this->get_re_meta_value( 'listing-url' );
    	}

    	public function has_listing_url() {
    		return (! empty($this->get_listing_url() ));
    	}

    	public function get_mls_id() {
    		return $this->get_re_meta_value( 'mls-id' );
    	}

    	public function has_mls_id() {
    		return (! empty($this->get_mls_id() ));
    	}

    	public function get_mls_name() {
    		return $this->get_re_meta_value( 'mls-name' );
    	}

    	public function has_mls_name() {
    		return (! empty($this->get_mls_name() ));
    	}

    	public function get_provider_listing_id() {
    		return $this->get_re_meta_value( 'provider-listing-id' );
    	}

    	public function has_provider_listing_id() {
    		return (! empty($this->get_provider_listing_id() ));
    	}

    	public function get_virtual_tour_url() {
    		return $this->get_re_meta_value( 'virtual-tour-url' );
    	}

    	public function has_virtual_tour_url() {
    		return (! empty($this->get_virtual_tour_url() ));
    	}

    	public function get_listing_email() {
    		return $this->get_re_meta_value( 'listing-email' );
    	}

    	public function has_listing_email() {
    		return (! empty($this->get_listing_email() ));
    	}

    	public function get_listing_neighborhood() {
    		return $this->get_re_meta_value( 'listing-neighborhood' );
    	}

    	public function has_listing_neighborhood() {
    		return (! empty($this->get_listing_neighborhood() ));
    	}

    	public function get_always_email_agent() {
    		return $this->get_re_meta_value( 'always-email-agent' );
    	}

    	public function has_always_email_agent() {
    		return (! empty($this->get_always_email_agent() ));
    	}

    	public function get_short_sale() {
    		return $this->get_re_meta_value( 'short-sale' );
    	}

    	public function has_short_sale() {
    		return (! empty($this->get_short_sale() ));
    	}

    	public function get_reo() {
    		return $this->get_re_meta_value( 'reo' );
    	}

    	public function has_reo() {
    		return (! empty($this->get_reo() ));
    	}

    	public function get_coming_soon_on_market_date() {
    		return $this->get_re_meta_value( 'coming-soon-on-market-date' );
    	}

    	public function has_coming_soon_on_market_date() {
    		return (! empty($this->get_coming_soon_on_market_date() ));
    	}

    	public function get_availability() {
    		return $this->get_re_meta_value( 'availability' );
    	}

    	public function has_availability() {
    		return (! empty($this->get_availability() ));
    	}

    	public function get_custom_availability() {
    		return $this->get_re_meta_value( 'custom-availability' );
    	}

    	public function has_custom_availability() {
    		return (! empty($this->get_custom_availability() ));
    	}

    	public function get_lease_term() {
    		return $this->get_re_meta_value( 'lease-term' );
    	}

    	public function has_lease_term() {
    		return (! empty($this->get_lease_term() ));
    	}

    	public function get_deposit_fees() {
    		return $this->get_re_meta_value( 'deposit-fees' );
    	}

    	public function has_deposit_fees() {
    		return (! empty($this->get_deposit_fees() ));
    	}

    	public function get_utilities_water_included() {
    		return $this->get_re_meta_value( 'utilities-water-included' );
    	}

    	public function has_utilities_water_included() {
    		return (! empty($this->get_utilities_water_included() ));
    	}

    	public function get_utilities_sewage_included() {
    		return $this->get_re_meta_value( 'utilities-sewage-included' );
    	}

    	public function has_utilities_sewage_included() {
    		return (! empty($this->get_utilities_sewage_included() ));
    	}

    	public function get_utilities_garbage_included() {
    		return $this->get_re_meta_value( 'utilities-garbage-included' );
    	}

    	public function has_utilities_garbage_included() {
    		return (! empty($this->get_utilities_garbage_included() ));
    	}

    	public function get_utilities_electricity_included() {
    		return $this->get_re_meta_value( 'utilities-electricity-included' );
    	}

    	public function has_utilities_electricity_included() {
    		return (! empty($this->get_utilities_electricity_included() ));
    	}

    	public function get_utilities_gas_included() {
    		return $this->get_re_meta_value( 'utilities-gas-included' );
    	}

    	public function has_utilities_gas_included() {
    		return (! empty($this->get_utilities_gas_included() ));
    	}

    	public function get_utilities_internet_included() {
    		return $this->get_re_meta_value( 'utilities-internet-included' );
    	}

    	public function has_utilities_internet_included() {
    		return (! empty($this->get_utilities_internet_included() ));
    	}

    	public function get_utilities_cable_included() {
    		return $this->get_re_meta_value( 'utilities-cable-included' );
    	}

    	public function has_utilities_cable_included() {
    		return (! empty($this->get_utilities_cable_included() ));
    	}

    	public function get_utilities_sattv_included() {
    		return $this->get_re_meta_value( 'utilities-sattv-included' );
    	}

    	public function has_utilities_sattv_included() {
    		return (! empty($this->get_utilities_sattv_included() ));
    	}

    	public function get_pets_allowed() {
    		return $this->get_re_meta_value( 'pets-allowed' );
    	}

    	public function has_pets_allowed() {
    		return (! empty($this->get_pets_allowed() ));
    	}

    	public function get_property_type() {
    		return $this->get_re_meta_value( 'property-type' );
    	}

    	public function has_property_type() {
    		return (! empty($this->get_property_type() ));
    	}

    	public function get_title() {
    		return $this->get_re_meta_value( 'title' );
    	}

    	public function has_title() {
    		return (! empty($this->get_title() ));
    	}

    	public function get_description() {
    		return $this->get_re_meta_value( 'description' );
    	}

    	public function has_description() {
    		return (! empty($this->get_description() ));
    	}

    	public function get_bedrooms() {
    		return $this->get_re_meta_value( 'bedrooms' );
    	}

    	public function has_bedrooms() {
    		return (! empty($this->get_bedrooms() ));
    	}

    	public function get_bathrooms() {
    		return $this->get_re_meta_value( 'bathrooms' );
    	}

    	public function has_bathrooms() {
    		return (! empty($this->get_bathrooms() ));
    	}

    	public function get_full_bathrooms() {
    		return $this->get_re_meta_value( 'full-bathrooms' );
    	}

    	public function has_full_bathrooms() {
    		return (! empty($this->get_full_bathrooms() ));
    	}

    	public function get_half_bathrooms() {
    		return $this->get_re_meta_value( 'half-bathrooms' );
    	}

    	public function has_half_bathrooms() {
    		return (! empty($this->get_half_bathrooms() ));
    	}

    	public function get_quarter_bathrooms() {
    		return $this->get_re_meta_value( 'quarter-bathrooms' );
    	}

    	public function has_quarter_bathrooms() {
    		return (! empty($this->get_quarter_bathrooms() ));
    	}

    	public function get_three_quarter_bathrooms() {
    		return $this->get_re_meta_value( 'three-quarter-bathrooms' );
    	}

    	public function has_three_quarter_bathrooms() {
    		return (! empty($this->get_three_quarter_bathrooms() ));
    	}

    	public function get_living_area() {
    		return $this->get_re_meta_value( 'living-area' );
    	}

    	public function has_living_area() {
    		return (! empty($this->get_living_area() ));
    	}

    	public function get_lot_size() {
    		return $this->get_re_meta_value( 'lot-size' );
    	}

    	public function has_lot_size() {
    		return (! empty($this->get_lot_size() ));
    	}

    	public function get_year_built() {
    		return $this->get_re_meta_value( 'year-built' );
    	}

    	public function has_year_built() {
    		return (! empty($this->get_year_built() ));
    	}

    	public function get_images() {
    		return $this->get_re_meta_value( 'images' );
    	}

    	public function has_images() {
    		return (! empty($this->get_images() ));
    	}

    	public function get_agent_first_name() {
    		return $this->get_re_meta_value( 'agent-first-name' );
    	}

    	public function has_agent_first_name() {
    		return (! empty($this->get_agent_first_name() ));
    	}

    	public function get_agent_last_name() {
    		return $this->get_re_meta_value( 'agent-last-name' );
    	}

    	public function has_agent_last_name() {
    		return (! empty($this->get_agent_last_name() ));
    	}

    	public function get_agent_email() {
    		return $this->get_re_meta_value( 'agent-email' );
    	}

    	public function has_agent_email() {
    		return (! empty($this->get_agent_email() ));
    	}

    	public function get_agent_picture_url() {
    		return $this->get_re_meta_value( 'agent-picture-url' );
    	}

    	public function has_agent_picture_url() {
    		return (! empty($this->get_agent_picture_url() ));
    	}

    	public function get_agent_office_line_number() {
    		return $this->get_re_meta_value( 'agent-office-line-number' );
    	}

    	public function has_agent_office_line_number() {
    		return (! empty($this->get_agent_office_line_number() ));
    	}

    	public function get_agent_mobile_phone_line() {
    		return $this->get_re_meta_value( 'agent-mobile-phone-line' );
    	}

    	public function has_agent_mobile_phone_line() {
    		return (! empty($this->get_agent_mobile_phone_line() ));
    	}

    	public function get_agent_fax_number() {
    		return $this->get_re_meta_value( 'agent-fax-number' );
    	}

    	public function has_agent_fax_number() {
    		return (! empty($this->get_agent_fax_number() ));
    	}

    	public function get_agent_license_number() {
    		return $this->get_re_meta_value( 'agent-license-number' );
    	}

    	public function has_agent_license_number() {
    		return (! empty($this->get_agent_license_number() ));
    	}

    	public function get_office_brokerage_name() {
    		return $this->get_re_meta_value( 'office-brokerage-name' );
    	}

    	public function has_office_brokerage_name() {
    		return (! empty($this->get_office_brokerage_name() ));
    	}

    	public function get_office_brokerage_phone() {
    		return $this->get_re_meta_value( 'office-brokerage-phone' );
    	}

    	public function has_office_brokerage_phone() {
    		return (! empty($this->get_office_brokerage_phone() ));
    	}

    	public function get_office_brokerage_email() {
    		return $this->get_re_meta_value( 'office-brokerage-email' );
    	}

    	public function has_office_brokerage_email() {
    		return (! empty($this->get_office_brokerage_email() ));
    	}

    	public function get_office_brokerage_website() {
    		return $this->get_re_meta_value( 'office-brokerage-website' );
    	}

    	public function has_office_brokerage_website() {
    		return (! empty($this->get_office_brokerage_website() ));
    	}

    	public function get_office_brokerage_street_address() {
    		return $this->get_re_meta_value( 'office-brokerage-street-address' );
    	}

    	public function has_office_brokerage_street_address() {
    		return (! empty($this->get_office_brokerage_street_address() ));
    	}

    	public function get_office_brokerage_unit_number() {
    		return $this->get_re_meta_value( 'office-brokerage-unit-number' );
    	}

    	public function has_office_brokerage_unit_number() {
    		return (! empty($this->get_office_brokerage_unit_number() ));
    	}

    	public function get_office_brokerage_city() {
    		return $this->get_re_meta_value( 'office-brokerage-city' );
    	}

    	public function has_office_brokerage_city() {
    		return (! empty($this->get_office_brokerage_city() ));
    	}

    	public function get_office_brokerage_state() {
    		return $this->get_re_meta_value( 'office-brokerage-state' );
    	}

    	public function has_office_brokerage_state() {
    		return (! empty($this->get_office_brokerage_state() ));
    	}

    	public function get_office_brokerage_zip() {
    		return $this->get_re_meta_value( 'office-brokerage-zip' );
    	}

    	public function has_office_brokerage_zip() {
    		return (! empty($this->get_office_brokerage_zip() ));
    	}

    	public function get_office_brokerage_office_name() {
    		return $this->get_re_meta_value( 'office-brokerage-office-name' );
    	}

    	public function has_office_brokerage_office_name() {
    		return (! empty($this->get_office_brokerage_office_name() ));
    	}

    	public function get_office_brokerage_franchise_name() {
    		return $this->get_re_meta_value( 'office-brokerage-franchise-name' );
    	}

    	public function has_office_brokerage_franchise_name() {
    		return (! empty($this->get_office_brokerage_franchise_name() ));
    	}

    	public function get_open_houses() {
    		return $this->get_re_meta_value( 'open-houses' );
    	}

    	public function has_open_houses() {
    		return (! empty($this->get_open_houses() ));
    	}

    	public function get_fees() {
    		return $this->get_re_meta_value( 'fees' );
    	}

    	public function has_fees() {
    		return (! empty($this->get_fees() ));
    	}

    	public function get_additional_features() {
    		return $this->get_re_meta_value( 'additional-features' );
    	}

    	public function has_additional_features() {
    		return (! empty($this->get_additional_features() ));
    	}

    	public function get_dishwasher() {
    		return $this->get_re_meta_value( 'dishwasher' );
    	}

    	public function has_dishwasher() {
    		return (! empty($this->get_dishwasher() ));
    	}

    	public function get_dryer() {
    		return $this->get_re_meta_value( 'dryer' );
    	}

    	public function has_dryer() {
    		return (! empty($this->get_dryer() ));
    	}

    	public function get_freezer() {
    		return $this->get_re_meta_value( 'freezer' );
    	}

    	public function has_freezer() {
    		return (! empty($this->get_freezer() ));
    	}

    	public function get_garbage_disposal() {
    		return $this->get_re_meta_value( 'garbage-disposal' );
    	}

    	public function has_garbage_disposal() {
    		return (! empty($this->get_garbage_disposal() ));
    	}

    	public function get_microwave() {
    		return $this->get_re_meta_value( 'microwave' );
    	}

    	public function has_microwave() {
    		return (! empty($this->get_microwave() ));
    	}

    	public function get_range_oven() {
    		return $this->get_re_meta_value( 'range-oven' );
    	}

    	public function has_range_oven() {
    		return (! empty($this->get_range_oven() ));
    	}

    	public function get_refrigerator() {
    		return $this->get_re_meta_value( 'refrigerator' );
    	}

    	public function has_refrigerator() {
    		return (! empty($this->get_refrigerator() ));
    	}

    	public function get_trash_compactor() {
    		return $this->get_re_meta_value( 'trash-compactor' );
    	}

    	public function has_trash_compactor() {
    		return (! empty($this->get_trash_compactor() ));
    	}

    	public function get_washer() {
    		return $this->get_re_meta_value( 'washer' );
    	}

    	public function has_washer() {
    		return (! empty($this->get_washer() ));
    	}

    	public function get_architecture_style() {
    		return $this->get_re_meta_value( 'architecture-style' );
    	}

    	public function has_architecture_style() {
    		return (! empty($this->get_architecture_style() ));
    	}

    	public function get_attic() {
    		return $this->get_re_meta_value( 'attic' );
    	}

    	public function has_attic() {
    		return (! empty($this->get_attic() ));
    	}

    	public function get_barbequeue_area() {
    		return $this->get_re_meta_value( 'barbequeue-area' );
    	}

    	public function has_barbequeue_area() {
    		return (! empty($this->get_barbequeue_area() ));
    	}

    	public function get_basement() {
    		return $this->get_re_meta_value( 'basement' );
    	}

    	public function has_basement() {
    		return (! empty($this->get_basement() ));
    	}

    	public function get_building_unit_count() {
    		return $this->get_re_meta_value( 'building-unit-count' );
    	}

    	public function has_building_unit_count() {
    		return (! empty($this->get_building_unit_count() ));
    	}

    	public function get_cable_ready() {
    		return $this->get_re_meta_value( 'cable-ready' );
    	}

    	public function has_cable_ready() {
    		return (! empty($this->get_cable_ready() ));
    	}

    	public function get_ceiling_fan() {
    		return $this->get_re_meta_value( 'ceiling-fan' );
    	}

    	public function has_ceiling_fan() {
    		return (! empty($this->get_ceiling_fan() ));
    	}

    	public function get_CondoFloorNum() {
    		return $this->get_re_meta_value( 'CondoFloorNum' );
    	}

    	public function has_CondoFloorNum() {
    		return (! empty($this->get_CondoFloorNum() ));
    	}

    	public function get_cooling_system() {
    		return $this->get_re_meta_value( 'cooling-system' );
    	}

    	public function has_cooling_system() {
    		return (! empty($this->get_cooling_system() ));
    	}

    	public function get_deck() {
    		return $this->get_re_meta_value( 'deck' );
    	}

    	public function has_deck() {
    		return (! empty($this->get_deck() ));
    	}

    	public function get_disabled_access() {
    		return $this->get_re_meta_value( 'disabled-access' );
    	}

    	public function has_disabled_access() {
    		return (! empty($this->get_disabled_access() ));
    	}

    	public function get_dock() {
    		return $this->get_re_meta_value( 'dock' );
    	}

    	public function has_dock() {
    		return (! empty($this->get_dock() ));
    	}

    	public function get_Doorman() {
    		return $this->get_re_meta_value( 'Doorman' );
    	}

    	public function has_Doorman() {
    		return (! empty($this->get_Doorman() ));
    	}

    	public function get_double_pane_windows() {
    		return $this->get_re_meta_value( 'double-pane-windows' );
    	}

    	public function has_double_pane_windows() {
    		return (! empty($this->get_double_pane_windows() ));
    	}

    	public function get_elevator() {
    		return $this->get_re_meta_value( 'elevator' );
    	}

    	public function has_elevator() {
    		return (! empty($this->get_elevator() ));
    	}

    	public function get_exterior_types() {
    		return $this->get_re_meta_value( 'exterior-types' );
    	}

    	public function has_exterior_types() {
    		return (! empty($this->get_exterior_types() ));
    	}

    	public function get_fireplace() {
    		return $this->get_re_meta_value( 'fireplace' );
    	}

    	public function has_fireplace() {
    		return (! empty($this->get_fireplace() ));
    	}

    	public function get_floor_coverings() {
    		return $this->get_re_meta_value( 'floor-coverings' );
    	}

    	public function has_floor_coverings() {
    		return (! empty($this->get_floor_coverings() ));
    	}

    	public function get_garden() {
    		return $this->get_re_meta_value( 'garden' );
    	}

    	public function has_garden() {
    		return (! empty($this->get_garden() ));
    	}

    	public function get_gated_entry() {
    		return $this->get_re_meta_value( 'gated-entry' );
    	}

    	public function has_gated_entry() {
    		return (! empty($this->get_gated_entry() ));
    	}

    	public function get_greenhouse() {
    		return $this->get_re_meta_value( 'greenhouse' );
    	}

    	public function has_greenhouse() {
    		return (! empty($this->get_greenhouse() ));
    	}

    	public function get_heating_fuels() {
    		return $this->get_re_meta_value( 'heating-fuels' );
    	}

    	public function has_heating_fuels() {
    		return (! empty($this->get_heating_fuels() ));
    	}

    	public function get_heating_systems() {
    		return $this->get_re_meta_value( 'heating-systems' );
    	}

    	public function has_heating_systems() {
    		return (! empty($this->get_heating_systems() ));
    	}

    	public function get_hot_tub_spa() {
    		return $this->get_re_meta_value( 'hot-tub-spa' );
    	}

    	public function has_hot_tub_spa() {
    		return (! empty($this->get_hot_tub_spa() ));
    	}

    	public function get_intercom() {
    		return $this->get_re_meta_value( 'intercom' );
    	}

    	public function has_intercom() {
    		return (! empty($this->get_intercom() ));
    	}

    	public function get_jetted_bathtub() {
    		return $this->get_re_meta_value( 'jetted-bathtub' );
    	}

    	public function has_jetted_bathtub() {
    		return (! empty($this->get_jetted_bathtub() ));
    	}

    	public function get_lawn() {
    		return $this->get_re_meta_value( 'lawn' );
    	}

    	public function has_lawn() {
    		return (! empty($this->get_lawn() ));
    	}

    	public function get_mother_in_law() {
    		return $this->get_re_meta_value( 'mother-in-law' );
    	}

    	public function has_mother_in_law() {
    		return (! empty($this->get_mother_in_law() ));
    	}

    	public function get_num_floors() {
    		return $this->get_re_meta_value( 'num-floors' );
    	}

    	public function has_num_floors() {
    		return (! empty($this->get_num_floors() ));
    	}

    	public function get_num_parking_spaces() {
    		return $this->get_re_meta_value( 'num-parking-spaces' );
    	}

    	public function has_num_parking_spaces() {
    		return (! empty($this->get_num_parking_spaces() ));
    	}

    	public function get_parking_types() {
    		return $this->get_re_meta_value( 'parking-types' );
    	}

    	public function has_parking_types() {
    		return (! empty($this->get_parking_types() ));
    	}

    	public function get_patio() {
    		return $this->get_re_meta_value( 'patio' );
    	}

    	public function has_patio() {
    		return (! empty($this->get_patio() ));
    	}

    	public function get_pond() {
    		return $this->get_re_meta_value( 'pond' );
    	}

    	public function has_pond() {
    		return (! empty($this->get_pond() ));
    	}

    	public function get_pool() {
    		return $this->get_re_meta_value( 'pool' );
    	}

    	public function has_pool() {
    		return (! empty($this->get_pool() ));
    	}

    	public function get_porch() {
    		return $this->get_re_meta_value( 'porch' );
    	}

    	public function has_porch() {
    		return (! empty($this->get_porch() ));
    	}

    	public function get_roof_types() {
    		return $this->get_re_meta_value( 'roof-types' );
    	}

    	public function has_roof_types() {
    		return (! empty($this->get_roof_types() ));
    	}

    	public function get_room_count() {
    		return $this->get_re_meta_value( 'room-count' );
    	}

    	public function has_room_count() {
    		return (! empty($this->get_room_count() ));
    	}

    	public function get_rooms() {
    		return $this->get_re_meta_value( 'rooms' );
    	}

    	public function has_rooms() {
    		return (! empty($this->get_rooms() ));
    	}

    	public function get_rv_parking() {
    		return $this->get_re_meta_value( 'rv-parking' );
    	}

    	public function has_rv_parking() {
    		return (! empty($this->get_rv_parking() ));
    	}

    	public function get_sauna() {
    		return $this->get_re_meta_value( 'sauna' );
    	}

    	public function has_sauna() {
    		return (! empty($this->get_sauna() ));
    	}

    	public function get_security_system() {
    		return $this->get_re_meta_value( 'security-system' );
    	}

    	public function has_security_system() {
    		return (! empty($this->get_security_system() ));
    	}

    	public function get_skylight() {
    		return $this->get_re_meta_value( 'skylight' );
    	}

    	public function has_skylight() {
    		return (! empty($this->get_skylight() ));
    	}

    	public function get_sports_court() {
    		return $this->get_re_meta_value( 'sports-court' );
    	}

    	public function has_sports_court() {
    		return (! empty($this->get_sports_court() ));
    	}

    	public function get_sprinkler_system() {
    		return $this->get_re_meta_value( 'sprinkler-system' );
    	}

    	public function has_sprinkler_system() {
    		return (! empty($this->get_sprinkler_system() ));
    	}

    	public function get_vaulted_ceiling() {
    		return $this->get_re_meta_value( 'vaulted-ceiling' );
    	}

    	public function has_vaulted_ceiling() {
    		return (! empty($this->get_vaulted_ceiling() ));
    	}

    	public function get_fitness_center() {
    		return $this->get_re_meta_value( 'fitness-center' );
    	}

    	public function has_fitness_center() {
    		return (! empty($this->get_fitness_center() ));
    	}

    	public function get_basketball_court() {
    		return $this->get_re_meta_value( 'basketball-court' );
    	}

    	public function has_basketball_court() {
    		return (! empty($this->get_basketball_court() ));
    	}

    	public function get_tennis_court() {
    		return $this->get_re_meta_value( 'tennis-court' );
    	}

    	public function has_tennis_court() {
    		return (! empty($this->get_tennis_court() ));
    	}

    	public function get_near_transportation() {
    		return $this->get_re_meta_value( 'near-transportation' );
    	}

    	public function has_near_transportation() {
    		return (! empty($this->get_near_transportation() ));
    	}

    	public function get_controlled_access() {
    		return $this->get_re_meta_value( 'controlled-access' );
    	}

    	public function has_controlled_access() {
    		return (! empty($this->get_controlled_access() ));
    	}

    	public function get_over_55_active_community() {
    		return $this->get_re_meta_value( 'over-55-active-community' );
    	}

    	public function has_over_55_active_community() {
    		return (! empty($this->get_over_55_active_community() ));
    	}

    	public function get_assisted_living_community() {
    		return $this->get_re_meta_value( 'assisted-living-community' );
    	}

    	public function has_assisted_living_community() {
    		return (! empty($this->get_assisted_living_community() ));
    	}

    	public function get_storage() {
    		return $this->get_re_meta_value( 'storage' );
    	}

    	public function has_storage() {
    		return (! empty($this->get_storage() ));
    	}

    	public function get_fenced_yard() {
    		return $this->get_re_meta_value( 'fenced-yard' );
    	}

    	public function has_fenced_yard() {
    		return (! empty($this->get_fenced_yard() ));
    	}

    	public function get_property_name() {
    		return $this->get_re_meta_value( 'property-name' );
    	}

    	public function has_property_name() {
    		return (! empty($this->get_property_name() ));
    	}

    	public function get_furnished() {
    		return $this->get_re_meta_value( 'furnished' );
    	}

    	public function has_furnished() {
    		return (! empty($this->get_furnished() ));
    	}

    	public function get_high_speed_internet() {
    		return $this->get_re_meta_value( 'high-speed-internet' );
    	}

    	public function has_high_speed_internet() {
    		return (! empty($this->get_high_speed_internet() ));
    	}

    	public function get_on_site_laundry() {
    		return $this->get_re_meta_value( 'on-site-laundry' );
    	}

    	public function has_on_site_laundry() {
    		return (! empty($this->get_on_site_laundry() ));
    	}

    	public function get_cable_sat_tv() {
    		return $this->get_re_meta_value( 'cable-sat-tv' );
    	}

    	public function has_cable_sat_tv() {
    		return (! empty($this->get_cable_sat_tv() ));
    	}

    	public function get_view_types() {
    		return $this->get_re_meta_value( 'view-types' );
    	}

    	public function has_view_types() {
    		return (! empty($this->get_view_types() ));
    	}

    	public function get_waterfront() {
    		return $this->get_re_meta_value( 'waterfront' );
    	}

    	public function has_waterfront() {
    		return (! empty($this->get_waterfront() ));
    	}

    	public function get_wetbar() {
    		return $this->get_re_meta_value( 'wetbar' );
    	}

    	public function has_wetbar() {
    		return (! empty($this->get_wetbar() ));
    	}

    	public function get_what_owners_love() {
    		return $this->get_re_meta_value( 'what-owners-love' );
    	}

    	public function has_what_owners_love() {
    		return (! empty($this->get_what_owners_love() ));
    	}

    	public function get_wired() {
    		return $this->get_re_meta_value( 'wired' );
    	}

    	public function has_wired() {
    		return (! empty($this->get_wired() ));
    	}

    	public function get_year_updated() {
    		return $this->get_re_meta_value( 'year-updated' );
    	}

    	public function has_year_updated() {
    		return (! empty($this->get_year_updated() ));
    	}

       public function get_office_brokerage_image() {
           return $this->get_re_meta_value( 'office-brokerage-image' );
       }

       public function has_office_brokerage_image() {

           return (! empty($this->get_office_brokerage_image() ));
       }

    }
}
