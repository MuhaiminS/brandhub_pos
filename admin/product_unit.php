<?php 
	session_start();
	include("../functions.php");
	include_once("../config.php");
	chkAdminLoggedIn();
	connect_dre_db();
	$id = '';
	$unit_name = '';
	$action = 'add';
	$update_img_tbl = false;
	//$category_img_dir = "../category_images/";
	
	if(isset($_POST['unit_post'])) {
		//$parent_id = $_POST['parent_id'];
		$unit_name = $_POST['unit_name'];
		if(isset($_POST['cat_id']) && $_POST['cat_id'] > 0) 
		{
			$id = $_POST['id'];
			$qry = "UPDATE item_units SET unit_name = '".safeTextIn($unit_name)."' WHERE id = '$id'";
			if(mysqli_query($GLOBALS['conn'], $qry)){
			}
			
			redirect('product_unit.php?resp=updatesucc');
		}
		else {
			$qry = "INSERT INTO item_units (unit_name) VALUES ('".safeTextIn($unit_name)."')";
			//echo $qry;
			if(mysqli_query($GLOBALS['conn'], $qry)){		
				$cat_id = mysqli_insert_id($GLOBALS['conn']);
		}
	}
	
		redirect('product_unit.php?resp=addsucc');
	}
	
/* 	if(isset($_GET['act']) && $_GET['act'] != '' && isset($_GET['id']) && $_GET['id'] > 0) {	
		$action = $_GET['act'];
		$id = $_GET['id'];
		if($action == 'edit') 
		{
			$edit_query = "SELECT * FROM item_units WHERE id = '$id'";
			$run_edit = mysqli_query($GLOBALS['conn'], $edit_query);		
			while ($edit_row = mysqli_fetch_array($run_edit)) {
				$unit_name = $edit_row['unit_name'];
								
			}
		}
	}
	if(isset($_GET['id']) && $_GET['id'] > 0) {
		$id = $_GET['id'];
   } */
    if(isset($_GET['act']) && $_GET['act'] != '' && isset($_GET['id']) && $_GET['id'] > 0) {
	  // print_r($_GET); die;
	$action = $_GET['act'];
	$id = $_GET['id'];
		if($action == 'delete') {
		$qry="DELETE FROM item_units WHERE id = $id";
		if(mysqli_query($GLOBALS['conn'], $qry)){

				redirect('product_unit.php?id='.$id);
		}
	}
	
}
   function getItemUnitPost()
   {
   	$qry="SELECT * FROM item_units ORDER BY id DESC";
   	//echo $qry;  die;
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
						Add Unit product
						<!--<small>Optional description</small>-->
					</h1>
					<ol class="breadcrumb">
						<li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
						<li><a href="product_unit.php">Unit Product</a></li>
						<li class="active">Unit Product</li>
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
									<form action="product_unit.php" method="post" id="categoryForm" enctype="multipart/form-data">
										<input type="hidden" name="unit_post" value="1" />
										<input type="hidden" name="id" id="id" value="<?php echo $id; ?>" />			
										<div class="form-group">
											<label>Unit</label>
											<input type="text" class="form-control" name="unit_name" id="unit_name" value="<?php echo safeTextOut(htmlspecialchars($unit_name)); ?>" placeholder="Enter ...">
										</div>
										<!-- /.form-group -->
										<div class="form-group">
											<?php 
												if(isset($action) && $action == 'edit') { 
												?>
											<button type="submit" class="btn btn-primary">Update</button>
											<a href="product_unit.php" class="btn btn-primary">Cancel</a>
											<?php } 
												else { 
												?>
											<button type="submit" class="btn btn-primary">Add Unit</button>
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
						<div class="box">
								<div class="box-header">
									<h3 class="box-title">Manage Unit Product</h3>
								</div>
								<!-- /.box-header -->
								<div class="box-body">
									<!-- Success msg display -->
									<?php include("common/info.php"); ?>
									<table id="example2" class="table table-bordered table-hover">
										<thead>
											<tr>
												<th>#</th>
												<th>Unit</th>
												<th><center>Action</center></th>
											</tr>
										</thead>
										<tbody>
											<?php
												$prs = getItemUnitPost();											
												if($prs != false) {
													$pcount = mysqli_num_rows($prs);
													if($pcount > 0) {
														for($p = 0; $p < $pcount; $p++) {
															$prow = mysqli_fetch_object($prs);
															$id = $prow->id;
															$unit_name = $prow->unit_name;
															?>
											<tr>
												<td><?php echo $id; ?></td>
												<td><?php echo $unit_name; ?></td>
												<td>
													<div class="text-center">
														<div class="btn-group">
															<!--  <a class="tip image btn btn-primary btn-xs" id="Milkshajr (123456789)" href="#" title="View Image"><i class="fa fa-picture-o"></i></a>-->
														<!--	<a href="product_unit.php?id=<?php echo $id ?>&act=edit" title="Edit Unit" class="tip btn btn-warning btn-xs"><i class="fa fa-edit"></i></a>-->
															<a href="javascript:void(0)" onclick="deleteIt(<?php echo $id ?>);" title="Delete Product Unit" class="tip btn btn-danger btn-xs"><i class="fa fa-trash-o"></i></a>
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
												echo "<td>No Unit found to list.</td>";
												echo "</tr>";
												}?>
										</tbody>
									</table>
								</div>
								<!-- /.box-body -->
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
var site_url = "<?php echo getServerURL(); ?>";
function deleteIt(id)
{
	if(id && confirm('Are you sure you want to delete this unit?'))
	{
		window.location.href = site_url+'/admin/product_unit.php?id='+id+'&act=delete';
	}
}
</script>
		<script type="text/javascript">
			var slug = function(str) {
			    var $slug = '';
			    var trimmed = $.trim(str);
			    $slug = trimmed.replace(/[^a-z0-9-]/gi, '-').
			    replace(/-+/g, '-').
			    replace(/^-|-$/g, '');
			    return $slug.toLowerCase();
			}
			$( "#category_title" ).change(function() {	
				if($("#category_title").val() != '' && $("#category_slug").val() == '') {
					$("#category_slug").val(slug($("#category_title").val()));
				}
			});
			  $('#categoryForm').bootstrapValidator({
				message: 'This value is not valid',
				fields: {
					
					         unit_name: {
			                validators: {
			                    notEmpty: {
			                        message: 'The unit is required'
			                    }
			                }
			            },
					
				}
			});


$( "#category_title" ).change(function() {	
	if($("#category_title").val() != '' && $("#category_slug").val() == '') {
		$("#category_slug").val(slug($("#category_title").val()));
	}
});
		</script>
	</body>
</html>