<?php
/**
 * @package   Toret Product Stock Alert
 * @author    Vladislav Musílek
 * @license   GPL-2.0+
 * @link      https://toret.cz
 * @copyright 2018 Toret
 */

class Toret_Product_Stock_Alert {

	/**
	 * Plugin version, used for cache-busting of style and script file references.
	 *
	 * @since   1.0
	 *
	 * @var     string
	 */
	const VERSION = '1.0';

	/**
	 *
	 * @since    1.0
	 *
	 * @var      string
	 */
	protected $plugin_slug = 'toret-product-stock-alert';

	/**
	 * Instance of this class.
	 *
	 * @since    1.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Initialize the plugin by setting localization and loading public scripts
	 * and styles.
	 *
	 * @since     1.0
	 */
	private function __construct() {

		// Load plugin text domain
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

		// Activate plugin when new blog is added
		add_action( 'wpmu_new_blog', array( $this, 'activate_new_site' ) );

		// Load public-facing style sheet and JavaScript.
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		
		add_action( 'woocommerce_single_product_summary', array( $this, 'stock_alert_button' ), 32 );

		add_action( 'wp_footer', array( $this, 'modal_code' ) );

		add_action( 'wp_ajax_stock_alert_form', array( $this, 'stock_alert_form' ) );
		add_action( 'wp_ajax_nopriv_stock_alert_form', array( $this, 'stock_alert_form' ) );

		add_filter( 'woocommerce_locate_template', array( $this, 'toret_locate_template' ), 10, 3 );

		//Trigger for stock change
		add_action( 'woocommerce_product_set_stock', array( $this, 'send_alert' ) );
		add_action( 'woocommerce_variation_set_stock', array( $this, 'send_alert' ) );

	}

	/**
	 * Return the plugin slug.
	 *
	 * @since    1.0.0
	 *
	 * @return    Plugin slug variable.
	 */
	public function get_plugin_slug() {
		return $this->plugin_slug;
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     1.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Fired when the plugin is activated.
	 *
	 * @since    1.0
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses
	 *                                       "Network Activate" action, false if
	 *                                       WPMU is disabled or plugin is
	 *                                       activated on an individual blog.
	 */
	public static function activate( $network_wide ) {

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			if ( $network_wide  ) {

				// Get all blog ids
				$blog_ids = self::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {

					switch_to_blog( $blog_id );
					self::single_activate();
				}

				restore_current_blog();

			} else {
				self::single_activate();
			}

		} else {
			self::single_activate();
		}

	}

	/**
	 * Fired when the plugin is deactivated.
	 *
	 * @since    1.0
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses
	 *                                       "Network Deactivate" action, false if
	 *                                       WPMU is disabled or plugin is
	 *                                       deactivated on an individual blog.
	 */
	public static function deactivate( $network_wide ) {

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			if ( $network_wide ) {

				// Get all blog ids
				$blog_ids = self::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {

					switch_to_blog( $blog_id );
					self::single_deactivate();

				}

				restore_current_blog();

			} else {
				self::single_deactivate();
			}

		} else {
			self::single_deactivate();
		}

	}

	/**
	 * Fired when a new site is activated with a WPMU environment.
	 *
	 * @since    1.0
	 *
	 * @param    int    $blog_id    ID of the new blog.
	 */
	public function activate_new_site( $blog_id ) {

		if ( 1 !== did_action( 'wpmu_new_blog' ) ) {
			return;
		}

		switch_to_blog( $blog_id );
		self::single_activate();
		restore_current_blog();

	}

	/**
	 * Get all blog ids of blogs in the current network that are:
	 * - not archived
	 * - not spam
	 * - not deleted
	 *
	 * @since    1.0
	 *
	 * @return   array|false    The blog ids, false if no matches.
	 */
	private static function get_blog_ids() {

		global $wpdb;

		// get an array of blog ids
		$sql = "SELECT blog_id FROM $wpdb->blogs
			WHERE archived = '0' AND spam = '0'
			AND deleted = '0'";

		return $wpdb->get_col( $sql );

	}

	
	/**
	 * Fired for each blog when the plugin is activated.
	 *
	 * @since    1.0
	 */
	private static function single_activate() {

		global $wpdb;

		$wpdb->hide_errors();

		$collate = '';

		if ( $wpdb->has_cap( 'collation' ) ) {
			if ( ! empty($wpdb->charset ) ) {
				$collate .= "DEFAULT CHARACTER SET $wpdb->charset";
			}
			if ( ! empty($wpdb->collate ) ) {
				$collate .= " COLLATE $wpdb->collate";
			}
		}

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

        $table = "
	        	CREATE TABLE IF NOT EXISTS {$wpdb->prefix}stock_alert (
  					`ID` bigint(255) NOT NULL AUTO_INCREMENT,
  					`date` varchar(100) COLLATE utf8_czech_ci NOT NULL,
  					`name` longtext COLLATE utf8_czech_ci NOT NULL,
  					`surname` longtext COLLATE utf8_czech_ci NOT NULL,
  					`email` varchar(100) COLLATE utf8_czech_ci NOT NULL,
  					`productid` varchar(10) COLLATE utf8_czech_ci NOT NULL,
  					`send` int(2) COLLATE utf8_czech_ci NOT NULL,
  					`senddate` varchar(100) COLLATE utf8_czech_ci NOT NULL,
  				PRIMARY KEY (`ID`)
	         	) $collate;
	      	";
		    dbDelta( $table );

    }

	/**
	 * Fired for each blog when the plugin is deactivated.
	 *
	 * @since    1.0
	 */
	private static function single_deactivate() {

	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0
	 */
	public function load_plugin_textdomain() {

		$domain = $this->plugin_slug;
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

		$load = load_textdomain( $domain, WP_LANG_DIR . '/toret/' . $domain . '-' . $locale . '.mo' );

		if( $load === false ){
			load_textdomain( $domain, TORETPSADIR . 'languages/' . $domain . '-' . $locale . '.mo' );
		}

	}	


	/**
	 * Register and enqueue public-facing style sheet.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style(  $this->plugin_slug . '-plugin-style', plugins_url( 'assets/css/public.css', __FILE__ ), array(), self::VERSION );
	}

	/**
	 * Register and enqueues public-facing JavaScript files.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		
		 if( !is_checkout() ){
        
            wp_enqueue_script( $this->plugin_slug . '-plugin-script', plugins_url( 'assets/js/public.js', __FILE__ ), array( 'jquery' ), self::VERSION );

            wp_localize_script($this->plugin_slug . '-plugin-script','wcp_localize',array(
                'adminurl'          	=> admin_url().'admin-ajax.php',
                'homeurl'           	=> get_bloginfo('url'),
                'wc_ajax_url'       	=> WC_AJAX::get_endpoint( "%%endpoint%%" ),
                'undefined_name'		=> __( 'Please fill your name', $this->plugin_slug ),
                'undefined_secondname'	=> __( 'Please fill your second name', $this->plugin_slug ),
                'undefined_email'		=> __( 'Please fill your e-mail', $this->plugin_slug ),
            ));
        }
	}

	/**
	 * Force WooCommerce to load email template from plugin
	 *
	 * @since    1.0.0
	 */
	public function toret_locate_template( $template, $template_name, $template_path ) {
    
		if ( $template_name == 'stock-alert-customer-save-email.php' ){
			if( locate_template( 'stock-alert/stock-alert-customer-save-email.php' ) ){
				load_template( 'stock-alert/stock-alert-customer-save-email.php' );
			}else{
            	$template = TORETPSADIR . 'includes/emails/stock-alert-customer-save-email.php';
        	}
        }elseif( $template_name == 'stock-alert-customer-save-email-plain.php' ){
        	if( locate_template( 'stock-alert/stock-alert-customer-save-email-plain.php' ) ){
				load_template( 'stock-alert/stock-alert-customer-save-email-plain.php' );
			}else{
            	$template = TORETPSADIR . 'includes/emails/stock-alert-customer-save-email-plain.php';
        	}
        }

        if ( $template_name == 'stock-alert-customer-email.php' ){
            if( locate_template( 'stock-alert/stock-alert-customer-email.php' ) ){
				load_template( 'stock-alert/stock-alert-customer-email.php' );
			}else{
            	$template = TORETPSADIR . 'includes/emails/stock-alert-customer-email.php';
        	}
        }elseif( $template_name == 'stock-alert-customer-email-plain.php' ){
            if( locate_template( 'stock-alert/stock-alert-customer-email-plain.php' ) ){
				load_template( 'stock-alert/stock-alert-customer-email-plain.php' );
			}else{
            	$template = TORETPSADIR . 'includes/emails/stock-alert-customer-email-plain.php';
        	}
        }
        
        return $template;
    
    }

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0
	 */
	public function stock_alert_button() {

		global $product;

		//$product = wc_get_product( $post->ID );

		if( !$product->is_in_stock() ){

			echo '<span class="stock-alert-button" data-productid="'.$product->get_id().'">'. __( 'Notify me when this product is available', $this->plugin_slug ) .'</span>';

		}

	}


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0
	 */
	public function modal_code() {

		$option = get_option( 'toret_stock_alert' );

		//Get option or default data
		if( !empty( $option['popup-header-text'] ) ){ 
			$popup_header_text = $option['popup-header-text']; 
		}else{ 
			$popup_header_text = __( 'Notify me when this product is available', $this->plugin_slug); 
		}
		if( !empty( $option['button-text'] ) ){ 
			$button_text = $option['button-text']; 
		}else{ 
			$button_text = __( 'Notify me', $this->plugin_slug); 
		}
		if( !empty( $option['gdpr-text'] ) ){ 
			$gdpr_text = $option['gdpr-text']; 
		}else{ 
			$gdpr_text = __( 'Your data will be used only for email notification, about product avaibility.', $this->plugin_slug); 
		}
		if( !empty( $option['succes-title'] ) ){ 
			$success_title = $option['succes-title']; 
		}else{ 
			$success_title = __( 'Thank you', $this->plugin_slug); 
		}
		if( !empty( $option['succes-subtitle'] ) ){ 
			$success_subtitle = $option['succes-subtitle']; 
		}else{ 
			$success_subtitle = __( 'We send you an e-mail when product will be availible again.', $this->plugin_slug); 
		}
		if( !empty( $option['succes-button-text'] ) ){ 
			$success_button_text = $option['succes-button-text']; 
		}else{ 
			$success_button_text = __( 'Close', $this->plugin_slug); 
		}
		if( !empty( $option['duplicite-email-title'] ) ){ 
			$duplicite_email_title = $option['duplicite-email-title']; 
		}else{ 
			$duplicite_email_title = __( 'Your e-mail is allready in database', $this->plugin_slug); 
		}
		if( !empty( $option['duplicite-email-subtitle'] ) ){ 
			$duplicite_email_subtitle = $option['duplicite-email-subtitle']; 
		}else{ 
			$duplicite_email_subtitle = __( 'We send you an e-mail when product will be availible again.', $this->plugin_slug); 
		}
		if( !empty( $option['duplicite-email-button-text'] ) ){ 
			$duplicite_email_button_text = $option['duplicite-email-button-text']; 
		}else{ 
			$duplicite_email_button_text = __( 'Close', $this->plugin_slug); 
		}

		$html = '
			<div id="popup-alert-stock" class="popup-alert-stock"> 
			<div class="popup-alert-stock-overlay">
				<svg width="200px"  height="200px"  xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid" class="lds-eclipse" style="background: none;"><path ng-attr-d="{{config.pathCmd}}" ng-attr-fill="{{config.color}}" stroke="none" d="M15 50A35 35 0 0 0 85 50A35 37 0 0 1 15 50" fill="#48afdb" transform="rotate(174 50 51)"><animateTransform attributeName="transform" type="rotate" calcMode="linear" values="0 50 51;360 50 51" keyTimes="0;1" dur="1s" begin="0s" repeatCount="indefinite"></animateTransform></path></svg>	
			</div> 
			<div class="popup-alert-stock-response">
				<h2>' . $success_title . '</h2>
				<h3>' . $success_subtitle . '</h3>
				<a href="#" class="popup-response-close">' . $success_button_text . '</a>
			</div>
			<div class="popup-alert-stock-email-error">
				<h2>' . $duplicite_email_title . '</h2>
				<h3>' . $duplicite_email_subtitle . '</h3>
				<a href="#" class="popup-response-close">' . $duplicite_email_button_text . '</a>
			</div>
        		<form action="#" method="post" id="popup-alert-form">
            		<header>
                		<a href="javascript:void(0)" class="popup-alert-close close" id="popup-close-button">×</a>
                		<h3 class="popup-alert-title">'.$popup_header_text.'</h3>
            		</header>
            		<div class="popup-alert-body">
                		<ul>
                    		<li><label for="alert-first-name">' . __( 'First Name', $this->plugin_slug ) . '</label> <input type="text" name="alert-first-name" class="alert-first-name" placeholder="' . __( 'First Name', $this->plugin_slug ) . '"></li>
                    		<li><label for="alert-last-name">' . __( 'Last Name', $this->plugin_slug ) . '</label> <input type="text" name="alert-last-name" class="alert-last-name" placeholder="' . __( 'Last Name', $this->plugin_slug ) . '"></li>
                    		<li><label for="alert-email">' . __( 'Email:', $this->plugin_slug ) . ' </label> <input type="email" name="alert-email" class="alert-email" placeholder="' . __( 'Your Email', $this->plugin_slug ) . '"></li>
                		</ul>
                		<input type="hidden" name="alert-productid" class="alert-productid" value="" />
            		</div>
            		<div class="gdpr-text"> '.$gdpr_text.' </div>
            		<footer>
            			<button type="submit" class="popup-alert-submit">' . $button_text . '</button>
            		</footer>
        		</form>
    		</div>
    	';
	
    	echo $html;

	}

	/**
	 * Save data
	 *
	 * @since    1.0
	 */
	public function stock_alert_form() {

		$check = true;

		if( !empty( $_POST['name'] ) ){
			$name = sanitize_text_field( $_POST['name'] );
		}else{
			$check = false;
		}

		if( !empty( $_POST['surname'] ) ){
			$surname = sanitize_text_field( $_POST['surname'] );
		}else{
			$check = false;
		}

		if( !empty( $_POST['email'] ) ){
			$email = sanitize_text_field( $_POST['email'] );
		}else{
			$check = false;
		}

		if( !empty( $_POST['productid'] ) ){
			$productid = sanitize_text_field( $_POST['productid'] );
		}else{
			$check = false;
		}

		if( $check === false ){
			
			$reponse = array(
    	        'status'    => 'failed',
	            'message'      => __( 'Unknow form error', $this->plugin_slug )
        	);
        	echo json_encode( $reponse );
        	exit();

		}else{
			global $wpdb;
			$check_email = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."stock_alert WHERE email = '".$email."'" );
			if( !empty( $check_email[0]->email ) ){

				$reponse = array(
    	        	'status'    => 'duplicate'
        		);
        		echo json_encode( $reponse );

				exit();

			}else{
		
				$data = array();
				$data['date']   	= time();
        		$data['name']     	= $name;
        		$data['surname']   	= $surname;
        		$data['email']   	= $email;
        		$data['productid']	= $productid;
        		//Not send
        		$data['send']   	= 1;
        		$data['senddate']   = 'no';
            
        		global $wpdb;
        		$wpdb->insert( $wpdb->prefix.'stock_alert', $data ); 

        		$number = $wpdb->get_var("SELECT COUNT( ID ) FROM ".$wpdb->prefix."stock_alert WHERE productid = '".$productid."' AND send = '1'");

        		$option = get_option( 'toret_stock_alert' );

        		//Send email to customer
        		WC()->mailer();
        		require_once( TORETPSADIR . 'includes/class-wc-stock-alert-customer-save-email.php' );          
				$send = new WC_Stock_Alert_Customer_Save_Email();      
        		$mail = $send->trigger( $productid, $email );

        		$reponse = array(
    	        	'status'    => 'success', 
    	        	'number'	=> $number 
        		);
        		echo json_encode( $reponse );

				exit();	

			}			

		}
	}

	/**
	 * Send alert
	 *
	 * @since    1.0.0
	 */
	public function send_alert( $product ){

		if( $product->is_in_stock() ){ 
			$product_id = $product->get_id();
			global $wpdb;	
        	$data = $wpdb->get_var("SELECT * FROM ".$wpdb->prefix."stock_alert WHERE productid = '".$product_id."' AND send = '1'");
        	if( !empty( $data ) ){
        		WC()->mailer();
        		require_once( TORETPSADIR . 'includes/class-wc-stock-alert-customer-email.php' );
          
				$send = new WC_Stock_Alert_Customer_Email();
      
        		$mail = $send->trigger( $product_id );
        	}

		}

	}


}//End class
