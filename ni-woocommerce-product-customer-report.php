<?php
/*
Plugin Name: Ni WooCommerce Customer Product Report
Description: Ni WooCommerce Customer Product Report shows the list of customers who purchase that product.
Author: 	 anzia
Version: 	 1.2.4
Author URI:  http://naziinfotech.com/
Plugin URI:  https://wordpress.org/plugins/ni-woocommerce-customer-product-report/
License:	 GPLv3 or later
License URI: http://www.gnu.org/licenses/agpl-3.0.html
Text Domain: nisalesreport13213
Domain Path: /languages/
Requires at least: 4.7
Tested up to: 6.4.3
WC requires at least: 3.0.0
WC tested up to: 8.7.0
Last Updated Date: 25-March-2024
Requires PHP: 7.0

*/
if ( !class_exists( 'Ni_WooCPR' ) ) {
	class Ni_WooCPR {
		 function __construct() {
			add_action( 'activated_plugin',  array(&$this,'niwoocpr_activation_redirect' ));
			add_filter( 'plugin_action_links', array(&$this,'niwoocpr_plugin_action_links'), 10, 5 );
			if ( is_admin() ) {
				
				include_once('include/ni-woocpr-core.php'); 
				$obj = new Ni_WooCPR_Core();
			}
		 }
		 function niwoocpr_plugin_action_links($actions, $plugin_file){
		 	static $plugin;

			if (!isset($plugin))
				$plugin = plugin_basename(__FILE__);
			if ($plugin == $plugin_file) {
					  $settings_url = admin_url() . 'admin.php?page=woocpr-setting';
						$settings = array('settings' => '<a href='. $settings_url.'>' . __('Settings', '') . '</a>');
						$site_link = array('support' => '<a href="http://naziinfotech.com" target="_blank">Support</a>');
						$email_link = array('email' => '<a href="mailto:support@naziinfotech.com" target="_top">Email</a>');
				
						$actions = array_merge($settings, $actions);
						$actions = array_merge($site_link, $actions);
						$actions = array_merge($email_link, $actions);
					
				}
				
			return $actions;
		 }
		 static   function niwoocpr_activation_redirect($plugin){
			 if( $plugin == plugin_basename( __FILE__ ) ) {
				exit( wp_redirect( admin_url( 'admin.php?page=ni-woocpr-setting' ) ) );
			}
		}
	}
	$obj  = new Ni_WooCPR();
}

?>
