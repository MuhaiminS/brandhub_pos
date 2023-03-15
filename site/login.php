<?php 
session_start();
include("functions_web.php");
include_once("config.php");
connect_dre_db();
if(isset($_POST['login'])) {
	$phone = mysqli_real_escape_string($GLOBALS['conn'], $_POST['phone']);
	$user_pass = mysqli_real_escape_string($GLOBALS['conn'], $_POST['user_pass']);	
	$encrypt_pass = md5($user_pass);	
	$admin_query = "select * from ".DB_PRIFIX."users where phone = '$phone' AND user_pass = '$encrypt_pass'";
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
		echo "<script>alert('Phone or password is incorrect')</script>";	
	}
}
?>
<!DOCTYPE html>
<!--[if lt IE 7]>      
<html class="no-js lt-ie9 lt-ie8 lt-ie7">
<![endif]-->
<!--[if IE 7]>         
<html class="no-js lt-ie9 lt-ie8">
<![endif]-->
<!--[if IE 8]>         
<html class="no-js lt-ie9">
<![endif]-->
<!--[if gt IE 8]><!--> 
<html class="no-js">
<!--<![endif]-->
<html>
<head>
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta charset="UTF-8" />
	<title>Login Page</title>
	<meta name="description" content="" />
	<meta name="keywords" content="" />
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="css/bootstrap.min.css">
	<link rel="stylesheet" href="css/main.css">
	<link rel="icon" type="image/png" sizes="32x32" href="img/favicon.png">
</head>
<body>
	<!--[if lt IE 7]>
	<p class="browsehappy">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
	<![endif]-->
	<div class="logi-in-body">
		<div class="container">
			<div class="logi-in-box">
				<div class="log-in-headr"><h2><?php echo CLIENT_NAME; ?></h2><img src="img/logo.png"></div>
				<div class="log-in-content">
				<form method="post" action="login.php">
					<div class="form-group">
						<input class="form-control login" name="phone" placeholder="Phone number" type="number" required>
					</div>
					<div class="form-group">
						<input class="form-control login" name="user_pass" placeholder="Password" type="password" required>
					</div>
					<div class="form-group">
						<input type="submit" name="login" class="btn btn-default cnt-submit" value="Submit">
					</div>
				</form>
				</div>
			</div>
		</div>
	</div>
	<script src="js/jquery-3.2.1.min.js"></script> 
	<script src="js/bootstrap.min.js"></script>
</body>
</html>