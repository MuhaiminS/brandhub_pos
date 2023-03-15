<?php
session_start();
include("functions_web.php");
include_once("config.php");
connect_dre_db();
chkUserLoggedIn();
$getUserDetails = getUserDetails($_SESSION['user_id']);
$getUserDetails = explode(",", $getUserDetails['user_action']);
if (!in_array('delivery_sale',$getUserDetails)){
	redirect('index.php');
}
$items = getItemsList("ASC");
//print_r($items);
$category = getCategoryList();
$item_img_dir = "item_images/";
$sale_order_id = (isset($_GET['order']) && $_GET['order'] !='') ? $_GET['order'] : '';
if($sale_order_id == ''){
	redirect('index.php');
}

	$result = mysqli_query($GLOBALS['conn'], "SELECT * FROM ".DB_PRIFIX."sale_orders WHERE id = '$sale_order_id' ORDER BY id ASC");  
	$sale_order =  mysqli_fetch_assoc($result);
	//print_r($sale_order);
	$result_arr = $old_item_id = $old_qty = array();
	$sql = "SELECT * FROM ".DB_PRIFIX."sale_order_items WHERE sale_order_id = $sale_order_id";
	$result_val = mysqli_query($GLOBALS['conn'], $sql);
	while ($row = mysqli_fetch_assoc($result_val)) {
		$result_arr[] = $row;			
	}
	$total_amount = $total_amount1  = '0';
	foreach($result_arr as $res) {
		$multiplle_val=$res['price']*$res['qty'];
		$total_amount+=$multiplle_val;
		$old_item_id[] = $res['item_id'];
		$old_qty[] = $res['qty'];
	}
	$old_item_ids = implode(',', $old_item_id);
	$old_qtys = implode(',', $old_qty);

if(isset($_POST['orderDetails'])) {

	$inputs = $_POST;
	if($inputs['orderDetails'] == 1)
	{

	$sale_insert = getSaleOrderItemDetailsEdit($inputs);
		if($sale_insert){
			//redirect('dine_in.php');
			if($sale_insert){
				$sale_order_id = $sale_insert['id'];
				$floor_id = $sale_insert['floor_id'];
				//redirect('single_item_print.php.?id='.$sale_order_id.'&re=sale_order_details.php&old_item='.$old_item_ids.'&old_qty='.$old_qtys);
				redirect('single_item_print.php?id='.$sale_order_id.'&re=sale_order_details.php&cus_print=1');
			}
		}
	}
	else
	{
	$sale_insert = getSaleOrderItemDetailsEditSplit($inputs);

	if($sale_insert){
	// 		$sale_order_id = $inputs['sale_order_id'];
	// $amount_given = $inputs['amount_given'];
	// $payment_type = $inputs['payment_type'];
	// $card_num = (isset($inputs['card_num']) && $inputs['card_num'] !='') ? $inputs['card_num'] : '';
	// $discount = (isset($inputs['discount']) && $inputs['discount'] !='') ? $inputs['discount'] : '0.0';
	// $payment_status = 'paid';
	// //$sale_insert = getSaleOrderItemDetailsEdit($inputs);
	// mysqli_query($GLOBALS['conn'], "UPDATE ".DB_PRIFIX."sale_orders SET discount = '$discount', card_num = '$card_num', amount_given = '$amount_given', payment_type = '$payment_type', payment_status = '$payment_status' WHERE id = '$sale_order_id'");
		foreach ($sale_insert as $key => $value) 
		{
		$sale_order_id = $value;
		// $amount_given = $inputs['amount_given'];
		// $payment_type = $inputs['payment_type'];
		$card_num = (isset($inputs['card_num']) && $inputs['card_num'] !='') ? $inputs['card_num'] : '';
		$discount = (isset($inputs['discount']) && $inputs['discount'] !='') ? $inputs['discount'] : '0.0';
		//$card_type = (isset($inputs['card_type']) && $inputs['card_type'] !='') ? $inputs['card_type'] : '';
	//	$multiple_amount_given = (isset($inputs['multiple_amount_given']) && $inputs['multiple_amount_given'] !='') ? $inputs['multiple_amount_given'] : '0.0';
		//$multiple_payment_types = (isset($inputs['multiple_payment_types']) && $inputs['multiple_payment_types'] !='') ? $inputs['multiple_payment_types'] : '';
		$payment_type = 'cash';
		$payment_status = 'paid';
		//$sale_insert = getSaleOrderItemDetailsEdit($inputs);
		//mysqli_query($GLOBALS['conn'], "UPDATE ".DB_PRIFIX."sale_orders SET discount = '$discount', card_num = '$card_num', payment_status = '$payment_status', multiple_payment_types = '$multiple_payment_types', multiple_amount_given = '$multiple_amount_given', card_type = '$card_type' WHERE id = '$sale_order_id'");
		mysqli_query($GLOBALS['conn'], "UPDATE ".DB_PRIFIX."sale_orders SET discount = '$discount', card_num = '$card_num', payment_status = '$payment_status', payment_type ='$payment_type' WHERE id = '$sale_order_id'");
		}
	}
	$sale_id = json_encode($sale_insert);
	redirect('single_item_print.php?id='.$sale_order_id.'&re=index.php&deliver=yes');
	// redirect('bill_print.php?id='.$sale_id.'&re=sale_order_details.php&cus_print=1');
	}
}

if(isset($_POST['pay_amount'])) {
	$inputs = $_POST;
	//echo '<pre>'; print_r($inputs); die;
	$sale_order_id = $inputs['sale_order_id'];
	$amount_given_card = 0;
	if($inputs['amount_given_card'] != '' && $inputs['amount_given_card'] != '0') {
		$amount_given_card = $inputs['amount_given_card'];
	}
	$amount_given = $inputs['amount_given'] + $amount_given_card;
	$payment_type = $inputs['payment_type'];
	$card_num = (isset($inputs['card_num']) && $inputs['card_num'] !='') ? $inputs['card_num'] : '';
	$discount = (isset($inputs['discount']) && $inputs['discount'] !='') ? $inputs['discount'] : '0.0';
	$card_type = (isset($inputs['card_type']) && $inputs['card_type'] !='') ? $inputs['card_type'] : '';
	$multiple_amount_given = (isset($inputs['multiple_amount_given']) && $inputs['multiple_amount_given'] !='') ? $inputs['multiple_amount_given'] : '0.0';
	$multiple_payment_types = (isset($inputs['multiple_payment_types']) && $inputs['multiple_payment_types'] !='') ? $inputs['multiple_payment_types'] : '';

	$payment_status = 'paid';
	//$sale_insert = getSaleOrderItemDetailsEdit($inputs);
	mysqli_query($GLOBALS['conn'], "UPDATE ".DB_PRIFIX."sale_orders SET discount = '$discount', card_num = '$card_num', amount_given = '$amount_given', payment_type = '$payment_type', payment_status = '$payment_status', multiple_payment_types = '$multiple_payment_types', multiple_amount_given = '$multiple_amount_given', card_type = '$card_type' WHERE id = '$sale_order_id'");
	//if($sale_insert){	
		redirect('single_item_print.php?id='.$sale_order_id.'&re=index.php&deliver=yes');
	//redirect('single_item_print.php?id='.$sale_order_id.'&re=dine_in.php&pay=given');
	//}
}
$result = mysqli_query($GLOBALS['conn'], "SELECT * FROM ".DB_PRIFIX."sale_orders WHERE id = '$sale_order_id' ORDER BY id ASC");  
$sale_order =  mysqli_fetch_assoc($result);
//print_r($sale_order);
$result_arr = array();
$sql = "SELECT * FROM ".DB_PRIFIX."sale_order_items WHERE sale_order_id = $sale_order_id";
$result_val = mysqli_query($GLOBALS['conn'], $sql);
while ($row = mysqli_fetch_assoc($result_val)) {
	$result_arr[] = $row;			
}
$total_amount = $total_amount1  = '0';
foreach($result_arr as $res) {
	$multiplle_val=$res['price']*$res['qty'];
	$total_amount+=$multiplle_val;
}
//echo "<pre>"; print_r($result_arr);die;
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
	  <title>Sale Edit</title>
	  <meta name="description" content="" />
	  <meta name="keywords" content="" />
	  <meta name="viewport" content="width=device-width, initial-scale=1">
	  <link rel="stylesheet" href="css/bootstrap.min.css">
	  <link rel="stylesheet" href="css/main.css">
	  <link rel='stylesheet prefetch' href='css/animate.min.css'>
	  <link rel='stylesheet' href='css/counter.css'>
	  <link rel="icon" type="image/png" sizes="32x32" href="img/favicon.png">
	  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.13/css/all.css" integrity="sha384-DNOHZ68U8hZfKXOrtjWvjxusGo9WQnrNx2sqG0tfsghAvtVlRW3tvkXWZh58N9jp" crossorigin="anonymous">
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
					 <a href="sale_order_details.php"><img src="img/out.jpg" alt="logo"></a>
				  </div>
			   </div>
			   <div class="col-sm-4">
				  <div class="counter-head">
					 Sale Edit
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
	  <div class="conter-product">
		 <div class="container-fluid">
		  <form id="itemsForm" method="post" action="" class="form-horizontal">
			<input type="hidden" name="orderDetails" id="orderDetails" value="1" />
			<input name="order_type" type="hidden" value="delivery">
			<input name="sale_order_id" type="hidden" value="<?php echo $sale_order_id; ?>">
			<input name="payment_status" type="hidden" value="unpaid">
			<input type="hidden" id="contact_number1" name="contact_number" value="">
			<input type="hidden" id="contact_name1" name="contact_name" value="">
			<input type="hidden" id="multiple_amount_given1" name="multiple_amount_given" value="0">
			<input type="hidden" id="multiple_payment_types1" name="multiple_payment_types" value="0">
			<input type="hidden" id="card_type1" name="card_type" value="0">
			<input type="hidden" class="amt_box" id="amount_given" name="amount_given" value="">
			<input type="hidden" id="discount1" name="discount" value="0">
		 <div class="row">
			  <div class="container-fluid biller_det">
				<!-- <div class="col-sm-1 sec_main order_det">
						<label class="" for="">Order no<br>أجل لا</label>
						<div><?php echo $sale_order['id']; ?></div>
				</div>
				<div class="col-sm-1 sec_main floor_det">
						<label class="" for="">Floor no<br>الطابق لا</label>
						<div><?php echo $sale_order['floor_id']; ?></div>
				</div>
				<div class="col-sm-1 sec_main table_det">
						<label class="" for="">Table no<br>الجدول رقم</label>
						<div><?php echo gettableno($sale_order['table_id']); ?></div>
				</div>
				<div class="col-sm-1 sec_main seat_det">
						<label class="" for="">Seats<br>مقاعد</label>
						<div><?php echo $sale_order['num_members']; ?></div>
				</div>-->
				<div class="col-sm-4"></div>
				<div class="col-sm-6">
					<a id="sub_frm" class="btn btn-danger btn-lg" disabled>
					  <span class="glyphicon glyphicon-shopping-cart"></span> Save order</a>
				
					<a id = "print_bill" href="single_item_print.php?id=<?php echo $sale_order_id; ?>&re=sale_order_details.php&cus_print=1" class="btn btn-info btn-lg">
					  <span class="glyphicon glyphicon-print"></span> Print bill
					</a>
		
				<!-- 	<a data-toggle="modal" data-target="#payModal" class="btn btn-success btn-lg">
					  <span class="glyphicon glyphicon-ok"></span> Pay bill
					</a> -->

					<!-- <a id="sub_frm_1" class="btn btn-primary btn-lg">
					  <span class="glyphicon glyphicon-ok"></span> Split and Pay bill
					</a> -->
				</div>
			</div> 
		</div>		
			<div class="col-sm-4">
			   <div class="counter-left table-responsive">
				  <table class="table listing--item" style="border:none;">
					 <thead>
						<tr>
						   <th style="width: 10%;">Si</th>
						   <th style="width: 45%;">Name</th>
						   <th style="width: 25%;">Qty</th>
						   <th style="width: 15%;">U.Price</th>
						   <th style="width: 15%;">Price</th>
						   <!-- <th style="width: 10%;">Split Bill</th> -->
						</tr>
					</thead>
					<tbody id="counter_append">
						<?php 
						$tax_pecr = 0.00;
			   		 $total_amount1 = 0.00;
					 foreach($result_arr as $res) { ?>
							<tr>
								<td style='width: 10%;'>
								<input class='item_scroll<?php echo $res['item_id']; ?>' type='hidden' name='items[]' value="<?php echo $res['item_id']; ?>"></td>
								<td style='width: 45%;'><?php echo $res['item_name']; ?></td>
								<td style='width: 25%;'><a class='act_btn minus_button'>-</a><input style='width: 45px;' class='quantity' name='quantity[]' type='number' value="<?php echo $res['qty']; ?>"><a class='act_btn plus_button'>+</a></td>
								<td style='width: 15%; text-align: right;'><input type='number' style='width: 100%;' class='unit-price' name='unit_price[]' value="<?php echo $res['price']; ?>"></td>
							
								<td style='width: 15%; text-align: right;'><?php echo $res['qty']*$res['price']; ?></td>
								<!-- <td style='width: 10%; text-align: right;'><select  name='split_bill[]' style="width:100%;"> 
									<option value="1">1</option>
									<option value="2">2</option>
									<option value="3">3</option>
									<option value="4">4</option>
									<option value="5">5</option>
								</select></td> -->
							</tr>
						<?php   $multiplle_val=$res['price']*$res['qty'];
								$total_amount1+=$multiplle_val;
						} ?>
					 </tbody>
				  </table>
				  <div id="scroll_id"></div>
			   </div>
			   <div class="value-split">
			   <?php 
			   		 ?>
					<span class="fn--amt">Net Total  </span><span class="tt-amount net_total_tt"><?php echo number_format((float)$total_amount1, 2, '.', ''); ?></span><hr>
					<?php if(BILL_TAX == 'yes' && BILL_COUNTRY == 'UAE') { $tax_pecr = ($total_amount1 / 100) * (BILL_TAX_VAL); ?>
					<span class="fn--amt">VAT(<?php echo BILL_TAX_VAL; ?>%):  </span><span class="tt-amount vat_total_tt"><?php echo number_format((float)$tax_pecr, 2, '.', ''); ?></span><hr>
					<?php } ?>
					<span class="fn--amt">Gross Total </span><span class="tt-amount grand_total_tt"><?php echo number_format((float)($total_amount1 + $tax_pecr), 2, '.', ''); ?></span><hr>
			   </div>
			</div>
			</form>
			<div class="col-sm-8">
			   <div class="tab-box1">
				  <div id="exTab1">
				  <?php if($category) { ?>
					 <ul id="items_cat" class="nav nav-pills">
					 <?php $i=0; foreach($category as $cat) { ?>
						<li class="<?php echo ($i == 0) ? 'active' : '';?>">
						   <a href="#<?php echo $cat['category_slug']; ?>" data-toggle="tab"><?php echo $cat['category_title']; ?></a>
						</li>
					 <?php $i++; } ?>
					 </ul>
				  <?php } ?>
				  <div class="search-box">
					   <div class="span12">
						  <form id="custom-search-form" class="form-search form-horizontal pull-right">
							 <div class="input-append span12">
								<!-- <input type="text" class="search-query" id="myInput" autofocus placeholder="Search">-->
								<!-- <select id="country" style="width:100%;">
								Dropdown List Option
								</select> -->
								<select id='standard' name='standard' class='custom-select'>
									<option value=""> -- No value -- </option>
								  <?php foreach($items as $item) {?>										
										<option data-name="<?php echo $item['name']; ?>" data-price="<?php echo $item['price']; ?>" value="<?php echo $item['id']; ?>"><?php echo $item['name']; ?> - <?php echo $item['barcode_id']; ?> - (<?php echo $item['price']; ?>)</option>
								  <?php } ?>
								</select>
							 </div>
						  </form>
					   </div>
					</div>
					 <div class="tab-content clearfix main-tb">						
						<?php $j=0; foreach($category as $cat) { ?>
						<div class="tab-pane <?php echo ($j == 0) ? 'active' : '';?>" id="<?php echo $cat['category_slug']; ?>">
						<?php foreach($items as $item) { //print_r($item);
						if($item['category_slug'] == $cat['category_slug']) { ?>
							<div class="col-sm-2 <?php if($item['image']) { ?> prod-fas <?php } else { ?> prod-fas1 <?php } ?> prod-main" data-id = "<?php echo $item['id']; ?>" data-name = "<?php echo $item['name']; ?>" data-price = "<?php echo $item['price']; ?>">
							<?php if($item['image']) { ?>
							  <span><img style="cursor:pointer;" class="img-responsive" src="<?php echo $item_img_dir.$item['image']; ?>" alt="<?php echo $item['name']; ?>" title="<?php echo $item['name']; ?>"></span>
							  <?php } else { ?>
							  <!--<span><img style="cursor:pointer;" class="img-responsive" src="img/no-image.jpg" alt="<?php echo $item['name']; ?>" title="<?php echo $item['name']; ?>"></span>-->
							  <?php } ?>
							  <h5><?php echo custom_echo($item['name'], 15); ?></h5>
							  <!-- <h5><?php echo custom_echo($item['name'], 8); ?></h5> -->
							  <p><?php echo CURRENCY.' '.$item['price']; ?></p>
						   </div>
						<?php } $j++; } ?>
						</div>
						<?php } ?>
					 </div>
				  </div>
			   </div>
			</div>
		 </div>
	  </div>
		<!-- Modal -->
		<div class="modal fade" id="payModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
		  <div class="modal-dialog" role="document">
			<div class="modal-content">
			  <div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			  </div>
			  <div class="modal-body">
				<div class="currency-sym">Total amount (<?php echo CURRENCY;?>)</div>
				<div class="total-amount">
					<span class="grand_total_tt"><?php echo number_format((float)($total_amount+$tax_pecr), 2, '.', ''); ?></span>
				</div>
				<span class="error-dng" id="error_value"></span>
				<form id="payForm" method="post" action="" class="form-horizontal">
				<input class="login-sp discountt" style="width: 100%; margin-bottom: 10px;" value="" name="discount" id="discount" placeholder="Discount (INR)" max="25" type="text">
				<div class="amount-given1">				
					<input type="hidden" id="total_value" name="total_value" value="<?php echo number_format((float)($total_amount+$tax_pecr), 2, '.', ''); ?>">
					<input type="hidden" id="sale_order_id" name="sale_order_id" value="<?php echo $sale_order_id; ?>">
					<input type="hidden" id="pay_amount" name="pay_amount" value="1">
					<input type="hidden" id="multiple_amount_given2" name="multiple_amount_given" value="0">
					<input type="hidden" id="multiple_payment_types2" name="multiple_payment_types" value="0">					
					<input type="hidden" id="payment_type2" name="payment_type" value="">
					<!-- <input type="number" id="amount_given_val" name="amount_given" value="" placeholder="<?php echo CURRENCY;?> 00.00"> 
					<select id="payment_type" name="payment_type">
						<option value="cash">Cash</option>
						<option value="card">Card</option>
						<option value="credit">Credit</option>
					</select> -->				
				<input type="number" class="amt_gvn" id="amount_given_val" name="amount_given" value="" placeholder="<?php echo 'Cash '.CURRENCY;?> 00.00"> 
					<input type="number" class="amt_gvn" id="amount_given_val_card" name="amount_given_card" value="" placeholder="<?php echo 'Card '.CURRENCY;?> 00.00"> 
				</div>
				<?php $card_list = array(array('name' => 'Visa', 'image' => 'fa-cc-visa'),array('name' => 'Master Card', 'image' => 'fa-cc-mastercard'),array('name' => 'American Express', 'image' => 'fa-cc-amex'));
				if($card_list) { ?>
				<div class="drivers-list">
					<?php foreach($card_list as $card) { ?>
					<div class="dv-val">
						<label>
							<input type="radio" id="card_type" value="<?php echo $card['name']; ?>" name="card_type"/> <i class="fa <?php echo $card['image']; ?>"></i>
							<!-- <img src="<?php //echo $card_type_img_dir.$card['image']; ?>" class="img-responsive"/> -->
							<p><?php echo $card['name']; ?></p>
						</label>
					</div>
					<?php } ?>
				</div>
				<?php } ?>
				<div class="amount-given2 card_val">
					<input type="text" pattern="([0-9]|[0-9]|[0-9])" id="card_num" name="card_num" value="" maxlength="4" placeholder="Enter card number">
				</div>
				<div class="credit_val" style="display: none;">
				<input class="login-sp" style="width: 49%;" value="" name="contact_number" id="contact_number" autocomplete="off" placeholder="Customer Numer" max="30" type="number" required>		 
				 <input class="login-sp" style="width: 49%;" value="" name="contact_name" id="contact_name" placeholder="Customer Name" max="25" type="text">
				 <div id="suggesstion-box"></div>
				</div>
				</form>
				<div class="amount-given1">
					<span>Balance</span>
					<span id="balance_amount_val"></span>
				</div>
			  </div>
			 <div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				<button type="button" id="pay_frm" class="btn btn-primary">Pay</button>
			  </div>
			</div>
		  </div>
		</div>
	  <script src="js/jquery-3.2.1.min.js"></script> 
	  <script src="js/bootstrap.min.js"></script>

	  <script src='js/jquery-customselect.js'></script>
    <link href='css/jquery-customselect.css' rel='stylesheet' />
	  
	  <script> 	
	    $('#payModal').on('shown.bs.modal', function () {
		$('#amount_given_val').focus();
	  })
	  // Quantity Key up function
		$(document).on('change', 'input.quantity', function() {
			var isvalid = $.isNumeric($(this).val());
			if(!isvalid){
				alert("Numeric values only...");
			} else {
				row_index = $(this).closest('tr').index();
				var unit_price_near = $("tr:nth-child(" + (row_index + 1) + ") td:nth-child(4) input").val();
				var tt_price = ($(this).val() * unit_price_near).toFixed(2);
				$(".listing--item tr:nth-child(" + (row_index + 1) + ") td:nth-child(5)").html(tt_price);
				show_sub_total_new();
			}
		});
		// Unit Price Key up function
		$(document).on('change paste keyup', 'input.unit-price', function() {
			var isvalid = $.isNumeric($(this).val());
			if(!isvalid){
				alert("Numeric values only...");
			} else {
				row_index = $(this).closest('tr').index();
				var unit_price_near = $("tr:nth-child(" + (row_index + 1) + ") td:nth-child(4) input").val();
				var qty = $("tr:nth-child(" + (row_index + 1) + ") td:nth-child(3) input").val();
				var tt_price = (qty * unit_price_near).toFixed(2);				
				$(".listing--item tr:nth-child(" + (row_index + 1) + ") td:nth-child(5)").html(tt_price);
				show_sub_total_new();
			}
		});
		//Form submit
		$( "#sub_frm" ).click(function() {
			$( "#itemsForm" ).submit();
		});
		$("#sub_frm_1").click(function() {
				$("#orderDetails").val(2);
			$("#itemsForm").submit();

		});
		function sub_frm() {
			$('#contact_number1').val($('#contact_number').val());
			$('#contact_name1').val($('#contact_name').val());
			var contact_number = $('#contact_number').val();
			//var amount_given = $('#amount_given_val').val();
			//$('#amount_given').val(amount_given);
			// payment_type = $('#payment_type').val();
			//$('#payment_type1').val(payment_type);
			var card_num = $('#card_num').val();
			$('#card_num1').val(card_num);
			$('#discount1').val($('#discount').val());
			var amount_given = $('#amount_given_val').val();
			var amount_given_card = $('#amount_given_val_card').val();
			var payment_type = '';
			
			if(amount_given !='' && amount_given_card =='') {
				$('#amount_given').val(amount_given);
				$('#payment_type1').val('cash');
				$('#payment_type2').val('cash');
				amount_given_card = 0;

			}
			if(amount_given =='' && amount_given_card !='') {
				$('#amount_given').val(amount_given_card);
				$('#payment_type1').val('card');
				$('#payment_type2').val('card');
				var card_type = $('input[name = "card_type"]:checked').val();
				$('#card_type1').val(card_type);
				amount_given = 0;
			}
			if(amount_given !='' && amount_given_card !='') {
				$('#amount_given').val(parseFloat(amount_given) + parseFloat(amount_given_card));
				$('#payment_type1').val('both');
				$('#payment_type2').val('both');
				var card_type = $('input[name = "card_type"]:checked').val();
				$('#card_type1').val(card_type);				
			}
			//alert(amount_given+','+amount_given_card);
			$('#multiple_amount_given1').val(amount_given+','+amount_given_card);
			$('#multiple_payment_types1').val('cash,card');
			$('#multiple_amount_given2').val(amount_given+','+amount_given_card);
			$('#multiple_payment_types2').val('cash,card');
		//alert($('#amount_given').val());

			var grand_total = $('#total_value').val();
			//var balance_amount_sub = parseFloat(amount_given) - parseFloat(grand_total);
			/*if(balance_amount_sub >= 0) {
				$( "#payForm" ).submit();
			} else { 				
				//$('#error_value').html('Given amount should be greater than total');
				alert('Given amount should be greater than total');
			}*/
			//alert(amount_given_card);
			var balance_tot = parseFloat(amount_given) + parseFloat(amount_given_card)
			var balance_amount_sub = parseFloat(balance_tot) - parseFloat(grand_total);
			if(payment_type != 'credit') {
				if(balance_amount_sub >= 0) {
					$( "#payForm" ).submit();
				} else { 
					//$('#error_value').html('Given amount should be greater than total');
					alert('Given amount should be greater than total');
				}
			} else {
				if(contact_number != '') {
					$( "#payForm" ).submit();
				} else {
					alert('Please enter customer number');
				}
			}
		}
		$( "#pay_frm" ).click(function() {
			sub_frm();
		});
		$('#amount_given_val').on('keydown', function(e) {
			var keyCode = e.keyCode || e.which;
			if (keyCode == 13) {				
				sub_frm();
				e.preventDefault();
			}
		});
		$('#payment_type').on('keydown', function(e) {
			var keyCode = e.keyCode || e.which;
			if (keyCode == 13) {				
				sub_frm();
				e.preventDefault();
			}
		});
		$('#payForm').on('keyup keypress', function(e) {
		  var keyCode = e.keyCode || e.which;
		  if (keyCode === 13) { 
			e.preventDefault();
			return false;
		  }
		});
		/*$(document).ready(function () {
			$("#itemsForm").submit(function () {
				$(".sub_frm").attr("disabled", true);
				return true;
			});
		});*/
	  $(".prod-main").click(function () {
			var id = $(this).data('id');
			var name = $(this).data('name');
			var unit_price = $(this).data('price').toFixed(2);
			add_product_to_list(id, name, unit_price);
		});

		function add_product_to_list(id, name, unit_price) {
			var name1 = name.split(' ').join('_');
			var qty = "1";
			var price = parseFloat(unit_price * qty).toFixed(2);
			var counter_val = $("#counter_append");

			var minus_symbol = "<a class='act_btn minus_button'>-</a>";
			var plus_symbol = "<a class='act_btn plus_button'>+</a>";
			var notes_icon = "<a class='notes-icon' data-id='"+id+"'><img src='img/note.png' ></a>";			

			numRows = $(".listing--item tr").length;
			for(var i=1 ; i<numRows ; i++){
				var prod_id = $("tr:nth-child(" + i + ") td:nth-child(1)").children().val();
				var qty_plus = $("tr:nth-child(" + i + ") td:nth-child(3) input").val();

				if(prod_id == id){
					//Added by Mani These lines added for unit price change fix
					row_index = $(".item_scroll"+id).closest('tr').index();
					$("tr:nth-child(" + (row_index + 1) + ") td:nth-child(3) a.plus_button").trigger('click');
					return true;

					var price_plus = parseFloat(unit_price*(parseInt(qty_plus) + 1)).toFixed(2);
					$(".listing--item tr:nth-child(" + i + ") td:nth-child(3)").html(minus_symbol+"<input style='width: 45px;' name='quantity[]' class='quantity' type='number' value="+(parseInt(qty_plus) + 1)+">"+plus_symbol);
					$(".listing--item tr:nth-child(" + i + ") td:nth-child(5)").html(price_plus);
					show_sub_total_new();
					return true;
				}
			}		

			var list_val = "<tr><td style='width: 10%;'><input class='item_scroll"+id+"' type='hidden' name='items[]' value="+id+"><input type='hidden' name='item_name[]' value="+name1+"></td><td style='width: 45%;'>"+name+" </td><td style='width: 30%;'>"+minus_symbol+"<input style='width: 50px;' class='quantity' name='quantity[]' type='number' value="+qty+">"+plus_symbol+"</td><td style='width: 10%; text-align: right;'><input type='number' style='width: 100%;' class='unit-price' name='unit_price[]' value="+unit_price+"></td><td style='width: 15%; text-align: right;'>"+price+"</td></tr>";
			//counter_val.html(counter_val.html() + list_val);
			$("#counter_append").append(list_val);			
			show_sub_total_new();	
			document.getElementById('scroll_id').scrollIntoView(true);
		}


		//Given amount cal
			$("#amount_given_val, #amount_given_val_card").on("change paste keyup", function() {
				$('#error_value').html('');
			   //alert($(this).val());
			   var amount_given = $('#amount_given_val').val();
			    var amount_given_card = $('#amount_given_val_card').val();
				if(amount_given == '') {
					amount_given = 0;
				}
				if(amount_given_card == '') {
					amount_given_card = 0;
				}
				//if(amount_given != ''){
					var grand_total = $('#total_value').val();
					var balance_amount_sub = parseFloat(parseFloat(amount_given)+ parseFloat(amount_given_card)) - parseFloat(grand_total);
					if(amount_given == '' && amount_given_card == ''){
						$('#balance_amount_val').html('0');
					} else {
						$('#balance_amount_val').html(parseFloat(balance_amount_sub).toFixed(2));
					}
				//}
			});

		
	  </script>
	  <script type="text/javascript">
	  //Minus button
		$(document).on('click', '.minus_button', function(e) {
			//var unit_price_near = $(this).closest('td').next('td').html();
			row_index = $(this).closest('tr').index();
			var unit_price_near = $("tr:nth-child(" + (row_index + 1) + ") td:nth-child(4) input").val();
			var qty_minus = $("tr:nth-child(" + (row_index + 1) + ") td:nth-child(3) input").val();
			var currentVal = parseInt(qty_minus) - 1;
			if(currentVal > 0) {
				$("tr:nth-child(" + (row_index + 1) + ") td:nth-child(3) input").val(currentVal);
				var tt_price = parseFloat(currentVal * unit_price_near).toFixed(2);
				$(".listing--item tr:nth-child(" + (row_index + 1) + ") td:nth-child(5)").html(tt_price);
				show_sub_total_new();
				amount_given_fun();
			} else {
				if(confirm("Are you sure want to remove?")){
					$(this).closest ('tr').remove ();
					show_sub_total_new();
					amount_given_fun();
				}
			}
		});
		//Plus button
		$(document).on('click', '.plus_button', function(e) {
			//var unit_price_near = $(this).closest('td').next('td').html();
			row_index = $(this).closest('tr').index();
			var unit_price_near = $("tr:nth-child(" + (row_index + 1) + ") td:nth-child(4) input").val();
			var qty_plus = $("tr:nth-child(" + (row_index + 1) + ") td:nth-child(3) input").val();
			if(currentVal != 0) {
				var currentVal = parseInt(qty_plus) + 1;
				$("tr:nth-child(" + (row_index + 1) + ") td:nth-child(3) input").val(currentVal);
				var tt_price = parseFloat(currentVal * unit_price_near).toFixed(2);
				$(".listing--item tr:nth-child(" + (row_index + 1) + ") td:nth-child(5)").html(tt_price);
				show_sub_total_new();
				amount_given_fun();
			}
		});

		//SUB total
		function show_sub_total_new(){
			$("#sub_frm").removeAttr("disabled");
			$("#print_bill").attr("disabled","disabled");
			var total_price_val = 0;
			numRows = $(".listing--item tr").length;
			for(var i=1 ; i<numRows ; i++){
				total_price_val += parseFloat($("tr:nth-child(" + i + ") td:nth-child(5)").text());	
			}
			total_price_val = total_price_val.toFixed(2);
			/*$('#sub_total').val('');
			$('#sub_total').val(total_price_val);
			$('#grand_total').val('');
			var discount_total = $('#discount').val();
			if(discount_total == ''){discount_total = '0';}
			var grand_total_sub = Math.round(total_price_val - discount_total);
			$('#grand_total').val(grand_total_sub);*/
			var net_total_sub = total_price_val;
			<?php if(BILL_TAX == 'yes' && BILL_COUNTRY == 'UAE') { ?>
				var vat_total_sub = ((net_total_sub/100) * <?php echo BILL_TAX_VAL; ?>).toFixed(2);
				var grand_total_sub = (parseFloat(net_total_sub) + parseFloat(vat_total_sub)).toFixed(2);
			<?php } else { ?>
				var vat_total_sub = 0;
				var grand_total_sub = parseFloat(net_total_sub).toFixed(2);
			<?php } ?>
			$('#grand_total').val(grand_total_sub);
			$('#total_value').val(grand_total_sub);
			$('.net_total_tt').html(net_total_sub);
			$('.vat_total_tt').html(vat_total_sub);
			$('.grand_total_tt').html(grand_total_sub);
			$("#myInput").val('');
			$("#myInput").focus();
		}		
		//Discount
		$(document).on('change', 'input#discount', function() {
			var discount_total = $('#discount').val();
			var total_price_val = $('#sub_total').val();

			if(parseFloat(total_price_val) < parseFloat(discount_total)){
				alert("Please enter below total value?");
				$('#discount').val('');
				return true;
			} else if(parseFloat(total_price_val) > parseFloat(discount_total)) {				
				var grand_total_sub = Math.round(total_price_val - discount_total);
				$('#grand_total').val(grand_total_sub);
				amount_given_fun();
				return true;
			}
			
		});
		//Amount Given
		$(document).on('change', 'input#amount_given', function() {
			amount_given_fun();
		});
		function amount_given_fun(){
			var amount_given = $('#amount_given').val();
			if(amount_given != ''){
				var grand_total = $('#grand_total').val();
				var balance_amount_sub = parseFloat(amount_given) - parseFloat(grand_total);
				$('#balance_amount').val(balance_amount_sub);
			}
		}
		//Tab height
		$( document ).ready(function() {
			var search_height = $('.search-box').height();	
			//var search_height = 0;
			var tab_height = $('#items_cat').height();			
			var total_height = $( window ).height();
			var top_height = $('.top-head').height();
			$(".main-tb").css("height", (total_height - (search_height+tab_height+top_height+50)));
			var tab_width = $(".col-sm-8").width();
			$("#items_cat").css("width", (tab_width));
		});		
		</script>

		<script>
		//Search
		/*$( "#myInput" ).focus(function() {
		  alert( "Handler for .focus() called." );
		});*/
		function myFunction() {			
			var input, filter, div, h5, a, i;
			input = document.getElementById("myInput");
			filter = input.value.toUpperCase();
			li = document.getElementsByClassName("prod-fas");
			for (i = 0; i < li.length; i++) {
				a = li[i].getElementsByTagName("h5")[0];
				if (a.innerHTML.toUpperCase().indexOf(filter) > -1) {
					li[i].style.display = "";
				} else {
					li[i].style.display = "none";

				}
			}
		}
		/*$(document).ready(function() {
			var pressed = false; 
			var chars = []; 
			$(window).keypress(function(e) {
				if (e.which >= 48 && e.which <= 57) {
					chars.push(String.fromCharCode(e.which));
				}
				console.log(e.which + ":" + chars.join("|"));
				if (pressed == false) {
					setTimeout(function(){
						if (chars.length >= 10) {
							var barcode = chars.join("");
							console.log("Barcode Scanned: " + barcode);						
							// assign value to some input (or do whatever you want)
							//$("#myInput").val(barcode);
						}
						chars = [];
						pressed = false;
					},500);
				}
				pressed = true;
			});
		});*/
		$("#myInput").keypress(function(e){
			if ( e.which === 13 ) {
				//console.log("Prevent form submit.");
				var barcode_id = $("#myInput").val();
				var barcode_id_numeric = $.isNumeric(barcode_id);
				if(!barcode_id_numeric){
					myFunction();
				} else {
					$.ajax({
						url: 'ajax_barcode_check.php?barcode_id='+barcode_id,
						type: 'post',
						dataType: 'json',
						success: function(json) {
							if (json['success']) {
								var id = json['id'];
								var name = json['name'];
								var unit_price = json['unit_price'];						
								add_product_to_list(id, name, unit_price);
								$("#myInput").val('');						
							}
							if(json['error']) {
								alert("Error");
							}
						}
					 });
				}
				e.preventDefault();
			}
		});
		
		//Card num
			$('#payment_type').on('change', function() {
			  if(this.value == 'card')
				{
					$('.card_val').css("display", "block");
					$('.credit_val').css("display", "none");
				} else {
					$('.card_val').css("display", "none");
				}
			
			//Credit num
			if(this.value == 'credit')
			{
				$('.credit_val').css("display", "block");
				$('.card_val').css("display", "none");
			} else {
				$('.credit_val').css("display", "none");
			}
			})
			$("#card_num").keyup(function() {
				$("#card_num").val(this.value.match(/[0-9]*/));
			});

		//Customer name auto search
		$(document).ready(function(){
			$("#contact_number").keyup(function(){
				$.ajax({
				type: "POST",
				url: "ajax_customer.php",
				data:'keyword='+$(this).val(),
				beforeSend: function(){
					$("#contact_number").css("background","#FFF url(LoaderIcon.gif) no-repeat 165px");
				},
				success: function(data){
					$("#suggesstion-box").show();
					$("#suggesstion-box").html(data);
					$("#contact_number").css("background","#FFF");
				}
				});
			});
		});
		function selectCountry(cus_id, num, name, address) {
			$("#customer_id").val(cus_id);
			$("#contact_number").val(num);
			$("#contact_name").val(name);
			$("#address").val(address);
			$("#suggesstion-box").hide();
		}
		</script>

		<script type="text/javascript">
			$(document).ready(function() {
				$(function() {
				  $("#standard").customselect();
				});
				//var country = [<?php foreach($items as $item) { ?>"<?php echo $item['name']; ?>",<?php } ?>];
				//$("#country").select2({
				  //data: country
				//});
				$('#standard').on('change', function() {
					var id = $(this).val();
					var name = $(this).find(':selected').data('name');
					var unit_price = $(this).find(':selected').data('price');
					if(id) {
						add_product_to_list(id, name, unit_price);
					}					
					$(this).val('');
				});

				
			});

				
		</script>

		<script>
		/*function testAnim(x) {
			$('.modal .modal-dialog').attr('class', 'modal-dialog  ' + x + '  animated');
		};
		$('#payModal').on('show.bs.modal', function (e) {
		  var anim = 'tada';
			  testAnim(anim);
		})
		$('#payModal').on('hide.bs.modal', function (e) {
		  var anim = 'swing';
			  testAnim(anim);
		})*/

		//Given amount cal
	/*	$("#amount_given_val").on("change paste keyup", function() {
			$('#error_value').html('');
		   //alert($(this).val());
		   var amount_given = $('#amount_given_val').val();
			//if(amount_given != ''){
				var grand_total = $('#total_value').val();
				var balance_amount_sub = (parseFloat(amount_given) - parseFloat(grand_total)).toFixed(2);
				if(amount_given == ''){
					$('#balance_amount_val').html('0');
				} else {
					$('#balance_amount_val').html(parseFloat(balance_amount_sub).toFixed(2));
				}
			//}
		});
*/
		//Card num
		$('#payment_type').on('change', function() {
		  if(this.value == 'card')
			{
				$('.card_val').css("display", "block");
			} else {
				$('.card_val').css("display", "none");
			}
		})
		$("#card_num").keyup(function() {
			$("#card_num").val(this.value.match(/[0-9]*/));
		});

		//Add Notes Modal Open
		$(document).on('click', '.notes-icon', function(e) {
			var id = $(this).data('id');
			//Set product id
			$('#notesModal_item_id').val(id);
			
			//Set product notes
			var notes = $('#notes_for_'+id).val();			
			$('#product_notes').val(notes);			

			$('#notesModal').modal('show');
		});
		
		//Submit Add Notes 
		$("#notes_frm").click(function () {
			var product_id = $('#notesModal_item_id').val();
			var product_notes = $('#product_notes').val();

			if((product_id && product_notes) != '') {
				$('#notes_for_'+product_id).val(product_notes);
				$('#product_notes').val('');
				$('#notesModal').modal('hide');
			}
		});
		
		// Discount Key up function
		$(document).on('change paste keyup', 'input.discountt', function() {
			var isvalid1 = $.isNumeric($(this).val());
			var isvalid_val = $(this).val();
			$('#card_type').prop('checked', false);
			$('#amount_given_val').val('');
			$('#amount_given_val_card').val('');

			var total_price_val = 0;
			numRows = $(".listing--item tr").length;
			for(var i=1 ; i<numRows ; i++){
				total_price_val += parseFloat($("tr:nth-child(" + i + ") td:nth-child(5)").text());	
			}
								
			var vat_total_sub = parseFloat((total_price_val/100) * <?php echo BILL_TAX_VAL; ?>).toFixed(2);
			var total_price_val = parseFloat(parseFloat(total_price_val) + parseFloat(vat_total_sub)).toFixed(2);

			if(!isvalid1){
				alert("Numeric values only...");
				$('#total_value').val(parseFloat(total_price_val).toFixed(2));
				$('.grand_total_tt').html(parseFloat(total_price_val).toFixed(2));
			} else {
				$('#total_value').val(parseFloat(total_price_val - isvalid_val).toFixed(2));
				$('.grand_total_tt').html(parseFloat(total_price_val - isvalid_val).toFixed(2));
			}
		});
		</script>
   </body>
</html>