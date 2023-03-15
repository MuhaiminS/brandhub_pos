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
		//$qry="DELETE FROM items WHERE id = $id";
		$qry = "UPDATE items SET active = '0' WHERE id = $id";
		if(mysqli_query($GLOBALS['conn'], $qry)){
			redirect('manage_items.php?resp=succ');
		}
	}
}

function getcategoryName($category_id)
{
	$where = "WHERE id = '$category_id'";
	$category = getnamewhere('category', 'category_title', $where);
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

if(isset($_GET["page"])) {
	$page = (int)$_GET["page"];
} else {
	$page = 1;
}
$setLimit = 15;
$pageLimit = ($page * $setLimit) - $setLimit;

$to_date1 = (date('Y-m-d'));
$from_date1 = (date('Y-m-d', strtotime('-7 days')));

$from_date = (isset($_GET['from_date']) && $_GET['from_date'] !='') ? $_GET['from_date'] : $from_date1;
$to_date = (isset($_GET['to_date']) && $_GET['to_date'] !='') ? $_GET['to_date'] : $to_date1;



function getItemsPost($from_date, $to_date)
{	
	$qry="SELECT *, COUNT(*) as count FROM sale_orders as so LEFT JOIN sale_order_items as soi ON (soi.sale_order_id = so.id)";

	if($from_date != '' && $to_date != '' ) {
		
		$qry .= " AND so.ordered_date BETWEEN '$from_date 00:00:00' AND '$to_date 23:59:59' ";		
	} 
	if($from_date != '' && $to_date == '' ) {
		$qry .= " AND so.ordered_date BETWEEN '$from_date 00:00:00' AND '$date 23:59:59' ";
	}

	if($from_date == '' && $to_date != '' ) {
		$qry .= " AND so.ordered_date <= '$to_date 23:59:59'";
	}
	$qry .=" GROUP BY soi.item_id ORDER BY count DESC LIMIT 30";
	//echo $qry;
	//die;
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
$items_img_dir = "../item_images/";

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
							<li><a href="javascript:void(0);">Manage Products</a></li>
						</ol>
					</div>
				</div>
				<?php
				
				?>
				<div class="row">
					<label class="control-label" style="margin-left:15px;">Search</label>
					<form action="fast_moving.php">
					<input type="hidden" class="form-control" name="page" id="page" value="<?php echo $page; ?>"/>
					<div class="form-group" style="margin-bottom:0px;">	
						<div class="col-sm-2">
							<span class="pj-form-field-custom pj-form-field-custom-after float_left r5">
								<input type="text" name="from_date" id="from_date" placeholder="From Date" value="<?php echo $from_date; ?>" class="datepick_acc1 pointer form-control" readonly="readonly" />
							</span>
						</div>
						<div class="col-sm-2">	
							<span class="pj-form-field-custom pj-form-field-custom-after float_left r5">
								<input type="text" name="to_date" id="to_date" placeholder="To Date" value="<?php echo $to_date; ?>" class="pj-form-field w80 datepick_acc2 pointer form-control" readonly="readonly" />										
							</span>
						</div>
						<div class="col-sm-3">
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
							<a href="manage_items.php" class="reset btn-default">Reset search</a>
						</div>						
					</div>
					</form>
					<div class="col-xs-12">
						<div class="box">
							<div class="box-header">
								<div class="box-name">
									<i class="fa fa-table"></i>
									<span>Manage Products List</span>
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
							<div class="box-content no-padding">
								<table class="table table-striped table-bordered table-hover table-heading no-border-bottom">
									<thead>
										<tr>
											<th>#</th>
											<th>Product Name</th>
											<th>Sale Count</th>
										</tr>
									</thead>
									<tbody>
										<?php
											$prs = getItemsPost($from_date, $to_date);										
											if($prs != false) {
												$pcount = mysqli_num_rows($prs);
												if($pcount > 0) {
													for($p = 0; $p < $pcount; $p++) {
														$prow = mysqli_fetch_object($prs);														
														$id = $prow->id;
														//$category_title = $prow->category_title;
														$item_name = $prow->item_name;
														$count = $prow->count;
														//$category = getcategoryName($cat_id);
														echo "<tr>";														
														echo "<td>".$id."</td>";
														//echo "<td>".$category_title."</td>";
														echo "<td>".safeTextOut($item_name)."</td>";
														echo "<td>".$count."</td>";
														echo "</tr>";
													}
												}
											}
											else {
												echo "<tr>";
												echo "<td>No Items found to list.</td>";
												echo "</tr>";
											}
										?>						
									</tbody>
								</table>
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
<script src="plugins/tinymce/tinymce.min.js"></script>
<script src="plugins/tinymce/jquery.tinymce.min.js"></script>
<script src="plugins/bootstrapvalidator/bootstrapValidator.min.js"></script>
<!-- All functions for this theme + document.ready processing -->
<script src="js/devoops.js"></script>
<script>
$( "#from_date" ).datepicker({dateFormat: 'yy-mm-dd'});
$( "#to_date" ).datepicker({dateFormat: 'yy-mm-dd'});
</script>
</body>
</html>