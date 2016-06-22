<?php
/**
 * Request a Review email template
 *
 * @package 	WOOCOMMERCE SIMPLE WAITING LIST
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;

do_action( 'woocommerce_email_header', $email_heading, $email ); 
?>

<p><?php printf( __( "Dear Customer,
<br />
The product you are interested in, %s, is back in stock now.<br />
[ %s ]<br />
<br />
We recommend that you place your order as soon as possible because the popularity of this product may soon result in another out-of-stock condition.
", 'wc-simple-waiting-list' ), $product_name, $product_url , get_option( 'blogname' ) ); ?>
	
</p>

<?php do_action( 'woocommerce_email_footer' ); ?>