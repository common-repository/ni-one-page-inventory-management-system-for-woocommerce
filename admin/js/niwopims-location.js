var JData = {};
var location_id = 0;
var JMessage = [];
var delay = 3000; 
jQuery(document).ready(function ($) {
	
	get_loaction();
	
	$("#frmLocation").submit(function (event) {
		
		
		if ( $.trim( $("#location_name").val()) === ''){
			AddClassAlertBoxByParent("alert-danger","#frmLocation")
			jQuery(".alert","#frmLocation").text("Enter Location Name");
			$("#location_name").focus();
			return false;
		}
		
		
		$.ajax({
			url: niwopims_ajax_object.niwopims_ajaxurl,
			data: $(this).serialize(),
			success: function (response) {
					//alert(JSON.stringify(response));
				//$(".niwopims_ajax_content").html(response);
				JMessage = JSON.parse(response);
				if (JMessage["status"] === 1) {
					AddClassAlertBoxByParent("alert-success","#frmLocation")
					jQuery(".alert","#frmLocation").html(JMessage["message"]);
				}else{
					
					AddClassAlertBoxByParent("alert-danger","#frmLocation")
				}
				 setTimeout(function(){ 
					jQuery(".alert","#frmLocation").text('');
					jQuery(".alert","#frmLocation").hide();
					
				 }, delay);
				
				get_loaction();
			},
			error: function (errorThrown) {
				console.log(errorThrown);
				//alert("e");
			}
		});
		return false;
	});

	$(document).on("keypress", "#location_name", function(event){
		jQuery(".alert","#frmLocation").hide();
	});

	$(document).on("click", "._edit", function(event){
		location_id = $(this).attr("id");
		//alert(location_id);

		JData = {};
		JData["action"] 		= "niwopims_ajax";
		JData["sub_action"] 	= "manage_location";
		JData["call"] 			= "edit_location";
		JData["location_id"] 	= location_id;

		jQuery.ajax({
			url: niwopims_ajax_object.niwopims_ajaxurl,
			data:JData,
			success: function (response) {
					
				//jQuery(".niwopims_ajax_content").html(response);
				//alert(JSON.stringify(response));


				jQuery("._hd_location_id","#frmLocation").val(location_id);

				$.each( JSON.parse( response), function(key, value){
						//alert(value.ID);

						$("#location_name").val(value.post_title);
						$("#location_code").val(value.location_code);
						$("#address_line").val(value.address_line);
						$("#address_line2").val(value.address_line2);
						$("#city").val(value.city);
						$("#zip_code").val(value.zip_code);
						$("#state").val(value.state);
						$("#country").val(value.country);
						$("#contact_person_first_name").val(value.contact_person_first_name);
						$("#contact_person_last_name").val(value.contact_person_last_name);
						$("#email_address").val(value.email_address);
						$("#contact_no").val(value.contact_no);
						if ("yes" == value.is_active){
							$( "#is_active" ).prop( "checked", true );
						}else{
							$( "#is_active" ).prop( "checked", false );
						}
						
				});

			},
			error: function (response) {
				console.log(response);
				alert(JSON.stringify(response));
				//alert("e");
			}
		});


		event.preventDefault();
	});



	

});

function get_loaction(){

		JData = {};
		JData["action"] 		= "niwopims_ajax";
		JData["sub_action"] 	= "manage_location";
		JData["call"] 			= "get_location_list";
		//JData["location_id"] 	= location_id;

	jQuery.ajax({
		url: niwopims_ajax_object.niwopims_ajaxurl,
		data:JData,
		success: function (response) {
				
			jQuery(".niwopims_ajax_content").html(response);
			//alert(JSON.stringify(response));
		},
		error: function (response) {
			console.log(response);
			alert(JSON.stringify(response));
			//alert("e");
		}
	});
}
function AddClassAlertBoxByParent(addClass, alertboxdiv){
		
		jQuery(".alert",alertboxdiv).removeClass('alert-success');
		jQuery(".alert",alertboxdiv).removeClass('alert-info');
		jQuery(".alert",alertboxdiv).removeClass('alert-warning');
		jQuery(".alert",alertboxdiv).removeClass('alert-danger');
		jQuery(".alert",alertboxdiv).removeClass('alert-primary');
		jQuery(".alert",alertboxdiv).removeClass('alert-secondary');
		jQuery(".alert",alertboxdiv).removeClass('alert-light');
		jQuery(".alert",alertboxdiv).removeClass('alert-dark');	
		jQuery(".alert",alertboxdiv).addClass(addClass).show();
						
}

