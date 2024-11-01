<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * 
 * @since 0.1
 * @extends \WC_Email
 */
class WC_Stock_Alert_Customer_Save_Email extends WC_Email {
	
  	/**
	 * Unique identifier
	 *    
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_slug = 'toret-product-stock-aler';
  
  
  	/**
	 * Set email defaults
	 *
	 * @since 0.1
	 */
	public function __construct() {
		
    	$this->id          = 'wc_stock_alert_customer_save_email';
    	$this->customer_email = true;
		$this->title       = __('Stock alert info', $this->plugin_slug);
		$this->description = __('Stock alert info', $this->plugin_slug);
		$this->heading     = __('Stock alert info', $this->plugin_slug);
		$this->subject     = __('Stock alert info from {site_title}', $this->plugin_slug);
    
		
    	// these define the locations of the templates that this email should use, we'll just use the new order template since this email is similar
		$this->template_html  = 'stock-alert-customer-save-email.php';
		$this->template_plain = 'stock-alert-customer-save-email-plain.php';
		$this->templates = array( 'stock-alert-customer-save-email.php', 'stock-alert-customer-save-email-plain.php' );

		// Call parent constructor to load any other defaults not explicity defined here
		parent::__construct();	  
  
  	}

  	/**
	 * Determine if the email should actually be sent and setup email merge variables
	 *
	 * @since 0.1
	 * @param int $order_id
	 */
	public function trigger( $product_id, $email ) {
  
		$this->product = wc_get_product( $product_id );

		if ( ! $this->is_enabled() ) {
			return;
		}

		$recipients = $email	;

		return $this->send( $recipients, $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );

    
	}
	
  
  	/**
	 * get_content_html function.
	 *
	 * @since 0.1
	 * @return string
	 */
	public function get_content_html() {
		return wc_get_template_html( $this->template_html, array(
			'product'	=> $this->product,
			'email_heading' => $this->get_heading()
		) );
        
	}
	
  
  	/**
	 * get_content_plain function.
	 *
	 * @since 0.1
	 * @return string
	 */
	public function get_content_plain() {
		return wc_get_template_html( $this->template_plain, array(
			'product'	=> $this->product,
			'email_heading' => $this->get_heading()
		) );
	}      

	/**
	 * Initialise settings form fields.
	 */
	public function init_form_fields() {
		$this->form_fields = array(
			'enabled' => array(
				'title'         => __( 'Enable/Disable', $this->plugin_slug ),
				'type'          => 'checkbox',
				'label'         => __( 'Enable this email notification', $this->plugin_slug ),
				'default'       => 'yes',
			),
			'subject' => array(
				'title'         => __( 'Subject', $this->plugin_slug ),
				'type'          => 'text',
				'desc_tip'      => true,
				/* translators: %s: list of placeholders */
				'description'   => sprintf( __( 'Available placeholders: %s', $this->plugin_slug ), '<code>{site_title}, {order_date}, {order_number}</code>' ),
				'placeholder'   => $this->get_default_subject(),
				'default'       => '',
			),
			'heading' => array(
				'title'         => __( 'Email heading', $this->plugin_slug ),
				'type'          => 'text',
				'desc_tip'      => true,
				/* translators: %s: list of placeholders */
				'description'   => sprintf( __( 'Available placeholders: %s', $this->plugin_slug ), '<code>{site_title}, {order_date}, {order_number}</code>' ),
				'placeholder'   => $this->get_default_heading(),
				'default'       => '',
			),
			'email_type' => array(
				'title'         => __( 'Email type', 'woocommerce' ),
				'type'          => 'select',
				'description'   => __( 'Choose which format of email to send.', $this->plugin_slug ),
				'default'       => 'html',
				'class'         => 'email_type wc-enhanced-select',
				'options'       => $this->get_email_type_options(),
				'desc_tip'      => true,
			),
		);
	} 

} // end class