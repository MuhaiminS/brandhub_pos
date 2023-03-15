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
			$qry="DELETE FROM item_category WHERE id = $cat_id";
			if(mysqli_query($GLOBALS['conn'], $qry)){
				redirect('categories.php?resp=deletesucc');
			}
		}
	}
	function getCategoriesPost()
	{
		$qry="SELECT * FROM item_category ORDER BY id DESC";
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
						Categories
						<!--<small>Optional description</small>-->
					</h1>
					<ol class="breadcrumb">
						<li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
						<li class="active">Categories</li>
					</ol>
				</section>
				<!-- Main content -->
				<section class="content container-fluid">
					<div class="row">
						<div class="col-xs-12">
							<div class="box">
								<div class="box-header">
									<h3 class="box-title">Manage Categories</h3>
								</div>
								<!-- /.box-header -->
								<div class="box-body">
									<!-- Success msg display -->
									<?php include("common/info.php"); ?>
									<table id="example2" class="table table-bordered table-hover">
										<thead>
											<tr>
												<th>#</th>
												<th>Name</th>
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
															$cat_id = $prow->id;
															$category_title = $prow->category_title;
															$category_img = $prow->category_img;?>
												<tr>
												<td><?php echo $cat_id; ?></td>
												<td><?php echo $category_title; ?></td>
												<td>
													<div class="text-center">
														<div class="btn-group">
															<!--  <a class="tip image btn btn-primary btn-xs" id="Milkshajr (123456789)" href="#" title="View Image"><i class="fa fa-picture-o"></i></a>-->
															<a href="categories_add.php?id=<?php echo $cat_id ?>&act=edit" title="Edit Category" class="tip btn btn-warning btn-xs"><i class="fa fa-edit"></i></a>
															<a href="javascript:void(0)" onclick="deleteIt(<?php echo $cat_id ?>);" title="Delete Category" class="tip btn btn-danger btn-xs"><i class="fa fa-trash-o"></i></a>
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
			    if(id && confirm('Are you sure you want to delete this category?'))
			    {
			        window.location.href = site_url+'/admin/categories.php?id='+id+'&act=delete';
			    }
			}
		</script>
		<!-- Optionally, you can add Slimscroll and FastClick plugins.
			Both of these plugins are recommended to enhance the
			user experience. -->
	</body>
</html>