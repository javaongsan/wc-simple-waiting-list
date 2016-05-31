<?php
/**
 * Version: 1.0.0
 * Author: Bob Ong Swee San
 * Author URI: www.imakewoocommerce.site
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if( ! class_exists( 'wc_simple_waiting_list_form' ) ){
    class wc_simple_waiting_list_form {
        protected $current_product = false;

    	public function __construct() {
            add_action( 'woocommerce_before_single_product', array( $this, 'wc_simple_waiting_list_box' ) );
            
    	}

        public function wc_simple_waiting_list_box(){
            global $post;

            if( get_post_type( $post->ID ) == 'product' && is_product() ) {

                $this->current_product = wc_get_product( $post->ID );

                if ( $this->current_product->product_type == 'grouped' ) {
                    return;
                }

                if( $this->current_product->product_type == 'variable' ){
                    add_action( 'woocommerce_stock_html', array( $this, 'wc_simple_waiting_list_box_details' ), 20, 3 );
                }
                else {
                    add_action( 'woocommerce_stock_html', array( $this, 'wc_simple_waiting_list_box_details' ), 20, 2 );
                }
            }
        }

        public function wc_simple_waiting_list_register($user_email, $product_id) {
            $waiting_list = get_post_meta( $product_id, WC_SIMPLE_WAITING_LIST_PLUGIN_META, true );
            if ( ! is_email( $user_email ) || (! empty($waiting_list) && ! is_array( $waiting_list ) )){
                return false;
            }

            if  ( ! empty($waiting_list) && in_array( $user_email, $waiting_list ) ) {
                return true;
            }

            $waiting_list[] = $user_email;
            update_post_meta($product_id, WC_SIMPLE_WAITING_LIST_PLUGIN_META, $waiting_list);
            return true;
        }

        public function wc_simple_waiting_list_deregister($user_email, $product_id) {
            $waiting_list = get_post_meta( $product_id, WC_SIMPLE_WAITING_LIST_PLUGIN_META, true );
            if ( ! is_email( $user_email ) || (! empty($waiting_list) && ! is_array( $waiting_list ) )){
                return false;
            }

            if  ( ! empty($waiting_list) && in_array( $user_email, $waiting_list ) ) {
                return true;
            }

            $new_waiting_list = array_diff($waiting_list, array($user_email));

            update_post_meta($product_id, WC_SIMPLE_WAITING_LIST_PLUGIN_META, $new_waiting_list);
            return true;
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
            $product_type   = $_product->product_type;
            $product_id     = ( $product_type == 'simple' ) ? $_product->id : $_product->variation_id;
           
            $url            = ( $product_type == 'simple' ) ? get_permalink( $_product->id ) : get_permalink( $_product->parent->id );
            ?>

            <div class="wrap">
                

                    <?php
                    if ( ($user->exists() || isset($_POST['emailaddr'])) && isset($_POST['save'])  )
                    {       
                        $user_email = ( isset( $_POST[ 'emailaddr' ] ) ) ? $_POST[ 'emailaddr' ] : $user->user_email;
                        $result = $this->wc_simple_waiting_list_register( $user_email, $product_id );
                        if ($result){

                            ?>
                             <form id="mywaitlistform" method="post" action="" class="form" >
                                <input type="hidden" name="emailaddr" id="emailaddr" value=" <?php echo $user_email ?>" placeholder="You email address" /><br /><br />
                                <input type="submit" name="remove" value="Leave Waiting List">
                            </form>
                            <?php
                        }
                    }
                    else {
                        if ( ($user->exists() || isset($_POST['emailaddr'])) && isset($_POST['remove'])  )
                        {
                            $user_email = ( isset( $_POST[ 'emailaddr' ] ) ) ? $_POST[ 'emailaddr' ] : $user->user_email;
                            $result = $this->wc_simple_waiting_list_deregister( $user_email, $product_id );
                        }
                        ?>
                            <form id="mywaitlistform" method="post" action="" class="form" >
                                <?php if (! $user->exists() ) { ?>
                                <input type="email" name="emailaddr" id="emailaddr" value="" placeholder="You email address" /><br /><br />
                                <?php } ?>
                                <input type="submit" name="save" value="Join Waiting List">
                            </form>
                <?php
                    }
                    ?>
            </div>

            <?php
        }
        
    }
}

return new wc_simple_waiting_list_form();
?>