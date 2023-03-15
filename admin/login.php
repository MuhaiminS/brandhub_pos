<?php
	session_start();
	include("../functions.php");
	include_once("../config.php");
	connect_dre_db();
	//echo md5('adminrice');
	if(isset($_POST['login'])) {	
		$user_name = mysqli_real_escape_string($GLOBALS['conn'], $_POST['user_name']);
		$user_pass = mysqli_real_escape_string($GLOBALS['conn'], $_POST['user_pass']);	
		$encrypt_pass = md5($user_pass);	
		$admin_query = "select * from users where user_name = '$user_name' AND user_pass = '$encrypt_pass' AND role_id = '1'";
		$run = mysqli_query($GLOBALS['conn'], $admin_query);
		if(mysqli_num_rows($run)>0){
			while($row = mysqli_fetch_array($run)) {
				//$user_id = $row['user_id'];
				$user_name = $row['user_name'];
				$role_id = $row['role_id'];
				$user_id = $row['id'];
				$shop_id = $row['shop_id'];
			}
			$_SESSION['shop_id'] = $shop_id;
			$_SESSION['role_id'] = $role_id;
			$_SESSION['user_id'] = $user_id;
			$_SESSION['user_name'] = $user_name;
			redirect('index.php');
		}
		else {
			echo "<script>alert('User name or password is incorrect')</script>";	
		}
	}
	?>
<!DOCTYPE html>
<html>
	<head>
		<?php include("common/header.php"); ?>     
		<link rel="stylesheet" href="plugins/iCheck/square/blue.css">
		<?php include("common/header-scripts.php"); ?>
	</head>
	<body class="hold-transition login-page">
		<div class="login-box">
			<div class="login-logo">
				<a target="_blank" href="http://www.overseepos.com"><?php echo CLIENT_NAME; ?></a>
			</div>
			<!-- /.login-logo -->
			<div class="login-box-body">
				<p class="login-box-msg">Login</p>
				<form action="login.php" method="post">
					<div class="form-group has-feedback">
						<input type="text" name="user_name" class="form-control" placeholder="User name">
						<span class="glyphicon glyphicon-envelope form-control-feedback"></span>
					</div>
					<div class="form-group has-feedback">
						<input type="password" name="user_pass" class="form-control" placeholder="Password">
						<span class="glyphicon glyphicon-lock form-control-feedback"></span>
					</div>
					<div class="row">
						<div class="col-xs-8">
							<div class="checkbox icheck">
								<label>
								<input type="checkbox"> Remember Me
								</label>
							</div>
						</div>
						<!-- /.col -->
						<div class="col-xs-4">
							<button type="submit" name="login" value="Login" class="btn btn-primary btn-block btn-flat">Sign In</button>
						</div>
						<!-- /.col -->
					</div>
				</form>
				<!--
					<div class="social-auth-links text-center">
					     <p>- OR -</p>
					     <a href="#" class="btn btn-block btn-social btn-facebook btn-flat"><i class="fa fa-facebook"></i> Sign in using
					       Facebook</a>
					     <a href="#" class="btn btn-block btn-social btn-google btn-flat"><i class="fa fa-google-plus"></i> Sign in using
					       Google+</a>
					   </div>
					-->
				<!-- /.social-auth-links -->
				<!--
					<a href="#">I forgot my password</a><br>
					<a href="register.html" class="text-center">Register a new membership</a>
					-->
			</div>
			<!-- /.login-box-body -->
		</div>
		<!-- /.login-box -->
		<!-- jQuery 3 -->
		<script src="bower_components/jquery/dist/jquery.min.js"></script>
		<!-- Bootstrap 3.3.7 -->
		<script src="bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
		<!-- iCheck -->
		<script src="plugins/iCheck/icheck.min.js"></script>
		<script>
			$(function () {
			  $('input').iCheck({
			    checkboxClass: 'icheckbox_square-blue',
			    radioClass: 'iradio_square-blue',
			    increaseArea: '20%' /* optional */
			  });
			});
		</script>
	</body>
</html>