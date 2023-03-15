<?php 
session_start();
include("functions.php");
include_once("config.php");

function custom_echo($x, $length) { if(strlen($x)<=$length) { return $x; } else { $y=substr($x,0,$length) . '...'; return $y; } }
	
connect_dre_db();

if(CURRENCY == '&#x20B9;')
{
	// define("CURRENCY",'INR');
	$CURRENCY = 'INR';
}
 else $CURRENCY = '$';
$session_id = (isset($_SESSION['user_id']) && $_SESSION['user_id'] !='') ? $_SESSION['user_id'] : '';

/* Change to the correct path if you copy this example! */
require __DIR__ . '/printer/autoload.php';
use Mike42\Escpos\Printer;
use Mike42\Escpos\EscposImage;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;

/**
 * Install the printer using USB printing support, and the "Generic / Text Only" driver,
 * then share it (you can use a firewall so that it can only be seen locally).
 *
 * Use a WindowsPrintConnector with the share name to print.
 *
 * Troubleshooting: Fire up a command prompt, and ensure that (if your printer is shared as
 * "Receipt Printer), the following commands work:
 *
 *  echo "Hello World" > testfile
 *  copy testfile "\\%COMPUTERNAME%\Receipt Printer"
 *  del testfile
 */
//echo '<pre>'; print_r($_GET); die;
mysqli_set_charset($GLOBALS['conn'], 'UTF8');
$sale_order_ids = array();
$server_url = getServerURL();
$sale_order_id = $_GET['id'];
$sale_order_ids = json_decode($sale_order_id);
// $bill_count = count($sale_order_ids);
//if(count($sale_order_ids) > 1){
	$sale_order_ids = $sale_order_ids;
//} else {
	$sale_order_ids = explode(" ",$sale_order_ids);
//}
//echo '<pre>'; print_r($sale_order_ids);
$redirect = $_GET['re'];
$both_print = (isset($_GET['both_print']) && $_GET['both_print'] !='') ? $_GET['both_print'] : '';
$kot_print = (isset($_GET['kot_print']) && $_GET['kot_print'] !='') ? $_GET['kot_print'] : '';
$cus_print = (isset($_GET['cus_print']) && $_GET['cus_print'] !='') ? $_GET['cus_print'] : '';
$drawer = (isset($_GET['drawer']) && $_GET['drawer'] !='') ? $_GET['drawer'] : '';
$duplicate = (isset($_GET['duplicate']) && $_GET['duplicate'] !='') ? $_GET['duplicate'] : '';
$old_item = (isset($_GET['old_item']) && $_GET['old_item'] !='') ? $_GET['old_item'] : '';
$old_qty = (isset($_GET['old_qty']) && $_GET['old_qty'] !='') ? $_GET['old_qty'] : '';

$pay = (isset($_GET['pay']) && $_GET['pay'] !='') ? $_GET['pay'] : '';
$remarks1 = (isset($_GET['remark']) && $_GET['remark'] !='') ? $_GET['remark'] : '';
$deliver = (isset($_GET['deliver']) && $_GET['deliver'] !='') ? $_GET['deliver'] : '';
$print_allow = (isset($_GET['print_allow']) && $_GET['print_allow'] !='') ? $_GET['print_allow'] : '';
$table_close = (isset($_GET['table_close']) && $_GET['table_close'] !='') ? $_GET['table_close'] : '';
$kot_print_copy = (isset($_GET['kot_print_copy']) && $_GET['kot_print_copy'] !='') ? $_GET['kot_print_copy'] : '';
$kitch_print_only = (isset($_GET['kitch_print_only']) && $_GET['kitch_print_only'] !='') ? $_GET['kitch_print_only'] : '';
if($print_allow == 1 && $session_id != 1) {
	mysqli_query($GLOBALS['conn'], "UPDATE ".DB_PRIFIX."sale_orders set print_allow=1 WHERE id = '$sale_order_id'");
}

//echo '<pre>'; print_r($_GET); die;

function getUserName($user_id)
{
	$where = "WHERE id = '$user_id'";
	$service = getnamewhere('users', 'first_name', $where);
	return $service;
}
function getDriverName($driver_id)
{
	$where = "WHERE id = '$driver_id'";
	$service = getnamewhere('drivers', 'name', $where);
	return $service;
}
function getMaincat($main_id)
{
	$where = "WHERE id = '$main_id'";
	$service = getnamewhere('item_main_category', 'category_title', $where);
	return $service;
}
function getWaiterName($waiter_id)
{
	$where = "WHERE id = '$waiter_id'";
	$service = getnamewhere('users', 'first_name', $where);
	return $service;
}

function gettableno($table_id)
{
	$where = "WHERE table_id = '$table_id'";
	$service = getnamewhere('table_management', 'table_no', $where);
	return $service;
}
function getflooreno($floor_id)
{
	$where = "WHERE floor_id = '$floor_id'";
	$service = getnamewhere('floors', 'floor_name', $where);
	return $service;
}

foreach($sale_order_ids as $sale_order_id)
{
$result = mysqli_query($GLOBALS['conn'], "SELECT * FROM sale_orders WHERE id = $sale_order_id");  
$sale_insert =  mysqli_fetch_assoc($result);
$sale_order_id = $sale_insert['id'];
$remarks = $sale_insert['remarks'];
$discount = $sale_insert['discount'];
$amount_given = $sale_insert['amount_given'];
$multiple_amount_given = '0';//explode(',', $sale_insert['multiple_amount_given']);
$balance = $sale_insert['balance_amount'];
$receipt_id = $sale_insert['receipt_id'];
$payment_type = $sale_insert['payment_type'];
$card_num = $sale_insert['card_num'];
$floor_no = $sale_insert['floor_id'];
$offer_amount = isset($sale_insert['offer_amount'])? $sale_insert['offer_amount']: '0';
$waiter_id = '';//$sale_insert['waiter_id'];
$driver_id = $sale_insert['driver_id'];
$table_no = $sale_insert['table_id'];
$order_type = $sale_insert['order_type'];
$cus_name = $sale_insert['contact_name'];
$cus_num = $sale_insert['contact_number'];
$address = $sale_insert['address'];
//$CGST = $sale_insert['CGST'];
//$SGST = $sale_insert['SGST'];
$user_name = getUserName($sale_insert['user_id']);
$ordered_date = date("d-m-Y H:i:s", strtotime($sale_insert['ordered_date']));
$result_arr = $new_item_id = $new_qty = $new_array = $old_array = $diff_array_value = $result_arr_kitchen = $remove_array_value_final = $main_cat_arr = array();



$sql = "SELECT * FROM sale_order_items WHERE sale_order_id = $sale_order_id";
//$sql = "SELECT si.*, i.main_cat_id FROM sale_order_items AS si LEFT JOIN items AS i ON si.item_id = i.id WHERE si.sale_order_id = $sale_order_id";
$result_val = mysqli_query($GLOBALS['conn'], $sql);
while ($row = mysqli_fetch_assoc($result_val)) {
	$result_arr[] = $row;
}

if(BILL_TAX == 'yes') {
	if(BILL_TAX_TYPE == 'GST') {
		$gst_result = array();
		foreach ($result_arr as $element) {
		    $gst_result[$element['CGST']][] = $element;
		}

		$per_gst_res = array();
		foreach ($gst_result as $key => $value) 
		{
			$per_gst_res[$key]['price'] = $per_gst_res[$key]['CGST'] = $per_gst_res[$key]['SGST'] = 0;
			for($is=0;$is<count($gst_result[$key]);$is++)
			{
			$per_gst_res[$key]['price'] += ($value[$is]['price']*$value[$is]['qty']);
			// $per_gst_res[$key]['CGST'] += (($value[$is]['price']*$value[$is]['qty'])*$key)/100;
			// $per_gst_res[$key]['SGST'] += (($value[$is]['price']*$value[$is]['qty'])*$key)/100;

			$per_gst_res[$key]['CGST'] += (($value[$is]['price']*$value[$is]['qty']) - (($value[$is]['price']*$value[$is]['qty'])*100)/(($key*2)+100))/2;
			$per_gst_res[$key]['SGST'] += (($value[$is]['price']*$value[$is]['qty']) - (($value[$is]['price']*$value[$is]['qty'])*100)/(($key*2)+100))/2;
			}
		}

	}
}
// echo '<pre>';print_r($per_gst_res);echo '</pre>'; die;

//Main category
/*foreach($result_arr as $res1) {
	if(!isset($main_cat_arr[$res1['main_cat_id']])) 
		$main_cat_arr[$res1['main_cat_id']] = 0;
	$main_cat_arr[$res1['main_cat_id']] += $res1['price'] * $res1['qty'];
}*/
//echo '<pre>';print_r($main_cat_arr);echo '</pre>'; die;
//echo '<pre>';print_r($main_cat_arr);echo '</pre>';die;
/*$sql = "SELECT * FROM ".DB_PRIFIX."item_main_category WHERE id = 1";
$result_val = mysqli_query($GLOBALS['conn'], $sql);
while ($row = mysqli_fetch_assoc($result_val)) {
	$main_cat_arr[] = $row;
}*/


$total_amount_net = $total_amount_cgst = $total_amount_sgst = 0;
foreach ($per_gst_res as $key => $value) 
	{
		$total_amount_net += $value['price'];
		$total_amount_cgst += $value['CGST'];
		$total_amount_sgst += $value['SGST'];
	}


if($old_item && $old_qty){
	$old_item_array = explode(',', $old_item);
	$old_qty_array = explode(',', $old_qty);
	foreach ($old_item_array as $key => $value) {
		if(!isset($old_qty_arr[$value]))	
			$old_qty_arr[$value] = 0;	
		$old_qty_arr[$value] += $old_qty_array[$key];
		// $new_array[$value] = $old_qty[$key];
	}
 $old_item_arr = array_unique($old_item_array); 
	// print_r($old_item_arr); 
	// print_r($old_qty_arr); 
	// die;
/*	
	$new_item_arr = implode(",",$new_array_item);
	$new_qty_arr = implode(",",$new_array_qty);*/

	$old_array = array_combine($old_item_arr,$old_qty_arr);
	// $old_array = array_combine($old_item_array,$old_qty_array);
	krsort($old_array);
	foreach($result_arr as $res) {
		$new_item_id[] = $res['item_id'];
		$new_qty[] = $res['qty'];
	}
	foreach ($new_item_id as $key => $value) {
		if(!isset($new_array_qty[$value]))	
			$new_array_qty[$value] = 0;	
		$new_array_qty[$value] += $new_qty[$key];
	}
	// echo "<pre>"; 
	// print_r(array_unique($new_item_id));
	// print_r($new_array_qty); 
	// die;
	$new_array = array_combine(array_unique($new_item_id),$new_array_qty);
	// $new_array = array_combine($new_item_id,$new_qty);
	krsort($new_array);

	foreach ($new_array as $key => $value) {
		if(array_key_exists($key, $new_array) && array_key_exists($key, $old_array)) {
			$ret = $new_array[$key] - $old_array[$key];
			if($ret) {
				$diff_array_value[$key] = $new_array[$key] - $old_array[$key];
			}
		}
	}
	$new_array_value = array_diff_key($new_array, $old_array);
	$remove_array_value = array_diff_key($old_array, $new_array);
	foreach($remove_array_value as $key => $rem){
		$remove_array_value_final[$key] = '-'.$rem;
	}

	$result_arr_merge = $new_array_value + $diff_array_value + $remove_array_value_final;
	foreach($result_arr_merge as $key => $value){
		$notes = getnamewhere('sale_order_items', 'comments', 'WHERE item_id = '.$key.' AND sale_order_id = '.$sale_order_id);
		if($notes) {
			$result_arr_kitchen[getItemName($key).'%%'.$value] = $notes;
		} else {
			$result_arr_kitchen[getItemName($key).'%%'.$value] = '';
		}

	}
	// print_r($result_arr_kitchen); die;
}

//die;
/*if($pay == 'given' && $table_close == 'yes') {
	$table_id = $sale_insert['table_id'];
	$num_members = $sale_insert['num_members'];
	$table_result=mysqli_query($GLOBALS['conn'], "SELECT * from ".DB_PRIFIX."table_management where table_id=$table_id");
			
	if($row=mysqli_fetch_assoc($table_result)){
		$filled_seats=$row['filled_seats'];

		if(isset($num_members)){
			$seats=$filled_seats-$num_members;			
			mysqli_query($GLOBALS['conn'], "UPDATE ".DB_PRIFIX."table_management set filled_seats=$seats WHERE table_id=$table_id");
		}
	}
}*/
if(($pay == 'given' && $table_close == 'yes') || ( $table_close == 'yes')) {
	$table_id = $sale_insert['table_id'];
	$num_members = $sale_insert['num_members'];
	$table_result=mysqli_query($GLOBALS['conn'], "SELECT * from ".DB_PRIFIX."table_management where table_id=$table_id");
			
	if($row=mysqli_fetch_assoc($table_result)){
		$filled_seats=$row['filled_seats'];

		if(isset($num_members)){
			$seats=$filled_seats-$num_members;
			if($seats < 0) {
				$seats = 0;
			}
			mysqli_query($GLOBALS['conn'], "UPDATE ".DB_PRIFIX."table_management set filled_seats=$seats WHERE table_id=$table_id");
		}
	}
}

if($order_type == 'delivery'){
	$print_copy = '2';	
} else {
	$print_copy = '1';
}
if($kot_print_copy == '1' || $kitch_print_only == '1') {
	$kitchen_copy = '1';
} else {
	$kitchen_copy = '2';
}

if($order_type == 'counter_sale') { $order_type = 'Take Away'; }


//Print Area

if($both_print || $cus_print) {//echo '123'; die;
	for($i=0;$i<$print_copy;$i++) {
	$total_amount = $tax_pecr = '0';
	try {
			// Enter the share name for your USB printer here
			//$connector = null;
			//$connector = new WindowsPrintConnector(CUSTOMER_COPY);
			if($_SESSION['role_id'] == '3') {
				$connector = new WindowsPrintConnector(WAITER_PRINTER);
			} else {			
				$connector = new WindowsPrintConnector(COUNTER_PRINTER);
			}

			/* Print a "Hello world" receipt" */
			$printer = new Printer($connector);
			//$printer -> text($print_content);
			//$printer -> cut();

			//New Lines Added
			
			$date = date("d-m-Y g:i a");
			/* Start the printer */
			$printer = new Printer($connector);

			if($pay == 'given' || $drawer =='yes'){
				// Regular pulse
				if($i==0){
					$printer->pulse();
					sleep(1);
				}
			}

			$printer -> setJustification(Printer::JUSTIFY_CENTER);
			
			//$logo = EscposImage::load("resources/escpos-php.png", false);
			if(CLIENT_LOGO) {
				$logo = EscposImage::load(CLIENT_LOGO, false);
				$printer -> bitImage($logo);
			}

			/* Name of shop */
			$printer -> feed();
			$printer -> selectPrintMode(Printer::MODE_DOUBLE_WIDTH | Printer::MODE_DOUBLE_HEIGHT);
			//$printer -> text(CLIENT_NAME."\n");
			//$printer -> feed();
			$printer -> selectPrintMode();
			$printer -> text(CLIENT_ADDRESS."\n".CLIENT_NUMBER."\n".CLIENT_WEBSITE);
			$printer -> feed(1);

			/* Order Type */
			$printer -> setEmphasis(true);
			$printer -> selectPrintMode(Printer::MODE_DOUBLE_HEIGHT);
			if($order_type == 'dine_in'){
				$printer -> text("Table no - ".gettableno($table_no)."\n");
			} else {
				$printer -> text(ucfirst(str_replace("_", ' ', $order_type))."\n");
			}
			$printer -> setEmphasis(false);
			$printer -> feed(1);

			/* Title of receipt */
			$printer -> setEmphasis(true);
			$printer -> selectPrintMode(Printer::MODE_DOUBLE_HEIGHT);
			$printer -> selectPrintMode(Printer::MODE_DOUBLE_WIDTH | Printer::MODE_DOUBLE_HEIGHT);
			$printer -> text("BILL\n");
			$printer -> setEmphasis(false);
			$printer -> feed(1);

			/* Description */
			$printer -> setEmphasis(true);
			$printer -> selectPrintMode();
			$printer -> text(str_pad("Qty", 3, ' ', STR_PAD_RIGHT).str_pad("Name", 29, ' ', STR_PAD_BOTH).str_pad("U.Price", 8, ' ', STR_PAD_LEFT).str_pad("Price", 8, ' ', STR_PAD_LEFT));
			$printer -> text(str_pad("", 48, '-', STR_PAD_RIGHT));
			$printer -> setEmphasis(false);
			$printer -> feed(1);

			/* Items */
			$printer -> setEmphasis(true);
			$printer -> selectPrintMode();
			foreach($result_arr as $key => $res){
				//$printer -> text(str_pad($res ['item_name'], 48, ' ', STR_PAD_BOTH));
			//	$printer -> text(str_pad(number_format((float)($res['price']), 2, '.', ''), 15, ' ', STR_PAD_LEFT).str_pad($res ['qty'], 11, ' ', STR_PAD_LEFT).str_pad(number_format((float)$res['price']*$res['qty'], 2, '.', ''), 20, ' ', STR_PAD_LEFT));
				$printer -> text(str_pad($res ['qty'], 3, ' ', STR_PAD_RIGHT).str_pad(custom_echo(ucfirst($res ['item_name']),23), 29, ' ', STR_PAD_RIGHT).str_pad(number_format((float)($res['price']), 2, '.', ''), 8, ' ', STR_PAD_LEFT).str_pad(number_format((float)$res['price']*$res['qty'], 2, '.', ''), 8, ' ', STR_PAD_LEFT));
		
			$multiplle_val=$res['price']*$res['qty'];
			$total_amount+=$multiplle_val;
			}
			$printer -> text(str_pad("", 48, '-', STR_PAD_RIGHT));
			$printer -> setEmphasis(false);
			$printer -> feed(1);

			

			$printer -> setEmphasis(true);
			//
			$printer -> text(str_pad("Sub Total", 28, ' ', STR_PAD_RIGHT).str_pad(number_format((float)$total_amount, 2, '.', '').' '.$CURRENCY, 20, ' ', STR_PAD_LEFT));
			if(BILL_TAX == 'yes') {
				if(BILL_TAX_TYPE == 'VAT') {
					$tax_pecr = ($total_amount / 100) * (BILL_TAX_VAL);
					$printer -> text(str_pad("TAX(".BILL_TAX_VAL."%)", 28, ' ', STR_PAD_RIGHT).str_pad(number_format((float)($tax_pecr), 2, '.', '').' '.$CURRENCY, 20, ' ', STR_PAD_LEFT));
				}
				elseif(BILL_TAX_TYPE == 'GST') {

					$printer -> text(str_pad("SGST", 28, ' ', STR_PAD_RIGHT).str_pad(number_format((float)($total_amount_sgst), 2, '.', '').' '.$CURRENCY, 20, ' ', STR_PAD_LEFT));
					$printer -> text(str_pad("CGST", 28, ' ', STR_PAD_RIGHT).str_pad(number_format((float)($total_amount_cgst), 2, '.', '').' '.$CURRENCY, 20, ' ', STR_PAD_LEFT));
					
					$printer -> text(str_pad("", 48, ' ', STR_PAD_RIGHT));
					$printer -> text(str_pad("GST BREAKUP DETAILS", 48, ' ', STR_PAD_RIGHT));
					$printer -> text(str_pad("", 48, '-', STR_PAD_RIGHT));
					$printer -> text(str_pad("GST%", 6, ' ', STR_PAD_RIGHT).str_pad("Amount", 12, ' ', STR_PAD_BOTH).str_pad("SGST", 10, ' ', STR_PAD_BOTH).str_pad("CGST", 10, ' ', STR_PAD_BOTH).str_pad("Total", 10, ' ', STR_PAD_LEFT));
					$printer -> text(str_pad("", 48, '-', STR_PAD_RIGHT));
					$total_amount_net = $total_amount_sub = $total_amount_cgst = $total_amount_sgst = 0;
					foreach ($per_gst_res as $key => $value) 
					{
					$printer -> text(str_pad(($key*2)." %", 6, ' ', STR_PAD_RIGHT).str_pad(number_format((float)($value['price']-$value['CGST']-$value['SGST']), 2, '.', ''), 12, ' ', STR_PAD_BOTH).str_pad(number_format((float)($value['SGST']), 2, '.', ''), 10, ' ', STR_PAD_BOTH).str_pad(number_format((float)($value['CGST']), 2, '.', ''), 10, ' ', STR_PAD_BOTH).str_pad(number_format((float)($value['price']), 2, '.', ''), 10, ' ', STR_PAD_LEFT));
					$total_amount_net += $value['price'];
					$total_amount_sub += $value['price']-$value['CGST']-$value['SGST'];
					$total_amount_cgst += $value['CGST'];
					$total_amount_sgst += $value['SGST'];
					}
					$printer -> text(str_pad("", 48, '-', STR_PAD_RIGHT));
					$printer -> text(str_pad("Total", 6, ' ', STR_PAD_RIGHT).str_pad(number_format((float)($total_amount_sub), 2, '.', ''), 12, ' ', STR_PAD_BOTH).str_pad(number_format((float)($total_amount_sgst), 2, '.', ''), 10, ' ', STR_PAD_BOTH).str_pad(number_format((float)($total_amount_cgst), 2, '.', ''), 10, ' ', STR_PAD_BOTH).str_pad(number_format((float)($total_amount_net), 2, '.', ''), 10, ' ', STR_PAD_LEFT));
					$printer -> text(str_pad("", 48, '-', STR_PAD_RIGHT));
				}

			}
			if($discount > 0) {
			$printer -> text(str_pad("Discount", 28, ' ', STR_PAD_RIGHT).str_pad(number_format((float)($discount), 2, '.', '').' '.$CURRENCY, 20, ' ', STR_PAD_LEFT));
			$printer -> text(str_pad("Sub Total", 28, ' ', STR_PAD_RIGHT).str_pad(number_format((float)$total_amount, 2, '.', '').' '.$CURRENCY, 20, ' ', STR_PAD_LEFT));
			$printer -> text(str_pad("", 48, '-', STR_PAD_RIGHT));
			}
			// $printer -> text(str_pad("Offer", 28, ' ', STR_PAD_RIGHT).str_pad(number_format((float)($offer_amount), 2, '.', '').' '.$CURRENCY, 20, ' ', STR_PAD_LEFT));
						

			$printer -> selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
			$printer -> text(str_pad("Grand Total", 12, ' ', STR_PAD_RIGHT).str_pad(number_format((float)(($total_amount - $discount - $offer_amount) + $tax_pecr), 2, '.', '').' '. $CURRENCY, 12, ' ', STR_PAD_LEFT));
			$printer -> setEmphasis(false);

			$printer -> feed();
			$printer -> selectPrintMode();

		


			/*Amount Given*/
			if($pay == 'given' && $payment_type != 'credit' && $amount_given > 0) {
				$printer -> setEmphasis(true);
				$printer -> text(str_pad("", 48, '-', STR_PAD_RIGHT));
					if(isset($amount_given)) {
					$printer -> text(str_pad("Amount Given ", 28, ' ', STR_PAD_RIGHT).str_pad(number_format((float)($amount_given), 2, '.', '').' '.$CURRENCY, 20, ' ', STR_PAD_LEFT));
				}
				$printer -> text(str_pad("Balance", 28, ' ', STR_PAD_RIGHT).str_pad(number_format((float)($amount_given - (($total_amount - $discount - $offer_amount) + $tax_pecr)), 2, '.', '').' '. $CURRENCY, 20, ' ', STR_PAD_LEFT));
				$printer -> setEmphasis(false);
			}

			/*Main Category*/
			/*if($main_cat_arr){
				//echo '<pre>'; print_r($main_cat_arr); die;
				$printer -> text(str_pad("", 48, '-', STR_PAD_RIGHT));
				foreach($main_cat_arr as $key => $amount) {					
					$printer -> text(str_pad(getMaincat($key), 28, ' ', STR_PAD_RIGHT).str_pad(number_format((float)($amount), 2, '.', '').' '.$CURRENCY, 20, ' ', STR_PAD_LEFT));
				}
			}*/
			
			/*Cashier Name*/
			$printer -> setEmphasis(true);
			$printer -> text(str_pad("", 48, '-', STR_PAD_RIGHT));
			//$printer -> text(str_pad("Cashier Name : ".ucfirst($user_name), 48, ' ', STR_PAD_RIGHT));
			$printer -> setEmphasis(false);

			/*Other Details*/
			$printer -> feed(1);
			/*$printer -> text("Order type : ".ucfirst(str_replace("_", ' ', $order_type))."\n");*/
			if($order_type != 'delivery' && $payment_type != '') {
				$printer -> text("Payment method : ".$payment_type."\n");
			}
			if($remarks1 == 'yes') {
				$printer -> text("Remarks : ".$remarks."\n");
			}
			if($order_type == 'dine_in') {
				$printer -> text("Floor no : ".getflooreno($floor_no)."\n");
			}
			if($order_type == 'delivery') {
				$printer -> text("Driver : ".getDriverName($driver_id)."\n");
			}
			if($waiter_id != 0) {
				$printer -> text("User Name: ".getWaiterName($waiter_id)."\n");	
			}
			
			/*Customer Details*/
			if($deliver == 'yes' && $cus_num !='0') {
				$printer -> feed(1);
				//$printer -> text("Name : ".$cus_name."\n");
				$printer -> text(str_pad("Name", 15, ' ', STR_PAD_RIGHT).str_pad($cus_name, 33, ' ', STR_PAD_RIGHT));
				$printer -> text(str_pad("Number", 15, ' ', STR_PAD_RIGHT).str_pad($cus_num, 33, ' ', STR_PAD_RIGHT));
				//$printer -> text("Number : ".$cus_num."\n");
				if (strpos($address, '~') !== false) {
    // echo 'true';
				if(!empty($address)) {
				$address_new = explode("~",$address);
				if(isset($address_new[0]) && $address_new[0] !='') {
				$printer -> text(str_pad("Building Name", 15, ' ', STR_PAD_RIGHT).str_pad($address_new[0], 33, ' ', STR_PAD_RIGHT));
				} if(isset($address_new[1]) && $address_new[1] !='') {
				$printer -> text(str_pad("Flat No", 15, ' ', STR_PAD_RIGHT).str_pad($address_new[1], 33, ' ', STR_PAD_RIGHT));
				} if(isset($address_new[2]) && $address_new[2] !='') {
				$printer -> text(str_pad("Address", 15, ' ', STR_PAD_RIGHT).str_pad($address_new[2], 33, ' ', STR_PAD_RIGHT));
				}}
				//$printer -> text("Building Name : ".$address_new[0]."\n");
				//$printer -> text("Flat No : ".$address_new[1]."\n");
				//$printer -> text("Address : ".$address_new[2]."\n");
				}
				else
				{
				$printer -> text(str_pad("Address", 15, ' ', STR_PAD_RIGHT).str_pad($address, 33, ' ', STR_PAD_RIGHT));
				//$printer -> text("Address : ".$address."\n");
				}
				$printer -> text(str_pad("",48," ",STR_PAD_RIGHT));
			}

			/* Footer */
			$printer -> feed(1);
			$printer -> setJustification(Printer::JUSTIFY_CENTER);
			if($order_type == 'dine_in') {
				$printer -> text(BILL_FOOTER."\n");
			} else { 
				$printer -> text("Take Away Food has to be consumed within 2 hours of purchase\n");
			}
			$printer -> feed(1);
			$printer -> text($date . "\n");
			$printer -> selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
			$printer -> text("Recipt no : ".$receipt_id."\n");
			$printer -> feed(1);
			//$printer -> barcode("POS-1009", Printer::BARCODE_CODE39);
			//$printer -> feed(2);
			
			/* Close printer */
			$printer -> cut();
			$printer -> close();
		} catch (Exception $e) {
			echo "Couldn't print to this printer: " . $e -> getMessage() . "\n";
		}
	}
}
}

if($both_print || $kot_print) {
	for($i=0;$i<$kitchen_copy;$i++) {
	try {
		// Enter the share name for your USB printer here
		//$connector = null;
		//$connector = new WindowsPrintConnector(KITCHEN_COPY);
		if($kitch_print_only==1) {			
			$connector = new NetworkPrintConnector(KITCHEN_PRINTER);
		} else {
			if($i==0) {
				$connector = new NetworkPrintConnector(WAITER_PRINTER); // for Re-Print
			} else {
				$connector = new NetworkPrintConnector(KITCHEN_PRINTER); // for KOT
			}
		}

		/* Print a "Hello world" receipt" */
		$printer = new Printer($connector);
		//$printer -> text($print_content);
		//$printer -> cut();

		//New Lines Added
		
		//$date = date("d-m-Y H:i:s");
		$date = date("d-m-Y g:i a");
		/* Start the printer */
		$printer = new Printer($connector);
		
		/* Name of shop */
		/*$printer -> feed();
		$printer -> setJustification(Printer::JUSTIFY_CENTER);
		$printer -> selectPrintMode(Printer::MODE_DOUBLE_WIDTH | Printer::MODE_DOUBLE_HEIGHT);
		$printer -> text(CLIENT_NAME."\n");*/
		$printer -> feed();
		$printer -> setEmphasis(true);
		$printer -> setJustification(Printer::JUSTIFY_CENTER);
		$printer -> text("-------------------------\n");
		if($i==0 && $kitch_print_only != 1) {
			$printer -> selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
			$printer -> text("-- RE-PRINT COPY --\n");			
		}
		if($order_type == 'dine_in') {
				$printer -> selectPrintMode(Printer::MODE_DOUBLE_WIDTH | Printer::MODE_DOUBLE_HEIGHT);
				$printer -> text(str_pad("Table no : ".gettableno($table_no)."\n", STR_PAD_LEFT));
		}
		$printer -> selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
		$printer -> text("Order type : ".ucfirst(str_replace("_", ' ', $order_type))."\n");
		$printer -> setEmphasis(false);	

		
		$printer -> feed();
		$printer -> setEmphasis(true);
		$printer -> setJustification(Printer::JUSTIFY_CENTER);
		$printer -> selectPrintMode();
		$printer -> text($date . "\n");
		

		if($order_type == 'dine_in') {
			//$printer -> text("Floor no : ".$floor_no."  Table no : ".$table_no."\n");
			$printer -> text(str_pad("Floor no : ".getflooreno($floor_no)."\n", STR_PAD_LEFT));
			//$printer -> text(str_pad("Table no : ".gettableno($table_no)."\n", STR_PAD_LEFT));
		}
		$printer -> setEmphasis(false);


		$printer -> feed();
		if($duplicate){
			$printer -> selectPrintMode(Printer::MODE_FONT_A);
			$printer -> text("-- DUPLICATE COPY --\n");
		}
		if($old_item && $old_qty){
			$printer -> selectPrintMode(Printer::MODE_FONT_A);
			$printer -> text("-- UPDATED ORDER --\n");
		}
		if($redirect == 'sale_order_details.php'){
			$printer -> selectPrintMode(Printer::MODE_FONT_A);
			$printer -> text("-- REPRINT ORDER --\n");
		}
		$printer -> feed();

		/* Description */
		$printer -> setEmphasis(true);
		$printer -> selectPrintMode();
		$printer -> text(str_pad("S.NO", 6, ' ', STR_PAD_RIGHT).str_pad("Item", 30, ' ', STR_PAD_RIGHT).str_pad("Qty", 12, ' ', STR_PAD_LEFT));
		$printer -> text(str_pad("", 48, '-', STR_PAD_RIGHT));
		$printer -> setEmphasis(false);
		$printer -> feed(1);

		/* Items */
		if($old_item && $old_qty){
			$y=1;
			// print_r($result_arr_kitchen);
			foreach($result_arr_kitchen as $key => $res){
				if(strafter($key, '%%') < 0) {$canceled = '(-- Canceled)';} else {$canceled = '';}				
				$printer -> setEmphasis(true);
				$printer -> selectPrintMode();
				$printer -> selectPrintMode(Printer::MODE_DOUBLE_HEIGHT);
				$printer -> text(str_pad(strafter($y, '%%'), 6, ' ', STR_PAD_RIGHT).str_pad(strbefore($key, '%%'), 30, ' ', STR_PAD_RIGHT).str_pad(strafter($key, '%%'), 12, ' ', STR_PAD_LEFT));
				//Notes Row
				if($res != '') {
					$printer -> text(str_pad("-- ".$res, 48, ' ', STR_PAD_RIGHT));
				}
				if($canceled) {
					$printer -> text(str_pad(' ',6,' ',STR_PAD_RIGHT).str_pad($canceled, 42, ' ', STR_PAD_RIGHT));
				}
				$printer -> setEmphasis(false);
				$printer -> feed(1);
				$y++;
			}
		} else {
			foreach($result_arr as $key => $res){
				$printer -> setEmphasis(true);
				$printer -> selectPrintMode(Printer::MODE_DOUBLE_HEIGHT);
				$printer -> text(str_pad(($key+1), 6, ' ', STR_PAD_RIGHT).str_pad($res['item_name'], 30, ' ', STR_PAD_RIGHT).str_pad($res['qty'], 12, ' ', STR_PAD_LEFT));
				//Notes Row
				if($res['notes'] != '') {
					$printer -> selectPrintMode();
					$printer -> text(str_pad("-- ".$res['notes'], 48, ' ', STR_PAD_RIGHT));
				}
				$printer -> setEmphasis(false);
				$printer -> feed(1);
			}
		}

		

		/* Footer */
		$printer -> feed(1);
		$printer -> selectPrintMode();
		$printer -> setJustification(Printer::JUSTIFY_CENTER);
		//$printer -> text("Order type : ".ucfirst(str_replace("_", ' ', $order_type))."\n");
		if($waiter_id != 0) {
			$printer -> text("User Name: ".getWaiterName($waiter_id)."\n");	
		}	
		if($order_type == 'dine_in') {
			//$printer -> text("Floor no : ".$floor_no."  Table no : ".$table_no."\n");
			//$printer -> text("Floor no : ".getflooreno($floor_no)."  Table no : ".gettableno($table_no)."\n");
		}
		/*Customer Details*/
		if($deliver == 'yes') {
			$printer -> feed(1);
			$printer -> text("Customer Details\n");
			$printer -> text("Name : ".$cus_name."\n");
			$printer -> text("Number : ".$cus_num."\n");
			if (strpos($address, '~') !== false) {
    // echo 'true';
			$address_new = explode("~",$address);
			$printer -> text("Building Name : ".$address_new[0]."\n");
			$printer -> text("Flat No : ".$address_new[1]."\n");
			$printer -> text("Address : ".$address_new[2]."\n");
			}
			else
			{
			$printer -> text("Address : ".$address."\n");

			}
			$printer -> text(str_pad("",48," ",STR_PAD_RIGHT));
		}
		//$printer -> text($date . "\n");
		$printer -> selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
		$printer -> text("Recipt no : ".$receipt_id."\n");
		$printer -> feed(1);
		
		//Printer Beep
		$printer -> beep(2,1); //this mean beep 4 time in 1 second
		
		/* Close printer */
		$printer -> cut();
		$printer -> close();

	} catch (Exception $e) {
		echo "Couldn't print to this printer: " . $e -> getMessage() . "\n";
	}
	}
}
 //die;
//redirect($redirect);
die;
redirect($redirect);
//$json['success'] = 'success';
//echo json_encode($json);
?>