<?php 
// if uninstall.php is not called by WordPress, die
if (!defined('WP_UNINSTALL_PLUGIN')) {
    die;
}
 
$option_name = 'niwopims_settings';
 
delete_option($option_name);


global $wpdb;
$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}niwopims_stock");
$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}niwopims_purchase_header");
$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}niwopims_purchase_detail");

?>