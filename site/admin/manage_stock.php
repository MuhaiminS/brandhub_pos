<?php 
session_start();
include("../functions.php");
include_once("../config.php");
chkAdminLoggedIn();
connect_dre_db();
//$getUserDetails = getUserDetails($_SESSION['user_id']);
//$getUserDetails = explode(",", $getUserDetails['user_action']);
//if (!in_array('view_stock',$getUserDetails)){
	//redirect('index.php');
//}

$item_id = (isset($_GET['item_id']) && $_GET['item_id'] !='') ? $_GET['item_id'] : '';

if(isset($_GET['act']) && $_GET['act'] != '' && isset($_GET['id']) && $_GET['id'] > 0) {	
	$action = $_GET['act'];
	$cat_id = $_GET['id'];
	if($action == 'delete') {
		$qry="UPDATE item_category SET active = '0' WHERE id = $cat_id";
		if(mysqli_query($GLOBALS['conn'], $qry)){
			redirect('manage_stock.php?resp=succ');
		}
	}
}

if(isset($_POST['stock_post'])) {
	foreach($_POST as $key => $stock) {
		if($key != 'stock_post' && $stock != ''){
			$result = mysqli_query($GLOBALS['conn'], "SELECT stock FROM items WHERE id = '$key'");
			while($row = mysqli_fetch_array($result)) {
				$final_stock = $row['stock']+$stock;
			}
			mysqli_query($GLOBALS['conn'], "UPDATE items SET stock = '$final_stock' WHERE id = '$key'");
		}
	}
	redirect('manage_stock.php?resp=addsucc');
}

function getStockPost($item_id = '')
{
	$qry="SELECT id, name, stock, price FROM items WHERE 1";
	if($item_id){
	    $qry .= " AND id = $item_id";
	}
	$qry .= " AND active != '0' ORDER BY name ASC";
	//echo $qry;
	$result=mysqli_query($GLOBALS['conn'], $qry);
	$num=mysqli_num_rows($result);
	//echo "total result ".$num;
	if($num>0)
	{
		return $result;
	}
	else
	return false;
}

function getitemdet()
{
    $items = array();
	$qry="SELECT id, name, stock, price FROM items WHERE active != '0' ORDER BY name ASC";
	//echo $qry;
	$run = mysqli_query($GLOBALS['conn'], $qry);  
     while ($row = mysqli_fetch_array($run)) {
       $items[$row['id']] = $row['name'];
     }
     return $items;
}
//$category_img_dir = "../category_images/";
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
							<li><a href="javascript:void(0);">Manage Stock</a></li>
						</ol>
					</div>
				</div>
				
				<div class="row">
				    <label class="control-label" style="margin-left:15px;">Search</label>
					<form action="manage_stock.php">
					<input type="hidden" class="form-control" name="page" id="page" value="<?php echo $page; ?>"/>
					<div class="form-group search_val" style="margin-bottom:0px;">	
						
						<div class="col-sm-3">
							<div class="row form-group">												
								<select class="" name="item_id" id="item_id">
									<?php $itemss = getitemdet();
									 foreach($itemss as $key=>$cus) { 
										 $selected = ($key == $item_id) ? "selected = selected" : "";?>
									  <option value="<?php echo $key; ?>" <?php echo $selected; ?>><?php echo $cus; ?></option>
									 <?php } ?>
								</select>
							</div>
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
							.counter-bottom-box {
								bottom: 0px;
								margin-top: 10%;
								position: fixed;
								z-index: 99;
								visibility: visible;
							}
							</style>
							
							<!-- <a href="manage_sale_orders.php" class="reset btn-default">Reset search</a> -->
						
						<a href="manage_stock.php" style="font-size: 20px;" class="aa-search-btn reset_btn" title="Reset"><i class="fa fa-repeat" ></i></a>	
							<?php if($item_id != '') { ?>
							<span title="Excel" class="print2" style="font-size: 20px;"><a target="_blank" href="excel_export_stock.php?sale=Counter Sale&order_type=counter_sale&get_type=excel&item_id=<?php echo $item_id; ?>" class="print excel_me"><i class="fa fa-file-excel-o"></i></a></span>
							<?php } else { ?>
							<span title="Excel" class="print2" style="font-size: 20px;"><a target="_blank" href="excel_export_stock.php?sale=Counter Sale&order_type=counter_sale&get_type=excel" class="print excel_me"><i class="fa fa-file-excel-o"></i></a></span>
							<?php } ?>
							<a class="print print_me" style="font-size: 20px; cursor: pointer;"><i class="fa fa-print"></i></a>
						</div>
					</div>
					</form>
					<div class="col-xs-12">
						<div class="box">
							<div class="box-header">
								<div class="box-name">
									<i class="fa fa-table"></i>
									<span>Manage Stock List</span>
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
											<th>Item name</th>
											<th>Stock</th>
											<?php //if (in_array('edit_stock',$getUserDetails)){ ?>
											<th>New Stock</th>
											<?php //} ?>
											<th>Unit price</th>
											<th>Price</th>
										</tr>
									</thead>
									<form id="pageForm" method="post" action="" class="form-horizontal">
									<input type="hidden" name="stock_post" value="1" />
									<tbody>
										<?php
											$prs = getStockPost($item_id);											
											if($prs != false) {
												$pcount = mysqli_num_rows($prs);
												if($pcount > 0) {
													for($p = 0; $p < $pcount; $p++) {
														$prow = mysqli_fetch_object($prs);
														$cat_id = $prow->id;
														$name = $prow->name;
														$stock = $prow->stock;
														$price = $prow->price;
														echo "<tr>";
														echo "<td>".($p+1)."</td>";
														echo "<td>".safeTextOut($name)."</td>";
														echo "<td>".$stock."</td>";
														//if (in_array('edit_stock',$getUserDetails)){
														echo "<td><input name='$cat_id' type='number' value=''></td>";
														//}
														echo "<td>".number_format((float)$price, 2, '.', '')."</td>";
														echo "<td>".number_format((float)$stock*$price, 2, '.', '')."</td>";
														echo "</tr>";
													}
												}
											}
											else {
												echo "<tr>";
												echo "<td>No Item found to list.</td>";
												echo "</tr>";
											}
										?>						
									</tbody>
									</form>
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
<?php //if (in_array('edit_stock',$getUserDetails)){ ?>
<div class="col-sm-1 col-sm-offset-11">
		<button type="button" id="submit_stock" class="btn btn-primary counter-bottom-box">Submit</button>
</div>
<?php //} ?>
<div class="box-content no-padding" id="counter_sale_orders_export" style=" display:none;" >	
	<table class="table table-striped table-bordered table-hover table-heading no-border-bottom" style="width: 100%;">
		<thead>
			<tr>
				<th>S.no</th>
				<th>Item name</th>
				<th>Stock</th>
				<th>Unit price</th>
				<th>Price</th>
			</tr>
		</thead>
		<tbody style=" background: #fff none repeat scroll 0 0 !important;color: #525252;">
			<?php											
				$prs = getStockPost($item_id);											
				if($prs != false) {
					$pcount = mysqli_num_rows($prs);
					if($pcount > 0) {
						for($p = 0; $p < $pcount; $p++) {
							$prow = mysqli_fetch_object($prs);
							$cat_id = $prow->id;
							$name = $prow->name;
							$stock = $prow->stock;
							$price = $prow->price;																
							echo "<tr>";
							echo "<td>".($p+1)."</td>";
							echo "<td>".$name."</td>";
							echo "<td>".$stock."</td>";
							echo "<td>".number_format((float)$price, 2, '.', '')."</td>";
							echo "<td>".number_format((float)$stock*$price, 2, '.', '')."</td>";
						}
					}
				}
				else {
					echo "<tr>";
					echo "<td>No Stock fond to list.</td>";
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
<script src="plugins/tinymce/tinymce.min.js"></script>
<script src="plugins/tinymce/jquery.tinymce.min.js"></script>
<script src="plugins/bootstrapvalidator/bootstrapValidator.min.js"></script>
<!-- All functions for this theme + document.ready processing -->
<script src="js/devoops.js"></script>
<script src="js/jquery-bizzpro-login.js"></script>
<script type="text/javascript">
var site_url = "<?php echo getServerURL(); ?>";
function deleteIt(id)
{
    if(id && confirm('Are you sure you want to delete this category?'))
    {
        window.location.href = site_url+'/admin/manage_item_category.php?id='+id+'&act=delete';
    }
}

function Select2Tests(){
		$("#item_id").select2();
	}
	$(document).ready(function() {
		// Load script of Select2 and run this
		LoadSelect2Script(Select2Tests);
		WinMove();
	});
	
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

	$( "#submit_stock" ).click(function() {
	  $( "#pageForm" ).submit();
	});
</script>
</body>
</html>