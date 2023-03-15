<?php 
	session_start();
	include("../functions.php");
	include_once("../config.php");
	chkAdminLoggedIn();
	connect_dre_db();
	//$getUserDetails = getUserDetails($_SESSION['user_id']);
	//$getUserDetails = explode(",", $getUserDetails['user_action']);
	//print_r($getUserDetails); DIE;
	/* if (!in_array('view_stock',$getUserDetails)){
		redirect('index.php');
	} */
$item_id = (isset($_GET['item_id']) && $_GET['item_id'] !='') ? $_GET['item_id'] : '';

if(isset($_GET['act']) && $_GET['act'] != '' && isset($_GET['id']) && $_GET['id'] > 0) {	
	$action = $_GET['act'];
	$cat_id = $_GET['id'];
	if($action == 'delete') {
		$qry="UPDATE item_category SET active = '0' WHERE id = $cat_id";
		if(mysqli_query($GLOBALS['conn'], $qry)){
			redirect('stock_product.php?resp=succ');
		}
	}
}

if(isset($_POST['stock_post'])) {
	foreach($_POST as $key => $stock) {
		$final_stock = 0;
		if($key != 'stock_post' && $stock != ''){
			$result = mysqli_query($GLOBALS['conn'], "SELECT stock FROM ".DB_PRIFIX."items WHERE id = '$key'");
			while($row = mysqli_fetch_array($result)) {
				$final_stock = $row['stock']+$stock;
			}
			mysqli_query($GLOBALS['conn'], "UPDATE ".DB_PRIFIX."items SET stock = '$final_stock' WHERE id = '$key'");
		}
	}//die;
	redirect('stock_product.php?resp=addsucc');
}

function getStockPost($item_id = '')
{
	$qry="SELECT id, name, stock, price, cost_price FROM ".DB_PRIFIX."items WHERE 1";
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
	$qry="SELECT id, name, stock, price, cost_price FROM ".DB_PRIFIX."items WHERE active != '0' ORDER BY name ASC";
	//echo $qry;
	$run = mysqli_query($GLOBALS['conn'], $qry);  
     while ($row = mysqli_fetch_array($run)) {
       $items[$row['id']] = $row['name'];
     }
     return $items;
}

function getItemName($item_id)
{
	$where = "WHERE id = '$item_id'";
	$service = getnamewhere('Items', 'name', $where);
	return $service;
}
?>
<!DOCTYPE html>
<!--
	This is a starter template page. Use this page to start your new project from
	scratch. This page gets rid of all links and provides the needed markup only.
	-->
<html>
	<head>
	 <style type='text/css'>
		/*@media print
		{
			.print_display {display:none !important;}
		}*/
		
		</style>
		<?php include("common/header.php"); ?>     
		<?php include("common/header-scripts.php"); ?>
		<style>.counter-bottom-box { bottom: 0px; margin-top: 10%; position: fixed; z-index: 99; visibility: visible; margin-left:60em;} </style>
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
						Stock
						<!--<small>Optional description</small>-->
					</h1>
					<ol class="breadcrumb">
						<li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
						<li class="active">Stock</li>
					</ol>
				</section>
				<!-- Main content -->
				<section class="content container-fluid">
					<div class="row">
						<div class="col-xs-12">
							<div class="box">
								<div class="box-header">
									<!-- <a href="#" class="btn btn-default btn-sm toggle_form pull-right">Show/Hide Form</a> -->
									<h3 class="box-title">Manage Stock</h3>
								</div>
								<!-- /.box-header -->
								<div class="box-body">
									<?php include("common/info.php"); ?>
									
									<div id="form" class="panel panel-warning" style="display: block;">
										<div class="panel-body">
											<form action="stock_product.php" accept-charset="utf-8">
												<div class="row">
													<div class="col-sm-4">
														<div class="form-group">
															<label for="name">Product name</label> 
															<select class="form-control select2" name="item_id" id="item_id">
																<?php $itemss = getitemdet();
																 foreach($itemss as $key=>$cus) { 
																	 $selected = ($key == $item_id) ? "selected = selected" : "";?>
																  <option value="<?php echo $key; ?>" <?php echo $selected; ?>><?php echo $cus; ?></option>
																 <?php } ?>
															</select>
														</div>
													</div>
													<div class="col-sm-12">
														<button type="submit" class="btn btn-primary">Submit</button>
														<a href="stock_product.php" class="btn btn-default">Reset</a>
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
											</div>
										</div>
									</div>									
									<form action="stock_product.php"  method="post">											
									<table id="example2" class="table table-bordered table-hover">
										<thead>
										<tr>
											<th>#</th>
											<th>Item name</th>
											<th>Stock</th>
											<?php //if (in_array('edit_stock',$getUserDetails)){ ?>
											<th>New Stock</th>
											<?php //} ?>
											<th>Selling price</th>
											<th>Cost price</th>
											<th>Total Cost Price</th>
										</tr>
									</thead>
									<!-- <form id="pageForm" method="post" action="" class="form-horizontal"> -->
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
														//$`min_stock = $prow->min_stock;
														$price = $prow->price;
														$cost_price = $prow->cost_price;
														echo "<tr>";
														//if($stock < $min_stock) { echo " style='background:red; color:#fff;' "; }
														//echo " >";
														echo "<td>".($p+1)."</td>";
														echo "<td>".safeTextOut($name)."</td>";
														echo "<td>".$stock."</td>";
														//if (in_array('edit_stock',$getUserDetails)){
														echo "<td><input name='$cat_id' type='number' value=''></td>";
														//}
														echo "<td>".number_format((float)$price, 2, '.', '')."</td>";
														echo "<td>".number_format((float)$cost_price, 2, '.', '')."</td>";
														echo "<td>".number_format((float)$stock*$cost_price, 2, '.', '')."</td>";
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
										<tfoot>
										<tr>											
											<td colspan=7 ><input type="submit" class="btn btn-primary counter-bottom-box" value='Stock Update' /> </td>
										</tr>
										</tfoot>
									</table>
									</form>								
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
<?php //if (in_array('edit_stock',$getUserDetails)){ ?>

<div id='stock_prodct' style="display:none;">
<table>
	   <tr><td>Stock Product</td></tr>	
	   <tr><td>Product Name: <?php echo getItemName($item_id); ?></td></tr>
</table>
<table id="example2" class="table" border="1">
	<thead>
		<tr>
			<th>#</th>
			<th>Item name</th>
			<th>Stock</th>											
			<th>Selling price</th>
			<th>Cost price</th>
			<th>Total Cost Price</th>
		</tr>

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
						//$`min_stock = $prow->min_stock;
						$price = $prow->price;
						$cost_price = $prow->cost_price;
						echo "<tr>";
						//if($stock < $min_stock) { echo " style='background:red; color:#fff;' "; }
						//echo " >";
						echo "<td>".($p+1)."</td>";
						echo "<td>".safeTextOut($name)."</td>";
						echo "<td>".$stock."</td>";														
						echo "<td>".number_format((float)$price, 2, '.', '')."</td>";
						echo "<td>".number_format((float)$cost_price, 2, '.', '')."</td>";
						echo "<td>".number_format((float)$stock*$cost_price, 2, '.', '')."</td>";
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
</table>

</div>
<?php include("common/footer.php"); ?>

<?php include("common/sidebar-right.php"); ?>
</div>
<!-- ./wrapper -->

<!-- REQUIRED JS SCRIPTS -->

<?php include("common/footer-scripts.php"); ?>

<script type="text/javascript">

var site_url = "<?php echo getServerURL(); ?>";
function deleteIt(id)
{
    if(id && confirm('Are you sure you want to delete this Item?'))
    {
        window.location.href = site_url+'/admin/products.php?id='+id+'&act=delete';
    }
}
</script>
<script>
	 $('.stock_update').on('click', function() {
	 var product_id = $(this).attr('id')
	 var form_data = $('#stock_update_'+product_id).serialize();
	 $.ajax({
		url: 'stock_update.php',
		type: 'post',
		dataType: 'json',
		data: form_data,
		success: function(json) {
			alert("updated!");
			location.reload();			
		}
	 });
	 });
	 $(document).on('click', '.print_me', function(e) {
			//alert(123);
		$(".show_titles").show();
		$('.display_print').hide();
		var content = document.getElementById('stock_prodct').innerHTML;		
		var win = window.open();	
		//win.document.write('<link href="css/style_v1.css" rel="stylesheet">');
		//win.document.write('<link href="core/framework/libs/pj/css/pj-table.css" rel="stylesheet" type="text/css" />');			
		win.document.write(content);		
		win.print();		
		win.window.close();
	$('.display_print').hide();
	});

	 $( "#submit_stock" ).click(function() {
	  $( "#pageForm" ).submit();
	});
	
</script>
	</body>
</html>