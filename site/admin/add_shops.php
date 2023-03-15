<?php 
session_start();
include("../functions.php");
include_once("../config.php");
chkAdminLoggedIn();
connect_dre_db();
$id = '';
$shop_name = '';
$shop_location = '';
$shop_country = '';
$status = '';
$shop_lable = '';
$action = 'add';

if(isset($_POST['shop_post'])) {
	$shop_name = $_POST['shop_name'];
	$shop_location = $_POST['shop_location'];
	$shop_country = $_POST['shop_country'];
	$shop_lable = $_POST['shop_lable'];

	if(isset($_POST['id']) && $_POST['id'] > 0) {
		$id = $_POST['id'];
		$qry = "UPDATE locations_shops SET shop_name = '".safeTextIn($shop_name)."', shop_location = '".safeTextIn($shop_location)."', shop_country = '".safeTextIn($shop_country)."', shop_lable = '".safeTextIn($shop_lable)."' WHERE id = '$id'";
	}
	else {
		$qry = "INSERT INTO locations_shops (shop_name, shop_location, shop_country, shop_lable) VALUES ('".safeTextIn($shop_name)."', '".safeTextIn($shop_location)."', '".safeTextIn($shop_country)."', '".safeTextIn($shop_lable)."')";
	}
	//echo $qry;die;
	if(mysqli_query($GLOBALS['conn'],$qry)){		
		//header('Location: manage_page.php?resp=updatesucc');
	}
		
	redirect('manage_shops.php?resp=addsucc');
}

if(isset($_GET['act']) && $_GET['act'] != '' && isset($_GET['id']) && $_GET['id'] > 0) {	
	$action = $_GET['act'];
	$id = $_GET['id'];
	if($action == 'edit') {
		$edit_query = "SELECT * FROM locations_shops WHERE id = '$id'";
		$run_edit = mysqli_query($GLOBALS['conn'],$edit_query);		
		while ($edit_row = mysqli_fetch_array($run_edit)) {
			$id = $edit_row['id'];			
			$shop_name = $edit_row['shop_name'];
			$shop_location = $edit_row['shop_location'];
			$shop_country = $edit_row['shop_country'];
			$shop_lable = $edit_row['shop_lable'];
		}
	}
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
							<li><a href="javascript:void(0); ">Add Shop</a></li>
						</ol>
					</div>
				</div>
				
				<div class="row">
					<div class="col-xs-12 col-sm-12">
						<div class="box">
							<div class="box-header">
								<div class="box-name">
									<i class="fa fa-search"></i>
									<span>Manage Shop List</span>
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
								<form id="pageForm" method="post" action="" class="form-horizontal">
									<input type="hidden" name="shop_post" value="1" />
									<input type="hidden" name="id" id="id" value="<?php echo $id ?>" />
									<fieldset>
										<legend>Add Shop</legend>
										<div class="form-group">
											<label class="col-sm-3 control-label">Shop Name</label>
											<div class="col-sm-5">
												<input type="text" class="form-control" name="shop_name" id="shop_name" value="<?php echo safeTextOut(htmlspecialchars($shop_name)); ?>" />
											</div>
										</div>
										<div class="form-group">
											<label class="col-sm-3 control-label">Shop Location</label>
											<div class="col-sm-5">
												<input type="text" class="form-control" name="shop_location" id="shop_location" value="<?php echo safeTextOut(htmlspecialchars($shop_location)); ?>" />
											</div>
										</div>
										<div class="form-group">
											<label class="col-sm-3 control-label">Shop Country</label>
											<div class="col-sm-5">
												<input type="text" class="form-control" name="shop_country" id="shop_country" value="<?php echo safeTextOut(htmlspecialchars($shop_country)); ?>" />
											</div>
										</div>
										<!-- <div class="form-group">
											<label class="col-sm-3 control-label">Shop Label</label>
											<div class="col-sm-5">
												<input type="text" class="form-control" name="shop_lable" id="shop_lable" value="<?php echo safeTextOut(htmlspecialchars($shop_lable)); ?>" />
											</div>
										</div> -->
									</fieldset>									
									<div class="form-group">
										<div class="col-sm-9 col-sm-offset-3">
											<?php 
												if(isset($action) && $action == 'edit') {
											?>
												<button type="submit" class="btn btn-primary">Update</button>
												<a href="manage_shops.php" class="btn btn-primary">Cancel</a>
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
<!--<script src="plugins/tinymce/tinymce.min.js"></script>
<script src="plugins/tinymce/jquery.tinymce.min.js"></script>-->
<script src="plugins/bootstrapvalidator/bootstrapValidator.min.js"></script>
<script src="plugins/select2/select2.min.js"></script>
<!-- All functions for this theme + document.ready processing -->
<script src="js/devoops.js"></script>
<script src="js/jquery-bizzpro-login.js"></script>
<script type="text/javascript">
$(document).ready(function() {
	// Create Wysiwig editor for textare
	//TinyMCEStart('#news_details', null);
	// Add slider for change test input length
	//FormLayoutExampleInputLength($( ".slider-style" ));
	// Initialize datepicker
	//$('#input_date').datepicker({setDate: new Date()});
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
$('#page_category').select2();
$('#pageForm').bootstrapValidator({
	message: 'This value is not valid',
	fields: {
			shop_name: {
			message: 'The Shop name is not valid',
			validators: {
				notEmpty: {
					message: 'The Shop name is required and can\'t be empty'
				},
				stringLength: {
					min: 2,
					max: 100,
					message: 'The Shop name must be more than 2 and less than 30 characters long'
				}
			}
		}
	}
});
</script>
</body>
</html>