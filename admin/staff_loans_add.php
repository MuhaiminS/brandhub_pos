<?php 
session_start();
include("../functions.php");
include_once("../config.php");
chkAdminLoggedIn();
connect_dre_db();
$err_msg = '';
$page_title = 'Add Staff Loan';

$loan_amount = '';
$loan_type = '';
$staff_id = '';
$loan_date = '';

$is_active = '';
$date_added = '';
$date_updated = '';
$action = 'add';


if(isset($_POST['user_post'])) {	
	$loan_amount = $_POST['loan_amount'];
	$loan_type = $_POST['loan_type'];	
	$staff_id = $_POST['staff_id'];
	$loan_date = $_POST['loan_date'];	
	$date_added = date("Y-m-d H:i:s");
	$date_updated = date("Y-m-d H:i:s");

	if(isset($_POST['id']) && $_POST['id'] > 0) {
		$id = $_POST['id'];
		$qry = "UPDATE staff_loans SET loan_amount = '".safeTextIn($loan_amount)."', staff_id = '$staff_id', loan_type = '".safeTextIn($loan_type)."', loan_date = '".safeTextIn($loan_date)."', date_updated = '$date_updated' WHERE id = '$id'";
		if(mysqli_query($GLOBALS['conn'],$qry)){		
			//header('Location: manage_blog.php?resp=updatesucc');
		}
	}
	else {
		//$user_pass = $_POST['user_pass'];
		$qry = "INSERT INTO staff_loans (loan_amount, staff_id, loan_type, loan_date, date_added, date_updated) VALUES ('".safeTextIn($loan_amount)."', '$staff_id', '".safeTextIn($loan_type)."', '".safeTextIn($loan_date)."', '$date_added', '$date_updated')";
		//echo $qry;
		if(mysqli_query($GLOBALS['conn'],$qry)){		
			$id = mysqli_insert_id($GLOBALS['conn']);			
		}
	}
	
	redirect('staff_loans.php?resp=addsucc');
}


if(isset($_GET['act']) && $_GET['act'] != '' && isset($_GET['id']) && $_GET['id'] > 0) {	
	$action = $_GET['act'];
	$id = $_GET['id'];
	if($action == 'edit') {
		$page_title = 'Edit User';
		$edit_query = "SELECT * FROM staff_loans WHERE id = '$id'";
		$run_edit = mysqli_query($GLOBALS['conn'],$edit_query);		
		while ($edit_row = mysqli_fetch_array($run_edit)) {
			$id = $edit_row['id'];
			$loan_amount = $edit_row['loan_amount'];
			$loan_type = $edit_row['loan_type'];	
			$staff_id = $edit_row['staff_id'];
			$loan_date = $edit_row['loan_date'];			
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
									<form action="staff_loans_add.php" method="post" id="staffForm" enctype="multipart/form-data">
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
											<label class="control-label">Credit/Debit</label>
											<select class="form-control" name="loan_type" id="loan_type" >
												<option value="">-- Select a Type --</option>
													<option value="credit" <?php if($loan_type == 'credit') { echo "selected = selected"; } ?> > Credit </option>
													<option value="debit" <?php if($loan_type == 'debit') { echo "selected = selected"; } ?> > Debit </option>
											</select>
										</div>	
										<div class="form-group">
											<label>Loan Amount</label>
											<input type="text" class="form-control" name="loan_amount" id="loan_amount" value="<?php echo safeTextOut(htmlspecialchars($loan_amount)); ?>" placeholder="Enter ..." >
										</div>
										<div class="form-group">
											<label>Date</label>
											<input type="text" class="form-control datepicker" name="loan_date" id="loan_date" value="<?php echo safeTextOut(htmlspecialchars($loan_date)); ?>" placeholder="Enter ..." >
										</div>
										<!-- /.form-group -->
										<div class="form-group">
											<?php 
												if(isset($action) && $action == 'edit') { 
												?>
											<button type="submit" class="btn btn-primary">Update</button>
											<a href="staff_loans.php" class="btn btn-primary">Cancel</a>
											<?php } 
												else { 
												?>
											<button type="submit" class="btn btn-primary">Add Loan</button>
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