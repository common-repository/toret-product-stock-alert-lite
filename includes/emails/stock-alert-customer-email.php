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


?>

<?php do_action('woocommerce_email_header', $email_heading); ?>

<h2><?php _e( 'Product stock alert info', 'toret-product-stock-alert' ); ?></h2>
<?php $link = get_the_permalink( $product->get_id() ); ?>
<p><a href="<?php echo $link; ?>" target="_blank"><?php echo __('Your product is ready to buy', 'toret-product-stock-alert'); ?></a></p>

<?php do_action( 'woocommerce_email_footer' ); ?>
