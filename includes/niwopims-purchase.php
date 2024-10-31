<?php
if (!defined('ABSPATH')) {
    exit;
}
if (!class_exists('NIWOPIMS_Purchase')) {
    include_once('niwopims-function.php');
    class NIWOPIMS_Purchase extends NIWOPIMS_Function
    {
        function __construct()
        { }
        function manage_purchase()
        {
            $call = $this->get_request("call");
            if ($call == "get_purchase_product") {
                $this->get_purchase_product_table();
            }
			if ($call == "purchase_count") {
			
               $count =  $this->get_purchase_order_count();
			   	
            }
			if ($call == "delete_purchase_product"){
				$this->delete_purchase_product();
			}
        }
        function create_purchase_order()
        {
            global $wpdb;
            $PurchaseProduct = array();
            $data            = array();
            $purchase_id     = 0;
            $PurchaseQuantity = 0;
            $product_id = 0;
            $message         = array();
            $message["status"]  = 1;
          
			$message["message"]  = "Record <strong> saved </strong> successfully";
            $message["purchase_id"]  = $purchase_id;
               
			$niwopims_stock = $wpdb->prefix.'niwopims_stock';
			   
			
			$niwopims_purchase_detail = $wpdb->prefix . "niwopims_purchase_detail";
			
            $niwopims_purchase_header = $wpdb->prefix . "niwopims_purchase_header";
           

            $purchase_date = $this->get_request("purchase_date",$this->get_today_date());
            $purchase_no = $this->get_request("purchase_no",'');
            $purchase_notes = $this->get_request("purchase_notes",'');

            $Location_id = $this->get_request("Location_id",0);
            $supplier_id = $this->get_request("supplier_id",0);
            $status_id = $this->get_request("status_id",0);

            
            $wpdb->query('START TRANSACTION');
            try {
                $data = array();
                $data["purchase_date"]      = $purchase_date;
                $data["purchase_no"]        =  $purchase_no;
                $data["location_id"]        =  $Location_id;
                $data["supplier_id"]        =  $supplier_id ;
                
                $data["status_id"]        =  $status_id ;

                $data["purchase_total"]     = "0";
                $data["purchase_notes"]     =  $purchase_notes;
                $data["created_date"]       = $this->get_today_date();;
                $data["updated_date"]       = $this->get_today_date();;
                $data["created_user_id"]    = $this->get_login_user_id();
                $data["updated_user_id"]    = $this->get_login_user_id();

                $wpdb->insert($niwopims_purchase_header, $data);
                $purchase_id = $wpdb->insert_id;
                $message["purchase_id"]  = $purchase_id;

					
				$PurchaseProduct  				= array_map('sanitize_post', isset($_REQUEST["PurchaseProductList"]) ? $_REQUEST["PurchaseProductList"] : array());
				
				foreach ($PurchaseProduct  as $key => $value) {
                    $data = array();
					
                    $PurchaseQuantity 			= isset($value["PurchaseQuantity"]) ? $value["PurchaseQuantity"] : 0;
                    $product_id 				= isset($value["ProductID"]) ? $value["ProductID"] : 0;

                    $data["purchase_id"]        = $purchase_id;

                    $data["product_id"]         = $product_id;
                    $data["purchase_quantity"]  = $PurchaseQuantity;

                    $data["purchase_price"]     = isset($value["PurchasePrice"]) ? $value["PurchasePrice"] : 0;
                    $data["line_total"]         = isset($value["LineTotal"]) ? $value["LineTotal"] : 0;


                    update_post_meta($product_id, '_manage_stock', 'yes');
                    $orgQty = get_post_meta($product_id, '_stock', true);
					
					
					$PurchaseQuantity = $PurchaseQuantity == "" ? 0 : $PurchaseQuantity;
					$orgQty = $orgQty == "" ? 0 : $orgQty;
					
                    $totalQty =  $orgQty + ($PurchaseQuantity + 0);
                    update_post_meta($product_id, '_stock', sanitize_meta('_manage_stock',$totalQty,'post'));
                    $wpdb->insert($niwopims_purchase_detail, $data);
				}
				/*Update Stock Master*/
				foreach ($PurchaseProduct  as $key => $value) {
					$stock_data = array();
					$PurchaseQuantity =  isset($value["PurchaseQuantity"]) ? $value["PurchaseQuantity"] : 0;
                    $product_id =  isset($value["ProductID"]) ? $value["ProductID"] : 0;
					
					$stock_data["product_id"]         = $product_id;
                    $stock_data["purchase_quantity"]  = $PurchaseQuantity;
					$stock_data["location_id"] 		  = $Location_id;
					
					$query = " SELECT *  FROM " . $niwopims_stock;
					$query .= " WHERE 1=1 ";
					$query .= " AND product_id  =  " .$product_id ;
					$query .= " AND location_id  =  " .$Location_id ;
					
					$row  = $wpdb->get_row($query);
					
				
					$stock_id = isset($row->stock_id)?$row->stock_id:0;
					$m_stock_purchase_quantity = isset($row->purchase_quantity)?$row->purchase_quantity:0;
					$m_stock_balance_quantity = isset($row->balance_quantity)?$row->balance_quantity:0;
					
					if ($stock_id>0){
						
						$m_stock_purchase_quantity = $m_stock_purchase_quantity == "" ? 0 : $m_stock_purchase_quantity;
						$m_stock_balance_quantity  = $m_stock_balance_quantity == ""  ? 0 : $m_stock_balance_quantity;
						$PurchaseQuantity  		   = $PurchaseQuantity == "" 		  ? 0 : $PurchaseQuantity;
					
						
						$stock_data["purchase_quantity"] = $PurchaseQuantity + $m_stock_purchase_quantity;
						 $stock_data["balance_quantity"] = $PurchaseQuantity + $m_stock_balance_quantity;
						$wpdb->update($niwopims_stock, $stock_data,  array(  'stock_id'    => $stock_id  ));
						
					}else{
					  $stock_data["balance_quantity"]  = $PurchaseQuantity;
					  $wpdb->insert($niwopims_stock, $stock_data);
					}
					
					
						
				}
		    } catch (Exception $ex) {
            
                $message["status"]  = 0;
                $message["message"]  =  $ex->getMessage();
                $ex->getMessage();
            }

            if ($purchase_id > 0 &&  $wpdb->last_error == '') {
                $wpdb->query('COMMIT');
            } else {
                $message["status"]  = 0;
                $message["message"]  =  $wpdb->last_error;
                $wpdb->query('ROLLBACK');
            }
            echo json_encode($message);
            die;
        }
        function get_purchase_product_query($purchase_id = 0, $purchase_product_id = 0, $product_id = 0, $start_date = NULL, $end_date = NULL)
        {
            global $wpdb;
            $niwopims_purchase_header = $wpdb->prefix . "niwopims_purchase_header";
            $niwopims_purchase_detail = $wpdb->prefix . "niwopims_purchase_detail";
            $posts = $wpdb->prefix . "posts";

            $data  = array();
            $data["rows"] = array();
            $data["query"] = "";
            $data["status"] = "";
            $strQuery = "";
            $strQuery .= " SELECT ";  
			
			$strQuery .= " purchase_date ";
			$strQuery .= ",  purchase_price ";
			$strQuery .= ",  purchase_quantity ";
			$strQuery .= ",  line_total ";
			$strQuery .= ",  purchase_no ";
			$strQuery .= ",  purchase_no ";
			$strQuery .= ",  location.post_title as location_name ";
			$strQuery .= ",  detail.purchase_product_id ";
			
			
			
			$strQuery .= " FROM " .  $niwopims_purchase_header . " as header";
            $strQuery .= " LEFT JOIN " .  $niwopims_purchase_detail . " as detail ON detail.purchase_id=header.purchase_id ";
            $strQuery .= " LEFT JOIN " .  $posts . " as posts ON posts.ID=detail.product_id ";
			
			$strQuery .= "  LEFT JOIN " .  $posts . " as location ON location.ID=header.location_id ";
			

            $strQuery .= " WHERE 1=1 ";
            if ($purchase_id != 0 && $purchase_id != '') {
                $strQuery .= " AND	header.purchase_id = " . $purchase_id;
            }
            if ($purchase_product_id != 0 && $purchase_product_id != '') {
                $strQuery .= " AND	detail.purchase_product_id = " . $purchase_product_id;
            }
            if ($product_id != 0 && $product_id != '') {
                $strQuery .= " AND	detail.product_id = " . $product_id;
            }
            if ($start_date != NULL && $end_date != NULL) {
                $strQuery .= " AND	header.purchase_date BETWEEN '" . $start_date . "' AND '" . $end_date . "' ";
            }
            $strQuery .= " Order By header.purchase_date DESC";
            $rows =   $wpdb->get_results($strQuery);
            return $rows;
        }
        function get_purchase_product()
        {
            $purchase_id            =  $this->get_request("purchase_id", 0);
            $purchase_product_id    =  $this->get_request("purchase_product_id", 0);
            $product_id             =  $this->get_request("product_id", 0);
            $start_date             =  $this->get_request("start_date", $this->get_today_date());
            $end_date               =  $this->get_request("end_date", $this->get_today_date());


            $rows =  $this->get_purchase_product_query($purchase_id, $purchase_product_id, $product_id, $start_date, $end_date);

            foreach ($rows as $key => $value) {
                $post_id =  $value->product_id;
                $post_meta =  $this->get_product_post_meta($post_id);

                foreach ($post_meta as $pkey => $pValue) {
                    $rows[$key]->$pkey = $pValue;
                }
            }
            return  $rows;
        }
        function get_purchase_product_table()
        {
            $rows =  $this->get_purchase_product();
            $columns =  $this->get_purchase_product_columns();
			
			
			

            ?>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="StartDate"><?php esc_html_e('Start Date', 'niwopims') ; ?></label>
                    <input type="text" class="form-control form-control-sm _datepicker _start_date" id="StartDate"  placeholder="Start Date" value="<?php echo esc_attr($this->get_today_date()); ?>" >
                </div>
                <div class="form-group col-md-6">
                    <label for="EndDate"><?php esc_html_e('End Date', 'niwopims') ; ?> </label>
                    <input type="text" class="form-control form-control-sm _datepicker _end_date" id="EndDate" placeholder="End Date" value="<?php echo esc_attr($this->get_today_date()); ?>" >
                </div>
            </div>
            <table class="table table-sm">

                <thead>
                    <tr>
                        <?php foreach ($columns as $key => $value) : ?>
                            <th>
                                <?php echo esc_html($value);?>
                            </th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rows as $key_row => $value_row) : ?>
                        <tr>
                            <?php foreach ($columns as $key_col => $value_col) : ?>
                                <?php switch ($key_col):
                                                        case "asdsad":
                                                            break; ?>
                                    <?php case "asdsad": ?>
                                    <?php break; ?>
                                    <?php case "delete": ?>
                                     <td> <a href="#" class="_delete_purchase_order" id="<?php echo esc_attr($value_row->purchase_product_id); ?>"><i class="fa fa-trash" aria-hidden="true"></i></a> </td>
                                    <?php break; ?>
                                   
                                    <?php
                                                        default: ?>
                                        <td><?php echo esc_html($value_row->$key_col); ?></td>
                                    <?php endswitch; ?>
                                <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>

            </table>
<?php



        }

         function get_purchase_product_columns()
        {
            $columns  = array();
            $columns["purchase_date"] = esc_html__('Purchase Date', 'niwopims');
			 $columns["location_name"] = esc_html__('Location', 'niwopims');
			$columns["purchase_price"] = esc_html__('Purchase Price', 'niwopims');
            $columns["purchase_quantity"] = esc_html__('Purchase Quantity', 'niwopims');
            $columns["line_total"] = esc_html__('Line Total', 'niwopims');
            $columns["purchase_no"] = esc_html__('purchase no', 'niwopims');
			$columns["delete"] = esc_html__('Delete', 'niwopims');
            
            return $columns;
        }
		function delete_purchase_product(){
			global $wpdb;
			
			$message["status"]  = 1;
            $message["message"]  = "Record <strong> deleted </strong> successfully";
			 
			$niwopims_purchase_detail 	= $wpdb->prefix . "niwopims_purchase_detail";
			$purchase_product_id 		= $this->get_request("purchase_product_id",0);
			
			try {
				$wpdb->query( "DELETE  FROM {$niwopims_purchase_detail} WHERE purchase_product_id = '{$purchase_product_id}'" );
				} 
				catch (Exception $ex ){
					$message["status"]  = 0;
              	 	$message["message"]  =  $ex->getMessage();
              
				}
			echo json_encode( $message);
		       
		}
    }
}
