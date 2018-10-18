<?php
/**
 * WC Simple Waiting List Db.
 *
 * @since   1.0.6
 * @package WC_Simple_Waiting_List
 */

/**
 * WC Simple Waiting List Db.
 *
 * @since 1.0.6
 */
class WCSWL_Db {
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

	}

	/**
	 * Get users reminders from database by product id.
	 *
	 * @since  1.0.6
	 */
	public function get_emails_by_product_id( $product_id ) {
		global $wpdb;
		return $wpdb->get_results( $wpdb->prepare( "SELECT email FROM {$wpdb->prefix}wswl_product_list where product_id = %d", $product_id ) );
	}

	/**
	 * Delete users reminders from database by product id.
	 *
	 * @since  1.0.6
	 */
	public function del_emails_by_product_id( $product_id ) {
		global $wpdb;
		return $wpdb->delete( $wpdb->prefix . 'wswl_product_list', array(
			'product_id' => $product_id,
		));
	}

	/**
	 * Get all users reminders from database.
	 *
	 * @since  1.0.6
	 */
	public function get_user_reminders() {
		global $wpdb;
		return $wpdb->get_results( "SELECT id, email, product_id, created_date FROM {$wpdb->prefix}wswl_product_list order by id" );
	}

	/**
	 * Get product count from database.
	 *
	 * @since  1.0.6
	 */
	public function get_product_count() {
		global $wpdb;
		return $wpdb->get_var( "SELECT count(product_id) FROM {$wpdb->prefix}wswl_product_list group by product_id" );
	}

	/**
	 * Is register.
	 *
	 * @since   1.0.6
	 */
	public function is_register( $user_email, $product_id ) {
		global $wpdb;
		$waiting_list = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$wpdb->prefix}wswl_product_list where email = %s and product_id = %d", $user_email, $product_id ) );
		if ( empty( $waiting_list ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Register User.
	 *
	 * @since   1.0.6
	 */
	public function register_user( $user_email, $product_id ) {
		global $wpdb;
		return $wpdb->replace( $wpdb->prefix . 'wswl_product_list', array(
			'email' => $user_email,
			'product_id' => $product_id,
		));
	}

	/**
	 * Deregister user.
	 *
	 * @since   1.0.6
	 */
	public function deregister_user( $user_email, $product_id ) {
		global $wpdb;
		return $wpdb->delete( $wpdb->prefix . 'wswl_product_list', array(
			'email' => $user_email,
			'product_id' => $product_id,
		));
	}
}
