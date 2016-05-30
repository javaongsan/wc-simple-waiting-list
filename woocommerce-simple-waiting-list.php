<?php
/**
 * Plugin Name: WOOCOMMERCE SIMPLE WAITING LIST
 * Plugin URI:
 * Description: A waiting list plugin for WooCommerce
 * Version: 1.0.0
 * Author: Bob Ong Swee San
 * Author URI: 
 * License: GPL2 
 * Requires at least: 4.5
 * Tested up to: 4.5.2
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if( ! class_exists( 'woocommerce_simple_waiting_list' ) ){

	class woocommerce_simple_waiting_list
	{

		public function __construct() {
			define( 'WOOCOMMERCE_SIMPLE_WAITING_LIST_VERSION', '1.1.0' );
			define( 'WOOCOMMERCE_SIMPLE_WAITING_LIST_PLUGIN_DIR', untrailingslashit( plugin_dir_path( __FILE__ ) ) );
			define( 'WOOCOMMERCE_SIMPLE_WAITING_LIST_PLUGIN_BASENAME', untrailingslashit( plugin_basename( dirname( __FILE__ ) ) ));
			define( 'WOOCOMMERCE_SIMPLE_WAITING_LIST_PLUGIN_URL', untrailingslashit( plugins_url( basename( plugin_dir_path( __FILE__ ) ), basename( __FILE__ ) ) ) );
			define( 'WOOCOMMERCE_SIMPLE_WAITING_LIST_PLUGIN_META', 'woocommerce_simple_waiting_list_meta_key' );

			add_action( 'init', array( $this, 'woocommerce_simple_waiting_list_init' ) );
			add_action( 'woocommerce_init', array( $this, 'woocommerce_simple_waiting_list_mailer' ) );
			add_filter( 'woocommerce_email_classes', array( $this, 'woocommerce_simple_waiting_list_class' ) );
		}

		public function woocommerce_simple_waiting_list_init() {
		    load_plugin_textdomain( 'woocommerce-simple-waiting-list', false, WOOCOMMERCE_SIMPLE_WAITING_LIST_PLUGIN_BASENAME . "/languages" );
			require_once( 'includes/woocommerce-simple-waiting-list-form.php' );
			require_once( 'includes/woocommerce-simple-waiting-list-options.php' );
			
		}

		public function woocommerce_simple_waiting_list_class( $emails )
		{
			$emails['woocommerce_simple_waiting_list_email'] = include( 'includes/woocommerce-simple-waiting-list-email.php' );
			return $emails;
		}
	
		public function woocommerce_simple_waiting_list_mailer() {
			add_action( 'woocommerce_simple_waiting_list_email_send', array( 'WC_Emails', 'send_transactional_email' ), 10, 2 );
		}
	}
}
new woocommerce_simple_waiting_list();
?>