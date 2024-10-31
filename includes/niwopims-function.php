<?php 
if ( ! defined( 'ABSPATH' ) ) { exit;}
if( !class_exists( 'NIWOPIMS_Function' ) ) {
	class NIWOPIMS_Function {
        function get_request($name,$default = NULL,$set = false){
			if(isset($_REQUEST[$name])){
				
				$newRequest =  sanitize_text_field( isset($_REQUEST[$name]) ? $_REQUEST[$name] : '');
				
				if(is_array($newRequest)){
					$newRequest = implode(",", $newRequest);					
				}else{
					$newRequest = trim($newRequest);
				}
				
				if($set){
					$_REQUEST[$name] = $newRequest;
				}
				
				return $newRequest;
			}else{
				if($set) {
					$_REQUEST[$name] = $default;
				}	
				return $default;
			}
		}
		function pretty_print($arr){
			echo "<pre>";
			print_r($arr);
			echo "</pre>";
		}
		function get_product_post_meta($post_id = 0){
			$post_meta	= get_post_meta($post_id);
			$rows = array();
			if($post_meta){
				foreach($post_meta as $key=>$value){
					$rows[ ltrim( $key,"_")]	= isset($value[0])?$value[0]:'';
				}
			}
			return $rows;

		}
		function get_today_date()
        {

            return  date_i18n("Y-m-d");
        }
        function get_login_user_id()
        {
            $current_user_id = get_current_user_id();
            return  $current_user_id;
		}
		function get_wc_price($price, $attr = array(), $return = true){
			$new_price  = 0;
			if ($price){
				$new_price  = wc_price($price);
			}
			if($return){
				return $new_price;
			}else{
				print($new_price);
			}			
		}
		function get_location(){
			global $wpdb;
            
			$location = array();
			
			$strQuery = "";
            $strQuery .= " SELECT posts.ID as location_id, posts.post_title as location_name FROM {$wpdb->prefix}posts as posts";
			$strQuery .= " LEFT JOIN {$wpdb->prefix}postmeta as is_active ON is_active.post_id=posts.ID ";
			$strQuery .= " WHERE 1=1 ";
            $strQuery .= " AND	posts.post_type ='niwopims_location' ";
			$strQuery .= " AND	is_active.meta_key ='_is_active' ";
			$strQuery .= " AND	is_active.meta_value ='yes' ";
			$strQuery .= " ORDER BY location_name asc ";
			
            $rows =   $wpdb->get_results($strQuery);

			foreach($rows as $key=>$value){
				$location[$value->location_id] = $value->location_name;
			}
            return $location;
		
		}
		function get_purchase_order_count(){
			global $wpdb;
			$niwopims_purchase_header = $wpdb->prefix . "niwopims_purchase_header"; 
			$strQuery = "";
		    $strQuery .= " SELECT (IFNULL((MAX(purchase_id) ),0)+1) FROM " . 	$niwopims_purchase_header;
			$rows =   $wpdb->get_var( $strQuery );
		
			return  $rows;
		}
		function get_product_parent(){
		    global $wpdb;
			$query = "";
			$query = " SELECT ";
			$query .= " posts.post_parent as post_parent ";
			$query .= " FROM  {$wpdb->prefix}posts as posts			";
			$query .= "	WHERE 1 = 1";
			$query .= "	AND posts.post_type  IN ('product_variation') ";
			$query .=" AND posts.post_status='publish'";
			
			$query .= " GROUP BY post_parent ";
			$row = $wpdb->get_results($query);		
			
			$post_parent_array = array();
			foreach($row as $key=>$value){
				$post_parent_array[] = $value->post_parent;
			}
			return $post_parent_array;
		 }
		function  display_alphabet(){
			?>
            
                <ul class="list-inline">
                <?php
                for ($x = ord('a'); $x <= ord('z'); $x++){
                ?>
                 <li class="list-inline-item" > <a href="#" class="_alphabet" id="<?php echo strtoupper( chr($x)); ?>"><?php echo strtoupper( chr($x)); ?></a>  </li>
                
              
            <?php
			}
			?>
			  </ul>
			<?php
			
		}
		
		function create_hidden_fields($request = array(), $type = "hidden"){
			$output_fields = "";
			
			foreach($request as $key => $value):
				if(is_array($value)){
					foreach($value as $akey => $avalue):
						if(is_array($avalue)){
							$output_fields .=  "\n<input type=\"{$type}\" name=\"{$key}[{$akey}]\" value=\"".implode(",",$avalue)."\" />";
						}else{
							$output_fields .=  "<input type=\"{$type}\" name=\"{$key}[{$akey}]\" value=\"{$avalue}\" />";
						}
					endforeach;
				}else{
					$output_fields .=  "\n<input type=\"{$type}\" name=\"{$key}\" value=\"{$value}\" />";
				}
			endforeach;
			return $output_fields;
		}
		
		function get_pagination($total_pages = 50,$limit = 10,$adjacents = 3,$targetpage = "#",$request = array()){		
				
				if(count($request)>0){
					unset($request['p']);
				}			
				
				/* Setup page vars for display. */
				if(isset($_REQUEST['p'])){
					$page = sanitize_text_field($_REQUEST['p']);
					$start = ($page - 1) * $limit; 			//first item to display on this page
				}else{
					$page = false;
					$start = 0;	
					$page = 1;
				}
				
				if ($page == 0) $page = 1;					//if no page var is given, default to 1.
				$prev = $page - 1;							//previous page is page - 1
				$next = $page + 1;							//next page is page + 1
				$lastpage = ceil($total_pages/$limit);		//lastpage is = total pages / items per page, rounded up.
				$lpm1 = $lastpage - 1;						//last page minus 1
				
				
				
				$label_previous = esc_html__('Previous', 'niwooims_textdomain');
				$label_next = esc_html__('Next', 'niwooims_textdomain');
				
				/* 
					Now we apply our rules and draw the pagination object. 
					We're actually saving the code to a variable in case we want to draw it more than once.
				*/
				$pagination = "";
				if($lastpage > 1)
				{	
					$pagination .= "<ul class=\"pagination\">";
					//previous button
					if ($page > 1) 
						$pagination.= "<li class=\"page-item\"><a href=\"#\" data-p=\"$prev\" class=\"page-link\">{$label_previous}</a></li>\n";
					else
						$pagination.= "<li class=\"page-item disabled\"><span class=\"page-link disabled\">{$label_previous}</span></li>\n";	
					
					//pages	
					if ($lastpage < 7 + ($adjacents * 2))	//not enough pages to bother breaking it up
					{	
						for ($counter = 1; $counter <= $lastpage; $counter++)
						{
							if ($counter == $page)
								$pagination.= "<li class=\"page-item active\"><span class=\"page-link current\">".number_format_i18n($counter)."</span></li>\n";
							else
								$pagination.= "<li class=\"page-item\"><a href=\"#\" data-p=\"$counter\" class=\"page-link\">".number_format_i18n($counter)."</a></li>\n";					
						}
					}
					elseif($lastpage > 5 + ($adjacents * 2))	//enough pages to hide some
					{
						//close to beginning; only hide later pages
						if($page < 1 + ($adjacents * 2))		
						{
							for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++)
							{
								if ($counter == $page)
									$pagination.= "<li class=\"page-item active\"><span class=\"page-link current\">".number_format_i18n($counter)."</span></li>\n";
								else
									$pagination.= "<li class=\"page-item\"><a href=\"#\" data-p=\"$counter\" class=\"page-link\">".number_format_i18n($counter)."</a></li>\n";					
							}
							$pagination.= "<li class=\"page-item\"><span class=\"page-link adjacents disabled\">....</span></li>";
							$pagination.= "<li class=\"page-item\"><a href=\"#\" data-p=\"$lpm1\" class=\"page-link\">$lpm1</a></li>\n";
							$pagination.= "<li class=\"page-item\"><a href=\"#\" data-p=\"$lastpage\" class=\"page-link\">$lastpage</a></li>\n";		
						}
						//in middle; hide some front and some back
						elseif($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2))
						{
							$pagination.= "<li class=\"page-item\"><a href=\"#\" data-p=\"1\" class=\"page-link\">".number_format_i18n(1)."</a></li>\n";
							$pagination.= "<li class=\"page-item\"><a href=\"#\" data-p=\"2\" class=\"page-link\">".number_format_i18n(2)."</a></li>\n";
							$pagination.= "<li class=\"page-item\"><span class=\"page-link adjacents disabled\">....</span></li>";
							for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++)
							{
								if ($counter == $page)
									$pagination.= "<li class=\"page-item active\"><span class=\"page-link current\">".number_format_i18n($counter)."</span></li>\n";
								else
									$pagination.= "<li class=\"page-item\"><a href=\"#\" data-p=\"$counter\" class=\"page-link\">".number_format_i18n($counter)."</a></li>\n";					
							}
							$pagination.= "<li class=\"page-item\"><span class=\"page-link adjacents disabled\">....</span></li>";
							$pagination.= "<li class=\"page-item\"><a href=\"#\" data-p=\"$lpm1\" class=\"page-link\">$lpm1</a></li>\n";
							$pagination.= "<li class=\"page-item\"><a href=\"#\" data-p=\"$lastpage\" class=\"page-link\">$lastpage</a></li>\n";		
						}
						//close to end; only hide early pages
						else
						{
							$pagination.= "<li class=\"page-item\"><a href=\"#\" data-p=\"1\" class=\"page-link\">".number_format_i18n(1)."</a></li>\n";
							$pagination.= "<li class=\"page-item\"><a href=\"#\" data-p=\"2\" class=\"page-link\">".number_format_i18n(2)."</a></li>\n";
							$pagination.= "<li class=\"page-item\"><span class=\"page-link adjacents disabled\">....</span></li>";
							for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++)
							{
								if ($counter == $page)
									$pagination.= "<li class=\"page-item active\"><span class=\"page-link current\">".number_format_i18n($counter)."</span></li>\n";
								else
									$pagination.= "<li class=\"page-item\"><a href=\"#\" data-p=\"$counter\" class=\"page-link\">".number_format_i18n($counter)."</a></li>\n";					
							}
						}
					}
					
					//next button
					if ($page < $counter - 1) 
						$pagination.= "<li class=\"page-item\"><a href=\"#\" data-p=\"$next\" class=\"page-link\">{$label_next}</a></li>\n";
					else
						$pagination.= "<li class=\"page-item disabled\"><span class=\"page-link disabled\">{$label_next}</span></li>\n";
						
					$pagination.= "</ul>\n";
				}
				
				return $pagination;
		}
		
		function get_the_pagination($requests = array()){
			
			$total_rows = isset($requests['total_rows']) ? $requests['total_rows'] : 0;
			
			$limit 	    = $this->get_request("limit",10);
			$adjacents  = $this->get_request("adjacents",3);
			$script     = $this->get_request("script",'');
			$p    		= $this->get_request("p",1,true);
			
			$output = "<div class=\"clearfix\"></div><br />";
			$output .='<div class="card" style="max-width:100%;">';
				$output .='<div class="card-body">';
					$output = '<div class="pull-right">'.$this->get_pagination($total_rows,$limit,$adjacents,'#').'</div>';
				$output .='</div>';
			$output .='</div>';
			
		
			
			$output .= '<form name="'.$script.'commanFormPagination" id="'.$script.'commanFormPagination" method="post" action="">';
			$output .= $this->create_hidden_fields($requests,'hidden');
			$output .= '</form>';
			$output .= "<div class=\"clearfix\"></div>";
			return $output;
		}
		
		function load_submenu_page(){
			$screen = get_current_screen();
 
			if(!is_object($screen))
				return;
				
		 	$per_page_field = $this->get_per_page_field();
				
			$args = array(
				'label' 	=> esc_html__('Per Page', 'niwooims_textdomain'),
				'default'   => 10,
				'option' 	=> $per_page_field
			);
			
			add_screen_option( 'per_page', $args );
		}
		
		function set_screen_option($status, $option, $value) {
			$per_page_field = $this->get_per_page_field();
			if($per_page_field == $option ) return $value;
		}
		
		function get_per_page($default = 10){
			
			$per_page_field = $this->get_per_page_field();
						
			// get the current user ID
			$user = get_current_user_id();
			
			// get the current admin screen
			$screen = get_current_screen();
			
			// retrieve the "per_page" option
			$screen_option = $screen->get_option($per_page_field, 'option');
			
			// retrieve the value of the option stored for the current user
			$per_page = get_user_meta($user, $screen_option, true);
			
			$per_page = isset($per_page[$per_page_field][0]) ? $per_page[$per_page_field][0] : $default;
			
			if ( empty ( $per_page) || $per_page < 1 ) {
				// get the default value if none is set
				$per_page = $screen->get_option($per_page_field, $default);
			}
			
			// now use $per_page to set the number of items displayed			
			return $per_page ;
		}
		
		function get_per_page_field(){
			$admin_page = sanitize_text_field(isset($_GET['page']) ? $_GET['page'] : '');
			$admin_page = str_replace("-","_",$admin_page);
			
			$per_page_field = $admin_page.'_per_page';
			
			return $per_page_field;
		}
		function get_purchase_order_status() {
			$purchase_order_statuses = array(
				'niwopimspo-received'    => _x( 'Received', 'Received', 'niwopims' ),
				
			);
			return apply_filters( 'niwopimspo_purchase_order_statuses', $purchase_order_statuses );
		}
		function get_low_in_stock(){
			global $wpdb;
			$row = array();
			$query = "";
			$stock   = absint( max( get_option( 'woocommerce_notify_low_stock_amount' ), 1 ) );
			$nostock = absint( max( get_option( 'woocommerce_notify_no_stock_amount' ), 0 ) );
		
			$query =  "SELECT COUNT( DISTINCT posts.ID ) as low_in_stock  FROM wp_posts as posts
			INNER JOIN wp_postmeta AS postmeta ON posts.ID = postmeta.post_id
			INNER JOIN wp_postmeta AS postmeta2 ON posts.ID = postmeta2.post_id
			WHERE 1=1
			AND posts.post_type IN ( 'product', 'product_variation' )
			AND posts.post_status = 'publish'
			AND postmeta2.meta_key = '_manage_stock' AND postmeta2.meta_value = 'yes'
			AND postmeta.meta_key = '_stock' AND CAST(postmeta.meta_value AS SIGNED) <= '{$stock}'
			AND postmeta.meta_key = '_stock' AND CAST(postmeta.meta_value AS SIGNED) > '{$nostock}'";
			
			$row = $wpdb->get_var($query);
			
		
			return $row;
			
		}
		function get_out_of_stock(){
			global $wpdb;
			$row = array();
			$query = "";
			$stock = absint( max( get_option( 'woocommerce_notify_no_stock_amount' ), 0 ) );
		
			$query =  "SELECT COUNT( DISTINCT posts.ID ) as out_of_stock FROM wp_posts as posts
			INNER JOIN wp_postmeta AS postmeta ON posts.ID = postmeta.post_id
			INNER JOIN wp_postmeta AS postmeta2 ON posts.ID = postmeta2.post_id
			WHERE 1=1
			AND posts.post_type IN ( 'product', 'product_variation' )
			AND posts.post_status = 'publish'
			AND postmeta2.meta_key = '_manage_stock' AND postmeta2.meta_value = 'yes'
			AND postmeta.meta_key = '_stock' AND CAST(postmeta.meta_value AS SIGNED) <= '{$stock}'";
			
			$row = $wpdb->get_var($query);
			
		
			return $row;
			
		}
		function get_most_stock(){
			global $wpdb;
			$row = array();
			$query = "";
			$stock = absint( max( get_option( 'woocommerce_notify_low_stock_amount' ), 0 ) );
		
			$query =  " SELECT COUNT( DISTINCT posts.ID ) FROM wp_posts as posts
			INNER JOIN wp_postmeta AS postmeta ON posts.ID = postmeta.post_id
			INNER JOIN wp_postmeta AS postmeta2 ON posts.ID = postmeta2.post_id
			WHERE 1=1
			AND posts.post_type IN ( 'product', 'product_variation' )
			AND posts.post_status = 'publish'
			AND postmeta2.meta_key = '_manage_stock' AND postmeta2.meta_value = 'yes'
			AND postmeta.meta_key = '_stock' AND CAST(postmeta.meta_value AS SIGNED) > '{stock}'";
			
			$row = $wpdb->get_var($query);
		
			return $row;
			
		}
		
		function get_user_role(){
			global $wp_roles;
			$roles = $wp_roles->get_names();
		
			return $roles;
		}
		function get_supplier_list($user_id =NULL){
			global $wpdb;
			
			$settings = get_option( 'niwopims_settings' );
			$role = isset($settings["supplier_user_role"])?$settings["supplier_user_role"]:"";
			$query=  "";
		
			
			$query = " SELECT ";
			$query .= " users.ID as user_id  ";
			$query .= " ,users.user_email as user_email  ";
			$query .= " ,first_name.meta_value as first_name  ";
			$query .= " ,last_name.meta_value as last_name  ";
			
			$query .= " FROM	{$wpdb->prefix}users as users  ";
			
			
			$query .= " LEFT JOIN {$wpdb->prefix}usermeta  role ON role.user_id=users.ID ";
			$query .= " LEFT JOIN {$wpdb->prefix}usermeta  first_name ON first_name.user_id=users.ID ";
			$query .= " LEFT JOIN {$wpdb->prefix}usermeta  last_name ON last_name.user_id=users.ID ";
			
			$query .= " WHERE 1 = 1 ";
			$query .= " AND   role.meta_key='{$wpdb->prefix}capabilities'";
			$query .= " AND  role.meta_value   LIKE '%\"{$role}\"%' ";
			
			$query .= " AND   first_name.meta_key='first_name'";
			$query .= " AND   last_name.meta_key='last_name'";
				
			if ($user_id !=NULL){
				$query .= " AND  users.ID = '{$user_id }'";
			}
			$query .= "  ORDER BY first_name.meta_value ASC";
			
			
			$row = $wpdb->get_results($query);
			return $row;
		}
		function check_before_proceed(){
			global $wpdb;	
			$strMessage  = "";

			$settings = get_option( 'niwopims_settings' );

			$online_sales_location = isset($settings["online_sales_location"])?$settings["online_sales_location"]:-1;
			$supplier_user_role = isset($settings["supplier_user_role"])?$settings["supplier_user_role"]:-1;
			
			$niwopims_stock = $wpdb->prefix.'niwopims_stock';

			$query = "SELECT COUNT(*) FROM " .$niwopims_stock;
			$count  = $wpdb->get_var($query);

			$location_name = get_the_title( $online_sales_location);

			$settings_page = admin_url() . 'admin.php?page=niwopims-settings'; 
			$location_page = admin_url() . 'admin.php?page=niwopims-location'; 
			$stock_center_page = admin_url() . 'admin.php?page=niwopims-stock-center&import-stock=yes'; 
			$user_page = admin_url() . 'user-new.php';           
			
			if ($settings =='' ){
				$strMessage .= "<p> Please go to <a href='{$settings_page}'>Setting</a> page, set the  <a href='{$location_page}'>Location</a> , create  <a href='{$user_page}'>Supplier</a>  and then add new purchase order to increase your product stock. </p>";
			}else if (($online_sales_location =='-1')  &&  ($supplier_user_role =='-1')) {
				$strMessage .= "<p>  Please go to <a href='{$settings_page}'>Setting</a> page, set the  <a href='{$location_page}'>Location</a> , create  <a href='{$user_page}'>Supplier</a>  and then add new purchase order to increase your product stock.</p> ";
			}else if ($online_sales_location =='-1') {
				$strMessage .= "<p>  Please go to <a href='{$settings_page}'>Setting</a> page, create  <a href='{$location_page}'>Location</a>  and then add new purchase order to increase your product stock.</p> ";
			}
			else if ($supplier_user_role =='-1') {
				$strMessage .= "<p>  Please go to <a href='{$settings_page}'>Setting</a> page,  create  <a href='{$user_page}'>Supplier</a>  and then add new purchase order to increase your product stock.</p> ";
			}else if ($count ==0) {
				$strMessage .= "<p>  Please go to <a href='{$stock_center_page}'>Stock Center</a> page, To import stock for online sales in location <strong> {$location_name}</strong> </p> ";
			}

			return $strMessage;
		}
    }
}
