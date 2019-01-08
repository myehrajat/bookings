<?php
/**
 * The template for displaying the booking form and calendar to customers.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce-bookings/booking-form/date-picker.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/bookings-templates/
 * @author  Automattic
 * @version 1.10.8
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

wp_enqueue_script( 'wc-bookings-date-picker' );
extract( $field );

$month_before_day = strpos( __( 'F j, Y', 'woocommerce-bookings' ), 'F' ) < strpos( __( 'F j, Y', 'woocommerce-bookings' ), 'j' );
?>
<fieldset class="wc-bookings-date-picker wc-bookings-date-picker-<?php echo esc_attr( $product_type ); ?> <?php echo implode( ' ', $class ); ?>">
	<legend>
		<span class="label"><?php echo $label; ?></span>: <small class="wc-bookings-date-picker-choose-date"><?php _e( 'Choose...', 'woocommerce-bookings' ); ?></small>
	</legend>

	<div class="picker" data-display="<?php echo $display; ?>" data-duration-unit="<?php echo esc_attr( $duration_unit ); ?>" data-default-availability="<?php echo $default_availability ? 'true' : 'false'; ?>" data-min_date="<?php echo ! empty( $min_date_js ) ? $min_date_js : 0; ?>" data-max_date="<?php echo $max_date_js; ?>" data-default_date="<?php echo esc_attr( $default_date ); ?>" data-is_range_picker_enabled="<?php echo $is_range_picker_enabled ? 1 : 0; ?>"></div>

	<div class="wc-bookings-date-picker-date-fields">
		<?php if ( 'customer' == $duration_type && $is_range_picker_enabled ) : ?>
			<span><?php echo esc_html( apply_filters( 'woocommerce_bookings_date_picker_start_label', __( 'Start', 'woocommerce-bookings' ) ) ); ?>:</span><br />
		<?php endif; ?>

		<?php
		// woocommerce_bookings_mdy_format filter to choose between month/day/year and day/month/year format
		if ( $month_before_day && apply_filters( 'woocommerce_bookings_mdy_format', true ) ) :
		?>
		<label>
			<input type="text" name="<?php echo $name; ?>_month" placeholder="<?php _e( 'mm', 'woocommerce-bookings' ); ?>" size="2" class="booking_date_month" />
			<span><?php _e( 'Month', 'woocommerce-bookings' ); ?></span>
		</label> / <label>
			<input type="text" name="<?php echo $name; ?>_day" placeholder="<?php _e( 'dd', 'woocommerce-bookings' ); ?>" size="2" class="booking_date_day" />
			<span><?php _e( 'Day', 'woocommerce-bookings' ); ?></span>
		</label>
		<?php else : ?>
		<label>
			<input type="text" name="<?php echo $name; ?>_day" placeholder="<?php _e( 'dd', 'woocommerce-bookings' ); ?>" size="2" class="booking_date_day" />
			<span><?php _e( 'Day', 'woocommerce-bookings' ); ?></span>
		</label> / <label>
			<input type="text" name="<?php echo $name; ?>_month" placeholder="<?php _e( 'mm', 'woocommerce-bookings' ); ?>" size="2" class="booking_date_month" />
			<span><?php _e( 'Month', 'woocommerce-bookings' ); ?></span>
		</label>
		<?php endif; ?> / <label>
			<input type="text" value="<?php echo date( 'Y' ); ?>" name="<?php echo $name; ?>_year" placeholder="<?php _e( 'YYYY', 'woocommerce-bookings' ); ?>" size="4" class="booking_date_year" />
			<span><?php _e( 'Year', 'woocommerce-bookings' ); ?></span>
		</label>
	</div>

	<?php if ( 'customer' == $duration_type && $is_range_picker_enabled ) : ?>
		<div class="wc-bookings-date-picker-date-fields">
			<span><?php echo esc_html( apply_filters( 'woocommerce_bookings_date_picker_end_label', __( 'End', 'woocommerce-bookings' ) ) ); ?>:</span><br />
			<?php if ( $month_before_day && apply_filters( 'woocommerce_bookings_mdy_format', true ) ) : ?>
			<label>
				<input type="text" name="<?php echo $name; ?>_to_month" placeholder="<?php _e( 'mm', 'woocommerce-bookings' ); ?>" size="2" class="booking_to_date_month" />
				<span><?php _e( 'Month', 'woocommerce-bookings' ); ?></span>
			</label> / <label>
				<input type="text" name="<?php echo $name; ?>_to_day" placeholder="<?php _e( 'dd', 'woocommerce-bookings' ); ?>" size="2" class="booking_to_date_day" />
				<span><?php _e( 'Day', 'woocommerce-bookings' ); ?></span>
			</label>
			<?php else : ?>
			<label>
				<input type="text" name="<?php echo $name; ?>_to_day" placeholder="<?php _e( 'dd', 'woocommerce-bookings' ); ?>" size="2" class="booking_to_date_day" />
				<span><?php _e( 'Day', 'woocommerce-bookings' ); ?></span>
			</label> / <label>
				<input type="text" name="<?php echo $name; ?>_to_month" placeholder="<?php _e( 'mm', 'woocommerce-bookings' ); ?>" size="2" class="booking_to_date_month" />
				<span><?php _e( 'Month', 'woocommerce-bookings' ); ?></span>
			</label>
			<?php endif; ?> / <label>
				<input type="text" value="<?php echo date( 'Y' ); ?>" name="<?php echo $name; ?>_to_year" placeholder="<?php _e( 'YYYY', 'woocommerce-bookings' ); ?>" size="4" class="booking_to_date_year" />
				<span><?php _e( 'Year', 'woocommerce-bookings' ); ?></span>
			</label>
		</div>
	<?php endif; ?>
</fieldset>
