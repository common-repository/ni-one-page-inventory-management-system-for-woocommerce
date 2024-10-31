<?php
if (!defined('ABSPATH')) {   exit;}
if (!class_exists('NIWOPIMS_Stock_Center')) {
    include_once('niwopims-function.php');
    class NIWOPIMS_Stock_Center extends NIWOPIMS_Function
    {
        function __construct() { }
        function page_init() {
			$location = $this->get_location();
			?>
		  <div class="container-fluid" id="niwopims">
                <div class="card">
                   <div class="card-header bg-rgba-salmon-strong">
                      <?php esc_html_e("Stock Center",'niwopims'); ?> 
                    </div>
                    <div class="card-body">
                   
                        <form method="post" name="frmStockCenter" id="frmStockCenter">
                            <div class="form-row">
                                <div class="form-group col-md-3">
                                    <label for="product_name" class="col-form-label-sm"><?php esc_html_e('Product Name', 'niwopims'); ?> </label>
                                    <input type="text" class="form-control form-control-sm" id="product_name" name="product_name">
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="product_name" class="col-form-label-sm"><?php esc_html_e('SKU', 'niwopims'); ?> </label>
                                    <input type="text" class="form-control form-control-sm" id="product_sku" name="product_sku" >
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="product_name" class="col-form-label-sm"><?php esc_html_e('Location Name', 'niwopims'); ?> </label>
                                    <select id="location_id" name="location_id" class="form-control form-control-sm">
                                    	 <option value="-1"><?php esc_html_e('--Select One--', 'niwopims'); ?> </option>
										<?php foreach($location as $key=>$value): ?>
                                          <option value="<?php esc_html_e(  $key ); ?>"     > <?php esc_html_e(  $value ); ?> </option>  
                                        <?php endforeach;?>
                                    </select>
                                     <?php $settings_url = admin_url() . 'admin.php?page=niwopims-location'; ?>             
                                    <?php if (count($location)==0) : ?>
                                    <a style="font-size:12px;" href="<?php esc_html_e( $settings_url);  ?>"><?php esc_html_e("Add New Location",'niwopims'); ?> </a>
                                    <?php endif; ?>

                                </div>
                            </div>
                            
                            <input type="hidden" name="action" value="niwopims_ajax">
                            <input type="hidden" name="sub_action" value="stock_center">
                            <input type="hidden" name="p" value="1">
                            <input type="hidden" name="limit" value="<?php echo esc_attr($this->get_per_page(50));?>">
                            <input type="hidden" name="total_rows" value="0">
                            
                            <div class="row">
                            	<div class="col">
                            	<button type="submit" class="btn btn-primary btn-sm"><?php echo esc_html('Search', 'niwopims');?></button>
								<button type="button" class="btn btn-secondary btn-sm" id="btnReset"><?php echo esc_html('Reset', 'niwopims');?></button>
                                </div>
                            </div>
                            
                        </form>
                        
                    </div>
                </div>

                <div class="niwopims_ajax_content"></div>
            </div>
		<?php	

		}
		
		function get_product_data($query_type = 'limited_rows')
        {
            $rows = $this->get_query($query_type);
            if ($query_type == 'total_rows') {
                return $rows;
            }           
            return  $rows;
        }
		
		function get_query($query_type = 'limited_rows'){
			global $wpdb;
			$niwopims_stock = $wpdb->prefix.'niwopims_stock';

			$product_sku    = $this->get_request("product_sku");	
			$product_name   = $this->get_request("product_name");	
			$location_id    =  $this->get_request("location_id");
			$p              = $this->get_request("p", 1);
            $limit          = $this->get_request("limit", 10);	

			$query = "";
			$query = "SELECT ";

			$query .= " stock.product_id as product_id ";
			$query .= ", stock.location_id as location_id ";
			$query .= ", SUM(stock.purchase_quantity) as purchase_quantity ";
			$query .= ", SUM(stock.sales_quantity) as sales_quantity ";
			$query .= ", SUM(stock.balance_quantity) as balance_quantity ";
			$query .= ", posts.post_title as product_name ";
		
			$query .= " FROM " . $niwopims_stock ." as stock";

			   $query .= "  LEFT JOIN  {$wpdb->prefix}posts as posts ON posts.ID=stock.product_id ";
			
	       if ($product_sku !=''){
	          $query .= "  LEFT JOIN  {$wpdb->prefix}postmeta as sku ON posts.ID=sku.post_id ";
		   }

			$query .= " WHERE 1=1 ";
			$query .= " AND posts.post_type IN('product','product_variation') ";
			if ($product_sku !=''){
			   $query .= " AND sku.meta_value LIKE '%{$product_sku}%' ";
			    $query .= " AND sku.meta_key ='_sku' ";	
			}
			

			if ($product_name !=''){
				$query .= " AND	posts.post_title LIKE '%{$product_name}%' ";	
		    }

			if ($location_id !=-1){
				$query .= " AND	stock.location_id =  '{$location_id}' ";	
			}
			
			
			$query .= " GROUP BY  stock.product_id, stock.location_id  ";

			
			
			if ($query_type == 'total_rows') {
                $rows = $wpdb->get_results($query);
				$rows = count($rows);
            } else {
                $start = $p > 0 ? ($p - 1) * $limit : 0;
                $query .= " LIMIT {$start}, {$limit}";
                $rows =   $wpdb->get_results($query);
            }
			
		
			
			return $rows;
		}
		function get_columns(){
			$columns = array();
			$columns["product_name"] 			= esc_html__("Name","niwopims");
			$columns["product_sku"] 			= esc_html__("SKU","niwopims");
			$columns["location_id"] 		= esc_html__("Location Name","niwopims");
			$columns["purchase_quantity"] 	= esc_html__("Purchase Quantity","niwopims");
			$columns["sales_quantity"] 		= esc_html__("Sales Quantity","niwopims");	
			$columns["balance_quantity"] 	= esc_html__("Balance Quantity","niwopims");	
			return $columns;
		}
		function get_ajax(){
			$call = $this->get_request("call");
			if ($call  =="import_existing_product"){
				$this->get_import_existing_product();
			}else{
				$this->get_table();
			}
			
			die;
		}
		function get_table(){
			$columns = $this->get_columns();
			$rows = $this->get_product_data('limited_rows');?>
			
			<?php  if ((isset($_REQUEST["import-stock"]) && $_REQUEST["import-stock"] =="yes") ||  (count(	$rows) == 0) ): ?>
			<div class="row  pb-1" id="Import" >
				<div class="col-8 text-left">
				<div class="alert alert-primary" role="alert" style="display:none">
                                      This is a primary alert—check it out!
                                    </div>
				</div>
				<div class="col text-right"><input type="button" id="btnImport" value="Import" class="btn btn-primary btn-sm"></div>
			</div>	
			<?php  endif; ?>
			<div class="table-responsive" style="padding-top:20px; ">
			
			<table class="table table-sm table-bordered table-striped  table-hover">
				<thead class="shadow-sm p-3 mb-5 bg-white rounded">
                	<tr>
                	<?php foreach($columns as $key=>$value): ?>
                    	

							<?php switch ($key):
								case "asdsad": break; ?>
							<?php case "purchase_quantity": ?>
							<?php case "sales_quantity": ?>
							<?php case "balance_quantity": ?>
							<th class="text-right"><?php esc_html_e($value);  ?></th>
							<?php break; ?>
							<?php default: ?>
                           	<th><?php esc_html_e( isset($value)?$value:''); ?></th>
                            <?php endswitch; ?>			
                    <?php endforeach; ?>
                	</tr>
                </thead>
                <tbody>
                	<?php foreach($rows as $row_key=>$value_row): ?>
                    	<tr>
                        	<?php foreach ($columns as $key_col => $value_col) : ?>
                                <?php switch ($key_col):
                                     case "asdsad": break; ?>
                                    <?php case "asdsad": ?>
                                    <?php break; ?>

									<?php case "product_sku": ?>
										<?php $td_value = isset($value_row->product_id)?$value_row->product_id:''; ?>
										<td><?php echo esc_html(get_post_meta($td_value,'_sku', true) ) ; ?></td>
									<?php break; ?>			

                                    <?php case "location_id": ?>
									<td><?php echo esc_html(get_the_title( isset($value_row->$key_col)?$value_row->$key_col:'')); ?></td>
                                    <?php break; ?>
									<?php case "product_id": ?>
                                    <td><?php echo esc_html(get_the_title( isset($value_row->$key_col)?$value_row->$key_col:'')); ?></td>
                                    <?php break; ?>

									<?php case "purchase_quantity": ?>
									<?php case "sales_quantity": ?>
									<?php case "balance_quantity": ?>
										<td style="text-align: right"><?php echo esc_html( isset($value_row->$key_col)?$value_row->$key_col:''); ?></td>
                                    <?php break; ?>		


									<?php default: ?>
                                        <td><?php esc_html_e( isset($value_row->$key_col)?$value_row->$key_col:''); ?></td>
                                    <?php endswitch; ?>
                                <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
			</div>            
            <?php
			
			$requests = array_map('sanitize_post', $_REQUEST);
            $total_rows = isset($requests['total_rows']) ? $requests['total_rows'] : 0;
            if ($total_rows <= 0) {
                $total_rows = $this->get_product_data('total_rows');
                $requests['total_rows'] = $total_rows;
            }

            print($this->get_the_pagination($requests));
		}
		function get_import_product(){
			global $wpdb;
			

			$product_parent =          $this->get_product_parent();

			$query = "";
			$query = "SELECT ";
			$query .= " posts.ID as product_id  ";
			$query .= ", stock.meta_value as stock  ";
			$query .= " FROM {$wpdb->prefix}posts as posts";
			
			$query .= " LEFT JOIN {$wpdb->prefix}postmeta as manage_stock ON manage_stock.post_id=posts.ID ";
			$query .= " LEFT JOIN {$wpdb->prefix}postmeta as stock_status ON stock_status.post_id=posts.ID ";
			$query .= " LEFT JOIN {$wpdb->prefix}postmeta as stock ON stock.post_id=posts.ID ";

			$query .= " WHERE 1=1 ";
			$query .= " AND  posts.post_type  IN ('product_variation','product') ";
			$query .= "	AND posts.ID NOT IN ('" .  implode("','", $product_parent) . "') ";
			$query .= " AND posts.post_status='publish'";
			
			$query .= " AND manage_stock.meta_key='_manage_stock'";
			$query .= " AND manage_stock.meta_value='yes'";

			$query .= " AND stock_status.meta_key='_stock_status'";
			$query .= " AND stock_status.meta_value='instock'";

			$query .= " AND stock.meta_key='_stock'";
		

			
			$rows = $wpdb->get_results($query);
			return $rows;

		}
		function get_import_existing_product(){
			global $wpdb;
			$data  =  array();
			$rows = $this->get_import_product();
		
			
			$niwopims_stock = $wpdb->prefix.'niwopims_stock';

			$settings = get_option( 'niwopims_settings' );
        	$online_sales_location = isset($settings["online_sales_location"])?$settings["online_sales_location"]:-1;
        	foreach($rows as $key=>$value){

				$data["product_id"] 		= $value->product_id;
				$data["location_id"] 		= $online_sales_location;
				$data["purchase_quantity"] 	= $value->stock;
				$data["balance_quantity"] 	= $value->stock;
				$wpdb->insert($niwopims_stock , $data);

			}
			
		}
	}
}