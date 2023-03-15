<?php 
	session_start();
	include("../functions.php");
	include_once("../config.php");
	chkAdminLoggedIn();
	connect_dre_db();
	
	
	function getItemNames($item_id)
	{
		$where = "WHERE id = '$item_id'";
		$service = getnamewhere('items', 'name', $where);
		return $service;
	}

	function getSupplierNames($supplier_id)
	{
		$where = "WHERE id = '$supplier_id'";
		$service = getnamewhere('suppliers', 'name', $where);
		return $service;
	}


	function getItemsList($order)
	{
		$items = array();
		$query = "SELECT * FROM items ORDER BY id ASC";		
		$run = mysqli_query($GLOBALS['conn'], $query);
		while ($row = mysqli_fetch_array($run)) {
			$items[] = $row;
		}
		return $items;
	}
	
	
	function getSaleorders($from_date ='', $to_date='', $product_id='')
	{
		$date = date('Y-m-d');
		$qry="SELECT poi.*, po.reference_id, po.supplier_id, po.purchase_date FROM purchase_order_items as poi join purchase_orders as po on po.id = poi.purchase_id WHERE 1";
				
		if($product_id != ''){
			$qry .=" AND poi.product_id = '$product_id'";
		}
		if($from_date != '' && $to_date != '' ) {
			
			$qry .= " AND po.purchase_date BETWEEN '$from_date' AND '$to_date' ";		
		} 
		if($from_date != '' && $to_date == '' ) {
			$qry .= " AND po.purchase_date >= '$from_date'";
		}
	
		if($from_date == '' && $to_date != '' ) {
			$qry .= " AND po.purchase_date <= '$to_date'";
		}
		$qry .=" ORDER BY po.id DESC";
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
	
	$product_arr = getItemsList("ASC");

	//print_r($product_arr);exit;
	
	$status_arr =  array(
		'pending' => 'Pending',
		'progressing' => 'Progressing',
		'ready_for_delivery' => 'Ready for delivery',
		'completed' => 'Completed',
		'delivered' => 'Delivered',
		'cancel' => 'canceled',
		'draft' => 'Draft'
	);

	$product_id = (isset($_GET['product_id']) && $_GET['product_id'] !='') ? $_GET['product_id'] : '';	
	$from_date1 = (isset($_GET['from_date']) && $_GET['from_date'] !='') ? $_GET['from_date'] : '';
	$to_date1 = (isset($_GET['to_date']) && $_GET['to_date'] !='') ? $_GET['to_date'] : '';

	$data_purchase_order = getSaleorders($from_date1, $to_date1, $product_id);
	
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
						Item Search Purchases
						<!--<small>Optional description</small>-->
					</h1>
					<ol class="breadcrumb">
						<li><a href="index.php"><i class="fa fa-dashboard"></i>Home</a></li>
						<li class="active">Item Search Purchases</li>
					</ol>
				</section>
				<!-- Main content -->
				<section class="content container-fluid">
					<div class="row">
						<div class="col-xs-12">
							<div class="box">
								<div class="box-header">
									<h3 class="box-title">Manage Item Search Purchases</h3>
								</div>
								<!-- /.box-header -->
								<div class="box-body">
									<?php include("common/info.php"); ?>
									<div id="form" class="panel panel-warning" style="display: block;">
										<div class="panel-body">
											<form action="search_purchases.php" accept-charset="utf-8">
												<div class="row">
													<div class="col-sm-4">
														<div class="form-group">
															<label class="title">Product Name:</label>
															<select name="product_id" class="form-control product_names" id="product_name">
																<option value="">----</option>
																<?php
																foreach ($product_arr as $product)
																{
																	?>						
																	<option value="<?php echo $product['id']; ?>" <?php echo $product['id'] == $product_id ? ' selected="selected"' : NULL; ?>><?php echo stripslashes($product['name']); ?></option>
																	<?php
																}
																?>
															</select>
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
														<a href="search_purchases.php" class="btn btn-default">Reset</a>
													</div>
												</div>
											</form>
										</div>
									</div>									
									<div class="row">
										<div class="col-md-6"></div>
										<div class="col-md-6 text-right pr0">
											<div class="dt-buttons btn-group">
												<!-- <a target="_blank" href="excel_export_purchase.php?reference_id=<?php echo $reference_ids; ?>&payment_status=<?php echo $payment_status; ?>&from_date=<?php echo $from_date1; ?>&to_date=<?php echo $to_date1; ?>" class="btn btn-default buttons-csv buttons-html5 export"><span>Excel</span></a> -->
											</div>
										</div>
									</div>
									<?php if(isset($_GET['product_id'])) { ?>
									<table id="example2" class="table table-bordered table-hover">									
									<?php
									if(!empty($data_purchase_order)) { ?>				
										<thead>
											<tr>
												<th>#</th>
												<th>Supplier</th>
												<th>Reference Id</th>
												<th>Purchase Date</th>
												<th>Product Name</th>
												<th>Stock</th>
												<th>Qty</th>
												<th>Price</th>
												<th>Total Amount</th>
											</tr>
										</thead>
										<?php $grand_total = $tot_stock = $tot_qty = $tot_price = '0.00';
										foreach($data_purchase_order as $data_purchase) { 
											$supplier = getSupplierNames($data_purchase['supplier_id']);
											$name = getItemNames($data_purchase['product_id']);
											?>
											<tbody>
												<tr>
													<td><?php echo $data_purchase['id']; ?></td>
													<td><?php echo $supplier; ?></td>
													<td><?php echo $data_purchase['reference_id']; ?></td>
													<td><?php echo $data_purchase['purchase_date']; ?></td>
													<td><?php echo $name; ?></td>
													<td><?php echo $data_purchase['stock']; ?></td>
													<td><?php echo $data_purchase['qty']; ?></td>
													<td><?php echo ($data_purchase['unit_price'] != '') ? number_format($data_purchase['unit_price'], 2) :'0.00'; ?></td>
													<td><?php echo ($data_purchase['total_amount'] != '') ? number_format($data_purchase['total_amount'], 2) :'0.00'; ?></td>
												</tr>
											</tbody>
										<?php
										$tot_stock += ($data_purchase['stock'] != '') ? $data_purchase['stock'] :'0';
										$tot_qty += ($data_purchase['qty'] != '') ? $data_purchase['qty'] :'0';
										$tot_price += ($data_purchase['unit_price'] != '') ?$data_purchase['unit_price'] :'0.00';
										$grand_total += ($data_purchase['total_amount'] != '') ? $data_purchase['total_amount'] :'0.00';
										}
										echo "<tr>";
										echo "<td colspan='5'>Grand Total</td>";
										echo "<td>".$tot_stock."</td>";
										echo "<td>".$tot_qty."</td>";
										echo "<td>".number_format($tot_price, 2)."</td>";
										echo "<td>".number_format($grand_total, 2)."</td>";
										echo "</tr>";									
									 } else {
										echo "<tr>";
										echo "<td>No Items found to list.</td>";
										echo "</tr>";
										} ?>
									 </table>
								<?php } ?>
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
				if(id && confirm(msg))	{
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