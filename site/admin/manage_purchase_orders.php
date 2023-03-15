<?php 
session_start();
include("../functions.php");
include_once("../config.php");
chkAdminLoggedIn();
connect_dre_db();

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

function getItemName($item_id)
{
	$where = "WHERE id = '$item_id'";
	$service = getnamewhere('items', 'name', $where);
	return $service;
}

/*function getPurchaseOrderItemDetailsList($shop='', $from_date='', $to_date='') {
	
	$query = "SELECT * FROM purchase_orders";
	if(isset($shop) && $shop != '') {
		$query .= " where shop_id ='$shop'";
	} 
	if((isset($from_date) && $from_date != '') && (isset($to_date) && $to_date != '' )) {
		
		if(isset($shop) && $shop != ''){
			$query .= " AND date_added BETWEEN  '$from_date' AND  '$to_date' ";
		}else{
			$query .= " where date_added BETWEEN  '$from_date' AND  '$to_date' ";
		}
		
	} 
	$query .= " ORDER BY id DESC";

	$result = mysql_query($query);
	if ($result) {
		$result_arr = array();
		while ($row = mysql_fetch_assoc($result)) {
			$result_ar = array();
			$result_ar['id'] = $row['id'];
			$result_ar['user_id'] = $row['user_id'];
			$result_ar['shop_id'] = $row['shop_id'];
			$shop_id = $row['shop_id'];
			$result_sh=mysql_query("SELECT * from locations_shops where id=$shop_id");
			$shop=mysql_fetch_assoc($result_sh);
			$result_ar['shop_name']=$shop['shop_name'];
			$result_ar['mfunits_id'] = $row['manufacturing_unit_id'];
			$result_ar['status'] = $row['status'];
			$result_ar['date_added']=$row['date_added'];
		
			
			//$order_items_arr = $items_arr = ';
			$query2 = "SELECT * FROM purchase_order_items WHERE purchase_order_id='".$row['id']."'";
			$result2 = mysql_query($query2);
			if ($result2) { 
				while ($row2 = mysql_fetch_assoc($result2)) {
					$item_ids = $row2['item_id'];
					if($row['status'] == 'pending') {
						$item_name = getItemName($row2['item_id']);
						if($row2['item_id'] == $item_ids) {
							//$order_items_arr[] = $row2;
							$order_items_arr[$item_name] += $row2['qty'];
							$item_ids = $row2['item_id'];
						}
					}
				}
			}		
			$result_ar['items'] = json_encode( $order_items_arr );
			$result_arr[] = $result_ar; 
		}			
		return $order_items_arr;
	}		
	else {
		return false;
	}
}*/

if(isset($_GET["page"])) {
	$page = (int)$_GET["page"];
} else {
	$page = 1;
}
$setLimit = 10;
$pageLimit = ($page * $setLimit) - $setLimit;

function getPurchaseOrderItemDetailsList($shop_name='', $from_date='', $to_date='', $pageLimit='', $setLimit='')
{
	$date = date('Y-m-d');
	$query = "SELECT * FROM purchase_order_items JOIN purchase_orders ON purchase_orders.id = purchase_order_items.purchase_order_id";
	if(isset($shop_name) && $shop_name != '') {
		$query .= " where purchase_orders.shop_id ='$shop_name'";
	} 
	if((isset($from_date) && $from_date != '') && (isset($to_date) && $to_date != '' )) {
		
		if(isset($shop_name) && $shop_name != ''){
			$query .= " AND purchase_orders.date_needed BETWEEN '$from_date 00:00:00' AND '$to_date 23:59:59' ";
		}else{
			$query .= " where purchase_orders.date_needed BETWEEN '$from_date 00:00:00' AND '$to_date 23:59:59' ";
		}
		
	}
	if($from_date != '' && $to_date == '' && $shop_name == '') {
		$query .= " Where purchase_orders.date_needed BETWEEN '$from_date 00:00:00' AND '$date 23:59:59' ";
	}

	if($from_date == '' && $to_date != '' && $shop_name == '') {
		$query .= " Where purchase_orders.date_needed <= '$to_date 23:59:59'";
	}
	if($from_date != '' && $to_date == '' && $shop_name != '') {
		$query .= " AND purchase_orders.date_needed BETWEEN '$from_date 00:00:00' AND '$date 23:59:59' ";
	}

	if($from_date == '' && $to_date != '' && $shop_name != '') {
		$query.= " AND purchase_orders.date_needed <= '$to_date 23:59:59'";
	}

	$query .= " AND purchase_orders.status = 'pending' ORDER BY purchase_order_items.item_id DESC"; // LIMIT $pageLimit, $setLimit";
//echo "<pre>"; print_r($query);//exit;

	$result = mysqli_query($GLOBALS['conn'], $query);
	if ($result) {
		$result_arr = array();
		while ($row = mysql_fetch_assoc($result)) {
			//echo '<pre>';print_r($row);echo '</pre>';
			if($row['status'] == 'pending') {
				//$item_name = (isset($row['item_id']) && $row['item_id'] !='') ? getItemName($row['item_id']) : '';
				if(isset($result_arr[$row['item_id']]))
					$result_arr[$row['item_id']] += $row['qty'];
				else
					$result_arr[$row['item_id']] = $row['qty'];
			}
		}
		//print_r($result_arr);exit;
		return $result_arr;
	}		
	else {
		return false;
	}
}

	//echo "<pre>"; print_r(getPurchaseOrderItemDetailsList());exit;

/*function displayPaginationBelow($per_page,$page,$shop_name='', $from_date='', $to_date='') {
    $page_url="?";
	$date = date('Y-m-d');
	$query = "SELECT count(*) as totalCount FROM purchase_order_items JOIN purchase_orders ON purchase_orders.id = purchase_order_items.purchase_order_id";
	if(isset($shop_name) && $shop_name != '') {
		$query .= " where purchase_orders.shop_id ='$shop_name'";
	} 
	if((isset($from_date) && $from_date != '') && (isset($to_date) && $to_date != '' )) {
		
		if(isset($shop_name) && $shop_name != ''){
			$query .= " AND purchase_orders.date_added BETWEEN '$from_date 00:00:00' AND '$to_date 23:59:59' ";
		}else{
			$query .= " where purchase_orders.date_added BETWEEN '$from_date 00:00:00' AND '$to_date 23:59:59' ";
		}
		
	}
	if($from_date != '' && $to_date == '' && $shop_name == '') {
		$query .= " Where purchase_orders.date_added BETWEEN '$from_date 00:00:00' AND '$date 23:59:59' ";
	}

	if($from_date == '' && $to_date != '' && $shop_name == '') {
		$query .= " Where purchase_orders.date_added <= '$to_date 23:59:59'";
	}
	if($from_date != '' && $to_date == '' && $shop_name != '') {
		$query .= " AND purchase_orders.date_added BETWEEN '$from_date 00:00:00' AND '$date 23:59:59' ";
	}

	if($from_date == '' && $to_date != '' && $shop_name != '') {
		$query.= " AND purchase_orders.date_added <= '$to_date 23:59:59'";
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
					$setPaginate.= "<li><a href='{$page_url}page=$counter&shop=$shop_name&from_date=$from_date&to_date=$to_date'>$counter</a></li>";
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
						$setPaginate.= "<li><a href='{$page_url}page=$counter&shop=$shop_name&from_date=$from_date&to_date=$to_date'>$counter</a></li>";
				}
				$setPaginate.= "<li class='dot'>...</li>";
				$setPaginate.= "<li><a href='{$page_url}page=$lpm1&shop=$shop_name&from_date=$from_date&to_date=$to_date'>$lpm1</a></li>";
				$setPaginate.= "<li><a href='{$page_url}page=$setLastpage&shop=$shop_name&from_date=$from_date&to_date=$to_date'>$setLastpage</a></li>"; 
			}
			elseif($setLastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2))
			{
				$setPaginate.= "<li><a href='{$page_url}page=1&shop=$shop_name&from_date=$from_date&to_date=$to_date'>1</a></li>";
				$setPaginate.= "<li><a href='{$page_url}page=2&shop=$shop_name&from_date=$from_date&to_date=$to_date'>2</a></li>";
				$setPaginate.= "<li class='dot'>...</li>";
				for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++)
				{
					if ($counter == $page)
						$setPaginate.= "<li><a class='current_page'>$counter</a></li>";
					else
						$setPaginate.= "<li><a href='{$page_url}page=$counter&shop=$shop_name&from_date=$from_date&to_date=$to_date'>$counter</a></li>";
				}
				$setPaginate.= "<li class='dot'>..</li>";
				$setPaginate.= "<li><a href='{$page_url}page=$lpm1&shop=$shop_name&from_date=$from_date&to_date=$to_date'>$lpm1</a></li>";
				$setPaginate.= "<li><a href='{$page_url}page=$setLastpage&shop=$shop_name&from_date=$from_date&to_date=$to_date'>$setLastpage</a></li>"; 
			}
			else
			{
				$setPaginate.= "<li><a href='{$page_url}page=1&shop=$shop_name&from_date=$from_date&to_date=$to_date'>1</a></li>";
				$setPaginate.= "<li><a href='{$page_url}page=2&shop=$shop_name&from_date=$from_date&to_date=$to_date'>2</a></li>";
				$setPaginate.= "<li class='dot'>..</li>";
				for ($counter = $setLastpage - (2 + ($adjacents * 2)); $counter <= $setLastpage; $counter++)
				{
					if ($counter == $page)
						$setPaginate.= "<li><a class='current_page'>$counter</a></li>";
					else
						$setPaginate.= "<li><a href='{$page_url}page=$counter&shop=$shop_name&from_date=$from_date&to_date=$to_date'>$counter</a></li>";
				}
			}
		}
		if ($page < $counter - 1){
			$setPaginate.= "<li><a href='{$page_url}page=$next&shop=$shop_name&from_date=$from_date&to_date=$to_date'>Next</a></li>";
			$setPaginate.= "<li><a href='{$page_url}page=$setLastpage&shop=$shop_name&from_date=$from_date&to_date=$to_date'>Last</a></li>";
		}else{
			$setPaginate.= "<li><a class='current_page'>Next</a></li>";
			$setPaginate.= "<li><a class='current_page'>Last</a></li>";
		}
		$setPaginate.= "</ul>\n"; 
	}
	return $setPaginate;
}*/

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
//echo "<pre>"; print_r(getShops());exit;

function getManufacturingUnitName($manufa_id)
{
	$where = "WHERE id = '$manufa_id'";
	$service = getnamewhere('locations_manufacturing_units', 'name', $where);
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

function getPurchaseOrderitems($id)
{	
	$id = isset($id) ? $id : '';
	$qry="SELECT * FROM purchase_order_items WHERE  purchase_order_id = '".$id."'";
	
	//echo $qry;
	$result=mysql_query($qry);
	$num=mysql_num_rows($result);
	
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
$shop1 = (isset($_GET['shop']) && $_GET['shop'] !='') ? $_GET['shop'] : '';
$from_date1 = (isset($_GET['from_date']) && $_GET['from_date'] !='') ? $_GET['from_date'] : date('Y-m-d');
$to_date1 = (isset($_GET['to_date']) && $_GET['to_date'] !='') ? $_GET['to_date'] : date('Y-m-d');

?>
<!-- Start include Header -->
<?php include('header.php'); ?>
<link href="css/jquery-ui.css" rel="stylesheet">
<link href="css/pagination.css" rel="stylesheet">
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
							<li><a href="javascript:void(0);">Purchase Order Reports
							</a></li>
						</ol>
					</div>
				</div>
						 
				  <ul class="nav nav-tabs">
					<li class="active"><a href="manage_purchase_orders.php">Purchase Orders</a></li>										
				  </ul>

				  <div class="tab-content">
					<div id="counter_sale" class="tab-pane fade in active">
						<div class="row">			
							<label class="control-label" style="margin-left:15px;">Search</label>
							<form action="manage_purchase_orders.php">
							<input type="hidden" class="form-control" name="page" id="page" value="<?php echo $page; ?>"/>
							<div class="form-group" style="margin-bottom:0px;">	
								<div class="col-sm-2">									
									<select name="shop" class="form-control">
										<option value=""> --Shops--  </option>
										<?php $shop_list = getShopsList();
										foreach ($shop_list as $shop_lo)
										{ 
											?><option value="<?php echo $shop_lo['shop_id']; ?>" <?php echo ($shop1 == $shop_lo['shop_id']) ? ' selected="selected"' : Null; ?> ><?php echo $shop_lo['shop_name']; ?></option><?php
										}
										?>
									</select>
									<span id="loader"></span>
								</div>
								<div class="col-sm-5">									
									<span class="pj-form-field-custom pj-form-field-custom-after float_left r5">
										<input type="text" name="from_date" placeholder="From Date" id="from_date" value="<?php echo $from_date1; ?>" class="datepick_acc1 pointer" readonly="readonly" />
									</span>									
									<span class="pj-form-field-custom pj-form-field-custom-after float_left r5">
										<input type="text" name="to_date"  placeholder="To Date" id="to_date" value="<?php echo $to_date1; ?>" class="pj-form-field w80 datepick_acc2 pointer" readonly="readonly" />										
									</span>
								</div>
								<div class="col-sm-5">
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
									</style>
									<a href="manage_purchase_orders.php" class="reset btn-default">Reset search</a>
								
								<div class="print1" style="float:right;padding: 0.2em; margin-right:2em;"><input type="button" class="print print_me" value="print" /></div>
								</div>
							</div>
							</form>
							<div class="col-xs-12">
								<div class="box">
									<div class="box-header">
										<div class="box-name">
											<i class="fa fa-table"></i>
											<span>Purchase Orders List</span>
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
											table {
												width:100%;
											}
										</style>
										<table class="table table-striped table-bordered table-hover table-heading no-border-bottom">
											<thead>
												<tr>
													<th>#</th>
													<!-- <th>User Name</th>								
													<th>Shop</th> 
													<th>Manufacturing Location</th>-->
													<th>Item</th>
													<th>Qty</th>
													<!-- <th>Date</th>
													<th>Status</th>													
													<th>View</th> -->
												</tr>
											</thead>
											<tbody>
												<?php												
													$purchase_order = getPurchaseOrderItemDetailsList($shop1, $from_date1, $to_date1, $pageLimit, $setLimit);
														if(count($purchase_order) > 0 && !empty($purchase_order)) { $i = 1;
															foreach($purchase_order as $key => $purchase) {	
																$item_name = getItemName($key);
																if($item_name != '') {
																/*$id = $purchase['id'];
																$sno = $key+1;
																$user_name = getUserName($purchase['user_id']);
																$shop_name = $purchase['shop_name'];
																$mfunits_name = getManufacturingUnitName($purchase['mfunits_id']);
																$status = $purchase['status'];
																$date_added = $purchase['date_added'];	*/
																//if($purchase['status'] == 'pending') {
																	$sno = $i++;

																	echo "<tr>";
																	echo "<td>".$sno."</td>";
																	//echo "<td>".$user_name."</td>";
																	//echo "<td>".$shop_name."</td>";	
																	//echo "<td>".$mfunits_name."</td>";
																	echo "<td>".$item_name."</td>";	
																	echo "<td>".$purchase."</td>";
																	//echo "<td>".$date_added."</td>";													
																	//echo "<td>".$status."</td>";
																	//echo " <td class='hide_print'><a data-toggle='modal' data-target='#myModal".$id."' style='cursor:pointer;'><i class='fa fa fa-eye'></i></a></td>";
																	echo "</tr>";
																}
																
															}
															//echo "<tr>";
															//echo "<td colspan='8'>Grand Total</td>";
															//echo "<td colspan='2'>".number_format(122, 2)."</td>";
															//echo "</tr>";
													
													}
													else {
														echo "<tr>";
														echo "<td colspan='3'>No Orders found to list.</td>";
														echo "</tr>";
													}
												?>					
											</tbody>
										</table>
									</div>
								</div>
								<?php //echo displayPaginationBelow($setLimit,$page,$shop1, $from_date1, $to_date1); ?>
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

<?php //include("purchase_order_items.php"); ?>
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
	var content = document.getElementById('counter_sale_orders').innerHTML;
	var win = window.open();	
	win.document.write('<link href="css/style_v1.css" rel="stylesheet">');
	//win.document.write('<link href="core/framework/libs/pj/css/pj-table.css" rel="stylesheet" type="text/css" />');			
	win.document.write(content);	
	win.print(content);			
});

$( "#from_date" ).datepicker({dateFormat: 'yy-mm-dd'});
$( "#to_date" ).datepicker({dateFormat: 'yy-mm-dd'});

</script>
</body>
</html>