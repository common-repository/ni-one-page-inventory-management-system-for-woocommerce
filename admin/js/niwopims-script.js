var JSelectedProductList = [];
var JPurchaseProductList = [];
var JMessage = [];
var JData = {};
var obj;
var strHTML = '';
var PurchaseQuantity = 0;
var PurchasePrice = 0;
var LineTotal = 0;
var ProductID = 0;
var delay = 3000; 
var tablesorter_option = {};
jQuery(document).ready(function ($) {

	// $( "._datepicker" ).datepicker({
	// 	changeMonth: true,
	// 	changeYear: true
	//   });

	$('body').on('focus',"._datepicker", function(){
		$(this).datepicker({
			dateFormat: 'yy-mm-dd',
			changeMonth: true,
			changeYear: true,
			showButtonPanel: true
		});
	});
	//tablesorter_option  = {theme : "bootstrap"};
	//jQuery("#TableProductList").tablesorter(tablesorter_option);

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

    jQuery(document).on('submit','form#frmProductList, form#commanFormPagination',function(event){
		//$(".niwopims_ajax_content").html("Please wait..");
		$.ajax({
			url: niwopims_ajax_object.niwopims_ajaxurl,
			data: $(this).serialize(),
			success: function (response) {
			//	alert( JSON.stringify( JSelectedProductList));
				count = Object.keys(JSelectedProductList).length;
				//	alert(JSON.stringify(response));
				$(".niwopims_ajax_content").html(response);
				$('#TableProductList').tablesorter().trigger('update');
				
				/*Reselect The selected Checkbox*/
				if (count>0)
				{
					jQuery('#TableProductList > tbody  > tr').each(function () {
						var tmpthis = $(this);
						var tmpProductID = jQuery(this).find("._select_product").attr("id");
						$.each(JSelectedProductList, function(key, value){
							if (parseInt(tmpProductID) === parseInt(value.ProductID)){
								$(tmpthis).find("input[type=checkbox]").attr("checked", true);
							}
						});
					});
				}
				
			},
			error: function (errorThrown) {
				console.log(errorThrown);
				//alert("e");
			}
		});
		return false;
	});
	
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
		$("#frmProductList").trigger("reset");
		$("#frmProductList").trigger("submit");
	});

	

	/*By Default load the Produtc*/
	$("#frmProductList").trigger("submit");

	//	$("#btnTest").click(function (event) {
	$(document).on("click", "#btnAddStock", function (event) {
		event.preventDefault();
		
	
		var count = Object.keys(JSelectedProductList).length;
		if (count<=0){
			//alert("Please select the product");

			jQuery(".alert","#StockListMessage").show();
			jQuery(".alert","#StockListMessage").html("Please select the product for purchase order and then click add stock button");
			jQuery(".alert","#StockListMessage").delay(5000).fadeOut('slow');
			
			
			setTimeout(function() {
				
			}, 5000);
			
			return false;
		}
	

		/*Get Total Purchase order count and used as purchase order no*/
		JData = {};
		JData["action"] = "niwopims_ajax";
		JData["sub_action"] = "manage_purchase";
		JData["call"] = "purchase_count";
		
		jQuery.ajax({
			url: niwopims_ajax_object.niwopims_ajaxurl,
			data: JData,
			success: function (response) {
				//alert(response);
				jQuery("._purchase_no","#PurchaseOrderModal").val(response);
			},
			error: function (response) {
				console.log(response);
				//alert("e");
			}
		});
		
		
		jQuery("._purchase_order_modal_contect",'#PurchaseOrderModal').html('');
		$(".alert",'#PurchaseOrderModal').hide();
		GetSelectedProductTable();
		
		$('#PurchaseOrderModal').modal('show');
		

	});

	/*Select/Unselct the product from product list*/
	//$('._select_product').change(function (event) {
	$(document).on("change", " ._select_product", function (event) {
		event.preventDefault();

		var tmpProductID = $(this).attr("id");

		if (this.checked) {
			//alert($(this).attr("id"));
			//alert($(this).attr("data-product_name"));
			obj = new Object();
			obj.ProductID = tmpProductID;
			obj.ProductName = $(this).attr("data-product_name");
			obj.ProductSalesPrice = "10";
			obj.ProductCostPrice = "10";
			obj.ProductSKU = "10";
			JSelectedProductList.push(obj);
			//alert(JSON.stringify(JSelectedProductList));
		} else {
			jQuery(JSelectedProductList).each(function (index) {
				if (JSelectedProductList[index].ProductID == tmpProductID) {
					JSelectedProductList.splice(index, 1); // This will remove the object that first name equals to Test1
					return false; // This will stop the execution of jQuery each loop.
				}
			});

		}
		//alert(JSON.stringify(JSelectedProductList));
	});


	/*Calculate Line  Total On KeyUp */
	$(document).on("keyup blur", "._purchase_price, ._purchase_quantity", function (event) {
		//$(document).on("keypress keyup blur","._purchase_price, ._purchase_quantity",function (event) {
		event.preventDefault();
		CalculateLineTotal();
	});

	/*Only Decimal value with one decimal point */
	$(document).on("keyup blur", "._allownumericwithdecimal", function (event) {
		//$("._allownumericwithdecimal").on("keypress keyup blur", function (event) {
		//this.value = this.value.replace(/[^0-9\.]/g,'');
		$(this).val($(this).val().replace(/[^0-9\.]/g, ''));
		if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
			event.preventDefault();
		}
	});


	/*Create Purchase Order*/
	$(document).on("click", "#btnCreatePurchaseOrder", function (event) {
		event.preventDefault();
		if (is_validate()){
			CreatePurchaseOrder();
		}else{
			AddClassAlertBoxByParent("alert-danger","#PurchaseOrderModal");
			jQuery(".alert","#PurchaseOrderModal").text("Fixed  error marked in red colour.");
		}
		
	});


	/*View Purchase Order*/
	$(document).on("click", "._view_purchase_order", function (event) {
		event.preventDefault();
		//alert($(this).attr("id"));

		ProductID = $.trim($(this).attr("id"));

		//alert(ProductID);
		
		JData = {};
		JData["action"] = "niwopims_ajax";
		JData["sub_action"] = "manage_purchase";
		JData["call"] = "get_purchase_product";
		JData["product_id"] = ProductID;

		jQuery.ajax({
			url: niwopims_ajax_object.niwopims_ajaxurl,
			data: JData,
			success: function (response) {
				
				//PurchaseProductModal
				jQuery("._purchase_product_modal_content").html(response);
				$('#PurchaseProductModal').modal('show');
				//console.log(response);

				jQuery("._hd_product_id","#PurchaseProductModal").val(ProductID);

			

			},
			error: function (errorThrown) {
				console.log(errorThrown);
				//alert("e");
			}
		});


	});
	

	//_view_edit_product

	$(document).on("click", "._edit_product", function (event) {
		event.preventDefault();
		//alert($(this).attr("id"));

		ProductID = $.trim($(this).attr("id"));

		JData = {};
		JData["action"] = "niwopims_ajax";
		JData["sub_action"] = "manage_product";
		JData["call"] = "edit_product";
		JData["product_id"] = ProductID;

		jQuery.ajax({
			url: niwopims_ajax_object.niwopims_ajaxurl,
			data: JData,
			success: function (response) {
				
				//PurchaseProductModal
				jQuery("._edit_product_modal_content").html(response);
				jQuery("._hd_product_id","#EditProductModal").val(ProductID);
				$('#EditProductModal').modal('show');
				console.log(response);

				
			

			},
			error: function (errorThrown) {
				console.log(errorThrown);
				//alert("e");
			}
		});


	});

	//btnEditProduct
	$(document).on("submit", "#frmUpdateProduct", function (event) {
		event.preventDefault();
		//alert(	jQuery("._hd_product_id","#EditProductModal").val());


		//ProductID = jQuery("._hd_product_id","#EditProductModal").val();

		JData = {};
		JData["action"] = "niwopims_ajax";
		JData["sub_action"] = "manage_product";
		JData["call"] = "update_product";
		JData["product_id"] = ProductID;

		jQuery.ajax({
			url: niwopims_ajax_object.niwopims_ajaxurl,
			//data: JData,
			data: $(this).serialize(),
			success: function (response) {
					
				JMessage = JSON.parse(response);
				if (JMessage["status"] === 1) {
					jQuery("#frmProductList").trigger("submit");
					jQuery('#EditProductModal').modal('hide');
					
				}
			

			},
			error: function (errorThrown) {
				console.log(errorThrown);
				//alert("e");
			}
		});


	});

	//Search btnSearchPurchaseProduct
	$(document).on("click", "#btnSearchPurchaseProduct", function (event) {
		event.preventDefault();
		//alert(	jQuery("._hd_product_id","#PurchaseProductModal").val());
		//alert(	jQuery("._start_date","#PurchaseProductModal").val());
		//alert(	jQuery("._end_date","#PurchaseProductModal").val());



		JData = {};
		JData["action"] = "niwopims_ajax";
		JData["sub_action"] = "manage_purchase";
		JData["call"] = "get_purchase_product";
		JData["product_id"] = jQuery("._hd_product_id","#PurchaseProductModal").val();
		JData["start_date"] = 	jQuery("._start_date","#PurchaseProductModal").val()
		JData["end_date"] = jQuery("._end_date","#PurchaseProductModal").val()
	
	
		jQuery.ajax({
			url: niwopims_ajax_object.niwopims_ajaxurl,
			data: JData,
			success: function (response) {
				
				//PurchaseProductModal
				jQuery("._purchase_product_modal_content").html(response);
				$('#PurchaseProductModal').modal('show');
				console.log(response);

				jQuery("._hd_product_id","#PurchaseProductModal").val(ProductID);

			

			},
			error: function (errorThrown) {
				console.log(errorThrown);
				//alert("e");
			}
		});



	});

	$(document).on("click", "._alphabet", function (event) {
		event.preventDefault();
		//alert($(this).attr("id"));

		$("#product_name").val($(this).attr("id"));
			/*By Default load the Produtc*/
		$("#frmProductList").trigger("submit");

	});
	
	$(document).on("click", "._delete_purchase_order", function (event) {
		event.preventDefault();
		alert($(this).attr("id"));

		JData = {};
		JData["action"] 			 = "niwopims_ajax";
		JData["sub_action"] 		 = "manage_purchase";
		JData["call"] 				 = "delete_purchase_product";
		JData["purchase_product_id"] =  $(this).attr("id");

		jQuery.ajax({
			url: niwopims_ajax_object.niwopims_ajaxurl,
			data: JData,
			success: function (response) {
				//alert(JSON.stringify(response));
				
				JMessage = JSON.parse(response);
				//alert(JSON.stringify(JMessage));
				if (JMessage["status"] === 1) {
					AddClassAlertBoxByParent("alert-success","#PurchaseProductModal")
					
					jQuery(".alert","#PurchaseProductModal").html(JMessage["message"]);
				}else{
					
					AddClassAlertBoxByParent("alert-danger","#PurchaseProductModal")
				}
				
				 setTimeout(function(){ 
					jQuery('#PurchaseProductModal').modal('hide');
					jQuery(".alert","#PurchaseProductModal").text('');
					jQuery(".alert","#PurchaseProductModal").hide();
					
				 }, delay);
				
				
			},
			error: function (response) {
				console.log(response);
			}
		});
	
	});
});
/**
 * Create HTML table structure for selected product model popup
 */
function GetSelectedProductTable() {

	strHTML = '';
	strHTML += '<div class="table table-responsive-sm"></div>';
	strHTML += '<table  class="table table-sm" id="SelectedProductTable">';
	strHTML += '<thead>';
	strHTML += '<tr>'
	strHTML += '<th>Product Name</th>';
	strHTML += '<th>Purchase Quantity </th>';
	strHTML += '<th>Purchase Price </th>';
	strHTML += '<th>Line Total</th>';
	strHTML += '</tr>';
	strHTML += '</thead>';
	strHTML += '<tbody>';
	jQuery.each(JSelectedProductList, function (key, value) {
		strHTML += '<tr>';
		strHTML += '<td> <input type="hidden" name="ProductID" class="form-control form-control-sm  _product_id" value=' + value.ProductID + '> ' + value.ProductName + ' </td>';
		strHTML += '<td> <input type="number" name="PurchaseQuantity" class="form-control form-control-sm _purchase_quantity"> </td>';
		strHTML += '<td> <input type="text" name="PurchasePrice" class = "form-control form-control-sm _purchase_price _allownumericwithdecimal"> </td>';
		strHTML += '<td> <input type="text" name="LineTotal" class = "form-control form-control-sm _line_total" readonly > </td>';
		strHTML += '</td>';
		//alert(value.ProductID);
	});
	strHTML += '</tr>';
	strHTML += '</table>';
	strHTML += '</div>';

	jQuery("._purchase_order_modal_contect").html(strHTML);
}

function CalculateLineTotal() {
	JPurchaseProductList = [];
	jQuery('#SelectedProductTable > tbody  > tr').each(function () {

		ProductID 			= jQuery.trim(jQuery(this).find("._product_id").val());
		PurchaseQuantity 	= jQuery.trim(jQuery(this).find("._purchase_quantity").val());
		PurchasePrice 		= jQuery.trim(jQuery(this).find("._purchase_price").val());
		if (!jQuery.isNumeric(PurchaseQuantity)) {
			PurchaseQuantity = 0;
			jQuery(this).find("._purchase_quantity").addClass("border-error");
		}else{
			jQuery(this).find("._purchase_quantity").removeClass("border-error");
		}
		if (!jQuery.isNumeric(PurchasePrice)) {
			PurchasePrice = 0;
			jQuery(this).find("._purchase_price").addClass("border-error");
		}else{
				jQuery(this).find("._purchase_price").removeClass("border-error");
		}
		if (!jQuery.isNumeric(ProductID)) {
			ProductID = 0;
		}


		LineTotal = (PurchaseQuantity * PurchasePrice);
		jQuery.trim(jQuery(this).find("._line_total").val(LineTotal));


		/*Add Product value to JSON Object*/
		if (ProductID > 0) {

			obj = new Object();
			obj.ProductID = ProductID;
			obj.PurchasePrice = PurchasePrice;
			obj.PurchaseQuantity = PurchaseQuantity;
			obj.LineTotal = LineTotal;
			JPurchaseProductList.push(obj);
		}



	});
}
function is_validate(){
	var is_valid  = true;
	try{
		
		var tmp_supplier_id = jQuery("._supplier_id","#PurchaseOrderModal").val();
		var tmp_Location_id = jQuery("._Location_id","#PurchaseOrderModal").val();
		
		
		if (tmp_supplier_id ==="-1" ){
			//	alert(tmp_supplier_id);
			jQuery("._supplier_id","#PurchaseOrderModal").addClass("is-invalid");	
			is_valid  =false;
		}else{
			jQuery("._supplier_id","#PurchaseOrderModal").removeClass('is-invalid');		
		}
		if (tmp_Location_id ==="-1" ){
			//	alert(tmp_supplier_id);
			jQuery("._Location_id","#PurchaseOrderModal").addClass("is-invalid");	
			is_valid  =false;
		}else{
			jQuery("._Location_id","#PurchaseOrderModal").removeClass('is-invalid');		
		}
		
		jQuery('#SelectedProductTable > tbody  > tr').each(function () {
	
			ProductID 			= jQuery.trim(jQuery(this).find("._product_id").val());
			PurchaseQuantity 	= jQuery.trim(jQuery(this).find("._purchase_quantity").val());
			PurchasePrice 		= jQuery.trim(jQuery(this).find("._purchase_price").val());
			if (!jQuery.isNumeric(PurchaseQuantity)) {
				PurchaseQuantity = 0;
				jQuery(this).find("._purchase_quantity").addClass("border-error");
			}
			if (!jQuery.isNumeric(PurchasePrice)) {
				PurchasePrice = 0;
				jQuery(this).find("._purchase_price").addClass("border-error");
				
			}
			if (PurchaseQuantity === 0  || PurchasePrice ===0){
				is_valid  =false;
			}
		});	
	}catch(e){
		alert(e.message);
	}
	return is_valid;
}
function CreatePurchaseOrder() {
	CalculateLineTotal();
	

	JData = {};
	JData["action"] = "niwopims_ajax";
	JData["sub_action"] = "create_purchase_product";
	JData["PurchaseProductList"] = JPurchaseProductList;

	JData["purchase_date"] = jQuery("._purchase_date","#PurchaseOrderModal").val();
	JData["purchase_no"]   = jQuery("._purchase_no","#PurchaseOrderModal").val();
	JData["purchase_notes"]   = jQuery("._purchase_notes","#PurchaseOrderModal").val();

	JData["supplier_id"]   = jQuery("._supplier_id","#PurchaseOrderModal").val();
	JData["Location_id"]   = jQuery("._Location_id","#PurchaseOrderModal").val();
	JData["status_id"]   = jQuery("._status_id","#PurchaseOrderModal").val();



	jQuery.ajax({
		url: niwopims_ajax_object.niwopims_ajaxurl,
		data: JData,
		success: function (response) {
			//jQuery(".niwoomcr_ajax_content").html(data);

			//alert(JSON.stringify(response));
			//alert(JSON.stringify(response["status"]));

			JMessage = JSON.parse(response);
			//alert(JSON.stringify(JMessage));
			if (JMessage["status"] === 1) {
				AddClassAlertBoxByParent("alert-success","#PurchaseOrderModal")
				jQuery("#frmProductList").trigger("submit");
				//jQuery('#PurchaseOrderModal').modal('hide');
				jQuery(".alert","#PurchaseOrderModal").html(JMessage["message"]);
			}else{
				
				AddClassAlertBoxByParent("alert-danger","#PurchaseOrderModal")
			}
			
			 setTimeout(function(){ 
			 	jQuery('#PurchaseOrderModal').modal('hide');
				jQuery(".alert","#PurchaseOrderModal").text('');
				jQuery(".alert","#PurchaseOrderModal").hide();
				
			 }, delay);
			
			/*
			 setTimeout(function(){ window.location = url; }, delay);
			*/
			//alert(JSON.stringify(JMessage["status"]));
			JSelectedProductList = [];
		},
		error: function (errorThrown) {
			console.log(errorThrown);
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