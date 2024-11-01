<?php
/**
 * @package   Toret Product Stock Alert
 * @author    Vladislav Musílek
 * @license   GPL-2.0+
 * @link      https://toret.cz
 * @copyright 2018 Toret
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

echo "= " . $email_heading . " =\n\n";


echo __( 'Hello!', 'toret-product-stock-alert' ); 
echo __( 'Product stock alert is ready!', 'toret-product-stock-alert' );
echo __( 'Your product to alert is:', 'toret-product-stock-alert') . ' ' .$product->get_name();
echo __( 'When product will available, we will inform you.', 'toret-product-stock-alert');

echo apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) );
