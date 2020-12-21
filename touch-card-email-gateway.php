<?php
/**
 * Plugin Name: Touch Card Payments Gateway
 * Plugin URI: https://www.hitech-trade.com
 * Author: Hitech Trade
 * Author URI: https://www.hitech-trade.com
 * Description: Local Payments Gateway for Credit Card Collection to Email.
 * Version: 0.1.0
 * License: GPL2
 * License URL: http://www.gnu.org/licenses/gpl-2.0.txt
 * text-domain: touch-card-payments-woo
 *
 * Class WC_Gateway_Touch file.
 *
 * @package WooCommerce\Touch
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) return;

add_action( 'plugins_loaded', 'touch_payment_init', 11 );

function touch_payment_init() {
    if( class_exists( 'WC_Payment_Gateway' ) ) {
		require_once plugin_dir_path( __FILE__ ) . '/includes/class-touch-card-payment-gateway.php';
		require_once plugin_dir_path( __FILE__ ) . '/includes/touch-checkout-description-fields.php';
	}
	<script>
		// your javscript code goes
		jQuery(document).ready(function(){
	
			function creditCardFormat(value, matches) {
			
			var match = matches && matches[0] || ''
			var parts = []
			for (i=0, len=match.length; i<len; i+=4) {
				parts.push(match.substring(i, i+4))
			}
			if (parts.length) {
				return parts.join(' ')
			} else {
				return value
			}
			}
		
			jQuery( 'form.woocommerce-checkout' ).on('keyup', '#card-number', function() {
			var card = this.value.replace(/\s+/g, '').replace(/[^0-9]/gi, '');
			var matches = card.match(/\d{4,16}/g);
			
			this.value = creditCardFormat(this.value, matches);
			
			});
  	</script>
}

add_filter( 'woocommerce_payment_gateways', 'add_to_woo_touch_payment_gateway');

function add_to_woo_touch_payment_gateway( $gateways ) {
    $gateways[] = 'WC_Gateway_Touch_card';
    return $gateways;
}
