<?php
	session_start();
	include("../functions.php");
	include_once("../config.php");
	chkAdminLoggedIn();
	connect_dre_db();
	
	if(isset($_GET['act']) && $_GET['act'] != '' && isset($_GET['id']) && $_GET['id'] > 0) {
		$action = $_GET['act'];
		$supplier_id = $_GET['id'];
		if($action == 'delete') {
		$qry = "DELETE FROM suppliers WHERE id = '$supplier_id'";
	  if(mysqli_query($GLOBALS['conn'],$qry)){
	    	redirect('suppliers.php?resp=deletesucc');
			}
		}
	}
	function getSuppliers()
	{
		$qry="SELECT * from suppliers Order BY id DESC";
		$result=mysqli_query($GLOBALS['conn'], $qry);
		$num=mysqli_num_rows($result);
		if($num>0)
		{
			return $result;
		}
		else
		return false;
	}
	//echo 'here';die;
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
						Suppliers
						<!--<small>Optional description</small>-->
					</h1>
					<ol class="breadcrumb">
						<li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
						<li class="active">Suppliers</li>
					</ol>
				</section>
				<!-- Main content -->
				<section class="content container-fluid">
					<div class="row">
						<div class="col-xs-12">
							<div class="box">
								<div class="box-header">
									<h3 class="box-title">Manage Suppliers</h3>
								</div>
								<!-- /.box-header -->
								<div class="box-body">
									<!-- Success msg display -->
									<?php include("common/info.php"); ?>
									<table id="example2" class="table table-bordered table-hover">
										<thead>
											<tr>
												<th>#</th>
												<th>Suppliers Name</th>
												<th>TRN</th>
												<th>Email</th>
												<th>Phone</th>
												<th>Action</th>
											</tr>
										</thead>
										<tbody>
											<?php
												$prs = getSuppliers();
												//echo '<pre>';print_r($prs);die;
												if($prs != false) {
													$pcount = mysqli_num_rows($prs);
													if($pcount > 0) {
														for($p = 0; $p < $pcount; $p++) {
															$prow = mysqli_fetch_object($prs);
															$id = $prow->id;
															$supplier_name = $prow->supplier_name;
															$trn = $prow->trn;
															$email = $prow->email;
															$phone = $prow->phone;?>
											<tr>
												<td><?php echo $id; ?></td>
												<td><?php echo $supplier_name; ?></td>
												<td><?php echo $trn; ?></td>
												<td><?php echo $email; ?></td>
												<td><?php echo $phone; ?></td>
												<td>
													<div class="text-center">
														<div class="btn-group">
															<!--  <a class="tip image btn btn-primary btn-xs" id="Milkshajr (123456789)" href="#" title="View Image"><i class="fa fa-picture-o"></i></a>-->
															<a href="suppliers_add.php?id=<?php echo $id ?>&act=edit" title="Edit Suppliers" class="tip btn btn-warning btn-xs"><i class="fa fa-edit"></i></a>
															<a href="javascript:void(0)" onclick="deleteIt(<?php echo $id ?>);" title="Delete Suppliers" class="tip btn btn-danger btn-xs"><i class="fa fa-trash-o"></i></a>
														</div>
													</div>
												</td>
											</tr>
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
			function deleteIt(id)
			{
			    if(id && confirm('Are you sure you want to delete this supulier?'))
			    {
			        window.location.href = site_url+'/admin/suppliers.php?id='+id+'&act=delete';
			    }
			}
		</script>
		<!-- Optionally, you can add Slimscroll and FastClick plugins.
			Both of these plugins are recommended to enhance the
			user experience. -->
	</body>
</html>