<?php 
   session_start();
   require_once 'db_functions.php';
   //include_once('config.php');
   //connect_dre_db();
   $function = New DB_Functions();
   $getUserDetails = $function->getUserDetails($_SESSION['user_id']);
	$getUserDetails = explode(",", $getUserDetails['user_action']);
	if (!in_array('reports',$getUserDetails)){
		//$function->redirect('index.php');
	}
   $from_date = (isset($_GET['from_date']) && $_GET['from_date'] != '') ? $_GET['from_date']:date('Y-m-d');
   $to_date = (isset($_GET['to_date']) && $_GET['to_date'] != '') ? $_GET['to_date']:date('Y-m-d');
   $shops = (isset($_GET['shops']) && $_GET['shops'] != '') ? $_GET['shops']:'';
   $payment_types = (isset($_GET['payment_type']) && $_GET['payment_type'] != '') ? $_GET['payment_type']:'';
   $sale_person = (isset($_GET['sale_per']) && $_GET['sale_per'] != '') ? $_GET['sale_per']:'';
   //echo "asdasd".$_GET['from_date'];
   
   function getShopsList()
   {
   	$service = array();
   	$query="SELECT * FROM ".DB_PRIFIX."locations_shops ORDER BY id ASC";
   	$run = mysqli_query($GLOBALS['conn'], $query);
   	while($row = mysqli_fetch_array($run)) {
   		$shop_id = $row['id'];
   		$service[$shop_id]['shop_id'] = $row['id'];
   		$service[$shop_id]['shop_name'] = $row['shop_name'];
   	}
   	return $service;	
   
   }
   
   function getSalePersonList()
   {
   	$service = array();
   	$query="SELECT * FROM ".DB_PRIFIX."users where role_id = '3' ORDER BY id ASC";
   	$run = mysqli_query($GLOBALS['conn'], $query);
   	while($row = mysqli_fetch_array($run)) {
   		$shop_id = $row['id'];
   		$service[$shop_id]['id'] = $row['id'];
   		$service[$shop_id]['user_name'] = $row['user_name'];
   	}
   	return $service;	
   
   }
   
   $sales_reports = array();
   if($from_date !='') {
   	$inputs['from_date'] = date("Y-m-d H:i:s", strtotime($from_date));
   	$inputs['to_date'] = date("Y-m-d 23:59:59", strtotime($to_date));
   	$inputs['shop_id'] = $shops;//$_SESSION['shop_id'];
   	$inputs['user_id'] = $sale_person;//$_SESSION['user_id'];
   	$inputs['order_type'] = '';
   	$inputs['payment_type'] = $payment_types;
   	$sales_reports = $function->getSaleOrderItemDetailsList($inputs);
   	//echo "<pre>"; print_r($sales_reports);
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
	  <title>Report Sales Page</title>
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
					 Sale Report
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
			<form method="get">
			   <div class="sale-report-box">
				  <table class="table">
					 <tbody>
						<tr style="border:none; border:0 !important;">
						   <td>From Date: <input name="from_date" class="from_date_pickr" id="from_date" value="<?php echo $from_date; ?>"/><img src="img/calender.png" class="from_date_pickr"></span></td>
						   <td>To Date: <input name="to_date" class="to_date_pickr" id="to_date" value="<?php echo $to_date; ?>" /><img src="img/calender.png" class="to_date_pickr"></span></td>
						   <!-- <td>
							  <select name="shops" class="form-control">
								 <option value="">  --Shops--  </option>
								 <?php $shop_list = getShopsList();
									foreach ($shop_list as $shop_lo)
									{ 
										?>
								 <option value="<?php echo $shop_lo['shop_id']; ?>" <?php echo ($shops == $shop_lo['shop_id']) ? ' selected="selected"' : Null; ?> ><?php echo $shop_lo['shop_name']; ?></option>
								 <?php
									}
									?>
							  </select>
						   </td> -->
						   <td>
							  <select class="form-control" name="payment_type" id="payment_type">
								 <option value=''>Select Payment Type</option>
								 <option value="cash" <?php if($payment_types == 'cash') { echo "Selected"; } ?>>cash</option>
								 <option value="card" <?php if($payment_types == 'card') { echo "Selected"; } ?>>card</option>						 
							</select>
						   </td>
						   <!-- <td>
							  <select name="sale_per" class="form-control">
								 <option value="">  --Sale Person--  </option>
								 <?php $sale_person = getSalePersonList();
									foreach ($sale_person as $sale_per)
									{ 
										?>
								 <option value="<?php echo $sale_per['id']; ?>" <?php echo ($sale_per == $sale_per['id']) ? ' selected="selected"' : Null; ?> ><?php echo $sale_per['user_name']; ?></option>
								 <?php
									}
									?>
							  </select>
						   </td> -->
						   <td><button style="7px 5px" class="report-btn">FILTER</button></td>
						</tr>
					 </tbody>
				  </table>
			   </div>
			</form>
			<div class="report-box">
			   <table class="table table-bordered bord">
				  <thead>
					 <tr bordercolor="#f56212;">
						<th>ID</th>						
						<th>Items</th>
						<th>Discount(INR)</th>
						<?php if(BILL_TAX == 'yes' && BILL_COUNTRY == 'UAE') { ?>
						<th>VAT(%)</th>
						<?php } ?>
						<th>TOTAL</th>
					 </tr>
				  </thead>
				  <tbody>
					 <?php if(!empty($sales_reports)) {
						foreach ($sales_reports as $sales_rep) { 
							$price = '0';
							$discount = $sales_rep['discount'];
							$order_type = $sales_rep['order_type'];
							$sale_order_item = json_decode($sales_rep['items']);
							foreach($sale_order_item as $item) { 
								$price += $item->price*$item->qty;								
							} ?>
							<?php if($order_type == 'combo') { ?>
							<tr>
								<td><?php echo $sales_rep['id']; ?></td>
								<td><?php echo count($sale_order_item).' - '.$order_type; ?></td>	
								<td><?php echo $discount; ?></td>
								<?php if(BILL_TAX == 'yes' && BILL_COUNTRY == 'UAE') { ?>
								<td><?php echo $sales_rep['combo_package_price']/100 * $sales_rep['vat']; ?></td>
								<td><?php echo $sales_rep['combo_package_price']+($sales_rep['combo_package_price']/100 * $sales_rep['vat']); ?></td>
								<?php } else { ?>
								<td><?php echo $sales_rep['combo_package_price']; ?></td>
								<?php } ?>
							 </tr>
							<?php } else { ?>
					 <tr>
						<td><?php echo $sales_rep['id']; ?></td>
						<td><?php echo count($sale_order_item); ?></td>	
						<td><?php echo $discount; ?></td>
						<?php if(BILL_TAX == 'yes' && BILL_COUNTRY == 'UAE') { ?>
						<td><?php echo $price/100 * $sales_rep['vat']; ?></td>
						<td><?php echo $price+($price/100 * $sales_rep['vat']); ?></td>
						<?php } else { ?>
						<td><?php echo $price; ?></td>
						<?php } ?>
					 </tr>
					 <?php } ?>
					 <?php } 
						}?>
				  </tbody>
			   </table>
			</div>
		 </div>
	  </div>
	  <script src="js/jquery-3.2.1.min.js"></script> 
	  <script src="js/bootstrap.min.js"></script>
	  <script src="js/jquery-date.js"></script>
	  <script src="js/jquery-ui.js"></script>
	  <script type="text/javascript">
		 $( ".from_date_pickr" ).datepicker({dateFormat: 'yy-mm-dd'});
		 $( ".to_date_pickr" ).datepicker({dateFormat: 'yy-mm-dd'});
		 
		  
	  </script>
	  <?php include('script_common.php'); ?>
   </body>
</html>