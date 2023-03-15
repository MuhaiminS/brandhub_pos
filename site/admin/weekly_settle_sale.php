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


if(isset($_GET["page"])) {
	$page = (int)$_GET["page"];
} else {
	$page = 1;
}
$setLimit = 10;
$pageLimit = ($page * $setLimit) - $setLimit;

function getSettleSales($from_date ='', $to_date='', $shop='', $pageLimit='', $setLimit='', $export)
{
	$date = date('Y-m-d');
	$qry="SELECT *, DATE_FORMAT(settle_date,'%Y-%m-%d') as settle_dat FROM settle_sale WHERE DATE_FORMAT(settle_date,'%Y-%m-%d') >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)"; 
	
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
		$qry .=" ORDER BY id DESC LIMIT $pageLimit, $setLimit";
	} else {
		$qry .=" ORDER BY id DESC";
	}
	//echo $qry;
	$result=mysql_query($qry);
	//$num=mysql_num_rows($result);	//echo "total result ".$num;
	if($result)
	{
		return $result;
	}
	else
	return false;
}

function displayPaginationBelows($per_page,$page,$from_date ='', $to_date='', $shop='', $export) {
    $page_url="?";
	$date = date('Y-m-d');
	$query = "SELECT COUNT(*) as totalCount FROM settle_sale WHERE DATE_FORMAT(settle_date,'%Y-%m-%d') >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";	
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
	$rec = mysql_fetch_array(mysql_query($query));
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
$export = (isset($_GET['export']) && $_GET['export'] !='') ? $_GET['export'] : '';

?>
<!-- Start include Header -->
<?php include('header.php'); ?>
<!-- End include Header -->
<!--Start Container-->
<div id="main" class="container-fluid">
	<div class="row">
		<!-- START left bar -->
		<?php include('left.php'); ?>
		<!-- END left bar -->

		<!--Start Content-->
		<div id="content" class="col-xs-12 col-sm-10">
			<div id="about">
				<div class="about-inner">
					<h4 class="page-header">Open-source admin theme for you</h4>
					<p>DevOOPS team</p>
					<p>Homepage - <a href="http://devoops.me" target="_blank">http://devoops.me</a></p>
					<p>Email - <a href="mailto:devoopsme@gmail.com">devoopsme@gmail.com</a></p>
					<p>Twitter - <a href="http://twitter.com/devoopsme" target="_blank">http://twitter.com/devoopsme</a></p>
					<p>Donate - BTC 123Ci1ZFK5V7gyLsyVU36yPNWSB5TDqKn3</p>
				</div>
			</div>
			<div class="preloader">
				<img src="img/devoops_getdata.gif" class="devoops-getdata" alt="preloader"/>
			</div>
			<div id="">
				<!-- CATEGORY START -->
				<div class="row">
					<div id="breadcrumb" class="col-xs-12">
						<a href="#" class="show-sidebar">
							<i class="fa fa-bars"></i>
						</a>
						<ol class="breadcrumb pull-left">
							<li><a href="index.php">Dashboard</a></li>
							<li><a href="javascript:void(0);">Weekly Settle Sale Reports
							</a></li>
						</ol>
					</div>
				</div>
				 
				  <div class="tab-content">
					<div id="counter_sale" class="tab-pane fade in active">
						<div class="row">			
							<label class="control-label" style="margin-left:15px;">Search</label>
							<form action="weekly_settle_sale.php">
							<div class="row search-st">
								<input type="hidden" class="form-control" name="page" id="page" value="<?php echo $page; ?>"/>
								<div class="form-group search_val" style="margin-bottom:0px;">	
									<div class="col-sm-4">
										<span class="pj-form-field-custom pj-form-field-custom-after float_left r5">
											<input type="text" name="from_date" id="from_date" placeholder="From Date" value="<?php echo $from_date1; ?>" class="datepick_acc1 pointer" readonly="readonly" />
										</span>
									</div>
									<div class="col-sm-4">	
										<span class="pj-form-field-custom pj-form-field-custom-after float_left r5">
											<input type="text" name="to_date" id="to_date" placeholder="To Date" value="<?php echo $to_date1; ?>" class="pj-form-field w80 datepick_acc2 pointer" readonly="readonly" />										
										</span>
									</div>
									<div class="col-sm-1">
										<input type="submit" value="Search" class="aa-search-btn">	
										<style>
										.reset {										
											border: 1px solid #B2BEB5;
											color: #000;										
											padding: 0.2em;
											text-align: center;
											text-decoration: none;										
										}
										.reset:hover {
											border: 1px solid #0078d7;
											text-decoration: none;
											color: #0078d7;
										}
										.search-st .pointer {width :100%}
										.print1 {font-size: 20px; display: inline-flex; margin-left: 0px;}
										.print2 {font-size: 20px; display: inline-flex; margin-right: 20px;}
										.search-st{ margin-bottom: 15px;}
										</style>
										
										<!-- <a href="manage_sale_orders.php" class="reset btn-default">Reset search</a> -->
									</div>
									<div class="col-sm-3" title="Reset Search"><a href="weekly_settle_sale.php" class="print2"><i class="fa fa-refresh" ></i></a>
									<div class="print1" title="Print"><a class="print print_me"><i class="fa fa-print" ></i></a></div>
									<div class="print2" title="Export"><a class="print excel_me"><i class="fa fa-file-excel-o"></i></a></div>
									<input type="submit" name="export" value="View All" Title="View All" class="aa-search-btn">
									</div>
								</div>
							</div>
							</form>
							<div class="col-xs-12">
								<div class="box">
									<div class="box-header">
										<div class="box-name">
											<i class="fa fa-table"></i>
											<span>Weekly Settle Sales List</span>
										</div>
										<div class="box-icons">
											<a class="collapse-link">
												<i class="fa fa-chevron-up"></i>
											</a>
											<a class="expand-link">
												<i class="fa fa-expand"></i>
											</a>
											<a class="close-link">
												<i class="fa fa-times"></i>
											</a>
										</div>
										<div class="no-move"></div>
									</div>
									<div class="box-content no-padding" id="counter_sale_orders">
										<style>
											body {
											   background: #fff none repeat scroll 0 0 !important;
											   color: #525252;
											}
										</style>
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
													<th>Net Total</th>
												</tr>
											</thead>
											<tbody>
												<?php	$grand_total = "0.00";												
													$prs = getSettleSales($from_date1, $to_date1,$shops, $pageLimit, $setLimit, $export);	
													if($prs != false) {
														$pcount = mysql_num_rows($prs);
														if($pcount > 0) {
															for($p = 0; $p < $pcount; $p++) {
																$prow = mysql_fetch_object($prs);
																$id = $prow->id;
																$settle_dat = $prow->settle_dat;
																$cash_sale = ($prow->cash_sale !='') ? $prow->cash_sale :  '0.00' ;
																$card_sale = ($prow->card_sale !='') ? $prow->card_sale :  '0.00' ;
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
								</div>
								<?php if($export == '') {
									echo displayPaginationBelows($setLimit,$page,$from_date1, $to_date1,$shops, $export);
								}
								?>
							</div>
						</div>
					</div>
				</div>
				<!-- CATEGORY END -->
			</div>
		</div>
		<!--End Content-->
	</div>
</div><div class="box-content no-padding" id="counter_sale_orders_export" style="display:none;">	
	<table class="table table-striped table-bordered table-hover table-heading no-border-bottom" border="1px solid Black">	
		<thead>
			<tr><th>Weekly Settle Sale Reports</th></tr>
			<tr><th>From Date: <?php echo $from_date1; ?></th><th>To Date: <?php echo $to_date1; ?></th></tr>
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
		<tbody style="background: #fff none repeat scroll 0 0 !important;color: #525252;">
			<?php	$grand_total = "0.00";												
				$prs1 = getSettleSales($from_date1, $to_date1,$shops, $pageLimit='', 0, $export='1');
				if($prs1 != false) {
					$pcount = mysql_num_rows($prs1);
					if($pcount > 0) {
						for($p = 0; $p < $pcount; $p++) {
							$prow = mysql_fetch_object($prs1);
							$id = $prow->id;
							$settle_dat = $prow->settle_dat;
							$cash_sale = ($prow->cash_sale !='') ? $prow->cash_sale :  '0.00' ;
							$card_sale = ($prow->card_sale !='') ? $prow->card_sale :  '0.00' ;
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
						echo "<td></td>";
						echo "<td></td>";
						echo "<td></td>";
						echo "<td></td>";
						echo "<td></td>";
						echo "<td></td>";
						echo "<td></td>";
						echo "<td></td>";
						echo "<td >Grand Total</td>";
						echo "<td >".number_format($grand_total, 2)."</td>";
						echo "</tr>";
					}
				}
				else {
					echo "<tr>";
					echo "<td  colspan='10'>No Sale found to list.</td>";
					echo "</tr>";
				}
			?>					
		</tbody>
	</table>
</div>		
<!--End Container-->
<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<!--<script src="http://code.jquery.com/jquery.js"></script>-->
<script src="plugins/jquery/jquery.min.js"></script>
<script src="plugins/jquery-ui/jquery-ui.min.js"></script>
<!-- Include all compiled plugins (below), or include individual files as needed -->
<script src="plugins/bootstrap/bootstrap.min.js"></script>
<script src="plugins/justified-gallery/jquery.justifiedGallery.min.js"></script>
<!--<script src="plugins/tinymce/tinymce.min.js"></script>
<script src="plugins/tinymce/jquery.tinymce.min.js"></script>-->
<script src="plugins/bootstrapvalidator/bootstrapValidator.min.js"></script>
<!-- All functions for this theme + document.ready processing -->
<script src="js/devoops.js"></script>
<script src="js/jquery-date.js"></script>
<script src="js/jquery-ui.js"></script>
<script src="js/jquery-bizzpro-login.js"></script>
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

$( "#from_date" ).datepicker({dateFormat: 'yy-mm-dd'});
$( "#to_date" ).datepicker({dateFormat: 'yy-mm-dd'});


jQuery(function($) {    
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
	/*$(".export").on('click', function (event) {			
		exportTableToCSV.apply(this, [$('#grid_export>table'), 'export_sales.csv']);        
	});*/
	$(".excel_me").on('click', function (event) {
		 exportTableToCSV.apply(this, [$('#counter_sale_orders_export>table'), 'export_weekly_settle_sales.csv']);
	});
});

</script>
</body>
</html>