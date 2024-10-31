<?php
if (!defined('ABSPATH')) {
    exit;
}
if (!class_exists('NIWOPIMS_Database')) {
    include_once('niwopims-function.php');
    class NIWOPIMS_Database extends NIWOPIMS_Function{
        function __construct(){}
		function create_tables(){
			global $wpdb;
			
			$prefix = $wpdb->prefix;
			
			$niwopims_stock = $prefix.'niwopims_stock';
			$niwopims_purchase_header = $prefix.'niwopims_purchase_header';
			$niwopims_purchase_detail = $prefix.'niwopims_purchase_detail';
			
			$charset_collate = $wpdb->get_charset_collate();
			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			
			if($wpdb->get_var("SHOW TABLES LIKE '$niwopims_stock'") != $niwopims_stock) {
					$sql = "CREATE TABLE IF NOT EXISTS `{$niwopims_stock}` (
					`stock_id` BIGINT NOT NULL AUTO_INCREMENT 
					,`product_id` BIGINT NOT NULL , `location_id` BIGINT NOT NULL 
					,`purchase_quantity` DOUBLE NOT NULL 
					, `sales_quantity` DOUBLE NOT NULL 
					, `balance_quantity` DOUBLE NOT NULL 
					, `adjust_add_quantity` DOUBLE NOT NULL 
					, `adjust_less_quantity` DOUBLE NOT NULL 
					, PRIMARY KEY (`stock_id`)
					)  $charset_collate;";					
					dbDelta( $sql );
			}
			
			
			if($wpdb->get_var("SHOW TABLES LIKE '$niwopims_purchase_header'") != $niwopims_purchase_header) {
					$sql = "CREATE TABLE IF NOT EXISTS `{$niwopims_purchase_header}` (
						`purchase_id` bigint(20) NOT NULL AUTO_INCREMENT,
						`purchase_date` date NOT NULL,
						`purchase_no` varchar(100) NOT NULL,
						`location_id` bigint(20) NOT NULL,
						`supplier_id` bigint(20) NOT NULL,
						`status_id` VARCHAR(100) NOT NULL,
						`purchase_total` decimal(10,0) NOT NULL,
						`purchase_notes` text NOT NULL,
						`created_date` date NOT NULL,
						`updated_date` date NOT NULL,
						`created_user_id` int(11) NOT NULL,
						`updated_user_id` int(11) NOT NULL,
						PRIMARY KEY (`purchase_id`)
					) $charset_collate;";					
					dbDelta( $sql );
			}
			
			if($wpdb->get_var("SHOW TABLES LIKE '$niwopims_purchase_detail'") != $niwopims_purchase_detail) {
					$sql = "CREATE TABLE IF NOT EXISTS `{$niwopims_purchase_detail}` (
						`purchase_product_id` bigint(20) NOT NULL AUTO_INCREMENT,
						`purchase_id` bigint(20) NOT NULL,
						`product_id` bigint(20) NOT NULL,
						`purchase_quantity` int(11) NOT NULL,
						`purchase_price` decimal(10,2) NOT NULL,
						`line_total` decimal(10,2) NOT NULL,
						PRIMARY KEY (`purchase_product_id`)
					) $charset_collate;";					
					dbDelta( $sql );
			}
			
			
		
		}
    }
}