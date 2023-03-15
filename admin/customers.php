<?php 
	session_start();
	include("../functions.php");
	include_once("../config.php");
	chkAdminLoggedIn();
	connect_dre_db();
	//$customer_id = (isset($_GET['customer_id']) && $_GET['customer_id'] !='') ? $_GET['customer_id'] : '';
	if(isset($_GET['act']) && $_GET['act'] != '' && isset($_GET['customer_id']) && $_GET['customer_id'] > 0) {	
		$action = $_GET['act'];
		$customer_id = $_GET['customer_id'];
		if($action == 'delete') {
			$qry="DELETE FROM customer_details WHERE customer_id = $customer_id";
			if(mysqli_query($GLOBALS['conn'], $qry)){
				redirect('customers.php?resp=deletesucc');
			}
		}
	}
	
	function getCustomerDetails()
	{
		$qry="SELECT * FROM customer_details ORDER BY customer_id DESC";
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
	function getCustomerOrders($customer_id)
	{
		//$date = date('Y-m-d');
		$qry="SELECT  DATE_FORMAT(so.ordered_date,'%d/%m/%Y') as order_date, SUM((soi.qty*soi.price) * 0.05) as total_vat,SUM(soi.qty*soi.price) as amount FROM sale_order_items as soi LEFT JOIN sale_orders as so ON (so.id = soi.sale_order_id) WHERE so.customer_id = '$customer_id' AND so.customer_id != '0'"; 		
		
		$qry .=" GROUP BY order_date ORDER BY order_date ASC";
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
						Customers
						<!--<small>Optional description</small>-->
					</h1>
					<ol class="breadcrumb">
						<li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
						<li class="active">Customers</li>
					</ol>
				</section>
				<!-- Main content -->
				<section class="content container-fluid">
					<div class="row">
						<div class="col-xs-12">
							<div class="box">
								<div class="box-header">
									<h3 class="box-title">Manage Customers</h3>
								</div>
								<!-- /.box-header -->
								<?php include("customer_details_modal.php"); ?>
								<div class="box-body">
									
									<?php include("common/info.php"); ?>

									<table id="example2" class="table table-bordered table-hover">
										<thead>
											<tr>
												<th>#</th>
												<th>Customer Number</th>
												<th>Customer Name</th>
												<th>Customer Address</th>
												<th>Customer Email</th>
												<th>Action</th>
											</tr>
										</thead>
										<tbody>
											<?php
												$prs = getCustomerDetails();											
												if($prs != false) {
													$pcount = mysqli_num_rows($prs);
													if($pcount > 0) {
														for($p = 0; $p < $pcount; $p++) {
															$prow = mysqli_fetch_object($prs);
															$customer_id = $prow->customer_id;
															$customer_name = $prow->customer_name;
															$customer_number = $prow->customer_number;
															$customer_address = $prow->customer_address;
															$customer_email = $prow->customer_email;?>
											<tr>
												<td><?php echo $customer_id; ?></td>
												<td><?php echo $customer_number; ?></td>
												<td><?php echo $customer_name; ?></td>
												<td><?php echo $customer_address; ?></td>
												<td><?php echo $customer_email; ?></td>
												<td>
													<div class="text-center">
														<div class="btn-group">
															<a href="javascript:void(0)" title="View '<?php echo $customer_name; ?>' Order List" class="tip btn btn-primary btn-xs" data-toggle="modal" data-target='#myModal<?php echo $customer_id;?>'><i class="fa fa-file-text-o"></i></a>
															<!--  <a class="tip image btn btn-primary btn-xs" id="Milkshajr (123456789)" href="#" title="View Image"><i class="fa fa-picture-o"></i></a>-->
															<a href="customers_add.php?customer_id=<?php echo $customer_id ?>&act=edit" title="Edit Customer" class="tip btn btn-warning btn-xs"><i class="fa fa-edit"></i></a>
															<a href="javascript:void(0)" data-toggle="modal" data-target="#delete<?php echo $prow->customer_id; ?>" title="Delete table" class="tip btn btn-danger btn-xs"><i class="fa fa-trash-o"></i></a>
														</div>
													</div>
												</td>
											</tr>
											<div class="modal fade in" id="delete<?php echo $prow->customer_id; ?>" >
												<div class="modal-dialog">
													<div class="modal-content">
														<div class="modal-header">
															<button type="button" class="close" data-dismiss="modal" aria-label="Close">
															<span aria-hidden="true">×</span></button>
															<h4 class="modal-title">Customer Delete</h4>
														</div>
														<div class="modal-body text-center">
															<h2 class="text-danger">Are You Sure Want to Delete ?</h2>
															<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get">
																<input type="hidden" name="customer_id" value="<?php echo $prow->customer_id; ?>">
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
												}
												else {
												echo "<tr>";
												echo "<td>No categories found to list.</td>";
												echo "</tr>";
												}?>
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
		<script type="text/javascript">
			var site_url = "<?php echo getServerURL(); ?>";
			function deleteIt(customer_id)
			{
			    if(customer_id && confirm('Are you sure you want to delete this customer?'))
			    {
			        window.location.href = site_url+'/admin/customers.php?customer_id='+customer_id+'&act=delete';
			    }
			}
		</script>
		<!-- Optionally, you can add Slimscroll and FastClick plugins.
			Both of these plugins are recommended to enhance the
			user experience. -->
	</body>
</html>