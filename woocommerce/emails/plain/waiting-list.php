<?php
/**
 * Request a Review email template
 *
 * @package 	WOOCOMMERCE SIMPLE WAITING LIST
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

echo "= " . $email_heading . " =\n\n";

echo printf( __( "Dear Customer,
\n\n
The product you are interested in, %s, is back in stock now.\n\n
[ %s ]
\n\n
We recommend that you place your order as soon as possible because the popularity of this product may soon result in another out-of-stock condition.
", 'wc-simple-waiting-list' ), $product_name, $product_url , get_option( 'blogname' ) ) . "\n\n";

echo "=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";



echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

echo apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) );
