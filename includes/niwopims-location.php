<?php
if (!defined('ABSPATH')) {
    exit;
}
if (!class_exists('NIWOPIMS_Location')) {
    include_once('niwopims-function.php');
    class NIWOPIMS_Location extends NIWOPIMS_Function
    {
        function __construct()
        { }
        function page_init()
        {
            ?>
           <div class="container-fluid" id="niwopims">
                <div class="row">
                    <div class="col" >
                        <div class="card mw-100" >
                            <div class="card-header bg-rgba-salmon-strong">
                            <?php esc_html_e("Location",'niwopims'); ?> 
                            </div>
                            <div class="card-body">
                                <form id="frmLocation" name="frmLocation" method="post" autocomplete="off">
                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                            <label for="location_name"><?php esc_html_e('Location Name', 'niwopims'); ?></label>
                                            <input type="text" name="location_name" id="location_name" class="form-control form-control-sm">
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="location_code"><?php esc_html_e('Location Code', 'niwopims'); ?></label>
                                            <input type="text" name="location_code" id="location_code" class="form-control form-control-sm">
                                        </div>

                                    </div>

                                    <hr>

                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                            <label for="address_line"><?php esc_html_e('Address Line', 'niwopims'); ?></label>
                                            <input type="text" name="address_line" id="address_line" class="form-control form-control-sm">
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="address_line2"><?php esc_html_e('Address Line 2', 'niwopims'); ?></label>
                                            <input type="text" name="address_line2" id="address_line2" class="form-control form-control-sm">
                                        </div>

                                    </div>

                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                            <label for="city"><?php esc_html_e('City', 'niwopims'); ?></label>
                                            <input type="text" name="city" id="city" class="form-control form-control-sm">
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="zip_code"><?php esc_html_e('Zip Code', 'niwopims'); ?></label>
                                            <input type="text" name="zip_code" id="zip_code" class="form-control form-control-sm">
                                        </div>

                                    </div>


                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                            <label for="state"><?php esc_html_e('State', 'niwopims'); ?></label>
                                            <input type="text" name="state" id="state" class="form-control form-control-sm">
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="country"><?php esc_html_e('County', 'niwopims'); ?></label>
                                            <input type="text" name="country" id="country" class="form-control form-control-sm">
                                        </div>

                                    </div>
                                    <hr>


                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                            <label for="contact_person_first_name"><?php esc_html_e('First Name', 'niwopims'); ?></label>
                                            <input type="text" name="contact_person_first_name" id="contact_person_first_name" class="form-control form-control-sm">
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="contact_person_last_name"><?php esc_html_e('Last Name', 'niwopims'); ?></label>
                                            <input type="text" name="contact_person_last_name" id="contact_person_last_name" class="form-control form-control-sm">
                                        </div>

                                    </div>

                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                            <label for="email_address"><?php esc_html_e('Email Adress', 'niwopims'); ?></label>
                                            <input type="text" name="email_address" id="email_address" class="form-control form-control-sm">
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="contact_no"><?php esc_html_e('Contact No', 'niwopims'); ?></label>
                                            <input type="text" name="contact_no" id="contact_no" class="form-control form-control-sm">
                                        </div>

                                    </div>

                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                            <label for="state"><?php esc_html_e('Is Active', 'niwopims'); ?></label>
                                            <input type="checkbox" name="is_active" id="is_active" class="form-control form-control-sm" checked>
                                        </div>


                                    </div>

									<div class="row">
                                    	<div class="col-md-8 text-left">
                                        	<div class="alert alert-danger" role="alert" style=" display:none">Fixed  error marked in red colour.</div>
                                        </div>
                                        <div class="col-md-4 text-right">
                                        	 <button type="submit" class="btn btn-sm btn-primary" id="btnManageLocation"><?php esc_html_e('Save changes', 'niwopims'); ?></button>
                                    	     <button type="reset" class="btn btn-sm btn-primary" id="btnManageLocation"><?php esc_html_e('Reset', 'niwopims'); ?></button>
                                        </div>
                                    </div>
									
                                   
                                    
                                    <input type="hidden" name="hdLocationID" class="_hd_location_id" value="0">
                                    <input type="hidden" name="action" value="niwopims_ajax">
                                    <input type="hidden" name="sub_action" value="manage_location">
                                    <input type="hidden" name="call" value="save_location">
                                  
                                                    

                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card mw-100">
                            <div class="card-header bg-rgba-salmon-strong">
                            <?php esc_html_e("Location List",'niwopims'); ?> 
                            </div>
                            <div class="card-body">

                                
                              <div class="niwopims_ajax_content"></div>
                              

                            </div>
                        </div>
                    </div>

                </div>
            </div>
<?php
        }

        function manage_location(){
         
            
        	 $call  = $this->get_request("call");
            if ("save_location" == $call  ){
				
                $this->save_location();  
            }
            if ("get_location_list"== $call){
              
                $this->get_location_table();
            }
            if ( $call  =="edit_location"){
                $this->edit_location();
            }
			die;
        }
        function save_location() {

          
			$message         = array();
            $message["status"]  = 0;
            $message["message"]  = "Location saved.";
			
            $hdLocationID               = $this->get_request("hdLocationID",0);
		    $location_name              = $this->get_request("location_name");
            $location_code              = $this->get_request("location_code");
            $address_line               = $this->get_request("address_line");
            $address_line2              = $this->get_request("address_line2");
            $city                       = $this->get_request("city");
            $zip_code                   = $this->get_request("zip_code");
            $state                      = $this->get_request("state");
            $country                    = $this->get_request("country");
            $contact_person_first_name  = $this->get_request("contact_person_first_name");
            $contact_person_last_name   = $this->get_request("contact_person_last_name");
            $email_address              = $this->get_request("email_address");
            $contact_no                 = $this->get_request("contact_no");
            $is_active                  = isset($_REQUEST["is_active"]) ? "yes" : "no";
            
			
			if ($hdLocationID ==0){
				// insert the post and set the category
				$post_id = wp_insert_post(array(
					'post_type' => 'niwopims_location',
					'post_title' => $location_name,
					
					'post_status' => 'publish',
					'comment_status' => 'closed',   // if you prefer
					'ping_status' => 'closed',      // if you prefer
				), true);
	
			}else{
				$post_id = $hdLocationID ;
				$post_id = wp_update_post(array(
					'ID' =>  $post_id,
					'post_type' => 'niwopims_location',
					'post_title' => $location_name,
					
					'post_status' => 'publish',
					'comment_status' => 'closed',   // if you prefer
					'ping_status' => 'closed',      // if you prefer
				), true);
			}

            
            if ($post_id) {
                // insert post meta
                update_post_meta($post_id, '_location_code', $location_code);
                update_post_meta($post_id, '_address_line', $address_line);
                update_post_meta($post_id, '_address_line2', $address_line2);
                update_post_meta($post_id, '_city', $city);
                update_post_meta($post_id, '_zip_code', $zip_code);
				update_post_meta($post_id, '_state', $state);
                update_post_meta($post_id, '_country', $country);
                update_post_meta($post_id, '_contact_person_first_name', $contact_person_first_name);
                update_post_meta($post_id, '_contact_person_last_name', $contact_person_last_name);
                update_post_meta($post_id, '_email_address', $email_address);
                update_post_meta($post_id, '_contact_no', $contact_no);
                update_post_meta($post_id, '_is_active', $is_active);
            }
			$message["status"]  = 1;
            echo json_encode($message);
            die;
        }
        function get_location($location_id = 0){
            global $wpdb;
            $strQuery = "";
            $strQuery .= " SELECT * FROM {$wpdb->prefix}posts as posts";
            $strQuery .= " WHERE 1=1 ";
            $strQuery .= " AND	posts.post_type ='niwopims_location' ";

            if ($location_id !=0 && $location_id>0 ) {
                $strQuery .= " AND	posts.ID =" . $location_id ;
            }   

            $rows =   $wpdb->get_results($strQuery);


            foreach(  $rows  as $key=>$value){
                $post_id =  $value->ID;
                $post_meta =  $this->get_product_post_meta($post_id );

                foreach($post_meta as $pkey=>$pValue){
                    $rows[$key]->$pkey = $pValue;
                }
           }
           return  $rows;

        }
        function get_location_table(){
            $location_id = $this->get_request("location_id",0);
            $columns = $this->get_location_columns();

            $rows = $this->get_location(  $location_id );
            ?>
            <table class="table table-sm">
                <thead>
                    <tr>
                        <?php foreach(  $columns as $key=>$value): ?>
                            <th><?php echo esc_html($value); ?></th>
                        <?php endforeach;?>
                       
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
                                    <?php
                                        case "edit": ?>
                                          <td> <a href="#" class="_edit" id="<?php echo esc_attr($value_row->ID); ?>" ><i class="fa fa-pencil" aria-hidden="true"></i></a>  </td>
                                        <?php break; ?>
                                    <?php default: ?>
                                        <td><?php echo esc_html($value_row->$key_col); ?></td>
                                    <?php endswitch; ?>
                                <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php    
           die;
        }
        function get_location_columns(){
           
            $columns["post_title"] = esc_html__('Location', 'niwopims');
            $columns["location_code"] = esc_html__('Code', 'niwopims');
           
            $columns["city"] = esc_html__('City', 'niwopims');
            $columns["zip_code"] = esc_html__('Zip code', 'niwopims');
            $columns["country"] = esc_html__('Country', 'niwopims');
            $columns["contact_person_first_name"] = esc_html__('First Name', 'niwopims');
           
            $columns["email_address"] = esc_html__('Email Address', 'niwopims');
            $columns["contact_no"] = esc_html__('Contact No', 'niwopims');
            $columns["is_active"] = esc_html__('Is Active', 'niwopims');
            $columns["edit"] = esc_html__('Edit', 'niwopims');
            return $columns;
        }
		
        function edit_location(){
            $location_id = $this->get_request("location_id",0);
            $rows =  $this->get_location($location_id);

            echo json_encode( $rows );
            die;
        }
		
		
      

    }
}
