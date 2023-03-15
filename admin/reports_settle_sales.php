<?php 
session_start();
include("../functions.php");
include_once("../config.php");
chkAdminLoggedIn();
connect_dre_db();
$server_url = getServerURL();

function getUserName($user_id)
{
	$where = "WHERE id = '$user_id'";
	$service = getnamewhere('users', 'user_name', $where);
	return $service;
}

function getShopsList()
{
	$service = array();
	$query="SELECT * FROM locations_shops ORDER BY id ASC";
	$run = mysql_query($query);
	while($row = mysql_fetch_array($run)) {
		$shop_id = $row['id'];
		$service[$shop_id]['shop_id'] = $row['id'];
		$service[$shop_id]['shop_name'] = $row['shop_name'];
	}
	return $service;	

}

function getShopName($shop_id)
{
	$where = "WHERE id = '$shop_id'";
	$service = getnamewhere('locations_shops', 'shop_name', $where);
	return $service;
}

function getLastSettle($user_id, $settle_id) {

	$settle_date = '0';
	$query = "SELECT `settle_date` FROM `settle_sale` WHERE user_id = '$user_id' AND id < '$settle_id' order by settle_date DESC LIMIT 1";	
	$run = mysqli_query($GLOBALS['conn'], $query);
	while($row = mysqli_fetch_assoc($run)) {
		$settle_date = $row['settle_date'];		
	}
	//echo $settle_date; die;
	return $settle_date;
}


if(isset($_GET["page"])) {
	$page = (int)$_GET["page"];
} else {
	$page = 1;
}
$setLimit = 50;
$pageLimit = ($page * $setLimit) - $setLimit;

function getSettleSales($from_date ='', $to_date='', $shop='', $pageLimit='', $setLimit='', $export="")
{
	$date = date('Y-m-d');
	$qry="SELECT *, DATE_FORMAT(settle_date,'%Y-%m-%d') as settle_dat FROM settle_sale WHERE id != ''"; 
	
	if($shop != ''){
		$qry .=" AND shop_id = '$shop'";
	}
	
	if($from_date != '' && $to_date != '' ) {
		
		$qry .= " AND settle_date BETWEEN '$from_date 00:00:00' AND '$to_date 23:59:59' ";		
	} 
	if($from_date != '' && $to_date == '' ) {
		$qry .= " AND settle_date BETWEEN '$from_date 00:00:00' AND '$date 23:59:59' ";
	}

	if($from_date == '' && $to_date != '' ) {
		$qry .= " AND settle_date <= '$to_date 23:59:59'";
	}
	if($export == '') {
		$qry .=" ORDER BY settle_date DESC LIMIT $pageLimit, $setLimit";
	} else {
		$qry .=" ORDER BY settle_date DESC ";
	}
	//echo $qry;
	$result=mysqli_query($GLOBALS['conn'], $qry);
	//$num=mysql_num_rows($result);	//echo "total result ".$num;
	if($result)
	{
		return $result;
	}
	else
	return false;
}

function displayPaginationBelows($per_page,$page,$from_date ='', $to_date='', $shop='', $export="") {
    $page_url="?";
	$date = date('Y-m-d');
	$query = "SELECT COUNT(*) as totalCount FROM settle_sale WHERE id != ''";	
	if($shop != ''){
		$query .=" AND shop_id = '$shop'";
	}	
	if($from_date != '' && $to_date != '' ) {
		
		$query .= " AND settle_date BETWEEN '$from_date 00:00:00' AND '$to_date 23:59:59' ";		
	} 
	if($from_date != '' && $to_date == '' ) {
		$query .= " AND settle_date BETWEEN '$from_date 00:00:00' AND '$date 23:59:59' ";
	}
	if($from_date == '' && $to_date != '' ) {
		$query .= " AND settle_date <= '$to_date 23:59:59'";
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
					$setPaginate.= "<li><a href='{$page_url}page=$counter&shop=$shop&from_date=$from_date&to_date=$to_date'>$counter</a></li>";
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
						$setPaginate.= "<li><a href='{$page_url}page=$counter&shop=$shop&from_date=$from_date&to_date=$to_date'>$counter</a></li>";
				}
				$setPaginate.= "<li class='dot'>...</li>";
				$setPaginate.= "<li><a href='{$page_url}page=$lpm1&shop=$shop&from_date=$from_date&to_date=$to_date'>$lpm1</a></li>";
				$setPaginate.= "<li><a href='{$page_url}page=$setLastpage&shop=$shop&from_date=$from_date&to_date=$to_date'>$setLastpage</a></li>"; 
			}
			elseif($setLastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2))
			{
				$setPaginate.= "<li><a href='{$page_url}page=1&shop=$shop&from_date=$from_date&to_date=$to_date'>1</a></li>";
				$setPaginate.= "<li><a href='{$page_url}page=2&shop=$shop&from_date=$from_date&to_date=$to_date'>2</a></li>";
				$setPaginate.= "<li class='dot'>...</li>";
				for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++)
				{
					if ($counter == $page)
						$setPaginate.= "<li><a class='current_page'>$counter</a></li>";
					else
						$setPaginate.= "<li><a href='{$page_url}page=$counter&shop=$shop&from_date=$from_date&to_date=$to_date'>$counter</a></li>";
				}
				$setPaginate.= "<li class='dot'>..</li>";
				$setPaginate.= "<li><a href='{$page_url}page=$lpm1&shop=$shop&from_date=$from_date&to_date=$to_date'>$lpm1</a></li>";
				$setPaginate.= "<li><a href='{$page_url}page=$setLastpage&shop=$shop&from_date=$from_date&to_date=$to_date'>$setLastpage</a></li>"; 
			}
			else
			{
				$setPaginate.= "<li><a href='{$page_url}page=1&shop=$shop&from_date=$from_date&to_date=$to_date'>1</a></li>";
				$setPaginate.= "<li><a href='{$page_url}page=2&shop=$shop&from_date=$from_date&to_date=$to_date'>2</a></li>";
				$setPaginate.= "<li class='dot'>..</li>";
				for ($counter = $setLastpage - (2 + ($adjacents * 2)); $counter <= $setLastpage; $counter++)
				{
					if ($counter == $page)
						$setPaginate.= "<li><a class='current_page'>$counter</a></li>";
					else
						$setPaginate.= "<li><a href='{$page_url}page=$counter&shop=$shop&from_date=$from_date&to_date=$to_date'>$counter</a></li>";
				}
			}
		}
		if ($page < $counter - 1){
			$setPaginate.= "<li><a href='{$page_url}page=$next&shop=$shop&from_date=$from_date&to_date=$to_date'>Next</a></li>";
			$setPaginate.= "<li><a href='{$page_url}page=$setLastpage&shop=$shop&from_date=$from_date&to_date=$to_date'>Last</a></li>";
		}else{
			$setPaginate.= "<li><a class='current_page'>Next</a></li>";
			$setPaginate.= "<li><a class='current_page'>Last</a></li>";
		}
		$setPaginate.= "</ul>\n"; 
	}
	return $setPaginate;
}

$shops = (isset($_GET['shop']) && $_GET['shop'] !='') ? $_GET['shop'] : '';
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
						Report - Settle Sale
						<!--<small>Optional description</small>-->
					</h1>
					<ol class="breadcrumb">
						<li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
						<li class="active">Settle Sale</li>
					</ol>
				</section>
				<!-- Main content -->
				<section class="content container-fluid">
					<div class="row">
						<div class="col-xs-12">
							<div class="box">
								<div class="box-header">
									<h3 class="box-title">Settle Sale</h3>
								</div>
								<!-- /.box-header -->
								<div class="box-body">
									<div id="form" class="panel panel-warning" style="display: block;">
										<div class="panel-body">
											<form action="reports_settle_sales.php" accept-charset="utf-8">
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
														<a href="reports_settle_sales.php" class="btn btn-default">Reset</a>
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
												<?php if($from_date1 != '' || $to_date1 != '') { ?>
												  <span title="Excel" class="print2" style="font-size: 20px;"><a target="_blank" href="excel_export_settle_sale.php?sale=Counter Sale&order_type=counter_sale&get_type=excel&from_date=<?php echo $from_date1; ?>&to_date=<?php echo $to_date1; ?>" class="btn btn-default buttons-print buttons-html5 excel_me"><span>Excel</span></a></span>
											    <?php } else { ?>
			    								<span title="Excel" class="print2" style="font-size: 20px;"><a target="_blank" href="excel_export_settle_sale.php?sale=Counter Sale&order_type=counter_sale&get_type=excel" class="btn btn-default buttons-print buttons-html5 excel_me"><span>Excel</span></a></span>
			    								<?php } ?>
											</div>
										</div>
									</div>
									<div id='settle_sale_repots'>
									<table class="show_titles" style="display:none;">
										   <tr><td>Settle Sale Reports</td></tr>										  
										   <tr><td>From Date: <?php echo $from_date1; ?>  To Date: <?php echo $to_date1; ?></td></tr>
									</table>
									<table class="table table-striped table-bordered table-hover table-heading no-border-bottom">
											<thead>
												<tr>
													<th>#</th>
													<th>Settle Date</th>
													<th>User Name</th>													
													<th>Shop</th>
													<th>Cash Sale</th>
													<th>Card Sale</th>
													<th>Delivery Sale</th>
													<th>Sale Total</th>
													<th>Discount</th>
												<?php if(BILL_TAX == 'yes'){ if(BILL_TAX_TYPE == 'VAT'){ ?>
													<th>VAT</th>
												<?php } elseif(BILL_TAX_TYPE == 'GST'){ ?>
													<th>GST</th>
												<?php } } ?>
												<th>Net Total</th>
													<th>Action</th>
												</tr>
											</thead>
											<tbody>
												<?php	$grand_total = $total_vat1= "0.00";												
													$prs = getSettleSales($from_date1, $to_date1,$shops, $pageLimit, $setLimit, $export="");	
													if($prs != false) {
														$pcount = mysqli_num_rows($prs);
														if($pcount > 0) {
															for($p = 0; $p < $pcount; $p++) {
																$prow = mysqli_fetch_object($prs);
																$id = $prow->id;
																$user_id = $prow->user_id;
																$settle_dat = $prow->settle_date;
																$cash_sale = ($prow->cash_sale !='') ? $prow->cash_sale :  '0.00' ;
																$card_sale = ($prow->card_sale !='') ? $prow->card_sale :  '0.00' ;
																$total_cgst = ($prow->total_cgst !='') ? $prow->total_cgst :  '0.00' ;
																$total_sgst = ($prow->total_sgst !='') ? $prow->total_sgst :  '0.00' ;
																$total_gst = ($prow->total_gst !='') ? $prow->total_gst :  '0.00' ;
																$delivery_sale = ($prow->delivery_sale !='') ? $prow->delivery_sale : '0.00';
																$discount = ($prow->discount !='') ? $prow->discount :  '0.00' ;
																$user_name = getUserName($prow->user_id);
																//$name = getManufacturingUnitName($prow->manufacturing_unit_id);
																$shop_name = getShopName($prow->shop_id);
																$total_without_discount = ($prow->gross_total !='') ? $prow->gross_total :  '0.00' ;
																$total_with_discount = $prow->net_total;
																$total = "0.00";
																$total_vat = $prow->total_vat;
																$last_settle_date =  getLastSettle($user_id, $id);
																echo "<tr>";
																echo "<td>".$id."</td>";
																echo "<td>".$settle_dat."</td>";
																echo "<td>".$user_name."</td>";	
																echo "<td>".$shop_name."</td>";
																echo "<td>".$cash_sale."</td>";
																echo "<td>".$card_sale."</td>";	
																echo "<td>".$delivery_sale."</td>";																
																echo "<td>".number_format($total_without_discount, 2)."</td>";
																echo "<td>".$discount."</td>";
																if(BILL_TAX == 'yes'){ if(BILL_TAX_TYPE == 'VAT'){ 
																echo "<td>".$total_vat."</td>";
																} elseif(BILL_TAX_TYPE == 'GST'){ 
																echo "<td>".number_format($total_gst, 2)."</td>";
															}}
																echo "<td>".number_format(($total_without_discount-$discount), 2)."</td>";	
																//echo "</tr>";
																if($id > 1) {
																echo "<td><a href='userwise_sale.php?from_date=".$last_settle_date."&to_date=".$settle_dat."'>User wise sale</a></td>";
																}
																echo "</tr>";
																$grand_total += $total_without_discount-$discount;
																$total_vat1 += $total_vat;
															}
															echo "<tr>";
															echo "<td colspan='9'>Grand Total</td>";
															echo "<td>".number_format($total_vat1, 2)."</td>";
															echo "<td colspan='2'>".number_format($grand_total, 2)."</td>";
															echo "</tr>";
														}
													}
													else {
														echo "<tr>";
														echo "<td>No Sale found to list.</td>";
														echo "</tr>";
													}
												?>					
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
			<div class="box-content no-padding" id="counter_sale_orders_export" style=" display:none;" >	
	<table class="table table-striped table-bordered table-hover table-heading no-border-bottom" border="1px solid black">
		<thead>
			<tr>
				<th>#</th>
				<th>Settle Date</th>
				<th>User Name</th>													
				<th>Shop</th>
				<th>Cash Sale</th>
				<th>Card Sale</th>
				<th>Delivery Sale</th>
				<th>Sale Total</th>
				<th>Discount</th>
				<th>Net Total</th>
			</tr>
		</thead>
		<tbody style=" background: #fff none repeat scroll 0 0 !important;color: #525252;">
			<?php	$grand_total = "0.00";												
				$prs = getSettleSales($from_date1, $to_date1,$shops, $pageLimit, $setLimit, $export="1");	
				if($prs != false) {
					$pcount = mysqli_num_rows($prs);
					if($pcount > 0) {
						for($p = 0; $p < $pcount; $p++) {
							$prow = mysqli_fetch_object($prs);
							$id = $prow->id;
							$settle_dat = $prow->settle_dat;
							$cash_sale = ($prow->cash_sale !='') ? $prow->cash_sale :  '0.00' ;
							$card_sale = ($prow->card_sale !='') ? $prow->card_sale :  '0.00' ;
							$total_cgst = ($prow->total_cgst !='') ? $prow->total_cgst :  '0.00' ;
							$total_sgst = ($prow->total_sgst !='') ? $prow->total_sgst :  '0.00' ;
							$total_gst = ($prow->total_gst !='') ? $prow->total_gst :  '0.00' ;
							$delivery_sale = ($prow->delivery_sale !='') ? $prow->delivery_sale : '0.00';
							$discount = ($prow->discount !='') ? $prow->discount :  '0.00' ;

							$user_name = getUserName($prow->user_id);
							//$name = getManufacturingUnitName($prow->manufacturing_unit_id);
							$shop_name = getShopName($prow->shop_id);
							$total_without_discount = ($prow->gross_total !='') ? $prow->gross_total :  '0.00' ;
							$total_with_discount = $prow->net_total;
							$total = "0.00";																
							echo "<tr>";
							echo "<td>".$id."</td>";
							echo "<td>".$settle_dat."</td>";
							echo "<td>".$user_name."</td>";	
							echo "<td>".$shop_name."</td>";
							echo "<td>".$cash_sale."</td>";
							echo "<td>".$card_sale."</td>";	
							echo "<td>".$delivery_sale."</td>";																
							echo "<td>".number_format($total_without_discount, 2)."</td>";
							echo "<td>".$discount."</td>";
							echo "<td>".number_format(($total_without_discount-$discount), 2)."</td>";	
							echo "</tr>";
							$grand_total += $total_without_discount-$discount;
						}
						echo "<tr>";
						echo "<td colspan='9'>Grand Total</td>";
						echo "<td colspan='2'>".number_format($grand_total, 2)."</td>";
						echo "</tr>";
					}
				}
				else {
					echo "<tr>";
					echo "<td>No Sale found to list.</td>";
					echo "</tr>";
				}
			?>					
		</tbody>
	</table>
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
    if(id && confirm('Are you sure you want to delete this category?'))
    {
        window.location.href = site_url+'/admin/manage_orders.php?id='+id+'&act=delete';
    }
}

$(document).on('click', '.print_me', function(e) {
	$(".hide_print").hide();
	var content = document.getElementById('counter_sale_orders_export').innerHTML;
	var win = window.open();	
	//win.document.write('<link href="css/style_v1.css" rel="stylesheet">');
	//win.document.write('<link href="core/framework/libs/pj/css/pj-table.css" rel="stylesheet" type="text/css" />');			
	win.document.write(content);	
	win.print();			
});

</script>
	</body>
</html>