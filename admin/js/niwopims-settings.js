jQuery(document).ready(function ($) {
	$("#frmSettings").submit(function (event) {
		event.preventDefault();
		
		
		
		if ($("#supplier_user_role").val() == "-1"){
			alert("Select Supplier User Role");
		
			return false;	
		}
		
		if ($("#online_sales_location").val() == "-1"){
			alert("Please select location");
		
			return false;	
		}
		
		$.ajax({
			url: niwopims_ajax_object.niwopims_ajaxurl,
			data: $(this).serialize(),
			success: function (response) {
				alert(JSON.stringify(response));
				//$(".niwopims_ajax_content").html(response);
				
			},
			error: function (response) {
				alert(JSON.stringify(response));
				//alert("e");
			}
		});
		
	});
});