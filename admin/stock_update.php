<?php
session_start();
include_once("../config.php");
include("../functions.php");
chkAdminLoggedIn();
connect_dre_db();
//print_r($_POST); die;
$product_id = $_POST['product_id'];
$stock_old = $_POST['stock_old'];
$stock_new = $_POST['stock_new'];
$action_type = $_POST['action_type'];
$date_added = date("Y-m-d H:i:s");
$user_id = $_SESSION['user_id'];
if($action_type == 'add') {
	$stock = $stock_old + $stock_new;
} elseif($action_type == 'sub') {
	$stock = $stock_old - $stock_new;
}
if($stock_new != '' && $stock_new > 0) {
	mysqli_query($GLOBALS['conn'], "UPDATE items SET stock = '$stock' WHERE id = '$product_id'");

	$qry = "INSERT INTO stock_management_history (`product_id`, `stock_value`, `action_type`, `date_added`, `user_id`) VALUES ('$product_id','$stock_new', '$action_type', '$date_added', '$user_id')";
	//echo $qry; die;
	if(mysqli_query($GLOBALS['conn'], $qry)){		
		//header('Location: manage_blog.php?resp=updatesucc');
		echo json_encode('success');
	}
} else { 
	echo json_encode('Please enetr stock value');
}
?>