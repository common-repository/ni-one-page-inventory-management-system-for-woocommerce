<?php
/*
Plugin Name: Ni One Page Inventory Management System For WooCommerce
Description: Ni One Page Inventory Management System For WooCommerce provides the option to purchase and maintained the stock for different location
Version: 1.1.3
Author: 	 anzia
Author URI:  http://naziinfotech.com/
Plugin URI:  https://wordpress.org/plugins/ni-one-page-inventory-management-system-for-woocommerce/
License:	 GPLv3 or later
License URI: http://www.gnu.org/licenses/agpl-3.0.html
Text Domain: niwopims
Domain Path: /languages/
Requires at least: 4.7
Tested up to: 6.4.2
WC requires at least: 3.0.0
WC tested up to: 8.4.0
Last Updated Date: 17-December-2023
Requires PHP: 7.0

*/
if ( ! defined( 'ABSPATH' ) ) { exit;}
if( !class_exists( 'Ni_One_Page_Inventory_Management_System_For_WooCommerce' ) ) { 
	class Ni_One_Page_Inventory_Management_System_For_WooCommerce {
		
		/*Defind vars*/
		var $vars = NULL;
		
		function __construct($vars = array()){
			
			/*Assing var*/
			$this->vars = $vars;
			
			
			$this->vars['__FILE__'] = __FILE__;
			
			register_activation_hook( __FILE__, array( $this,  'niwopims_register_activation') );
			add_filter( 'plugin_action_links', array( $this, 'niwopims_plugin_action_links' ), 10, 2);
			include_once('includes/niwopims-core.php'); 
			$obj = new NIWOPIMS_Core($this->vars);
			
			include_once('includes/niwopims-sales-order.php'); 
			$obj_sales_order = new NIWOPIMS_Sales_Order();
			
			
			
		}
		function niwopims_plugin_action_links($actions, $plugin_file){
			static $plugin;

			if (!isset($plugin))
				$plugin = plugin_basename(__FILE__);
			if ($plugin == $plugin_file) {
					  $settings_url = admin_url() . 'admin.php?page=niwopims-settings';
						$settings = array('settings' => '<a href='. $settings_url.'>' . __('Settings', 'niwopims') . '</a>');
						$site_link = array('support' => '<a href="http://naziinfotech.com" target="_blank">' . __('Support', 'niwopims') . '</a>');
						$email_link = array('email' => '<a href="mailto:support@naziinfotech.com" target="_top">' . __('Email', 'niwopims') . '</a>');
				
						$actions = array_merge($settings, $actions);
						$actions = array_merge($site_link, $actions);
						$actions = array_merge($email_link, $actions);
					
				}
				
				return $actions;
		}
		function niwopims_register_activation(){
			$cap = array();
			
			remove_role( 'niwopims_supplier' );
			
			$result = add_role( 'niwopims_supplier', __('Ni Supplier' ),$cap); 
			$role = get_role( 'niwopims_supplier' );
			
			$role->add_cap("manage_woocommerce");
			$role->add_cap("edit_product");
			$role->add_cap("read_product");
			$role->add_cap("delete_product");
			$role->add_cap("edit_products");
			$role->add_cap("edit_others_products");
			$role->add_cap("publish_products");
			$role->add_cap("read_private_products");
			$role->add_cap("delete_products");
			$role->add_cap("delete_private_products");
			$role->add_cap("delete_published_products");
			$role->add_cap("delete_others_products");
			$role->add_cap("edit_private_products");
			$role->add_cap("edit_published_products");
			$role->add_cap("assign_product_terms");
			
			do_action('niwopims_register_activated', $role);

			//$settings_url = admin_url() . 'admin.php?page=niwopims-settings';
			//exit( wp_redirect( 	$settings_url ) );
		}
		
		static function activation() {
			require_once('includes/niwopims-database.php');
			$obj = new NIWOPIMS_Database();
			$obj->create_tables();
			
		}
	}
	$obj_niwoomcr =  new Ni_One_Page_Inventory_Management_System_For_WooCommerce();
	register_activation_hook( __FILE__, array('Ni_One_Page_Inventory_Management_System_For_WooCommerce','activation'));	
	
}
?>