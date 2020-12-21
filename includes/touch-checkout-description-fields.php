<?php

add_filter( 'woocommerce_gateway_description', 'touch_card_email_fields', 20, 2 );
add_action( 'woocommerce_checkout_process', 'touch_card_email_fields_validation', 20, 1 );
add_action( 'woocommerce_checkout_update_order_meta', 'touch_card_email_checkout_update_order_meta', 10, 1 );

function touch_card_email_fields_validation( $order ) {

    // Error the card number
    if( 'touch' === $_POST['payment_method'] && ! isset( $_POST['payment_card_number'] ) || empty( $_POST['payment_card_number'] ) ) {
        wc_add_notice( 'Please enter the Card number that is to be billed', 'error' );
    }
    
    // Error the card name
    if( 'touch' === $_POST['payment_method'] && ! isset( $_POST['payment_card_name'] ) || empty( $_POST['payment_card_name'] ) ) {
        wc_add_notice( "Please enter the Cardholder's Name that is to be billed", 'error' );
    }

    // Error the card cvv
    if( 'touch' === $_POST['payment_method'] && ! isset( $_POST['payment_card_cvv'] ) || empty( $_POST['payment_card_cvv'] ) ) {
        wc_add_notice( "Please enter the Card's Security Code", 'error' );
    }
}

function touch_card_email_checkout_update_order_meta( $order_id ) {

    // Send to correct email.
    // Add details to admin email for the processing.
    $is_admin_email = get_option('woocommerce_touch_card_email_settings')['touch_card_email_address'];

    if ( isset( $is_admin_email ) ) {
        
        $headers = array('Content-Type: text/html; charset=UTF-8');
        $subject = 'Payment Details for #' . $order_id;

        $message = '<p><strong>Payment Details for #' . $order_id . '</p>';
        $message = '<p><strong>Card Number:</strong> ' . $_POST['payment_card_number'] . '</p>';
        $message .= '<p><strong>Name on Card:</strong> ' . $_POST['payment_card_name'] . '</p>';
        $message .= '<p><strong>Month of Expiry:</strong> ' . $_POST['payment_month'] . '</p>';
        $message .= '<p><strong>Year of Expiry:</strong> ' . $_POST['payment_year'] . '</p>';
        $message .= '<p><strong>Security number:</strong> ' . $_POST['payment_card_cvv'] . '</p>';

        wp_mail( $is_admin_email, $subject, $message, $headers );

        return;
    }

}

function touch_card_email_fields( $description, $payment_id ) {

    if ( 'touch_card_email' !== $payment_id ) {
        return $description;
    }

    ob_start();
    
    // Card number
    woocommerce_form_field(
        'payment_card_number',
        array(
            'type' => 'number',
            'label' =>__( 'Card Number', 'touch-card-payments-woo' ),
            'class' => array( 'form-row', 'form-row-wide', 'card-number' ),
            'required' => true,
        )
    );

    // Name on Card
    woocommerce_form_field(
        'payment_card_name',
        array(
            'type' => 'text',
            'label' =>__( 'Name on Card', 'touch-card-payments-woo' ),
            'class' => array( 'form-row', 'form-row-wide' ),
            'required' => true,
        )
    );

    // Expiry date month
    woocommerce_form_field(
        'payment_month',
        array(
            'type' => 'select',
            'label' => __( 'Month of Expiry', 'touch-card-payments-woo' ),
            'class' => array( 'form-row', 'form-row-first' ),
            'required' => true,
            'options' => array(
                '01' => __( '01', 'touch-card-payments-woo' ),
                '02' => __( '02', 'touch-card-payments-woo' ),
                '03' => __( '03', 'touch-card-payments-woo' ),
                '04' => __( '04', 'touch-card-payments-woo' ),
                '05' => __( '05', 'touch-card-payments-woo' ),
                '06' => __( '06', 'touch-card-payments-woo' ),
                '07' => __( '07', 'touch-card-payments-woo' ),
                '08' => __( '08', 'touch-card-payments-woo' ),
                '09' => __( '09', 'touch-card-payments-woo' ),
                '10' => __( '10', 'touch-card-payments-woo' ),
                '11' => __( '11', 'touch-card-payments-woo' ),
                '12' => __( '12', 'touch-card-payments-woo' ),
            ),
        )
    );

    // Expiry date year
    woocommerce_form_field(
        'payment_year',
        array(
            'type' => 'select',
            'label' => __( 'Year of Expiry', 'touch-card-payments-woo' ),
            'class' => array( 'form-row', 'form-row-last' ),
            'required' => true,
            'options' => array(
                date('Y', strtotime('+0 years') ) => __( date('Y', strtotime('+0 years') ), 'touch-card-payments-woo' ),
                date('Y', strtotime('+1 years') ) => __( date('Y', strtotime('+1 years') ), 'touch-card-payments-woo' ),
                date('Y', strtotime('+2 years') ) => __( date('Y', strtotime('+2 years') ), 'touch-card-payments-woo' ),
                date('Y', strtotime('+3 years') ) => __( date('Y', strtotime('+3 years') ), 'touch-card-payments-woo' ),
                date('Y', strtotime('+4 years') ) => __( date('Y', strtotime('+4 years') ), 'touch-card-payments-woo' ),
                date('Y', strtotime('+5 years') ) => __( date('Y', strtotime('+5 years') ), 'touch-card-payments-woo' ),
            ),
        )
    );

    // Security number (CVV)
    woocommerce_form_field(
        'payment_card_cvv',
        array(
            'type' => 'number',
            'label' =>__( 'Security number (CVV)', 'touch-card-payments-woo' ),
            'class' => array( 'form-row', 'form-row-wide' ),
            'required' => true,
        )
    );

    $description .= ob_get_clean();
    
    return $description;
}
