<?php 
session_start();
include("../functions.php");
include_once("../config.php");
chkAdminLoggedIn();
connect_dre_db();
$err_msg = '';
$page_title = 'Add Staff Salary';

$basic_salary = '';
$allowance = '';
$staff_id = '';
$month_year = '';

$is_active = '';
$date_added = '';
$date_updated = '';
$action = 'add';


if(isset($_POST['user_post'])) {	
	$basic_salary = $_POST['basic_salary'];
	$allowance = $_POST['allowance'];	
	$staff_id = $_POST['staff_id'];
	$month_year = $_POST['month_year'];	
	$date_added = date("Y-m-d H:i:s");
	$date_updated = date("Y-m-d H:i:s");

	if(isset($_POST['id']) && $_POST['id'] > 0) {
		$id = $_POST['id'];
		$qry = "UPDATE staff_salary SET basic_salary = '".safeTextIn($basic_salary)."', allowance = '$allowance', staff_id = '".safeTextIn($staff_id)."', month_year = '".safeTextIn($month_year)."', date_updated = '$date_updated' WHERE id = '$id'";
		if(mysqli_query($GLOBALS['conn'],$qry)){		
			//header('Location: manage_blog.php?resp=updatesucc');
		}
	}
	else {
		//$user_pass = $_POST['user_pass'];
		$qry = "INSERT INTO staff_salary (basic_salary, allowance, staff_id, month_year, date_added, date_updated) VALUES ('".safeTextIn($basic_salary)."', '$allowance', '".safeTextIn($staff_id)."', '".safeTextIn($month_year)."', '$date_added', '$date_updated')";
		//echo $qry;
		if(mysqli_query($GLOBALS['conn'],$qry)){		
			$id = mysqli_insert_id($GLOBALS['conn']);			
		}
	}
	

	$s_basic_salary = 0;
	$s_allowance = 0;

	$query = "SELECT * FROM staff_salary WHERE staff_id = '$staff_id'";
	$staff_query = mysqli_query($GLOBALS['conn'],$query);
	if($staff_query) {
		while ($s_row = mysqli_fetch_assoc($staff_query)) {
			//echo '<pre>';print_r($s_row);
			$s_basic_salary += $s_row['basic_salary'];
			$s_allowance += $s_row['allowance'];
		} 		
	}
	//$s_basic_salary = $s_basic_salary + $basic_salary;
	//$s_allowance = $s_allowance + $allowance;

	$qry1 = "UPDATE drivers SET basic_salary = '".safeTextIn($s_basic_salary)."', allowance = '$s_allowance', date_updated = '$date_updated' WHERE id = '$staff_id'";
	if(mysqli_query($GLOBALS['conn'],$qry1)){		
		//header('Location: manage_blog.php?resp=updatesucc');
	}
	redirect('staff_salary.php?resp=addsucc');
}


if(isset($_GET['act']) && $_GET['act'] != '' && isset($_GET['id']) && $_GET['id'] > 0) {	
	$action = $_GET['act'];
	$id = $_GET['id'];
	if($action == 'edit') {
		$page_title = 'Edit User';
		$edit_query = "SELECT * FROM staff_salary WHERE id = '$id'";
		$run_edit = mysqli_query($GLOBALS['conn'],$edit_query);		
		while ($edit_row = mysqli_fetch_array($run_edit)) {
			$id = $edit_row['id'];
			$basic_salary = $edit_row['basic_salary'];
			$allowance = $edit_row['allowance'];	
			$staff_id = $edit_row['staff_id'];
			$month_year = $edit_row['month_year'];				
		}
	}
}

function getStaffList()
{
	$manufacturing = array();
	$query = "SELECT * FROM drivers ORDER BY id ASC";
	$run = mysqli_query($GLOBALS['conn'], $query);
	while($row = mysqli_fetch_array($run)) {
		$id = $row['id'];
		$manufacturing[$id] = $row['name'];
	}
	return $manufacturing;	
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
						Add Staff Salary
						<!--<small>Optional description</small>-->
					</h1>
					<ol class="breadcrumb">
						<li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
						<li><a href="staff_salary.php">Add Staff Salary</a></li>
						<li class="active">Add Staff Salary</li>
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
									<form action="staff_salary_add.php" method="post" id="staffForm" enctype="multipart/form-data">
										<input type="hidden" name="user_post" value="1" />
										<input type="hidden" name="id" id="id" value="<?php echo $id ?>" />	
										<div class="form-group">
											<label class="control-label">Staffs</label>
											<select class="form-control" name="staff_id" id="staff_id" >
												<option value="">-- Select a Staff --</option>
													<?php 
														$staff_list = getStaffList();
														foreach ($staff_list as $key => $value) {
															$selected = ($key == $staff_id) ? "selected = selected" : "";
															echo "<option value=\"".$key."\" ".$selected.">".ucfirst($value)."</option>";
														}
													?>
											</select>
										</div>		
										<div class="form-group">
											<label>Package Name</label>
											<input type="text" class="form-control" name="basic_salary" id="basic_salary" value="<?php echo safeTextOut(htmlspecialchars($basic_salary)); ?>" placeholder="Enter ..." >
										</div>
										<div class="form-group">
											<label>Package Price</label>
											<input type="text" class="form-control" name="allowance" id="allowance" value="<?php echo safeTextOut(htmlspecialchars($allowance)); ?>" placeholder="Enter ..." >
										</div>
										<div class="form-group">
											<label>No. of Items</label>
											<input type="text" class="form-control" name="month_year" id="month_year" value="<?php echo safeTextOut(htmlspecialchars($month_year)); ?>" placeholder="Enter ..." >
										</div>
										<!-- /.form-group -->
										<div class="form-group">
											<?php 
												if(isset($action) && $action == 'edit') { 
												?>
											<button type="submit" class="btn btn-primary">Update</button>
											<a href="staff_salary.php" class="btn btn-primary">Cancel</a>
											<?php } 
												else { 
												?>
											<button type="submit" class="btn btn-primary">Add Salary</button>
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
			<script src="plugins/bootstrapvalidator/bootstrapValidator.min.js"></script>
		</div>
		<!-- ./wrapper -->
		<!-- REQUIRED JS SCRIPTS -->
		<?php include("common/footer-scripts.php"); ?>
		<!-- Optionally, you can add Slimscroll and FastClick plugins.
			Both of these plugins are recommended to enhance the
			user experience. -->
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
			  $('#staffForm').bootstrapValidator({
				message: 'This value is not valid',
				fields: {
					category_title: {
						message: 'The category title is not valid',
						validators: {
							notEmpty: {
								message: 'The category title is required and can\'t be empty'
							},
							stringLength: {
								min: 3,
								max: 100,
								message: 'The category title must be more than 3 and less than 30 characters long'
							}
						}
					},
					            staff_id: {
			                validators: {
			                    notEmpty: {
			                        message: 'The Staff is required'
			                    }
			                }
			            },
			            package_items: {
			                validators: {
			                    notEmpty: {
			                        message: 'The Package Items is required'
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