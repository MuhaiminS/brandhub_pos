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
	
	redirect('manage_staff_loans.php?resp=addsucc');
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
<!-- Start include Header -->
<?php include('header.php'); ?>
<!-- End include Header -->

<!--Start Container-->
<div id="main" class="container-fluid">
	<div class="row">
		<!-- START left bar -->
		<?php include('left.php'); ?>
		<!-- END left bar -->

		
		<!--Start Content-->
		<div id="content" class="col-xs-12 col-sm-10">
			<div id="about">
				<div class="about-inner">
					<h4 class="page-header">Open-source admin theme for you</h4>
					<p>DevOOPS team</p>
					<p>Homepage - <a href="http://devoops.me" target="_blank">http://devoops.me</a></p>
					<p>Email - <a href="mailto:devoopsme@gmail.com">devoopsme@gmail.com</a></p>
					<p>Twitter - <a href="http://twitter.com/devoopsme" target="_blank">http://twitter.com/devoopsme</a></p>
				</div>
			</div>
			<div class="preloader">
				<img src="img/devoops_getdata.gif" class="devoops-getdata" alt="preloader"/>
			</div>
			<div id="">
				<!-- BLOG START -->
				<div class="row">
					<div id="breadcrumb" class="col-xs-12">
						<a href="#" class="show-sidebar">
							<i class="fa fa-bars"></i>
						</a>
						<ol class="breadcrumb pull-left">
							<li><a href="index.php">Dashboard</a></li>
							<li><a href="javascript:void(0); "><?php echo $page_title; ?></a></li>
						</ol>
					</div>
				</div>
				
				<div class="row">
					<div class="col-xs-12 col-sm-12">
						<div class="box">
							<div class="box-header">
								<div class="box-name">
									<i class="fa fa-search"></i>
									<span>Manage Staff Loans</span>
								</div>
								<div class="box-icons">
									<a class="collapse-link">
										<i class="fa fa-chevron-up"></i>
									</a>
									<a class="expand-link">
										<i class="fa fa-expand"></i>
									</a>
									<a class="close-link">
										<i class="fa fa-times"></i>
									</a>
								</div>
								<div class="no-move"></div>
							</div>
							<div class="box-content">
								<?php if($err_msg != '') { ?>
								<p class="bg-danger"> <?php echo $err_msg; ?></p>
								<?php } ?>
								<form id="userForm" method="post" action="" class="form-horizontal" enctype="multipart/form-data">
									<input type="hidden" name="user_post" value="1" />
									<input type="hidden" name="id" id="id" value="<?php echo $id ?>" />
									<fieldset>
										<legend><?php echo $page_title; ?></legend>
										<div class="form-group">
											<label class="col-sm-3 control-label">Staffs</label>
											<div class="col-sm-5">												
												<select class="form-control" name="staff_id" id="staff_id" required>
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
										</div>
										<div class="form-group">
											<label class="col-sm-3 control-label">Credit/Debit</label>
											<div class="col-sm-5">												
												<select class="form-control" name="loan_type" id="loan_type" required>
													<option value="">-- Select a Type --</option>
													<option value="credit" <?php if($loan_type == 'credit') { echo "selected = selected"; } ?> > Credit </option>
													<option value="debit" <?php if($loan_type == 'debit') { echo "selected = selected"; } ?> > Debit </option>
												</select>
											</div>
										</div>
										<div class="form-group">
											<label class="col-sm-3 control-label">Loan Amount</label>
											<div class="col-sm-5">
												<input type="text" class="form-control" required name="loan_amount" id="loan_amount" value="<?php echo safeTextOut(htmlspecialchars($loan_amount)); ?>" />
											</div>
										</div>										
										<div class="form-group">
											<label class="col-sm-3 control-label">Date</label>
											<div class="col-sm-5">
												<input type="text" class="form-control" required name="loan_date" id="loan_date" value="<?php echo safeTextOut(htmlspecialchars($loan_date)); ?>" />
											</div>
										</div>	
									</fieldset>									
									<div class="form-group">
										<div class="col-sm-9 col-sm-offset-3">
											<?php 
												if(isset($action) && $action == 'edit') {
											?>
												<button type="submit" class="btn btn-primary">Update</button>
												<a href="manage_staff_loans.php" class="btn btn-primary">Cancel</a>
											<?php } 
												else { 
											?>
												<button type="submit" class="btn btn-primary">Submit</button>
											<?php 
												} 
											?>
										</div>
									</div>
								</form>
								
							</div>
						</div>
					</div>
				</div>
				<!-- BLOG END -->
			</div>
		</div>
		<!--End Content-->
	</div>
</div>
<!--End Container-->
<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<!--<script src="http://code.jquery.com/jquery.js"></script>-->
<script src="plugins/jquery/jquery.min.js"></script>
<script src="plugins/jquery-ui/jquery-ui.min.js"></script>
<!-- Include all compiled plugins (below), or include individual files as needed -->
<script src="plugins/bootstrap/bootstrap.min.js"></script>
<script src="plugins/justified-gallery/jquery.justifiedGallery.min.js"></script>
<script src="plugins/tinymce/tinymce.min.js"></script>
<script src="plugins/tinymce/jquery.tinymce.min.js"></script>
<script src="plugins/bootstrapvalidator/bootstrapValidator.min.js"></script>
<script src="plugins/select2/select2.min.js"></script>
<!-- All functions for this theme + document.ready processing -->
<script src="js/devoops.js"></script>
<script src="js/jquery-bizzpro-login.js"></script>
<script type="text/javascript">
$(document).ready(function() {
	// Create Wysiwig editor for textare
	//TinyMCEStart('#blog_content', null);
	//TinyMCEStart('#wysiwig_full', 'extreme');
	// Add slider for change test input length
	//FormLayoutExampleInputLength($( ".slider-style" ));
	// Initialize datepicker
	//$('#input_date').datepicker({setDate: new Date()});
	$('#loan_date').datepicker({dateFormat: 'yy-mm-dd'});
	
  //}).on("focusin", ".datepick1", function (e) {
	 //  var $this = $(this);

	/*$('#month_year').datepicker({    
		changeMonth: true,
		changeYear: true,
		changeDate: false,
		showButtonPanel: true,
		dateFormat: 'M-yy',
		onClose: function(dateText, inst) { 
		 $(this).datepicker('setDate', new Date(inst.selectedYear, inst.selectedMonth, 1));
		 }
	   });  */ 
 
	// Load Timepicker plugin
	//LoadTimePickerScript(DemoTimePicker);
	// Add tooltip to form-controls
	$('.form-control').tooltip();
	//LoadSelect2Script(DemoSelect2);
	// Load example of form validation
	//LoadBootstrapValidatorScript(DemoFormValidator);
	// Add drag-n-drop feature to boxes
	WinMove();
});
</script>

<script type="text/javascript">
$('#userForm').bootstrapValidator({
	message: 'This value is not valid',
	fields: {
		name: {
			message: 'The Name is not valid',
			validators: {
				notEmpty: {
					message: 'The Name is required and can\'t be empty'
				},
				stringLength: {
					min: 3,
					max: 30,
					message: 'The Name must be more than 5 and less than 30 characters long'
				}
			}
		},
		
	}
});

</script>
</body>
</html>