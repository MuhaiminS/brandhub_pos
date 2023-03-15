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
$total_amount = '';
$category = getCategoryList();
$item_img_dir = "item_images/";
$diver_img_dir = "driver_images/";
$driver_list = getDrivers();
//echo '<pre>'; print_r($driver_list); die;
if(isset($_POST['orderDetails'])) {
	$inputs = $_POST;
	//print_r($_POST); die;
	$sale_insert = getSaleOrderItemDetails($inputs);
	$sale_order_id = $sale_insert['id'];
	$discount = $sale_insert['discount'];
	$amount_given = $sale_insert['amount_given'];
	$balance = $sale_insert['balance_amount'];
	$receipt_id = $sale_insert['receipt_id'];
	$ordered_date = date("d-m-Y", strtotime($sale_insert['ordered_date']));
	$customer_id = $sale_insert['customer_id'];
	$cus_details = getCustomerDetail($customer_id);
	$result_arr = array();
	$sql = "SELECT * FROM ".DB_PRIFIX."sale_order_items WHERE sale_order_id = $sale_order_id";
	$result_val = mysqli_query($GLOBALS['conn'], $sql);
	while ($row = mysqli_fetch_assoc($result_val)) {
		$result_arr[] = $row;			
	}
	//echo "<pre>"; print_r($result_arr);
	//die;
?>
<?php redirect('single_item_print.php?id='.$sale_order_id.'&re=index.php&deliver=yes'); ?>
<?php redirect('delivery_sale.php'); } ?>
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
	  <title>Sale</title>
	  <meta name="description" content="" />
	  <meta name="keywords" content="" />
	  <meta name="viewport" content="width=device-width, initial-scale=1">
	  <link rel="stylesheet" href="css/bootstrap.min.css">
	  <link rel="stylesheet" href="css/main.css">
	  <link rel='stylesheet prefetch' href='css/animate.min.css'>
	  <link rel='stylesheet' href='css/counter.css'>
	  <link rel="icon" type="image/png" sizes="32x32" href="img/favicon.png">
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
					 <a href="index.php"><img src="img/out.jpg" alt="logo"></a>
				  </div>
			   </div>
			   <div class="col-sm-4">
				  <div class="counter-head">
					 Sale
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
			<input type="hidden" name="orderDetails" value="1" />
			<input name="order_type" type="hidden" value="counter_sale">
			<input type="hidden" id="grand_total" name="grand_total" value="">
			<input type="hidden" id="contact_number1" name="contact_number" value="">
			<input type="hidden" id="contact_name1" name="contact_name" value="">
			<input type="hidden" id="address1" name="address" value="">
			<input type="hidden" id="driver_id" name="driver_id" value="">
			<input type="hidden" id="payment_status" name="payment_status" value="paid">
			<input type="hidden" id="payment_type1" name="payment_type" value="">
			<input type="hidden" id="discount1" name="discount" value="">
			<input type="hidden" id="additional1" name="additional" value="">
			<div class="col-sm-4">
			   <div class="counter-left table-responsive">
				  <table class="table listing--item" style="border:none;">
					 <thead>
						<tr>
						   <th style="width: 10%;">Si</th>
						   <th style="width: 40%;">Name</th>
						   <th style="width: 30%;">Qty</th>
						   <th style="width: 10%;">Unit Price</th>
						   <th style="width: 10%;">Price</th>
						</tr>
					</thead>
					<tbody id="counter_append">
					 </tbody>
				  </table>
				  <div id="scroll_id"></div>
			   </div>
			   <div class="value-split">
					<span class="fn--amt">Net Total:  </span><span class="tt-amount net_total_tt">0.00</span><hr>
					<?php if(BILL_TAX == 'yes' && BILL_COUNTRY == 'UAE') { ?>
					<span class="fn--amt">VAT(<?php echo BILL_TAX_VAL; ?>%):  </span><span class="tt-amount vat_total_tt">0.00</span><hr>
					<?php } ?>
					<span class="fn--amt">Gross Total:  </span><span class="tt-amount grand_total_tt">0.00</span><hr>
			   </div>
			   <div class="counter-bottom-box">
					<a data-toggle="modal" data-target="#payModal" class="btn btn-default submit">SUBMIT</a>
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
					<span class="grand_total_tt"><?php echo number_format((float)round($total_amount), 2, '.', ''); ?></span>
				</div>
				<span class="error-dng" id="error_value"></span>
				<!-- <form id="payForm" method="post" action="" class="form-horizontal"> -->
				<div class="amount-given1">				
					<input type="hidden" id="total_value" name="total_value" value="<?php echo number_format((float)round($total_amount), 2, '.', ''); ?>">
					<input type="hidden" id="pay_amount" name="pay_amount" value="1">					
					<select id="payment_type" name="payment_type" style="width:100%;">
						<option value="cash">Cash</option>
						<option value="card">Card</option>
					</select>				
				</div>
				<!--</form>-->
				<div class="cus-det">
				<input class="login-sp" style="width: 49%;" value="" name="contact_number" id="contact_number" autocomplete="off" placeholder="Customer Numer" max="30" type="number" required>					 
				 <input class="login-sp" style="width: 50%;" value="" name="contact_name" id="contact_name" placeholder="Customer Name" max="15" type="text">
				 <div id="suggesstion-box"></div>
				 <input class="login-sp" style="width: 100%;" value="" name="address" id="address" placeholder="Address" max="50" type="text">	
				 <input class="login-sp" style="width: 100%;" value="" name="discount" id="discount" placeholder="Discount (INR)" max="50" type="text">	
				 <input class="login-sp" style="width: 100%;" value="" name="additional" id="additional" placeholder="Additional(INR)" max="50" type="text">
				 <input class="login-sp" style="width: 100%;" value="" name="amount_given" id="amount_given" placeholder="Amount given(INR)" max="50" type="text">				 
				 <span style="font-size:23px;">Balance amount :</span> <input class="login-sp" style="width: 100%;" value="" name="balance_amount" id="balance_amount" type="text" disabled readonly />
				</div>
				<?php if($driver_list) { ?>
				<div class="drivers-list">
				<?php foreach($driver_list as $driver) { ?>
					<div class="dv-val">
						<label><input type="radio" value="<?php echo $driver['id']; ?>" name="driver"/>
						<img src="<?php echo $diver_img_dir.$driver['image']; ?>" class="img-responsive"/>
						<p><?php echo $driver['name']; ?></p>
						</label>
					</div>
				<?php } ?>
				</div>
				<?php } ?>
			  </div>
			 <div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				<button type="button" id="pay_frm" class="btn btn-primary">confirm</button>
			  </div>
			</div>
		  </div>
		</div>
	  <script src="js/jquery-3.2.1.min.js"></script> 
	  <script src="js/bootstrap.min.js"></script>

	  <script src='js/jquery-customselect.js'></script>
    <link href='css/jquery-customselect.css' rel='stylesheet' />
	  
	  <script>
	  // Quantity Key up function
		$(document).on('change paste keyup', 'input.quantity', function() {
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
		function sub_frm() {
			var driver_id = '';
			$('#contact_number1').val($('#contact_number').val());
			$('#contact_name1').val($('#contact_name').val());
			var contact_number = $('#contact_number').val();
			$('#address1').val($('#address').val());
			$('#discount1').val($('#discount').val());
			$('#additional1').val($('#additional').val());
			$('#payment_type1').val($('#payment_type').val());
			//var amount_given = $('#amount_given_val').val();
			//$('#amount_given').val(amount_given);
			var grand_total = $('#grand_total').val();
			//var balance_amount_sub = parseFloat(amount_given) - parseFloat(grand_total);
			//if(contact_number != '') {
				if(grand_total > 0) {
					if($('input[name=driver]:checked').length<=0) {
						$('#error_value').html('Please select User');
					} else {
						var driver_id = document.querySelector('input[name = "driver"]:checked').value;
						$('#driver_id').val(driver_id);
						$( "#itemsForm" ).submit();						
					}
				} else {
					$('#error_value').html('Please select atleast one item');
				}
			//} else { 
				//$('#error_value').html('Customer number cannot be empty');
				//alert('Customer number cannot be empty');
			//}
		}
		$( "#pay_frm" ).click(function() {
			sub_frm();
		});
		$("#payModal input").keypress(function (e) {
			var keyCode = e.keyCode || e.which;
			if (keyCode == 13) {
				sub_frm();
			}
		});
		/*$('#payForm').on('keyup keypress', function(e) {
		  var keyCode = e.keyCode || e.which;
		  if (keyCode === 13) { 
			e.preventDefault();
			return false;
		  }
		});*/
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
			var qty = "1";
			var price = parseFloat(unit_price * qty).toFixed(2);
			var counter_val = $("#counter_append");

			var minus_symbol = "<a class='act_btn minus_button'>-</a>";
			var plus_symbol = "<a class='act_btn plus_button'>+</a>";			

			numRows = $(".listing--item tr").length;
			for(var i=1 ; i<numRows ; i++){
				var prod_id = $("tr:nth-child(" + i + ") td:nth-child(1)").children().val();
				var qty_plus = $("tr:nth-child(" + i + ") td:nth-child(3) input").val();

				if(prod_id == id){
					var price_plus = parseFloat(unit_price*(parseInt(qty_plus) + 1)).toFixed(2);
					$(".listing--item tr:nth-child(" + i + ") td:nth-child(3)").html(minus_symbol+"<input style='width: 50px;' name='quantity[]' class='quantity' type='number' value="+(parseInt(qty_plus) + 1)+">"+plus_symbol);
					$(".listing--item tr:nth-child(" + i + ") td:nth-child(5)").html(price_plus);
					show_sub_total_new();
					return true;
				}
			}	

			var list_val = "<tr><td style='width: 10%;'><input class='item_scroll"+id+"' type='hidden' name='items[]' value="+id+"></td><td style='width: 40%;'>"+name+"</td><td style='width: 30%;'>"+minus_symbol+"<input style='width: 50px;' class='quantity' name='quantity[]' type='number' value="+qty+">"+plus_symbol+"</td><td style='width: 10%; text-align: right;'><input type='number' style='width: 50px;' class='unit-price' name='unit_price[]' value="+unit_price+"></td><td style='width: 10%; text-align: right;'>"+price+"</td></tr>";
			//counter_val.html(counter_val.html() + list_val);
			$("#counter_append").append(list_val);
			show_sub_total_new();	
			document.getElementById('scroll_id').scrollIntoView(true);
		}		
	  </script>
	  <script type="text/javascript">
	  //Minus button
		$(document).on('click', '.minus_button', function(e) {
			//var unit_price_near = $(this).closest('td').next('td').html();
			row_index = $(this).closest('tr').index();			
			var qty_minus = $("tr:nth-child(" + (row_index + 1) + ") td:nth-child(3) input").val();
			var unit_price_near = $("tr:nth-child(" + (row_index + 1) + ") td:nth-child(4) input").val();
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
			var qty_plus = $("tr:nth-child(" + (row_index + 1) + ") td:nth-child(3) input").val();
			var unit_price_near = $("tr:nth-child(" + (row_index + 1) + ") td:nth-child(4) input").val();
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
			var total_price_val = 0;
			numRows = $(".listing--item tr").length;
			for(var i=1 ; i<numRows ; i++){
				total_price_val += parseFloat($("tr:nth-child(" + i + ") td:nth-child(5)").text());	
			}
			//alert(total_price_val);
			total_price_val = total_price_val.toFixed(2);
			//$('#sub_total').val('');
			//$('#sub_total').val(total_price_val);
			//('#grand_total').val('');
			//var discount_total = $('#discount').val();
			//if(discount_total == ''){discount_total = '0';}
			//var grand_total_sub = Math.round(total_price_val - discount_total);
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
		$(document).on('change', 'input#amount_given', function() {//alert(123);
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
			

			function testAnim(x) {
				$('.modal .modal-dialog').attr('class', 'modal-dialog  ' + x + '  animated');
			};
			$('#payModal').on('show.bs.modal', function (e) {
			  var anim = 'tada';
				  testAnim(anim);
			})
			$('#payModal').on('hide.bs.modal', function (e) {
			  var anim = 'swing';
				  testAnim(anim);
			})

			//Given amount cal			
			$("#amount_given_val").on("change paste keyup", function() {
				$('#error_value').html('');
			   //alert($(this).val());
			   var amount_given = $('#amount_given_val').val();
				//if(amount_given != ''){
					var grand_total = $('#total_value').val();
					var balance_amount_sub = parseFloat(amount_given) - parseFloat(grand_total);
					if(amount_given == ''){
						$('#balance_amount_val').html('0');
					} else {
						$('#balance_amount_val').html(balance_amount_sub);
					}
				//}
			});

			$("#contact_number").on("change paste keyup", function() {
				$('#error_value').html('');
			});
			$("#contact_name").on("change paste keyup", function() {
				$("#customer-list").css("display","none");
			});
			$("#address").on("change paste keyup", function() {
				$("#customer-list").css("display","none");
			});			    
				
		</script>
		<?php include('script_common.php'); ?>
   </body>
</html>