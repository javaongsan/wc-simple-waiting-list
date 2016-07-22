<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://imakeplugins.com
 * @since      1.0.0
 *
 * @package    Wc_Simple_Waiting_List
 * @subpackage Wc_Simple_Waiting_List/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Wc_Simple_Waiting_List
 * @subpackage Wc_Simple_Waiting_List/admin
 * @author     Bob Ong <ongsweesan@gmail.com>
 */
class Wc_Simple_Waiting_List_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	private $metakey;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version, $metakey ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->metakey = $metakey;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wc_Simple_Waiting_List_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wc_Simple_Waiting_List_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wc-simple-waiting-list-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wc_Simple_Waiting_List_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wc_Simple_Waiting_List_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wc-simple-waiting-list-admin.js', array( 'jquery' ), $this->version, false );

	}

	public function wc_simple_waiting_list_class( $emails ) {
		require_once( 'class-wc-simple-waiting-list-email.php' );
		$emails['Wc_Simple_Waiting_List_Email'] =  new Wc_Simple_Waiting_List_Email();
		return $emails;
	}
	
	public function wc_simple_waiting_list_mailer() {
		add_action( 'class_wc_simple_waiting_list_email_send', array( 'WC_Emails', 'send_transactional_email' ), 10, 2 );
	}

	public function wc_simple_waiting_list_widgets() {	
		wp_add_dashboard_widget( 'wc-simple-waiting-list', 'Waiting List', array( $this, 'wc_simple_waiting_list_dashboard' ) );
	}

	public function wc_simple_waiting_list_dashboard() {
        $results = $this->get_meta_count( $this->metakey );
        if (! empty($results))
        {
            $path = 'admin.php?page=wc-simple-waiting-list/wc-simple-waiting-list.php';
            $url = admin_url($path);
            $link = "<a href='{$url}'>View Details</a>";
            $output = $results . ' product have a waiting list <br />' .$link;
        }
        else
            $output = '<li>'.__('N/A', 'wc-simple-waiting-list').'</li>'."\n";
       
        echo $output;
    }

    public function wc_simple_waiting_list_menu() {
        add_menu_page(
                    __( 'Waiting List', 'wc-simple-waiting-list' ),
                    'Waiting List',
                    'manage_options',
                    'wc-simple-waiting-list/wc-simple-waiting-list.php',
                    array(
                            $this,
                            'wc_simple_waiting_list_page'
                        )
                );
    }

    public function get_meta_values( $key = '') {
        global $wpdb;

        if( empty( $key ) )
            return;

        $r = $wpdb->get_results( $wpdb->prepare( "
            SELECT pm.post_id as ID, pm.meta_value as Value FROM {$wpdb->postmeta} pm
            WHERE pm.meta_key = '%s' ", $key) );

        return $r;
    }

    public function get_meta_count( $key = '') {
        global $wpdb;

        if( empty( $key ) )
            return;

        $r = $wpdb->get_var( $wpdb->prepare( "
            SELECT count(*) as counts FROM {$wpdb->postmeta} pm
            WHERE pm.meta_key = '%s' ", $key) );

        return $r;
    }

    public function wc_simple_waiting_list_email_trigger( $product_id ) {
        $product = wc_get_product(  $product_id );
        if ( ! $product->managing_stock() && ! $product->is_in_stock() ) {
            return;
        }

        $waiting_list = get_post_meta( $product_id, $this->metakey, true );
        if (  empty($waiting_list) || ! is_array( $waiting_list ) ) {
            return;
        }

        if( is_array( $waiting_list ) ) {
            foreach( $waiting_list as $key => $user_email ) {
                do_action('class_wc_simple_waiting_list_email_send', $product_id,  $user_email);
            }
            $cleaned = delete_post_meta( $product_id, $this->metakey );
        }
    }
        
	public function wc_simple_waiting_list_page() {
	    $results = $this->get_meta_values( $this->metakey );
	    ?>
	    <div class="wrap">
			<h1><?php _e( 'Waiting List', 'wc-simple-waiting-list' ); ?></h1>
			<br class="clear" />
			<div id="reminders">
				<table class="shop_table shop_table_responsive">
					<thead>
						<tr>
							<th scope="col"><?php _e( 'Product', 'wc-simple-waiting-list' ); ?></th>
							<th scope="col"><?php _e( 'No. of People Joined', 'wc-simple-waiting-list' ); ?></th>
							<th scope="col"><?php _e( 'Emails', 'wc-simple-waiting-list' ); ?></th>
						</tr>
					</thead>
					<tbody>
					     <?php
					     foreach ($results as $data) {
					         $product = new WC_product($data->ID);
					           echo '<tr><td>';
					           echo $product->post->post_title;
					           echo '</td><td>';
					           echo  count(unserialize($data->Value)); 
					           echo '</td><td>';
					           foreach (unserialize($data->Value) as $emails) {
					           	echo $emails . '<br>'; 
					           }
					           echo '</td></tr>';
					     }
					    ?>
						</tbody>
				</table>
			</div>
		</div>
	<?php
	}
}
