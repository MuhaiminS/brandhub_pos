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
			<!-- Content Wrapper. Contains page content -->
			<div class="content-wrapper">
				<!-- Content Header (Page header) -->
				<section class="content-header">
					<h1>
						Add User
						<!--<small>Optional description</small>-->
					</h1>
					<ol class="breadcrumb">
						<li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
						<li><a href="products.php">Products</a></li>
						<li class="active">Add User</li>
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
								<div class="col-md-6">
									
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
												<input type="text" class="form-control" name="user_name" id="user_name" value="<?php echo safeTextOut(htmlspecialchars($user_name)); ?>" required />
											</div>
										</div>
										<?php if(isset($action) && $action == 'add') { ?>
										<div class="form-group">
											<label class="col-sm-3 control-label">Password</label>
											<div class="col-sm-5">
												<input type="text" class="form-control" name="user_pass" id="user_pass" value="" required />
											</div>
										</div>
										<?php } ?>
										<div class="form-group">
											<label class="col-sm-3 control-label">First name</label>
											<div class="col-sm-5">
												<input type="text" class="form-control" name="first_name" id="first_name" value="<?php echo safeTextOut($first_name); ?>" required />
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
												<input type="text" class="form-control" name="email" id="email" value="<?php echo safeTextOut($email); ?>" required />												
											</div>
										</div>
										<div class="form-group">
											<label class="col-sm-3 control-label">Phone</label>
											<div class="col-sm-5">
												<input type="text" class="form-control" name="phone" id="phone" value="<?php echo $phone; ?>" required />												
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
												<button type="submit" id="validateBtn" class="btn btn-primary">Update</button>
												<a href="users.php" class="btn btn-primary">Cancel</a>
											<?php } 
												else { 
											?>
												<button type="submit" id="validateBtn" class="btn btn-primary">Submit</button>
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
												<input type="text" class="form-control" name="new_pass" id="new_pass" value="" required="" />
											</div>
										</div>
										<div class="form-group">
											<label class="col-sm-3 control-label">Confirm New Password</label>
											<div class="col-sm-5">
												<input type="text" class="form-control" name="confirm_new_pass" id="confirm_new_pass" value="" required="" />
											</div>
										</div>		
									</fieldset>									
									<div class="form-group">
										<div class="col-sm-9 col-sm-offset-3">											
											<button type="submit" id="password_check" class="btn btn-primary">Update</button>												
										</div>
									</div>
								</form>
								<?php 
									}
								?>
								<!-- Change password end -->						
								
								<!-- /.col -->
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
		<script type="text/javascript">
				  $('#validateBtn').click(function() {
			      $('#userForm').bootstrapValidator('validate');
			  }); 
			   $('#password_check').click(function() {
			      $('#passForm').bootstrapValidator('validate');
			  });
		</script>
		<!-- Optionally, you can add Slimscroll and FastClick plugins.
			Both of these plugins are recommended to enhance the
			user experience. -->
	</body>
</html>