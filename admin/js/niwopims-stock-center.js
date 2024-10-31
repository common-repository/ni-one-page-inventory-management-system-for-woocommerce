var JData = {};
jQuery(document).ready(function ($) {
    
    $("#TableProductList").tablesorter({
		theme : "bootstrap",
	  
		widthFixed: true,
	
		// widget code contained in the jquery.tablesorter.widgets.js file
		// use the zebra stripe widget if you plan on hiding any rows (filter widget)
		// the uitheme widget is NOT REQUIRED!
		widgets : [ "filter", "columns", "zebra" ],
	
		widgetOptions : {
		  // using the default zebra striping class name, so it actually isn't included in the theme variable above
		  // this is ONLY needed for bootstrap theming if you are using the filter widget, because rows are hidden
		  zebra : ["even", "odd"],
	
		  // class names added to columns when sorted
		  columns: [ "primary", "secondary", "tertiary" ],
	
		  // reset filters button
		  filter_reset : ".reset",
	
		  // extra css class name (string or array) added to the filter element (input or select)
		  filter_cssFilter: [
			'form-control',
			'form-control',
			'form-control custom-select', // select needs custom class names :(
			'form-control',
			'form-control',
			'form-control',
			'form-control'
		  ]
	
		}
	  });

    /*By Default load the Produtc*/
	jQuery(document).on('submit','form#frmStockCenter, form#commanFormPagination',function(event){

        $.ajax({
			url: niwopims_ajax_object.niwopims_ajaxurl,
			data: $(this).serialize(),
			success: function (response) {
			
				$(".niwopims_ajax_content").html(response);
				
			},
			error: function (errorThrown) {
				console.log(errorThrown);
				//alert("e");
			}
        });
        event.preventDefault();
	});
	
	$("#frmStockCenter").trigger("submit");
	
	jQuery(document).on('click','ul.pagination a',function(event){
		var p = jQuery(this).attr("data-p");
		jQuery("form#commanFormPagination").find("input[name='p']").val(p);
		jQuery("form#commanFormPagination").trigger('submit');
		event.preventDefault();
	});

	/*btnReset*/
	$("#btnReset").click(function(event){
		event.preventDefault();
		//alert("dasdas");
		//$('#configform')[0].reset();
		$("#frmStockCenter").trigger("reset");
		$("#frmStockCenter").trigger("submit");
	});
	/*
	$(document).on('click','#btnImport2',function(event){
		
		event.preventDefault();
		JData = {};
		JData["action"] = "niwopims_ajax";
		JData["sub_action"] = "stock_center";
		JData["call"] = "import_existing_product";

		$.ajax({
			url: niwopims_ajax_object.niwopims_ajaxurl,
			data: JData,
			success: function (response) {
				alert(JSON.stringify(response));
				//$(".niwopims_ajax_content").html(response);
				
			},
			error: function (response) {
				alert(JSON.stringify(response));
				console.log(errorThrown);
				//alert("e");
			}
        });

	});
	*/

	jQuery(document).on('click','#btnImport',function(){
		if (jQuery("#location_id").val() =="-1"){
			alert("Please select the location");
			
			return false;	
		}

		jQuery(".alert","#Import").show();
		jQuery(".alert","#Import").html('Please wait...');
		JData = {};
		JData["action"] = "niwopims_ajax";
		JData["sub_action"] = "stock_center";
		JData["call"] = "import_existing_product";

		jQuery.ajax({
			url: niwopims_ajax_object.niwopims_ajaxurl,
			data: JData,
			success: function (response) {
				//alert(JSON.stringify(response));
				//$(".niwopims_ajax_content").html(response);
				
				jQuery(".alert","#Import").delay(3000).fadeOut('slow');
				window.setTimeout(function(){location.reload()},5000)
				//jQuery("#frmStockCenter").trigger("submit");

			},
			error: function (response) {
				jQuery(".alert","#Import").text('');
				jQuery(".alert","#Import").hide();
				//alert(JSON.stringify(response));
				console.log(JSON.stringify(response));
				//alert("e");
			}
        });
	});
});