<?php 
   session_start();
   require_once 'db_functions.php';
   $function = New DB_Functions();
   $getUserDetails = $function->getUserDetails($_SESSION['user_id']);
	$getUserDetails = explode(",", $getUserDetails['user_action']);
	if (!in_array('delivery_sale',$getUserDetails)){
		$function->redirect('index.php');
	}

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
	// $expense_category_id = $_POST['expense_category_id'];
	$is_active = 1;
	$date_added = date("Y-m-d H:i:s");
	$date_updated = date("Y-m-d H:i:s");
	
	if(isset($_POST['id']) && $_POST['id'] > 0) {
		$id = $_POST['id'];		
		//$qry = "UPDATE items SET barcode_id= '$barcode_id', name = '".safeTextIn($name)."', price = '".safeTextIn($price)."', weight = '".safeTextIn($weight)."', unit = '".safeTextIn($unit)."' WHERE id = '$id'";
		//	echo $qry; die;
		if(mysqli_query($GLOBALS['conn'], $qry)){
		}
	}
	else {		
		$qry = "INSERT INTO `expense`(`trn_no`, `reference_id`, `company_name`, `payment_status`, `sub_total`, `purchase_date`, `is_active`, `date_added`, `date_updated`, supplier_vat, vat_amount, net_total, expense_category_id, description) VALUES ('".$trn_no."', '".$reference_id."', '".$company_name."', '".$payment_status."', '".$sub_total."', '".$purchase_date."', '$is_active', '$date_added', '$date_updated', '$supplier_vat', '$vat_amount', '$net_total', '$expense_category_id', '$description')";
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
	echo"<script>
		alert('Successfully Expense Added!');
		</script>";
	$function->redirect('index.php?resp=addsucc');
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
  
   
   ?>
<!DOCTYPE html>
<!--[if lt IE 7]>      
<html class="no-js lt-ie9 lt-ie8 lt-ie7">
<![endif]-->
<!--[if IE 7]>         
<html class="no-js lt-ie9 lt-ie8">
<![endif]-->
<!--[if IE 8]>         
<html class="no-js lt-ie9">
<![endif]-->
<!--[if gt IE 8]><!--> 
<html class="no-js">
<!--<![endif]-->
<html>
   <head>
	  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	  <meta charset="UTF-8" />
	  <title>Expenses Page/صفحة المصروفات</title>
	  <meta name="description" content="" />
	  <meta name="keywords" content="" />
	  <meta name="viewport" content="width=device-width, initial-scale=1">
	  <link rel="stylesheet" href="css/bootstrap.min.css">
	  <link rel="stylesheet" href="css/main.css">
	  <link rel="icon" type="image/png" sizes="32x32" href="img/favicon.ico">
	  <link rel="stylesheet" href="css/bootstrap.css">
	  <link href="css/jquery-ui.css" rel="stylesheet">
   </head>
   <body>
	  <!--[if lt IE 7]>
	  <p class="browsehappy">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
	  <![endif]-->
	  <header>
		 <!--logo-start-->
		 <div class="container-fluid top-head">
			<div class="row">
			   <div class="col-sm-4">
				  <div class="logo-s">
					 <a href="index.php"><img src="img/out.png" alt="logo"></a>
				  </div>
			   </div>
			   <div class="col-sm-4">
				  <div class="counter-head">
					Expense
				  </div>
			   </div>
			   <div class="col-sm-4">
				  <div class="log-box-s">
					 <div class="log-box-s-one">
					 <a href="index.php">
						<span><img src="img/home.png"></span>
						<p>HOME</p>
					 </a>
					 </div>
				  </div>
			   </div>
			</div>
		 </div>
	  </header>
	  <div style="clear:both"></div>
	  <div class="sale-report-body">
		 <div class="container">
			 <style>
			.expences_name h2 {
			   font-size: 18px;
			   text-align: right;
			}

			.expences_name h1 {
			   font-size: 18px;
			   text-align: left;
			font-weight:800;
			}
			.btnnew button:hover {
			   border: 2px solid #ff5c29 !important;
			   background: #000 !important;
			}
			.btnnew button {
			   background: #ff5c29;
			   border: 2px solid #ff5c29;
			   border-radius: 35px;
			   color: #fff;
			   display: inline-block;
			   font-family: roboto;
			   font-size: 13px;
			   font-weight: 500;
			   letter-spacing: 0.5px;
			   padding: 10px 25px;
			   text-align: center;
			   text-transform: capitalize;
			   transition: all 0.25s linear 0s;
			}
			 </style>
			 <div style='text-align:center;color:green;'>
			 <?php if(isset($_GET['resp']) && $_GET['resp'] !='') {
				//echo "<span>Successfully Purchase Added!</span>";
			 }
			 
			 ?>
			 </div>
			<form id="itemsForm" method="post" action="" class="form-horizontal" enctype="multipart/form-data" style="margin-top:20px;">
									<input type="hidden" name="items_post" value="1" />									
									<fieldset>						
										
										<div class="form-group">
											<label class="col-sm-3 control-label">TRN No</label>
											<div class="col-sm-5">
												<input type="text" class="form-control" name="trn_no" id="trn_no" required/>
											</div>
											<!--<a href="add_items.php" class="pj-button add_product" style="display:None">Add Product</a>-->
										</div>
										<div class="form-group">
											<label class="col-sm-3 control-label">Company Name</label>
											<div class="col-sm-5">
												<input type="text" class="form-control" name="company_name" id="company_name"/>
											</div>
										</div>
										<div class="form-group">
											<label class="col-sm-3 control-label">Purchase Date</label>
											<div class="col-sm-5">
												<span class="pj-form-field-custom pj-form-field-custom-after float_left r5">
													<input type="text" name="purchase_date" id="purchase_date" placeholder="Purchase Date" class="form-control" readonly="readonly" required />
												</span>
											</div>
										</div>										
										<div class="form-group">
											<label class="col-sm-3 control-label">Invoice No</label>
											<div class="col-sm-5">											
												<input type="text" name="reference_id" id="reference_id" class="form-control" required />
											</div>
										</div>
										<div class="form-group box-content2">
											<label class="col-sm-3 control-label">VAT %</label>
											<div class="col-sm-5">											
												<input type="number" name="supplier_vat" id="supplier_vat" value="5" class="form-control" required />
											</div>
										</div>
										<div class="form-group">
											<label class="col-sm-3 control-label">Payment Status</label>
											<div class="col-sm-5">											
												<select name="payment_status" id="payment_status" class="form-control" required>					
													<option value="paid">Paid</option>
													<option value="not_paid">Not Paid</option>					
												</select>
											</div>
										</div>
										<div class="form-group box-content2">
											<label class="col-sm-3 control-label">Description</label>
											<div class="col-sm-5">											
												<textarea type="number" name="description" id="description" value="5" class="form-control" /></textarea>
											</div>
										</div>
										<!-- <div class="form-group">
											<label class="col-sm-3 control-label">Category</label>
											<div class="col-sm-5">											
												<select id="expense_category_id" required name="expense_category_id" class="form-control">
												<?php 
													$expense_list = getExpenseCategoryList();
													foreach ($expense_list as $key => $value) {								
														echo "<option value=\"".$value['1']."\">".$value['expense_name']."</option>";
													}
												?>
												</select>
											</div>
										</div> -->
										<div class="form-group box-content1">
											<label class="col-sm-3 control-label">Sub Total</label>
											<div class="col-sm-5">											
												<input type="number" name="sub_total" id="sub_total" class="form-control" required />
											</div>
										</div>
										<div class="form-group">
											<label class="col-sm-3 control-label">VAT Amount</label>
											<div class="col-sm-5">											
												<input type="number" name="vat_amount" id="vat_amount" value="" class="form-control" readonly required />
											</div>
										</div>
										<div class="form-group">
											<label class="col-sm-3 control-label">Net Total</label>
											<div class="col-sm-5">											
												<input type="number" name="net_total" id="net_total" value="" class="form-control" readonly required />
											</div>
										</div>										
									</fieldset>									
									<div class="form-group">
										<div class="col-sm-9 col-sm-offset-3 btnnew">
											<?php 
												if(isset($action) && $action == 'edit') {
											?>
												<button id="button1id" name="button1id" class="btn btn-primary" type="submit">Save</button>
												<a href="manage_items.php" class="btn btn-primary">Cancel</a>
											<?php } 
												else { 
											?>
												<button id="button1id" name="button1id" class="btn btn-primary" type="submit">Save</button>
											<?php 
												} 
											?>
										</div>
									</div>
								</form>
		</div>
	  </div>
	  <script src="js/jquery-3.2.1.min.js"></script> 
	  <script src="js/bootstrap.min.js"></script>
	  <script src="js/jquery-date.js"></script>
	  <script src="js/jquery-ui.js"></script>
	  <script type="text/javascript">
		 
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

	//$( "#purchase_date" ).datepicker({dateFormat: 'yy-mm-dd'});
		$( function() {
		    $( "#purchase_date" ).datepicker({
		      defaultDate: new Date(2017, 12, 1),
		      numberOfMonths: [4,3],
		      dateFormat: 'yy-mm-dd',
		      showButtonPanel: true
		    });
		  });				  
	  </script>
	  <?php //include('script_common.php'); ?>
   </body>
</html>