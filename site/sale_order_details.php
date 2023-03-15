<?php 
   session_start();
   require_once 'db_functions.php';
   //include_once('config.php');
   //connect_dre_db();
   $function = New DB_Functions();
   $getUserDetails = $function->getUserDetails($_SESSION['user_id']);
	$getUserDetails = explode(",", $getUserDetails['user_action']);
	if (!in_array('delivery_sale',$getUserDetails)){
		$function->redirect('index.php');
	}
   $from_date = (isset($_GET['from_date']) && $_GET['from_date'] != '') ? $_GET['from_date']:date('Y-m-d');
   $to_date = (isset($_GET['to_date']) && $_GET['to_date'] != '') ? $_GET['to_date']:date('Y-m-d');
   
   $inputs['from_date'] = $from_date.' 00:00:00';
   $inputs['to_date'] = $to_date.' 23:59:59';
   $inputs['shop_id'] = '';//$_SESSION['shop_id'];
   $inputs['user_id'] = '';//$_SESSION['user_id'];
   $inputs['order_type'] = '';
   $inputs['payment_type'] = '';
   
   $sale_orders = $function->getSaleOrderItemDetailsList($inputs);
   $last_settle_date = $function->getLastSettleDate(); 

   
   //echo "<pre>";print_r($sale_orders);
   
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
	  <link rel="stylesheet" href="css/main.css">
	  <link rel="icon" type="image/png" sizes="32x32" href="img/favicon.ico">
	  <link rel="stylesheet" href="css/bootstrap.css">
	  <link rel='stylesheet prefetch' href='css/animate.min.css'>
	  <link href="css/jquery-ui.css" rel="stylesheet">
	  <style>
		 .thead_cl th:nth-child(2) {
		 text-align: right;
		 }
		 .body_cl th:nth-child(2) {
		 text-align: right;
		 }
		 .thead_cl th {
		 border: unset !important;
		 }
		 .table.newtable {
		 background: #80481c none repeat scroll 0 0;
		 border-radius: 8px;
		 }
		 .body_cl th, .thead_cl th {
		 color: #fff;
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
					 <a href="index.php"><img src="img/out.png" alt="logo"></a>
				  </div>
			   </div>
			   <div class="col-sm-4">
				  <div class="counter-head">
					 Sale Order Details
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
						   <td><button class="report-btn">FILTER</button></td>
						</tr>
					 </tbody>
				  </table>
			   </div>
			</form>
			<?php if(!empty($sale_orders)) {
			   foreach($sale_orders as $sale_order) {
				$price = '0';
				$sale_order_item = json_decode($sale_order['items']);
				foreach($sale_order_item as $item) { 
					$price += $item->price*$item->qty;
				} ?>
			<div class="report-box">
			   <table class="table newtable">
				  <thead class="thead_cl">
					 <tr>
						<th></th>
						<th>
							<?php 
								  $ordered_date_check = $sale_order['ordered_date'];
								if(strtotime($ordered_date_check) > strtotime($last_settle_date))
								{
							?>
							<?php } ?>
							<?php if($sale_order['order_type'] != 'combo'){
							?>
							<a href="sale_order_edit.php?order=<?php echo $sale_order['id']; ?>" class="print-btnn">Edit</a>
							<?php } else { ?>
							<a href="combo_package_list_edit.php?order=<?php echo $sale_order['id']; ?>&name=<?php echo $sale_order['combo_package_name']; ?>" class="print-btnn">Edit</a>
							<?php } ?>
							<a href="single_item_print.php?id=<?php echo $sale_order['id']; ?>&re=sale_order_details.php" class="print-btnn">Print</a></th>
					 </tr>
					 <tr>
						<th>Contact Name: <?php echo $sale_order['contact_name']; ?></th>
						<th>Recepit No: <?php echo $sale_order['receipt_id']; ?></th>
					 </tr>
				  </thead>
				  <tbody class="body_cl">
					 <tr>
						<th><?php echo date("d-m-Y", strtotime($sale_order['ordered_date'])); ?></th>
						<th>Discount INR <?php echo number_format($sale_order['discount'], 2); ?></th>
					 </tr>
					 <?php if($sale_order['order_type'] != 'combo') { ?>
					 <tr>
						<th><a data-toggle='modal' data-target='#myModal<?php echo $sale_order['id']; ?>' class="det-po" style='cursor:pointer;' title="View Details">View Details</a>	</th>
						<?php if(BILL_TAX == 'yes' && BILL_COUNTRY == 'UAE') { ?>
						<th>Total INR <?php echo number_format($price+($price/100 * 5), 2); ?></th>
						<?php } else { ?>
						<th>Total INR <?php echo number_format($price, 2); ?></th>
						<?php } ?>
					 </tr>
					 <?php } else { ?>
					 <tr>
						<th><a data-toggle='modal' data-target='#myModal<?php echo $sale_order['id']; ?>' class="det-po" style='cursor:pointer;' title="View Details">View Details</a>	</th>
						<?php if(BILL_TAX == 'yes' && BILL_COUNTRY == 'UAE') { 
						// echo '<pre>';print_r($sale_order['combo_package_price'] + ($sale_order['vat'])); ?>
						<th>Total INR 
							<?php echo number_format(($sale_order['combo_package_price'] + $sale_order['vat']), 2); ?></th>
						<?php } else { ?>
						<th>Total INR <?php echo number_format($sale_order['combo_package_price'], 2); ?></th>
						<?php } ?>
					 </tr>
					 <?php } ?>
				  </tbody>
			   </table>
			</div>
			<?php } 
			   } else { ?>
			   <p>No Orders found to list.</p>
			   <?php } ?>
		 </div>
	  </div>
	  <?php include("sale_order_items.php"); ?>
	  <script src="js/jquery-3.2.1.min.js"></script>
	  <script src="js/bootstrap.min.js"></script>
	  
	  <?php if(!empty($sale_orders)) {
		foreach($sale_orders as $sale_order) { ?>
	  <script>
		function testAnim(x) {
				$('.modal .modal-dialog').attr('class', 'modal-dialog  ' + x + '  animated');
			};
			$('#myModal<?php echo $sale_order['id']; ?>').on('show.bs.modal', function (e) {
			  var anim = 'tada';
				  testAnim(anim);
			})
			$('#myModal<?php echo $sale_order['id']; ?>').on('hide.bs.modal', function (e) {
			  var anim = 'swing';
				  testAnim(anim);
			})
		</script>
		<?php } } ?>
		<script src="js/jquery-date.js"></script>
	  <script src="js/jquery-ui.js"></script>
	  <script type="text/javascript">
		 $( ".from_date_pickr" ).datepicker({dateFormat: 'yy-mm-dd'});
		 $( ".to_date_pickr" ).datepicker({dateFormat: 'yy-mm-dd'});
		 
		  
	  </script>
	  <?php include('script_common.php'); ?>
   </body>
</html>