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

function getSaleorders($receipt_id = '',$payment_type = '', $from_date ='', $to_date='', $shop='', $pageLimit='', $setLimit='')
{
	$date = date('Y-m-d');
	$qry="SELECT * FROM sale_orders WHERE order_type = 'website_order'";
	if($shop != ''){
		$qry .=" AND shop_id = '$shop'";
	}
	if($receipt_id != ''){
		$qry .=" AND receipt_id = '$receipt_id'";
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

function displayPaginationBelows($per_page,$page,$receipt_id = '',$payment_type = '', $from_date ='', $to_date='', $shop='') {
    $page_url="?";
	$query = "SELECT COUNT(*) as totalCount FROM sale_orders WHERE order_type = 'website_order'";
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
		$query .= " AND ordered_date BETWEEN '$from_date 00:00:00' AND '$date 23:59:59' ";
	}
	if($from_date == '' && $to_date != '' ) {
		$query .= " AND ordered_date <= '$to_date 23:59:59'";
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
							<li><a href="javascript:void(0);">Sale Order Reports
							</a></li>
						</ol>
					</div>
				</div>
						 
				  <ul class="nav nav-tabs">
					<li><a href="manage_sale_orders.php">Counter Sale</a></li>
					<li><a href="delivery_sales.php">Delivery Sale</a></li>
					<li><a href="dine_in.php">Dine In</a></li>
					<li><a href="take_away.php">Take away</a></li>
					<li class="active"><a href="website_order.php">Online order</a></li>
				  </ul>

				  <div class="tab-content">					
					<div id="delivery_sale" class="tab-pane fade in active">
						<div class="row">			
							<label class="control-label" style="margin-left:15px;">Search</label>
							<form action="manage_sale_orders.php">
							<input type="hidden" class="form-control" name="page" id="page" value="<?php echo $page; ?>"/>
							<div class="form-group search_val" style="margin-bottom:0px;">	
								<div class="col-sm-2">					
									<input type="text" class="form-control" name="receipt_id" id="receipt_id" placeholder="Receipt id" value="<?php echo $receipt_ids; ?>"/>					
									<span id="loader"></span>
								</div>								
								<div class="col-sm-2">
									<select name="shop" class="form-control">
										<option value="">  --Shops--  </option>
										<?php $shop_list = getShopsList();
										foreach ($shop_list as $shop_lo)
										{ 
											?><option value="<?php echo $shop_lo['shop_id']; ?>" <?php echo ($shops == $shop_lo['shop_id']) ? ' selected="selected"' : Null; ?> ><?php echo $shop_lo['shop_name']; ?></option><?php
										}
										?>
									</select>
									<span id="loader"></span>
								</div>
								<div class="col-sm-2">					
									<select class="form-control" name="payment_type" id="payment_type">	
										<option value=''>Select Payment Type</option>										
										<option value="cash" <?php if($payment_types == 'cash') { echo "Selected"; } ?>>cash</option>
										<option value="card" <?php if($payment_types == 'card') { echo "Selected"; } ?>>card</option>
										<!-- <option value="cod" <?php if($payment_types == 'cod') { echo "Selected"; } ?>>cod</option> -->
										<option value="credit" <?php if($payment_types == 'credit') { echo "Selected"; } ?>>credit</option>
								   </select>
								</div>
								<div class="col-sm-2">
									<span class="pj-form-field-custom pj-form-field-custom-after float_left r5">
										<input type="text" name="from_date" id="from_date" placeholder="From Date" value="<?php echo $from_date1; ?>" class="datepick_acc1 pointer form-control" readonly="readonly" />
									</span>
								</div>
								<div class="col-sm-2">	
									<span class="pj-form-field-custom pj-form-field-custom-after float_left r5">
										<input type="text" name="to_date" id="to_date" placeholder="To Date" value="<?php echo $to_date1; ?>" class="pj-form-field w80 datepick_acc2 pointer form-control" readonly="readonly" />										
									</span>
								</div>
								<div class="col-sm-2">
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
									
									<!-- <a href="manage_sale_orders.php" class="reset btn-default">Reset search</a> -->
								
								<a href="manage_sale_orders.php" style="font-size: 20px;" class="aa-search-btn reset_btn" title="Reset"><i class="fa fa-repeat" ></i></a>
    								<!--<div class="print1" style="float:right;"><input type="button" class="print print_me" value="print" /></div>-->
    								<?php if($receipt_ids != '' || $payment_types != '' || $from_date1 != '' || $to_date1 != '') { ?>
								    <!-- <span title="Print" class="print2" style="font-size: 20px;"><a target="_blank" href="export_to_excel.php?sale=Counter Sale&order_type=counter_sale&get_type=print_r&receipt_id=<?php echo $receipt_ids; ?>&payment_type=<?php echo $payment_types; ?>&user=<?php echo $users; ?>&from_date=<?php echo $from_date1; ?>&to_date=<?php echo $to_date1; ?>" class="print excel_me"><i class="fa fa-print"></i></a></span> -->
								    <?php } else { ?>
    								<!-- <span title="Print" class="print2" style="font-size: 20px;"><a target="_blank" href="export_to_excel.php?sale=Counter Sale&order_type=counter_sale&get_type=print_r" class="print excel_me"><i class="fa fa-print"></i></a></span> -->
    								<?php } ?>
    								<?php if($receipt_ids != '' || $shop != '' || $payment_types != '' || $from_date1 != '' || $to_date1 != '') { ?>
								    <span title="Excel" class="print2" style="font-size: 20px;"><a target="_blank" href="excel_export.php?sale=Website order Sale&order_type=website_order&get_type=excel&receipt_id=<?php echo $receipt_ids; ?>&shop=<?php echo $shop; ?>&payment_type=<?php echo $payment_types; ?>&from_date=<?php echo $from_date1; ?>&to_date=<?php echo $to_date1; ?>" class="print excel_me"><i class="fa fa-file-excel-o"></i></a></span>
								    <?php } else { ?>
    								<span title="Excel" class="print2" style="font-size: 20px;"><a target="_blank" href="excel_export.php?sale=Website order Sale&order_type=website_order&get_type=excel" class="print excel_me"><i class="fa fa-file-excel-o"></i></a></span>
    								<?php } ?>
								</div>
							</div>
							</form>
							<div class="col-xs-12">
								<div class="box">
									<div class="box-header">
										<div class="box-name">
											<i class="fa fa-table"></i>
											<span>Online order Sale List</span>
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
									<div class="box-content no-padding" id="delivery_sale_orders">
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
													<th>Receipt Id</th>													
													<th>Contact Details</th>													
													<th>Shop</th>
													<th>Date&Time</th>
													<th>Payment Type</th>
													<th>Payment Status</th>
													<!-- <th>Status</th> -->
													<th>Total</th>
													<th>Discount</th>
													<th>Final Total</th>													
													<th class="hide_print">Action</th>
												</tr>
											</thead>
											<tbody>
												<?php	$grand_total = "0.00";												
													$prs = getSaleorders($receipt_ids,$payment_types,$from_date1, $to_date1,$shops, $pageLimit, $setLimit);	
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
																$user_name = getUserName($prow->user_id);
																//$name = getManufacturingUnitName($prow->manufacturing_unit_id);
																$shop_name = getShopName($prow->shop_id);
																$total = "0.00";
																$prs2 = getSaleOrderitems($id);
																if($prs2 != false) {
																	$pcount2 = mysqli_num_rows($prs2);
																	if($pcount2 > 0) {
																		for($p2 = 0; $p2 < $pcount2; $p2++) {
																			$prow2 = mysqli_fetch_object($prs2);													
																			$price = $prow2->price;
																			$qty = $prow2->qty;											
																			$total += $price * $qty;
																		}
																	}
																}
																echo "<tr>";
																echo "<td>".$id."</td>";
																echo "<td>".$receipt_id."</td>";
																echo "<td>Name:".$contact_name."<br><br>Address:".$address."<br>Ph:".$contact_number."</td>";	
																echo "<td>".$shop_name."</td>";
																echo "<td>".$ordered_date."</td>";
																echo "<td>".$payment_type."</td>";	
																echo "<td>".$payment_status."</td>";
																//echo "<td>".$status_arr[$status]."</td>";
																echo "<td>".number_format($total, 2)."</td>";
																echo "<td>".$discount."</td>";
																echo "<td>".number_format($total-$discount, 2)."</td>";												
																if($status == 'deleted'){
																echo "<td></td>";/*"<td><a href='javascript:void(0)' onclick='deleteIt($id);'>Delete</a></td>";*/
																}else{
																echo " <td class='hide_print'><a data-toggle='modal' data-target='#myModal".$id."' style='cursor:pointer;'><i class='fa fa fa-eye'></i></a> | <a href='single_item_print.php?id=$id&re=website_order.php&deliver=yes'><i class='fa fa-print'></i></a></td>";
																}
																echo "</tr>";
																$grand_total += $total-$discount;
															}
															echo "<tr>";
															echo "<td colspan='10'>Grand Total</td>";
															echo "<td colspan='2'>".number_format($grand_total, 2)."</td>";
															echo "</tr>";
														}
													}
													else {
														echo "<tr>";
														echo "<td>No Orders found to list.</td>";
														echo "</tr>";
													}
												?>					
											</tbody>
										</table>
									</div>
								</div>
								<?php echo displayPaginationBelows($setLimit, $page, $receipt_ids,$payment_types,$from_date1, $to_date1,$shops); ?>
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

<?php include("sale_order_items.php"); ?>
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
	var content = document.getElementById('delivery_sale_orders').innerHTML;
	var win = window.open();	
	win.document.write('<link href="css/style_v1.css" rel="stylesheet">');
	//win.document.write('<link href="core/framework/libs/pj/css/pj-table.css" rel="stylesheet" type="text/css" />');			
	win.document.write(content);	
	win.print();			
});
$( "#from_date" ).datepicker({dateFormat: 'yy-mm-dd'});
$( "#to_date" ).datepicker({dateFormat: 'yy-mm-dd'});


</script>
</body>
</html>