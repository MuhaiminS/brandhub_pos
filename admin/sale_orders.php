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
	$run = mysqli_query($GLOBALS['conn'], $query);
	while($row = mysqli_fetch_array($run)) {
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

function getItemName($item_id)
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

function getSaleorders($receipt_id = '',$payment_type = '', $from_date ='', $to_date='', $shop='', $pageLimit='', $setLimit='', $export="")
{
	$date = date('Y-m-d');
	$qry="SELECT * FROM sale_orders WHERE order_type = 'counter_sale'"; 
	if($receipt_id != ''){
		$qry .=" AND receipt_id = '$receipt_id'";
	}
	if($shop != ''){
		$qry .=" AND shop_id = '$shop'";
	}
	if($payment_type != ''){
		$qry .=" AND payment_type = '$payment_type'";
	}
	if($from_date != '' && $to_date != '' ) {
		
		$qry .= " AND ordered_date BETWEEN '$from_date 00:00:00' AND '$to_date 23:59:59' ";		
	} 
	if($from_date != '' && $to_date == '' ) {
		$qry .= " AND ordered_date BETWEEN '$from_date 00:00:00' AND '$date 23:59:59' ";
	}

	if($from_date == '' && $to_date != '' ) {
		$qry .= " AND ordered_date <= '$to_date 23:59:59'";
	}
	
	if($export == '') {
		$qry .=" ORDER BY ordered_date DESC LIMIT $pageLimit, $setLimit";
	} else {
		$qry .=" ORDER BY ordered_date DESC ";
	}
	//echo $qry; die;
	$result=mysqli_query($GLOBALS['conn'], $qry);
	//$num=mysqli_num_rows($result);
	//echo "total result ".$num;
	if($result)
	{
		return $result;
	}
	else
	return false;
}

function displayPaginationBelows($per_page,$page,$receipt_id = '',$payment_type = '', $from_date ='', $to_date='', $shop='', $export="") {
    $page_url="?";
	$query = "SELECT COUNT(*) as totalCount FROM sale_orders WHERE order_type = 'counter_sale'";
	if($receipt_id != ''){
		$query .=" AND receipt_id = '$receipt_id'";
	}
	if($shop != ''){
		$query .=" AND shop_id = '$shop'";
	}
	if($payment_type != ''){
		$query .=" AND payment_type = '$payment_type'";
	}
	if($from_date != '' && $to_date != '' ) {
		
		$query .= " AND ordered_date BETWEEN '$from_date 00:00:00' AND '$to_date 23:59:59' ";		
	} 
	if($from_date != '' && $to_date == '' ) {
		$query .= " AND ordered_date BETWEEN '$from_date 00:00:00' AND '$from_date 23:59:59' ";
	}
	if($from_date == '' && $to_date != '' ) {
		$query .= " AND ordered_date <= '$to_date 23:59:59'";
	}	
	//echo $query;exit;
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
					$setPaginate.= "<li><a href='{$page_url}page=$counter&receipt_id=$receipt_id&shop=$shop&payment_type=$payment_type&from_date=$from_date&to_date=$to_date'>$counter</a></li>";
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
						$setPaginate.= "<li><a href='{$page_url}page=$counter&receipt_id=$receipt_id&shop=$shop&payment_type=$payment_type&from_date=$from_date&to_date=$to_date'>$counter</a></li>";
				}
				$setPaginate.= "<li class='dot'>...</li>";
				$setPaginate.= "<li><a href='{$page_url}page=$lpm1&receipt_id=$receipt_id&shop=$shop&payment_type=$payment_type&from_date=$from_date&to_date=$to_date'>$lpm1</a></li>";
				$setPaginate.= "<li><a href='{$page_url}page=$setLastpage&receipt_id=$receipt_id&shop=$shop&payment_type=$payment_type&from_date=$from_date&to_date=$to_date'>$setLastpage</a></li>"; 
			}
			elseif($setLastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2))
			{
				$setPaginate.= "<li><a href='{$page_url}page=1&receipt_id=$receipt_id&shop=$shop&payment_type=$payment_type&from_date=$from_date&to_date=$to_date'>1</a></li>";
				$setPaginate.= "<li><a href='{$page_url}page=2&receipt_id=$receipt_id&shop=$shop&payment_type=$payment_type&from_date=$from_date&to_date=$to_date'>2</a></li>";
				$setPaginate.= "<li class='dot'>...</li>";
				for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++)
				{
					if ($counter == $page)
						$setPaginate.= "<li><a class='current_page'>$counter</a></li>";
					else
						$setPaginate.= "<li><a href='{$page_url}page=$counter&receipt_id=$receipt_id&shop=$shop&payment_type=$payment_type&from_date=$from_date&to_date=$to_date'>$counter</a></li>";
				}
				$setPaginate.= "<li class='dot'>..</li>";
				$setPaginate.= "<li><a href='{$page_url}page=$lpm1&receipt_id=$receipt_id&shop=$shop&payment_type=$payment_type&from_date=$from_date&to_date=$to_date'>$lpm1</a></li>";
				$setPaginate.= "<li><a href='{$page_url}page=$setLastpage&receipt_id=$receipt_id&shop=$shop&payment_type=$payment_type&from_date=$from_date&to_date=$to_date'>$setLastpage</a></li>"; 
			}
			else
			{
				$setPaginate.= "<li><a href='{$page_url}page=1&receipt_id=$receipt_id&shop=$shop&payment_type=$payment_type&from_date=$from_date&to_date=$to_date'>1</a></li>";
				$setPaginate.= "<li><a href='{$page_url}page=2&receipt_id=$receipt_id&shop=$shop&payment_type=$payment_type&from_date=$from_date&to_date=$to_date'>2</a></li>";
				$setPaginate.= "<li class='dot'>..</li>";
				for ($counter = $setLastpage - (2 + ($adjacents * 2)); $counter <= $setLastpage; $counter++)
				{
					if ($counter == $page)
						$setPaginate.= "<li><a class='current_page'>$counter</a></li>";
					else
						$setPaginate.= "<li><a href='{$page_url}page=$counter&receipt_id=$receipt_id&shop=$shop&payment_type=$payment_type&from_date=$from_date&to_date=$to_date'>$counter</a></li>";
				}
			}
		}
		if ($page < $counter - 1){
			$setPaginate.= "<li><a href='{$page_url}page=$next&receipt_id=$receipt_id&shop=$shop&payment_type=$payment_type&from_date=$from_date&to_date=$to_date'>Next</a></li>";
			$setPaginate.= "<li><a href='{$page_url}page=$setLastpage&receipt_id=$receipt_id&shop=$shop&payment_type=$payment_type&from_date=$from_date&to_date=$to_date'>Last</a></li>";
		}else{
			$setPaginate.= "<li><a class='current_page'>Next</a></li>";
			$setPaginate.= "<li><a class='current_page'>Last</a></li>";
		}
		$setPaginate.= "</ul>\n"; 
	}
	return $setPaginate;
}

function getFillingName($filling_id)
{
	$where = "WHERE id = '$filling_id'";
	$service = getnamewhere('cake_filling', 'filling_name', $where);
	return $service;
}
function getFlavourName($flavour_id)
{
	$where = "WHERE id = '$flavour_id'";
	$flavour = getnamewhere('cake_flavours', 'flavour_name', $where);
	return $flavour;
}
function getcategoryName($category_id)
{
	$where = "WHERE id = '$category_id'";
	$category = getnamewhere('category', 'category_title', $where);
	return $category;
}

function getSaleOrderitems($id)
{	
	$id = isset($id) ? $id : '';
	$qry="SELECT * FROM sale_order_items WHERE  sale_order_id = '".$id."'";
	
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
$receipt_ids = (isset($_GET['receipt_id']) && $_GET['receipt_id'] !='') ? $_GET['receipt_id'] : '';
$payment_types = (isset($_GET['payment_type']) && $_GET['payment_type'] !='') ? $_GET['payment_type'] : '';
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
						Counter Sales
						<!--<small>Optional description</small>-->
					</h1>
					<ol class="breadcrumb">
						<li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
						<li class="active">Counter Sales</li>
					</ol>
				</section>
				<!-- Main content -->
				<section class="content container-fluid">
					<div class="row">
						<div class="col-xs-12">
							<div class="box">
								<div class="box-header">
									<h3 class="box-title">Counter Sales</h3>
								</div>
								<!-- /.box-header -->
								<div class="box-body">
									<div id="form" class="panel panel-warning" style="display: block;">
										<div class="panel-body">
											<form action="sale_orders.php" accept-charset="utf-8">
												<div class="row">
													<div class="col-sm-3">
														<div class="form-group">
															<label for="receipt_id">Receipt id.</label> <input type="text" name="receipt_id" value="<?php echo $receipt_ids; ?>" class="form-control tip" id="receipt_id">
														</div>
													</div>
													<div class="col-sm-3">
														<div class="form-group">
															<label>Payment Status</label>
															<select name="payment_type" id="payment_type" class="form-control select2" style="width: 100%;">
																<option value=''>--Select Payment Type--</option>
																<option value="cash" <?php if($payment_types == 'cash') { echo "Selected"; } ?>>Cash</option>
																<option value="card" <?php if($payment_types == 'card') { echo "Selected"; } ?>>Card</option>
																<option value="credit" <?php if($payment_types == 'credit') { echo "Selected"; } ?>>Credit</option>
															</select>
														</div>
													</div>
													<div class="col-sm-3">
														<div class="form-group">
															<label>Start date</label>sssss
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
														<a href="sale_orders.php" class="btn btn-default">Reset</a>
													</div>
												</div>
											</form>
										</div>
									</div>
									<?php include("sale_order_items.php"); ?>

									<div class="row">
										<div class="col-md-6"></div>
										<div class="col-md-6 text-right pr0">
											<div class="dt-buttons btn-group">												
												<?php if($receipt_ids != '' || $shops != '' || $payment_types != '' || $from_date1 != '' || $to_date1 != '') { ?>
												<a target="_blank" href="excel_export.php?sale=Counter Sale&order_type=counter_sale&get_type=excel&receipt_id=<?php echo $receipt_ids; ?>&shop=<?php echo $shops; ?>&payment_type=<?php echo $payment_types; ?>&from_date=<?php echo $from_date1; ?>&to_date=<?php echo $to_date1; ?>" class="btn btn-default buttons-csv buttons-html5 export"><span>Excel</span></a>
												 <?php } else { ?>
												<a target="_blank" href="excel_export.php?sale=Counter Sale&order_type=counter_sale&get_type=excel" class="btn btn-default buttons-csv buttons-html5 export"><span>Excel</span></a>
												<?php } ?>

											</div>
										</div>
									</div>
									<table class="table table-striped table-bordered table-hover table-heading no-border-bottom">
											<thead>
												<tr>
													<th>#</th>
													<th>Receipt Id</th>													
													<th>Contact Details</th>	
													<th>Shop</th>
													<th>Date&Time</th>
													<th>Payment Type</th>
													<th>Total</th>
													<?php if(BILL_TAX == 'yes'){ if(BILL_TAX_TYPE == 'VAT'){ ?>
													<th>VAT</th>
													<?php } elseif(BILL_TAX_TYPE == 'GST'){ ?>
													<th>SGST</th>
													<th>CGST</th>
													<?php } } ?>
													<th>Final Total</th>													
													<th class="hide_print">Action</th>
												</tr>
											</thead>
											<tbody>
												<?php	$grand_total =  $grand_tota_gst = "0.00";												
													$prs = getSaleorders($receipt_ids,$payment_types,$from_date1, $to_date1,$shops, $pageLimit, $setLimit, $export="");	
													if($prs != false) {
														$pcount = mysqli_num_rows($prs);
														if($pcount > 0) {
															for($p = 0; $p < $pcount; $p++) {
																$prow = mysqli_fetch_object($prs);
																$id = $prow->id;
																$receipt_id = $prow->receipt_id;
																$contact_name = $prow->contact_name;
																$contact_number = $prow->contact_number;
																$address = $prow->address;
																$ordered_date = $prow->ordered_date;																
																$payment_status = $prow->payment_status;
																$payment_type = $prow->payment_type;
																$discount = $prow->discount;
																$status = $prow->status;
																$vat = $prow->vat;
																$user_name = getUserName($prow->user_id);
																//$name = getManufacturingUnitName($prow->manufacturing_unit_id);
																$shop_name = getShopName($prow->shop_id);
																$total = $net_total = $total_sgst = $total_cgst = "0.00";
																$prs2 = getSaleOrderitems($id);
																if($prs2 != false) {
																	$pcount2 = mysqli_num_rows($prs2);
																	if($pcount2 > 0) {
																		for($p2 = 0; $p2 < $pcount2; $p2++) {
																			$prow2 = mysqli_fetch_object($prs2);													
																			$price = $prow2->price;
																			$qty = $prow2->qty;
																			//$cgst = $prow2->CGST;
																			//$sgst = $prow2->SGST;
																			$total += $price * $qty;
																			$total_sgst += (($price * $qty)*$prow2->SGST)/100;
																			$total_cgst += (($price * $qty)*$prow2->CGST)/100;
																			//$total = $net_total + (($net_total / 100) * ($cgst + $sgst));
																		}
																		$vat_price = (($total / 100) * ($vat));
																		$with_vat_price = $total + (($total / 100) * ($vat));
																	}
																}
																echo "<tr>";
																echo "<td>".$id."</td>";
																echo "<td>".$receipt_id."</td>";
																echo "<td>Name:".$contact_name."<br><br>Address:".$address."<br>Ph:".$contact_number."</td>";	
																echo "<td>".$shop_name."</td>";
																echo "<td>".$ordered_date."</td>";
																echo "<td>".$payment_type."</td>";	
																//echo "<td>".$payment_status."</td>";
																//echo "<td>".$status_arr[$status]."</td>";
																echo "<td>".number_format($total, 2)."</td>";
																if(BILL_TAX == 'yes'){ if(BILL_TAX_TYPE == 'VAT'){ 
																echo "<td>".$vat_price."</td>";
																 } elseif(BILL_TAX_TYPE == 'GST'){ 
																 	echo "<td align='right'>".number_format($total_sgst, 2)."</td>";
																	echo "<td align='right'>".number_format($total_cgst, 2)."</td>";
															   } } 
															   if(BILL_TAX == 'yes'){ if(BILL_TAX_TYPE == 'VAT'){ 
																echo "<td>".number_format($with_vat_price-$discount, 2)."</td>"; } elseif(BILL_TAX_TYPE == 'GST'){	
																echo "<td align='right'>".number_format($total-$discount, 2)."</td>";	
															} }											
																if($status == 'deleted'){
																echo "<td></td>";/*"<td><a href='javascript:void(0)' onclick='deleteIt($id);'>Delete</a></td>";*/
																}else{
																echo "<td class='hide_print'>";
																	echo "<div class=\"text-center\">";
																		echo "<div class=\"btn-group\">";
																			echo "<a href=\"javascript:void(0)\" data-toggle=\"modal\" data-target=\"#myModal".$id."\" title=\"View Items\" class=\"tip btn btn-warning btn-xs\"><i class=\"fa fa-eye\"></i></a><a href='single_item_print.php?id=$id&re=sale_orders.php' class='btn btn-primary btn-xs'><i class='fa fa-print'></i></a>";
																			//echo "<a href=\"javascript:void(0)\" onclick=\"deleteIt($id);\" title=\"Delete Expense\" class=\"tip btn btn-danger btn-xs\"><i class=\"fa fa-trash-o\"></i></a>";
																		echo "</div>";
																	echo "</div>";
																echo "</td>";
																}
																echo "</tr>";
																$grand_total += $with_vat_price-$discount;
																$grand_tota_gst += $total-$discount;
															}
															echo "<tr>";
															echo "<td colspan='8'>Grand Total</td>";
															if(BILL_TAX == 'yes'){ if(BILL_TAX_TYPE == 'VAT'){ 
															echo "<td colspan='2'>".number_format($grand_total, 2)."</td>";
															 } elseif(BILL_TAX_TYPE == 'GST'){
															 echo "<td colspan='2' align='right'>".number_format($grand_tota_gst, 2)."</td>";
															 } }

															echo "</tr>";
														}else {
														echo "<tr>";
														echo "<td colspan='9'>No Orders found to list.</td>";
														echo "</tr>";
													}
													}
													else {
														echo "<tr>";
														echo "<td colspan='9'>No Orders found to list.</td>";
														echo "</tr>";
													}
												?>					
											</tbody>
										</table>
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
		<script type="text/javascript">
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
				win.window.close();
			});
		</script>
	</body>
</html>