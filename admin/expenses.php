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
			redirect('expenses.php?resp=paidsucc');
		}
	}
	else if($action == 'not_paid') {
		$qry = "UPDATE expense SET payment_status = 'paid' WHERE id = $purchase_order_id";		
		if(mysqli_query($GLOBALS['conn'], $qry)){
			//UserLogDetails($_SESSION['user_id'], $purchase_order_id, 'purchase_orders', 'not_paid');
			redirect('expenses.php?resp=notpaid');
		}
	}
	else if($action == 'delete') {
		$qry = "DELETE FROM expense WHERE id = '$purchase_order_id'";
		if(mysqli_query($GLOBALS['conn'], $qry)){
			//UserLogDetails($_SESSION['user_id'], $purchase_order_id, 'purchase_orders', 'paid');
			redirect('expenses.php?resp=deletesucc');
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
        Expenses
        <!--<small>Optional description</small>-->
      </h1>
      <ol class="breadcrumb">
        <li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Expenses</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content container-fluid">

      <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-header">
              <h3 class="box-title">Manage Expenses</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">

			  <div id="form" class="panel panel-warning" style="display: block;">
				<div class="panel-body">
					<?php include("common/info.php"); ?>
					<form action="expenses.php" accept-charset="utf-8">
						<div class="row">
							<div class="col-sm-3">
								<div class="form-group">
									<label for="reference_id">Invoice no.</label> <input type="text" name="reference_id" value="<?php echo $reference_ids; ?>" class="form-control tip" id="reference_id">
								</div>
							</div>
							<div class="col-sm-3">
								<div class="form-group">
									<label>Payment Status</label>
									<select name="payment_status" id="payment_status" class="form-control select2" style="width: 100%;">
										<option value=''>--Select Status--</option>										
										<option value="paid" <?php if($payment_status == 'paid') { echo "Selected"; } ?>>Paid</option>
										<option value="not_paid" <?php if($payment_status == 'not_paid') { echo "Selected"; } ?>>Not Paid</option>
									</select>
								</div>
							</div>
							<div class="col-sm-3">
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
							<div class="col-sm-3">
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
								<a href="purchases.php" class="btn btn-default">Reset</a>
							</div>
							<div class="row">
										<div class="col-md-6"></div>
										<div class="col-md-6 text-right pr0">
											<div class="dt-buttons btn-group">
												<!--<a class="btn btn-default buttons-print buttons-html5 print_me" tabindex="0" aria-controls="SLData" href="#"><span>Print</span></a>
												<a class="btn btn-default buttons-copy buttons-html5" tabindex="0" aria-controls="SLData" href="#"><span>Copy</span></a>
												<a class="btn btn-default buttons-excel buttons-html5" tabindex="0" aria-controls="SLData" href="#"><span>Excel</span></a>-->
												<a target="_blank"  href="excel_export_expense.php?sale=Counter Sale&order_type=counter_sale&get_type=excel&from_date=<?php echo $from_date1; ?>&to_date=<?php echo $to_date1; ?>" class="btn btn-default buttons-csv buttons-html5 export" tabindex="0" aria-controls="SLData" href="#"><span>Excel</span></a>
												<!--<a class="btn btn-default buttons-pdf buttons-html5" tabindex="0" aria-controls="SLData" href="#"><span>PDF</span></a>
												<a class="btn btn-default buttons-collection buttons-colvis" tabindex="0" aria-controls="SLData" href="#"><span>Columns</span></a>-->
											</div>
										</div>
									</div>
						</div>
					</form>
				</div>
			  </div>				

              <table id="example2" class="table table-bordered table-hover">
                <thead>
                <tr>
                  <th>#</th>
                  <th>Date</th>
                  <th>Invoice</th>
                  <th>TRN</th>
                  <th>Category</th>
                  <th>Company</th>
                  <!--<th>Desc</th>-->
                  <th>Pay Status</th>
                  <th>Total</th>
                  <th>VAT</th>				  
                  <th>Net Total</th>
                  <th>Action</th>
                </tr>
                </thead>
                <tbody>
                <tr>
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
									echo "<td>".date("d-m-Y",strtotime($purchase_date))."</td>";
									echo "<td>".$reference_id."</td>";	
									echo "<td>".$trn_no."</td>";
									echo "<td>".getExpenseCategory($expense_category_id)."</td>";
									echo "<td>".$company_name."</td>";
									//echo "<td>".$description."</td>";
																		
									if($prow->payment_status == 'not_paid') {
										echo "<td><a href='javascript:void(0)' onclick='changePaymentStatus(\"$rev_payment_status\", \"$id\");'>".$payment_status."</a></td>";
									} else {
										echo "<td><span class=\"label label-success\">".$payment_status."</span></td>";
									}
									echo "<td align=\"right\">".number_format($sub_total, 2)."</td>";
									echo "<td align=\"right\">".number_format($vat_total, 2)."</td>";
									echo "<td align=\"right\">".number_format($net_total, 2)."</td>";
									echo "<td class='hide_print'>";
										echo "<div class=\"text-center\">";
											echo "<div class=\"btn-group\">";
												echo "<a href=\"expenses_add.php?id=".$id."&act=edit\" title=\"Edit Expense\" class=\"tip btn btn-warning btn-xs\"><i class=\"fa fa-edit\"></i></a>";
												//echo "<a href=\"javascript:void(0)\" onclick=\"deleteIt($id);\" title=\"Delete Expense\" class=\"tip btn btn-danger btn-xs\"><i class=\"fa fa-trash-o\"></i></a>"; ?>
												<a href="javascript:void(0)" data-toggle="modal" data-target="#delete<?php echo $prow->id; ?>" title="Delete Expense" class="tip btn btn-danger btn-xs"><i class="fa fa-trash-o"></i></a>
												<?php
											echo "</div>";
										echo "</div>";
									echo "</td>";
									echo "</tr>";																
								}
								echo "<tr>";
								echo "<td colspan='7'>Final balance</td>";
								echo "<td align=\"right\">".number_format($sub_total1, 2)."</td>";
								echo "<td align=\"right\">".number_format($vat_total1, 2)."</td>";
								echo "<td align=\"right\">".number_format($net_total1, 2)."</td>";
								echo "<td></td>";
								echo "</tr>"; ?>
								<div class="modal fade in" id="delete<?php echo $prow->id; ?>" >
								<div class="modal-dialog">
									<div class="modal-content">
										<div class="modal-header">
											<button type="button" class="close" data-dismiss="modal" aria-label="Close">
											<span aria-hidden="true">×</span></button>
											<h4 class="modal-title">Expense Delete</h4>
										</div>
										<div class="modal-body text-center">
											<h2 class="text-danger">Are You Sure Want to Delete ?</h2>
											<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get">
												<input type="hidden" name="id" value="<?php echo $prow->id; ?>">
												<input type="hidden" name="act" value="delete">
												<br>
												<button type="submit" class="btn btn-danger ">YES</button>
												<button type="button" class="btn btn-default " data-dismiss="modal">NO</button>
											</form>
										</div>
									</div>
									<!-- /.modal-content -->
								</div>
								<!-- /.modal-dialog -->
							</div>
							<?php
							}
						}
						else {
							echo "<tr>";
							echo "<td>No Purchase Orders found to list.</td>";
							echo "</tr>";
						}
					?>
                </tr>                             
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
<script>
	function deleteIt(id)
{
    if(id && confirm('Are you sure you want to delete this Expense?'))
    {
        window.location.href = site_url+'/admin/expenses.php?id='+id+'&act=delete';
    }
}
function changePaymentStatus(status, id)
{
	var msg = 'Are you sure you want to change the status for paid?';
	if(status == 'deactivate')
		msg = 'Are you sure you want to change the status for not paid?';
    if(id && confirm(msg))
    {
        window.location.href = site_url+'admin/expenses.php?id='+id+'&act='+status;
    }
}
function changeStatus(val, id)
{
	var status = $(val).val();
	var msg = 'Are you sure you want to change the status for '+status+'?';
    if(id && confirm(msg))
    {
        window.location.href = site_url+'admin/expenses.php?id='+id+'&act='+status+'&s=1';
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