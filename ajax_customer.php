<?php
session_start();
include("functions_web.php");
include_once("config.php");
connect_dre_db();
$cus_num = trim($_POST['keyword']);
//print_r($barcode_id); die;
$json = array();
$id = mysqli_real_escape_string($GLOBALS['conn'], $cus_num);
if($id != '') {
	$sql = "SELECT customer_id, customer_number, customer_name, customer_address FROM ".DB_PRIFIX."customer_details WHERE customer_number like '%" . $cus_num . "%' ORDER BY customer_name";
	$res = mysqli_query($GLOBALS['conn'], $sql);
	//echo $sql;
	$num=mysqli_num_rows($res);
	if($num > 0){
		while ($row = mysqli_fetch_array($res)) {
			$result[] = $row;
		}		
	}
	if(!empty($result)) {
	
	echo "<ul id='customer-list'>";
	foreach($result as $customer) {
	$cus_id = $customer['customer_id'];
	$cus_number = $customer['customer_number'];
	$cus_name = $customer['customer_name'];
	$cus_address = $customer['customer_address'];
	echo "<li onClick='selectCountry(\"$cus_id\", \"$cus_number\", \"$cus_name\", \"$cus_address\")'>".$customer["customer_number"]."</li>";
	}
	echo "</ul>";
} }