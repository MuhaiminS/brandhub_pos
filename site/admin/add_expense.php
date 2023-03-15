<?php 
session_start();
include("../functions.php");
include_once("../config.php");
chkAdminLoggedIn();
connect_dre_db();

$trn_no = '';
$company_name = '';
$purchase_date = '';
$reference_id = '';
$status = '';
$payment_status = '';
$product_id = array();
$qty = array();
$unit_price = array();
$total_amount = array();
$tax = array();
$payment_type = array();
$supplier_vat = '';
$sub_total = '';
$vat_amount = '';
$net_total = '';
$description = '';
$expense_category_id = '';

$action = 'add';
$update_img_tbl = false;

if(isset($_POST['items_post'])) {
	
	$trn_no = $_POST['trn_no'];
	$company_name = $_POST['company_name'];
	$purchase_date = $_POST['purchase_date'];
	$reference_id = $_POST['reference_id'];
	$supplier_vat = $_POST['supplier_vat'];
	//$status = $_POST['status'];
	$payment_status = $_POST['payment_status'];
	$sub_total = $_POST['sub_total'];
	$vat_amount = $_POST['vat_amount'];
	$net_total = $_POST['net_total'];
	$description = $_POST['description'];
	$expense_category_id = '';
	$is_active = 1;
	$date_added = date("Y-m-d H:i:s");
	$date_updated = date("Y-m-d H:i:s");
	
	if(isset($_POST['id']) && $_POST['id'] > 0) {
		$id = $_POST['id'];		
		$qry = "UPDATE expense SET trn_no= '$trn_no', reference_id = '".safeTextIn($reference_id)."', company_name = '".safeTextIn($company_name)."', payment_status = '".safeTextIn($payment_status)."', sub_total = '".safeTextIn($sub_total)."', purchase_date = '".safeTextIn($purchase_date)."', is_active = '".safeTextIn($is_active)."', date_updated = '".safeTextIn($date_updated)."', supplier_vat = '".safeTextIn($supplier_vat)."', vat_amount = '".safeTextIn($vat_amount)."', net_total = '".safeTextIn($net_total)."', expense_category_id = '$expense_category_id', description = '$description' WHERE id = '$id'";
		//	echo $qry; die;
		if(mysqli_query($GLOBALS['conn'], $qry)){
		}
	}
	else {		
		$qry = "INSERT INTO `expense`(`trn_no`, `reference_id`, `company_name`, `payment_status`, `sub_total`, `purchase_date`, `is_active`, `date_added`, `date_updated`, supplier_vat, vat_amount, net_total, expense_category_id, description) VALUES ('".safeTextIn($trn_no)."', '".safeTextIn($reference_id)."', '".safeTextIn($company_name)."', '".safeTextIn($payment_status)."', '".safeTextIn($sub_total)."', '".safeTextIn($purchase_date)."', '$is_active', '$date_added', '$date_updated', '$supplier_vat', '$vat_amount', '$net_total', '$expense_category_id', '$description')";
		//echo $qry; die;
		if(mysqli_query($GLOBALS['conn'], $qry)){		
			$id = mysqli_insert_id($GLOBALS['conn']);
			
			/*foreach($_POST['product_id'] as $key => $value) {
				$product_id = $_POST['product_id'][$key];
				$qty = $_POST['qty'][$key];
				$unit_price = $_POST['unit_price'][$key];
				$total_amount = $_POST['total_amount'][$key];
				$tax = $_POST['tax'][$key];
				//$payment_type = $_POST['payment_type'][$key];
				
				$qry1 = "INSERT INTO purchase_order_items (purchase_id, item_name, qty, unit_price, total_amount, tax) VALUES ('$id', '$product_id', '$qty', '$unit_price', '$total_amount', '$tax')";		

				if(mysqli_query($GLOBALS['conn'], $qry1)){		
					$id1 = mysqli_insert_id($GLOBALS['conn']);
				}
			}*/
		}		
	}	
		//die;
	redirect('manage_expense.php?resp=addsucc');
}

function getExpenseCategoryList()
   {
   	$service = array();
   	$query="SELECT * FROM ".DB_PRIFIX."expense_category ORDER BY id ASC";
   	$run = mysqli_query($GLOBALS['conn'], $query);
   	while($row = mysqli_fetch_array($run)) {
   		$expense_id = $row['id'];
   		$service[$expense_id]['expense_id'] = $row['id'];
   		$service[$expense_id]['expense_name'] = $row['expense_name'];
   	}
   	return $service;	
   
   } 

if(isset($_GET['act']) && $_GET['act'] != '' && isset($_GET['id']) && $_GET['id'] > 0) {	
	$action = $_GET['act'];
	$id = $_GET['id'];
	if($action == 'edit') {
		$edit_query = "SELECT * FROM expense WHERE id = '$id'";
		$run_edit = mysqli_query($GLOBALS['conn'], $edit_query);		
		while ($edit_row = mysqli_fetch_array($run_edit)) {
			$id = $edit_row['id'];
			$trn_no = $edit_row['trn_no'];
			$company_name = $edit_row['company_name'];
			$purchase_date = $edit_row['purchase_date'];
			$reference_id = $edit_row['reference_id'];
			$supplier_vat = $edit_row['supplier_vat'];
			//$status = $edit_row['status'];
			$payment_status = $edit_row['payment_status'];
			$sub_total = $edit_row['sub_total'];
			$vat_amount = $edit_row['vat_amount'];
			$net_total = $edit_row['net_total'];
			$description = $edit_row['description'];
			//$expense_category_id = $edit_row['expense_category_id'];
		}
	}
}

function getCategorieList()
{
	$cat = array();
	$query = "SELECT * FROM item_category ORDER BY id ASC";
	$run = mysqli_query($GLOBALS['conn'], $query);
	while($row = mysqli_fetch_array($run)) {
		$cat_id = $row['id'];
		$cat[$cat_id] = $row['category_title'];
	}
	return $cat;	
}
?>
<!-- Start include Header -->
<?php include('header.php'); ?>
<!-- End include Header -->
<style>
	.error{
		color:red;
	}
</style>
<!--Start Container-->
<div id="main" class="container-fluid">
	<div class="row">
		<!-- START left bar -->
		<?php include('left.php'); ?>
		<!-- END left bar -->

		<!--Start Content-->
		<div id="content" class="col-xs-12 col-sm-10">
			<div id="about">
				<div class="about-inner">
					<h4 class="page-header">Open-source admin theme for you</h4>
					<p>DevOOPS team</p>
					<p>Homepage - <a href="http://devoops.me" target="_blank">http://devoops.me</a></p>
					<p>Email - <a href="mailto:devoopsme@gmail.com">devoopsme@gmail.com</a></p>
					<p>Twitter - <a href="http://twitter.com/devoopsme" target="_blank">http://twitter.com/devoopsme</a></p>
				</div>
			</div>
			<div class="preloader">
				<img src="img/devoops_getdata.gif" class="devoops-getdata" alt="preloader"/>
			</div>
			<div id="">
				<!-- BLOG START -->
				<div class="row">
					<div id="breadcrumb" class="col-xs-12">
						<a href="#" class="show-sidebar">
							<i class="fa fa-bars"></i>
						</a>
						<ol class="breadcrumb pull-left">
							<li><a href="index.php">Dashboard</a></li>
							<li><a href="javascript:void(0); ">Add Expense</a></li>
						</ol>
					</div>
				</div>
				
				<div class="row">
					<div class="col-xs-12 col-sm-12">
						<div class="box">
							<div class="box-header">
								<div class="box-name">
									<i class="fa fa-search"></i>
									<span>Manage Expense</span>
								</div>
								<div class="box-icons">
									<a class="collapse-link">
										<i class="fa fa-chevron-up"></i>
									</a>
									<a class="expand-link">
										<i class="fa fa-expand"></i>
									</a>
									<a class="close-link">
										<i class="fa fa-times"></i>
									</a>
								</div>
								<div class="no-move"></div>
							</div>
							<div class="box-content">
								<form id="itemsForm" method="post" action="" class="form-horizontal" enctype="multipart/form-data">
									<input type="hidden" name="items_post" value="1" />
									<input type="hidden" name="id" value="<?php echo $id; ?>" />
									<fieldset>
										<legend>Add Expense Order</legend>									
										
										<div class="form-group">
											<label class="col-sm-3 control-label">TRN No</label>
											<div class="col-sm-5">
												<input type="text" class="form-control" name="trn_no" id="trn_no" value="<?php echo $trn_no; ?>" required/>
											</div>
											<!--<a href="add_items.php" class="pj-button add_product" style="display:None">Add Product</a>-->
										</div>
										<div class="form-group">
											<label class="col-sm-3 control-label">Company Name</label>
											<div class="col-sm-5">
												<input type="text" class="form-control" name="company_name" id="company_name" value="<?php echo $company_name; ?>" />
											</div>
										</div>
										<div class="form-group">
											<label class="col-sm-3 control-label">Purchase Date</label>
											<div class="col-sm-5">
												<span class="pj-form-field-custom pj-form-field-custom-after float_left r5">
													<input type="text" name="purchase_date" id="purchase_date" placeholder="Purchase Date" class="form-control" readonly="readonly" required value="<?php echo $purchase_date; ?>" />
												</span>
											</div>
										</div>										
										<div class="form-group">
											<label class="col-sm-3 control-label">Invoice No</label>
											<div class="col-sm-5">											
												<input type="text" name="reference_id" id="reference_id" class="form-control" required value="<?php echo $reference_id; ?>" />
											</div>
										</div>
										<div class="form-group box-content2">
											<label class="col-sm-3 control-label">VAT %</label>
											<div class="col-sm-5">											
												<input type="number" name="supplier_vat" id="supplier_vat" value="5" class="form-control" required value="<?php echo $supplier_vat; ?>" />
											</div>
										</div>
										<div class="form-group">
											<label class="col-sm-3 control-label">Payment Status</label>
											<div class="col-sm-5">											
												<select name="payment_status" id="payment_status" class="form-control" required>					
													<option value="paid" <?php if($payment_status == 'paid') { echo 'selected'; } ?>>Paid</option>
													<option value="not_paid" <?php if($payment_status == 'not_paid') { echo 'selected'; } ?>>Not Paid</option>					
												</select>
											</div>
										</div>
										<div class="form-group box-content2">
											<label class="col-sm-3 control-label">Description</label>
											<div class="col-sm-5">											
												<input type="text" name="description" id="description"class="form-control" value="<?php echo $description; ?>" />
											</div>
										</div>
										
										<div class="form-group box-content1">
											<label class="col-sm-3 control-label">Sub Total</label>
											<div class="col-sm-5">											
												<input type="number" name="sub_total" id="sub_total" class="form-control" required value="<?php echo $sub_total; ?>" />
											</div>
										</div>
										<div class="form-group">
											<label class="col-sm-3 control-label">VAT Amount</label>
											<div class="col-sm-5">											
												<input type="number" name="vat_amount" id="vat_amount" class="form-control" readonly required value="<?php echo $vat_amount; ?>" />
											</div>
										</div>
										<div class="form-group">
											<label class="col-sm-3 control-label">Net Total</label>
											<div class="col-sm-5">											
												<input type="number" name="net_total" id="net_total" class="form-control" readonly required value="<?php echo $net_total; ?>" />
											</div>
										</div>										
									</fieldset>									
									<div class="form-group">
										<div class="col-sm-9 col-sm-offset-3">
											<?php 
												if(isset($action) && $action == 'edit') {
											?>
												<button type="submit" class="btn btn-primary">Update</button>
												<a href="manage_expense.php" class="btn btn-primary">Cancel</a>
											<?php } 
												else { 
											?>
												<button type="submit" class="btn btn-primary">Submit</button>
											<?php 
												} 
											?>
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
				<!-- BLOG END -->
			</div>
		</div>
		<!--End Content-->
	</div>
</div>
<!--End Container-->
<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<!--<script src="http://code.jquery.com/jquery.js"></script>-->
<script src="plugins/jquery/jquery.min.js"></script>
<script src="plugins/jquery-ui/jquery-ui.min.js"></script>
<!-- Include all compiled plugins (below), or include individual files as needed -->
<script src="plugins/bootstrap/bootstrap.min.js"></script>
<script src="plugins/justified-gallery/jquery.justifiedGallery.min.js"></script>
<script src="plugins/tinymce/tinymce.min.js"></script>
<script src="plugins/tinymce/jquery.tinymce.min.js"></script>
<!-- <script src="plugins/bootstrapvalidator/bootstrapValidator.min.js"></script> -->
<script src="plugins/select2/select2.min.js"></script>
<script src="js/validation.js"></script>
<!-- All functions for this theme + document.ready processing -->
<script src="js/devoops.js"></script>

<script type="text/javascript">
$(document).ready(function() {
	// Create Wysiwig editor for textare
	//TinyMCEStart('#items_content', null);
	//TinyMCEStart('#wysiwig_full', 'extreme');
	// Add slider for change test input length
	//FormLayoutExampleInputLength($( ".slider-style" ));
	// Initialize datepicker
	//$('#input_date').datepicker({setDate: new Date()});
	// Load Timepicker plugin
	//LoadTimePickerScript(DemoTimePicker);
	// Add tooltip to form-controls
	$('.form-control').tooltip();
	//LoadSelect2Script(DemoSelect2);
	// Load example of form validation
	//LoadBootstrapValidatorScript(DemoFormValidator);
	// Add drag-n-drop feature to boxes
	WinMove();
});
</script>

<script type="text/javascript">
var slug = function(str) {
    var $slug = '';
    var trimmed = $.trim(str);
    $slug = trimmed.replace(/[^a-z0-9-]/gi, '-').
    replace(/-+/g, '-').
    replace(/^-|-$/g, '');
    return $slug.toLowerCase();
}
//$( "#manuf_date" ).datepicker({dateFormat: 'yy-mm-dd'});
//$( "#expiry_date" ).datepicker({dateFormat: 'yy-mm-dd'});
//$( "#inward_date" ).datepicker({dateFormat: 'yy-mm-dd'});

$("#itemsForm").validate();
var $itemsForm = $("#itemsForm");
		$(document).on("click", "#btnAddproduct", function (e) {
			var $tr,
				$tbody = $("#tblproduct tbody"),
				index = Math.ceil(Math.random() * 999999),
				h = $tbody.find("tr:last").find("td:first").html(),
				i = (h === null) ? 0 : parseInt(h, 10);
			
			i = !isNaN(i) ? i : 0;				
			$tr = $("#tblproductClone").find("tbody").clone();
			$tbody.find(".notFound").remove();
			var tr_html = $tr.html().replace(/\{INDEX\}/g, 'x_' + index);
			//tr_html = tr_html.replace(/\{PTCLASS\}/g, 'pj-payment-type');
			//tr_html = tr_html.replace(/\{ACLASS\}/g, 'pj-payment-amount');
			//tr_html = tr_html.replace(/\{SCLASS\}/g, 'pj-payment-status');
			//alert($("#supplier_id").val());
			//if($("#supplier_name").val() != '') { alert(1);
				$tbody.append(tr_html);
				/*if ($itemsForm.length > 0 && $itemsForm.validate) {
					getProducts.call(null, $itemsForm, 'x_' + index);
				} else {
					getProducts.call(null, $itemsForm, 'x_' + index);
				}*/				
				$tbody.find("tr:last").find(".spin").spinner({
					min: 0,
					step: 1
				});
			//} else {
				//$(".err").show();
			//}
		}).on("click", ".btnDeleteProduct", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var $tr = $(this).closest("tr");
			$tr.css("backgroundColor", "#FFB4B4").fadeOut("slow", function () {
				$tr.remove();
				//calPayment();
			});
			return false;
		});

		$(".box-content").on("keyup", ".qty", function (e) {
			if (/\D/g.test(this.value)){
				this.value = this.value.replace(/\D/g,'')
			}
			var $qty = $(this).val();
			var $index = $(this).attr('data-index');
			var $unit_price = $("#unit_price_" + $index).val();
				
			$qty = !isNaN($qty) ? $qty : 0;
			$unit_price = !isNaN($unit_price) ? $unit_price : 0;

			var result = 0;
			
			if($unit_price !='' && $qty !='') {
				result = $qty * $unit_price;
				$('#total_amount_'+$index).val(result);
			} else {
				$('#total_amount_'+$index).val('');
			}
		}).on("keyup", ".unit_price", function (e) {
			if (/\D/g.test(this.value)){
				this.value = this.value.replace(/\D/g,'')
			}
			var $unit_price = $(this).val();
			var $index = $(this).attr('data-index');
			var $qty = $("#qty_" + $index).val();

			$qty = !isNaN($qty) ? $qty : 0;
			$unit_price = !isNaN($unit_price) ? $unit_price : 0;

			var result = 0;
			
			if($unit_price !='' && $qty !='') {
				result = $qty * $unit_price;
				$('#total_amount_'+$index).val(result);
			} else {
				$('#total_amount_'+$index).val('');
			}
		});
		
		/*function getProducts($form, $index) {
			$.get("get_items.php", $form.serialize()).done(function (data) {
				//$("a").attr("href", "index.php?controller=pjAdminSuppliers&action=pjActionAddProduct&id="+$("#supplier_id").val());
				$(".add_product").show();
				var index = $index;//$( ".product_id" ).attr('data-index');				
				$("#product_id_"+index).html(data);
			});
		}*/

		$(".box-content1").on("keyup", "#sub_total", function (e) {
			var sub_total = $(this).val();
			var supplier_vat = $('#supplier_vat').val();
			if(supplier_vat > 0) {
				var vat_amount = (sub_total*supplier_vat)/100;
			} else {
				var vat_amount = supplier_vat;
			}
			$('#vat_amount').val(vat_amount);
			$('#net_total').val(Number(vat_amount)+Number(sub_total));
		});
		$(".box-content2").on("keyup", "#supplier_vat", function (e) {
			var supplier_vat = $(this).val();
			var sub_total = $('#sub_total').val();
			if(supplier_vat > 0) {
				var vat_amount = (sub_total*supplier_vat)/100;
			} else {
				var vat_amount = supplier_vat;
			}
			$('#vat_amount').val(vat_amount);
			$('#net_total').val(Number(vat_amount)+Number(sub_total));
		});

$( "#purchase_date" ).datepicker({dateFormat: 'yy-mm-dd'});

</script>
</body>
</html>