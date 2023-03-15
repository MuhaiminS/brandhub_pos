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
		$qry = "UPDATE expense SET payment_status = 'not_paid' WHERE id = '$purchase_order_id'";
		if(mysqli_query($GLOBALS['conn'], $qry)){
			//UserLogDetails($_SESSION['user_id'], $purchase_order_id, 'purchase_orders', 'paid');
			redirect('manage_expense.php?resp=succ');
		}
	}
	else if($action == 'not_paid') {
		$qry = "UPDATE expense SET payment_status = 'paid' WHERE id = $purchase_order_id";		
		if(mysqli_query($GLOBALS['conn'], $qry)){
			//UserLogDetails($_SESSION['user_id'], $purchase_order_id, 'purchase_orders', 'not_paid');
			redirect('manage_expense.php?resp=succ');
		}
	}
	else if($action == 'delete') {
		$qry = "DELETE FROM expense WHERE id = '$purchase_order_id'";
		if(mysqli_query($GLOBALS['conn'], $qry)){
			//UserLogDetails($_SESSION['user_id'], $purchase_order_id, 'purchase_orders', 'paid');
			redirect('manage_expense.php?resp=succ');
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

function getExpenseCategory($expense_category_id)
{
	$where = "WHERE id = '$expense_category_id'";
	$value = getnamewhere('expense_category', 'expense_name', $where);
	return $value;
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
	$qry="SELECT * FROM expense WHERE 1";
	
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
	$qry .=" ORDER BY id ASC LIMIT $pageLimit, $setLimit";
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
	$query = "SELECT COUNT(*) as totalCount FROM expense WHERE 1 ";
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

function getExpenseCategoryList()
   {
   	$service = array();
   	$query="SELECT * FROM ".DB_PRIFIX."expense_category ORDER BY id ASC";
   	$run = mysqli_query($GLOBALS['conn'], $query);
   	while($row = mysqli_fetch_array($run)) {
   		$expense_id = $row['id'];
   		$service[$expense_id]['expense_id'] = $row['id'];
   		$service[$expense_id]['expense_name'] = $row['expense_name'];
   	}
   	return $service;	
   
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
							<li><a href="javascript:void(0);">Manage Expense
							</a></li>
						</ol>
					</div>
				</div>
				<div>
					
				</div>
<?php //include("purchase_order_items.php"); ?>
				  <div class="tab-content">					
					<div id="delivery_sale" class="tab-pane fade in active">
						<div class="row">			
							<label class="control-label" style="margin-left:15px;">Search</label>
							<form action="manage_expense.php">
							<input type="hidden" class="form-control" name="page" id="page" value="<?php echo $page; ?>"/>
							<div class="form-group search_val" style="margin-bottom:0px;">	
								<div class="col-sm-2">					
									<input type="text" class="form-control" name="reference_id" id="reference_id" placeholder="Invoice No" value="<?php echo $reference_ids; ?>"/>					
									<span id="loader"></span>
								</div>	
								<div class="col-sm-2">					
									<select class="form-control" name="payment_status" id="payment_status">	
										<option value=''>Select Payment Status</option>										
										<option value="paid" <?php if($payment_status == 'paid') { echo "Selected"; } ?>>Paid</option>
										<option value="not_paid" <?php if($payment_status == 'not_paid') { echo "Selected"; } ?>>Not Paid</option>					
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
									
									<!-- <a href="manage_purchase_orders.php" class="reset btn-default">Reset search</a> -->
								
								<a href="manage_expense.php" style="font-size: 20px;" class="aa-search-btn reset_btn" title="Reset"><i class="fa fa-repeat" ></i></a>
    								<span title="Excel" class="print2" style="font-size: 20px;"><a target="_blank" href="excel_export_expense.php?reference_id=<?php echo $reference_ids; ?>&payment_status=<?php echo $payment_status; ?>&from_date=<?php echo $from_date1; ?>&to_date=<?php echo $to_date1; ?>" class="print excel_me"><i class="fa fa-file-excel-o"></i></a></span>  
								</div>
							</div>
							</form>
							<div class="col-xs-12">
								<div class="box">
									<div class="box-header">
										<div class="box-name">
											<i class="fa fa-table"></i>
											<span>Expense Order List</span>
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
									<div class="box-content no-padding" id="delivery_purchase_order">
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
													<th>Invoice No</th>	
													<th>TRN No's</th>
													<th>Date</th>
													<th>Company name</th>
													<th>Description</th>
													<th>Payment Status</th>
													<th>Sub Total</th>
													<th>VAT Amount</th>
													<th>Net Total</th>																					
													<th class="hide_print">Action</th>
												</tr>
											</thead>
											<tbody>
												<?php	$vat_total1 = $net_total1 = $sub_total1 = "0.00";										
													$prs = getSaleorders($reference_ids,$payment_status,$from_date1, $to_date1,$shops, $pageLimit, $setLimit);	
													if($prs != false) {
														$pcount = mysqli_num_rows($prs);
														if($pcount > 0) {
															for($p = 0; $p < $pcount; $p++) {
																$prow = mysqli_fetch_object($prs);
																$id = $prow->id;
																$reference_id = $prow->reference_id;
																$trn_no = $prow->trn_no;
																$company_name = $prow->company_name;
																$payment_status = $prow->payment_status;
																$purchase_date = $prow->purchase_date;
																$description = $prow->description;
																$purchase_date = $prow->purchase_date;
																$sub_total = $prow->sub_total;
																$vat_total = $prow->vat_amount;
																$net_total = $prow->net_total;
																$expense_category_id = $prow->expense_category_id;
																$description = $prow->description;
																$payment_status = ($prow->payment_status == 'paid') ? 'Paid' : 'Not Paid';
																$rev_payment_status = ($prow->payment_status) ? 'not_paid' : 'paid';
																
																$sub_total1 += $sub_total;
																$vat_total1 += $vat_total;
																$net_total1 += $net_total;
																$s_no = $p+1;
																echo "<tr>";
																echo "<td>".$id."</td>";
																echo "<td>".$reference_id."</td>";	
																echo "<td>".$trn_no."</td>";
																echo "<td>".$purchase_date."</td>";
																//echo "<td>".$expense_category_id)."</td>";
																echo "<td>".$company_name."</td>";
																echo "<td>".$description."</td>";
																
																//if($prow->status == 'pending') {
																	//echo "<td><a href='javascript:void(0)' onclick='changeStatus(\"$rev_payment_status\", \"$id\");'>".ucfirst($status)."</a></td>";
																
															if($prow->payment_status == 'not_paid') {
																	echo "<td><a href='javascript:void(0)' onclick='changePaymentStatus(\"$rev_payment_status\", \"$id\");'>".$payment_status."</a></td>";
																} else {
																	echo "<td>".$payment_status."</td>";
																}
																echo "<td>".$sub_total."</td>";
																echo "<td>".$vat_total."</td>";
																echo "<td>".$net_total."</td>";
																echo " <td class='hide_print'><a href='add_expense.php?id=".$id."&act=edit'>Edit</a> | <a href='javascript:void(0)' onclick='deleteIt($id);'>Delete</a></td>";
																echo "</tr>";																
															}
															echo "<tr>";
															echo "<td colspan='7'>Final balance</td>";
															echo "<td>".number_format($sub_total1, 2)."</td>";
															echo "<td>".number_format($vat_total1, 2)."</td>";
															echo "<td>".number_format($net_total1, 2)."</td>";
															echo "</tr>";
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
								</div>
								<?php echo displayPaginationBelows($setLimit, $page, $reference_ids,$payment_status,$from_date1, $to_date1,$shops); ?>
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
    if(id && confirm('Are you sure you want to delete this Expense?'))
    {
        window.location.href = site_url+'/admin/manage_expense.php?id='+id+'&act=delete';
    }
}
function changePaymentStatus(status, id)
{
	var msg = 'Are you sure you want to change the status for paid?';
	if(status == 'deactivate')
		msg = 'Are you sure you want to change the status for not paid?';
    if(id && confirm(msg))
    {
        window.location.href = site_url+'/admin/manage_expense.php?id='+id+'&act='+status;
    }
}
function changeStatus(val, id)
{
	var status = $(val).val();
	var msg = 'Are you sure you want to change the status for '+status+'?';
    if(id && confirm(msg))
    {
        window.location.href = site_url+'/admin/manage_expense.php?id='+id+'&act='+status+'&s=1';
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
$( "#from_date" ).datepicker({dateFormat: 'yy-mm-dd'});
$( "#to_date" ).datepicker({dateFormat: 'yy-mm-dd'});


</script>
</body>
</html>