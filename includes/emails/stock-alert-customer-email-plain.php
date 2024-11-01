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


echo __( 'Product stock alert info', 'toret-product-stock-alert' );
$link = get_the_permalink( $product->get_id() );
echo __('Your product is ready to buy', 'toret-product-stock-alert');

echo apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) );
