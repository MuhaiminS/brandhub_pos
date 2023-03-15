<?php
session_start();
include("functions_web.php");
include_once("config.php");
connect_dre_db();
chkUserLoggedIn();
$getUserDetails = getUserDetails($_SESSION['user_id']);
$getUserDetails = explode(",", $getUserDetails['user_action']);
$combo = getComboOffers();
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
	  <title>Login Page/صفحة تسجيل الدخول</title>
	  <meta name="description" content="" />
	  <meta name="keywords" content="" />
	  <meta name="viewport" content="width=device-width, initial-scale=1">
	  <link rel="stylesheet" href="css/bootstrap.min.css">
	  <link rel="stylesheet" href="css/main.css">
	  <link rel="icon" type="image/png" sizes="32x32" href="img/favicon.png">
	  <style>
	   .table-val-result, .table-val-result p{text-align: center; font-weight: bold;}
		.table-val {
			padding-right: 9px;
			padding-left: 9px;
			border-radius: 9px;
			padding-top: 9px;
			padding-bottom: 9px;
			color: #fff;
		}
		.success{background: green;}
		.danger{background: red;}
		.normal{background: orange;}
		.border-btm{border-bottom: 1px solid #ccc;}
		.table-bdy{
		    text-align: center;
			padding: 30px;
			border: 1px solid #ccc;
			border-radius: 10px;
			font-size: 20px;
			color: #fff;
			font-weight: bold;
		}
		.table-str .col-sm-2{margin-top: 5px;}
		.table-remain-cnt {
			position: absolute;
			top: 5px;
			float: right;
			font-size: 12px;
			right: 25px;
		}
		.border-btm select{
			height: 45px;
			font-size: 20px;
		}
	  </style>
   </head>
   <body>
	  <!--[if lt IE 7]>
	  <p class="browsehappy">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
	  <![endif]-->
	  <header>
		 <!--logo-start-->
		 <div class="container-fluid top-head">
			<div class="row">
			   <div class="col-sm-4">
				  <div class="logo-s">
					 <a href="index.php"><img src="img/out.jpg" alt="logo"></a>
				  </div>
			   </div>
			   <div class="col-sm-4">
				  <div class="counter-head">
					 COMBO PACKAGE<br>حزمة كومبو
				  </div>
			   </div>
			   <div class="col-sm-4">
				  <div class="log-box-s">
					 <div class="log-box-s-one">
					 <a href="index.php">
						<span><img src="img/home.png"></span>
						<p>HOME<br>الصفحة الرئيسية</p>
					 </a>
					 </div>
				  </div>
			   </div>
			</div>
		 </div>
	  </header>
	  <div style="clear:both"></div>
	  <div class="conter-product">
		 		  
		  <div class="container-fluid table-str">
		  <div class="row">
		  <?php foreach($combo as $com) { 		
		  ?>
				<div class="col-sm-2">
					<a href="combo_package_list.php?id=<?php echo $com['id']; ?>">
					<div class="table-bdy" style="background: red;">
						<span><?php echo $com['package_name']; ?></span>
						<span><?php echo $com['package_price']; ?></span>
						<span class="table-remain-cnt"><?php echo $com['package_items']; ?></span>
					</div>
					</a>
				</div>
		  <?php } ?>
			</div>
		  </div>
	  </div>
	  <script src="js/jquery-3.2.1.min.js"></script> 
	  <script src="js/bootstrap.min.js"></script>
	  <script>
	  document.querySelector('#floors').addEventListener('change', function () {
		  location.href = "dine_in.php?floor=" + this.value;
		});
	  </script>
   </body>
</html>