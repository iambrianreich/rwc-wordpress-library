<?php

/**
 * This file contains the RWC\Features\Fundraisers\ReportingMetabox class.
 *
 * @author Brian Reich <breich@reich-consulting.net>
 * @copyright Copyright (C) 2017 Reich Web Consulting
 * @package RWC
 */
namespace RWC\Features\Fundraisers {

    /**
     * An Exception class for Fundraiser feature related errors.
     *
     * @author Brian Reich <breich@reich-consulting.net>
     * @copyright Copyright (C) 2017 Reich Web Consulting
     * @package RWC
     */
    class ReportingMetabox extends \RWC\Object {

        private $aggregate = [];

        private $aggregate_price = 0;

        public function set_fundraisers_feature( \RWC\Features\Fundraisers $feature )
        {
            $this->set_option( 'fundraisersFeature', $feature );
        }

        public function get_fundraisers_feature()
        {
            return $this->get_option( 'fundraisersFeature', null );
        }

        public function __construct( $options = array() )
        {
            parent::__construct( $options );

            add_action( 'add_meta_boxes', array( $this, 'register_metabox' ) );

            add_action( 'wp_ajax_rwc_fundraiser_report_pdf',
                array( $this, 'rwc_fundraiser_report_pdf' ) );

            add_action( 'wp_ajax_rwc_fundraiser_report_csv',
                array( $this, 'rwc_fundraiser_report_csv' ) );
            
            add_action('wp_ajax_rwc_fundraiser_customer_list', [ $this,
            	'rwc_fundraiser_customer_list'
            ]);
        }

        public function rwc_fundraiser_report_pdf()
        {
            $fundraiserId = intval( $_REQUEST[ 'fundraiser' ] );


            $feature = $this->get_fundraisers_feature();
            $fundraiser = $feature->get_fundraiser( $fundraiserId );
            $orderIds = $fundraiser->get_order_ids();
            $library = $this->get_option( 'library' );

            echo \RWC\Utility::get_include_content(
                '/features/fundraisers/fundraising-report.php', [
                    'fundraiser' => $fundraiser,
                    'cssDirectory' => $library->get_uri(),
                    'feature'    => $feature,
                    'orderIds'   => $orderIds,
                    'library'    => $library
            ] );

            exit();
        }

        /**
         * Returns a CSV list of contact information for fundraiser customers.
         * 
         * When the request for the fundraiser customer list is made, the
         * request must contain a request parameter called "fundraiser" which
         * specifies the unique id of the fundraiser whose customer list is
         * desired. The fundraiser value must map to an existing Fundraiser.
         * 
         * 
         * @return void
         */
        public function rwc_fundraiser_customer_list()
        {
        	// Make sure required option was specified.
        	if(! isset($_REQUEST['fundraiser']))
        	{
        		throw new Exception('Request parameter "fundraiser" not set.');
        	}
        	// Get the unique id of the fundraiser.
        	$fundraiserId = intval( $_REQUEST[ 'fundraiser' ] );
        	
        	$feature = $this->get_fundraisers_feature();
        	$fundraiser = $feature->get_fundraiser( $fundraiserId );
        	
        	// Make sure it exists.
        	if(is_null($fundraiser))
        	{
        		throw new Exception($fundraiserId . 
        			' is not a valid fundraiser.');
        	}
        	
        	// Get a list of orders in the fundraiser.
        	$orderIds = $fundraiser->get_order_ids();
        	$library = $this->get_option( 'library' );
        	
        	// Output in CSV format.
        	header('Content-type: text/csv');
        	header('Content-Disposition: attachment; filename=fundraiser_' . 
        		$fundraiserId . '_customers.csv');
        	
        	echo \RWC\Utility::get_include_content(
        		'/features/fundraisers/customer-list.php', [
        			'fundraiser' => $fundraiser,
        			'cssDirectory' => $library->get_uri(),
        			'feature'    => $feature,
        			'orderIds'   => $orderIds,
        			'library'    => $library
        	] );
        	
        	exit();
        }
        public function rwc_fundraiser_report_csv()
        {
            $fundraiserId = intval( $_REQUEST[ 'fundraiser' ] );


            $fundraiser = $this->get_fundraisers_feature()->get_fundraiser();


            die();
        }

        public function register_metabox()
        {
            \add_meta_box(
                'rwc-features-fundraisers-reporting',
                'Reporting Options',
                array( $this, 'render' ),
                $this->get_fundraisers_feature()->get_post_type(),
                'side'
            );
        }

        /**
         * Renders the contents of the Reporting Metabox.
         *
         * This method renders the contents of the Reporting Metabox, which
         * provides the interface for downloading reports associated with the
         * currently selected fundraiser.
         *
         * @return void
         */
        public function render() {

            $base = 'ajaxurl + \'?action=rwc_fundraiser_report_%s&fundraiser=%s\'';
            $csvUrl = sprintf('ajaxurl + \'?action=rwc_fundraiser_customer_list&fundraiser=%s\'',get_the_ID() );
            
            $pdfUrl = sprintf( $base, 'pdf', get_the_ID() ); ?>
                <a class="button button-large button-secondary" href="#"
                    onclick="window.open(<?php echo $pdfUrl ; ?>); return false;">Download Report (PDF)</a>

                <a class="button button-large button-secondary"
                    href="#" onclick="window.open(<?php echo $csvUrl ; ?>)  ; return false">Download Customer List (CSV)</a>
        <?php }
    }
}
