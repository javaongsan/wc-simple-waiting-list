<?php
/**
 * Plugin Name: WC Simple Waiting List
 * Plugin URI:  http://imakeplugins.com
 * Description: A woocommerce extension to allow customers to leave their email address on a waiting list for out of stock products.
 * Version:     1.0.11
 * Author:      Bob Ong
 * Author URI:  http://imakeplugins.com
 * Donate link: http://imakeplugins.com
 * License:     GPLv2
 * Text Domain: wc-simple-waiting-list
 * Domain Path: /languages
 * WC requires at least:   3.0.0
 * WC tested up to:**      3.6.2
 *
 * @link    http://imakeplugins.com
 *
 * @package WC_Simple_Waiting_List
 * @version 1.0.11
 *
 */

/**
 * Copyright (c) 2018 Bob Ong (email : bob@imakeplugins.com)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2 or, at
 * your discretion, any later version, as published by the Free
 * Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */


/**
 * Autoloads files with classes when needed.
 *
 * @since  1.0.6
 * @param  string $class_name Name of the class being requested.
 */
function wc_simple_waiting_list_autoload_classes( $class_name ) {

	// If our class doesn't have our prefix, don't load it.
	if ( 0 !== strpos( $class_name, 'WCSWL_' ) ) {
		return;
	}

	// Set up our filename.
	$filename = strtolower( str_replace( '_', '-', substr( $class_name, strlen( 'WCSWL_' ) ) ) );

	// Include our file.
	WC_Simple_Waiting_List::include_file( 'includes/class-' . $filename );
}
spl_autoload_register( 'wc_simple_waiting_list_autoload_classes' );

/**
 * Main initiation class.
 *
 * @since  1.0.6
 */
final class WC_Simple_Waiting_List {

	/**
	 * Current version.
	 *
	 * @var    string
	 * @since  1.0.6
	 */
	const VERSION = '1.0.11';

	/**
	 * URL of plugin directory.
	 *
	 * @var    string
	 * @since  1.0.6
	 */
	protected $url = '';

	/**
	 * Path of plugin directory.
	 *
	 * @var    string
	 * @since  1.0.6
	 */
	protected $path = '';

	/**
	 * Plugin basename.
	 *
	 * @var    string
	 * @since  1.0.6
	 */
	protected $basename = '';

	/**
	 * Detailed activation error messages.
	 *
	 * @var    array
	 * @since  1.0.6
	 */
	protected $activation_errors = array();

	/**
	 * Singleton instance of plugin.
	 *
	 * @var    WC_Simple_Waiting_List
	 * @since  1.0.6
	 */
	protected static $single_instance = null;

	/**
	 * Instance of WCSWL_Public
	 *
	 * @since 1.0.6
	 * @var WCSWL_Public
	 */
	protected $public;

	/**
	 * Instance of WCSWL_Admin
	 *
	 * @since 1.0.6
	 * @var WCSWL_Admin
	 */
	protected $admin;

	/**
	 * Instance of WCSWL_Ajax
	 *
	 * @since 1.0.6
	 * @var WCSWL_Ajax
	 */
	protected $ajax;

	/**
	 * Plugin name.
	 *
	 * @var    string
	 * @since 1.0.6
	 */
	protected $name = '';

	/**
	 * Instance of Uploads
	 *
	 * @since1.0.6
	 * @var uploads
	 */
	protected $uploads;

	/**
	 * Instance of WCSWL_Db
	 *
	 * @since1.0.6
	 * @var WCSWL_Db
	 */
	protected $db;

	/**
	 * Instance of WCSWL_Feedback
	 *
	 * @since1.0.11
	 * @var WCSWL_Feedback
	 */
	protected $feedback;

	/**
	 * Creates or returns an instance of this class.
	 *
	 * @since   1.0.6
	 * @return  WC_Simple_Waiting_List A single instance of this class.
	 */
	public static function get_instance() {
		if ( null === self::$single_instance ) {
			self::$single_instance = new self();
		}

		return self::$single_instance;
	}

	/**
	 * Sets up our plugin.
	 *
	 * @since  1.0.6
	 */
	protected function __construct() {
		$this->basename = plugin_basename( __FILE__ );
		$this->url      = plugin_dir_url( __FILE__ );
		$this->path     = plugin_dir_path( __FILE__ );
		$this->uploads  = wp_upload_dir();
		$names = explode( '/', $this->basename );
		$this->name = $names[0];
	}

	/**
	 * Attach other plugin classes to the base plugin class.
	 *
	 * @since  1.0.6
	 */
	public function plugin_classes() {

		$this->public = new WCSWL_Public( $this );
		$this->admin = new WCSWL_Admin( $this );
		$this->ajax = new WCSWL_Ajax( $this );
		$this->db = new WCSWL_Db( $this );
		$this->feedback = new WCSWL_Feedback( $this );
	} // END OF PLUGIN CLASSES FUNCTION

	/**
	 * Add hooks and filters.
	 * Priority needs to be
	 * < 10 for CPT_Core,
	 * < 5 for Taxonomy_Core,
	 * and 0 for Widgets because widgets_init runs at init priority 1.
	 *
	 * @since  1.0.6
	 */
	public function hooks() {
		add_action( 'init', array( $this, 'init' ), 0 );
	}

	/**
	 * Activate the plugin.
	 *
	 * @since  1.0.6
	 */
	public function _activate() {
		// Bail early if requirements aren't met.
		if ( ! $this->check_requirements() ) {
			return;
		}

		// Make sure any rewrite functionality has been loaded.
		flush_rewrite_rules();
		$this->create_table();
		add_option( 'wswl_review', false );
	}

	/**
	 * Setup Reminder Table.
	 *
	 * @since  1.0.3
	 */
	public function create_table() {
		global $wpdb;

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE {$wpdb->prefix}wswl_product_list_log (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			recipient tinytext NOT NULL,
			subject tinytext NOT NULL,
			message text NOT NULL,
			created_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
			UNIQUE KEY id (id)
		) $charset_collate;";

		dbDelta( $sql );

		$sql = "CREATE TABLE {$wpdb->prefix}wswl_product_list (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			email text NOT NULL,
			product_id bigint(20) unsigned NOT NULL,
			created_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
			UNIQUE KEY id (id)
		) $charset_collate;";

		dbDelta( $sql );

		add_option( 'wswl_db_version', self::VERSION );
	}

	/**
	 * Deactivate the plugin.
	 * Uninstall routines should be in uninstall.php.
	 *
	 * @since  1.0.6
	 */
	public function _deactivate() {
		// Add deactivation cleanup functionality here.
		$this->drop_table();
	}

	/**
	 * Drop Reminder Table.
	 *
	 * @since  1.0.3
	 */
	public function drop_table() {
		global $wpdb;
		if ( ! class_exists( 'WC_Simple_Waiting_List_Pro' ) ) {
			$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}wswl_product_list_log" );
			$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}wswl_product_list" );
		}
		delete_option( 'wswl_review' );
		delete_option( 'wswl_db_version' );
	}

	/**
	 * Init hooks
	 *
	 * @since  1.0.6
	 */
	public function init() {

		// Bail early if requirements aren't met.
		if ( ! $this->check_requirements() ) {
			return;
		}

		// Load translated strings for plugin.
		load_plugin_textdomain( 'wc-simple-waiting-list', false, dirname( $this->basename ) . '/languages/' );

		// Initialize plugin classes.
		$this->plugin_classes();
	}

	/**
	 * Upgrade old versions.
	 *
	 * @since  1.0.6
	 */
	public function upgrade_log_db() {
		$db_version = get_option( 'wswl_db_version' );
		if ( version_compare( $db_version, '1.0.6', '<' ) ) {
			$this->create_table();
		}
	}

	/**
	 * Check if the plugin meets requirements and
	 * disable it if they are not present.
	 *
	 * @since  1.0.6
	 *
	 * @return boolean True if requirements met, false if not.
	 */
	public function check_requirements() {

		// Bail early if plugin meets requirements.
		if ( $this->meets_requirements() ) {
			return true;
		}

		// Add a dashboard notice.
		add_action( 'all_admin_notices', array( $this, 'requirements_not_met_notice' ) );

		// Deactivate our plugin.
		add_action( 'admin_init', array( $this, 'deactivate_me' ) );

		// Didn't meet the requirements.
		return false;
	}

	/**
	 * Deactivates this plugin, hook this function on admin_init.
	 *
	 * @since  1.0.6
	 */
	public function deactivate_me() {

		// We do a check for deactivate_plugins before calling it, to protect
		// any developers from accidentally calling it too early and breaking things.
		if ( function_exists( 'deactivate_plugins' ) ) {
			deactivate_plugins( $this->basename );
		}
	}

	/**
	 * Check that all plugin requirements are met.
	 *
	 * @since  1.0.6
	 *
	 * @return boolean True if requirements are met.
	 */
	public function meets_requirements() {

		// Do checks for required classes / functions or similar.
		// Add detailed messages to $this->activation_errors array.
		if ( ! class_exists( 'woocommerce' ) ) {
			$this->activation_errors[] = 'Woocommerce Plugin not install or actiavted!';
			return false;
		}

		if ( class_exists( 'WC_Simple_Waiting_List_Pro' ) ) {
			$this->activation_errors[] = 'Deactivate WC Simple Waiting List Pro Version and try again!';
			return false;
		}

		return true;
	}

	/**
	 * Adds a notice to the dashboard if the plugin requirements are not met.
	 *
	 * @since  1.0.6
	 */
	public function requirements_not_met_notice() {

		// Compile default message.
		$default_message = sprintf( __( 'WC Simple Waiting List is missing requirements and has been <a href="%s">deactivated</a>. Please make sure all requirements are available.', 'wc-simple-waiting-list' ), admin_url( 'plugins.php' ) );

		// Default details to null.
		$details = null;

		// Add details if any exist.
		if ( $this->activation_errors && is_array( $this->activation_errors ) ) {
			$details = '<small>' . implode( '</small><br /><small>', $this->activation_errors ) . '</small>';
		}

		// Output errors.
		?>
		<div id="message" class="error">
			<p><?php echo wp_kses_post( $default_message ); ?></p>
			<?php echo wp_kses_post( $details ); ?>
		</div>
		<?php
	}

	/**
	 * Magic getter for our object.
	 *
	 * @since  1.0.6
	 *
	 * @param  string $field Field to get.
	 * @throws Exception     Throws an exception if the field is invalid.
	 * @return mixed         Value of the field.
	 */
	public function __get( $field ) {
		switch ( $field ) {
			case 'version':
				return self::VERSION;
			case 'basename':
			case 'name':
			case 'url':
			case 'path':
			case 'uploads':
			case 'public':
			case 'admin':
			case 'ajax':
			case 'db':
			case 'feedback':
				return $this->$field;
			default:
				throw new Exception( 'Invalid ' . __CLASS__ . ' property: ' . $field );
		}
	}

	/**
	 * Include a file from the includes directory.
	 *
	 * @since  1.0.6
	 *
	 * @param  string $filename Name of the file to be included.
	 * @return boolean          Result of include call.
	 */
	public static function include_file( $filename ) {
		$file = self::dir( $filename . '.php' );
		if ( file_exists( $file ) ) {
			return include_once( $file );
		}
		return false;
	}

	/**
	 * This plugin's directory.
	 *
	 * @since  1.0.6
	 *
	 * @param  string $path (optional) appended path.
	 * @return string       Directory and path.
	 */
	public static function dir( $path = '' ) {
		static $dir;
		$dir = $dir ? $dir : trailingslashit( dirname( __FILE__ ) );
		return $dir . $path;
	}

	/**
	 * This plugin's url.
	 *
	 * @since  1.0.6
	 *
	 * @param  string $path (optional) appended path.
	 * @return string       URL and path.
	 */
	public static function url( $path = '' ) {
		static $url;
		$url = $url ? $url : trailingslashit( plugin_dir_url( __FILE__ ) );
		return $url . $path;
	}
}

/**
 * Grab the WC_Simple_Waiting_List object and return it.
 * Wrapper for WC_Simple_Waiting_List::get_instance().
 *
 * @since  1.0.6
 * @return WC_Simple_Waiting_List  Singleton instance of plugin class.
 */
function wc_simple_waiting_list() {
	return WC_Simple_Waiting_List::get_instance();
}

// Kick it off.
add_action( 'plugins_loaded', array( wc_simple_waiting_list(), 'hooks' ) );

// Activation and deactivation.
register_activation_hook( __FILE__, array( wc_simple_waiting_list(), '_activate' ) );
register_deactivation_hook( __FILE__, array( wc_simple_waiting_list(), '_deactivate' ) );
