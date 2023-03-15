<?php 
session_start();
include("../functions.php");
chkAdminLoggedIn();
connect_dre_db();
$id = '';
$title = '';
$slug = '';
$status = '';
$action = 'add';

if(isset($_POST['details_post'])) {
	$title = $_POST['title'];
	$slug = $_POST['slug'];
	$created_at = date("Y-m-d H:i:s");
	$updated_at = date("Y-m-d H:i:s");

	if(isset($_POST['id']) && $_POST['id'] > 0) {
		$id = $_POST['id'];
		$qry = "UPDATE users_role SET title = '".safeTextIn($title)."', slug = '".safeTextIn($slug)."', updated_at = '$updated_at' WHERE id = '$id'";
	}
	else {
		$qry = "INSERT INTO users_role (title, slug, updated_at, created_at) VALUES ('".safeTextIn($title)."', '".safeTextIn($slug)."', '$updated_at', 'created_at')";
	}
	//echo $qry;die;
	if(mysqli_query($GLOBALS['conn'],$qry)){		
		//header('Location: manage_page.php?resp=updatesucc');
	}
		
	redirect('manage_user_role.php?resp=addsucc');
}

if(isset($_GET['act']) && $_GET['act'] != '' && isset($_GET['id']) && $_GET['id'] > 0) {	
	$action = $_GET['act'];
	$id = $_GET['id'];
	if($action == 'edit') {
		$edit_query = "SELECT * FROM users_role WHERE id = '$id'";
		$run_edit = mysqli_query($GLOBALS['conn'],$edit_query);		
		while ($edit_row = mysqli_fetch_array($run_edit)) {
			$id = $edit_row['id'];			
			$title = $edit_row['title'];
			$slug = $edit_row['slug'];
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
							<li><a href="javascript:void(0); ">Add Users Role</a></li>
						</ol>
					</div>
				</div>
				
				<div class="row">
					<div class="col-xs-12 col-sm-12">
						<div class="box">
							<div class="box-header">
								<div class="box-name">
									<i class="fa fa-search"></i>
									<span>Manage Users Role</span>
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
									<input type="hidden" name="details_post" value="1" />
									<input type="hidden" name="id" id="id" value="<?php echo $id ?>" />
									<fieldset>
										<legend>Add Users Role</legend>
										<div class="form-group">
											<label class="col-sm-3 control-label">Title</label>
											<div class="col-sm-5">
												<input type="text" class="form-control" name="title" id="title" value="<?php echo safeTextOut(htmlspecialchars($title)); ?>" />
											</div>
										</div>
										<div class="form-group">
											<label class="col-sm-3 control-label">Slug</label>
											<div class="col-sm-5">
												<input type="text" class="form-control" name="slug" id="slug" value="<?php echo safeTextOut(htmlspecialchars($slug)); ?>" />
											</div>										
									</fieldset>									
									<div class="form-group">
										<div class="col-sm-9 col-sm-offset-3">
											<?php 
												if(isset($action) && $action == 'edit') {
											?>
												<button type="submit" class="btn btn-primary">Update</button>
												<a href="manage_user_role.php" class="btn btn-primary">Cancel</a>
											<?php } 
												else { 
											?>
												<button type="submit" class="btn btn-primary">Submit</button>
											<?php 
												} 
											?>												
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
var slug = function(str) {
    var $slug = '';
    var trimmed = $.trim(str);
    $slug = trimmed.replace(/[^a-z0-9-]/gi, '-').
    replace(/-+/g, '-').
    replace(/^-|-$/g, '');
    return $slug.toLowerCase();
}

$('#page_category').select2();
$('#pageForm').bootstrapValidator({
	message: 'This value is not valid',
	fields: {
			title: {
			message: 'The Title  is not valid',
			validators: {
				notEmpty: {
					message: 'The Title is required and can\'t be empty'
				},
				stringLength: {
					min: 2,
					max: 100,
					message: 'The Title must be more than 2 and less than 30 characters long'
				}
			}
		}
	}
});
$( "#title" ).change(function() {	
	if($("#title").val() != '' && $("#slug").val() == '') {
		$("#slug").val(slug($("#title").val()));
	}
});
</script>
</body>
</html>