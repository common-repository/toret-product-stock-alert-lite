<?php
/**
 * @package   Toret Product Stock Alert Lite
 * @author    Vladislav Musílek
 * @license   GPL-2.0+
 * @link      https://toret.cz
 * @copyright 2018 Toret
 *
 * Plugin Name:       Toret Product Stock Alert Lite
 * Plugin URI:        https://stock-alert.toret.cz
 * Description:       Display product variations in table
 * Version:           1.0
 * Author:            Vladislav Musílek
 * Author URI:        toret.cz
 * Text Domain:       toret-product-stock-alert
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 * WC requires at least: 3.0.0
 * WC tested up to: 3.3.5
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


define( 'TORETPSADIR', plugin_dir_path( __FILE__ ) );
define( 'TORETPSAURL', plugin_dir_url( __FILE__ ) );
define( 'TORETPSA', 'toret-product-stock-alert');


/*----------------------------------------------------------------------------*
 * Public-Facing Functionality
 *----------------------------------------------------------------------------*/

require_once( plugin_dir_path( __FILE__ ) . 'public/class-toret-product-stock-alert.php' );

register_activation_hook(   __FILE__, array( 'Toret_Product_Stock_Alert', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'Toret_Product_Stock_Alert', 'deactivate' ) );

add_action( 'plugins_loaded', array( 'Toret_Product_Stock_Alert', 'get_instance' ) );

/*----------------------------------------------------------------------------*
 * Dashboard and Administrative Functionality
 *----------------------------------------------------------------------------*/

if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {

	require_once( plugin_dir_path( __FILE__ ) . 'admin/class-toret-product-stock-alert-admin.php' );
	add_action( 'plugins_loaded', array( 'Toret_Product_Stock_Alert_Admin', 'get_instance' ) );

}


function add_stock_alert_admin_email( $email_classes ) {
  
    require_once TORETPSADIR . 'includes/class-wc-stock-alert-customer-email.php';
    require_once TORETPSADIR . 'includes/class-wc-stock-alert-customer-save-email.php';

    $email_classes['WC_Stock_Alert_Customer_Email'] = new WC_Stock_Alert_Customer_Email();
    $email_classes['WC_Stock_Alert_Customer_Save_Email'] = new WC_Stock_Alert_Customer_Save_Email();
 
    return $email_classes;
 
}
add_filter( 'woocommerce_email_classes', 'add_stock_alert_admin_email' );
