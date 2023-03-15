<?php
session_start();
include("../../../functions.php");
include_once("../../../config.php");
connect_dre_db();
/* Change to the correct path if you copy this example! */
require __DIR__ . '/../../autoload.php';
use Mike42\Escpos\Printer;
use Mike42\Escpos\EscposImage;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;

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
$server_url = getServerURL();
$sale_order_id = $_GET['id'];
$redirect = $_GET['re'];
$both_print = (isset($_GET['both_print']) && $_GET['both_print'] !='') ? $_GET['both_print'] : '';
$kot_print = (isset($_GET['kot_print']) && $_GET['kot_print'] !='') ? $_GET['kot_print'] : '';
$cus_print = (isset($_GET['cus_print']) && $_GET['cus_print'] !='') ? $_GET['cus_print'] : '';

$duplicate = (isset($_GET['duplicate']) && $_GET['duplicate'] !='') ? $_GET['duplicate'] : '';
$old_item = (isset($_GET['old_item']) && $_GET['old_item'] !='') ? $_GET['old_item'] : '';
$old_qty = (isset($_GET['old_qty']) && $_GET['old_qty'] !='') ? $_GET['old_qty'] : '';

$pay = (isset($_GET['pay']) && $_GET['pay'] !='') ? $_GET['pay'] : '';
$remarks1 = (isset($_GET['remark']) && $_GET['remark'] !='') ? $_GET['remark'] : '';
$deliver = (isset($_GET['deliver']) && $_GET['deliver'] !='') ? $_GET['deliver'] : '';
//echo '<pre>'; print_r($_GET); die;
$result = mysqli_query($GLOBALS['conn'], "SELECT * FROM ".DB_PRIFIX."sale_orders WHERE id = $sale_order_id");  
$sale_insert =  mysqli_fetch_assoc($result);
$sale_order_id = $sale_insert['id'];
$remarks = $sale_insert['remarks'];
$discount = $sale_insert['discount'];
$amount_given = $sale_insert['amount_given'];
$balance = $sale_insert['balance_amount'];
$receipt_id = $sale_insert['receipt_id'];
$payment_type = $sale_insert['payment_type'];
$card_num = $sale_insert['card_num'];
$floor_no = $sale_insert['floor_id'];
$table_no = $sale_insert['table_id'];
$order_type = $sale_insert['order_type'];
$cus_name = $sale_insert['contact_name'];
$cus_num = $sale_insert['contact_number'];
$address = $sale_insert['address'];
$user_name = getUserName($sale_insert['user_id']);
$ordered_date = date("d-m-Y H:i:s", strtotime($sale_insert['ordered_date']));
$result_arr = $new_item_id = $new_qty = $new_array = $old_array = $diff_array_value = $result_arr_kitchen = array();

$sql = "SELECT * FROM ".DB_PRIFIX."sale_order_items WHERE sale_order_id = $sale_order_id";
$result_val = mysqli_query($GLOBALS['conn'], $sql);
while ($row = mysqli_fetch_assoc($result_val)) {
	$result_arr[] = $row;
}

if($old_item && $old_qty){
	$old_item_array = explode(',', $old_item);
	$old_qty_array = explode(',', $old_qty);
	$old_array = array_combine($old_item_array,$old_qty_array);
	krsort($old_array);
	foreach($result_arr as $res) {
		$new_item_id[] = $res['item_id'];
		$new_qty[] = $res['qty'];
	}
	$new_array = array_combine($new_item_id,$new_qty);
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

	$result_arr_merge = $new_array_value + $diff_array_value;
	foreach($result_arr_merge as $key => $value){
		$notes_val = getnamewhere('sale_order_items', 'notes', 'WHERE item_id = '.$key.' AND sale_order_id = '.$sale_order_id);
		if($notes_val) {
			$result_arr_kitchen[getItemName($key).'%%'.$value] = $notes_val;
		} else {
			$result_arr_kitchen[getItemName($key).'%%'.$value] = '';
		}		
	}
	//echo '<!-- <pre> -->'; print_r($result_arr_kitchen);
}
//die;
if($pay == 'given1') {
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
}

if($deliver == 'yes'){
	$print_copy = '2';	
} else {
	$print_copy = '1';
}
function getUserName($user_id)
{
	$where = "WHERE id = '$user_id'";
	$service = getnamewhere('users', 'first_name', $where);
	return $service;
}
//Print Area

if($both_print || $cus_print) {
	for($i=0;$i<$print_copy;$i++) {
	$total_amount = $tax_pecr = '0';
	try {
			// Enter the share name for your USB printer here
			//$connector = null;
			$connector = new WindowsPrintConnector(CUSTOMER_COPY);

			/* Print a "Hello world" receipt" */
			$printer = new Printer($connector);
			//$printer -> text($print_content);
			//$printer -> cut();

			//New Lines Added
			
			$date = date("d-m-Y H:i:s");
			/* Start the printer */
			$printer = new Printer($connector);

			if($order_type != 'delivery'){
				// Regular pulse
				$printer->pulse();
				sleep(1);
			}

			$printer -> setJustification(Printer::JUSTIFY_CENTER);
			
			//$logo = EscposImage::load("resources/escpos-php.png", false);
			if(CLIENT_LOGO) {
				$logo = EscposImage::load(CLIENT_LOGO, false);
				$printer -> bitImage($logo);
			}
			//static address
			$addressLine1="Omar Bin Hyder Masjid Building";
			$addressLine2="Mizhar-1 Dubai";

			/* Name of shop */
			//$printer -> feed();
			$printer -> selectPrintMode(Printer::MODE_DOUBLE_HEIGHT);
			$printer -> text(CLIENT_NAME."\n");
			//$printer -> feed();
			$printer -> selectPrintMode();
			//$printer -> text(CLIENT_ADDRESS."\n".CLIENT_NUMBER."\n".CLIENT_WEBSITE);	
			$printer -> text($addressLine1."\n".$addressLine2."\n".CLIENT_NUMBER."\n".CLIENT_WEBSITE);
			$printer -> feed();

			/* Title of receipt */
			$printer -> setEmphasis(true);
			$printer -> selectPrintMode(Printer::MODE_DOUBLE_HEIGHT);
			$printer -> selectPrintMode(Printer::MODE_DOUBLE_WIDTH | Printer::MODE_DOUBLE_HEIGHT);
			$printer -> text("TAX INVOICE\n");
			$printer -> setEmphasis(false);
			//$printer -> feed(2);

			/* Description */
			$printer -> setEmphasis(true);
			$printer -> selectPrintMode();
			$printer -> text(str_pad("Qty", 10, ' ', STR_PAD_BOTH).str_pad("U.Price", 12, ' ', STR_PAD_LEFT).str_pad("Price", 20, ' ', STR_PAD_LEFT));
			$printer -> text(str_pad("", 42, '-', STR_PAD_RIGHT));
			$printer -> setEmphasis(false);
			//$printer -> feed(1);

			/* Items */
			$printer -> setEmphasis(true);
			$printer -> selectPrintMode();
			foreach($result_arr as $key => $res){
				$printer -> text(str_pad($res ['item_name'], 42, ' ', STR_PAD_BOTH));
				$printer -> text(str_pad($res ['qty'], 10, ' ', STR_PAD_BOTH).str_pad(number_format((float)($res['price']), 2, '.', ''), 12, ' ', STR_PAD_LEFT).str_pad(number_format((float)$res['price']*$res['qty'], 2, '.', ''), 20, ' ', STR_PAD_LEFT));
				//Notes Row
				/*if($res['notes'] != '') {
					$printer -> text(str_pad("-- ".$res ['notes'], 48, ' ', STR_PAD_RIGHT));
				}*/
			$multiplle_val=$res['price']*$res['qty'];
			$total_amount+=$multiplle_val;
			}
			$printer -> text(str_pad("", 42, '-', STR_PAD_RIGHT));
			$printer -> setEmphasis(false);
			//$printer -> feed(1);

			

			$printer -> setEmphasis(true);
			$printer -> text(str_pad("Sub Total", 25, ' ', STR_PAD_RIGHT).str_pad(number_format((float)$total_amount, 2, '.', '').' '.CURRENCY, 17, ' ', STR_PAD_LEFT));
			if(BILL_TAX == 'yes') {
				if(BILL_COUNTRY == 'UAE') {
					$tax_pecr = ($total_amount / 100) * (BILL_TAX_VAL);
					$printer -> text(str_pad("TAX(".BILL_TAX_VAL."%)", 25, ' ', STR_PAD_RIGHT).str_pad(number_format((float)($tax_pecr), 2, '.', '').' '.CURRENCY, 17, ' ', STR_PAD_LEFT));
				}
			}
			$printer -> text(str_pad("Discount", 25, ' ', STR_PAD_RIGHT).str_pad(number_format((float)($discount), 2, '.', '').' '.CURRENCY, 17, ' ', STR_PAD_LEFT));
			$printer -> text(str_pad("Grand Total", 25, ' ', STR_PAD_RIGHT).str_pad(number_format((float)($total_amount - $discount + $tax_pecr), 2, '.', '').' '. CURRENCY, 17, ' ', STR_PAD_LEFT));
			$printer -> setEmphasis(false);
			//$printer -> feed();
			$printer -> selectPrintMode();

			/*Amount Given*/
			if($pay == 'given' && $payment_type != 'credit') {
				$printer -> setEmphasis(true);
				$printer -> text(str_pad("", 42, '-', STR_PAD_RIGHT));
				$printer -> text(str_pad("Amount Given", 25, ' ', STR_PAD_RIGHT).str_pad(number_format((float)($amount_given), 2, '.', '').' '.CURRENCY, 17, ' ', STR_PAD_LEFT));
				$printer -> text(str_pad("Balance", 25, ' ', STR_PAD_RIGHT).str_pad(number_format((float)($amount_given - ($total_amount - $discount + $tax_pecr)), 2, '.', '').' '. CURRENCY, 17, ' ', STR_PAD_LEFT));
				$printer -> setEmphasis(false);
			}
			
			/*Cashier Name*/
			$printer -> setEmphasis(true);
			$printer -> text(str_pad("", 42, '-', STR_PAD_RIGHT));
			$printer -> text(str_pad("Cashier Name : ".ucfirst($user_name), 42, ' ', STR_PAD_RIGHT));
			$printer -> text(str_pad("", 42, '-', STR_PAD_RIGHT));
			$printer -> setEmphasis(false);

			/*Other Details*/
			//$printer -> feed(2);
			$printer -> text("Order type : ".ucfirst(str_replace("_", ' ', $order_type))."\n");
			if($order_type != 'delivery' && $payment_type != '') {
				$printer -> text("Payment method : ".$payment_type."\n");
			}
			if($remarks1 == 'yes') {
				$printer -> text("Remarks : ".$remarks."\n");
			}
			if($order_type == 'dine_in') {
				$printer -> text("Floor no : ".$floor_no."  Table no : ".$table_no."\n");
			}		

			/*Customer Details*/
			if($deliver == 'yes') {
				//$printer -> feed(1);
				$printer -> text("Name : ".$cus_name."\n");
				$printer -> text("Number : ".$cus_num."\n");
				$printer -> text("Address : ".$address."\n");
			}

			/* Footer */
			//$printer -> feed(2);
			$printer -> setJustification(Printer::JUSTIFY_CENTER);
			$printer -> text(BILL_FOOTER."\n");
			//$printer -> feed(2);
			$printer -> text($date . "\n");
			$printer -> selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
			$printer -> text("Recipt no : ".$receipt_id."\n");
			//$printer -> feed(1);
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

if($both_print || $kot_print) {
	try {
		// Enter the share name for your USB printer here
		//$connector = null;
		$connector = new WindowsPrintConnector(KITCHEN_COPY);

		/* Print a "Hello world" receipt" */
		$printer = new Printer($connector);
		//$printer -> text($print_content);
		//$printer -> cut();

		//New Lines Added
		
		$date = date("d-m-Y H:i:s");
		/* Start the printer */
		$printer = new Printer($connector);

		
		/* Name of shop */
		//$printer -> feed();
		$printer -> setJustification(Printer::JUSTIFY_CENTER);
		$printer -> selectPrintMode(Printer::MODE_DOUBLE_HEIGHT);
		$printer -> text(CLIENT_NAME."\n");
		if($duplicate){
			$printer -> selectPrintMode(Printer::MODE_FONT_A);
			$printer -> text("-- DUPLICATE COPY --\n");
		}
		//$printer -> feed();

		/* Description */
		$printer -> setEmphasis(true);
		$printer -> selectPrintMode();
		$printer -> text(str_pad("Item", 33, ' ', STR_PAD_RIGHT).str_pad("Qty", 9, ' ', STR_PAD_LEFT));
		$printer -> text(str_pad("", 42, '-', STR_PAD_RIGHT));
		$printer -> setEmphasis(false);
		//$printer -> feed(1);

		/* Items */
		if($old_item && $old_qty){
			foreach($result_arr_kitchen as $key => $res){
				$printer -> setEmphasis(true);
				$printer -> selectPrintMode();
				$printer -> text(str_pad(strbefore($key, '%%'), 33, ' ', STR_PAD_RIGHT).str_pad(strafter($key, '%%'), 9, ' ', STR_PAD_LEFT));
				//Notes Row
				if($res != '') {
					$printer -> text(str_pad("-- ".$res, 42, ' ', STR_PAD_RIGHT));
				}
				$printer -> setEmphasis(false);
				//$printer -> feed(1);
			}
		} else {
			foreach($result_arr as $key => $res){
				$printer -> setEmphasis(true);
				$printer -> selectPrintMode();
				$printer -> text(str_pad($res['item_name'], 33, ' ', STR_PAD_RIGHT).str_pad($res['qty'], 9, ' ', STR_PAD_LEFT));
				//Notes Row
				/*if($res['notes'] != '') {
					$printer -> text(str_pad("-- ".$res['notes'], 42, ' ', STR_PAD_RIGHT));
				}*/
				$printer -> setEmphasis(false);
				//$printer -> feed(1);
			}
		}

		/*Customer Details*/
			if($deliver == 'yes') {
				//$printer -> feed(1);
				$printer -> text("Name : ".$cus_name."\n");
				$printer -> text("Number : ".$cus_num."\n");
				$printer -> text("Address : ".$address."\n");
			}

		/* Footer */
		//$printer -> feed(2);
		$printer -> setJustification(Printer::JUSTIFY_CENTER);
		if($order_type == 'dine_in') {
			$printer -> text("Floor no : ".$floor_no."  Table no : ".$table_no."\n");
		}
		$printer -> text($date . "\n");
		$printer -> selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
		$printer -> text("Recipt no : ".$receipt_id."\n");
		//$printer -> feed(1);		
		
		/* Close printer */
		$printer -> cut();
		$printer -> close();

	} catch (Exception $e) {
		echo "Couldn't print to this printer: " . $e -> getMessage() . "\n";
	}
}
//die;
//redirect($redirect);
$json['success'] = 'success';
echo json_encode($json);