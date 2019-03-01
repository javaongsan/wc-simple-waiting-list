<?php
/**
 * WC Simple Waiting List Ajax.
 *
 * @since   1.0.6
 * @package WC_Simple_Waiting_List
 */

/**
 * WC Simple Waiting List Ajax.
 *
 * @since 1.0.6
 */
class WCSWL_Ajax {
	/**
	 * Parent plugin class.
	 *
	 * @since 1.0.6
	 *
	 * @var   WC_Simple_Waiting_List
	 */
	protected $plugin = null;

	/**
	 * Constructor.
	 *
	 * @since  1.0.6
	 *
	 * @param  WC_Simple_Waiting_List $plugin Main plugin object.
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
		$this->hooks();
	}

	/**
	 * Initiate our hooks.
	 *
	 * @since  1.0.6
	 */
	public function hooks() {
		add_action( 'wp_ajax_wc_simple_waiting_list_export_csv', array( $this, 'export_csv' ) );
		add_action( 'wp_ajax_wc_simple_waiting_list_register_user',array( $this, 'register_user' ) );
		add_action( 'wp_ajax_nopriv_wc_simple_waiting_list_register_user', array( $this, 'register_user' ) );
		add_action( 'wp_ajax_wc_simple_waiting_list_user_deregister_user', array( $this, 'deregister_user' ) );
		add_action( 'wp_ajax_nopriv_wc_simple_waiting_list_user_deregister_user', array( $this, 'deregister_user' ) );
	}

	public function export_csv() {
		if ( wp_verify_nonce( $_POST['wc_simple_waiting_list_nonce'], 'wc-simple-waiting-list-nonce' ) ) {
			$results = $this->plugin->admin->export_reminders();
			if ( $results ) {
				echo $results;
			} else {
				echo 'fail';
			}
		}
		die();
	}

	public function register_user() {
		if ( isset( $_POST['user_email'] ) && isset( $_POST['product_id'] ) && wp_verify_nonce( $_POST['wc_simple_waiting_list_nonce'], 'wc-simple-waiting-list-public-nonce' ) ) {
			if ( $this->plugin->public->wc_simple_waiting_list_register( $_POST['user_email'],  $_POST['product_id'] ) ) {
				echo 'success';
			} else {
				echo 'fail';
			}
		}
		die();
	}

	public function deregister_user() {
		if ( isset( $_POST['user_email'] ) && isset( $_POST['product_id'] ) && wp_verify_nonce( $_POST['wc_simple_waiting_list_nonce'], 'wc-simple-waiting-list-public-nonce' ) ) {
			if ( $this->plugin->public->wc_simple_waiting_list_deregister( $_POST['user_email'],  $_POST['product_id'] ) ) {
				echo 'success';
			} else {
				echo 'fail';
			}
		}
		die();
	}
}
