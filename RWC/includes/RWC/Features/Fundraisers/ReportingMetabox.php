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
            $csvUrl = sprintf( $base, 'csv', get_the_ID() );
            $pdfUrl = sprintf( $base, 'pdf', get_the_ID() ); ?>
                <a class="button button-large button-secondary" href="#"
                    onclick="window.open(<?php echo $pdfUrl ; ?>); return false;">Download Report (PDF)</a>

                <a class="button button-large button-secondary"
                    href="#" onclick="window.open(<?php echo $csvUrl ; ?>)  ; return false">Download Report (CSV)</a>
        <?php }
    }
}
