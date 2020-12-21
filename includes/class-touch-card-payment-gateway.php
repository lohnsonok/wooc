<?php

/**
 * Bank Transfer Payment Gateway.
 *
 * Provides a Bank Transfer Payment Gateway. Based on code by Mike Pepper.
 *
 * @class       WC_Gateway_Touch
 * @extends     WC_Payment_Gateway
 * @version     WC-2.1.0
 * @package     ClassicCommerce/Classes/Payment
 */
class WC_Gateway_Touch_card extends WC_Payment_Gateway {

	/**
	 * Constructor for the gateway.
	 */
	public function __construct() {

		$this->id                 = 'touch_card_email';
		$this->icon               = apply_filters( 'woocommerce_touch_card_email_icon', plugins_url('../assets/icon.png', __FILE__ ) );
		$this->has_fields         = false;
		$this->method_title       = __( 'Debit/Credit Card Capture Email', 'classic-commerce' );
		$this->method_description = __( 'Take payments in Card transfer to Email Address.', 'classic-commerce' );

		// Load the settings.
		$this->init_form_fields();
		$this->init_settings();

		// Define user set variables.
		$this->title        = $this->get_option( 'title' );
		$this->description  = $this->get_option( 'description' );
		$this->instructions = $this->get_option( 'instructions' );

		// Email address to send the card details.
		$this->touch_card_email_address = $this->get_option( 'touch_card_email_address' );

		// Actions.
		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'save_account_details' ) );
		add_action( 'woocommerce_thankyou_touch_card_email', array( $this, 'thankyou_page' ) );

		// Customer Emails.
		add_action( 'woocommerce_email_before_order_table', array( $this, 'email_instructions' ), 10, 3 );
	}

	/**
	 * Initialise Gateway Settings Form Fields.
	 */
	public function init_form_fields() {

		$this->form_fields = array(
			'enabled'         => array(
				'title'   => __( 'Enable/Disable', 'classic-commerce' ),
				'type'    => 'checkbox',
				'label'   => __( 'Enable Card Capture Email', 'classic-commerce' ),
				'default' => 'no',
			),
			'title'           => array(
				'title'       => __( 'Title', 'classic-commerce' ),
				'type'        => 'text',
				'description' => __( 'This controls the title which the user sees during checkout.', 'classic-commerce' ),
				'default'     => __( 'Debit/Credit Card transfer', 'classic-commerce' ),
				'desc_tip'    => true,
			),
			'touch_card_email_address' => array(
				'title'       => __( 'Receiving Email Address', 'classic-commerce' ),
				'type'        => 'textarea',
				'description' => __( 'Enter Email Address receiving the Captured Details', 'classic-commerce' ),
				'default'     => __( 'Enter Email Address receiving the Captured Details', 'classic-commerce' ),
				'desc_tip'    => true,
			),
			'description'     => array(
				'title'       => __( 'Description', 'classic-commerce' ),
				'type'        => 'textarea',
				'description' => __( 'Payment method description that the customer will see on your checkout.', 'classic-commerce' ),
				'default'     => __( 'Make your payment directly into our bank account. Please use your Order ID as the payment reference. Your order will not be shipped until the funds have cleared in our account.', 'classic-commerce' ),
				'desc_tip'    => true,
			),
			'instructions'    => array(
				'title'       => __( 'Instructions', 'classic-commerce' ),
				'type'        => 'textarea',
				'description' => __( 'Instructions that will be added to the thank you page and emails.', 'classic-commerce' ),
				'default'     => __( 'Instructions that will be added to the thank you page and emails.', 'classic-commerce' ),
				'desc_tip'    => true,
			),
		);

	}

	/**
	 * Output for the order received page.
	 *
	 * @param int $order_id Order ID.
	 */
	public function thankyou_page( $order_id ) {

		if ( $this->instructions ) {
			echo wp_kses_post( wpautop( wptexturize( wp_kses_post( $this->instructions ) ) ) );
		}

	}

	/**
	 * Add content to the WC emails.
	 *
	 * @param WC_Order $order Order object.
	 * @param bool     $sent_to_admin Sent to admin.
	 * @param bool     $plain_text Email format: plain text or HTML.
	 */
	public function email_instructions( $order, $sent_to_admin, $plain_text = false ) {

		if ( ! $sent_to_admin && 'touch_card_email' === $order->get_payment_method() && $order->has_status( 'on-hold' ) ) {
			if ( $this->instructions ) {
				echo wp_kses_post( wpautop( wptexturize( $this->instructions ) ) . PHP_EOL );
			}
		}

	}

	/**
	 * Process the payment and return the result.
	 *
	 * @param int $order_id Order ID.
	 * @return array
	 */
	public function process_payment( $order_id ) {
		
		$order = wc_get_order( $order_id );

		if ( $order->get_total() > 0 ) {
			// Mark as on-hold (we're awaiting the payment).
		// 	$order->update_status( 'on-hold', __( 'Awaiting touch_card_email payment', 'classic-commerce' ) );
		// } else {
			$order->payment_complete();
		}

		// Remove cart.
		WC()->cart->empty_cart();

		// Return thankyou redirect.
		return array(
			'result'   => 'success',
			'redirect' => $this->get_return_url( $order ),
		);

	}
}
