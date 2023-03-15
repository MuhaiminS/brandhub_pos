<?php 
	session_start();
	include("../functions.php");
	include_once("../config.php");
	chkAdminLoggedIn();
	connect_dre_db();
	$customer_id = '';
	$customer_number = '';
	$customer_name = '';
	$customer_address = '';
	$customer_email = '';
	$action = 'add';
	
	
	if(isset($_POST['customer_post'])) {
		//$parent_id = $_POST['parent_id'];
		$customer_number = $_POST['customer_number'];
		$customer_name = $_POST['customer_name'];
		$customer_address = $_POST['customer_address'];
		$customer_email = $_POST['customer_email'];
		//print_r($_POST);die;
		
		if(isset($_POST['customer_id']) && $_POST['customer_id'] > 0) 
		{
			$customer_id = $_POST['customer_id'];
			$qry = "UPDATE customer_details SET customer_number = '".safeTextIn($customer_number)."',customer_name = '".safeTextIn($customer_name)."',customer_address = '".safeTextIn($customer_address)."',customer_email = '".safeTextIn($customer_email)."' WHERE customer_id = '$customer_id'";
			if(mysqli_query($GLOBALS['conn'], $qry)){
			}
			redirect('customers.php?resp=updatesucc');
		}
		else {
			$qry = "INSERT INTO customer_details (customer_number,customer_name,customer_address,customer_email) VALUES ('$customer_number', '$customer_name', '$customer_address', '$customer_email')";
			//echo $qry;die;
			if(mysqli_query($GLOBALS['conn'], $qry)){		
				$customer_id = mysqli_insert_id($GLOBALS['conn']);
			}
			redirect('customers.php?resp=addsucc');
		}	
		
	}
	
	if(isset($_GET['act']) && $_GET['act'] != '' && isset($_GET['customer_id']) && $_GET['customer_id'] > 0) {	
		$action = $_GET['act'];
		$customer_id = $_GET['customer_id'];
		if($action == 'edit') 
		{
			$edit_query = "SELECT * FROM customer_details WHERE customer_id = '$customer_id'";
			$run_edit = mysqli_query($GLOBALS['conn'], $edit_query);		
			while ($edit_row = mysqli_fetch_array($run_edit)) {
				$customer_id = $edit_row['customer_id'];
				$customer_number = $edit_row['customer_number'];
				$customer_name = $edit_row['customer_name'];
				$customer_address = $edit_row['customer_address'];
				$customer_email = $edit_row['customer_email'];
			}
		}
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
						Add Customer
						<!--<small>Optional description</small>-->
					</h1>
					<ol class="breadcrumb">
						<li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
						<li><a href="customers.php">Customers</a></li>
						<li class="active">Add Customer</li>
					</ol>
				</section>
				<!-- Main content -->
				<section class="content container-fluid">
					<!-- SELECT2 EXAMPLE -->
					<div class="box box-default">
						<div class="box-header with-border">
							<h3 class="box-title">Please fill in the details below</h3>
							<div class="box-tools pull-right">
								<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
								<button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
							</div>
						</div>
						<!-- /.box-header -->
						<div class="box-body">
							<div class="row">
								<div class="col-md-6">
									<form action="customers_add.php" method="post" id="customerForm">
										<input type="hidden" name="customer_post" value="1" />
										<input type="hidden" name="customer_id" id="customer_id" value="<?php echo $customer_id; ?>" />
										<div class="form-group">
											<label>Customer Number</label>
											<input type="text" class="form-control" placeholder="Enter ..." name="customer_number" id="customer_number" value="<?php echo safeTextOut(htmlspecialchars($customer_number)); ?>">
										</div>
										<div class="form-group">
											<label>Customer Name</label>
											<input type="text" class="form-control" placeholder="Enter ..." name="customer_name" id="customer_name" value="<?php echo safeTextOut(htmlspecialchars($customer_name)); ?>">
										</div>
										<div class="form-group">
											<label>Address</label>
											<textarea class="form-control" rows="3" placeholder="Enter ..." name="customer_address" id="customer_address"><?php echo safeTextOut(htmlspecialchars($customer_address)); ?></textarea>
										</div>
										<div class="form-group">
											<label>Email</label>
											<input type="email" class="form-control" placeholder="Enter ..." name="customer_email" id="customer_email" value="<?php echo safeTextOut(htmlspecialchars($customer_email)); ?>">
										</div>
										<div class="form-group">
											<?php 
												if(isset($action) && $action == 'edit') { 
												?>
											<button type="submit" class="btn btn-primary">Update</button>
											<a href="customers.php" class="btn btn-primary">Cancel</a>
											<?php } 
												else { 
												?>
											<button type="submit" class="btn btn-primary">Add customer</button>
											<?php 
												} 
												?>
										</div>
									</form>
								</div>
								<!-- /.col -->
								<div class="col-md-6">
									<!-- right column content -->
								</div>
								<!-- /.col -->
							</div>
							<!-- /.row -->
						</div>
						<!-- /.box-body -->
						<div class="box-footer">
							<!--Visit <a href="https://select2.github.io/">Select2 documentation</a> for more examples and information about
								the plugin.-->
						</div>
					</div>
					<!-- /.box -->
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
		<script type="text/javascript">
			$('#customerForm').bootstrapValidator({
			message: 'This value is not valid',
			fields: {
			customer_number: {
			validators: {
			notEmpty: {
				message: 'The Categories number is required'
			}
			}
			},
			customer_name: {
			validators: {
			notEmpty: {
				message: 'The customer name is required'
			}
			}
			},
			customer_address: {
			validators: {
			notEmpty: {
				message: 'The customer address is required'
			}
			}
			},
			customer_email: {
			validators: {
			notEmpty: {
				message: 'The customer email is required'
			}
			}
			},
			}
			});
		</script>
	</body>
</html>