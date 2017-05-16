<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://imakeplugins.com
 * @since      1.0.0
 *
 * @package    Wc_Simple_Waiting_List
 * @subpackage Wc_Simple_Waiting_List/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Wc_Simple_Waiting_List
 * @subpackage Wc_Simple_Waiting_List/public
 * @author     Bob Ong <ongsweesan@gmail.com>
 */
class Wc_Simple_Waiting_List_Public {

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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version, $metakey ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->metakey = $metakey;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wc-simple-waiting-list-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wc-simple-waiting-list-public.js', array( 'jquery' ), $this->version, false );
        wp_localize_script( $this->plugin_name, 'wc_simple_waiting_list_vars', 
                array( 
                    'ajaxurl' => admin_url( 'admin-ajax.php' ),
                    'nonce' => wp_create_nonce('wc-simple-waiting-list-nonce'),
                    'already_inserted_message' => __('You are already on the list.', 'wc_simple_waiting_list'),
                    'error_message' => __('Sorry, there was a problem processing your request.', 'wc_simple_waiting_list')
                ) 
            );  
	}

	public function wc_simple_waiting_list_box(){
        global $post;

        if( get_post_type( $post->ID ) == 'product' && is_product() ) {

            $this->current_product = wc_get_product( $post->ID );

            if ( $this->current_product->get_type() == 'grouped' ) {
                return;
            }

            if( $this->current_product->get_type() == 'variable' ){
                add_action( 'woocommerce_get_stock_html', array( $this, 'wc_simple_waiting_list_box_details' ), 20, 3 );
            }
            else {
                add_action( 'woocommerce_get_stock_html', array( $this, 'wc_simple_waiting_list_box_details' ), 20, 2 );
            }
        }
    }

    public function wc_simple_waiting_list_isregister($user_email, $product_id) {
        $waiting_list = get_post_meta( $product_id, $this->metakey, true );
        if ( empty($waiting_list) || !is_array( $waiting_list ) ){
            return false;
        }

        if  ( ! in_array( $user_email, $waiting_list ) ) {
            return false;
        }

        return true;
    }

    public function wc_simple_waiting_list_register($user_email, $product_id) {
        $waiting_list = get_post_meta( $product_id, $this->metakey, true );
        if ( ! is_email( $user_email ) || (! empty($waiting_list) && ! is_array( $waiting_list ) )){
            return false;
        }

        if  ( ! empty($waiting_list) && in_array( $user_email, $waiting_list ) ) {
            return true;
        }
        $waiting_list[] = $user_email;
        update_post_meta($product_id, $this->metakey, $waiting_list);

        return true;
    }

    public function wc_simple_waiting_list_deregister($user_email, $product_id) {
        $waiting_list = get_post_meta( $product_id, $this->metakey, true );
        if ( ! is_email( $user_email ) || (! empty($waiting_list) && ! is_array( $waiting_list ) )){
            return false;
        }

        if  ( ! in_array( $user_email, $waiting_list ) ) {
            return true;
        }
        $new_waiting_list = array_diff($waiting_list, array($user_email));
        update_post_meta($product_id, $this->metakey, $new_waiting_list);
        return true;
    }

    public function wc_simple_waiting_list_add_user(){
        if ( isset( $_POST['user_email'] ) &&  isset( $_POST['product_id'] ) && wp_verify_nonce($_POST['wc_simple_waiting_list_nonce'], 'wc-simple-waiting-list-nonce') ) {
            if ($this->wc_simple_waiting_list_register( $_POST['user_email'],  $_POST['product_id'] )) 
                echo 'success';
            else
                echo 'fail';
        }
        die();
    }

    public function wc_simple_waiting_list_del_user(){
        if ( isset( $_POST['user_email'] ) &&  isset( $_POST['product_id'] ) && wp_verify_nonce($_POST['wc_simple_waiting_list_nonce'], 'wc-simple-waiting-list-nonce') ) {
            if ($this->wc_simple_waiting_list_deregister( $_POST['user_email'],  $_POST['product_id'] )) 
                echo 'success';
            else
                echo 'fail';
        }
        die();
    }


    public function wc_simple_waiting_list_box_details($html, $availability, $_product = false ) {
        global $product;

        if( ! $_product ) {
            $_product = $this->current_product;
        }

        if( $_product->is_in_stock() ) {
            return $html;
        }

        $user           = wp_get_current_user();
        $product_type   = $_product->get_type();
        $product_id     = ( $product_type == 'simple' ) ? $_product->get_id() : $_product->variation_id;
        $box = '<div class="wrap">';
        $addstyle = "display:none";
        $removestyle = "display:none";
        if ($this->wc_simple_waiting_list_isregister( $user->user_email, $product_id ))
            $removestyle = "display:block";
        else
            $addstyle = "display:block";

        $box .='<input type="submit" name="remove" id="remove" style="'. $removestyle . '" data-user-email="'.  $user->user_email . '" data-product-id="'. $product_id . '" value="Leave Waiting List">';
        if ( ! $user->exists() )
            $box .= '<input type="text" name="emailaddr" id="emailaddr" style="'. $addstyle . '" placeholder="You email address" /><br /><br />';
        $box .= '<input type="submit" name="save" id="save" style="'. $addstyle . '" data-user-email="'. $user->user_email . '" data-product-id="'. $product_id . '" value="Join Waiting List">';
        $box .= '</div>';
        echo $box;
    }

}
