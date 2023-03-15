<?php 
session_start();
include("../functions.php");
include_once("../config.php");
chkAdminLoggedIn();
connect_dre_db();

$cat_id = '';
$parent_id = 0;
$name = '';
$price = '';
$items_details = '';
$weight = '';
$unit = '';
$image = '';
$barcode_id = '';
$cgst = '';
$sgst = '';
$stock = '';
$manuf_date = '';
$inward_date = '';
$expiry_date = '';
$action = 'add';
$update_img_tbl = false;
$items_img_dir = "../item_images/";

//$barcode_id = randomString();

if(isset($_POST['items_post'])) {
	$name = $_POST['name'];
	//$sgst = $_POST['sgst'];
	//$cgst = $_POST['cgst'];
	$barcode_id = $_POST['barcode_id'];
    $cat_id = $_POST['cat_id'];
	//$category_slug = getnamewhere('item_category', 'category_slug', 'WHERE id = '.$cat_id);
	//$weight_unit = explode('-', $category_slug);
	$unit = $_POST['unit'];
	$price = $_POST['price'];
	$weight = $_POST['weight'];
	//$manuf_date = $_POST['manuf_date'];
	//$expiry_date = $_POST['expiry_date'];
//	$inward_date = $_POST['inward_date'];
	$image = $_FILES['image']['name'];
	
	if(isset($_POST['id']) && $_POST['id'] > 0) {
		$id = $_POST['id'];		
		$qry = "UPDATE items SET barcode_id= '$barcode_id', name = '".safeTextIn($name)."', price = '".safeTextIn($price)."', weight = '".safeTextIn($weight)."', unit = '".safeTextIn($unit)."' WHERE id = '$id'";
		//	echo $qry; die;
		if(mysqli_query($GLOBALS['conn'], $qry)){
		}
	}
	else {
		/*$result_rad = mysqli_query($GLOBALS['conn'], "SELECT `barcode_id` FROM `items` WHERE `barcode_id` = '".$barcode_id."'");
		if(mysqli_num_rows($result_rad)) {
			$barcode_id = randomString();
			$result_rad_long= mysqli_query($GLOBALS['conn'], "SELECT `barcode_id` FROM `items` WHERE `barcode_id` = '".$barcode_id."'");
			if(mysqli_num_rows($result_rad_long)) {
				echo"<script>
				alert('Something went worng...');
				</script>";
			}
		} else {*/
			$stock = $_POST['stock'];
			$qry = "INSERT INTO items (cat_id, name, price, weight, unit, barcode_id, stock) VALUES ('$cat_id', '".safeTextIn($name)."', '".safeTextIn($price)."', '".safeTextIn($weight)."', '".safeTextIn($unit)."', '".safeTextIn($barcode_id)."', '$stock')";
			//echo $qry; die;
			if(mysqli_query($GLOBALS['conn'], $qry)){		
				$id = mysqli_insert_id($GLOBALS['conn']);
			}
		//}
	}

	if (file_exists($_FILES['image']['tmp_name'])) {		
		$userfile_name = "_items";
		$objectname = "image";
		if (isset($_FILES[$objectname]['name'])) {
			$path = $items_img_dir;
			$new_width = 340;
			$new_height = 330;
			$userfile_name = saveProductItemImage($userfile_name, $objectname, $path, $new_height, $new_width);
			$qry = "UPDATE items SET image = '$userfile_name' WHERE id = '$id'";
			if(mysqli_query($GLOBALS['conn'], $qry)){
			}			
		}
	}
		//die;
	redirect('manage_items.php?resp=addsucc');
}

if(isset($_GET['act']) && $_GET['act'] != '' && isset($_GET['id']) && $_GET['id'] > 0) {	
	$action = $_GET['act'];
	$id = $_GET['id'];
	if($action == 'edit') {
		$edit_query = "SELECT * FROM items WHERE id = '$id'";
		$run_edit = mysqli_query($GLOBALS['conn'], $edit_query);		
		while ($edit_row = mysqli_fetch_array($run_edit)) {
			$id = $edit_row['id'];
			$cat_id = $edit_row['cat_id'];
			$name = $edit_row['name'];
			$price = $edit_row['price'];
			//$sgst = $edit_row['SGST'];
			$image = $edit_row['image'];
			//$cgst = $edit_row['CGST'];
			$barcode_id = $edit_row['barcode_id'];
			$unit = $edit_row['unit'];
			$weight = $edit_row['weight'];
			$stock = $edit_row['stock'];
			//$manuf_date = $edit_row['manuf_date'];
			//$expiry_date = $edit_row['expiry_date'];
			//$inward_date = $edit_row['inward_date'];
		}
	}
}

function getCategorieList()
{
	$cat = array();
	$query = "SELECT * FROM item_category ORDER BY id ASC";
	$run = mysqli_query($GLOBALS['conn'], $query);
	while($row = mysqli_fetch_array($run)) {
		$cat_id = $row['id'];
		$cat[$cat_id] = $row['category_title'];
	}
	return $cat;	
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
							<li><a href="javascript:void(0); ">Add Products</a></li>
						</ol>
					</div>
				</div>
				
				<div class="row">
					<div class="col-xs-12 col-sm-12">
						<div class="box">
							<div class="box-header">
								<div class="box-name">
									<i class="fa fa-search"></i>
									<span>Manage Products</span>
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
										<legend>Add Products</legend>									
										<?php if(isset($_GET['sts']) && $_GET['sts'] == 'invalid') {?>
										<p style="color:red">Invalid file format</p>
										<?php } ?>
										<div class="form-group">
											<label class="col-sm-3 control-label">Name</label>
											<div class="col-sm-5">
												<input type="text" class="form-control" name="name" id="name" value="<?php echo safeTextOut(htmlspecialchars($name)); ?>" required/>
											</div>
										</div>
										<div class="form-group">
											<label class="col-sm-3 control-label">Price</label>
											<div class="col-sm-5">
												<input type="text" class="form-control" name="price" id="price" value="<?php echo safeTextOut(htmlspecialchars($price)); ?>" />
											</div>
										</div>
										<div class="form-group">
											<label class="col-sm-3 control-label">Weight</label>
											<div class="col-sm-5">
												<input type="text" class="form-control" name="weight" id="weight" value="<?php echo safeTextOut(htmlspecialchars($weight)); ?>" />
											</div>
										</div>
										<div class="form-group">
											<label class="col-sm-3 control-label">Unit</label>
											<div class="col-sm-5">												
												<select class="form-control" name="unit" id="unit">
													<option value="">-- Select a Unit --</option>
													<?php 
														$unit_list = getUnitList();
														foreach ($unit_list as $key => $value) {
															$selected = ($key == $unit) ? "selected = selected" : "";
															echo "<option value=\"".$key."\" ".$selected.">".ucfirst($value)."</option>";
														}
													?>
												</select>
											</div>
										</div>
										<div class="form-group">
											<label class="col-sm-3 control-label">Category</label>
											<div class="col-sm-5">												
												<select class="form-control" name="cat_id" id="cat_id">
													<option value="">-- Select a Category --</option>
													<?php 
														$cat_list = getCategorieList();
														foreach ($cat_list as $key => $value) {
															$selected = ($key == $cat_id) ? "selected = selected" : "";
															echo "<option value=\"".$key."\" ".$selected.">".$value."</option>";
														}
													?>
												</select>
											</div>
										</div>
										<div class="form-group">
											<label class="col-sm-3 control-label" for="form-styles">Image</label>
											<div class="col-sm-5">
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
															  <img src="<?php echo $items_img_dir.$image; ?>" width="100" height="80" alt="<?php echo $name; ?>" />
														</div>
													</div>
												</div>
												<?php } ?>
											</div>
										</div>																				
										<!--<div class="form-group">-->
										<!--	<label class="col-sm-3 control-label">CGST %</label>-->
										<!--	<div class="col-sm-5">-->
										<!--		<input type="text" class="form-control" name="cgst" id="cgst" value="<?php echo safeTextOut(htmlspecialchars($cgst)); ?>" />-->
										<!--	</div>-->
										<!--</div>-->
										<!--<div class="form-group">-->
										<!--	<label class="col-sm-3 control-label">SGST %</label>-->
										<!--	<div class="col-sm-5">-->
										<!--		<input type="text" class="form-control" name="sgst" id="sgst" value="<?php echo safeTextOut(htmlspecialchars($sgst)); ?>" />-->
										<!--	</div>-->
										<!--</div>-->
										<!--<div class="form-group">-->
										<!--	<label class="col-sm-3 control-label">Manuf date</label>-->
										<!--	<div class="col-sm-5">-->
										<!--		<input type="text" class="form-control" name="manuf_date" id="manuf_date" value="<?php echo safeTextOut(htmlspecialchars($manuf_date)); ?>" required/>-->
										<!--	</div>-->
										<!--</div>-->
										<!--<div class="form-group">-->
										<!--	<label class="col-sm-3 control-label">Expiry date</label>-->
										<!--	<div class="col-sm-5">-->
										<!--		<input type="text" class="form-control" name="expiry_date" id="expiry_date" value="<?php echo safeTextOut(htmlspecialchars($expiry_date)); ?>" required/>-->
										<!--	</div>-->
										<!--</div>-->
										<!--<div class="form-group">-->
										<!--	<label class="col-sm-3 control-label">Inward date</label>-->
										<!--	<div class="col-sm-5">-->
										<!--		<input type="text" class="form-control" name="inward_date" id="inward_date" value="<?php echo safeTextOut(htmlspecialchars($inward_date)); ?>" required/>-->
										<!--	</div>-->
										<!--</div>-->
										<div class="form-group">
											<label class="col-sm-3 control-label">Stock</label>
											<div class="col-sm-5">
												<input type="text" <?php if(isset($action) && $action == 'edit') {echo "disabled";} ?> class="form-control" name="stock" id="stock" value="<?php echo safeTextOut(htmlspecialchars($stock)); ?>" />
											</div>
										</div>
										<div class="form-group">
											<label class="col-sm-3 control-label">Barcode num</label>
											<div class="col-sm-5">
												<input type="text" class="form-control" name="barcode_id" id="barcode_id" value="<?php echo safeTextOut(htmlspecialchars($barcode_id)); ?>" />
											</div>
										</div>
										
									</fieldset>									
									<div class="form-group">
										<div class="col-sm-9 col-sm-offset-3">
											<?php 
												if(isset($action) && $action == 'edit') {
											?>
												<button type="submit" class="btn btn-primary">Update</button>
												<a href="manage_items.php" class="btn btn-primary">Cancel</a>
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
<script src="js/validation.js"></script>
<!-- All functions for this theme + document.ready processing -->
<script src="js/devoops.js"></script>

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
//$( "#manuf_date" ).datepicker({dateFormat: 'yy-mm-dd'});
//$( "#expiry_date" ).datepicker({dateFormat: 'yy-mm-dd'});
//$( "#inward_date" ).datepicker({dateFormat: 'yy-mm-dd'});

$("#itemsForm").validate();



</script>
</body>
</html>