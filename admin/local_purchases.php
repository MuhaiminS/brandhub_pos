<?php
	session_start();
	include("../functions.php");
	include_once("../config.php");
	chkAdminLoggedIn();
	connect_dre_db();
	
	if(isset($_GET['act']) && $_GET['act'] != '' && isset($_GET['id']) && $_GET['id'] > 0) {
		$action = $_GET['act'];
		$purchase_order_id = $_GET['id'];
		if($action == 'cash') {
			$qry = "UPDATE purchase_order SET payment_options = 'cash' WHERE id = '$purchase_order_id'";
			if(mysqli_query($GLOBALS['conn'], $qry)){
				//UserLogDetails($_SESSION['user_id'], $purchase_order_id, 'purchase_orders', 'paid');
				redirect('manage_purchase_orders.php?resp=succ');
			}
		}
		else if($action == 'card') {
			$qry = "UPDATE purchase_order SET payment_options = 'card' WHERE id = $purchase_order_id";
			if(mysqli_query($GLOBALS['conn'], $qry)){
				//UserLogDetails($_SESSION['user_id'], $purchase_order_id, 'purchase_orders', 'not_paid');
				redirect('manage_purchase_orders.php?resp=succ');
			}
		}
		else if($action == 'delete') {
			$qry = "DELETE FROM purchase_order WHERE id = '$purchase_order_id'";
			if(mysqli_query($GLOBALS['conn'], $qry)){
				//UserLogDetails($_SESSION['user_id'], $purchase_order_id, 'purchase_orders', 'paid');
				redirect('manage_purchase_orders.php?resp=succ');
			}
		}
	}

	function getSuppliersList()
	{
		$suppliers = array();
		$query = "SELECT * FROM suppliers ORDER BY id ASC";
		$run = mysqli_query($GLOBALS['conn'], $query);
		while($row = mysqli_fetch_array($run)) {
		$suppliers_id = $row['id'];
		$suppliers[$suppliers_id] = $row['supplier_name'];
		}
		return $suppliers;
	}
	
	function getSuppliersName($suppliers_id)
	{
		$where = "WHERE id = '$suppliers_id'";
		$service = getnamewhere('suppliers', 'supplier_name', $where);
		return $service;
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
	$setLimit = 100;
	$pageLimit = ($page * $setLimit) - $setLimit;
	
	function getPurchaseorders($from_date ='', $to_date='',$pageLimit, $setLimit)
	{
		$date = date('Y-m-d');
		$from_date = ($from_date !='') ? date('Y-m-d',strtotime($from_date)):'';
		$to_date = ($to_date !='') ? date('Y-m-d',strtotime($to_date)):'';
		
		$qry="SELECT *, po.id AS po_purchase_id FROM local_purchase_front AS po WHERE 1";
		
		if($from_date != '' && $to_date != '' ) {
	
			$qry .= " AND po.purchase_date BETWEEN '$from_date 00:00:00' AND '$to_date 23:59:59' ";
		}
		if($from_date != '' && $to_date == '' ) {
			$qry .= " AND po.purchase_date >= '$from_date 23:59:59'";
		}
	
		if($from_date == '' && $to_date != '' ) {
			$qry .= " AND po.purchase_date <= '$to_date 23:59:59'";
		}		
				
		$qry .=" ORDER BY po.id DESC";
		
		$qry .= " LIMIT $pageLimit, $setLimit";		
		
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
	$payment_options ='';
	function displayPaginationBelows($per_page, $page, $from_date ='', $to_date='') {
	    $page_url="?";
		$date = date('Y-m-d');
		$from_date = ($from_date !='') ? date('Y-m-d',strtotime($from_date)):'';
		$to_date = ($to_date !='') ? date('Y-m-d',strtotime($to_date)):'';		
		
		$query="SELECT COUNT(*) as totalCount FROM local_purchase_front AS po WHERE 1";
				
		if($from_date != '' && $to_date != '' ) {
	
			$query .= " AND po.purchase_date BETWEEN '$from_date 00:00:00' AND '$to_date 23:59:59' ";
		}
		if($from_date != '' && $to_date == '' ) {
			$query .= " AND po.purchase_date >= '$from_date 23:59:59'";
		}
		if($from_date == '' && $to_date != '' ) {
			$query .= " AND po.purchase_date <= '$to_date 23:59:59'";
		}	
		
		//echo $query;
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
						$setPaginate.= "<li><a href='{$page_url}page=$counter&from_date=$from_date&to_date=$to_date'>$counter</a></li>";
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
							$setPaginate.= "<li><a href='{$page_url}page=$counter&from_date=$from_date&to_date=$to_date'>$counter</a></li>";
					}
					$setPaginate.= "<li class='dot'>...</li>";
					$setPaginate.= "<li><a href='{$page_url}page=$lpm1&from_date=$from_date&to_date=$to_date'>$lpm1</a></li>";
					$setPaginate.= "<li><a href='{$page_url}page=$setLastpage&from_date=$from_date&to_date=$to_date'>$setLastpage</a></li>";
				}
				elseif($setLastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2))
				{
					$setPaginate.= "<li><a href='{$page_url}page=1&from_date=$from_date&to_date=$to_date'>1</a></li>";
					$setPaginate.= "<li><a href='{$page_url}page=2&from_date=$from_date&to_date=$to_date'>2</a></li>";
					$setPaginate.= "<li class='dot'>...</li>";
					for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++)
					{
						if ($counter == $page)
							$setPaginate.= "<li><a class='current_page'>$counter</a></li>";
						else
							$setPaginate.= "<li><a href='{$page_url}page=$counter&from_date=$from_date&to_date=$to_date'>$counter</a></li>";
					}
					$setPaginate.= "<li class='dot'>..</li>";
					$setPaginate.= "<li><a href='{$page_url}page=$lpm1&from_date=$from_date&to_date=$to_date'>$lpm1</a></li>";
					$setPaginate.= "<li><a href='{$page_url}page=$setLastpage&from_date=$from_date&to_date=$to_date'>$setLastpage</a></li>";
				}
				else
				{
					$setPaginate.= "<li><a href='{$page_url}page=1&from_date=$from_date&to_date=$to_date'>1</a></li>";
					$setPaginate.= "<li><a href='{$page_url}page=2&from_date=$from_date&to_date=$to_date'>2</a></li>";
					$setPaginate.= "<li class='dot'>..</li>";
					for ($counter = $setLastpage - (2 + ($adjacents * 2)); $counter <= $setLastpage; $counter++)
					{
						if ($counter == $page)
							$setPaginate.= "<li><a class='current_page'>$counter</a></li>";
						else
							$setPaginate.= "<li><a href='{$page_url}page=$counter&from_date=$from_date&to_date=$to_date'>$counter</a></li>";
					}
				}
			}
			if ($page < $counter - 1){
				$setPaginate.= "<li><a href='{$page_url}page=$next&from_date=$from_date&to_date=$to_date'>Next</a></li>";
				$setPaginate.= "<li><a href='{$page_url}page=$setLastpage&from_date=$from_date&to_date=$to_date'>Last</a></li>";
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
	
	$invoice_nos = (isset($_GET['invoice_no']) && $_GET['invoice_no'] !='') ? $_GET['invoice_no'] : '';
	$payment_options1 = (isset($_GET['payment_options']) && $_GET['payment_options'] !='') ? $_GET['payment_options'] : '';
	$from_date1 = (isset($_GET['from_date']) && $_GET['from_date'] !='') ? $_GET['from_date'] : '';
	$to_date1 = (isset($_GET['to_date']) && $_GET['to_date'] !='') ? $_GET['to_date'] : '';	
	
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
						Local Purchases
						<!--<small>Optional description</small>-->
					</h1>
					<ol class="breadcrumb">
						<li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
						<li class="active">Local Purchases</li>
					</ol>
				</section>
				<!-- Main content -->
				<section class="content container-fluid">
					<div class="row">
						<div class="col-xs-12">
							<div class="box">
								<div class="box-header">
									<h3 class="box-title">Manage Local Purchases</h3>
								</div>
								<!-- /.box-header -->
								<div class="box-body">
									<?php include("common/info.php"); ?>
									<div id="form" class="panel panel-warning" style="display: block;">
										<div class="panel-body">
											<form action="local_purchases.php" accept-charset="utf-8">
												<div class="row">
													<div class="col-sm-2">
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
													<div class="col-sm-2">
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
														<a href="local_purchases.php" class="btn btn-default">Reset</a>
													</div>
												</div>
											</form>
										</div>
									</div>
									<div class="row">
										<div class="col-md-6"></div>
										<div class="col-md-6 text-right pr0">
											<div class="dt-buttons btn-group">
												<a class="btn btn-default buttons-print buttons-html5 print_me" tabindex="0" aria-controls="SLData" href="#"><span>Print</span></a>
												<a class="btn btn-default buttons-print buttons-html5 export_me" tabindex="0" aria-controls="SLData" href="#"><span>Export</span></a>
											</div>
										</div>
									</div>									
									<table id="example2" class="table table-bordered table-hover">
										<thead>
											<tr>
												<th>#</th>
												<th>TRN No's</th>
												<th>Company Name</th>
												<th>Purchase Date</th>
												<th>Total</th>
												<th>Vat%</th>
												<th>Vat Amount</th>
												<th>Net Total</th>
											</tr>
										</thead>
										<tbody>
											<?php $vat_total1 = $net_total1 = $sub_total1 = "0.00";
												$prs = getPurchaseorders($from_date1,$to_date1,$pageLimit, $setLimit);
												if($prs != false) {
													$pcount = mysqli_num_rows($prs);
													if($pcount > 0) {
														for($p = 0; $p < $pcount; $p++) {
															$prow = mysqli_fetch_object($prs);
															$id = $prow->po_purchase_id;
															//$invoice_no = $prow->invoice_no;
															$trn_no = $prow->trn_no;
															$company_name = $prow->company_name;
															$supplier_vat = $prow->supplier_vat;
															//$payment_options = $prow->payment_options;
															$purchase_date = $prow->purchase_date;
															$sub_total = $prow->sub_total;
															$vat_amount = $prow->vat_amount;
															$net_total = $prow->net_total;
															//$sub_total = $prow->sub_total;
															//$vat_total = $prow->vat_amount;
															
												
															/* $total = $net_total = $with_vat_price = $vat_price = "0.00";
															$tot_item = $tot_qty=0;
															$tot_discount=0.00;
															$tot_amount=0.00;
															$tot_vat=0.00;
															$tot_net_amount=0.00;
														
															$prs2 = getPurchaseOrderitems($id); */
														//if($prs2 != false) {
															
															/* $pcount2 = mysqli_num_rows($prs2);
															if($pcount2 > 0) {
																for($p2 = 0; $p2 < $pcount2; $p2++) {
																	$prow2 = mysqli_fetch_object($prs2);
																	
																	$price = $prow2->price;																	
																	$qty = $prow2->qty;
																	$bonus = $prow2->bonus;
																	$total_qty = $prow2->total_qty;
																	//$vat = $prow2->vat;
																	$total_amount = $prow2->total_amount;
																	$discount = $prow2->discount;
																	$sub_total = $prow2->sub_total;
																	$vat_total = $prow2->vat_total;
																	$net_total = $prow2->net_total;
																	
																	$tot_item = ($p2 + 1);
																	$tot_qty += $total_qty;
																	$tot_discount += $discount;
																	$tot_amount += $sub_total;
																	$tot_vat += $vat_total;
																	$tot_net_amount += $net_total;
																	}
																}
															} */
														$net_total1 += $net_total;
														$s_no = $p+1;?>
											<tr>
												<td><?php echo $id; ?></td>
												<td><?php echo $trn_no; ?></td>
												<td><?php echo $company_name; ?></td>
												<td><?php echo date("d-m-Y",strtotime($purchase_date)); ?></td>
												<td><?php echo $sub_total; ?></td>
												<td><?php echo $supplier_vat; ?></td>
												<td><?php echo $vat_amount; ?></td>
												<td><?php echo $net_total; ?></td>
												
											</td>
																							</tr>
											<?php
												} ?>
											<tr>
												<td colspan='7'>Total Amount Due With VAT</td>
												<td colspan='1' align="center"><?php echo number_format($net_total1, 2);?></td>
												
											</tr>
											<?php	}
												}
												else { ?>
											<tr>
												<td>No Purchase Orders found to list.</td>
											</tr>
											<?php }
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

		<div id="local_purchase_order" style="display:none;">
		<table id="example2" class="table table-bordered table-hover">
			<tr><th>Local Purchase Orders</th></tr>
			<tr><th>From Date: <?php echo $from_date1; ?></th></tr>
			<tr><th>To Date: <?php echo $to_date1; ?></th></tr>
		</table>
			<table id="example2" class="table table-bordered table-hover" border="1">
				<thead>
					<tr>
						<th>#</th>
						<th>TRN No's</th>
						<th>Company Name</th>
						<th>Purchase Date</th>
						<th>Total</th>
						<th>Vat%</th>
						<th>Vat Amount</th>
						<th>Net Total</th>
					</tr>
				</thead>
				<tbody>
				<?php $vat_total1 = $net_total1 = $sub_total1 = "0.00";
					$prs = getPurchaseorders($from_date1,$to_date1,$pageLimit, $setLimit);
					if($prs != false) {
						$pcount = mysqli_num_rows($prs);
						if($pcount > 0) {
							for($p = 0; $p < $pcount; $p++) {
								$prow = mysqli_fetch_object($prs);
								$id = $prow->po_purchase_id;
								//$invoice_no = $prow->invoice_no;
								$trn_no = $prow->trn_no;
								$company_name = $prow->company_name;
								$supplier_vat = $prow->supplier_vat;
								//$payment_options = $prow->payment_options;
								$purchase_date = $prow->purchase_date;
								$sub_total = $prow->sub_total;
								$vat_amount = $prow->vat_amount;
								$net_total = $prow->net_total;
								
							$net_total1 += $net_total;
							$s_no = $p+1;?>
					<tr>
						<td><?php echo $id; ?></td>
						<td><?php echo $trn_no; ?></td>
						<td><?php echo $company_name; ?></td>
						<td><?php echo date("d-m-Y",strtotime($purchase_date)); ?></td>
						<td><?php echo $sub_total; ?></td>
						<td><?php echo $supplier_vat; ?></td>
						<td><?php echo $vat_amount; ?></td>
						<td><?php echo $net_total; ?></td>
						
					</td>
																	</tr>
					<?php
						} ?>
					<tr>
						<td colspan='7'>Total Amount Due With VAT</td>
						<td colspan='1' align="center"><?php echo number_format($net_total1, 2);?></td>
						
					</tr>
					<?php	}
						}
						else { ?>
					<tr>
						<td>No Local Purchase Orders found to list.</td>
					</tr>
					<?php }
						?>	               
				</tbody>
			</table>
		</div>
		<script type="text/javascript">
			
			$(document).on('click', '.print_me', function(e) {
				$(".hide_print").hide();
				var content = document.getElementById('local_purchase_order').innerHTML;
				var win = window.open();
				win.document.write('<link href="css/style_v1.css" rel="stylesheet">');
				//win.document.write('<link href="core/framework/libs/pj/css/pj-table.css" rel="stylesheet" type="text/css" />');
				win.document.write(content);
				win.print();
				win.window.close();
			});
			$( "#from_date" ).datepicker();
			$( "#to_date" ).datepicker();
			
			function exportTableToCSV($table, filename) {
			var $rows = $table.find('tr:has(th),tr:has(td)'),
				// Temporary delimiter characters unlikely to be typed by keyboard
				// This is to avoid accidentally splitting the actual contents
				tmpColDelim = String.fromCharCode(11), // vertical tab character
				tmpRowDelim = String.fromCharCode(0), // null character
				// actual delimiter characters for CSV format
				colDelim = '","',
				rowDelim = '"\r\n"',

				// Grab text from table into CSV formatted string
				csv = '"' + $rows.map(function (i, row) {
					var $row = $(row),
						$cols = $row.find('th,td');

					return $cols.map(function (j, col) {
						var $col = $(col),
							text = $col.text();

						return text.replace(/"/g, '""'); // escape double quotes

					}).get().join(tmpColDelim);

				}).get().join(tmpRowDelim)
					.split(tmpRowDelim).join(rowDelim)
					.split(tmpColDelim).join(colDelim) + '"',

				// Data URI
				csvData = 'data:application/csv;charset=utf-8,' + encodeURIComponent(csv);

			$(this)
				.attr({
				'download': filename,
					'href': csvData,
					'target': '_blank'
			});
		}

		// This must be a hyperlink		
	    $(".export_me").on('click', function (event) {			
			exportTableToCSV.apply(this, [$('#local_purchase_order>table'), 'local_purchase_order.csv']);        
		});
	
			
		</script>
	</body>
</html>