<?php
/**
 * Plugin Name: WC SIMPLE WAITING LIST
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

if( ! class_exists( 'wc_simple_waiting_list' ) ){

	class wc_simple_waiting_list
	{

		public function __construct() {
			define( 'WC_SIMPLE_WAITING_LIST_VERSION', '1.1.0' );
			define( 'WC_SIMPLE_WAITING_LIST_PLUGIN_DIR', untrailingslashit( plugin_dir_path( __FILE__ ) ) );
			define( 'WC_SIMPLE_WAITING_LIST_PLUGIN_BASENAME', untrailingslashit( plugin_basename( dirname( __FILE__ ) ) ));
			define( 'WC_SIMPLE_WAITING_LIST_PLUGIN_URL', untrailingslashit( plugins_url( basename( plugin_dir_path( __FILE__ ) ), basename( __FILE__ ) ) ) );
			define( 'WC_SIMPLE_WAITING_LIST_PLUGIN_META', 'wc_simple_waiting_list_meta_key' );

			add_action( 'init', array( $this, 'wc_simple_waiting_list_init' ) );
			add_action( 'woocommerce_init', array( $this, 'wc_simple_waiting_list_mailer' ) );
			add_filter( 'woocommerce_email_classes', array( $this, 'wc_simple_waiting_list_class' ) );
		}

		public function wc_simple_waiting_list_init() {
		    load_plugin_textdomain( 'wc-simple-waiting-list', false, WC_SIMPLE_WAITING_LIST_PLUGIN_BASENAME . "/languages" );
			require_once( 'includes/wc-simple-waiting-list-form.php' );
			require_once( 'includes/wc-simple-waiting-list-options.php' );
			
		}

		public function wc_simple_waiting_list_class( $emails )
		{
			$emails['wc_simple_waiting_list_email'] = include( 'includes/wc-simple-waiting-list-email.php' );
			return $emails;
		}
	
		public function wc_simple_waiting_list_mailer() {
			add_action( 'wc_simple_waiting_list_email_send', array( 'WC_Emails', 'send_transactional_email' ), 10, 2 );
		}
	}
}
new wc_simple_waiting_list();
?>