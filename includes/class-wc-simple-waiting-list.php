<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://imakeplugins.com
 * @since      1.0.0
 *
 * @package    Wc_Simple_Waiting_List
 * @subpackage Wc_Simple_Waiting_List/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Wc_Simple_Waiting_List
 * @subpackage Wc_Simple_Waiting_List/includes
 * @author     Bob Ong <ongsweesan@gmail.com>
 */
class Wc_Simple_Waiting_List {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Wc_Simple_Waiting_List_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	protected $metakey;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->plugin_name = 'wc-simple-waiting-list';
		$this->version = '1.0.0';
		$this->metakey = 'wc_simple_waiting_list_meta_key';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Wc_Simple_Waiting_List_Loader. Orchestrates the hooks of the plugin.
	 * - Wc_Simple_Waiting_List_i18n. Defines internationalization functionality.
	 * - Wc_Simple_Waiting_List_Admin. Defines all hooks for the admin area.
	 * - Wc_Simple_Waiting_List_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wc-simple-waiting-list-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wc-simple-waiting-list-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wc-simple-waiting-list-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-wc-simple-waiting-list-public.php';


		$this->loader = new Wc_Simple_Waiting_List_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Wc_Simple_Waiting_List_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Wc_Simple_Waiting_List_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Wc_Simple_Waiting_List_Admin( $this->get_plugin_name(), $this->get_version(), $this->metakey);

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'woocommerce_product_set_stock_status', $plugin_admin, 'wc_simple_waiting_list_email_trigger' );

		$this->loader->add_action( 'woocommerce_init', $plugin_admin, 'wc_simple_waiting_list_mailer' );
		$this->loader->add_filter( 'woocommerce_email_classes', $plugin_admin, 'wc_simple_waiting_list_class');

		$this->loader->add_action( 'wp_dashboard_setup',  $plugin_admin, 'wc_simple_waiting_list_widgets' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'wc_simple_waiting_list_menu' );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Wc_Simple_Waiting_List_Public( $this->get_plugin_name(), $this->get_version(), $this->metakey);

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		$this->loader->add_action( 'wp_ajax_wc_simple_waiting_list_user_add', 			$plugin_public, 'wc_simple_waiting_list_add_user' );
		$this->loader->add_action( 'wp_ajax_nopriv_wc_simple_waiting_list_user_add', 	$plugin_public, 'wc_simple_waiting_list_add_user' );
		$this->loader->add_action( 'wp_ajax_wc_simple_waiting_list_user_del', 			$plugin_public, 'wc_simple_waiting_list_del_user' );
		$this->loader->add_action( 'wp_ajax_nopriv_wc_simple_waiting_list_user_del', 	$plugin_public, 'wc_simple_waiting_list_del_user' );
		$this->loader->add_action( 'woocommerce_before_single_product', $plugin_public, 'wc_simple_waiting_list_box' );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Wc_Simple_Waiting_List_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
