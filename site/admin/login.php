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
<html lang="en">
	<head>
		<meta charset="utf-8">
		<title><?php echo CLIENT_NAME; ?></title>
		<meta name="description" content="description">
		<meta name="author" content="Evgeniya">
		<meta name="keyword" content="keywords">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="icon" type="image/png" sizes="32x32" href="../images/favicon.ico">
		<link href="plugins/bootstrap/bootstrap.css" rel="stylesheet">
		<link href="http://netdna.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.css" rel="stylesheet">
		<link href='http://fonts.googleapis.com/css?family=Righteous' rel='stylesheet' type='text/css'>
		<link href="css/style_v2.css" rel="stylesheet">
		<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
		<!--[if lt IE 9]>
				<script src="http://getbootstrap.com/docs-assets/js/html5shiv.js"></script>
				<script src="http://getbootstrap.com/docs-assets/js/respond.min.js"></script>
		<![endif]-->
	</head>
<body>
<div class="container-fluid">
	<div id="page-login" class="row">
		<div class="col-xs-12 col-md-4 col-md-offset-4 col-sm-6 col-sm-offset-3">
			<!--<div class="text-right">
				<a href="page_register.html" class="txt-default">Need an account?</a>
			</div>-->
			<form method="post" action="login.php">
				<div class="box">
					<div class="box-content">
						<div class="text-center">
							<h3 class="page-header"><?php echo CLIENT_NAME; ?> - Login Page</h3>
						</div>
						<div class="form-group">
							<label class="control-label">Username</label>
							<input type="text" class="form-control" name="user_name" />
						</div>
						<div class="form-group">
							<label class="control-label">Password</label>
							<input type="password" class="form-control" name="user_pass" />
						</div>
						<div class="text-center">
							<input type="submit" name="login" value="Login" class="btn btn-primary">
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
</body>
</html>
