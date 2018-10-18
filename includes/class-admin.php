<?php
/**
 * WC Simple Waiting List Admin.
 *
 * @since   1.0.6
 * @package WC_Simple_Waiting_List
 */

/**
 * WC Simple Waiting List Admin.
 *
 * @since 1.0.6
 */
class WCSWL_Admin {
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
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'admin_menu', array( $this, 'add_menu' ) );
		add_action( 'wp_dashboard_setup', array( $this, 'add_wc_simple_waiting_list_dashboard_widgets' ) );
		add_action( 'woocommerce_product_set_stock_status', array( $this, 'wc_simple_waiting_list_email_trigger' ) );
		add_filter( 'woocommerce_email_classes', array( $this, 'wc_simple_waiting_list_class' ) );
		add_filter( 'woocommerce_email_actions', array( $this, 'wc_simple_waiting_list_email_actions' ) );
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since   1.0.6
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
		wp_enqueue_style( $this->plugin->__get( 'name' ), $this->plugin->__get( 'url' ) . '/assets/css/wc-simple-waiting-list.min.css', array(), $this->plugin->__get( 'version' ), 'all' );
		wp_enqueue_style( 'font-awesome', $this->plugin->__get( 'url' ) . '/assets/css/font-awesome.min.css', array(), $this->plugin->__get( 'version' ), 'all' );
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.6
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

		wp_enqueue_script( $this->plugin->__get( 'name' ), $this->plugin->__get( 'url' ) . '/assets/js/wc-simple-waiting-list.min.js', array( 'jquery' ), $this->plugin->__get( 'version' ), false );
		wp_localize_script( $this->plugin->__get( 'name' ), 'wc_simple_waiting_list_vars',
			array(
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
				'nonce' => wp_create_nonce( 'wc-simple-waiting-list-nonce' ),
				'already_inserted_message' => __( 'You have already added this item.', 'wc-simple-waiting-list' ),
				'error_message' => __( 'Sorry, there was a problem processing your request.', 'wc-simple-waiting-list' ),
			)
		);
	}

	public function add_menu() {
		add_menu_page(
			__( 'Waiting List', 'wc-simple-waiting-list' ),
			'Waiting List',
			'manage_options',
			'wc-simple-waiting-list',
			array(
				$this,
				'wc_simple_waiting_list_page',
			)
		);
	}

	public function wc_simple_waiting_list_page() {
		$results = $this->plugin->db->get_user_reminders();
		$header_columns = $this->header_columns();
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Waiting List', 'wc-simple-waiting-list' ); ?></h1>
			<p><a href='#/' class='wcswl-export-reminders button' > Export </a></p>
			<div id="reminders">
				<table class="shop_table shop_table_responsive">
					<thead>
						<tr>
							<?php foreach ( $header_columns as $column_id => $column_name ) : ?>
								<th class="<?php echo esc_attr( $column_id ); ?>"><span class="nobr"><?php echo esc_html( $column_name ); ?></span></th>
							<?php endforeach; ?>
						</tr>
					</thead>
					<tbody>
						<?php
						foreach ( $results as $data ) {
							$product = new WC_product( $data->product_id );
							echo '<tr><td>';
							echo $product->get_name();
							echo '</td><td>';
							echo $data->email;
							echo '</td><td>';
							echo $data->created_date;
							echo '</td></tr>';
						}
						?>
						</tbody>
				</table>
			<div id='Results'></div>
			</div>
		</div>
	<?php
	}

	/**
	 * Function to add dashboard wigdet.
	 *
	 * @since  1.0.6
	 */
	public function add_wc_simple_waiting_list_dashboard_widgets() {
		wp_add_dashboard_widget( 'dashboard_widget', 'Simple Waiting List', array( $this, 'wc_simple_waiting_list_dashboard' ) );
	}

	/**
	 * Function to display dashboard widget
	 *
	 * @since  1.0.6
	 */
	public function wc_simple_waiting_list_dashboard() {
		$results = $this->plugin->db->get_product_count();
		if ( ! empty( $results ) ) {
			$path = 'admin.php?page=wc-simple-waiting-list';
			$url = admin_url( $path );
			$link = "<a href='{$url}'>View Details</a>";
			$output = $results . ' product have a waiting list <br />' . $link;
		} else {
			$output = '<li>' . __( 'N/A', 'wc-simple-waiting-list' ) . '</li>' . "\n";
		}
		echo $output;
	}

	public function wc_simple_waiting_list_class( $emails ) {
		require_once( 'class-email.php' );
		$emails['Wc_Simple_Waiting_List_Email'] = new WCSWL_Email();
		return $emails;
	}

	public function wc_simple_waiting_list_email_actions( $email_actions ) {
		$email_actions[] = 'class_wc_simple_waiting_list_email_send';
		return $email_actions;
	}
	/**
	 * Export.
	 *
	 * @since  1.0.6
	 */
	public function export_reminders() {
		$results = $this->plugin->db->get_user_reminders();
		unset( $results->id );
		$filename = '/wc-simple-waiting-list-' . date( 'Y-m-d-h:i:s', current_time( 'timestamp' ) ) . '.csv';
		$upload_dir = $this->plugin->__get( 'uploads' );
		$download = $upload_dir['url'] . $filename;
		$data = json_decode( json_encode( $results ), true );
		$this->generatecsv( $upload_dir['path'] . $filename, $data, $this->header_columns() );
		return $download;
	}

	/**
	 * Generate CSV.
	 *
	 * @since  1.0.6
	 */
	public function generatecsv( $filename, $data, $header = array(), $delimiter = ',', $enclosure = '"' ) {
		$fp = fopen( $filename, 'w' );
		fputcsv( $fp, $header, $delimiter, $enclosure );
		foreach ( $data as $row ) {
			fputcsv( $fp, $row, $delimiter, $enclosure );
		}
		fclose( $fp );
	}

	/**
	 * Get headers columns.
	 *
	 * @since  1.0.6
	 */
	public function header_columns() {
		$wc_list_columns = apply_filters( 'woocommerce_wc_list_columns', array(
			'wc-list-product'  => __( 'Product', 'wc-simple-waiting-list' ),
			'wc-list-emails'    => __( 'Emails', 'wc-simple-waiting-list' ),
			'wc-list-created_date'    => __( 'Date', 'wc-simple-waiting-list' ),
		) );
		return $wc_list_columns;
	}

	public function wc_simple_waiting_list_email_trigger( $product_id ) {
		$product = wc_get_product( $product_id );
		if ( ! $product->managing_stock() && ! $product->is_in_stock() ) {
			return;
		}

		$waiting_list = $this->plugin->db->get_emails_by_product_id( $product_id );
		foreach ( $waiting_list as $item ) {
			do_action( 'class_wc_simple_waiting_list_email_send', $product_id,  $item->email );
		}
		$this->plugin->db->del_emails_by_product_id( $product_id );
	}
}
