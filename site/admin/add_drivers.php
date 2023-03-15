<?php 
session_start();
include("../functions.php");
include_once("../config.php");
chkAdminLoggedIn();
connect_dre_db();
$err_msg = '';
$page_title = 'Add User';
$name = '';
$email = '';
$phone = '';
$qualification = '';
$country = '';
$state = '';
$city = '';
$address = '';
$zip = '';
$license = '';
$passport = '';
$idproof = '';
$image = '';
$doj = '';
$manufacturing = '';
$is_active = '';
$date_added = '';
$date_updated = '';
$action = 'add';

$license_img_dir = "../driver_images/license_images/";
$passport_img_dir = "../driver_images/passport_images/";
$idproof_img_dir = "../driver_images/idproof_images/";
$image_img_dir = "../driver_images/image_images/";

if(isset($_POST['user_post'])) {	
	$name = $_POST['name'];
	$email = $_POST['email'];
	$phone = $_POST['phone'];
	$qualification = $_POST['qualification'];
	$country = $_POST['country'];
	$manufacturing = $_POST['manufacturing_unit_id'];
	$state = $_POST['state'];
	$city = $_POST['city'];
	$address = $_POST['address'];
	$zip = $_POST['zip'];
	$doj = $_POST['doj'];
	$is_active = $_POST['is_active'];
	$license = $_FILES['license']['name'];
	$passport = $_FILES['passport']['name'];
	$idproof = $_FILES['idproof']['name'];
	$image = $_FILES['image']['name'];
	$date_added = date("Y-m-d H:i:s");
	$date_updated = date("Y-m-d H:i:s");

	if(isset($_POST['id']) && $_POST['id'] > 0) {
		$id = $_POST['id'];
		$qry = "UPDATE drivers SET name = '".safeTextIn($name)."', manufacturing_unit_id = '$manufacturing', email = '".safeTextIn($email)."', phone = '".safeTextIn($phone)."', qualification = '".safeTextIn($qualification)."', country = '".safeTextIn($country)."', state = '".safeTextIn($state)."', city = '".safeTextIn($city)."', address = '".safeTextIn($address)."', zip = '".safeTextIn($zip)."', doj = '$doj', is_active = '$is_active', date_updated = '$date_updated' WHERE id = '$id'";
		if(mysqli_query($GLOBALS['conn'],$qry)){		
			//header('Location: manage_blog.php?resp=updatesucc');
		}
	}
	else {
		//$user_pass = $_POST['user_pass'];

		$qry = "INSERT INTO drivers (name, manufacturing_unit_id, email, phone, qualification, country, state, city, address, zip, doj, is_active, date_added) VALUES ('".safeTextIn($name)."', '$manufacturing', '".safeTextIn($email)."', '".safeTextIn($phone)."', '".safeTextIn($qualification)."', '".safeTextIn($country)."', '".safeTextIn($state)."', '".safeTextIn($city)."', '".safeTextIn($address)."', '".safeTextIn($zip)."', '$doj', '$is_active', '$date_added')";
		//echo $qry;
		if(mysqli_query($GLOBALS['conn'],$qry)){		
			$id = mysqli_insert_id();
		}
	}

	if (file_exists($_FILES['license']['tmp_name'])) {		
		$userfile_name = "_license";
		$objectname = "license";
		if (isset($_FILES[$objectname]['name'])) {
			$path = $license_img_dir;
			$new_width = 340;
			$new_height = 330;
			$userfile_name = saveCategoryImages($userfile_name, $objectname, $path, $new_height, $new_width);
			$qry = "UPDATE drivers SET license = '$userfile_name' WHERE id = '$id'";
			if(mysqli_query($GLOBALS['conn'],$qry)){
			}			
		}
	}
	if (file_exists($_FILES['passport']['tmp_name'])) {		
		$userfile_name = "_passport";
		$objectname = "passport";
		if (isset($_FILES[$objectname]['name'])) {
			$path = $passport_img_dir;
			$new_width = 340;
			$new_height = 330;
			$userfile_name = saveCategoryImages($userfile_name, $objectname, $path, $new_height, $new_width);
			$qry = "UPDATE drivers SET passport = '$userfile_name' WHERE id = '$id'";
			if(mysqli_query($qry)){
			}			
		}
	}
	if (file_exists($_FILES['idproof']['tmp_name'])) {		
		$userfile_name = "_idproof";
		$objectname = "idproof";
		if (isset($_FILES[$objectname]['name'])) {
			$path = $idproof_img_dir;
			$new_width = 340;
			$new_height = 330;
			$userfile_name = saveCategoryImages($userfile_name, $objectname, $path, $new_height, $new_width);
			$qry = "UPDATE drivers SET idproof = '$userfile_name' WHERE id = '$id'";
			if(mysqli_query($qry)){
			}			
		}
	}
	if (file_exists($_FILES['image']['tmp_name'])) {		
		$userfile_name = "_image";
		$objectname = "image";
		if (isset($_FILES[$objectname]['name'])) {
			$path = $image_img_dir;
			$new_width = 340;
			$new_height = 330;
			$userfile_name = saveCategoryImages($userfile_name, $objectname, $path, $new_height, $new_width);
			$qry = "UPDATE drivers SET image = '$userfile_name' WHERE id = '$id'";
			if(mysqli_query($qry)){
			}			
		}
	}
//die;
	redirect('manage_drivers.php?resp=addsucc');
}


if(isset($_GET['act']) && $_GET['act'] != '' && isset($_GET['id']) && $_GET['id'] > 0) {	
	$action = $_GET['act'];
	$id = $_GET['id'];
	if($action == 'edit') {
		$page_title = 'Edit User';
		$edit_query = "SELECT * FROM drivers WHERE id = '$id'";
		$run_edit = mysqli_query($GLOBALS['conn'],$edit_query);		
		while ($edit_row = mysqli_fetch_array($run_edit)) {
			$id = $edit_row['id'];
			$name = $edit_row['name'];
			$email = $edit_row['email'];
			$phone = $edit_row['phone'];
			$manufacturing = $edit_row['manufacturing_unit_id'];
			$qualification = $edit_row['qualification'];
			$country = $edit_row['country'];
			$state = $edit_row['state'];
			$city = $edit_row['city'];
			$address = $edit_row['address'];
			$zip = $edit_row['zip'];
			$license = $edit_row['license'];
			$passport = $edit_row['passport'];
			$idproof = $edit_row['idproof'];
			$image = $edit_row['image'];
			$doj = $edit_row['doj'];
			$is_active = $edit_row['is_active'];
		}
	}
}

function getManufacturingList()
{
	$manufacturing = array();
	$query = "SELECT * FROM locations_manufacturing_units ORDER BY id ASC";
	$run = mysqli_query($query);
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
									<span>Manage Staffs</span>
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
											<label class="col-sm-3 control-label">Staff Name</label>
											<div class="col-sm-5">
												<input type="text" class="form-control" name="name" id="name" value="<?php echo safeTextOut(htmlspecialchars($name)); ?>" />
											</div>
										</div>
										<!--<div class="form-group">
											<label class="col-sm-3 control-label">Manufacturing Unit</label>
											<div class="col-sm-5">												
												<select class="form-control" name="manufacturing_unit_id" id="manufacturing_unit_id">
													<option value="">-- Select a Unit --</option>
													<?php 
														$manu_list = getManufacturingList();
														foreach ($manu_list as $key => $value) {
															$selected = ($key == $manufacturing) ? "selected = selected" : "";
															echo "<option value=\"".$key."\" ".$selected.">".ucfirst($value)."</option>";
														}
													?>
												</select>
											</div>
										</div>-->
										<input type="hidden" name="manufacturing_unit_id" id="manufacturing_unit_id" value="1" />
										<div class="form-group">
											<label class="col-sm-3 control-label">Email</label>
											<div class="col-sm-5">
												<input type="text" class="form-control" name="email" id="email" value="<?php echo safeTextOut(htmlspecialchars($email)); ?>" />
											</div>
										</div>
										<div class="form-group">
											<label class="col-sm-3 control-label">Phone</label>
											<div class="col-sm-5">
												<input type="text" class="form-control" name="phone" id="phone" value="<?php echo safeTextOut(htmlspecialchars($phone)); ?>" />
											</div>
										</div>
										<div class="form-group">
											<label class="col-sm-3 control-label">Qualification</label>
											<div class="col-sm-5">
												<input type="text" class="form-control" name="qualification" id="qualification" value="<?php echo safeTextOut(htmlspecialchars($qualification)); ?>" />
											</div>
										</div>										
										<div class="form-group">
											<label class="col-sm-3 control-label">Country</label>
											<div class="col-sm-5">
												<input type="text" class="form-control" name="country" id="country" value="<?php echo safeTextOut(htmlspecialchars($country)); ?>" />
											</div>
										</div>
										<div class="form-group">
											<label class="col-sm-3 control-label">State</label>
											<div class="col-sm-5">
												<input type="text" class="form-control" name="state" id="state" value="<?php echo safeTextOut(htmlspecialchars($state)); ?>" />
											</div>
										</div>
										<div class="form-group">
											<label class="col-sm-3 control-label">City</label>
											<div class="col-sm-5">
												<input type="text" class="form-control" name="city" id="city" value="<?php echo safeTextOut(htmlspecialchars($city)); ?>" />
											</div>
										</div>
										<div class="form-group">
											<label class="col-sm-3 control-label">Address</label>
											<div class="col-sm-5">
												<input type="text" class="form-control" name="address" id="address" value="<?php echo safeTextOut(htmlspecialchars($address)); ?>" />
											</div>
										</div>
										<div class="form-group">
											<label class="col-sm-3 control-label">Zip</label>
											<div class="col-sm-5">
												<input type="text" class="form-control" name="zip" id="zip" value="<?php echo safeTextOut(htmlspecialchars($zip)); ?>" />
											</div>
										</div>
										<div class="form-group">
											<label class="col-sm-3 control-label" for="form-styles">License</label>
											<div class="col-sm-5">
												<div class="row">
													<div class="col-sm-12">
														<input type="file" class="form-control" name="license" />
														<p><small>Dimension: 340 X 330</small></p>
													</div>
												</div>
												<?php if(isset($action) && $action == 'edit' && $license !='') { ?>
												<div class="row">
													<div class="col-sm-12">
														<p><small>License</small></p>
														<div>
															  <img src="<?php echo $license_img_dir.$license; ?>" width="100" height="80" alt="<?php echo $name; ?>" />
														</div>
													</div>
												</div>
												<?php } ?>
											</div>
										</div>
										<div class="form-group">
											<label class="col-sm-3 control-label" for="form-styles">Passport</label>
											<div class="col-sm-5">
												<div class="row">
													<div class="col-sm-12">
														<input type="file" class="form-control" name="passport" />
														<p><small>Dimension: 340 X 330</small></p>
													</div>
												</div>
												<?php if(isset($action) && $action == 'edit' && $passport !='') { ?>
												<div class="row">
													<div class="col-sm-12">
														<p><small>Passport</small></p>
														<div>
															  <img src="<?php echo $passport_img_dir.$passport; ?>" width="100" height="80" alt="<?php echo $name; ?>" />
														</div>
													</div>
												</div>
												<?php } ?>
											</div>
										</div>
										<div class="form-group">
											<label class="col-sm-3 control-label" for="form-styles">Idproof</label>
											<div class="col-sm-5">
												<div class="row">
													<div class="col-sm-12">
														<input type="file" class="form-control" name="idproof" />
														<p><small>Dimension: 340 X 330</small></p>
													</div>
												</div>
												<?php if(isset($action) && $action == 'edit' && $idproof !='') { ?>
												<div class="row">
													<div class="col-sm-12">
														<p><small>Idproof</small></p>
														<div>
															  <img src="<?php echo $idproof_img_dir.$idproof; ?>" width="100" height="80" alt="<?php echo $name; ?>" />
														</div>
													</div>
												</div>
												<?php } ?>
											</div>
										</div>
										<div class="form-group">
											<label class="col-sm-3 control-label" for="form-styles">Staff Image</label>
											<div class="col-sm-5">
												<div class="row">
													<div class="col-sm-12">
														<input type="file" class="form-control" name="image" />
														<p><small>Dimension: 340 X 330</small></p>
													</div>
												</div>
												<?php if(isset($action) && $action == 'edit' && $image !='') { ?>
												<div class="row">
													<div class="col-sm-12">
														<p><small>Staff Image</small></p>
														<div>
															  <img src="<?php echo $image_img_dir.$image; ?>" width="100" height="80" alt="<?php echo $name; ?>" />
														</div>
													</div>
												</div>
												<?php } ?>
											</div>
										</div>
										<div class="form-group">
											<label class="col-sm-3 control-label">Date of Joining</label>
											<div class="col-sm-5">
												<input type="text" class="form-control" name="doj" id="doj" value="<?php echo safeTextOut(htmlspecialchars($doj)); ?>" />
											</div>
										</div>
										<div class="form-group">
											<label class="col-sm-3 control-label">Status</label>
											<div class="col-sm-5">												
												<select class="form-control" name="is_active" id="is_active">
													<option value="1" <?php echo ($is_active == "1") ? "selected = selected" : ""; ?>>Active</option>
													<option value="0" <?php echo ($is_active == "0") ? "selected = selected" : ""; ?>>Inactive</option>
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
												<a href="manage_drivers.php" class="btn btn-primary">Cancel</a>
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
	$('#doj').datepicker({dateFormat: 'yy-mm-dd'});
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