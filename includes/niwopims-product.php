<?php
if (!defined('ABSPATH')) {
    exit;
}
if (!class_exists('NIWOPIMS_Product')) {
    include_once('niwopims-function.php');
    class NIWOPIMS_Product extends NIWOPIMS_Function
    {
        function __construct()
        { }
        function manage_product()
        {
            $call = $this->get_request("call");
            if ($call == "edit_product") {
                $this->get_edit_product_table();
            }
            if ($call== "update_product"){
                $this->update_product();
            }
        }
        function get_edit_product_table2()
        { }
        function get_edit_product_table()
        {
            $product_id = $this->get_request("product_id");
            $post_title = sanitize_text_field(get_the_title($product_id));
            $columns = $this->get_product_postmeta_columns();

            ?>
            <table class="table table-sm">
                <tr>
                    <td><?php esc_html_e('Post Title', 'niwopims'); ?></td>
                    <td> <input type="text" value="<?php echo esc_attr($post_title);?>" name="post_title"> </td>
                </tr>
                <?php foreach ($columns as $key => $value) : ?>

                    <?php switch ($key):
                      case "asdsad": break; ?>
                    
                    <?php case "_manage_stock": ?>
                     <?php $mvalue =  sanitize_text_field( get_post_meta(  $product_id , $key , true));   ?>
                    <tr>
                        <td><?php echo esc_html($value); ?></td>
                        <td> 
                            <select class="form-control form-control-sm <?php echo esc_attr($key); ?>" name="<?php echo esc_attr($key); ?>">
                                <option value="yes" <?php echo ($mvalue == 'yes') ? 'selected' : ''; ?>>yes</option>
                                <option value="no" <?php echo ($mvalue == 'no') ? 'selected' : ''; ?>>no</option>
                            </select>
                        </td>
                    </tr>   
                    
                    <?php break; ?>

                    <?php case "_sold_individually": ?>
                     <?php $mvalue =  sanitize_text_field( get_post_meta(  $product_id , $key , true));   ?>
                    <tr>
                        <td><?php echo esc_html($value); ?></td>
                        <td> 
                            <select class="form-control form-control-sm  <?php echo esc_attr($key); ?>" name="<?php echo esc_attr($key); ?>" >
                                <option value="yes" <?php echo ($mvalue == 'yes') ? 'selected' : ''; ?>>yes</option>
                                <option value="no" <?php echo ($mvalue == 'no') ? 'selected' : ''; ?>>no</option>
                            </select>
                        </td>
                    </tr>   
                    
                    <?php break; ?>


                    <?php case "_backorders": ?>
                     <?php $mvalue =  sanitize_text_field( get_post_meta(  $product_id , $key , true));   ?>
                    <tr>
                        <td><?php echo esc_html($value); ?></td>
                        <td> 
                            <select class="form-control form-control-sm <?php echo esc_attr($key); ?>" name="<?php echo esc_attr($key); ?>">
                                <option value="no" <?php echo ($mvalue == 'yes') ? 'selected' : ''; ?>>Do not allow</option>
                                <option value="notify" <?php echo ($mvalue == 'no') ? 'selected' : ''; ?>>Allow, but notify customer</option>
                                <option value="yes" <?php echo ($mvalue == 'no') ? 'selected' : ''; ?>>Allow</option>
                            </select>
                        </td>
                    </tr>   
                    
                    <?php break; ?>

                    <?php case "_stock": ?>
                        <tr>
                        <td><?php echo esc_html($value); ?></td>
                        <td> <input type="number" name="<?php echo esc_attr($key); ?>" class="form-control form-control-sm <?php echo esc_attr($key); ?>" value="<?php echo get_post_meta(  $product_id , $key , true); ?>"> </td>
                    </tr>
                    <?php break; ?>

                    <?php case "_regular_price": ?>
                    <?php case "_sale_price": ?>    
                        <tr>
                        <td><?php echo esc_html($value); ?></td>
                        <td> <input type="text" name="<?php echo esc_attr($key); ?>" class="form-control form-control-sm _allownumericwithdecimal <?php echo esc_attr($key); ?>" value="<?php echo get_post_meta(  $product_id , $key , true); ?>"> </td>
                    </tr>
                    <?php break; ?>


                    <?php default: ?>  

                    <tr>
                        <td><?php echo esc_html($value); ?></td>
                        <td> <input type="text" name="<?php echo esc_attr($key); ?>" class="form-control form-control-sm <?php echo esc_attr($key); ?>" value="<?php echo get_post_meta(  $product_id , $key , true); ?>"> </td>
                    </tr>
                    <?php endswitch; ?>

                   
                <?php endforeach; ?>



            </table>
            <?php
        }
        function get_product_postmeta_columns()
        {
            $columns  = array();
            $columns["_sku"] = esc_html__('SKU', 'niwopims');
            $columns["_regular_price"] = esc_html__('Regular Price', 'niwopims');
            $columns["_sale_price"] = esc_html__('Sale Price', 'niwopims');
          
            $columns["_stock"] = esc_html__('Stock quantity', 'niwopims');
            $columns["_manage_stock"] = esc_html__('Manage stock?', 'niwopims');
            $columns["_backorders"] = esc_html__('Allow backorders?', 'niwopims');
            $columns["_sold_individually"] = esc_html__('Sold individually', 'niwopims');

            
            return $columns;
        }


        function update_product(){

            $product_id = $this->get_request("hdProductID");
            $post_title = $this->get_request("post_title");
            $columns =   $this->get_product_postmeta_columns();

            $message         = array();
            $message["status"]  = 1;
            $message["message"]  = "Saved";
        
            try {

                foreach($columns as $key => $value): 
                    if (isset($_REQUEST[ $key ])){
                      $mvalue = sanitize_text_field(  $_REQUEST[ $key ]);
                      update_post_meta(  $product_id , $key , $mvalue );
                  }
                  endforeach; 
                  $product_post = array(
                      'ID' =>      $product_id,
                      'post_title'    => $post_title,
                  );
      
                  wp_update_post($product_post, true);

            } catch (Exception $ex) {
                $message["status"]  = 0;
                $message["message"]  =  $ex->getMessage();
                $ex->getMessage();
            }
            
           

            echo json_encode( $message );

        }
    }
}
