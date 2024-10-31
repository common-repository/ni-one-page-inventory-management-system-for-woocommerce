<?php
if (!defined('ABSPATH')) {exit;}
if (!class_exists('NIWOPIMS_Settings')) {
    include_once('niwopims-function.php');
    class NIWOPIMS_Settings extends NIWOPIMS_Function
    {
        function __construct(){ }
        function page_init(){
		$location = 	$this->get_location();
        $role = $this->get_user_role();
        $settings = get_option( 'niwopims_settings', array());
        
		//$this->pretty_print($role);
	
        $online_sales_location = isset($settings["online_sales_location"])?$settings["online_sales_location"]:-1;
        $supplier_user_role = isset($settings["supplier_user_role"])?$settings["supplier_user_role"]:-1;
		?>
        <div class="container-fluid" id="niwopims">
                <div class="card">
                    <div class="card-header bg-rgba-salmon-strong">
                        <?php  esc_html_e('Settings', 'niwopims'); ?>
                    </div>
                    <div class="card-body">
                   
                        <form method="post" name="frmSettings" id="frmSettings">
                            <div class="form-row">
                                
                                <div class="form-group col-md-3">
                                    <label for="inputEmail4" class="col-form-label-sm"><?php esc_html_e('Select Supplier User Role', 'niwopims'); ?> </label>
                                    <select id="supplier_user_role" name="niwopims_settings[supplier_user_role]" class="form-control form-control-sm">
                                    	 <option value="-1"><?php esc_html_e('--Select One--', 'niwopims'); ?> </option>
										<?php foreach($role as $key=>$value): ?>
                                          <option value="<?php esc_html_e(  $key ); ?>"   <?php  echo esc_html(($supplier_user_role == $key)?'selected="selected"' : ''); ?>   > <?php esc_html_e(  $value ); ?> </option>  
                                        <?php endforeach;?>
                                    </select>
                                            
                                </div>
                                
                                <div class="form-group col-md-3">
                                    <label for="inputEmail4" class="col-form-label-sm"><?php esc_html_e('Online Sales Location', 'niwopims'); ?> </label>
                                  
                                    <select id="online_sales_location" name="niwopims_settings[online_sales_location]" class="form-control form-control-sm">
                                    	 <option value="-1"><?php esc_html_e('--Select One--', 'niwopims'); ?> </option>
										<?php foreach($location as $key=>$value): ?>
                                          <option value="<?php esc_html_e(  $key ); ?>"   <?php  echo esc_html(($online_sales_location == $key)?'selected="selected"' : ''); ?>   > <?php esc_html_e(  $value ); ?> </option>  
                                        <?php endforeach;?>
                                    </select>
                                   <?php $settings_url = admin_url() . 'admin.php?page=niwopims-location'; ?>             
                                    <?php if (count($location)==0) : ?>
                                    <a href="<?php esc_html_e( $settings_url);  ?>"><?php esc_html_e("Add New Location",'niwopims'); ?> </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <input type="hidden" name="action" value="niwopims_ajax">
                            <input type="hidden" name="sub_action" value="save_settings">
                            <div class="row">
                            	<div class="col">
                            	<button type="submit" class="btn btn-primary btn-sm"> <?php  esc_html_e('Save', 'niwopims'); ?> </button>
								<button type="button" class="btn btn-secondary btn-sm" id="btnReset"><?php  esc_html_e('Reset', 'niwopims'); ?></button>
                                </div>
                            </div>
                            
                        </form>
                    </div>
                </div>

                <div class="niwopims_ajax_content"></div>
            </div>
        <?php	
		}
		function ajax_init(){
			
			$settings = array_map('sanitize_post',isset($_REQUEST["niwopims_settings"])?$_REQUEST["niwopims_settings"]:array());
			update_option( 'niwopims_settings', $settings );
			esc_html_e("Record Saved",'niwopims');
			die;
		}
	}
}