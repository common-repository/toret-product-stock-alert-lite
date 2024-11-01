<?php
/**
 * @package   Toret Product Stock Alert
 * @author    Vladislav Musílek
 * @license   GPL-2.0+
 * @link      http://toret.cz
 * @copyright 2018 Toret
 */


if( isset( $_POST['update'] ) ){ 

    $option = array();

    if( !empty( $_POST['product-button-text'] ) ){ $option['product-button-text'] = sanitize_text_field( $_POST['product-button-text'] ); }
    if( !empty( $_POST['popup-header-text'] ) ){ $option['popup-header-text'] = sanitize_text_field( $_POST['popup-header-text'] ); }
    if( !empty( $_POST['button-text'] ) ){ $option['button-text'] = sanitize_text_field( $_POST['button-text'] ); }
    if( !empty( $_POST['gdpr-text'] ) ){ $option['gdpr-text'] = sanitize_text_field( $_POST['gdpr-text'] ); }
    if( !empty( $_POST['succes-title'] ) ){ $option['succes-title'] = sanitize_text_field( $_POST['succes-title'] ); }
    if( !empty( $_POST['succes-button-text'] ) ){ $option['succes-button-text'] = sanitize_text_field( $_POST['succes-button-text'] ); }
    if( !empty( $_POST['duplicite-email-title'] ) ){ $option['duplicite-email-title'] = sanitize_text_field( $_POST['duplicite-email-title'] ); }
    if( !empty( $_POST['duplicite-email-subtitle'] ) ){ $option['duplicite-email-subtitle'] = sanitize_text_field( $_POST['duplicite-email-subtitle'] ); }
    if( !empty( $_POST['duplicite-email-button-text'] ) ){ $option['duplicite-email-button-text'] = sanitize_text_field( $_POST['duplicite-email-button-text'] ); }
    
    update_option( 'toret_stock_alert', $option );

}  

    $option = get_option( 'toret_stock_alert' );

?>

<div class="wrap">

	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>

    <div class="t-col-12">
        <div class="toret-box box-info">
            <div class="box-header">
                <h3 class="box-title"><?php esc_attr_e('Settings', $this->plugin_slug); ?></h3>
            </div>
            <div class="box-body">
                <form method="post" style="margin-bottom:10px;">
                    <table class="table-bordered">
                        <tr>
                            <th><?php esc_attr_e('Product button text', $this->plugin_slug); ?></th>
                            <td>
                                <input class="input-big" type="text" name="product-button-text" value="<?php if( !empty( $option['product-button-text'] ) ){ echo $option['product-button-text']; }else{ esc_attr_e( 'Notify me when this product is available', $this->plugin_slug); } ?>" />
                            </td>
                        </tr>
                        <tr>
                            <th><?php esc_attr_e('Popup header text', $this->plugin_slug); ?></th>
                            <td>
                                <input class="input-big" type="text" name="popup-header-text" value="<?php if( !empty( $option['popup-header-text'] ) ){ echo $option['popup-header-text']; }else{ esc_attr_e( 'Notify me', $this->plugin_slug); } ?>" />
                            </td>
                        </tr>
                        <tr>
                            <th><?php esc_attr_e('Button text', $this->plugin_slug); ?></th>
                            <td>
                                <input class="input-big" type="text" name="button-text" value="<?php if( !empty( $option['button-text'] ) ){ echo $option['button-text']; }else{ esc_attr_e( 'Notify me', $this->plugin_slug); } ?>" />
                            </td>
                        </tr>
                        <tr>
                            <th><?php esc_attr_e('GDPR text', $this->plugin_slug); ?></th>
                            <td>
                                <input class="input-big" type="text" name="gdpr-text" value="<?php if( !empty( $option['gdpr-text'] ) ){ echo $option['gdpr-text']; }else{ esc_attr_e( 'Your data will be used only for email notification, about product avaibility.', $this->plugin_slug); } ?>" />
                            </td>
                        </tr>
                        <tr>
                            <th><?php esc_attr_e('Succes title', $this->plugin_slug); ?></th>
                            <td>
                                <input class="input-big" type="text" name="succes-title" value="<?php if( !empty( $option['succes-title'] ) ){ echo $option['succes-title']; }else{ esc_attr_e( 'Thank you', $this->plugin_slug); } ?>" />
                            </td>
                        </tr>
                        <tr>
                            <th><?php esc_attr_e('Succes subtitle', $this->plugin_slug); ?></th>
                            <td>
                                <input class="input-big" type="text" name="succes-subtitle" value="<?php if( !empty( $option['succes-subtitle'] ) ){ echo $option['succes-subtitle']; }else{ esc_attr_e( 'We send you an e-mail when product will be availible again.', $this->plugin_slug); } ?>" />
                            </td>
                        </tr>
                        <tr>
                            <th><?php esc_attr_e('Succes button text', $this->plugin_slug); ?></th>
                            <td>
                                <input class="input-big" type="text" name="succes-button-text" value="<?php if( !empty( $option['succes-button-text'] ) ){ echo $option['succes-button-text']; }else{ esc_attr_e( 'Close', $this->plugin_slug); } ?>" />
                            </td>
                        </tr>
                        <tr>
                            <th><?php esc_attr_e('Duplicite e-mail title', $this->plugin_slug); ?></th>
                            <td>
                                <input class="input-big" type="text" name="duplicite-email-title" value="<?php if( !empty( $option['duplicite-email-title'] ) ){ echo $option['duplicite-email-title']; }else{ esc_attr_e( 'Your e-mail is allready in database', $this->plugin_slug); } ?>" />
                            </td>
                        </tr>
                        <tr>
                            <th><?php esc_attr_e('Duplicite e-mail subtitle', $this->plugin_slug); ?></th>
                            <td>
                                <input class="input-big" type="text" name="duplicite-email-subtitle" value="<?php if( !empty( $option['duplicite-email-subtitle'] ) ){ echo $option['duplicite-email-subtitle']; }else{ esc_attr_e( 'We send you an e-mail when product will be availible again.', $this->plugin_slug); } ?>" />
                            </td>
                        </tr>
                        <tr>
                            <th><?php esc_attr_e('Duplicite e-mail button text', $this->plugin_slug); ?></th>
                            <td>
                                <input class="input-big" type="text" name="duplicite-email-button-text" value="<?php if( !empty( $option['duplicite-email-button-text'] ) ){ echo $option['duplicite-email-button-text']; }else{ esc_attr_e( 'Close', $this->plugin_slug); } ?>" />
                            </td>
                        </tr>                                            
                    </table>
                    <input type="hidden" name="update" value="ok" />
                    <input type="submit" class="button" value="<?php _e( 'Save', $this->plugin_slug ); ?>" />
                </form>
            </div>
        </div>
    </div>
  
    <div class="clear"></div>

</div>
