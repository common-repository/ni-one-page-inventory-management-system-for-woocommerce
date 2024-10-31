<?php
if (!defined('ABSPATH')) {
    exit;
}
if (!class_exists('NIWOPIMS_Stock_List')) {
    include_once('niwopims-function.php');
    class NIWOPIMS_Stock_List extends NIWOPIMS_Function
    {
        function __construct()
        {
        }
        function page_init()
        {
           
?>

            <div class="container-fluid" id="niwopims">
                <div class="card" style="max-width: 1000px;">
                    <div class="card-header bg-rgba-salmon-strong">
                        <?php esc_html_e("Product List", 'niwopims'); ?>
                    </div>
                    <div class="card-body">

                        <form method="post" name="frmProductList" id="frmProductList">
                            <div class="form-row">
                                <div class="form-group col-md-3">
                                    <label for="product_name" class="col-form-label-sm"><?php esc_html_e('Product Name', 'niwopims'); ?> </label>
                                    <input type="text" class="form-control form-control-sm" id="product_name" name="product_name">
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="product_name" class="col-form-label-sm"><?php esc_html_e('SKU', 'niwopims'); ?> </label>
                                    <input type="text" class="form-control form-control-sm" id="product_sku" name="product_sku">
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="product_name" class="col-form-label-sm"><?php esc_html_e('Manage Stock', 'niwopims'); ?> </label>
                                    <select class="form-control form-control-sm" name="manage_stock" id="manage_stock">
                                        <option value="all"><?php esc_html_e('--Select One--', 'niwopims'); ?> </option>
                                        <option value="yes"><?php esc_html_e('Yes', 'niwopims'); ?> </option>
                                        <option value="no"><?php esc_html_e('No', 'niwopims'); ?> </option>
                                    </select>

                                </div>
                                <div class="form-group col-md-3">
                                    <label for="inputEmail4" class="col-form-label-sm"><?php esc_html_e('Allow backorders?', 'niwopims'); ?> </label>
                                    <select id="backorders" name="backorders" class="form-control form-control-sm">
                                        <option value="all"><?php esc_html_e('--Select One--', 'niwopims'); ?> </option>
                                        <option value="no"><?php esc_html_e(' Do not allow', 'niwopims'); ?> </option>
                                        <option value="notify"><?php esc_html_e('Allow, but notify customer', 'niwopims'); ?></option>
                                        <option value="yes"><?php esc_html_e('Allow', 'niwopims'); ?> </option>
                                    </select>
                                </div>
                            </div>

                            <input type="hidden" name="action" value="niwopims_ajax">
                            <input type="hidden" name="sub_action" value="product_list">
                            <input type="hidden" name="p" value="1">
                            <input type="hidden" name="limit" value="<?php echo esc_attr($this->get_per_page(50)); ?>">
                            <input type="hidden" name="total_rows" value="0">

                            <div class="row">
                                <div class="col">
                                    <button type="submit" class="btn btn-primary btn-sm">Search</button>
                                    <button type="button" class="btn btn-secondary btn-sm" id="btnReset">Reset</button>
                                </div>
                            </div>

                        </form>
                        <div>
                            <div class="row ">
                                <div class="col text-right">
                                    <?php $this->display_alphabet(); ?>
                                </div>
                            </div>
                            <div class="row" id="StockListMessage">
                                <div class="col-6">
                                    <div class="alert alert-primary" role="alert" style="display:none">

                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="niwopims_ajax_content"></div>
            </div>

        <?php
        }
        function get_query2()
        {
            global $wpdb;
            $strQuery = "";
            $strQuery .= " SELECT ";
            $strQuery .= " product_meta.product_id ";
            $strQuery .= ",posts.ID ";
            $strQuery .= ",product_meta.sku ";
            $strQuery .= ",product_meta.stock_quantity ";
            $strQuery .= ",product_meta.stock_status ";
            $strQuery .= " FROM {$wpdb->prefix}wc_product_meta_lookup as product_meta ";

            $strQuery .= "  LEFT JOIN  {$wpdb->prefix}posts as posts ON posts.ID=product_meta.product_id ";

            $strQuery .= " WHERE 1=1 ";
            $strQuery .= " AND	posts.post_type ='product' ";

            $rows =   $wpdb->get_results($strQuery);

         
        }

        function get_query($query_type = 'limited_rows')
        {
            $product_parent =          $this->get_product_parent();
            $product_name      = $this->get_request("product_name");
            $product_sku      = $this->get_request("product_sku");
            $manage_stock      = $this->get_request("manage_stock");
            $backorders      = $this->get_request("backorders");
            $p              = $this->get_request("p", 1);
            $limit          = $this->get_request("limit", 10);
			
			$order          = $this->get_request("order", 'ASC');
			$orderby          = $this->get_request("orderby", 'post_title');
			
            global $wpdb;
			
			
			
			$product_variation_sql = " SELECT product_variation.post_parent FROM {$wpdb->posts} AS product_variation WHERE product_variation.post_type IN ('product_variation') AND product_variation.post_parent > 0 GROUP BY  product_variation.post_parent";
			
			$strQuery = "";

            if ($query_type == 'total_rows') {
                $strQuery .= " SELECT COUNT(*) FROM {$wpdb->posts} as posts";
            } else {
                $strQuery .= " SELECT * FROM {$wpdb->prefix}posts as posts";
            }

            /*SKU*/
            if ($product_sku != '') {
                $strQuery .= "  LEFT JOIN  {$wpdb->prefix}postmeta as sku ON sku.post_id=posts.ID ";
            }

            /*manage_stock*/
            if ($manage_stock != 'all') {
                $strQuery .= "  LEFT JOIN  {$wpdb->prefix}postmeta as manage_stock ON manage_stock.post_id=posts.ID ";
            }

            /*backorders*/
            if ($backorders != 'all') {
                $strQuery .= "  LEFT JOIN  {$wpdb->prefix}postmeta as backorders ON backorders.post_id=posts.ID ";
            }


            $strQuery .= " WHERE 1=1 ";
            $strQuery .= "	AND posts.post_type  IN ('product_variation','product') ";
            $strQuery .= "	AND posts.ID NOT IN ('" .  implode("','", $product_parent) . "') ";
            $strQuery .= " AND posts.post_status='publish'";



            if ($product_name != '') {
                $strQuery .= " AND	posts.post_title LIKE '{$product_name}%' ";
            }
            /*SKU*/
            if ($product_sku != '') {
                $strQuery .= " AND sku.meta_value LIKE '%{$product_sku}%' ";
                $strQuery .= " AND sku.meta_key = '_sku'";
            }

            /*manage_stock*/
            if ($manage_stock != 'all') {
                $strQuery .= " AND manage_stock.meta_value =  '{$manage_stock}' ";
                $strQuery .= " AND manage_stock.meta_key = '_manage_stock'";
            }
            /*backorders*/
            if ($backorders != 'all') {
                $strQuery .= " AND backorders.meta_value =  '{$backorders}' ";
                $strQuery .= " AND backorders.meta_key = '_backorders'";
            }
			
			$strQuery .= " AND posts.ID NOT IN ($product_variation_sql)";

            if ($query_type == 'total_rows') {
                $rows =   $wpdb->get_var($strQuery);
            } else {
                
				$start = $p > 0 ? ($p - 1) * $limit : 0;
                
				$strQuery .= " ORDER BY {$orderby} {$order}";
				$strQuery .= " LIMIT {$start}, {$limit}";
                $rows =   $wpdb->get_results($strQuery);
            }
            return $rows;
        }
		
        function get_product_data($query_type = 'limited_rows')
        {
            $rows = $this->get_query($query_type);
            if ($query_type == 'total_rows') {
                return $rows;
            }
  
            foreach ($rows  as $key => $value) {
                $post_id =  $value->ID;
                $post_meta =  $this->get_product_post_meta($post_id);

                foreach ($post_meta as $pkey => $pValue) {
                    $rows[$key]->$pkey = $pValue;
                }
            }
      
            return  $rows;
        }
        function get_table()
        {
           $strMessage  = $this->check_before_proceed();
            $rows = $this->get_product_data('limited_rows');
            $columns = $this->get_columns();
            ?>
            <!-- Button trigger modal -->
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModalCenter" style="display: none;">
                Launch demo modal
            </button>

            <div class="row">
                <?php if ( $strMessage !="") :?>
                <div class="col-md-12 text-left p-1">
                 <p class="pl-2"><?php  echo html_entity_decode($strMessage) ; ?></p> 
                </div>
                <?php else: ?>
                <div class="col-md-12 text-right p-1"> <input type="button" id="btnAddStock" value="Add Stock" class="btn btn-sm btn-primary "></div>
                <?php endif; ?>
            </div>
            
            <div class="table-responsive">
            	<table class="table table-sm  table-striped table-hover" id="TableProductList">

                <thead class="shadow-sm p-3 mb-5 bg-white rounded">
                    <!-- add class="thead-light" for a light header -->
                    <tr>
                        <?php foreach ($columns as $key => $value) : ?>
                            <th class="text-uppercase">
                                <?php  esc_html_e($value);?>
                            </th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($rows) == 0) : ?>
                        <tr>
                            <td colspan="<?php echo esc_attr(count($columns)); ?>"><?php esc_html_e(' No record found', 'niwopims'); ?></td>
                        </tr>
                    <?php endif; ?>
                    <?php foreach ($rows as $key_row => $value_row) : ?>
                        <tr>
                            <?php foreach ($columns as $key_col => $value_col) : ?>
                                <?php switch ($key_col):
                                    case "asdsad":
                                        break; ?>
                                    <?php
                                    case "asdsad": ?>
                                        <?php break; ?>

                                    <?php
                                    case "regular_price": ?>
                                    <?php
                                    case "sale_price": ?>
                                        <td><?php

                                            $sale_price = isset($value_row->$key_col) ? $value_row->$key_col : 0;

                                            $this->get_wc_price($sale_price,array(),false); ?></td>
                                        <?php break; ?>

                                    <?php
                                    case "edit_product": ?>
                                        <td> <a href="#" class="_edit_product" id="<?php echo esc_attr($value_row->ID); ?>"><i class="fa fa-pencil-square" aria-hidden="true"></i></a> </td>
                                        <?php break; ?>
                                    <?php
                                    case "view_purchase": ?>
                                        <td> <a href="#" class="_view_purchase_order" id="<?php echo esc_attr($value_row->ID); ?>"><i class="fa fa-shopping-cart" aria-hidden="true"></i></a> </td>
                                        <?php break; ?>
                                    <?php
                                    case "SelectionCheckBox": ?>
                                        <td><input type="checkbox" class="_select_product" id="<?php echo esc_attr($value_row->ID); ?>" data-product_name="<?php echo esc_attr($value_row->post_title); ?>"> </td>
                                        <?php break; ?>
                                    <?php
                                    default: ?>
                                        <td><?php echo esc_html(isset($value_row->$key_col) ? $value_row->$key_col : ''); ?></td>
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
        function get_columns()
        {
            $columns["SelectionCheckBox"] = esc_html__('Select', 'niwopims');
            $columns["post_title"] = esc_html__('Product Name', 'niwopims');
            $columns["sku"] = esc_html__('SKU', 'niwopims');

            $columns["regular_price"] = esc_html__('Regular Price', 'niwopims');
            $columns["sale_price"] = esc_html__('Sale Price', 'niwopims');

            $columns["stock"] = esc_html__('Stock quantity', 'niwopims');
            $columns["manage_stock"] = esc_html__('Manage stock?', 'niwopims');
            $columns["backorders"] = esc_html__('Allow backorders?', 'niwopims');
            $columns["sold_individually"] = esc_html__('Sold individually', 'niwopims');

            $columns["manage_stock"] = esc_html__('Manage Stock', 'niwopims');
            $columns["edit_product"] = esc_html__('Edit', 'niwopims');
            $columns["view_purchase"] = esc_html__('View Purchase', 'niwopims');

            return $columns;
        }
    }
}
