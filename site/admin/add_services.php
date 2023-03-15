<?php 
session_start();
include("../functions.php");
include_once("../config.php");
chkAdminLoggedIn();
connect_dre_db();

$name = '';
$arabic_name = '';
$price = '';
$car_ids = array();
$action = 'add';

$barcode_id = randomString();

if(isset($_POST['items_post'])) {
	//echo '<pre>'.print_r($_POST).'</pre>';
	
	$name = $_POST['name'];
	$arabic_name = $_POST['arabic_name'];
	$price = $_POST['price'];
	$car_ids = $_POST['car_ids'];
	$car_ids_str = implode(",", $car_ids);// (count($car_ids) > 0) ? implode(",", $car_ids) : '';
	
	if(isset($_POST['id']) && $_POST['id'] > 0) {
		$id = $_POST['id'];		
		$qry = "UPDATE items SET arabic_name = '".safeTextIn($arabic_name)."', name = '".safeTextIn($name)."', price = '".safeTextIn($price)."', car_ids = '".$car_ids_str."' WHERE id = '$id'";
		
		if(mysql_query($qry)){
		}
	}
	else {		
		$qry = "INSERT INTO items (name, arabic_name, price, car_ids) VALUES ('".safeTextIn($name)."', '".safeTextIn($arabic_name)."', '".safeTextIn($price)."', '".safeTextIn($car_ids_str)."')";
		//echo $qry;
		if(mysql_query($qry)){		
			$id = mysql_insert_id();
		}
	}
	redirect('manage_services.php?resp=addsucc');
}

if(isset($_GET['act']) && $_GET['act'] != '' && isset($_GET['id']) && $_GET['id'] > 0) {	
	$action = $_GET['act'];
	$id = $_GET['id'];
	if($action == 'edit') {
		$edit_query = "SELECT * FROM items WHERE id = '$id'";
		$run_edit = mysql_query($edit_query);		
		while ($edit_row = mysql_fetch_array($run_edit)) {
			$id = $edit_row['id'];
			$name = $edit_row['name'];
			$arabic_name = $edit_row['arabic_name'];
			$price = $edit_row['price'];
			$car_ids = $edit_row['car_ids'];
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
							<li><a href="javascript:void(0); ">Add Services</a></li>
						</ol>
					</div>
				</div>
				
				<div class="row">
					<div class="col-xs-12 col-sm-12">
						<div class="box">
							<div class="box-header">
								<div class="box-name">
									<i class="fa fa-search"></i>
									<span>Manage Services</span>
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
								<form id="itemsForm" method="post" action="" class="form-horizontal" enctype="multipart/form-data">
									<input type="hidden" name="items_post" value="1" />
									<input type="hidden" name="id" id="id" value="<?php echo $id ?>" />
									<fieldset>
										<legend>Add Services</legend>									
										<?php if(isset($_GET['sts']) && $_GET['sts'] == 'invalid') {?>
										<p style="color:red">Invalid file format</p>
										<?php } ?>
										<div class="form-group">
											<label class="col-sm-3 control-label">Name</label>
											<div class="col-sm-5">
												<input type="text" class="form-control" name="name" id="name" value="<?php echo safeTextOut(htmlspecialchars($name)); ?>" />
											</div>
										</div>
										<div class="form-group">
											<label class="col-sm-3 control-label">Arabic Name</label>
											<div class="col-sm-5">
												<input type="text" class="form-control" name="arabic_name" id="arabic_name" value="<?php echo safeTextOut(htmlspecialchars($arabic_name)); ?>" />
											</div>
										</div>
										<div class="form-group">
											<label class="col-sm-3 control-label">Price</label>
											<div class="col-sm-5">
												<input type="text" class="form-control" name="price" id="price" value="<?php echo safeTextOut(htmlspecialchars($price)); ?>" />
											</div>
										</div>
										<div class="form-group">
											<label class="col-sm-3 control-label">Cars</label>
											<div class="col-sm-5">												
												<select class="form-control" name="car_ids[]" id="car_ids" size="10" multiple="multiple">
													<option value="">-- Select a Cars --</option>
													<?php 
														$cars_list = getCarsList();
														foreach ($cars_list as $key => $value) {
															$car_ids_arr = explode(',',$car_ids);
															$selected = in_array($key, $car_ids_arr) ? 'selected ' : '';
															echo "<option value=\"".$key."\" ".$selected.">".$value."</option>";
														}
													?>
												</select>
											</div>
										</div>
									</fieldset>									
									<div class="form-group">
										<div class="col-sm-9 col-sm-offset-3">
											<?php 
												if(isset($action) && $action == 'edit') {
											?>
												<button type="submit" class="btn btn-primary">Update</button>
												<a href="manage_services.php" class="btn btn-primary">Cancel</a>
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
	//TinyMCEStart('#items_content', null);
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
$('#itemsForm').bootstrapValidator({
	message: 'This value is not valid',
	fields: {
		name: {
			message: 'The Item name is not valid',
			validators: {
				notEmpty: {
					message: 'The Item name is required and can\'t be empty'
				},
				stringLength: {
					min: 3,
					max: 100,
					message: 'The Item name must be more than 3 and less than 30 characters long'
				}
			}
		}
	}
});

</script>
</body>
</html>