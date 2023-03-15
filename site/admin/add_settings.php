<?php 
session_start();
include("../functions.php");
include_once("../config.php");
chkAdminLoggedIn();
connect_dre_db();
$CLIENT_LOGO = '';
$action = 'add';

if($_POST) {
	foreach($_POST as $key => $ps) {
		$qry = "UPDATE settings SET set_value = '".$ps."' WHERE set_name = '$key'";
		if(mysqli_query($GLOBALS['conn'],$qry)){		
			//header('Location: manage_page.php?resp=updatesucc');
		}
	}
	//Image to string
	$CLIENT_LOGO = $_FILES['CLIENT_LOGO']['name'];
    $image_tmp = $_FILES['CLIENT_LOGO']['tmp_name'];
	if (file_exists($_FILES['CLIENT_LOGO']['tmp_name'])) {
	    $CLIENT_LOGO = $_FILES['CLIENT_LOGO']['name'];
        $image_tmp = $_FILES['CLIENT_LOGO']['tmp_name'];
	    $data = file_get_contents($image_tmp);
        $CLIENT_LOGO = base64_encode($data);
    	//print_r($CLIENT_LOGO); die;
			$qry = "UPDATE settings SET set_value = '".$CLIENT_LOGO."' WHERE set_name = 'CLIENT_LOGO'";
			if(mysqli_query($GLOBALS['conn'], $qry)){
			}
	}
	
		
	redirect('add_settings.php');
}

$query = "SELECT * FROM settings";
$result = mysqli_query($GLOBALS['conn'], $query);
if ($result) {
	$result_arr = array();
	while ($row = mysqli_fetch_assoc($result)) {
	   $result_arr[] = $row;			
	}
}
//echo '<pre>'; print_r($result_arr);
foreach($result_arr as $edit_row) {//echo $edit_row['set_name']; die;
	$set_name[] = $edit_row['set_name'];
	$set_value[] = $edit_row['set_value'];
}
$final_array = array_combine($set_name, $set_value);
//echo '<pre>'; print_r($final_array);
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
							<li><a href="javascript:void(0); ">Add Settings</a></li>
						</ol>
					</div>
				</div>
				
				<div class="row">
					<div class="col-xs-12 col-sm-12">
						<div class="box">
							<div class="box-header">
								<div class="box-name">
									<i class="fa fa-search"></i>
									<span>Manage Settings</span>
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
								<form id="pageForm" method="post" action="" class="form-horizontal" enctype="multipart/form-data">
									<!-- <input type="hidden" name="settings_post" value="1" /> -->
									<fieldset>
										<legend>Add Settings</legend>
										<div class="form-group">
											<label class="col-sm-3 control-label">Client name</label>
											<div class="col-sm-5">
												<input maxlength="33" type="text" class="form-control" name="CLIENT_NAME" value="<?php echo $final_array['CLIENT_NAME']; ?>" />
											</div>
										</div>
										<div class="form-group">
											<label class="col-sm-3 control-label">Client address</label>
											<div class="col-sm-5">
												<input maxlength="60" type="text" class="form-control" name="CLIENT_ADDRESS" value="<?php echo $final_array['CLIENT_ADDRESS']; ?>" />
											</div>
										</div>
										<div class="form-group">
											<label class="col-sm-3 control-label">Client number</label>
											<div class="col-sm-5">
												<input maxlength="50" type="text" class="form-control" name="CLIENT_NUMBER" value="<?php echo $final_array['CLIENT_NUMBER']; ?>" />
												<p style="font-style: italic;">(ex: +971 1234566789, +971 1234566789)</p>
											</div>
										</div>
										<div class="form-group">
											<label class="col-sm-3 control-label">Client VAT</label>
											<div class="col-sm-5">
												<input type="text" class="form-control" name="CLIENT_WEBSITE" value="<?php echo $final_array['CLIENT_WEBSITE']; ?>" />
											</div>
										</div>
										<div class="form-group">
											<label class="col-sm-3 control-label">Recipit pre</label>
											<div class="col-sm-5">
												<input maxlength="8" type="text" class="form-control" name="RECIPT_PRE" value="<?php echo $final_array['RECIPT_PRE']; ?>" />
												<p style="font-style: italic;">(ex: CLT-)</p>
											</div>
										</div>
										<div class="form-group">
											<label class="col-sm-3 control-label">Currency</label>
											<div class="col-sm-5">
												<input maxlength="6" type="text" class="form-control" name="CURRENCY" value="<?php echo $final_array['CURRENCY']; ?>" />
												<p style="font-style: italic;">(ex: AED)</p>
											</div>
										</div>
										<div class="form-group">
											<label class="col-sm-3 control-label">Bill footer</label>
											<div class="col-sm-5">
												<input maxlength="60" type="text" class="form-control" name="BILL_FOOTER" value="<?php echo $final_array['BILL_FOOTER']; ?>" />
											</div>
										</div>
										<div class="form-group">
											<label class="col-sm-3 control-label">SMS Owner number</label>
											<div class="col-sm-5">
												<input type="text" maxlength="15" class="form-control" name="OWNER_NUM" value="<?php echo $final_array['OWNER_NUM']; ?>" />
												<p style="font-style: italic;">(ex: +971 1234566789 - Make sure country code)</p>
											</div>
										</div>
										<div class="form-group">
											<label class="col-sm-3 control-label" for="form-styles">Client Logo</label>
											<div class="col-sm-5">
												<div class="row">
													<div class="col-sm-12">
														<input type="file" class="form-control" name="CLIENT_LOGO" />
													</div>
												</div>
												<div class="row">
													<div class="col-sm-12">
														<p><small>Client Logo</small></p>
														<div>
															  <img src="data:image/png;base64,<?php echo $final_array['CLIENT_LOGO']; ?>" width="100" height="80" alt="<?php echo $final_array['CLIENT_NAME']; ?>" />
														</div>
													</div>
												</div>
											</div>
										</div>	
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