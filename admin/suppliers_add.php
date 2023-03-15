<?php
	session_start();
	include("../functions.php");
	include_once("../config.php");
	chkAdminLoggedIn();
	connect_dre_db();
	$err_msg = '';
	$page_title = 'Add supplier';
	$supplier_id = '';
	$supplier_name = '';
	$trn = '';
	$email = '';
	$phone = '';
	$address = '';
	
	$action = 'add';
	
	if(isset($_POST['supplier_post'])) {
		 $supplier_id = $_POST['supplier_id'];
		 $supplier_name = $_POST['supplier_name'];
		 $trn = $_POST['trn'];
		 $email= $_POST['email'];
		 $phone = $_POST['phone'];
		 $address = $_POST['address'];
		 //$is_active = $_POST['is_active'];
	
		if(isset($_POST['supplier_id']) && $_POST['supplier_id'] > 0) {
			$supplier_id = $_POST['supplier_id'];
			$qry = "UPDATE suppliers SET supplier_name = '".safeTextIn($supplier_name)."', trn= '".safeTextIn($trn)."', email= '".safeTextIn($email)."', phone = '".safeTextIn($phone)."', address = '".safeTextIn($address)."' WHERE id = '$supplier_id'";
			if(mysqli_query($GLOBALS['conn'],$qry)){
				//header('Location: manage_blog.php?resp=updatesucc');
			}
			redirect('suppliers.php?resp=updatesucc');
		}
		else {
			//$user_pass = $_POST['user_pass'];
	
			$qry = "INSERT INTO suppliers (supplier_name, trn, email, phone,address) VALUES ('".safeTextIn($supplier_name)."', '".safeTextIn($trn)."', '".safeTextIn($email)."', '".safeTextIn($phone)."','".safeTextIn($address)."')";
			//echo $qry; die;
			if(mysqli_query($GLOBALS['conn'],$qry)){
				$supplier_name = mysqli_insert_id($GLOBALS['conn']);
			}
		}
	redirect('suppliers.php?resp=addsucc');
	}
	
	if(isset($_GET['act']) && $_GET['act'] != '' && isset($_GET['id']) && $_GET['id'] > 0) {
		$action = $_GET['act'];
		$id = $_GET['id'];
		if($action == 'edit') {
		//$page_title = 'edit';
			$edit_query = "SELECT * FROM suppliers WHERE id = '$id'";
			$run_edit = mysqli_query($GLOBALS['conn'],$edit_query);
			while ($edit_row = mysqli_fetch_array($run_edit)) {
				$supplier_id = $edit_row['id'];
				$supplier_name = $edit_row['supplier_name'];
				$trn = $edit_row['trn'];
				$email = $edit_row['email'];
				$phone = $edit_row['phone'];
				$address = $edit_row['address'];
				//$is_active = $edit_row['is_active'];
			}
		}
	}
	
	/*function getSuppliersList()
	{
		$suppliers = array();
		$query = "SELECT * FROM supplier_name ORDER BY id ASC";
		$run = mysqli_query($query);
		while($row = mysqli_fetch_array($run)) {
			$supplier_name = $row['$supplier_name'];
			$suppliers[$supplier_name] = $row['$supplier_name'];
		}
		//return $suppliers;
	}*/
	
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
						Add Suppliers
						<!--<small>Optional description</small>-->
					</h1>
					<ol class="breadcrumb">
						<li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
						<li><a href="categories.php">Suppliers</a></li>
						<li class="active">Add Suppliers</li>
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
									<form action="suppliers_add.php" method="post" id="supplierForm" enctype="multipart/form-data">
										<input type="hidden" name="supplier_post" value="1" />
										<input type="hidden" name="supplier_id" id="supplier_id" value="<?php echo $supplier_id; ?>" />			
										<div class="form-group">
											<label>Supplier Name</label>
											<input type="text" class="form-control" name="supplier_name" size="250" id="supplier_name" value="<?php echo safeTextOut(htmlspecialchars($supplier_name)); ?>" placeholder="Supplier Name">
										</div>
										<div class="form-group">
											<label>TRN</label>
											<input type="text" class="form-control" name="trn" size="250" id="trn" value="<?php echo safeTextOut(htmlspecialchars($trn)); ?>" placeholder="TRN">
										</div>
										<div class="form-group">
											<label>Email</label>
											<input type="email" class="form-control" name="email" id="email" value="<?php echo safeTextOut(htmlspecialchars($email)); ?>" placeholder="Email">
										</div>
										<div class="form-group">
											<label>Phone</label>
											<input type="text" class="form-control" name="phone" id="phone" value="<?php echo safeTextOut(htmlspecialchars($phone)); ?>" placeholder="Phone">
										</div>
										<div class="form-group">
											<label>Address</label>
											<textarea class="form-control" name="address" id="address" placeholder="Address"><?php echo safeTextOut(htmlspecialchars($address)); ?></textarea>
										</div>
										<!-- /.form-group -->
										<div class="form-group">
											<?php 
												if(isset($action) && $action == 'edit') { 
												?>
											<button type="submit" class="btn btn-primary">Update</button>
											<a href="suppliers.php" class="btn btn-primary">Cancel</a>
											<?php } 
												else { 
												?>
											<button type="submit" class="btn btn-primary">Add supplier</button>
											<?php 
												} 
												?>
										</div>
									</form>
								</div>
								<!-- /.col -->
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
			$('#supplierForm').bootstrapValidator({
			message: 'This value is not valid',
			fields: {
			supplier_name: {
				validators: {
					notEmpty: {
						message: 'The Supplier Name is required and connot be empty'
					}
				}
			},
			trn: {
				validators: {
					notEmpty: {
						message: 'The Trn is required and connot be empty'
					}
				}
			},
			email: {
				validators: {
					notEmpty: {
						message: 'The Email is required and connot be empty'
					}
				}
			},
			phone: {
				validators: {
					notEmpty: {
						message: 'The Phone Number is required and connot be empty'
					}
				}
			},
			}
			});
		</script>
	</body>
</html>