<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://imakeplugins.com
 * @since             1.0.0
 * @package           Wc_Simple_Waiting_List
 *
 * @wordpress-plugin
 * Plugin Name:       WC Simple Waiting List
 * Plugin URI:        http://imakeplugins.com/wc-simple-waiting-list/
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.5
 * Author:            Bob Ong
 * Author URI:        http://imakeplugins.com
 * License:           GPL-3.0+
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain:       wc-simple-waiting-list
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! defined( 'WC_SIMPLE_WAITING_LIST_MAIN_PATH' ) ){
	define( 'WC_SIMPLE_WAITING_LIST_MAIN_PATH', plugin_dir_path( __FILE__ )  );
}
/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wc-simple-waiting-list-activator.php
 */
function activate_wc_simple_waiting_list() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wc-simple-waiting-list-activator.php';
	Wc_Simple_Waiting_List_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wc-simple-waiting-list-deactivator.php
 */
function deactivate_wc_simple_waiting_list() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wc-simple-waiting-list-deactivator.php';
	Wc_Simple_Waiting_List_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_wc_simple_waiting_list' );
register_deactivation_hook( __FILE__, 'deactivate_wc_simple_waiting_list' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-wc-simple-waiting-list.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_wc_simple_waiting_list() {

	$plugin = new Wc_Simple_Waiting_List();
	$plugin->run();

}
run_wc_simple_waiting_list();
