<?php 
session_start();
include("../functions.php");
include_once("../config.php");
chkAdminLoggedIn();
connect_dre_db();
$err_msg = '';
$page_title = 'Add User';
$user_id = '';
$user_name = '';
$user_pass = '';
$role_id = '';
$manufacturing_unit_id = '';
$shop_id = '';
$first_name = '';
$last_name = '';
$email = '';
$phone = '';
$is_active = '';
$action = 'add';
$action_id = '';

if(isset($_POST['user_post'])) {
	$user_name = $_POST['user_name'];	
	$role_id = $_POST['role_id'];
	$manufacturing_unit_id = 1;
	$shop_id = 1;
	$first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $is_active = $_POST['is_active'];
	//$action_id = implode(",", $_POST['action_id']);

	if(isset($_POST['user_id']) && $_POST['user_id'] > 0) {
		$user_id = $_POST['user_id'];
		$qry = "UPDATE users SET user_name = '".safeTextIn($user_name)."', role_id = '$role_id',manufacturing_unit_id = '$manufacturing_unit_id', shop_id = '$shop_id', first_name = '".safeTextIn($first_name)."', last_name = '".safeTextIn($last_name)."', email = '".safeTextIn($email)."', phone = '$phone', is_active = '$is_active' WHERE id = '$user_id'";
		if(mysqli_query($GLOBALS['conn'], $qry)){		
			//header('Location: manage_blog.php?resp=updatesucc');
		}
	}
	else {
		$user_pass = $_POST['user_pass'];

		$qry = "INSERT INTO users (user_name, user_pass, role_id, manufacturing_unit_id, shop_id, first_name, last_name, email, phone, is_active) VALUES ('".safeTextIn($user_name)."', '".md5(safeTextIn($user_pass))."', '$role_id', '$manufacturing_unit_id', '$shop_id', '".safeTextIn($first_name)."', '".safeTextIn($last_name)."', '".safeTextIn($email)."', '$phone', '$is_active')";
		//echo $qry;
		//if(mysqli_query($GLOBALS['conn'], $qry)){		
			//$user_id = mysqli_insert_id();
		//}
	}
	redirect('manage_user.php?resp=addsucc');
}

if(isset($_POST['change_pass_post'])) {	
	$user_id = $_POST['user_id'];
	//$old_pass = $_POST['old_pass'];
	$new_pass = $_POST['new_pass'];
	$confirm_new_pass = $_POST['confirm_new_pass'];
	
	$where = " WHERE id = '".$user_id."'";
	$old_pass_db = getnamewhere('users', 'user_pass', $where);
	
	if($new_pass != $confirm_new_pass)
	{
		$err_msg = 'New password and confirm password not match';
	}

	//if(md5($old_pass) != md5($old_pass_db)) {
		//$err_msg = 'Invalid old password';
	//}
	
	if(isset($_POST['user_id']) && $_POST['user_id'] > 0) {
		$qry = "UPDATE users SET user_pass = '".md5(safeTextIn($new_pass))."' WHERE id = '$user_id'";
		if(mysqli_query($GLOBALS['conn'], $qry)){		
			//header('Location: manage_blog.php?resp=updatesucc');
		}
	}
	if($err_msg == '') {
		redirect('manage_user.php?resp=succ');
	}
	else {
		//echo "HI ,ani";die;
	}
}

function getRolesList()
{
	$roles_arr = array();
	$query = "SELECT * FROM users_role ORDER BY id ASC";
	$run = mysqli_query($GLOBALS['conn'], $query);
	while($row = mysqli_fetch_array($run)) {
		$role_id = $row['id'];
		$roles_arr[$role_id] = $row['title'];
	}
	return $roles_arr;	
}

function getManufacturingUnitListDropdown()
{
	$manufacturing_unit_arr = array();
	$query = "SELECT * FROM locations_manufacturing_units ORDER BY id ASC";
	$run = mysqli_query($GLOBALS['conn'], $query);
	while($row = mysqli_fetch_array($run)) {
		$manufacturing_unit_id = $row['id'];
		$id = $row['id'];

		$manufacturing_unit_title_full = $row['name'];		
		
		$manufacturing_unit_arr[$manufacturing_unit_id] = $manufacturing_unit_title_full;
	}
	return $manufacturing_unit_arr;	
}

function getShopListDropdown()
{
	$shop_arr = array();
	$query = "SELECT * FROM locations_shops ORDER BY id ASC";
	$run = mysqli_query($GLOBALS['conn'], $query);
	while($row = mysqli_fetch_array($run)) {
		$shop_id = $row['id'];
		$id = $row['id'];

		$shop_title_full = $row['shop_name'];		
		
		$shop_arr[$shop_id] = $shop_title_full;
	}
	return $shop_arr;	
}

if(isset($_GET['act']) && $_GET['act'] != '' && isset($_GET['id']) && $_GET['id'] > 0) {	
	$action = $_GET['act'];
	$user_id = $_GET['id'];
	if($action == 'edit') {
		$page_title = 'Edit User';
		$edit_query = "SELECT * FROM users WHERE id = '$user_id'";
		$run_edit = mysqli_query($GLOBALS['conn'], $edit_query);		
		while ($edit_row = mysqli_fetch_array($run_edit)) {
			$user_id = $edit_row['id'];
			$user_name = $edit_row['user_name'];
			$user_pass = $edit_row['user_pass'];
			$role_id = $edit_row['role_id'];
			//$manufacturing_unit_id = $edit_row['manufacturing_unit_id'];
			//$shop_id = $edit_row['shop_id'];
			$first_name = $edit_row['first_name'];
			$last_name = $edit_row['last_name'];
			$email = $edit_row['email'];
			$phone = $edit_row['phone'];
			$is_active = $edit_row['is_active'];
			//$action_id = explode(",", $edit_row['user_action']);
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
									<span>Manage User</span>
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
								<form id="userForm" method="post" action="" class="form-horizontal">
									<input type="hidden" name="user_post" value="1" />
									<input type="hidden" name="user_id" id="user_id" value="<?php echo $user_id ?>" />
									<fieldset>
										<legend><?php echo $page_title; ?></legend>
										<div class="form-group">
											<label class="col-sm-3 control-label">User name</label>
											<div class="col-sm-5">
												<input type="text" class="form-control" name="user_name" id="user_name" value="<?php echo safeTextOut(htmlspecialchars($user_name)); ?>" />
											</div>
										</div>
										<?php if(isset($action) && $action == 'add') { ?>
										<div class="form-group">
											<label class="col-sm-3 control-label">Password</label>
											<div class="col-sm-5">
												<input type="text" class="form-control" name="user_pass" id="user_pass" value="" />
											</div>
										</div>
										<?php } ?>
										<div class="form-group">
											<label class="col-sm-3 control-label">First name</label>
											<div class="col-sm-5">
												<input type="text" class="form-control" name="first_name" id="first_name" value="<?php echo safeTextOut($first_name); ?>" />
											</div>
										</div>
										<div class="form-group">
											<label class="col-sm-3 control-label">Last name</label>
											<div class="col-sm-5">
												<input type="text" class="form-control" name="last_name" id="last_name" value="<?php echo safeTextOut($last_name); ?>" />												
											</div>
										</div>
										<div class="form-group">
											<label class="col-sm-3 control-label">Email</label>
											<div class="col-sm-5">
												<input type="text" class="form-control" name="email" id="email" value="<?php echo safeTextOut($email); ?>" />												
											</div>
										</div>
										<div class="form-group">
											<label class="col-sm-3 control-label">Phone</label>
											<div class="col-sm-5">
												<input type="text" class="form-control" name="phone" id="phone" value="<?php echo $phone; ?>" />												
											</div>
										</div>
										<div class="form-group">
											<label class="col-sm-3 control-label">Role</label>
											<div class="col-sm-5">												
												<select class="form-control" name="role_id" id="role_id">
													<?php 
														$roles_list = getRolesList();
														foreach ($roles_list as $key => $value) {
															$selected = ($key == $role_id) ? "selected = selected" : "";
															echo "<option value=\"".$key."\"".$selected.">".$value."</option>";
														}
													?>
												</select>
											</div>
										</div>
										<!-- <div class="form-group">
											<label class="col-sm-3 control-label">Manufacturing Unit</label>
											<div class="col-sm-5">												
												<select class="form-control" name="manufacturing_unit_id" id="manufacturing_unit_id">
												<option value="">-- Select Manufacturing Unit --</option>
													<?php 
														$maufacturing_unit_list = getManufacturingUnitListDropdown();
														foreach ($maufacturing_unit_list as $key => $value) {
															$selected = ($key == $manufacturing_unit_id) ? "selected = selected" : "";
															//echo "<option value="".$key.""".$selected.">".$value."</option>";
														}
													?>
												</select>
											</div>
										</div>
										<div class="form-group">
											<label class="col-sm-3 control-label">Shop</label>
											<div class="col-sm-5">												
												<select class="form-control" name="shop_id" id="shop_id">
												<option value="">-- Select Shop --</option>
													<?php 
														$shop_list = getShopListDropdown();
														foreach ($shop_list as $key => $value) {
															$selected = ($key == $shop_id) ? "selected = selected" : "";
															//echo "<option value="".$key.""".$selected.">".$value."</option>";
														}
													?>
												</select>
											</div>
										</div> -->
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
												<a href="manage_user.php" class="btn btn-primary">Cancel</a>
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
								
								<!-- Change password start -->
								<?php 
								if(isset($action) && $action == 'edit') {
								?>
								<form id="passForm" method="post" action="" class="form-horizontal">
									<input type="hidden" name="change_pass_post" value="1" />
									<input type="hidden" name="user_id" id="user_id" value="<?php echo $user_id ?>" />
									<fieldset>
										<legend>Change password</legend>
										<!--<div class="form-group">
											<label class="col-sm-3 control-label">Old password</label>
											<div class="col-sm-5">
												<input type="text" class="form-control" name="old_pass" id="old_pass" value="" />
											</div>
										</div>-->
										<div class="form-group">
											<label class="col-sm-3 control-label">New Password</label>
											<div class="col-sm-5">
												<input type="text" class="form-control" name="new_pass" id="new_pass" value="" />
											</div>
										</div>
										<div class="form-group">
											<label class="col-sm-3 control-label">Confirm New Password</label>
											<div class="col-sm-5">
												<input type="text" class="form-control" name="confirm_new_pass" id="confirm_new_pass" value="" />
											</div>
										</div>		
									</fieldset>									
									<div class="form-group">
										<div class="col-sm-9 col-sm-offset-3">											
											<button type="submit" class="btn btn-primary">Update</button>												
										</div>
									</div>
								</form>
								<?php 
									}
								?>
								<!-- Change password end -->
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
		user_name: {
			message: 'The username is not valid',
			validators: {
				notEmpty: {
					message: 'The username is required and can\'t be empty'
				},
				stringLength: {
					min: 5,
					max: 30,
					message: 'The username must be more than 5 and less than 30 characters long'
				},
				regexp: {
					regexp: /^[a-zA-Z0-9_\.]+$/,
					message: 'The username can only consist of alphabetical, number, dot and underscore'
				}
			}
		},
		<?php if(isset($action) && $action == 'add') { ?>
		user_pass: {
			validators: {
				notEmpty: {
					message: 'The password is required and can\'t be empty'
				}
			}
		},
		<?php } ?>
		email: {
			validators: {
				notEmpty: {
					message: 'The email address is required and can\'t be empty'
				},
				emailAddress: {
					message: 'The input is not a valid email address'
				}
			}
		},
		manufacturing_unit_id: {
			validators: {
				notEmpty: {
					message: 'The Manufacturing Unit is required and can\'t be empty'
				}
			}
		},
		shop_id: {
			validators: {
				notEmpty: {
					message: 'The Shop is required and can\'t be empty'
				}
			}
		},
		phone: {
			validators: {
				notEmpty: {
					message: 'The phone is required and can\'t be empty'
				},
				digits: {
					message: 'The value can contain only digits'
				}
			}
		}
	}
});

$('#passForm').bootstrapValidator({
	message: 'This value is not valid',
	fields: {
		//old_pass: {
			//validators: {
				//notEmpty: {
					//message: 'The old password is required and can\'t be empty'
				//}
			//}
		//},
		new_pass: {
			validators: {
				notEmpty: {
					message: 'The password is required and can\'t be empty'
				},
				identical: {
					field: 'confirm_new_pass',
					message: 'The password and its confirm are not the same'
				}
			}
		},
		confirm_new_pass: {
			validators: {
				notEmpty: {
					message: 'The confirm password is required and can\'t be empty'
				},
				identical: {
					field: 'new_pass',
					message: 'The password and its confirm are not the same'
				}
			}
		},
	}
});
</script>
</body>
</html>