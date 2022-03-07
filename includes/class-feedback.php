<?php
/**
 * WC Simple Waiting List Feedback.
 *
 * @since   1.0.11
 * @package WC_Simple_Waiting_List
 */

/**
 * WC Simple Waiting List Feedback.
 *
 * @since 1.0.11
 */
class WCSWL_Feedback {
	/**
	 * Parent plugin class.
	 *
	 * @since 1.0.11
	 *
	 * @var   WC_Simple_Waiting_List
	 */
	protected $plugin = null;

	/**
	 * Constructor.
	 *
	 * @since  1.0.11
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
	 * @since  1.0.11
	 */
	public function hooks() {
		add_action( 'wp_mail_content_type', array( $this, 'set_content_type' ) );
	}

	public function page_display() {
		?>
		<div id="wswl-feedback">
			<?php
			$this->pro();
			$this->review();
			$this->feedback();
			?>
		</div>
	}

	public function pro() {
		?>
		<section class="container">
			<section class="one">
				<h1><?php esc_html_e( 'Get Premium', 'wc-simple-waiting-list' ); ?></h1>
				<div>
					<h2><?php esc_html_e( 'Get our Premium version to unlock more features', 'wc-simple-waiting-list' ); ?></h2>
					<p><span>✓</span><?php esc_html_e( 'Enable SMS.' ); ?></p>
					<a href="https://imakeplugins.com/<?php esc_html_e( $this->plugin->name ); ?>-pro" class='button btn'> Upgrade Now <i class='fa fa-buy'></i></a>
				</div>
			</section>
		</section>
		<?php
	}

	public function review() {
		?>
		<section class="container">
			<section class="one">
				<h1><?php esc_html_e( 'Review', 'wc-simple-waiting-list' ); ?></h1>
				<div>
					<h2><?php esc_html_e( 'Leave us a review?', 'wc-simple-waiting-list' ); ?></h2>
					<a href="https://wordpress.org/support/plugin/<?php esc_html_e( $this->plugin->name ); ?>/reviews/?filter=5#new-post" target="_blank" class="wc-simple-waiting-list-review"><?php esc_html_e('Ok, you deserved it', 'wc-simple-waiting-list'); ?></a>
					<p><?php esc_html_e( 'If you like Simple Waiting List please leave us a review.' ); ?></p>
					<p><?php esc_html_e( 'A huge thanks in advance! We really appreciate this!', 'wc-simple-waiting-list' ); ?></p>
				</div>
			</section>
			<section class="two">
				<h3>
				  Thank you for your review!
				</h3>
				<div class="close"> 
				</div>
			</section>
		</section>
		<?php
	}

	public function feedback() {
		$user = wp_get_current_user();
		?>
		<section class="container">
			<section class="one">
				<h1><?php esc_html_e( 'Feedback', 'wc-simple-waiting-list' ); ?></h1>
				<h2><?php esc_html_e( 'Propose a feature or report a bug', 'wc-simple-waiting-list' ); ?></h2>
				 <form>
					<textarea name='wc-simple-waiting-list-feature-request-msg' id='wc-simple-waiting-list-feature-request-msg' placeholder="<?php __( 'Feature Description', 'wc-simple-waiting-list' ); ?>"></textarea>
					<button name='wc-simple-waiting-list-feature-request-submit' id='wc-simple-waiting-list-feature-request-submit' class='button btn' data-name="<?php esc_html_e( $user->display_name ); ?>" data-email="<?php esc_html_e( $user->user_email ); ?>" data-blog="<?php esc_html_e(get_bloginfo( 'name' ) ) ?>" data-plugin="<?php esc_html_e( $this->plugin->name ); ?>" type='submit'> Send <i class='fa fa-send'></i></button>
				</form>
			</section>
			<section class="two">
				<h3>
				  Thank you for your feedback!
				</h3>
				<div class="close"> 
				</div>
			</section>
		</section>
		<?php
	}

	public function send_email( $data ) {
		try {
			$to = 'ongsweesan@gmail.com';
			$headers = array();
			$headers[] = "From: {$data['blog']}<{$data['email']}>";

			$subject = 'Feedback: ' . $data['plugin'];
			$message = sprintf( __( 'Created: %s<br/>From: %s<br/>blog: %s<br />Message:%s', 'wc-simple-waiting-list' ), date( 'Y-m-d-h:i:s', current_time( 'timestamp' ) ), $data['name'], $data['blog'],htmlspecialchars_decode( $data['msg'] ) );
			$email_result = wp_mail( $to, $subject, $message, $headers );
		} catch ( Exception $e ) {
			echo $e->message;
			return false;
		}
		return true;
	}

	public function set_content_type() {
		return 'text/html';
	}


	public function update_review() {
		return update_option( 'wswl_review', true );
	}

	public function feature_request( $data ) {
		return $this->send_email( $data );
	}
}
