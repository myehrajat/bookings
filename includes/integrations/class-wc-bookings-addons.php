<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Addons integration class.
 */
class WC_Bookings_Addons {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_filter( 'woocommerce_product_addons_show_grand_total', array( $this, 'addons_show_grand_total' ), 20, 2 );
		add_action( 'woocommerce_product_addons_panel_before_options', array( $this, 'addon_options' ), 20, 3 );
		add_filter( 'woocommerce_product_addons_save_data', array( $this, 'save_addon_options' ), 20, 2 );
		add_filter( 'woocommerce_product_addon_cart_item_data', array( $this, 'addon_price' ), 20, 4 );
		add_filter( 'woocommerce_bookings_calculated_booking_cost_success_output', array( $this, 'filter_output_cost' ), 10, 3 );
		add_filter( 'woocommerce_product_addons_adjust_price', array( $this, 'adjust_price' ), 20, 2 );
		add_filter( 'booking_form_calculated_booking_cost', array( $this, 'adjust_booking_cost' ), 10, 3 );
	}

	/**
	 * Show grand total or not?
	 * @param  bool $show_grand_total
	 * @param  object $product
	 * @return bool
	 */
	public function addons_show_grand_total( $show_grand_total, $product ) {
		if ( $product->is_type( 'booking' ) && defined( 'WC_PRODUCT_ADDONS_VERSION' ) && version_compare( WC_PRODUCT_ADDONS_VERSION, '3.0', '<' ) ) {
			$show_grand_total = false;
		}
		return $show_grand_total;
	}

	/**
	 * Show options
	 */
	public function addon_options( $post, $addon, $loop ) {
		$css_classes = '';

		if ( is_object( $post ) ) {
			$product = wc_get_product( $post->ID );
			$css_classes .= 'show_if_booking';
			if ( ! $product->is_type( 'booking' ) ) {
				$css_classes .= ' hide_initial_booking_addon_options';
			}
		}

		if ( defined( 'WC_PRODUCT_ADDONS_VERSION' ) && version_compare( WC_PRODUCT_ADDONS_VERSION, '3.0', '<' ) ) {
		?>
		<tr class="<?php echo esc_attr( $css_classes ); ?>">
			<td class="addon_wc_booking_person_qty_multiplier addon_required" width="50%">
				<label for="addon_wc_booking_person_qty_multiplier_<?php echo $loop; ?>"><?php esc_html_e( 'Bookings: Multiply cost by person count', 'woocommerce-bookings' ); ?></label>
				<input type="checkbox" id="addon_wc_booking_person_qty_multiplier_<?php echo $loop; ?>" name="addon_wc_booking_person_qty_multiplier[<?php echo $loop; ?>]" <?php checked( ! empty( $addon['wc_booking_person_qty_multiplier'] ), true ); ?> />
			</td>
			<td class="addon_wc_booking_block_qty_multiplier addon_required" width="50%">
				<label for="addon_wc_booking_block_qty_multiplier_<?php echo $loop; ?>"><?php esc_html_e( 'Bookings: Multiply cost by block count', 'woocommerce-bookings' ); ?></label>
				<input type="checkbox" id="addon_wc_booking_block_qty_multiplier_<?php echo $loop; ?>" name="addon_wc_booking_block_qty_multiplier[<?php echo $loop; ?>]" <?php checked( ! empty( $addon['wc_booking_block_qty_multiplier'] ), true ); ?> />
			</td>
		</tr>
		<?php
		} else {
		?>
		<div class="<?php echo esc_attr( $css_classes ); ?>">
			<div class="addon_wc_booking_person_qty_multiplier">
				<label for="addon_wc_booking_person_qty_multiplier_<?php echo $loop; ?>">
				<input type="checkbox" id="addon_wc_booking_person_qty_multiplier_<?php echo $loop; ?>" name="addon_wc_booking_person_qty_multiplier[<?php echo $loop; ?>]" <?php checked( ! empty( $addon['wc_booking_person_qty_multiplier'] ), true ); ?> /> <?php esc_html_e( 'Bookings: Multiply cost by person count', 'woocommerce-bookings' ); ?></label>
			</div>

			<div class="addon_wc_booking_block_qty_multiplier">
				<label for="addon_wc_booking_block_qty_multiplier_<?php echo $loop; ?>">
				<input type="checkbox" id="addon_wc_booking_block_qty_multiplier_<?php echo $loop; ?>" name="addon_wc_booking_block_qty_multiplier[<?php echo $loop; ?>]" <?php checked( ! empty( $addon['wc_booking_block_qty_multiplier'] ), true ); ?> /> <?php esc_html_e( 'Bookings: Multiply cost by block count', 'woocommerce-bookings' ); ?></label>
			</div>
		</div>
		<?php
		}
	}

	/**
	 * Save options
	 */
	public function save_addon_options( $data, $i ) {
		$data['wc_booking_person_qty_multiplier'] = isset( $_POST['addon_wc_booking_person_qty_multiplier'][ $i ] ) ? 1 : 0;
		$data['wc_booking_block_qty_multiplier']  = isset( $_POST['addon_wc_booking_block_qty_multiplier'][ $i ] ) ? 1 : 0;

		return $data;
	}

	/**
	 * Change addon price based on settings
	 * @return float
	 */
	public function addon_price( $cart_item_data, $addon, $product_id, $post_data ) {
		$product = wc_get_product( $product_id );

		// Make sure we know if multipliers are enabled so adjust_booking_cost can see them
		foreach ( $cart_item_data as $key => $data ) {
			if ( ! empty( $addon['wc_booking_person_qty_multiplier'] ) ) {
				$cart_item_data[ $key ]['wc_booking_person_qty_multiplier'] = 1;
			}
			if ( ! empty( $addon['wc_booking_block_qty_multiplier'] ) ) {
				$cart_item_data[ $key ]['wc_booking_block_qty_multiplier'] = 1;
			}
		}

		return $cart_item_data;
	}

	/**
	 * Don't adjust price for bookings since the booking form class adds the costs itself
	 * @return bool
	 */
	public function adjust_price( $bool, $cart_item ) {
		if ( $cart_item['data']->is_type( 'booking' ) ) {
			return false;
		}
		return $bool;
	}

	/**
	 * Adjust the final booking cost
	 */
	public function adjust_booking_cost( $booking_cost, $booking_form, $posted ) {
		$addons = $GLOBALS['Product_Addon_Cart']->add_cart_item_data( array(), $booking_form->product->get_id(), $posted, true );

		$addon_costs  = 0;
		$booking_data = $booking_form->get_posted_data( $posted );

		if ( ! empty( $addons['addons'] ) ) {
			foreach ( $addons['addons'] as $addon ) {
				$person_multiplier = 1;
				$duration_multipler = 1;

				$addon['price'] = ( ! empty( $addon['price'] ) ) ? $addon['price'] : 0;

				if ( ! empty( $addon['wc_booking_person_qty_multiplier'] ) && ! empty( $booking_data['_persons'] ) && array_sum( $booking_data['_persons'] ) ) {
					$person_multiplier = array_sum( $booking_data['_persons'] );
				}
				if ( ! empty( $addon['wc_booking_block_qty_multiplier'] ) && ! empty( $booking_data['_duration'] ) ) {
					$duration_multipler = (int) $booking_data['_duration'];
				}

				if ( defined( 'WC_PRODUCT_ADDONS_VERSION' ) && version_compare( WC_PRODUCT_ADDONS_VERSION, '3.0', '>=' ) ) {
					$price_type  = $addon['price_type'];
					$addon_price = $addon['price'];

					switch ( $price_type ) {
						case 'percentage_based':
							$addon_costs += (float) ( ( $booking_cost * ( $addon_price / 100 ) ) * ( $person_multiplier * $duration_multipler ) );
							break;
						case 'flat_fee':
							$addon_costs += (float) $addon_price;
							break;
						default:
							$addon_costs += (float) ( $addon_price * $person_multiplier * $duration_multipler );
							break;
					}
				} else {
					$addon_costs += floatval( $addon['price'] ) * $person_multiplier * $duration_multipler;
				}
			}
		}

		return $booking_cost + $addon_costs;
	}

	/**
	 * Filter the cost display of bookings after booking selection.
	 * This only filters on success.
	 *
	 * @since 1.11.4
	 * @param html $output
	 * @param string $display_price
	 * @param object $product
	 * @return JSON Filtered results
	 */
	public function filter_output_cost( $output, $display_price, $product ) {
		parse_str( $_POST['form'], $posted );
		$booking_form = new WC_Booking_Form( $product );
		$cost         = $booking_form->calculate_booking_cost( $posted );

		wp_send_json( array(
			'result'    => 'SUCCESS',
			'html'      => $output,
			'raw_price' => (float) $cost,
		) );
	}
}

new WC_Bookings_Addons();
