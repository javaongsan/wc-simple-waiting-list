<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       http://imakeplugins.com
 * @since      1.0.0
 *
 * @package    Wc_Simple_Waiting_List
 * @subpackage Wc_Simple_Waiting_List/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Wc_Simple_Waiting_List
 * @subpackage Wc_Simple_Waiting_List/includes
 * @author     Bob Ong <ongsweesan@gmail.com>
 */
class Wc_Simple_Waiting_List_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'wc-simple-waiting-list',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
