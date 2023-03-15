<?php 
session_start();
include("../functions.php");
include_once("../config.php");
chkAdminLoggedIn();
connect_dre_db();

$cat_id = '';
$parent_id = 0;
$expense_name = '';
$category_slug = '';
//$category_details = '';
//$category_img = '';
$action = 'add';
$update_img_tbl = false;
//$category_img_dir = "../category_images/";

if(isset($_POST['category_post'])) {
	//$parent_id = $_POST['parent_id'];
	$expense_name = $_POST['expense_name'];
	//$category_slug = seoUrl($expense_name);
    //$category_details = $_POST['category_details'];
	//$category_img = $_FILES['category_img']['name'];

	//$category_details=str_replace('\r\n', '', $category_details);
	//$category_details =stripslashes($category_details);
	
	//print_r($_POST);die;
	
	/*if (file_exists($_FILES['category_img']['tmp_name'])) {		
		$userfile_name = "_category";//$_FILES["category_image"]["name"];
		$objectname = "category_img";
		if (isset($_FILES[$objectname]['name'])) {
			$path = $category_img_dir;			
			$new_width = 340;
			$new_height = 330;
			$userfile_name = saveCategoryImages($userfile_name,$objectname,$path,$new_height,$new_width);
			if(!$userfile_name) {
				redirect('add_category.php?sts=invalid');
				exit;
			}
			$update_img_tbl = true;
		}
	}*/
	if(isset($_POST['cat_id']) && $_POST['cat_id'] > 0) {
		$cat_id = $_POST['cat_id'];
		$qry = "UPDATE expense_category SET expense_name = '".safeTextIn($expense_name)."' WHERE id = '$cat_id'";
		if(mysqli_query($GLOBALS['conn'], $qry)){
		}
	}
	else {
		$qry = "INSERT INTO expense_category (expense_name) VALUES ('".safeTextIn($expense_name)."')";
		//echo $qry;
		if(mysqli_query($GLOBALS['conn'], $qry)){		
			$cat_id = mysqli_insert_id($GLOBALS['conn']);
}
	}

	/*if (file_exists($_FILES['category_img']['tmp_name'])) {		
		$userfile_name = "_category";
		$objectname = "category_img";
		if (isset($_FILES[$objectname]['name'])) {
			$path = $category_img_dir;
			$new_width = 340;
			$new_height = 330;
			$userfile_name = saveCategoryImages($userfile_name, $objectname, $path, $new_height, $new_width);
			$qry = "UPDATE category SET category_img = '$userfile_name' WHERE id = '$cat_id'";
			if(mysqli_query($GLOBALS['conn'], $qry)){
			}			
		}
	}*///die;
		
	redirect('manage_expense_category.php?resp=addsucc');
}

if(isset($_GET['act']) && $_GET['act'] != '' && isset($_GET['id']) && $_GET['id'] > 0) {	
	$action = $_GET['act'];
	$cat_id = $_GET['id'];
	if($action == 'edit') {
		$edit_query = "SELECT * FROM expense_category WHERE id = '$cat_id'";
		$run_edit = mysqli_query($GLOBALS['conn'], $edit_query);		
		while ($edit_row = mysqli_fetch_array($run_edit)) {
			$cat_id = $edit_row['id'];
			//$parent_id = $edit_row['parent_id'];
			$expense_name = $edit_row['expense_name'];
			//$category_slug = $edit_row['category_slug'];
			//$category_img = $edit_row['category_img'];
			//$category_details = $edit_row['category_details'];
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
							<li><a href="javascript:void(0); ">Add Expense Category</a></li>
						</ol>
					</div>
				</div>
				
				<div class="row">
					<div class="col-xs-12 col-sm-12">
						<div class="box">
							<div class="box-header">
								<div class="box-name">
									<i class="fa fa-search"></i>
									<span>Manage Expense Category</span>
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
								<form id="categoryForm" method="post" action="" class="form-horizontal" enctype="multipart/form-data">
									<input type="hidden" name="category_post" value="1" />
									<input type="hidden" name="cat_id" id="cat_id" value="<?php echo $cat_id ?>" />
									<fieldset>
										<legend>Add Expense Category</legend>									
										<?php if(isset($_GET['sts']) && $_GET['sts'] == 'invalid') {?>
										<p style="color:red">Invalid file format</p>
										<?php } ?>
										<div class="form-group">
											<label class="col-sm-3 control-label">Title</label>
											<div class="col-sm-5">
												<input type="text" class="form-control" name="expense_name" id="expense_name" value="<?php echo safeTextOut(htmlspecialchars($expense_name)); ?>" required/>
											</div>
										</div>
									</fieldset>									
									<div class="form-group">
										<div class="col-sm-9 col-sm-offset-3">
											<?php 
												if(isset($action) && $action == 'edit') {
											?>
												<button type="submit" class="btn btn-primary">Update</button>
												<a href="manage_expense_category.php" class="btn btn-primary">Cancel</a>
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
$('#categoryForm').bootstrapValidator({
	message: 'This value is not valid',
	fields: {
		expense_name: {
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
		}
	}
});

$( "#expense_name" ).change(function() {	
	if($("#expense_name").val() != '' && $("#category_slug").val() == '') {
		$("#category_slug").val(slug($("#expense_name").val()));
	}
});
</script>
</body>
</html>