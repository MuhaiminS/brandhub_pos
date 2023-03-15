<?php 
session_start();
include("../functions.php");
include_once("../config.php");
chkAdminLoggedIn();
connect_dre_db();
$car_id = '';
$car_title = '';
$car_slug = '';
$car_details = '';
$car_img = '';
$action = 'add';
$update_img_tbl = false;
$car_img_dir = "../car_images/";

if(isset($_POST['car_post'])) {
	$car_title = $_POST['car_title'];
	$car_slug = seoUrl($car_title);
    //$car_details = $_POST['car_details'];
	//$car_img = $_FILES['car_img']['name'];

	//$car_details=str_replace('\r\n', '', $car_details);
	//$car_details =stripslashes($car_details);
	
	if(isset($_POST['car_id']) && $_POST['car_id'] > 0) {
		$car_id = $_POST['car_id'];
		$qry = "UPDATE cars SET car_title = '".safeTextIn($car_title)."', car_slug = '$car_slug', car_details = '$car_details' WHERE id = '$car_id'";
		if(mysql_query($qry)){
		}
	}
	else {
		$qry = "INSERT INTO cars (car_title, car_slug, car_details) VALUES ('".safeTextIn($car_title)."', '$car_slug', '$car_details')";
		//echo $qry;die;
		if(mysql_query($qry)){		
			$car_id = mysql_insert_id();
}
	}

	/*if (file_exists($_FILES['car_img']['tmp_name'])) {		
		$userfile_name = "_cars";
		$objectname = "car_img";
		if (isset($_FILES[$objectname]['name'])) {
			$path = $car_img_dir;
			$new_width = 340;
			$new_height = 330;
			$userfile_name = saveCategoryImages($userfile_name, $objectname, $path, $new_height, $new_width);
			$qry = "UPDATE cars SET car_img = '$userfile_name' WHERE id = '$car_id'";
			if(mysql_query($qry)){
			}			
		}
	}*///die;
		
	redirect('manage_cars.php?resp=addsucc');
}

if(isset($_GET['act']) && $_GET['act'] != '' && isset($_GET['id']) && $_GET['id'] > 0) {	
	$action = $_GET['act'];
	$car_id = $_GET['id'];
	if($action == 'edit') {
		$edit_query = "SELECT * FROM cars WHERE id = '$car_id'";
		$run_edit = mysql_query($edit_query);		
		while ($edit_row = mysql_fetch_array($run_edit)) {
			$car_id = $edit_row['id'];
			$car_title = $edit_row['car_title'];
			$car_slug = $edit_row['car_slug'];
			$car_img = $edit_row['car_img'];
			$car_details = $edit_row['car_details'];
		}
	}
}

function getParentCategoriesList()
{
	$cars = array();
	$query = "SELECT * FROM cars ORDER BY car_title ASC";
	$run = mysql_query($query);
	while($row = mysql_fetch_array($run)) {
		$car_id = $row['id'];
		$cars[$car_id] = $row['car_title'];
	}
	return $cars;	
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
							<li><a href="javascript:void(0); ">Add Cars</a></li>
						</ol>
					</div>
				</div>
				
				<div class="row">
					<div class="col-xs-12 col-sm-12">
						<div class="box">
							<div class="box-header">
								<div class="box-name">
									<i class="fa fa-search"></i>
									<span>Manage Cars</span>
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
								<form id="carForm" method="post" action="" class="form-horizontal" enctype="multipart/form-data">
									<input type="hidden" name="car_post" value="1" />
									<input type="hidden" name="car_id" id="car_id" value="<?php echo $car_id ?>" />
									<fieldset>
										<legend>Add Cars</legend>																			
										<div class="form-group">
											<label class="col-sm-3 control-label">Title</label>
											<div class="col-sm-5">
												<input type="text" class="form-control" name="car_title" id="car_title" value="<?php echo safeTextOut(htmlspecialchars($car_title)); ?>" />
											</div>
										</div>

										<div class="form-group">
											<label class="col-sm-3 control-label">Slug</label>
											<div class="col-sm-5">
												<input type="text" class="form-control" name="car_slug" id="car_slug" value="<?php echo safeTextOut(htmlspecialchars($car_slug)); ?>" />
											</div>
										</div>
									</fieldset>									
									<div class="form-group">
										<div class="col-sm-9 col-sm-offset-3">
											<?php 
												if(isset($action) && $action == 'edit') {
											?>
												<button type="submit" class="btn btn-primary">Update</button>
												<a href="manage_cars.php" class="btn btn-primary">Cancel</a>
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
	//TinyMCEStart('#category_content', null);
	//TinyMCEStart('#wysiwig_full', 'extreme');
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
var slug = function(str) {
    var $slug = '';
    var trimmed = $.trim(str);
    $slug = trimmed.replace(/[^a-z0-9-]/gi, '-').
    replace(/-+/g, '-').
    replace(/^-|-$/g, '');
    return $slug.toLowerCase();
}
$('#carForm').bootstrapValidator({
	message: 'This value is not valid',
	fields: {
		car_title: {
			message: 'The car title is not valid',
			validators: {
				notEmpty: {
					message: 'The car title is required and can\'t be empty'
				},
				stringLength: {
					min: 3,
					max: 100,
					message: 'The car title must be more than 3 and less than 30 characters long'
				}
			}
		}
	}
});

$( "#car_title" ).change(function() {	
	if($("#car_title").val() != '' && $("#car_slug").val() == '') {
		$("#car_slug").val(slug($("#car_title").val()));
	}
});
</script>
</body>
</html>