<?php 
session_start();
include("../functions.php");
include_once("../config.php");
chkAdminLoggedIn();
connect_dre_db();
$server_url = getServerURL();
////$getUserDetails = getUserDetails($_SESSION['user_id']);
//$getUserDetails = explode(",", $getUserDetails['user_action']);
//if (!in_array('userwise_sale_report',$getUserDetails)){
	//redirect('index.php');
//}

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

function getDriverName($driver_id)
{
	$where = "WHERE id = '$driver_id'";
	$service = getnamewhere('drivers', 'name', $where);
	return $service;
}


if(isset($_GET["page"])) {
	$page = (int)$_GET["page"];
} else {
	$page = 1;
}
$setLimit = 10;
$pageLimit = ($page * $setLimit) - $setLimit;

function getSalesDetails($from_date ='', $to_date = '', $shop='', $pageLimit='', $setLimit='', $export="", $user_id = '')
{
	$date = date('Y-m-d');
	$qry="SELECT user_id, SUM(soi.qty*soi.price) as amount, driver_id FROM `sale_orders` so LEFT JOIN sale_order_items soi ON (soi.sale_order_id = so.id) WHERE order_type != 'combo'"; 
	
	if($shop != ''){
		$qry .=" AND shop_id = '$shop'";
	}
	
	if($user_id != ''){
		$qry .=" AND so.user_id= '$user_id'";
	}
	if($from_date != '' && $to_date != '') {
		$qry .= " AND so.ordered_date BETWEEN '$from_date' AND '$to_date' ";
	}
		$qry .="  GROUP BY so.driver_id ORDER BY so.driver_id ASC";
	//echo $qry;
	$result=mysqli_query($GLOBALS['conn'], $qry);
	//$num=mysqli_num_rows($result);	//echo "total result ".$num;
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
	if($user_id != ''){
		$query .=" AND user_id= '$user_id'";
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
$user_id1 = (isset($_GET['user_id']) && $_GET['user_id'] !='') ? $_GET['user_id'] : ''; 

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
							<li><a href="javascript:void(0);">User wise Reports
							</a></li>
						</ol>
					</div>
				</div>
				 
				  <div class="tab-content">
					<div id="counter_sale" class="tab-pane fade in active">
						<div class="row">
							<div class="form-group search_val" style="margin-bottom:0px;">																	
								
								<div class="print1" style="float:right; margin-bottom:10px;"><input type="button" class="print print_me" value="print" /></div>
							</div>
							</form>
							<div class="col-xs-12">
								<div class="box">
									<div class="box-header">
										<div class="box-name">
											<i class="fa fa-table"></i>
											<span>User wise List</span>
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
									<div class="box-content no-padding table-responsive" id="counter_sale_orders">
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
													<th>User name</th>
													<th>price</th>
												</tr>
											</thead>
											<tbody>
												<?php	$total_amount = "0.00";												
													$prs = getSalesDetails($from_date1,$to_date1,$shops, $pageLimit, $setLimit, $export="", $user_id1);	
													if($prs != false) {
														$pcount = mysqli_num_rows($prs);
														if($pcount > 0) {
															for($p = 0; $p < $pcount; $p++) {
																$prow = mysqli_fetch_object($prs);
																//$user_id = getUserName($prow->user_id);
																$driver_id = getDriverName($prow->driver_id);
																$amount= $prow->amount;
																$total_amount += $amount;																
																echo "<tr>";
																echo "<td>".($p+1)."</td>";
																echo "<td>".$driver_id."</td>";	
																echo "<td>".$amount."</td>";
																echo "</tr>";
															}
															echo "<tr>";
																echo "<td colspan='2' style='text-align: right;'><strong>Total sale amount</strong></td>";
																echo "<td><strong>".$total_amount."</strong></td>";
															echo "</tr>";
														}
													}
													else {
														echo "<tr>";
														echo "<td>No User wise Sale found to list.</td>";
														echo "</tr>";
													}
												?>					
											</tbody>
										</table>
									</div>									
								</div>
								<?php //echo displayPaginationBelows($setLimit,$page,$from_date1, $to_date1,$shops, $export=""); ?>
							</div>
						</div>
					</div>
				</div>
				<!-- CATEGORY END -->
			</div>
		</div>
		<!--End Content-->
	</div>
</div>
<div class="box-content no-padding" id="counter_sale_orders_export" style=" display:none;" >	
	<table class="table table-striped table-bordered table-hover table-heading no-border-bottom" style="width: 100%;">
		<thead>
			<tr>
				<th>#</th>
				<th>User name</th>
				<th>price</th>
			</tr>
		</thead>
		<tbody style=" background: #fff none repeat scroll 0 0 !important;color: #525252;">
			<?php	$total_amount = "0.00";												
				$prs = getSalesDetails($from_date1,$to_date1,$shops, $pageLimit, $setLimit, $export="1", $user_id1);	
				if($prs != false) {
					$pcount = mysqli_num_rows($prs);
					if($pcount > 0) {
						for($p = 0; $p < $pcount; $p++) {
							$prow = mysqli_fetch_object($prs);
							$user_id = getUserName($prow->user_id);
							$amount= $prow->amount;
							$total_amount += $amount;															
							echo "<tr>";
							echo "<td>".($p+1)."</td>";
							echo "<td>".$user_id."</td>";	
							echo "<td>".$amount."</td>";
							echo "</tr>";	
						}
						echo "<tr>";
							echo "<td colspan='2' style='text-align: right;'><strong>Total sale amount</strong></td>";
							echo "<td><strong>".$total_amount."</strong></td>";
						echo "</tr>";
					}
				}
				else {
					echo "<tr>";
					echo "<td>No User wise Sale found to list.</td>";
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
	win.document.write('<style>table {border-collapse: collapse;} table, td, th {border: 1px solid black;}</style>');
	win.document.write(content);	
	win.print();
	win.window.close();
});

$( "#from_date" ).datepicker({dateFormat: 'yy-mm-dd'});
$( "#to_date" ).datepicker({dateFormat: 'yy-mm-dd'});

</script>
</body>
</html>