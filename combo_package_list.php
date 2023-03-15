<?php
session_start();
include("functions_web.php");
include_once("config.php");
connect_dre_db();
chkUserLoggedIn();
$getUserDetails = getUserDetails($_SESSION['user_id']);
$getUserDetails = explode(",", $getUserDetails['user_action']);
$items = getItemsList("ASC");
$main_id = (isset($_GET['id']) && $_GET['id'] !='') ? $_GET['id'] : '';
if($main_id != '') {
	$pack_items = getcomboitems($main_id);
	$pack_name = getcomboname($main_id);
	$pack_price = getcomboprice($main_id);
} else {
	redirect('index.php');
}
if(isset($_POST['combo_post'])) {
	$inputs = $_POST;
	$sale_insert = getSaleOrderItemDetails($inputs);
	$sale_order_id = $sale_insert['id'];


	// redirect('single_item_print.php?id='.$sale_order_id.'&re=index.php&combo=yes');
	if(DIRECT_PRINT != 'yes'){
		redirect('single_item_print.php?id='.$sale_order_id.'&re=index.php&combo=yes');
		}else{
		redirect('bill_print.php?id='.$sale_order_id.'&re=index.php&pay=given&cus_print=1&combo=yes');
	}
}?>


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
	  <title>Combo Package</title>
	  <meta name="description" content="" />
	  <meta name="keywords" content="" />
	  <meta name="viewport" content="width=device-width, initial-scale=1">
	  <link rel="stylesheet" href="css/bootstrap.min.css">
	  <link rel="stylesheet" href="css/main.css">
	  <link rel='stylesheet' href='css/counter.css'>
	  <link href="admin/plugins/select2/select2.css" rel="stylesheet">
	  <link rel="icon" type="image/png" sizes="32x32" href="img/favicon.png">
	  <style>
	   .table-val-result, .table-val-result p{text-align: center; font-weight: bold;}
		.table-val {
			padding-right: 9px;
			padding-left: 9px;
			border-radius: 9px;
			padding-top: 9px;
			padding-bottom: 9px;
			color: #fff;
		}
		.success{background: green;}
		.danger{background: red;}
		.normal{background: orange;}
		.border-btm{border-bottom: 1px solid #ccc;}
		.table-bdy{
		    text-align: center;
			padding: 30px;
			border: 1px solid #ccc;
			border-radius: 10px;
			font-size: 20px;
			color: #fff;
			font-weight: bold;
		}
		.table-str .col-sm-2{margin-top: 5px;}
		.table-remain-cnt {
			position: absolute;
			top: 5px;
			float: right;
			font-size: 12px;
			right: 25px;
		}
		.border-btm select{
			height: 45px;
			font-size: 20px;
		}
	  </style>
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
					 COMBO PACKAGE
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
		 		  
		  <div class="container table-str">
		  <h2 style="text-align:center;"><?php echo $pack_name; ?> - <?php echo $pack_price; ?></h3>
		  <form method="post">
		  <input name="id" id="id" type="hidden" value="<?php echo $main_id; ?>"/>
		  <input name="order_type" id="order_type" type="hidden" value="combo"/>
		 
		  <input type="hidden" id="payment_status" name="payment_status" value="paid">
		  <input type="hidden" id="customer_id" name="customer_id" value="">
		  <input type="hidden" id="combo_package_name" name="combo_package_name" value="<?php echo $pack_name; ?>">
		  <input type="hidden" id="combo_package_price" name="combo_package_price" value="<?php echo $pack_price; ?>">
		  <input name="combo_post" id="" type="hidden" />
		  <h4>Choose Items</h4>
		  <?php for($i=0; $i<$pack_items;$i++) { ?>
		  
				<span style="font-size:17px; font-weight:bold; padding:50px 10px;margin:50px 10px;"><?php echo $i+1; ?></span><br>
				<input name="quantity[]" id="quantity[]" value="1" type="hidden"/>
				<select class="items_lists" name="items[]" id="items[]" required style="width:100%;">
					<option value="">-- Select Item --</option>
					<?php foreach($items as $item) { ?>
						<option value="<?php echo $item['id']; ?>"><?php echo $item['name']; ?> - <?php echo $item['price'].' '.CURRENCY; ?></option>
					<?php } ?>
				</select>
		  
		  <?php } ?>
		  <hr>
		  <h4>Customer details</h4>
		  <input style="margin-bottom:10px;" class="form-control" name="contact_number" id="contact_number" value="" type="number" placeholder="Customer Number" required/>
		  <div id="suggesstion-box"></div>
		  <input style="margin-bottom:10px;" class="form-control" name="contact_name" id="contact_name" value="" type="text" placeholder="Customer Name" required/>
		  <input style="margin-bottom:10px;" class="form-control" name="address" id="address" value="" type="text" placeholder="Customer Address"/>
		  <hr>
			<label>Gst%</label>
			<select name="gst" id="gst" class="form-control" style="width: 100%;">
				<option value=''>--Select Percentage--</option>
				<option value="12">12%</option>
				<option value="18" >18%</option>
				<option value="28" >28%</option>
			</select>
		  <h4>Payment details</h4>
		   <?php if(BILL_TAX == 'yes'){
		   if (BILL_TAX_TYPE == 'VAT')  { ?>
		      <span class="fn--amt">Total Amount with VAT(<?php echo BILL_TAX_VAL; ?>%):  </span><span class="tt-amount vat_total_tt"><?php echo $pack_price+($pack_price*(BILL_TAX_VAL/100)); ?>  <?php echo CURRENCY; ?></span>
		  <?php }   else if (BILL_TAX_TYPE == 'GST') { ?>
		  	<span class="fn--amt">Total Amount with GST : </span><span class="tt-amount vat_total_tt"><?php 
		  		echo $pack_price; ?></span>
		<?php } } ?>		  
		  <input style="margin-bottom:10px;" class="form-control" name="given_amount" id="given_amount" value="" type="number" placeholder="Given Amount" required/>
		  <select class="form-control" name="payment_type" id="payment_type">
			<option value="cash">Cash</option>
			<option value="card">Card</option>
		  </select>
		  <button style="margin-top:20px;" type="submit" class="report-btn">SUBMIT</button>
		  </form>
		  </div>
	  </div>
	  <script src="js/jquery-3.2.1.min.js"></script> 
	  <script src="js/bootstrap.min.js"></script>
	  <script src="admin/plugins/select2/select2.js"></script>
	  <script>
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
		$("#gst").on("change", function (e) {
			var gst = $(this).val();
			var total_cgst = 0;
			var total_sgst = 0;
			var cgst = gst/2;
			var sgst = gst/2;
			var total_amount = $('#combo_package_price').val();
			if(total_amount > 0) {
			 	total_cgst += parseFloat(total_amount) - (parseFloat(100*total_amount) / (100+parseFloat(cgst)));
			    total_sgst += parseFloat(total_amount) - (parseFloat(100*total_amount) / (100+parseFloat(sgst)));
			} 
			var grand_tot = parseFloat(total_amount)+parseFloat(total_cgst)+parseFloat(total_sgst);
			$('.vat_total_tt').html(parseFloat(grand_tot).toFixed(2));
			
		});
	  </script>
	  <script type="text/javascript">
			$(".items_lists").select2();
	  </script>
	  <?php include('script_common.php'); ?>
   </body>
</html>