<?php
/**
 * WC Simple Waiting List Public.
 *
 * @since   1.0.6
 * @package WC_Simple_Waiting_List
 */

/**
 * WC Simple Waiting List Public.
 *
 * @since 1.0.6
 */
class WCSWL_Public {
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
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'woocommerce_before_single_product', array( $this, 'wc_simple_waiting_list_box' ) );
	}

	/**
	 * Register the JavaScript for the public area.
	 *
	 * @since    1.0.6
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( $this->plugin->__get( 'name' ) . 'public' , $this->plugin->__get( 'url' ) . '/assets/js/wc-simple-waiting-list-public.min.js', array( 'jquery' ), $this->plugin->__get( 'version' ), false );
		wp_localize_script( $this->plugin->__get( 'name' ) . 'public', 'wc_simple_waiting_list_public_vars',
			array(
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
				'nonce' => wp_create_nonce( 'wc-simple-waiting-list-public-nonce' ),
				'already_inserted_message' => __( 'You have already added this item.', 'wc-simple-waiting-list' ),
				'error_message' => __( 'Sorry, there was a problem processing your request.', 'wc-simple-waiting-list' ),
			)
		);
	}

	/**
	 * Register the stylesheets for the public area.
	 *
	 * @since    1.0.6
	 */
	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin->__get( 'name' ), $this->plugin->__get( 'url' ) . '/assets/css/wc-simple-waiting-list-public.min.css', array(), $this->plugin->__get( 'version' ), 'all' );
		wp_enqueue_style( 'font-awesome', $this->plugin->__get( 'url' ) . '/assets/css/font-awesome.min.css', array(), $this->plugin->__get( 'version' ), 'all' );
	}

	/**
	 * Create an email optin.
	 *
	 * @since    1.0.6
	 */
	public function wc_simple_waiting_list_box() {
		global $post;

		if ( get_post_type( $post->ID ) == 'product' && is_product() ) {

			$this->current_product = wc_get_product( $post->ID );

			if ( $this->current_product->get_type() == 'grouped' ) {
				return;
			}

			if ( $this->current_product->get_type() == 'variable' ) {
				add_action( 'woocommerce_get_stock_html', array( $this, 'wc_simple_waiting_list_box_details' ), 20, 3 );
			} else {
				add_action( 'woocommerce_get_stock_html', array( $this, 'wc_simple_waiting_list_box_details' ), 20, 2 );
			}
		}
	}

	/**
	 * Email optin.
	 *
	 * @since    1.0.6
	 */
	public function wc_simple_waiting_list_box_details( $html, $availability, $_product = false ) {
		global $product;

		if ( ! $_product ) {
			$_product = $this->current_product;
		}

		if ( $_product->is_in_stock() ) {
			return $html;
		}

		$user           = wp_get_current_user();
		$product_type   = $_product->get_type();
		$product_id     = ( $product_type == 'simple' ) ? $_product->get_id() : $_product->variation_id;
		$box = '<div class="wrap">';
		$addstyle = 'display:none';
		$removestyle = 'display:none';
		if ( $this->plugin->db->is_register( $user->user_email, $product_id ) ) {
			$removestyle = 'display:block';
		} else {
			$addstyle = 'display:block';
		}

		$box .= '<input type="submit" name="deregister_user" id="deregister_user" style="' . $removestyle . '" data-user-email="' . $user->user_email . '" data-product-id="' . $product_id . '" value="Leave Waiting List">';
		if ( ! $user->exists() ) {
			$box .= '<input type="text" name="emailaddr" id="emailaddr" style="' . $addstyle . '" placeholder="Your email address" /><br /><br />';
		}
		$box .= '<input type="submit" name="register_user" id="register_user" style="' . $addstyle . '" data-user-email="' . $user->user_email . '" data-product-id="' . $product_id . '" value="Join Waiting List">';
		$box .= '</div>';
		return $html . $box;
	}

	/**
	 * Register User.
	 *
	 * @since   1.0.6
	 */
	public function wc_simple_waiting_list_register( $user_email, $product_id ) {
		return $this->plugin->db->register_user( $user_email, $product_id );
	}

	/**
	 * Deregister User.
	 *
	 * @since   1.0.6
	 */
	public function wc_simple_waiting_list_deregister( $user_email, $product_id ) {
		return $this->plugin->db->deregister_user( $user_email, $product_id );
	}
}
