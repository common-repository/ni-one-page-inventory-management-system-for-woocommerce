<?php
if (!defined('ABSPATH')) {exit;}
if (!class_exists('NIWOPIMS_Sales_Order')) {
    include_once('niwopims-function.php');
    class NIWOPIMS_Sales_Order extends NIWOPIMS_Function{
        function __construct(){
			add_action( 'woocommerce_checkout_update_order_meta', array(&$this,'woocommerce_checkout_update_order_meta'), 101, 2 );
		}
		function woocommerce_checkout_update_order_meta($order_id, $items){
			$settings = get_option( 'niwopims_settings' );
			$online_sales_location = isset($settings["online_sales_location"])?$settings["online_sales_location"]:-1;
			if ($online_sales_location < 0){
				return false;
			}
			$order = wc_get_order( $order_id );
			$order_items =  $order->get_items();
			foreach ( $order_items as $item ) {
				$product_id = $item->get_product_id();
				$quantity = $item->get_quantity();
				$product_variation_id = $item->get_variation_id();
				$stock_product_id = ($product_variation_id > 0 )?$product_variation_id :$product_id ;
				
				$this->update_stock_sales_quantity($stock_product_id, $quantity );
				
			}
		}
		function update_stock_sales_quantity($product_id = 0,$quantity= 0){
			 global $wpdb;
			$settings = get_option( 'niwopims_settings' );
			$Location_id = isset($settings["online_sales_location"])?$settings["online_sales_location"]:-1;
			$niwopims_stock = $wpdb->prefix.'niwopims_stock';
			
			$query = "";
			$query = " SELECT *  FROM " . $niwopims_stock;
			$query .= " WHERE 1=1 ";
			$query .= " AND product_id  =  " .$product_id ;
			$query .= " AND location_id  =  " .$Location_id ;
			$row  = $wpdb->get_row($query);
			
			$stock_id 					= isset($row->stock_id)?$row->stock_id:0;
			$m_stock_sales_quantity 	= isset($row->sales_quantity)?$row->sales_quantity:0;
			$m_stock_balance_quantity 	= isset($row->balance_quantity)?$row->balance_quantity:0;
			
			if ($stock_id>0){
				$stock_data["sales_quantity"] = $quantity + $m_stock_sales_quantity;
			 	$stock_data["balance_quantity"]  = $m_stock_balance_quantity - $quantity ;
				$wpdb->update($niwopims_stock, $stock_data,  array(  'stock_id'    => $stock_id  ));
			
			}
			
			
		}
	}
}