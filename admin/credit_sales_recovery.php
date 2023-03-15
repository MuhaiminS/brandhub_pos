<?php
	session_start();	
	include("../functions.php");
	include_once("../config.php");
	chkAdminLoggedIn();
	connect_dre_db();
	//print_r($_POST); die;
	$customer_id = $_POST['customer_id'];
	$name = $_POST['name'];
	$number = $_POST['number'];
	$amount = $_POST['amount'];
	$paid_date = date("Y-m-d H:i:s");
	$shop_id = $_SESSION['shop_id'];
	$user_id = $_SESSION['user_id'];
	if($amount != '' && $amount > 0) {
		$qry = "INSERT INTO `credit_sale`(`customer_id`, `name`, `number`, `type`, `amount`, `paid_date`, `user_id`, `shop_id`) VALUES ('$customer_id','$name','$number','debit','$amount', '$paid_date', '$user_id', '$shop_id')";
		//echo $qry; die;
		if(mysqli_query($GLOBALS['conn'], $qry)){		
			//header('Location: manage_blog.php?resp=updatesucc');
			echo json_encode('success');
		}
	} else { 
		echo json_encode('error');
	}
	
	
	?>