<?php

/**
 * Contains the RWC\Features\RealEstate class.
 *
 * @author Brian Reich <breich@reich-consulting.net>
 * @copyright Copyright (C) 2016 Reich Consulting
 * @package RWC\Features
 */

namespace RWC\Features {

    /**
	 * The RWC\Feature\RealEstate plugin provides features for Real Estate sites
     * that will register a custom post type for Real Estate properties, as well
     * as all of the appropriate administrative infrastructure for assigning
     * real estate data to them.
	 *
	 * @author Brian Reich <breich@reich-consulting.net>
	 * @copyright Copyright (C) 2016 Reich Consulting
	 * @package FC_Client
	 */
    class RealEstate extends \RWC\Feature {

    	/**
    	 * The post type.
    	 *
    	 * @var string
    	 */
    	const POST_TYPE = 'rwc_real_estate';

        /**
		 * Returns an array of dependancies that loads the GoogleMaps Feature.
		 *
		 * @return array Returns an array of dependancies.
		 */
		public function get_dependancies() {

			return array(

                // Need Google Maps
				'GoogleMaps',

                // Need Post Location
                'PostLocation'
			);
		}

        /**
		 * Initializes the RealEstate Feature.
		 *
		 * @return void
		 */
		public function initialize() {

			// Merge in default options.
			$this->_options = array_merge( array(

				// TODO Place defaults here.

			), $this->_options );

            // Register URL rewriting for Zillow XML.
            add_action( 'init', array( $this, 'register_zillow_xml_rewrite' ) );

            // Add query var for Zillow XML feed
            add_filter( 'query_vars', array( $this, 'pmg_rewrite_add_var' ) );

            // Redirect for zillow feed.
            add_action( 'template_redirect', array( $this, 'pmg_rewrite_catch_form' ) );

            // Register stylesheets
            add_action( 'wp_enqueue_scripts', array( $this, 'register_styles' ) );

            // Register the custom post type.
            add_action( 'init', array( $this, 'create_post_type' ) );

            // Add the_content filter to display full property details for
            // properties when the_content() runs.
            add_filter( 'the_content', array( $this,
                'auto_generate_property_detail' ), 10, 1 );

            // Load shortcodes for this feature.
            $this->_shortcodes = array(
                'property-list' => new RealEstate\Shortcodes\PropertyList()
            );

            add_image_size( 'real-estate', 400, 218, true);
            add_image_size( 'real-estate-zillow-primary', 550, 416, true );
            add_image_size( 'real-estate-zillow-thumbnail', 280, 208, true );

            // Add AJAX handlers for contact form.
            add_action('wp_ajax_rwc_features_realestate_contact_form', array( $this, 'process_contact_form' ) );
            add_action('wp_ajax_nopriv_rwc_features_realestate_contact_form', array( $this, 'process_contact_form' ) );

            $this->_metabox = new \RWC\Metabox( $this->get_library(), array(
                'renderer' => 'vertical-tabs',
                'id'       => 'rwc-real-estate-metabox',
                'title'    => 'Real Estate Properties',
                'post_types' => array( self::POST_TYPE ),
                'sections' => array(
                    array(
                        'id' => 'location',
                        'name' => 'Location'
                    ),
                    array(
                        'id' => 'listing-details',
                        'name' => 'Listing Details'
                    ),
                    array(
                        'id' => 'rental-details',
                        'name' => 'Rental Details'
                    ),
                    array(
                        'id' => 'property-details',
                        'name' => 'Property Details'
                    ),
                    array(
                        'id' => 'pictures',
                        'name' => 'Pictures'
                    ),
                    array(
                        'id' => 'agent',
                        'name' => 'Agent'
                    ),
                    array(
                        'id' => 'office',
                        'name' => 'Office'
                    ),
                    array(
                        'id' => 'open-houses',
                        'name' => 'Open Houses'
                    ),
                    array(
                        'id' => 'fees',
                        'name' => 'Fees'
                    ),
                    array(
                        'id' => 'neighborhood',
                        'name' => 'Neighborhood'
                    ),
                    array(
                        'id' => 'extended-details',
                        'name' => 'Extended Details'
                    )
                ),


                'fields' => array(
                    'street-address' => array(
                        'type' => 'text',
                        'section' => 'location',
                        'name' => 'Street Address'
                    ),
                    'unit-number' => array(
                        'type' => 'text',
                        'section' => 'location',
                        'name' => 'Unit Number'
                    ),
                    'city' => array(
                        'type' => 'text',
                        'section' => 'location',
                        'name' => 'City'
                    ),
                    'state' => array(
                        'type' => 'text',
                        'section' => 'location',
                        'name' => 'State'
                    ),
                    'zip' => array(
                        'type' => 'text',
                        'section' => 'location',
                        'name' => 'ZIP Code'
                    ),
                    'latitude' => array(
                        'type' => 'text',
                        'section' => 'location',
                        'name' => 'Latitude'
                    ),
                    'longitude' => array(
                        'type' => 'text',
                        'section' => 'location',
                        'name' => 'longitude'
                    ),
                    'display-address' => array(
                        'type' => 'checkbox',
                        'section' => 'location',
                        'name' => 'Display Address',
                        'checkbox-values' => array(
                            'checked' => 'Yes',
                            'unchecked' => 'No'
                        ),
                        'description' => 'Defines if the address should be ' .
                            'displayed on Zillow'
                    ),

                    // LISTING DETAILS

                    'status' => array(
                        'type' => 'dropdown',
                        'section' => 'listing-details',
                        'name' => 'Status',
                        'required' => true,
                        'description' => 'Specifies the availability of this property.',
                        'options' => array(
                            'Active',
                            'Contingent',
                            'Pending',
                            'For Rent',
                            'Rented',
                            'Coming Soon'
                        )
                    ),

                    'price' => array(
                        'type' => 'text',
                        'required' => true,
                        'section' => 'listing-details',
                        'name' => 'Price',
                        'description' => 'The price of this property. If this property is for sale, the asking amount. If it is a rental, the monthly rental price.'
                    ),

                    'listing-url' => array(
                        'type' => 'text',
                        'section' => 'listing-details',
                        'name' => 'Listing URL',
                        'description' => 'The URL of this listing. Leave blank to automatically specify the permalink to this property.'
                    ),
                    'mls-id' => array(
                        'type' => 'text',
                        'section' => 'listing-details',
                        'name' => 'MLS ID',
                        'description' => 'If this property is listed in MLS, provide the MLS ID.'
                    ),
                    'mls-name' => array(
                        'type'  => 'text',
                        'section' => 'listing-details',
                        'name' => 'MLS Name',
                        'description' => 'If this property is listed in MLS, provide the MLS Listing name.'
                    ),
                    'provider-listing-id' => array(
                        'type' => 'text',
                        'section' => 'listing-details',
                        'name' => 'Provider Listing ID',
                        'description' => 'Provider’s internal id for this listing'
                    ),
                    'virtual-tour-url' => array(
                        'type' => 'text',
                        'section' => 'listing-details',
                        'name' => 'Virtual Tour URL',
                        'description' => 'Link that points to listing virtual tour'
                    ),
                    'listing-email' => array(
                        'type' => 'text',
                        'section' => 'listing-details',
                        'name' => 'Listing Email',
                        'description' => 'Email address that will be used ' .
                            'instead of the agent email for this listing only.' .
                            ' The Agent\EmailAddress field is still required, ' .
                            'even ifListingEmail is provided.'
                    ),
                    'listing-neighborhood' => array(
                        'type' => 'text',
                        'section' => 'neighborhood',
                        'name' => 'Neighborhood',
                        'description' => 'The name of the neighborhood.'
                    ),
                    'always-email-agent' => array(
                        'type' => 'checkbox',
                        'checkbox-values' => array(
                            'checked' => 'Yes',
                            'unchecked' => 'No'
                        ),
                        'section' => 'listing-details',
                        'name' => 'Always Email Agent',
                        'description' => 'Set this field to 1 in order to ' .
                            'email both the agent and the email address ' .
                            ' specified in the Listing Email above.'
                    ),
                    'short-sale' => array(
                        'type' => 'checkbox',
                        'checkbox-values' => array(
                            'checked' => 'Yes',
                            'unchecked' => 'No'
                        ),
                        'section' => 'listing-details',
                        'name' => 'Short Sale',
                        'description' => 'Is this listing a short sale?'
                    ),
                    'reo' => array(
                        'type' => 'checkbox',
                        'checkbox-values' => array(
                            'checked' => 'Yes',
                            'unchecked' => 'No'
                        ),
                        'section' => 'listing-details',
                        'name' => 'REO',
                        'description' => 'Is this listing bank-owned?'
                    ),
                    'coming-soon-on-market-date' => array(
                        'type' => 'date',
                        'section' => 'listing-details',
                        'name' => 'Coming Soon on Market Date',
                        'description' => 'If this is a Coming Soon listing, ' .
                            'when is it expected to be listed on the market?'
                    ),

                    // Rental details
                    'availability' => array(
                        'type' => 'dropdown',
                        'section' => 'rental-details',
                        'name' => 'Availability',
                        'options' => array(
                            'Now' => 'Now',
                            'ContactForDetails' => 'Contact for Details',
                            'Custom' => 'Custom'
                        )
                    ),
                    'custom-availability' => array(
                        'type' => 'date',
                        'section' => 'rental-details',
                        'name' => 'Custom Availability',
                        'description' => 'If "Availability" is "Custom", ' .
                            'specify the availability date.'
                    ),
                    'lease-term' => array(
                        'type' => 'dropdown',
                        'section' => 'rental-details',
                        'name' => 'Lease Terms',
                        'options' => array(
                            'ContactForDetails' => 'Contact for Details',
                            'Monthly' => 'Monthly',
                            'SixMonths' => 'Six Months',
                            'OneYear' => 'One Year',
                            'RentToOwn' => 'Rent to Own'
                        )
                    ),
                    'deposit-fees' => array(
                        'type' => 'text',
                        'section' => 'rental-details',
                        'name' => 'Deposit Fees',
                        'description' => 'Deposit amount required',
                    ),
                    'utilities-water-included' => array(
                        'type' => 'checkbox',
                        'checkbox-values' => array(
                            'checked' => 'Yes',
                            'unchecked' => 'No'
                        ),
                        'section' => 'rental-details',
                        'name' => 'Water Included',
                        'description' => 'Check if water utility is included in rent'
                    ),
                    'utilities-sewage-included' => array(
                        'type' => 'checkbox',
                        'checkbox-values' => array(
                            'checked' => 'Yes',
                            'unchecked' => 'No'
                        ),
                        'section' => 'rental-details',
                        'name' => 'Sewer Included',
                        'description' => 'Check if sewage utility is included in rent'
                    ),
                    'utilities-garbage-included' => array(
                        'type' => 'checkbox',
                        'checkbox-values' => array(
                            'checked' => 'Yes',
                            'unchecked' => 'No'
                        ),
                        'section' => 'rental-details',
                        'name' => 'Garbage Included',
                        'description' => 'Check if garbage utility is included in rent'
                    ),
                    'utilities-electricity-included' => array(
                        'type' => 'checkbox',
                        'checkbox-values' => array(
                            'checked' => 'Yes',
                            'unchecked' => 'No'
                        ),
                        'section' => 'rental-details',
                        'name' => 'Electricity Included',
                        'description' => 'Check if electricity utility is included in rent'
                    ),
                    'utilities-gas-included' => array(
                        'type' => 'checkbox',
                        'checkbox-values' => array(
                            'checked' => 'Yes',
                            'unchecked' => 'No'
                        ),
                        'section' => 'rental-details',
                        'name' => 'Gas Included',
                        'description' => 'Check if gas utility is included in rent'
                    ),

                    'utilities-internet-included' => array(
                        'type' => 'checkbox',
                        'checkbox-values' => array(
                            'checked' => 'Yes',
                            'unchecked' => 'No'
                        ),
                        'section' => 'rental-details',
                        'name' => 'Internet Included',
                        'description' => 'Check if internet utility is included in rent'
                    ),
                    'utilities-cable-included' => array(
                        'type' => 'checkbox',
                        'checkbox-values' => array(
                            'checked' => 'Yes',
                            'unchecked' => 'No'
                        ),
                        'section' => 'rental-details',
                        'name' => 'Cable Included',
                        'description' => 'Check if cable utility is included in rent'
                    ),
                    'utilities-sattv-included' => array(
                        'type' => 'checkbox',
                        'checkbox-values' => array(
                            'checked' => 'Yes',
                            'unchecked' => 'No'
                        ),
                        'section' => 'rental-details',
                        'name' => 'Satellite TV Included',
                        'description' => 'Check if satellite TV utility is included in rent'
                    ),
                    'pets-allowed' => array(
                        'type' => 'dropdown',
                        'name' => 'Pets',
                        'section' => 'rental-details',
                        'options' => array(
                            'No Pets' => 'No Pets',
                            'Cats' => 'Cats',
                            'SmallDogs' => 'Small Dogs',
                            'LargeDogs' => 'Large Dogs'
                        )
                    ),

                    'property-type' => array(
                        'section' => 'property-details',
                        'type' => 'dropdown',
                        'name' => 'Property Type',
                        'options' => array(
                            'SingleFamily' => 'Single Family',
                            'Condo' => 'Condo',
                            'Townhouse' => 'Townhouse',
                            'Coop' => 'Coop',
                            'MultiFamily' => 'Multi-Family',
                            'Manufactured' => 'Manufactured',
                            'VacantLand' => 'Vacant Land',
                            'Other' => 'Other',
                            'Apartment' => 'Apartment'
                        )
                    ),
                    'title' => array(
                        'section' => 'property-details',
                        'type' => 'text',
                        'name' => 'Title',
                        'description' => 'A title for the property.'
                    ),
                    'description' => array(
                        'section' => 'property-details',
                        'type' => 'text',
                        'name' => 'Description',
                        'description' => 'A brief description of the property.'
                    ),
                    'bedrooms' => array(
                        'section' => 'property-details',
                        'type' => 'text',
                        'name' => 'Bedrooms',
                        'description' => 'The number of bedrooms at this property.'
                    ),
                    'bathrooms' => array(
                        'section' => 'property-details',
                        'type' => 'text',
                        'name' => 'Bathrooms',
                        'description' => 'The number of bathrooms at this ' .
                            'property. Only relevent if Full Baths and Half ' .
                            'Baths are not provided below.'
                    ),
                    'full-bathrooms' => array(
                        'section' => 'property-details',
                        'type' => 'text',
                        'name' => 'Full Bathrooms',
                        'description' => 'The number of full bathrooms.'
                    ),
                    'half-bathrooms' => array(
                        'section' => 'property-details',
                        'type' => 'text',
                        'name' => 'Half Bathrooms',
                        'description' => 'The number of half bathrooms.'
                    ),
                    'quarter-bathrooms' => array(
                        'section' => 'property-details',
                        'type' => 'text',
                        'name' => 'Quarter Bathrooms',
                        'description' => 'The number of quarter bathrooms.'
                    ),
                    'three-quarter-bathrooms' => array(
                        'section' => 'property-details',
                        'type' => 'text',
                        'name' => 'Three Quarter Bathrooms',
                        'description' => 'The number of 3/4 bathrooms.'
                    ),
                    'living-area' => array(
                        'section' => 'property-details',
                        'type' => 'text',
                        'name' => 'Living Area',
                        'description' => 'The number of square feet of ' .
                            'finished, heated spaces.'
                    ),
                    'lot-size' => array(
                        'section' => 'property-details',
                        'type' => 'text',
                        'name' => 'Lot Size',
                        'description' => 'Lot size, in acres'
                    ),
                    'year-built' => array(
                        'section' => 'property-details',
                        'type' => 'text',
                        'name' => 'Year Built',
                        'description' => 'The year the property was built.'
                    ),
                    'images' => array(
                        'section' => 'pictures',
                        'type' => 'media',
                        'name' => 'Picture',
                        'description' => 'Add a picture of this property.'
                    ),

                    'agent-first-name' => array(
                        'section' => 'agent',
                        'type' => 'text',
                        'name' => 'First Name',
                        'description' => 'The first name of the agent.'
                    ),
                    'agent-last-name' => array(
                        'section' => 'agent',
                        'type' => 'text',
                        'name' => 'Last Name',
                        'description' => 'The last name of the agent.'
                    ),
                    'agent-email' => array(
                        'section' => 'agent',
                        'type' => 'text',
                        'name' => 'Email',
                        'description' => 'The agent\'s email address.',
                    ),
                    'agent-picture-url' => array(
                        'section' => 'agent',
                        'type' => 'media',
                        'name' => 'Agent Picture',
                        'description' => 'A photo of the agent.'
                    ),
                    'agent-office-line-number' => array(
                        'section' => 'agent',
                        'type' => 'text',
                        'name' => 'Office Phone',
                        'description' => 'The agent\'s office phone number.'
                    ),
                    'agent-mobile-phone-line' => array(
                        'section' => 'agent',
                        'type' => 'text',
                        'name' => 'Mobile Phone',
                        'description' => 'The agent\'s mobile phone number.'
                    ),
                    'agent-fax-number' => array(
                        'section' => 'agent',
                        'type' => 'text',
                        'name' => 'Fax Number',
                        'description' => 'The agent\'s fax number.'
                    ),
                    'agent-license-number' => array(
                        'section' => 'agent',
                        'type' => 'text',
                        'name' => 'License Number',
                        'description' => 'The agent\s real estate license number.'
                    ),

                    'office-brokerage-image' => array(
                        'section' => 'office',
                        'type' => 'media',
                        'name' => 'Office Image',
                        'description' => 'Add a picture of the office'
                    ),

                    'office-brokerage-name' => array(
                        'section' => 'office',
                        'type' => 'text',
                        'name' => 'Name',
                        'description' => 'The name of the real estate brokerage.'
                    ),
                    'office-brokerage-phone' => array(
                        'section' => 'office',
                        'type' => 'text',
                        'name' => 'Phone',
                        'description' => 'The real estate brokerage phone number.'
                    ),
                    'office-brokerage-email' => array(
                        'section' => 'office',
                        'type' => 'text',
                        'name' => 'Email',
                        'description' => 'The real estate office email.'
                    ),
                    'office-brokerage-website' => array(
                        'section' => 'office',
                        'type' => 'text',
                        'name' => 'Website',
                        'description' => 'The office website.'
                    ),
                    'office-brokerage-street-address' => array(
                        'section' => 'office',
                        'type' => 'text',
                        'name' => 'Street Address',
                        'description' => 'The name of the real estate brokerage.'
                    ),
                    'office-brokerage-unit-number' => array(
                        'section' => 'office',
                        'type' => 'text',
                        'name' => 'Unit Number',
                        'description' => 'The unit number of the brokerage\'s address.'
                    ),
                    'office-brokerage-city' => array(
                        'section' => 'office',
                        'type' => 'text',
                        'name' => 'City',
                        'description' => 'The brokerage\'s city.'
                    ),
                    'office-brokerage-state' => array(
                        'section' => 'office',
                        'type' => 'text',
                        'name' => 'State',
                        'description' => 'The brokerage\'s state.'
                    ),
                    'office-brokerage-zip' => array(
                        'section' => 'office',
                        'type' => 'text',
                        'name' => 'ZIP Code',
                        'description' => 'The brokerage\'s ZIP code.'
                    ),
                    'office-brokerage-office-name' => array(
                        'section' => 'office',
                        'type' => 'text',
                        'name' => 'Office Name',
                        'description' => 'Name of local brokerage office if applies.'
                    ),
                    'office-brokerage-franchise-name' => array(
                        'section' => 'office',
                        'type' => 'text',
                        'name' => 'Franchise Name',
                        'description' => 'Name of Franchise.'
                    ),

                    'open-houses' => array(
                        'section' => 'open-houses',
                        'type' => 'repeater',
                        'name' => 'Open Houses',
                        'fields' => array(
                            'open-house-date' => array(
                                'type' => 'date',
                                'name' => 'Date'
                            ),
                            'open-house-start-time' => array(
                                'type' => 'text',
                                'name' => 'Start Time'
                            ),
                            'open-house-end-time' => array(
                                'type' => 'text',
                                'name' => 'End Time'
                            )
                        )
                    ),

                    // FEES
                    'fees' => array(
                        'section' => 'fees',
                        'type' => 'repeater',
                        'name' => 'Fees',
                        'fields' => array(
                            'fee-type' => array(
                                'type' => 'dropdown',
                                'name' => 'Fee Type',
                                'options' => array(
                                    'HOA' => 'HOA',
                                    'Maintenance' => 'Maintenance',
                                    'CommonCharges' => 'Common Charges'
                                ),
                                'description' => 'Type of fee.'
                            ),
                            'fee-amount' => array(
                                'type' => 'text',
                                'name' => 'Fee Amount',
                                'description' => 'Fee amount in dollars'
                            ),
                            'fee-period' => array(
                                'type' => 'dropdown',
                                'name' => 'Fee Period',
                                'options' => array(
                                    'monthly' => 'Monthly',
                                    'quarterly' => 'Quarterly',
                                    'annually' => 'Annually'
                                ),
                                'description' => 'How often this fee recurs.'
                            ),
                        )
                    ),

                    // EXTENDED DETAILS

                    'additional-features' => array(
                        'section' => 'extended-details',
                        'type' => 'text',
                        'name' => 'Additional Features',
                        'description' => 'Comma-separated list of additional ' .
                            'features not already available in another field.'
                    ),
                    'dishwasher' => array(
                        'type' => 'checkbox',
                        'checkbox-values' => array(
                            'checked' => 1,
                            'unchecked' => 0
                        ),
                        'section' => 'extended-details',
                        'name' => 'Dishwasher'
                    ),
                    'dryer' => array(
                        'type' => 'checkbox',
                        'checkbox-values' => array(
                            'checked' => 1,
                            'unchecked' => 0
                        ),
                        'section' => 'extended-details',
                        'name' => 'Dryer'
                    ),
                    'freezer' => array(
                        'type' => 'checkbox',
                        'checkbox-values' => array(
                            'checked' => 1,
                            'unchecked' => 0
                        ),
                        'section' => 'extended-details',
                        'name' => 'Freezer'
                    ),
                    'garbage-disposal' => array(
                        'type' => 'checkbox',
                        'checkbox-values' => array(
                            'checked' => 1,
                            'unchecked' => 0
                        ),
                        'section' => 'extended-details',
                        'name' => 'Garbage Disposal'
                    ),
                    'microwave' => array(
                        'type' => 'checkbox',
                        'checkbox-values' => array(
                            'checked' => 1,
                            'unchecked' => 0
                        ),
                        'section' => 'extended-details',
                        'name' => 'Microwave'
                    ),
                    'range-oven' => array(
                        'type' => 'checkbox',
                        'checkbox-values' => array(
                            'checked' => 1,
                            'unchecked' => 0
                        ),
                        'section' => 'extended-details',
                        'name' => 'Range Oven'
                    ),
                    'refrigerator' => array(
                        'type' => 'checkbox',
                        'checkbox-values' => array(
                            'checked' => 1,
                            'unchecked' => 0
                        ),
                        'section' => 'extended-details',
                        'name' => 'Refrigerator'
                    ),
                    'trash-compactor' => array(
                        'type' => 'checkbox',
                        'checkbox-values' => array(
                            'checked' => 1,
                            'unchecked' => 0
                        ),
                        'section' => 'extended-details',
                        'name' => 'Trash Compactor'
                    ),
                    'washer' => array(
                        'type' => 'checkbox',
                        'checkbox-values' => array(
                            'checked' => 1,
                            'unchecked' => 0
                        ),
                        'section' => 'extended-details',
                        'name' => 'Washer'
                    ),
                    'architecture-style' => array(
                        'section' => 'extended-details',
                        'type' => 'dropdown',
                        'name' => 'Architecture Style',
                        'options' => array(
                            'Bungalow' => 'Bungalow',
                            'CapeCod' => 'Cape Cod',
                            'Colonial' => 'Colonial',
                            'Contemporary' => 'Contemporary',
                            'Craftsman' => 'Craftsman',
                            'French' => 'French',
                            'Georgian' => 'Georgian',
                            'Loft' => 'Loft',
                            'Modern' => 'Modern',
                            'QueenAnneVictorian' => 'Queen Anne Victorian',
                            'RanchRambler' => 'Ranch Rambler',
                            'SantaFePuebloStyle' => 'Santa Fe Pueblo Style',
                            'Spanish' => 'Spanish',
                            'Split-level' => 'Split-Level',
                            'Tudor' => 'Tudor',
                            'Other' => 'Other'
                        ),
                        'description' => 'The property\'s architectural style.'
                    ),
                    'attic' => array(
                        'type' => 'checkbox',
                        'checkbox-values' => array(
                            'checked' => 'Yes',
                            'unchecked' => 'No'
                        ),
                        'section' => 'extended-details',
                        'name' => 'Attic'
                    ),
                    'barbequeue-area' => array(
                        'type' => 'checkbox',
                        'checkbox-values' => array(
                            'checked' => 'Yes',
                            'unchecked' => 'No'
                        ),
                        'section' => 'extended-details',
                        'name' => 'Barbecue Area'
                    ),
                    'basement' => array(
                        'type' => 'checkbox',
                        'checkbox-values' => array(
                            'checked' => 'Yes',
                            'unchecked' => 'No'
                        ),
                        'section' => 'extended-details',
                        'name' => 'Basement'
                    ),
                    'building-unit-count' => array(
                        'type' => 'text',
                        'section' => 'extended-details',
                        'name' => 'Number of units in the building, for ' .
                            'condos, co-ops or multi-family only.'
                    ),
                    'cable-ready' => array(
                        'type' => 'checkbox',
                        'checkbox-values' => array(
                            'checked' => 'Yes',
                            'unchecked' => 'No'
                        ),
                        'section' => 'extended-details',
                        'name' => 'Cable-Ready'
                    ),
                    'ceiling-fan' => array(
                        'type' => 'checkbox',
                        'checkbox-values' => array(
                            'checked' => 'Yes',
                            'unchecked' => 'No'
                        ),
                        'section' => 'extended-details',
                        'name' => 'Ceiling Fan'
                    ),
                    'CondoFloorNum' => array(
                        'type' => 'text',
                        'section' => 'extended-details',
                        'name' => 'Floor Number',
                        'description' => 'Floor that property is on, for ' .
                            'condos and co-ops only.'
                    ),
                    'cooling-system' => array(
                        'type' => 'dropdown',
                        'multiple' => true,
                        'options' => array(
                            'none' => 'None',
                            'Central' => 'Central',
                            'Evaporative' => 'Evaporative',
                            'Geothermal' => 'Geothermal',
                            'Wall' => 'Wall',
                            'Solar' => 'Solar',
                            'Other' => 'Other'
                        ),
                        'section' => 'extended-details',
                        'name' => 'Cooling System'
                    ),
                    'deck' => array(
                        'type' => 'checkbox',
                        'checkbox-values' => array(
                            'checked' => 'Yes',
                            'unchecked' => 'No'
                        ),
                        'section' => 'extended-details',
                        'name' => 'Deck'
                    ),
                    'disabled-access' => array(
                        'type' => 'checkbox',
                        'checkbox-values' => array(
                            'checked' => 'Yes',
                            'unchecked' => 'No'
                        ),
                        'section' => 'extended-details',
                        'name' => 'Disabled Access'
                    ),
                    'dock' => array(
                        'type' => 'checkbox',
                        'checkbox-values' => array(
                            'checked' => 'Yes',
                            'unchecked' => 'No'
                        ),
                        'section' => 'extended-details',
                        'name' => 'Dock'
                    ),
                    'Doorman' => array(
                        'type' => 'checkbox',
                        'checkbox-values' => array(
                            'checked' => 'Yes',
                            'unchecked' => 'No'
                        ),
                        'section' => 'extended-details',
                        'name' => 'Doorman'
                    ),
                    'double-pane-windows' => array(
                        'type' => 'checkbox',
                        'checkbox-values' => array(
                            'checked' => 'Yes',
                            'unchecked' => 'No'
                        ),
                        'section' => 'extended-details',
                        'name' => 'Double Pane Windows'
                    ),
                    'elevator' => array(
                        'type' => 'checkbox',
                        'checkbox-values' => array(
                            'checked' => 'Yes',
                            'unchecked' => 'No'
                        ),
                        'section' => 'extended-details',
                        'name' => 'Elevator'
                    ),
                    'exterior-types' => array(
                        'type' => 'dropdown',
                        'options' => array(
                            'Brick' => 'Brick',
                            'CementConcrete' => 'Cement Concrete',
                            'Composition' => 'Composition',
                            'Metal' => 'Metal',
                            'Shingle' => 'Shingle',
                            'Stone' => 'Stone',
                            'Stucco' => 'Stucco',
                            'Vinyl' => 'Vinyl',
                            'Wood' => 'Wood',
                            'WoodProducts' => 'Wood Products',
                            'Other' => 'Other'
                        ),
                        'multiple' => true,
                        'section' => 'extended-details',
                        'name' => 'Exterior Type',
                        'description' => 'The exterior finish of the ' .
                            'property. Multiple selections are allowed.'
                    ),
                    'fireplace' => array(
                        'type' => 'checkbox',
                        'checkbox-values' => array(
                            'checked' => 'Yes',
                            'unchecked' => 'No'
                        ),
                        'section' => 'extended-details',
                        'name' => 'Fireplace'
                    ),
                    'floor-coverings' => array(
                        'type' => 'dropdown',
                        'options' => array(
                            'Carpet' => 'Carpet',
                            'Concrete' => 'Concrete',
                            'Hardwood' => 'Hardwood',
                            'Laminate' => 'Laminate',
                            'LinoleumVinyl' => 'Linoleum/Vinyl',
                            'Slate' => 'Slate',
                            'Softwood' => 'Softwood',
                            'Tile' => 'Tile',
                            'Other' => 'Other'
                        ),
                        'multiple' => true,
                        'section' => 'extended-details',
                        'name' => 'Floor Coverings',
                        'description' => 'The floor finish. Multiple ' .
                            'selections are allowed.'
                    ),
                    'garden' => array(
                        'type' => 'checkbox',
                        'checkbox-values' => array(
                            'checked' => 'Yes',
                            'unchecked' => 'No'
                        ),
                        'section' => 'extended-details',
                        'name' => 'Garden'
                    ),
                    'gated-entry' => array(
                        'type' => 'checkbox',
                        'checkbox-values' => array(
                            'checked' => 'Yes',
                            'unchecked' => 'No'
                        ),
                        'section' => 'extended-details',
                        'name' => 'Gated Entry'
                    ),
                    'greenhouse' => array(
                        'type' => 'checkbox',
                        'checkbox-values' => array(
                            'checked' => 'Yes',
                            'unchecked' => 'No'
                        ),
                        'section' => 'extended-details',
                        'name' => 'Greenhouse'
                    ),
                    'heating-fuels' => array(
                        'type' => 'dropdown',
                        'options' => array(
                            'None' => 'None',
                            'Coal' => 'Coal',
                            'Electric' => 'Electric',
                            'Gas' => 'Gas',
                            'Oil' => 'Oil',
                            'PropaneButane' => 'Propane/Butane',
                            'Solar' => 'Solar',
                            'WoodPellet' => 'Wood/Pellet',
                            'Other' => 'Other'
                        ),
                        'multiple' => true,
                        'section' => 'extended-details',
                        'name' => 'Heating Fuels',
                        'description' => 'Energy type for heating system'
                    ),
                    'heating-systems' => array(
                        'type' => 'dropdown',
                        'options' => array(
                            'Baseboard' => 'Baseboard',
                            'ForcedAir' => 'Forced Air',
                            'HeatPump' => 'Heat Pump',
                            'Radiant' => 'Radiant',
                            'Stove' => 'Stove',
                            'Wall' => 'Wall',
                            'Other' => 'Other'
                        ),
                        'multiple' => true,
                        'section' => 'extended-details',
                        'name' => 'Heating Systems'
                    ),
                    'hot-tub-spa' => array(
                        'type' => 'checkbox',
                        'checkbox-values' => array(
                            'checked' => 'Yes',
                            'unchecked' => 'No'
                        ),
                        'section' => 'extended-details',
                        'name' => 'Hot Tub/Spa'
                    ),
                    'intercom' => array(
                        'type' => 'checkbox',
                        'checkbox-values' => array(
                            'checked' => 'Yes',
                            'unchecked' => 'No'
                        ),
                        'section' => 'extended-details',
                        'name' => 'Intercom'
                    ),
                    'jetted-bathtub' => array(
                        'type' => 'checkbox',
                        'checkbox-values' => array(
                            'checked' => 'Yes',
                            'unchecked' => 'No'
                        ),
                        'section' => 'extended-details',
                        'name' => 'Jetted Bathtub'
                    ),
                    'lawn' => array(
                        'type' => 'checkbox',
                        'checkbox-values' => array(
                            'checked' => 'Yes',
                            'unchecked' => 'No'
                        ),
                        'section' => 'extended-details',
                        'name' => 'Lawn'
                    ),
                    'mother-in-law' => array(
                        'type' => 'checkbox',
                        'checkbox-values' => array(
                            'checked' => 'Yes',
                            'unchecked' => 'No'
                        ),
                        'section' => 'extended-details',
                        'name' => 'Mother In-Law Suite'
                    ),
                    'num-floors' => array(
                        'type' => 'text',
                        'section' => 'extended-details',
                        'name' => 'Number of Floors',
                        'description' => 'Number of floors in the building.'
                    ),
                    'num-parking-spaces' => array(
                        'type' => 'text',
                        'section' => 'extended-details',
                        'name' => 'Number of Parking Spaces',
                        'description' => 'Number of covered parking spaces.'
                    ),
                    'parking-types' => array(
                        'type' => 'dropdown',
                        'options' => array(
                            'Carport' => 'Carport',
                            'GarageAttached' => 'Attached Garage',
                            'GarageDetached' => 'Detached Garage',
                            'OffStreet' => 'Off-Street Parking',
                            'OnStreet' => 'On-Street Parking',
                            'None' => 'None'
                        ),
                        'multiple' => true,
                        'section' => 'extended-details',
                        'name' => 'Parking Types',
                        'description' => 'Types of parking spaces available.' .
                            'Multiple selections allowed.'
                    ),
                    'patio' => array(
                        'type' => 'checkbox',
                        'checkbox-values' => array(
                            'checked' => 'Yes',
                            'unchecked' => 'No'
                        ),
                        'section' => 'extended-details',
                        'name' => 'Patio'
                    ),
                    'pond' => array(
                        'type' => 'checkbox',
                        'checkbox-values' => array(
                            'checked' => 'Yes',
                            'unchecked' => 'No'
                        ),
                        'section' => 'extended-details',
                        'name' => 'Pond'
                    ),
                    'pool' => array(
                        'type' => 'checkbox',
                        'checkbox-values' => array(
                            'checked' => 'Yes',
                            'unchecked' => 'No'
                        ),
                        'section' => 'extended-details',
                        'name' => 'pool'
                    ),
                    'porch' => array(
                        'type' => 'checkbox',
                        'checkbox-values' => array(
                            'checked' => 'Yes',
                            'unchecked' => 'No'
                        ),
                        'section' => 'extended-details',
                        'name' => 'Porch'
                    ),
                    'roof-types' => array(
                        'type' => 'dropdown',
                        'options' => array(
                            'Asphalt' => 'Asphalt',
                            'BuiltUp' => 'Built-Up',
                            'Composition' => 'Composition',
                            'Metal' => 'Metal',
                            'ShakeShingle' =>  'Shake/Shingle',
                            'Slate' => 'Slate',
                            'Tile' => 'Tile',
                            'Other' => 'Other'
                        ),
                        'multiple' => true,
                        'section' => 'extended-details',
                        'name' => 'Roof Types',
                        'description' => 'Type of roof material. ' .
                            'Multiple selections allowed.'
                    ),
                    'room-count' => array(
                        'type' => 'text',
                        'section' => 'extended-details',
                        'name' => 'Room Count',
                        'description' => 'Totalnumber of rooms. It should ' .
                            'be greater than the sum of beds and baths.'
                    ),
                    'rooms' => array(
                        'type' => 'dropdown',
                        'options' => array(
                            'BreakfastNook' => 'Breakfast Nook',
                            'DiningRoom' => 'Dining Room',
                            'FamilyRoom' => 'Family Room',
                            'LaundryRoom' => 'Laundry Room',
                            'Library' => 'Library',
                            'MasterBath' => 'Master Bath',
                            'MudRoom' => 'Mud Room',
                            'Office' => 'Office',
                            'Pantry' => 'Pantry',
                            'RecreationRoom' => 'Recreation Room',
                            'Workshop' => 'Workshop',
                            'SolariumAtrium' => 'Solarium/Atrium',
                            'SunRoom' => 'Sun Room',
                            'WalkInCloset' => 'Walk-in Closet'
                        ),
                        'multiple' => true,
                        'section' => 'extended-details',
                        'name' => 'Rooms',
                        'description' => 'Types of rooms available. Multiple ' .
                            'selections available.'
                    ),
                    'rv-parking' => array(
                        'type' => 'checkbox',
                        'checkbox-values' => array(
                            'checked' => 'Yes',
                            'unchecked' => 'No'
                        ),
                        'section' => 'extended-details',
                        'name' => 'RV Parking'
                    ),
                    'sauna' => array(
                        'type' => 'checkbox',
                        'checkbox-values' => array(
                            'checked' => 'Yes',
                            'unchecked' => 'No'
                        ),
                        'section' => 'extended-details',
                        'name' => 'Sauna'
                    ),
                    'security-system' => array(
                        'type' => 'checkbox',
                        'checkbox-values' => array(
                            'checked' => 'Yes',
                            'unchecked' => 'No'
                        ),
                        'section' => 'extended-details',
                        'name' => 'Security System'
                    ),
                    'skylight' => array(
                        'type' => 'checkbox',
                        'checkbox-values' => array(
                            'checked' => 'Yes',
                            'unchecked' => 'No'
                        ),
                        'section' => 'extended-details',
                        'name' => 'Skylight'
                    ),
                    'sports-court' => array(
                        'type' => 'checkbox',
                        'checkbox-values' => array(
                            'checked' => 'Yes',
                            'unchecked' => 'No'
                        ),
                        'section' => 'extended-details',
                        'name' => 'Sports Court'
                    ),
                    'sprinkler-system' => array(
                        'type' => 'checkbox',
                        'checkbox-values' => array(
                            'checked' => 'Yes',
                            'unchecked' => 'No'
                        ),
                        'section' => 'extended-details',
                        'name' => 'Sprinkler System'
                    ),
                    'vaulted-ceiling' => array(
                        'type' => 'checkbox',
                        'checkbox-values' => array(
                            'checked' => 'Yes',
                            'unchecked' => 'No'
                        ),
                        'section' => 'extended-details',
                        'name' => 'Vaulted Ceiling'
                    ),
                    'fitness-center' => array(
                        'type' => 'checkbox',
                        'checkbox-values' => array(
                            'checked' => 'Yes',
                            'unchecked' => 'No'
                        ),
                        'section' => 'extended-details',
                        'name' => 'Fitness Center'
                    ),
                    'basketball-court' => array(
                        'type' => 'checkbox',
                        'checkbox-values' => array(
                            'checked' => 'Yes',
                            'unchecked' => 'No'
                        ),
                        'section' => 'extended-details',
                        'name' => 'Basketball Court'
                    ),
                    'tennis-court' => array(
                        'type' => 'checkbox',
                        'checkbox-values' => array(
                            'checked' => 'Yes',
                            'unchecked' => 'No'
                        ),
                        'section' => 'extended-details',
                        'name' => 'Tennis Court'
                    ),
                    'near-transportation' => array(
                        'type' => 'checkbox',
                        'checkbox-values' => array(
                            'checked' => 'Yes',
                            'unchecked' => 'No'
                        ),
                        'section' => 'extended-details',
                        'name' => 'Near Transportation'
                    ),
                    'controlled-access' => array(
                        'type' => 'checkbox',
                        'checkbox-values' => array(
                            'checked' => 'Yes',
                            'unchecked' => 'No'
                        ),
                        'section' => 'extended-details',
                        'name' => 'Controlled Access'
                    ),
                    'over-55-active-community' => array(
                        'type' => 'checkbox',
                        'checkbox-values' => array(
                            'checked' => 'Yes',
                            'unchecked' => 'No'
                        ),
                        'section' => 'extended-details',
                        'name' => 'Over 55 Active Community'
                    ),
                    'assisted-living-community' => array(
                        'type' => 'checkbox',
                        'checkbox-values' => array(
                            'checked' => 'Yes',
                            'unchecked' => 'No'
                        ),
                        'section' => 'extended-details',
                        'name' => 'Assisted Living Community'
                    ),
                    'storage' => array(
                        'type' => 'checkbox',
                        'checkbox-values' => array(
                            'checked' => 'Yes',
                            'unchecked' => 'No'
                        ),
                        'section' => 'extended-details',
                        'name' => 'Storage'
                    ),
                    'fenced-yard' => array(
                        'type' => 'checkbox',
                        'checkbox-values' => array(
                            'checked' => 'Yes',
                            'unchecked' => 'No'
                        ),
                        'section' => 'extended-details',
                        'name' => 'Fenced Yard'
                    ),
                    'property-name' => array(
                        'type' => 'text',
                        'section' => 'extended-details',
                        'name' => 'Property Name',
                        'description' => 'Used for names of apartment or ' .
                            'condo communities'
                    ),
                    'furnished' => array(
                        'type' => 'checkbox',
                        'checkbox-values' => array(
                            'checked' => 'Yes',
                            'unchecked' => 'No'
                        ),
                        'section' => 'extended-details',
                        'name' => 'Furnished'
                    ),
                    'high-speed-internet' => array(
                        'type' => 'checkbox',
                        'checkbox-values' => array(
                            'checked' => 'Yes',
                            'unchecked' => 'No'
                        ),
                        'section' => 'extended-details',
                        'name' => 'High-Speed Internet'
                    ),
                    'on-site-laundry' => array(
                        'type' => 'checkbox',
                        'checkbox-values' => array(
                            'checked' => 'Yes',
                            'unchecked' => 'No'
                        ),
                        'section' => 'extended-details',
                        'name' => 'On-Site Laundry',
                        'description' => 'Is there laundry avauilable in the ' .
                            'building? Do not use if the laundry is directly ' .
                            'in the home/unit.'
                    ),
                    'cable-sat-tv' => array(
                        'type' => 'checkbox',
                        'checkbox-values' => array(
                            'checked' => 'Yes',
                            'unchecked' => 'No'
                        ),
                        'section' => 'extended-details',
                        'name' => 'Cable/Satellite TV'
                    ),
                    'skylight' => array(
                        'type' => 'checkbox',
                        'checkbox-values' => array(
                            'checked' => 'Yes',
                            'unchecked' => 'No'
                        ),
                        'section' => 'extended-details',
                        'name' => 'Skylight'
                    ),
                    'view-types' => array(
                        'type' => 'dropdown',
                        'options' => array(
                            'None' => 'None',
                            'City' => 'City',
                            'Mountain' => 'Mountain',
                            'Park' => 'Park',
                            'Territoria' => 'Territoria'
                        ),
                        'multiple' => true,
                        'section' => 'extended-details',
                        'name' => 'View Type'
                    ),
                    'waterfront' => array(
                        'type' => 'checkbox',
                        'checkbox-values' => array(
                            'checked' => 'Yes',
                            'unchecked' => 'No'
                        ),
                        'section' => 'extended-details',
                        'name' => 'Waterfront'
                    ),
                    'wetbar' => array(
                        'type' => 'checkbox',
                        'checkbox-values' => array(
                            'checked' => 'Yes',
                            'unchecked' => 'No'
                        ),
                        'section' => 'extended-details',
                        'name' => 'Wet Bar'
                    ),
                    'what-owners-love' => array(
                        'type' => 'text',
                        'section' => 'extended-details',
                        'name' => 'What Owners Love',
                        'description' => 'A description of what the current owner loves about the property'
                    ),
                    'wired' => array(
                        'type' => 'checkbox',
                        'checkbox-values' => array(
                            'checked' => 'Yes',
                            'unchecked' => 'No'
                        ),
                        'section' => 'extended-details',
                        'name' => 'Wired',
                        'description' => 'Indicated that home has high-tech wiring.'
                    ),
                    'year-updated' => array(
                        'type' => 'text',
                        'section' => 'extended-details',
                        'name' => 'Year Updated',
                        'description' => 'Year of last remodel.'
                    ),
                )
            ) );

            // Register an \RWC\Features\RealEstate\Post object when appropriate.
    		add_action( 'the_post', array( $this, 'register_real_estate_object' ) );

            add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );

            // Flush rewrites when the plugin/theme that loaded
            // the library activates.
            register_activation_hook(
                $this->get_library()->get_activation_file() ,
            	   array( $this, 'rewrite_flush' ) );
        }

        /**
         * Returns the RealEstate Metabox.
         *
         * @return \RWC\Metabox Returns the RealEstate Metabox.
         */
        public function get_realestate_metabox() {

            return $this->_metabox;
        }

        /**
         * Registers a URL rewrite for the Zillow XML feed.
         *
         * @return void
         */
        public function register_zillow_xml_rewrite() {

            add_rewrite_rule(
                '^zillow\.xml/?$',
                'index.php?zillow_xml=1',
                'top'
            );
        }

        public function pmg_rewrite_catch_form()
        {
            if( get_query_var( 'zillow_xml' ) )
            {
                \RWC\Features\RealEstate\Zillow\Feed::render_feed();
                exit();
            }
        }

        /**
         * Adds the zillow_xml query var.
         *
         * @param array $vars The current list of query vars.
         *
         * @return array Returns the modified list of query vars.
         */
        function pmg_rewrite_add_var( $vars )
        {
            $vars[] = 'zillow_xml';
            return $vars;
        }

        /**
         * Loads admin scripts for the RealEstate feature.
         *
         * @return void
         */
        public function enqueue_admin_scripts() {

            // Load admin style
            wp_enqueue_style( 'rwc-features-realestate-admin',
            $this->get_library()->get_uri() .
                '/css/rwc/features/realestate-admin.css' );

            // Load the admin script
            wp_enqueue_script(
                'rwc-features-realestate-admin',
                $this->get_library()->get_uri() .
                    '/js/rwc/features/realestate-admin.js',
                    array( 'rwc-vertical-tabs-js' ) );
        }

    	/**
    	 * Registers a global variable named "property" that will contain an
    	 * RWC\RealEstate\Property object that wraps the current post.

    	 * @param WP_Post|null $post The current post, or null to use the default.
    	 *
    	 * @return void
    	 */
    	public function register_real_estate_object( $post = null ) {

    		// If no post is specified, use the current global post.
    		$post = is_null( $post ) ? $GLOBALS[ 'post' ] : $post;

    		// If there's no post to wrap, don't bother.
    		if( is_null( $post ) ) {
    			return;
    		}

    		if( $post->post_type == self::POST_TYPE ) {

    			$GLOBALS[ 'property' ] = new \RWC\Features\RealEstate\Post( $post );
    		}
    	}

    	/**
    	 * Creates the custom post type for real estate posts.
    	 *
    	 * The Real Estate custom post type will provide standard post features in
    	 * addition the following features: title, editor, thumbnail, excerpt, and
    	 * revisions.
    	 *
    	 * @return void
    	 */
    	public function create_post_type() {

    		register_post_type( 'rwc_real_estate',
    			array(

    				'labels' => array(
    					'name' => __( 'Properties' ),
    					'singular_name' => __( 'Property' )
    				),
    				'description' => 'Posts configured to represent real estate properties.',
    				'menu_icon' => 'dashicons-admin-multisite',
    				'supports' => array( 'title', 'editor', 'thumbnail', 'excerpt',
    					'revisions' ),
    				'public' => true,
    				'has_archive' => true,
    				'rewrite' => array(
    					'slug' => __( 'properties' ),
    					'with_front' => false
    				)
    			)
    		);
    	}

    	/**
    	 * Flush rewrite rules on activation.
    	 *
    	 * @return void
    	 */
    	public function rewrite_flush() {

    		// Register the post type.
    		$this->create_post_type();

    		// ATTENTION: This is *only* done during plugin activation hook in this example!
    		// You should *NEVER EVER* do this on every page load!!
    		flush_rewrite_rules();
    	}

        /**
         *
         */
        public function register_styles() {

            wp_enqueue_style( 'rwc_features_realestate_propertylist_zillow',
                $this->get_library()->get_uri() .
                'css/rwc/features/realestate/property-list-zillow.css' );

            // TODO Totally not a style. put in it's own method.
            wp_enqueue_script(
                'rwc_features_realestate_propertylist_zillow_js',
                $this->get_library()->get_uri() .
                    'js/rwc/features/realestate/realestate-zillow-detail.js',
                    array( 'jquery' ) );

            add_thickbox();
        }

        /**
         * Automatically renders the full detail for a property when
         * the_content() runs for a real estate property.
         *
         * @param string $content The original the_content() result.
         *
         * @return string Returns the full detail page.
         */
        public function auto_generate_property_detail( $content ) {

            // Pull in the global WP_Post object
            global $post, $wp_query;

            // If it's not a real estate property, do nothing.
            if( $post->post_type == self::POST_TYPE ) {

                // Temporarily disable the_content filter, so the_content can run
                // in the view.
                remove_filter( 'the_content', array( $this,
                    'auto_generate_property_detail' ), 10 );

                // Get the result of the View::render().
                $view = new \RWC\Features\RealEstate\View();

                $content = $view->render( array(
                    'view' => 'zillow-detail',
                    'query' => $wp_query
                ) );

                // Reapply the_content() filter.
                add_filter( 'the_content', array( $this,
                    'auto_generate_property_detail' ), 10, 1 );
            }

            return $content;
        }

        /**
         * Processes the contact form that is submitted from the Property
         * detail page.
         *
         * @return void
         */
        public function process_contact_form() {

            // Enable HTML Email
            add_filter( 'wp_mail_content_type', array( $this, 'allow_html_email' ) );

            // Set required options

            $name = $_POST[ 'name' ];
            $phone = $_POST[ 'phone' ];
            $email = $_POST[ 'email' ];
            $message = $_POST[ 'message' ];
            $propertyId = $_POST[ 'property_id' ];
            $post = get_post( $propertyId );

            $property = new \RWC\Features\RealEstate\Post( $post );
            $to = $property->get_contact_recipient_list();
            $subject = 'Information Requested on ' . get_the_title( $propertyId );
            $options = array(
                'name' => $name,
                'phone' => $phone,
                'email' => $email,
                'message' => $message,
                'property_id' => $propertyId,
                'subject' => $subject,
            );

            // Generate the email template
            $html = \RWC\Utility::get_include_content( '/features/realestate/contact-email.php', $options );

            // Send email
            wp_mail( $to, $subject, $html );

            // Build client email.
            $to = $email;
            $html = \RWC\Utility::get_include_content( '/features/realestate/contact-email-client.php', $options );

            // Send client email.
            wp_mail( $to, $subject, $html );
            // Send response back to AJAX
            echo '<p>Your message to ' . get_bloginfo( 'sitename' ) . ' has been submitted. We will respond as soon as possible.</p>';

            // Die a horrible death
            die();
        }

        public function allow_html_email() {
            return 'text/html';
        }
    }

}
