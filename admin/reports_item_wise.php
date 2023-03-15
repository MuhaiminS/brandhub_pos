<?php 
	session_start();
	include("../functions.php");
	include_once("../config.php");
	chkAdminLoggedIn();
	connect_dre_db();
	if(isset($_GET['act']) && $_GET['act'] != '' && isset($_GET['id']) && $_GET['id'] > 0) {	
		$action = $_GET['act'];
		$id = $_GET['id'];
		if($action == 'delete') {
			$qry = "UPDATE items SET active = '0' WHERE id = $id";
			if(mysqli_query($GLOBALS['conn'], $qry)){
				redirect('products.php?resp=succ');
			}
		}
	}
	
	function getcategoryName($category_id)
	{
		$where = "WHERE id = '$category_id'";
		$category = getnamewhere('category', 'category_title', $where);
		return $category;
	}
	
	function getIncrediantName($incrediant_id)
	{
		$where = "WHERE id = '$incrediant_id'";
		$category = getnamewhere('incrediants', 'name', $where);
		return $category;
	}
	
	function getCategoryList()
	{
		$service = array();
		$query="SELECT * FROM item_category WHERE active != '0' ORDER BY category_title ASC";
		$run = mysqli_query($GLOBALS['conn'], $query);
		while($row = mysqli_fetch_array($run)) {
			$cat_id = $row['id'];
			$service[$cat_id]['cat_id'] = $row['id'];
			$service[$cat_id]['category_title'] = $row['category_title'];
		}
		return $service;	
	}
	
	
	$name = (isset($_GET['name']) && $_GET['name'] !='') ? $_GET['name'] : '';
	$category = (isset($_GET['category']) && $_GET['category'] !='') ? $_GET['category'] : '';

	function getSaleOrderItemDetailsListItemWise($from_date ='', $to_date='', $export=""){					

		$qry="SELECT soi.id, soi.item_id, soi.item_name as item_name, soi.weight, SUM((soi.price*soi.qty)/100 * so.vat) as tax_value, SUM(soi.qty) as sale_count, SUM(soi.price*soi.qty) as amount, soi.unit_price as unit_price FROM sale_order_items as soi LEFT JOIN sale_orders as so ON (so.id = soi.sale_order_id) WHERE 1"; 
	
		if($from_date != '' && $to_date != '') {
			$qry .= " AND so.ordered_date BETWEEN '$from_date 00:00:00' AND '$to_date 23:59:59' ";
		}
		$qry .="  GROUP BY soi.price, soi.item_name ORDER BY soi.item_name ASC ";
		// echo $qry; 
		$result=mysqli_query($GLOBALS['conn'], $qry);
		if ($result) {
			$result_arr = array();
			while ($row = mysqli_fetch_assoc($result)) {
				$result_arr[] = $row;			
			}
			return $result_arr;
			//return $result;
		}else{
			return false;
		}
	}		
	
	function getItemNames($receipe_id)
	{
		$where = "WHERE id = '$receipe_id'";
		$service = getnamewhere('items', 'name', $where);
		return $service;
	}

	function getIncunitNames($incunit_id)
	{
		$where = "WHERE id = '$incunit_id'";
		$service = getnamewhere('incrediant_units', 'unit_name', $where);
		return $service;
	}

	$items_img_dir = "../item_images/";

	$shops = (isset($_GET['shop']) && $_GET['shop'] !='') ? $_GET['shop'] : '';
	$from_date1 = (isset($_GET['from_date']) && $_GET['from_date'] !='') ? $_GET['from_date'] : '';
	$to_date1 = (isset($_GET['to_date']) && $_GET['to_date'] !='') ? $_GET['to_date'] : '';
	$data_sale_reports = getSaleOrderItemDetailsListItemWise($from_date1, $to_date1, $export="");
	//print_r($data_sale_reports);die;

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
						Item Wise Report
						<!--<small>Optional description</small>-->
					</h1>
					<ol class="breadcrumb">
						<li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
						<li class="active">Item Wise</li>
					</ol>
				</section>
				<!-- Main content -->
				<section class="content container-fluid">
					<div class="row">
						<div class="col-xs-12">
							<div class="box">
								<div class="box-header">
									<a href="#" class="btn btn-default btn-sm toggle_form pull-right">Show/Hide Form</a>
									<h3 class="box-title">Item Wise Report</h3>
								</div>
								<!-- /.box-header -->
								<div class="box-body">
									<div id="form" class="panel panel-warning" style="display: block;">
										<div class="panel-body">
											<form action="reports_item_wise.php" accept-charset="utf-8">
												<div class="row">
													<div class="col-sm-3">
														<div class="form-group">
															<label>Start date</label>
															<div class="input-group date">
																<div class="input-group-addon">
																	<i class="fa fa-calendar"></i>
																</div>
																<input type="text" name="from_date" class="form-control datepicker pull-right" id="from_date" value="<?php echo $from_date1; ?>">
															</div>
														</div>
													</div>
													<div class="col-sm-3">
														<div class="form-group">
															<label>End date</label>
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
														<a href="reports_item_wise.php" class="btn btn-default">Reset</a>
													</div>
												</div>
											</form>
										</div>
									</div>
									<div class="row" style="margin-bottom: 5px;display: none;" >
										<div class="col-md-6"></div>
										<div class="col-md-6 text-right pr0">
											<div class="dt-buttons btn-group">
												<a class="btn btn-default buttons-print buttons-html5 print_me" tabindex="0" aria-controls="SLData" href="#"><span>Print</span></a>
												<!--<a class="btn btn-default buttons-copy buttons-html5" tabindex="0" aria-controls="SLData" href="#"><span>Copy</span></a>
												<a class="btn btn-default buttons-excel buttons-html5" tabindex="0" aria-controls="SLData" href="#"><span>Excel</span></a>-->
												<a target="_blank"  href="export_settle_sale.php?sale=Counter Sale&order_type=counter_sale&get_type=excel&from_date=<?php echo $from_date1; ?>&to_date=<?php echo $to_date1; ?>" class="btn btn-default buttons-csv buttons-html5 export" tabindex="0" aria-controls="SLData" href="#"><span>CSV</span></a>
												<!--<a class="btn btn-default buttons-pdf buttons-html5" tabindex="0" aria-controls="SLData" href="#"><span>PDF</span></a>
												<a class="btn btn-default buttons-collection buttons-colvis" tabindex="0" aria-controls="SLData" href="#"><span>Columns</span></a>-->
											</div>
										</div>
									</div>
									<div id='item_wise_repots'>									
									<table id="example2" class="table table-bordered table-hover">
										<?php if(!empty($data_sale_reports)) { ?>				
											<thead>
												<tr>
													<th style="border: 1px solid black;">Item</th>
													<th style="border: 1px solid black;">Qty</th>		
													<th style="border: 1px solid black;">Sale Unit Price</th>
													<th style="border: 1px solid black;">Total Price</th>							
													<th style="border: 1px solid black;">VAT %</th>
													<th style="border: 1px solid black;">VAT Amount</th>
													<th style="border: 1px solid black;">Total Amount</th>
												</tr>
											</thead>
											<tbody>
											<?php $totals1 = $totals_with_out_vat = $vat_amount_total = $weight_tot = 0;
												foreach($data_sale_reports as $data_sale) {	 
													$item = $data_sale['item_name'];
													$item_value = explode('-', $item);							
													$item_val = $item_value[0].'-'.number_format($data_sale['weight'],2).'kg';
													$qty = $data_sale['sale_count'];
													//$weight_tot = $data_sale['weight_tot'];
													$price = $data_sale['amount'];
													$total = $data_sale['amount'];
													$unit_price= ($data_sale['unit_price'] != '') ? $data_sale['unit_price']: '0.00';
													$total_vat = $data_sale['tax_value'];
													$totals1 += ($total)+$total_vat;
													$vat_amount_total += $total_vat; 
													$totals_with_out_vat += ($total);
													?>
													<tr>
														<td><?php echo $item_val; ?></td>
														<td><?php echo $qty; ?></td>								
														<td style="text-align:right;"><?php echo number_format(($price), 2); ?></td>
														<td style="text-align:right;"><?php echo number_format(($price), 2); ?></td>
														<td style="text-align:right;"><?php echo '5'; ?></td>
														<td style="text-align:right;"><?php echo number_format($total_vat, 2); ?></td>
														<td style="text-align:right;"><?php echo number_format(($price)+$total_vat, 2); ?></td>								
													</tr>						
												<?php
											} ?>
											<tr>
											<td colspan="6" style="text-align:right;" >Total Before VAT</td><td style="text-align:right;"><?php echo number_format(($totals_with_out_vat ), 2, '.', ''); ?></td>
											</tr>
											<tr>
											<td colspan="6" style="text-align:right;" >5%VAT</td><td style="text-align:right;"><?php echo number_format(($vat_amount_total), 2, '.', ''); ?></td>
											</tr>
											<tr>
											<td colspan="6" style="text-align:right;" >Total Amount Due with VAT</td><td style="text-align:right;"><?php echo number_format(($totals1), 2, '.', ''); ?></td>
											</tr>	
										 <?php } else {
											echo "<tr>";
											echo "<td>No Sale found to list.</td>";
											echo "</tr>";
										} ?>
										</tbody>										
									</table>
								</div>
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
		<script type="text/javascript">
			var site_url = "<?php echo getServerURL(); ?>";
			function deleteIt(id)
			{
			    if(id && confirm('Are you sure you want to delete this Item?'))
			    {
			        window.location.href = site_url+'/admin/products.php?id='+id+'&act=delete';
			    }
			}
		</script>
		<script type="text/javascript">			
			
			$(document).on('click', '.print_me', function(e) {
				$(".show_titles").show();
				var content = document.getElementById('item_wise_repots').innerHTML;
				var win = window.open();	
				//win.document.write('<link href="css/style_v1.css" rel="stylesheet">');
				//win.document.write('<link href="core/framework/libs/pj/css/pj-table.css" rel="stylesheet" type="text/css" />');			
				win.document.write(content);	
				win.print();
				$(".show_titles").hide();
				win.window.close();
			});
			
			//export
			$(".show_titles").hide();
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
					$(".show_titles").hide();
			  }

			  // This must be a hyperlink  
				// $(".export").on('click', function (event) {
			   $(document).on('click', '.excel_me', function(event) {
					$(".show_titles").show();
					exportTableToCSV.apply(this, [$('#item_wise_repots>table'), 'export_settle_reports.csv']);        
			  });

			</script>
	</body>
</html>