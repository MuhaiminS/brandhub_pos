<?php 
	session_start();
	include("../functions.php");
	include_once("../config.php");
	chkAdminLoggedIn();
	connect_dre_db();
	
	if(isset($_GET['act']) && $_GET['act'] != '' && isset($_GET['id']) && $_GET['id'] > 0) {	
		$action = $_GET['act'];
		$purchase_order_id = $_GET['id'];	
		if($action == 'paid') {
			$qry = "UPDATE purchase_orders SET payment_status = 'not_paid' WHERE id = '$purchase_order_id'";
			if(mysqli_query($GLOBALS['conn'], $qry)){
				//UserLogDetails($_SESSION['user_id'], $purchase_order_id, 'purchase_orders', 'paid');
				redirect('purchases.php?resp=updatesucc');
			}
		}
		else if($action == 'not_paid') {
			$qry = "UPDATE purchase_orders SET payment_status = 'paid' WHERE id = $purchase_order_id";		
			if(mysqli_query($GLOBALS['conn'], $qry)){
				//UserLogDetails($_SESSION['user_id'], $purchase_order_id, 'purchase_orders', 'not_paid');
				redirect('purchases.php?resp=updatesucc');
			}
		}
		if($action == 'activate') {
			$qry = "UPDATE purchase_orders SET is_active = '1' WHERE id = '$purchase_order_id'";
			if(mysqli_query($GLOBALS['conn'], $qry)){
			redirect('purchases.php?resp=updatesucc');
			}
		}
		else if($action == 'deactivate') {
			$qry = "UPDATE purchase_orders SET is_active = '0' WHERE id = $purchase_order_id";		
			if(mysqli_query($GLOBALS['conn'], $qry)){
			}
		}
		else if($action != '' && $_GET['s'] == 1) { 
			$qry = "UPDATE purchase_orders SET status = '$action' WHERE id = $purchase_order_id";		
			if(mysqli_query($GLOBALS['conn'], $qry)){
				//UserLogDetails($_SESSION['user_id'], $purchase_order_id, 'purchase_orders', 'not_paid');
				//ADD Stock
				$purchase_order = $_GET['id'];
				if($action == 'received') {
					$update_order = "SELECT * FROM purchase_order_items WHERE purchase_id = $purchase_order";
					$update_order_edit = mysqli_query($GLOBALS['conn'], $update_order);		
					while ($edit_row = mysqli_fetch_assoc($update_order_edit)) {					
						$item_id = $edit_row['product_id'];
						$qty = $edit_row['qty'];
						$unit_id = $edit_row['unit_id'];
						//ADD Stock						
						$sql = "SELECT * FROM item_price WHERE unit_id = '$unit_id' AND product_id = '$item_id'";								
						$item_details = mysqli_fetch_assoc(mysqli_query($GLOBALS['conn'], $sql));
						$item_id_i = $item_details['id'];
						$stock = $item_details['stock'];
						$stock_added = $stock + $qty;
						mysqli_query($GLOBALS['conn'], "UPDATE item_price SET stock = '$stock_added' WHERE id = '$item_id_i'");
						
					}
					//die;
					redirect('purchases.php?stat=updatesucc');
				}			
			}
		}
	}
	
	function getUserName($user_id)
	{
		$where = "WHERE id = '$user_id'";
		$service = getnamewhere('users', 'user_name', $where);
		return $service;
	}
	function getShopName($shop_id)
	{
		$where = "WHERE id = '$shop_id'";
		$service = getnamewhere('locations_shops', 'shop_name', $where);
		return $service;
	}
	function getSuppliersName($suppliers_id)
	{
		$where = "WHERE id = '$suppliers_id'";
		$service = getnamewhere('suppliers', 'supplier_name', $where);
		return $service;
	}
	function getShopsList()
	{
		$service = array();
		$query="SELECT * FROM locations_shops ORDER BY id ASC";
		$run = mysqli_query($GLOBALS['conn'], $query);
		while($row = mysqli_fetch_array($run)) {
			$shop_id = $row['id'];
			$service[$shop_id]['shop_id'] = $row['id'];
			$service[$shop_id]['shop_name'] = $row['shop_name'];
		}
		return $service;	
	}
	
	function getItemNames($item_id)
	{
		$where = "WHERE id = '$item_id'";
		$service = getnamewhere('items', 'name', $where);
		return $service;
	}
	
	if(isset($_GET["page"])) {
		$page = (int)$_GET["page"];
	} else {
		$page = 1;
	}
	$setLimit = 25;
	$pageLimit = ($page * $setLimit) - $setLimit;
	
	function getSaleorders($reference_id = '',$payment_status = '', $from_date ='', $to_date='', $shop='', $pageLimit='', $setLimit='')
	{
		$date = date('Y-m-d');
		$qry="SELECT * FROM purchase_orders WHERE 1";
		
		if($reference_id != ''){
			$qry .=" AND reference_id = '$reference_id'";
		}
		if($payment_status != ''){
			$qry .=" AND payment_status = '$payment_status'";
		}
		if($from_date != '' && $to_date != '' ) {
			
			$qry .= " AND purchase_date BETWEEN '$from_date 00:00:00' AND '$to_date 23:59:59' ";		
		} 
		if($from_date != '' && $to_date == '' ) {
			$qry .= " AND purchase_date >= '$from_date 23:59:59'";
		}
	
		if($from_date == '' && $to_date != '' ) {
			$qry .= " AND purchase_date <= '$to_date 23:59:59'";
		}
		$qry .=" ORDER BY id DESC LIMIT $pageLimit, $setLimit";
		//echo $qry;
		$result=mysqli_query($GLOBALS['conn'], $qry);
		//$num=mysql_num_rows($result);
		//echo "total result ".$num;
		if($result)
		{
			return $result;
		}
		else
		return false;
	}
	
	function displayPaginationBelows($per_page,$page,$reference_id = '',$payment_status = '', $from_date ='', $to_date='', $shop='') {
	    $page_url="?";
		$date = date('Y-m-d');
		$query = "SELECT COUNT(*) as totalCount FROM purchase_orders WHERE 1 ";
		if($reference_id != ''){
			$query .=" AND reference_id = '$reference_id'";
		}
		//if($shop != ''){
			//$query .=" AND shop_id = '$shop'";
		//}
		if($payment_status != ''){
			$query .=" AND payment_status = '$payment_status'";
		}
		if($from_date != '' && $to_date != '' ) {
			
			$query .= " AND purchase_date BETWEEN '$from_date 00:00:00' AND '$to_date 23:59:59' ";		
		} 
		if($from_date != '' && $to_date == '' ) {
			$query .= " AND purchase_date >= '$from_date 23:59:59'";
		}
		if($from_date == '' && $to_date != '' ) {
			$query .= " AND purchase_date <= '$to_date 23:59:59'";
		}	
		//print_r($query);exit;
		$rec = mysqli_fetch_array(mysqli_query($GLOBALS['conn'], $query));
		$total = $rec['totalCount'];
		$adjacents = "2";
		$page = ($page == 0 ? 1 : $page); 
		$start = ($page - 1) * $per_page; 
		$prev = $page - 1; 
		$next = $page + 1;
		$setLastpage = ceil($total/$per_page);
		$lpm1 = $setLastpage - 1;
		$setPaginate = "";
		if($setLastpage > 1)
		{  
			$setPaginate .= "<ul class='setPaginate'>";
					$setPaginate .= "<li class='setPage'>Page $page of $setLastpage</li>";
			if ($setLastpage < 7 + ($adjacents * 2))
			{  
				for ($counter = 1; $counter <= $setLastpage; $counter++)
				{
					if ($counter == $page)
						$setPaginate.= "<li><a class='current_page'>$counter</a></li>";
					else
						$setPaginate.= "<li><a href='{$page_url}page=$counter&reference_id=$reference_id&shop=$shop&payment_status=$payment_status&from_date=$from_date&to_date=$to_date'>$counter</a></li>";
				}
			}
			elseif($setLastpage > 5 + ($adjacents * 2))
			{
				if($page < 1 + ($adjacents * 2)) 
				{
					for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++)
					{
						if ($counter == $page)
							$setPaginate.= "<li><a class='current_page'>$counter</a></li>";
						else
							$setPaginate.= "<li><a href='{$page_url}page=$counter&reference_id=$reference_id&shop=$shop&payment_status=$payment_status&from_date=$from_date&to_date=$to_date'>$counter</a></li>";
					}
					$setPaginate.= "<li class='dot'>...</li>";
					$setPaginate.= "<li><a href='{$page_url}page=$lpm1&reference_id=$reference_id&shop=$shop&payment_status=$payment_status&from_date=$from_date&to_date=$to_date'>$lpm1</a></li>";
					$setPaginate.= "<li><a href='{$page_url}page=$setLastpage&reference_id=$reference_id&shop=$shop&payment_status=$payment_status&from_date=$from_date&to_date=$to_date'>$setLastpage</a></li>"; 
				}
				elseif($setLastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2))
				{
					$setPaginate.= "<li><a href='{$page_url}page=1&reference_id=$reference_id&shop=$shop&payment_status=$payment_status&from_date=$from_date&to_date=$to_date'>1</a></li>";
					$setPaginate.= "<li><a href='{$page_url}page=2&reference_id=$reference_id&shop=$shop&payment_status=$payment_status&from_date=$from_date&to_date=$to_date'>2</a></li>";
					$setPaginate.= "<li class='dot'>...</li>";
					for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++)
					{
						if ($counter == $page)
							$setPaginate.= "<li><a class='current_page'>$counter</a></li>";
						else
							$setPaginate.= "<li><a href='{$page_url}page=$counter&reference_id=$reference_id&shop=$shop&payment_status=$payment_status&from_date=$from_date&to_date=$to_date'>$counter</a></li>";
					}
					$setPaginate.= "<li class='dot'>..</li>";
					$setPaginate.= "<li><a href='{$page_url}page=$lpm1&reference_id=$reference_id&shop=$shop&payment_status=$payment_status&from_date=$from_date&to_date=$to_date'>$lpm1</a></li>";
					$setPaginate.= "<li><a href='{$page_url}page=$setLastpage&reference_id=$reference_id&shop=$shop&payment_status=$payment_status&from_date=$from_date&to_date=$to_date'>$setLastpage</a></li>"; 
				}
				else
				{
					$setPaginate.= "<li><a href='{$page_url}page=1&reference_id=$reference_id&shop=$shop&payment_status=$payment_status&from_date=$from_date&to_date=$to_date'>1</a></li>";
					$setPaginate.= "<li><a href='{$page_url}page=2&reference_id=$reference_id&shop=$shop&payment_status=$payment_status&from_date=$from_date&to_date=$to_date'>2</a></li>";
					$setPaginate.= "<li class='dot'>..</li>";
					for ($counter = $setLastpage - (2 + ($adjacents * 2)); $counter <= $setLastpage; $counter++)
					{
						if ($counter == $page)
							$setPaginate.= "<li><a class='current_page'>$counter</a></li>";
						else
							$setPaginate.= "<li><a href='{$page_url}page=$counter&reference_id=$reference_id&shop=$shop&payment_status=$payment_status&from_date=$from_date&to_date=$to_date'>$counter</a></li>";
					}
				}
			}
			if ($page < $counter - 1){
				$setPaginate.= "<li><a href='{$page_url}page=$next&reference_id=$reference_id&shop=$shop&payment_status=$payment_status&from_date=$from_date&to_date=$to_date'>Next</a></li>";
				$setPaginate.= "<li><a href='{$page_url}page=$setLastpage&reference_id=$reference_id&shop=$shop&payment_status=$payment_status&from_date=$from_date&to_date=$to_date'>Last</a></li>";
			}else{
				$setPaginate.= "<li><a class='current_page'>Next</a></li>";
				$setPaginate.= "<li><a class='current_page'>Last</a></li>";
			}
			$setPaginate.= "</ul>\n"; 
		}
		return $setPaginate;
	}
	

	function getcategoryName($category_id)
	{
		$where = "WHERE id = '$category_id'";
		$category = getnamewhere('category', 'category_title', $where);
		return $category;
	}
	
	function getPurchaseOrderitems($id)
	{	
		$id = isset($id) ? $id : '';
		$qry="SELECT * FROM purchase_order_items WHERE purchase_id = '".$id."'";
		
		//echo $qry;
		$result=mysqli_query($GLOBALS['conn'], $qry);
		$num=mysqli_num_rows($result);
		
		if($num>0)
		{
			return $result;
		}
		else
		return false;
	}				
	
	$status_arr =  array(
		'pending' => 'Pending',
		'progressing' => 'Progressing',
		'ready_for_delivery' => 'Ready for delivery',
		'completed' => 'Completed',
		'delivered' => 'Delivered',
		'cancel' => 'canceled',
		'draft' => 'Draft'
	);
	$shops = (isset($_GET['shop']) && $_GET['shop'] !='') ? $_GET['shop'] : '';
	$reference_ids = (isset($_GET['reference_id']) && $_GET['reference_id'] !='') ? $_GET['reference_id'] : '';
	$payment_status = (isset($_GET['payment_status']) && $_GET['payment_status'] !='') ? $_GET['payment_status'] : '';
	$from_date1 = (isset($_GET['from_date']) && $_GET['from_date'] !='') ? date('Y-m-d',strtotime($_GET['from_date'])) : '';
	$to_date1 = (isset($_GET['to_date']) && $_GET['to_date'] !='') ? date('Y-m-d',strtotime($_GET['to_date'])) : '';
	
	?>
<!DOCTYPE html>
<!--
	This is a starter template page. Use this page to start your new project from
	scratch. This page gets rid of all links and provides the needed markup only.
	-->
<html>
	<head>
		<?php include("common/header.php"); ?>     
		<?php include("common/header-scripts.php"); ?>
	</head>
	<!--
		BODY TAG OPTIONS:
		=================
		Apply one or more of the following classes to get the
		desired effect
		|---------------------------------------------------------|
		| SKINS         | skin-blue                               |
		|               | skin-black                              |
		|               | skin-purple                             |
		|               | skin-yellow                             |
		|               | skin-red                                |
		|               | skin-green                              |
		|---------------------------------------------------------|
		|LAYOUT OPTIONS | fixed                                   |
		|               | layout-boxed                            |
		|               | layout-top-nav                          |
		|               | sidebar-collapse                        |
		|               | sidebar-mini                            |
		|---------------------------------------------------------|
		-->
	<body class="hold-transition skin-blue sidebar-mini">
		<div class="wrapper">
			<?php include("common/topbar.php"); ?>
			<?php include("common/sidebar.php"); ?>
			<!-- Content Wrapper. Contains page content -->
			<div class="content-wrapper">
				<!-- Content Header (Page header) -->
				<section class="content-header">
					<h1>
						Purchases
						<!--<small>Optional description</small>-->
					</h1>
					<ol class="breadcrumb">
						<li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
						<li class="active">Purchases</li>
					</ol>
				</section>
				<!-- Main content -->
				<section class="content container-fluid">
					<div class="row">
						<div class="col-xs-12">
							<div class="box">
								<div class="box-header">
									<h3 class="box-title">Manage Purchases</h3>
								</div>
								<!-- /.box-header -->
								<div class="box-body">
									<?php include("common/info.php"); ?>
									<div id="form" class="panel panel-warning" style="display: block;">
										<div class="panel-body">
											<form action="purchases.php" accept-charset="utf-8">
												<div class="row">
													<div class="col-sm-4">
														<div class="form-group">
															<label for="reference_id">Reference no.</label> <input type="text" name="reference_id" value="<?php echo $reference_ids; ?>" class="form-control tip" id="reference_id">
														</div>
													</div>
													<div class="col-sm-4">
														<div class="form-group">
															<label>Start date:</label>
															<div class="input-group date">
																<div class="input-group-addon">
																	<i class="fa fa-calendar"></i>
																</div>
																<input type="text" name="from_date" class="form-control datepicker pull-right" id="from_date" value="<?php echo $from_date1; ?>">
															</div>
														</div>
													</div>
													<div class="col-sm-4">
														<div class="form-group">
															<label>End date:</label>
															<div class="input-group date">
																<div class="input-group-addon">
																	<i class="fa fa-calendar"></i>
																</div>
																<input type="text" name="to_date" class="form-control datepicker pull-right" id="to_date" value="<?php echo $to_date1; ?>">
															</div>
														</div>
													</div>
													<div class="col-sm-12">
														<button type="submit" class="btn btn-primary">Submit</button>
														<a href="purchases.php" class="btn btn-default">Reset</a>
													</div>
												</div>
											</form>
										</div>
									</div>
									<?php include("purchase_order_items.php"); ?>

										<div class="row">
											<div class="col-md-6"></div>
											<div class="col-md-6 text-right pr0">
												<div class="dt-buttons btn-group">
													<a target="_blank" href="excel_export_purchase.php?reference_id=<?php echo $reference_ids; ?>&payment_status=<?php echo $payment_status; ?>&from_date=<?php echo $from_date1; ?>&to_date=<?php echo $to_date1; ?>" class="btn btn-default buttons-csv buttons-html5 export"><span>Excel</span></a>
												</div>
											</div>
										</div>										
										<table id="example2" class="table table-bordered table-hover">
										<thead>
											<tr>
												<th>#</th>
												<th>Date purchase</th>
												<th>Supplier</th>
												<th>Reference</th>
												<th>Qty</th>
												<th>Price</th>
												<th>Total VAT</th>
												<th>Total Amount</th>
												<th>Status</th>
												<th>Payment Status</th>
												<th>Action</th>
											</tr>
										</thead>
										<tbody>
											<?php	
												$grand_total = 0.00;												
												$prs = getSaleorders($reference_ids,$payment_status,$from_date1, $to_date1,$shops, $pageLimit, $setLimit);	
												if($prs != false) {
													$pcount = mysqli_num_rows($prs);
													if($pcount > 0) {
														$total_vat = $total12 = $total11 =0.00;
														for($p = 0; $p < $pcount; $p++) {
															$prow = mysqli_fetch_object($prs);
															$id = $prow->id;
															$reference_id = $prow->reference_id;
															$supplier_id = $prow->supplier_id;
															$suppliers_name = getSuppliersName($supplier_id);
															//$company_name = $prow->company_name;
															$purchase_date = $prow->purchase_date;
															$payment_status = $prow->payment_status;
															$status = $prow->status;	
															$total = $net_total = $total1 = 0.00;
															$prs1 = getPurchaseOrderitems($id);		
															$rev_status = ($prow->is_active) ? 'deactivate' : 'activate';															
															if($prs1 != false) {
																$pcount1 = mysqli_num_rows($prs1);
																if($pcount1 > 0) {
																	for($p1 = 0; $p1 < $pcount1; $p1++) {
																		$prow1 = mysqli_fetch_object($prs1);
																		$pur_order_item_id = $prow1->id;													
																		$price = $prow1->unit_price;
																		$qty = $prow1->qty;
																		$total_amount = $prow1->total_amount;
																		$vat = $prow1->tax;
																		$price_qty = $price * $qty;
																		$total1 += $price_qty;
																		$total += $price_qty+ ($price_qty * ($vat/100));
																	}
																}
															}
															$total11 += $total1;
															$total12 = $total;
															$total_vat = ($total1 * ($vat/100)); ?>
											<tr>
												<td><?php echo $id; ?></td>
												<td><?php echo date("d-m-Y",strtotime($purchase_date)); ?></td>
												<td><?php echo $suppliers_name; ?></td>
												<td><?php echo $reference_id; ?></td>
												<td><?php echo $qty; ?></td>
												<td><?php echo $total1; ?></td>
												<td><?php echo $total_vat; ?></td>
												<td align="right"><?php echo number_format($total12,2); ?></td>
												<td>
													<?php
														if($status != "received") {?>
													<select name="status" id="status" class="form-control" onchange='changeStatus(this, "<?php echo $id ?>");' >
														<option value="pending" <?php if($status == "pending") { echo "selected"; } ?> >Pending</option>
														<option value="ordered" <?php if($status == "ordered") { echo "selected"; } ?> >Ordered</option>
														<option value="received" <?php if($status == "received") { echo "selected"; } ?> >Received</option>
													</select>
													<?php	
														} else { ?>
													<span class="label label-success"><?php echo ucfirst($status); ?></span>
													<?php
														}
														?>
												</td>
												<td>
													<select name="payment_status" id="payment_status" class="form-control" style="width: 100;"onchange='changePaymentStatus(this, "<?php echo $id ?>");' >
													<option value="paid" <?php if($payment_status == "paid") { echo "selected"; } ?> >Paid</option>
													<option value="not_paid" <?php if($payment_status == "not_paid") { echo "selected"; } ?> >Not Paid</option>
													</select>
													
												</td>
												<td>
													<div class="text-center">
														<div class="btn-group">
															<?php
															if($status != "received") {?>
															<a href="purchases_add.php?id=<?php echo $id ?>&act=edit" title="Edit Category" class="tip btn btn-warning btn-xs"><i class="fa fa-edit"></i></a>
															<?php } ?>
															<a href="javascript:void(0)" title="View" class="tip btn btn-primary btn-xs" data-toggle="modal" data-target='#myModal<?php echo $id;?>'><i class="fa fa-file-text-o"></i></a>
															<a href="javascript:void(0)" onclick='changePurchaseStatus(this, "<?php echo $id ?>");' title="Change Status" class="tip btn btn-warning btn-xs"><i class="fa fa-exchange"></i><?php echo ucfirst($rev_status); ?></a>
															<!--<a href="#" title="Print Bill" class="tip btn btn-default btn-xs" data-toggle="ajax-modal"><i class="fa fa-print"></i></a>
																<a class="tip image btn btn-primary btn-xs" id="Milkshajr (123456789)" href="#" title="View Attachment"><i class="fa fa-picture-o"></i></a>
																<a href="#" title="Edit Purchase" class="tip btn btn-warning btn-xs"><i class="fa fa-edit"></i></a>
																<a href="#" onclick="return confirm('You are going to delete product, please click ok to delete.')" title="Delete Purchase" class="tip btn btn-danger btn-xs"><i class="fa fa-trash-o"></i></a>-->
														</div>
													</div>
												</td>
											</tr>
											<?php
												}
												}
												}
												else {
												echo "<tr>";
												echo "<td>No Purchase Orders found to list.</td>";
												echo "</tr>";
												}
												?>	               
										</tbody>
									</table>
								</div>
								<!-- /.box-body -->
							</div>
							<!-- /.box -->
						</div>
						<!-- /.col -->
					</div>
					<!-- /.row -->
				</section>
				<!-- /.content -->
			</div>
			<!-- /.content-wrapper -->
			<?php include("common/footer.php"); ?>
			<?php include("common/sidebar-right.php"); ?>
		</div>
		<!-- ./wrapper -->
		<!-- REQUIRED JS SCRIPTS -->
		<?php include("common/footer-scripts.php"); ?>
		<!-- Optionally, you can add Slimscroll and FastClick plugins.
			Both of these plugins are recommended to enhance the
			user experience. -->
		<script>
			function changePaymentStatus(status, id)
			{
				var msg = 'Are you sure you want to change the status for paid?';
				if(status == 'deactivate')
					msg = 'Are you sure you want to change the status for not paid?';
			    if(id && confirm(msg))
			    {
			        window.location.href = site_url+'/admin/purchases.php?id='+id+'&act='+status;
			    }
			}
			function changeStatus(val, id)
			{
				var status = $(val).val();
				var msg = 'Are you sure you want to change the status for '+status+'?';
			    if(id && confirm(msg))
			    {
			        window.location.href = site_url+'/admin/purchases.php?id='+id+'&act='+status+'&s=1';
			    }
			}
			function changePurchaseStatus(status, id)
			{
				var msg = 'Are you sure you want to De-activate this purchase?';
				if(status == 'deactivate')
					msg = 'Are you sure you want to activate this purchase?';
				if(id && confirm(msg)) {
					window.location.href = site_url+'/admin/purchases.php?id='+id+'&act='+status;
				}
			}
			$(document).on('click', '.print_me', function(e) {
				$(".hide_print").hide();
				var content = document.getElementById('delivery_purchase_order').innerHTML;
				var win = window.open();	
				win.document.write('<link href="css/style_v1.css" rel="stylesheet">');
				//win.document.write('<link href="core/framework/libs/pj/css/pj-table.css" rel="stylesheet" type="text/css" />');			
				win.document.write(content);	
				win.print();			
			});
			//$( "#from_date" ).datepicker({dateFormat: 'yy-mm-dd'});
			//$( "#to_date" ).datepicker({dateFormat: 'yy-mm-dd'});
		</script>
	</body>
</html>