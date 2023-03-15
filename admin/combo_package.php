<?php 
session_start();
include("../functions.php");
include_once("../config.php");
chkAdminLoggedIn();
connect_dre_db();
if(isset($_GET['act']) && $_GET['act'] != '' && isset($_GET['id']) && $_GET['id'] > 0) {	
	$action = $_GET['act'];
	$cat_id = $_GET['id'];
	if($action == 'delete') {
		$qry="DELETE FROM combo_package WHERE id = $cat_id";
		if(mysqli_query($GLOBALS['conn'], $qry)){
			redirect('combo_package.php?resp=succ');
		}
	}
}

function getCategoriesPost()
{
	$qry="SELECT * FROM combo_package ORDER BY id DESC";
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
//$category_img_dir = "../category_images/";
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
						Combo Package
						<!--<small>Optional description</small>-->
					</h1>
					<ol class="breadcrumb">
						<li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
						<li class="active">Combo Package</li>
					</ol>
				</section>
				<!-- Main content -->
				<section class="content container-fluid">
					<div class="row">
						<div class="col-xs-12">
							<div class="box">
								<div class="box-header">
									<h3 class="box-title">Combo Package</h3>
								</div>
								<!-- /.box-header -->
								<div class="box-body">
									<!-- Success msg display -->
									<?php include("common/info.php"); ?>
									<table id="example2" class="table table-bordered table-hover">
										<thead>
										<tr>
											<th>#</th>
											<th>Package Name</th>
											<th>Package Price</th>
											<th>Package items</th>
											<th>Action</th>
										</tr>
										</thead>
										
											<tbody>
										<?php
											$prs = getCategoriesPost();											
											if($prs != false) {
												$pcount = mysqli_num_rows($prs);
												if($pcount > 0) {
													for($p = 0; $p < $pcount; $p++) {
														$prow = mysqli_fetch_object($prs);
														$id = $prow->id;
														$package_name = $prow->package_name;
														$package_price = $prow->package_price;
														$package_items = $prow->package_items;
														echo "<tr>";
														echo "<td>".$id."</td>";
														echo "<td>".$package_name."</td>";
														echo "<td>".$package_price."</td>";
														echo "<td>".$package_items."</td>";
														//echo "<td><img src=\"".$category_img_dir.$category_img."\" width=\"100\" height=\"100\"  alt=\"".$category_title."\" /></td>";
														echo "<td><a href='combo_package_add.php?id=".$id."&act=edit'class='btn btn-warning btn-xs'><i class='fa fa-edit'></i></a>
														</td>";
														echo "</tr>";
													}
												}
											}
											else {
												echo "<tr>";
												echo "<td>No Package found to list.</td>";
												echo "</tr>";
											}
										?>						
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
    if(id && confirm('Are you sure you want to delete this Package?'))
    {
        window.location.href = site_url+'/admin/combo_package.php?id='+id+'&act=delete';
    }
}
</script>
		<!-- Optionally, you can add Slimscroll and FastClick plugins.
			Both of these plugins are recommended to enhance the
			user experience. -->
	</body>
</html>