<?php 
session_start();
include("../functions.php");
include_once("../config.php");
chkAdminLoggedIn();
connect_dre_db();
$id='';
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
$image_img_dir = "../driver_images/";

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
	$doj = date('Y-m-d',strtotime($_POST['doj']));
	//$is_active = $_POST['is_active'];
	$license = $_FILES['license']['name'];
	$passport = $_FILES['passport']['name'];
	$idproof = $_FILES['idproof']['name'];
	$image = $_FILES['image']['name'];
	$date_added = date("Y-m-d H:i:s");
	$date_updated = date("Y-m-d H:i:s");
	//print_r($_FILES);die;

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
		 $id = mysqli_insert_id($GLOBALS['conn']);
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
			//echo $qry; die;
			if(mysqli_query($GLOBALS['conn'],$qry)){
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
			if(mysqli_query($GLOBALS['conn'],$qry)){
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
			if(mysqli_query($GLOBALS['conn'],$qry)){
			}			
		}
	}
//die;
	redirect('drivers.php?resp=updatesucc');
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
//echo $id;
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
  <style>
	.error{ color: red; }
  </style>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Add Driver
        <!--<small>Optional description</small>-->
      </h1>      
	  <ol class="breadcrumb">
        <li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="drivers.php">Drivers</a></li>
        <li class="active">Add Driver</li>
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
		   <form action="drivers_add.php" method="post" id="driverform" enctype="multipart/form-data">
		   <input type="hidden" name="user_post" value="1" />
		   <input type="hidden" name="id" id="id" value="<?php echo $id; ?>" />
		   <input type="hidden" name="manufacturing_unit_id" id="manufacturing_unit_id" value="1" />
            <div class="col-md-6">		
				<div class="form-group">
					<label>Driver Name</label>
					<input type="text" class="form-control" placeholder="Enter ..." name="name" id="name" value="<?php echo safeTextOut(htmlspecialchars($name)); ?>" required />
				</div>					
				 <div class="form-group">
			    <label>Email</label>
			    <input type="text" class="form-control" placeholder="Enter ..." name="email" id="email" value="<?php echo safeTextOut(htmlspecialchars($email)); ?>"/>
			  </div>
				  <div class="form-group">
			    <label>Phone</label>
			    <input type="text" class="form-control" required placeholder="Enter ..." name="phone" id="phone" value="<?php echo safeTextOut(htmlspecialchars($phone)); ?>"/>
			  </div>
			    <div class="form-group">
			    <label>Qualification</label>
			    <input type="text" class="form-control" placeholder="Enter ..." name="qualification" id="qualification" value="<?php echo safeTextOut(htmlspecialchars($qualification)); ?>"/>
			  </div>
			   <div class="form-group">
			    <label>Country</label>
			    <input type="text" class="form-control" placeholder="Enter ..." name="country" id="country" value="<?php echo safeTextOut(htmlspecialchars($country)); ?>"/>
			  </div>
			   <div class="form-group">
			    <label>State</label>
			    <input type="text" class="form-control" placeholder="Enter ..." name="state" id="state" value="<?php echo safeTextOut(htmlspecialchars($state)); ?>"/>
			  </div>
			    <div class="form-group">
			    <label>City</label>
			    <input type="text" class="form-control" placeholder="Enter ..." name="city" id="city" value="<?php echo safeTextOut(htmlspecialchars($city)); ?>"/>
			  </div>
			   <div class="form-group">
			    <label>Address</label>
			    <input type="text" class="form-control" placeholder="Enter ..." name="address" id="address" value="<?php echo safeTextOut(htmlspecialchars($address)); ?>"/>
			  </div>
			  <div class="form-group">
			    <label>Zip</label>
			    <input type="text" class="form-control" placeholder="Enter ..." name="zip" id="zip" value="<?php echo safeTextOut(htmlspecialchars($address)); ?>"/>
				</div>
				<div class="form-group">
					<label class="control-label" for="form-styles">Driver Image</label>
					<div class="row">
						<div class="col-sm-12">
							<input type="file" class="form-control" name="image" />
							<p><small>Dimension: 340 X 330</small></p>
						</div>
					</div>
					<?php if(isset($action) && $action == 'edit') { ?>
					<div class="row">
						<div class="col-sm-12">
							<p><small>Image</small></p>
							<div>
								<img src="<?php echo $image_img_dir.$image; ?>" width="100" height="80" alt="<?php echo $name; ?>" />
							</div>
						</div>
					</div>
					<?php } ?>
				</div>
				<div class="form-group">
					<label class="control-label" for="form-styles">Idproof</label>
					<div class="row">
						<div class="col-sm-12">
							<input type="file" class="form-control" name="idproof" />
							<p><small>Dimension: 340 X 330</small></p>
						</div>
					</div>
					<?php if(isset($action) && $action == 'edit') { ?>
					<div class="row">
						<div class="col-sm-12">
							<p><small>Idproof</small></p>
							<div>
								<img src="<?php echo $idproof_img_dir.$idproof; ?>" width="100" height="80" alt="<?php echo $idproof; ?>" />
							</div>
						</div>
					</div>
					<?php } ?>
				</div>
				<div class="form-group">
					<label class="control-label" for="form-styles">Passport</label>
					<div class="row">
						<div class="col-sm-12">
							<input type="file" class="form-control" name="passport" />
							<p><small>Dimension: 340 X 330</small></p>
						</div>
					</div>
					<?php if(isset($action) && $action == 'edit') { ?>
					<div class="row">
						<div class="col-sm-12">
							<p><small>Passport</small></p>
							<div>
								<img src="<?php echo $passport_img_dir.$passport; ?>" width="100" height="80" alt="<?php echo $passport; ?>" />
							</div>
						</div>
					</div>
					<?php } ?>
				</div>
				<div class="form-group">
					<label class="control-label" for="form-styles">License</label>
					<div class="row">
						<div class="col-sm-12">
							<input type="file" class="form-control" name="license" />
							<p><small>Dimension: 340 X 330</small></p>
						</div>
					</div>
					<?php if(isset($action) && $action == 'edit') { ?>
					<div class="row">
						<div class="col-sm-12">
							<p><small>License</small></p>
							<div>
								<img src="<?php echo $license_img_dir.$license; ?>" width="100" height="80" alt="<?php echo $license; ?>" />
							</div>
						</div>
					</div>
					<?php } ?>
				</div>
				 <div class="form-group">
			    <label>Date of Joining</label>
			    <input type="text" class="form-control datepicker" placeholder="Enter ..." name="doj" id="doj" value="<?php echo $doj; ?>" required/>
				</div>

				<!-- /.form-group -->
				<div class="form-group">
					<?php 
						if(isset($action) && $action == 'edit') { 
						?>
					<button type="submit" class="btn btn-primary">Update</button>
					<a href="drivers.php" class="btn btn-primary">Cancel</a>
					<?php } 
						else { 
						?>
					<button type="submit" class="btn btn-primary">Add Driver</button>
					<?php 
						} 
				?>
				</div>
            </div>
            <!-- /.col -->
		   <!-- /.col -->			
            </form>			
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
</div>
<!-- ./wrapper -->

<!-- REQUIRED JS SCRIPTS -->

<?php include("common/footer-scripts.php"); ?>

<!-- Optionally, you can add Slimscroll and FastClick plugins.
     Both of these plugins are recommended to enhance the
     user experience. -->
<script type="text/javascript">
	/*$('#driverform').bootstrapValidator({
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
		// $("#doj").datepicker();
	});*/
	 $("#driverform").validate();
</script>
</body>
</html>