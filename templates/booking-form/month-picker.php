<?php
/**
 * The template used for the month picker on the booking form.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce-bookings/booking-form/month-picker.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/bookings-templates/
 * @author  Automattic
 * @version 1.8.0
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

wp_enqueue_script( 'wc-bookings-month-picker' );
$blocks = $field['blocks'];
$label  = $field['label'];
$name   = $field['name'];
$fully_booked_months = array_keys( $field['fully_booked_months'] );
?>
<div class="form-field form-field-wide">
	<label for="<?php echo $name; ?>"><?php echo $label; ?>:</label>
	<ul class="block-picker">
		<?php
		foreach ( $blocks as $block ) {
			$fully_booked_class = in_array( date( 'Y-n', $block ), $fully_booked_months ) ? 'fully_booked' : '';
			echo '<li class="' . $fully_booked_class . '" data-block="' . esc_attr( date( 'Ym', $block ) ) . '"><a href="#" data-value="' . date( 'Y-m', $block ) . '">' . date_i18n( 'M y', $block ) . '</a></li>';
		}
		?>
	</ul>
	<input type="hidden" name="<?php echo $name; ?>_yearmonth" id="<?php echo $name; ?>" />
</div>

