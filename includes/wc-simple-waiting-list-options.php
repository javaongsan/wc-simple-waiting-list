<?php
/**
 * Version: 1.0.0
 * Author: Bob Ong Swee San
 * Author URI: www.imakewoocommerce.site
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if( ! class_exists( 'wc_simple_waiting_list_options' ) ){
    class wc_simple_waiting_list_options {
    	
        public function __construct() {
            add_action( 'wp_dashboard_setup', array( $this, 'wc_simple_waiting_list_widgets' ) );
    		add_action( 'admin_menu', array( $this, 'wc_simple_waiting_list_menu' ) );
            add_action( 'woocommerce_product_set_stock_status', array( __CLASS__, 'wc_simple_waiting_list_email_trigger' ) );
    	}

    	public function wc_simple_waiting_list_widgets() {	
    		wp_add_dashboard_widget( 'wc-simple-waiting-list', 'Waiting List', array( $this, 'wc_simple_waiting_list_dashboard' ) );
    	}
    	
    	public function wc_simple_waiting_list_dashboard() {
            $results = $this->get_meta_count( WC_SIMPLE_WAITING_LIST_PLUGIN_META );
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

        public function wc_simple_waiting_list_page() {
             $results = $this->get_meta_values( WC_SIMPLE_WAITING_LIST_PLUGIN_META );
             ?>
             <style type="text/css">
                #divTable
                {
                    display:  table;
                }
                #divRowHeader
                {
                    display: table-row;
                    font-weight: bold;
                    text-align: center;
                   background: #999;
                }    
                #divRow
                {
                    display: table-row;
                }

                #divCell
                {
                    display: table-cell;
                    border: solid;
                    border-width: thin;
                    padding-left: 5px;
                    padding-right: 5px;
                }
            </style>
             <div class="wrap">
                <h2><?php esc_html_e('Waiting List', 'wc-simple-waiting-list') ?></h2>
             <div id="divTable">
                <div id="divRowHeader">
                     <div id="divCell">
                        Product
                     </div>
                      <div id="divCell">
                     No. of People on Waiting List</td></tr>
                      </div>
                </div>
             <?php
             foreach ($results as $data) {
                 $product = new WC_product($data->ID);
                   echo '<div id="divRow">
                     <div id="divCell">';
                   echo $product->post->post_title;
                   echo '</div>
                      <div id="divCell">';
                   echo  count(unserialize($data->Value)); 
                   echo '</div>
                </div>';
             }
            ?>
            </table>
            
               
            </div>
            <?php
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

        public static function wc_simple_waiting_list_email_trigger( $product_id ) {
            $product = wc_get_product(  $product_id );
            if ( ! $product->managing_stock() && ! $product->is_in_stock() ) {
                return;
            }

            $waiting_list = get_post_meta( $product_id, WC_SIMPLE_WAITING_LIST_PLUGIN_META, true );
            if (  empty($waiting_list) || ! is_array( $waiting_list ) ) {
                return;
            }

            if( is_array( $waiting_list ) ) {
                foreach( $waiting_list as $key => $user_email ) {
                    do_action( 'wc_simple_waiting_list_email_send', $product_id,  $user_email);
                }
                $cleaned = delete_post_meta( $product_id, WC_SIMPLE_WAITING_LIST_PLUGIN_META );
            }
            
        }


    }
}

return new wc_simple_waiting_list_options();
?>