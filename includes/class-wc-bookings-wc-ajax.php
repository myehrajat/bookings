<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Bookings WC ajax callbacks.
 */
class WC_Bookings_WC_Ajax {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'wc_ajax_wc_bookings_find_booked_day_blocks', array( $this, 'find_booked_day_blocks' ) );
	}

	/**
	 * This endpoint is supposed to replace the back-end logic in booking-form.
	 */
	public function find_booked_day_blocks() {

		check_ajax_referer( 'find-booked-day-blocks', 'security' );

		$product_id = absint( $_GET['product_id'] );

		if ( empty( $product_id ) ) {
			wp_send_json_error( 'Missing product ID' );
			exit;
		}

		try {

			$args                          = array();
			$product                       = new WC_Product_Booking( $product_id );
			$args['availability_rules']    = array();
			$args['availability_rules'][0] = $product->get_availability_rules();
			$args['min_date']              = isset( $_GET['min_date'] ) ? strtotime( $_GET['min_date'] ) : $product->get_min_date();
			$args['max_date']              = isset( $_GET['max_date'] ) ? strtotime( $_GET['max_date'] ) : $product->get_max_date();

			$min_date        = ( ! isset( $_GET['min_date'] ) ) ? strtotime( "+{$args['min_date']['value']} {$args['min_date']['unit']}", current_time( 'timestamp' ) ) : $args['min_date'];
			$max_date        = ( ! isset( $_GET['max_date'] ) ) ? strtotime( "+{$args['max_date']['value']} {$args['max_date']['unit']}", current_time( 'timestamp' ) ) : $args['max_date'];
			$timezone_offset = isset( $_GET['timezone_offset'] ) ? $_GET['timezone_offset'] : 0;

			if ( $product->has_resources() ) {
				foreach ( $product->get_resources() as $resource ) {
					$args['availability_rules'][ $resource->ID ] = $product->get_availability_rules( $resource->ID );
				}
			}

			$booked = WC_Bookings_Controller::find_booked_day_blocks( $product_id, $min_date, $max_date, 'Y-n-j', $timezone_offset );

			$args['partially_booked_days'] = $booked['partially_booked_days'];
			$args['fully_booked_days']     = $booked['fully_booked_days'];
			$args['unavailable_days']      = $booked['unavailable_days'];
			$args['restricted_days']       = $product->has_restricted_days() ? $product->get_restricted_days() : false;

			$buffer_days = array();
			if ( ! in_array( $product->get_duration_unit(), array( 'minute', 'hour' ) ) ) {
				$buffer_days = WC_Bookings_Controller::get_buffer_day_blocks_for_booked_days( $product, $args['fully_booked_days'] );
			}

			$args['buffer_days']           = $buffer_days;

			wp_send_json( $args );

		} catch ( Exception $e ) {

			wp_die();

		}

	}
}

new WC_Bookings_WC_Ajax();
