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

<h2><?php _e( 'Hello!', 'toret-product-stock-alert' ); ?></h2>
<h3><?php _e( 'Product stock alert is ready!', 'toret-product-stock-alert' ); ?></h3>
<?php $link = get_the_permalink( $product->get_id() ); ?>
<p><?php _e( 'Your product to alert is:', 'toret-product-stock-alert'); ?> <a href="<?php echo $link; ?>" target="_blank"><?php echo $product->get_name(); ?></a></p>
<p><?php _e( 'When product will available, we will inform you.', 'toret-product-stock-alert'); ?></p>

<?php do_action( 'woocommerce_email_footer' ); ?>
