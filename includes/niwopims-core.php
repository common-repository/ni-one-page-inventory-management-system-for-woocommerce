<?php
if (!defined('ABSPATH')) {
    exit;
}
if (!class_exists('NIWOPIMS_Core')) {
    include_once('niwopims-function.php');
    class NIWOPIMS_Core extends NIWOPIMS_Function
    {
       /*Defind vars*/
		var $vars = NULL;
		
		function __construct($vars = array()){
			
			/*Assing var*/
			$this->vars = $vars;
			           
            add_action('admin_menu',  array(&$this, 'admin_menu'),110);
            add_action('admin_enqueue_scripts',  array(&$this, 'admin_enqueue_scripts'));
            add_action('wp_ajax_niwopims_ajax',  array(&$this, 'niwopims_ajax')); /*used in form field name="action" value="my_action"*/

            add_action('admin_footer',  array(&$this, 'admin_footer'));
			add_filter('set-screen-option', 	array(&$this,'set_screen_option'),101,3);
			
			
		}
		function admin_menu()
        {
            add_menu_page(
                esc_html__('Ni Inventory', 'niwopims'),
                esc_html__('Ni Inventory', 'niwopims'),
                'manage_options',
                'niwopims-dashboard',
                array(&$this, 'add_page'),
                'dashicons-chart-pie',
                59.14
            );

            add_submenu_page(
                'niwopims-dashboard',
                esc_html__('Dashboard', 'niwopims'),
                esc_html__('Dashboard', 'niwopims'),
                'manage_options',
                'niwopims-dashboard',
                array(&$this, 'add_page')
            );


            $niwopims_stock_list = add_submenu_page(
                'niwopims-dashboard',
                esc_html__('Product List', 'niwopims'),
                esc_html__('Product List', 'niwopims'),
                'manage_options',
                'niwopims-stock-list',
                array(&$this, 'add_page')
            );
			
			$admin_page = sanitize_text_field(  isset($_GET['page']) ? $_GET['page'] : '');
					
			
			 add_submenu_page(
                'niwopims-dashboard',
                esc_html__('Location', 'niwopims'),
                esc_html__('Location', 'niwopims'),
                'manage_options',
                'niwopims-location',
                array(&$this, 'add_page')
            );
			
			$niwopims_stock_center = add_submenu_page(
                'niwopims-dashboard',
                esc_html__('Stock Center', 'niwopims'),
                esc_html__('Stock Center', 'niwopims'),
                'manage_options',
                'niwopims-stock-center',
                array(&$this, 'add_page')
            );
			
			
			add_submenu_page(
                'niwopims-dashboard',
                esc_html__('Settings', 'niwopims'),
                esc_html__('Settings', 'niwopims'),
                'manage_options',
                'niwopims-settings',
                array(&$this, 'add_page')
            );
			
			if($admin_page){
				switch($admin_page){
					case "niwopims-stock-center":
						add_action("load-".$niwopims_stock_center, array(&$this,'load_submenu_page'));
						break;
					case "niwopims-stock-list":
						add_action("load-".$niwopims_stock_list, array(&$this,'load_submenu_page'));
						break;
				}
			}
			
			
        }
        function admin_enqueue_scripts($hook)
        {
            $page =    $this->get_request("page");
            if ($page == "niwopims-stock-list" || $page == "niwopims-location" || $page  =="niwopims-settings" 
            || $page =="niwopims-stock-center" || $page =="niwopims-dashboard") {
				
				$file_vars = $this->vars['__FILE__'];

                wp_enqueue_script('jquery-ui-datepicker');
                // You need styling for the datepicker. For simplicity I've linked to the jQuery UI CSS on a CDN.
                wp_register_style('niwopims-jquery-ui', plugins_url('/admin/css/lib/jquery-ui.css', $file_vars));
                wp_enqueue_style('niwopims-jquery-ui');
				

                wp_enqueue_script('niwopims-script', plugins_url('/admin/js/script.js', $file_vars), array('jquery'));
                wp_localize_script('niwopims-script', 'niwopims_ajax_object', array('niwopims_ajaxurl' => admin_url('admin-ajax.php')));


                wp_register_style('niwopims-bootstrap-css', plugins_url('/admin/css/lib/bootstrap.min.css', $file_vars));
                wp_enqueue_style('niwopims-bootstrap-css');


                wp_register_style('niwopims-font-awesome-css', plugins_url('/admin/css/font-awesome.min.css', $file_vars));
                wp_enqueue_style('niwopims-font-awesome-css');


                wp_register_style('niwopims-style-css', plugins_url('/admin/css/niwopims-style.css', $file_vars));
                wp_enqueue_style('niwopims-style-css');
				
				wp_enqueue_script('niwopims-bootstrap-script', plugins_url('admin/js/lib/bootstrap.min.js', $file_vars));
				
				if ( $page == 'niwopims-stock-list' ) {
                		
                        wp_enqueue_script('niwopims-stock-script', plugins_url('/admin/js/niwopims-script.js', $file_vars), array('jquery'));                        
						wp_enqueue_script('niwopims-tablesorter-script', plugins_url('/admin/js/tablesorter/jquery.tablesorter.min.js', $file_vars), array('jquery'));
						wp_enqueue_script('niwopims-tablesorter-widgets-script', plugins_url('/admin/js/tablesorter/jquery.tablesorter.widgets.min.js', $file_vars), array('jquery'));
						
						/*tablesorter css*/
						wp_register_style('niwopims-tablesorter-css', plugins_url('/admin/css/tablesorter/theme.bootstrap_4.css', $file_vars));
	    	            wp_enqueue_style('niwopims-tablesorter-css');
							
				}
				
				if ( $page == 'niwopims-settings') {
					wp_enqueue_script('niwopims-settings-script', plugins_url('/admin/js/niwopims-settings.js', $file_vars), array('jquery'));
                }
				
                if ( $page == 'niwopims-stock-center') {
						wp_enqueue_script('niwopims-stock-center-script', plugins_url('/admin/js/niwopims-stock-center.js', $file_vars), array('jquery'));
						wp_enqueue_script('niwopims-tablesorter-script', plugins_url('/admin/js/tablesorter/jquery.tablesorter.min.js', $file_vars), array('jquery'));
						wp_enqueue_script('niwopims-tablesorter-widgets-script', plugins_url('/admin/js/tablesorter/jquery.tablesorter.widgets.min.js', $file_vars), array('jquery'));
						
						/*tablesorter css*/
						wp_register_style('niwopims-tablesorter-css', plugins_url('/admin/css/tablesorter/theme.bootstrap_4.css', $file_vars));
						wp_enqueue_style('niwopims-tablesorter-css');
				}
				
				if ($page == "niwopims-location") {
					wp_enqueue_script('niwopims-location-script', plugins_url('/admin/js/niwopims-location.js', $file_vars), array('jquery'));	
				}
				
            }
        }
        function niwopims_ajax()
        {
            $sub_action  = $this->get_request("sub_action");
          
            if ($sub_action  == "product_list") {
                include_once("niwopims-stock-list.php");
                $obj = new NIWOPIMS_Stock_List();
                $obj->get_table();
            }
            if ($sub_action  == "create_purchase_product") {
                include_once("niwopims-purchase.php");
                $obj =  new  NIWOPIMS_Purchase();
                $obj->create_purchase_order();
            }
            if ($sub_action  == "manage_purchase") {
                include_once("niwopims-purchase.php");
                $obj =  new  NIWOPIMS_Purchase();
                $obj->manage_purchase();
            }
            if ($sub_action  == "manage_product") {
                include_once("niwopims-product.php");
                $obj =  new  NIWOPIMS_Product();
                $obj->manage_product();
            }
			if ($sub_action  =="manage_location"){
				 include_once("niwopims-location.php");
                $obj = new NIWOPIMS_Location();
                $obj->manage_location();
			}
			if ($sub_action == "save_settings") {
                include_once("niwopims-settings.php");
                $obj = new NIWOPIMS_Settings();
                $obj->ajax_init();
            }
            if ($sub_action =="stock_center"){
                include_once("niwopims-stock-center.php");
                $obj = new NIWOPIMS_Stock_Center();
                $obj->get_ajax();
               
            }

            die;
        }
        function add_page()
        {
            $page = $this->get_request("page");
            if ($page == "niwopims-dashboard") {
                include_once("niwopims-dashboard.php");
                $obj = new NIWOPIMS_Dashboard();
                $obj->page_init();
            }
            if ($page == "niwopims-stock-list") {
                include_once("niwopims-stock-list.php");
                $obj = new NIWOPIMS_Stock_List();
                $obj->page_init();
            }
			if ($page == "niwopims-location") {
                include_once("niwopims-location.php");
                $obj = new NIWOPIMS_Location();
                $obj->page_init();
            }
			if ($page == "niwopims-settings") {
                include_once("niwopims-settings.php");
                $obj = new NIWOPIMS_Settings();
                $obj->page_init();
            }
			if ($page =="niwopims-stock-center"){
				include_once("niwopims-stock-center.php");
                $obj = new NIWOPIMS_Stock_Center();
                $obj->page_init();
			}
        }
        function admin_footer()
        {
            $page = $this->get_request("page");

            if ($page != 'niwopims-stock-list') {
                return;
            }
            $location = $this->get_location();
            $status = $this->get_purchase_order_status();
           $supplier =  $this->get_supplier_list();
            ?>
            <!-- Selected Product Model Popup -->
            <div class="modal fade" id="PurchaseOrderModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable  modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLongTitle"><?php esc_html_e('Purchase Order', 'niwopims'); ?></h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-row">
                                <div class="form-group col-md-2">
                                    <label for="PurchaseDate"><?php esc_html_e('Purchase Date', 'niwopims'); ?></label>
                                    <input type="text" class="form-control form-control-sm _datepicker _purchase_date" placeholder="Purchase Date" value="<?php echo esc_html($this->get_today_date()); ?>">
                                </div>
                                <div class="form-group col-md-2">
                                    <label for="PurchaseNo"><?php esc_html_e('Purchase No', 'niwopims'); ?></label>
                                    <input type="text" class="form-control form-control-sm  _purchase_no" placeholder="Enter Purchase No" value="<?php echo esc_html($this->get_purchase_order_count()); ?>">
                                </div>

                                <div class="form-group col-md-3">
                                    <label for="Location_id"><?php esc_html_e('Location', 'niwopims'); ?></label>
                                    <select class="form-control form-control-sm  _Location_id" name="Location_id">
                                          <option value="-1"><?php esc_html_e('Select one location', 'niwopims'); ?></option>
                                          
                                       		<?php foreach(	$location as $key=>$value): ?>	
                                          
                                            <option value="<?php echo esc_attr($key);?>"> <?php echo esc_html($value); ?></option>
                                            <?php endforeach; ?>	
                                       
                                    </select>
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="supplier_id"><?php esc_html_e('Supplier', 'niwopims'); ?></label>
                                    <select class="form-control form-control-sm  _supplier_id" name="supplier_id">
                                       <option value="-1"><?php esc_html_e('Select one supplier', 'niwopims'); ?></option>
                                    <?php foreach(	$supplier as $key=>$value): ?>	
                                          
                                          <option value="<?php echo esc_attr($value->user_id)?>"> <?php esc_html_e( $value->last_name . ", " . $value->first_name)  ; ?></option>
                                          <?php endforeach; ?>	
                                    </select>
                                </div>
                                <div class="form-group col-md-2">
                                    <label for="status_id"><?php esc_html_e('Status', 'niwopims'); ?></label>
                                    <select class="form-control form-control-sm  _status_id" name="status_id">
                                        <?php foreach(	$status  as $key=>$value): ?>	
                                          
                                          <option value="<?php echo esc_attr($key);?>"><?php echo esc_html($value); ?></option>
                                          <?php endforeach; ?>	
                                    </select>
                                </div>

                            </div>
							<div class="_purchase_order_modal_contect"></div>
                            <div class="row">
                                <div class="col">
                                    <textarea class="form-control form-control-sm purchase_notes" placeholder="<?php esc_html_e('Enter Purchase Notes', 'niwopims'); ?>"></textarea>
                                </div>

                            </div>
                        </div>
                        <div class="modal-footer d-block">
                           
                           <div class="row">
                           	<div class="col-md-8 text-left">
                                 <div class="alert alert-primary" role="alert" style="display:none">
                                  
                                </div>
                            </div>
                            	<div class="col-md-4 text-right">
                            		 <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php esc_html_e('Close', 'niwopims'); ?> </button>
                           			 <button type="button" class="btn btn-primary" id="btnCreatePurchaseOrder"><?php esc_html_e('Save changes', 'niwopims'); ?> </button>
                            	</div>
                           </div>
                           
                           
                            
                           
                            
                        </div>
                    </div>
                </div>
            </div>

            <!-- Selected Product Model Popup -->
            <div class="modal fade" id="PurchaseProductModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable  modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLongTitle"><?php esc_html_e('Purchase Product History', 'niwopims'); ?></h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                        	<div class="_purchase_product_modal_content"></div>
                        </div>
                        <div class="modal-footer  d-block">
                        	<div class="row">
                                <div class="col-md-8 text-left">
                                     <div class="alert alert-primary" role="alert" style="display:none">
                                      This is a primary alert—check it out!
                                    </div>
                                </div>
                                <div class="col-md-4 text-right">
                                <input type="hidden" name="hdProductID" class="_hd_product_id" value="0">
                                         <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php esc_html_e('Close', 'niwopims'); ?></button>
                                      <button type="button" class="btn btn-primary" id="btnSearchPurchaseProduct"><?php esc_html_e('Search', 'niwopims'); ?></button>
                              
                                </div>
                           </div>
                        </div>
                    </div>
                </div>
            </div>

            <!--Edit Product-->
            <div class="modal fade" id="EditProductModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                <form method="post" name="frmUpdateProduct" id="frmUpdateProduct">
                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable  modal-lg" role="document">

                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLongTitle"><?php esc_html_e('Edit Product', 'niwopims'); ?></h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="_edit_product_modal_content"></div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php esc_html_e('Close', 'niwopims'); ?></button>
                                <button type="submit" class="btn btn-primary" id="btnEditProduct"><?php esc_html_e('Save changes', 'niwopims'); ?></button>
                                <input type="hidden" name="hdProductID" class="_hd_product_id" value="0">
                                <input type="hidden" name="action" value="niwopims_ajax">
                                <input type="hidden" name="sub_action" value="manage_product">
                                <input type="hidden" name="call" value="update_product">
                            </div>
                        </div>
                </form>
            </div>
            </div>
<?php
        }
    }
}
?>