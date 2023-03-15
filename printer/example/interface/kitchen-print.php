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

mysqli_set_charset($GLOBALS['conn'], 'UTF8');
$server_url = getServerURL();
$sale_order_id = $_GET['id'];
$redirect = $_GET['re'];
$both_print = (isset($_GET['both_print']) && $_GET['both_print'] !='') ? $_GET['both_print'] : '';

$result = mysqli_query($GLOBALS['conn'], "SELECT * FROM ".DB_PRIFIX."sale_orders WHERE id = $sale_order_id");  
$sale_insert =  mysqli_fetch_assoc($result);
$sale_order_id = $sale_insert['id'];
$receipt_id = $sale_insert['receipt_id'];
$order_type = $sale_insert['order_type'];
$floor_no = $sale_insert['floor_id'];
$table_no = $sale_insert['table_id'];
$ordered_date = date("d-m-Y H:i:s", strtotime($sale_insert['ordered_date']));
$result_arr = array();
$sql = "SELECT * FROM ".DB_PRIFIX."sale_order_items WHERE sale_order_id = $sale_order_id";
$result_val = mysqli_query($GLOBALS['conn'], $sql);
while ($row = mysqli_fetch_assoc($result_val)) {
	$result_arr[] = $row;			
}
 
try {
    // Enter the share name for your USB printer here
    //$connector = null;
    $connector = new WindowsPrintConnector("BM-C02");

    /* Print a "Hello world" receipt" */
    $printer = new Printer($connector);
    //$printer -> text($print_content);
    //$printer -> cut();

	//New Lines Added
	
	$date = date("d-m-Y H:i:s");
	/* Start the printer */
	$printer = new Printer($connector);

	
	/* Name of shop */
	$printer -> feed();
	$printer -> setJustification(Printer::JUSTIFY_CENTER);
	$printer -> selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
	$printer -> text("Oversee POS Solution\n");
	$printer -> feed();

	/* Description */
	$printer -> setEmphasis(true);
	$printer -> selectPrintMode();
	$printer -> text(str_pad("Item", 40, ' ', STR_PAD_RIGHT).str_pad("Qty", 8, ' ', STR_PAD_LEFT));
	$printer -> text(str_pad("", 48, '-', STR_PAD_RIGHT));
	$printer -> setEmphasis(false);
	$printer -> feed(1);

	/* Items */
	foreach($result_arr as $key => $res){
		$printer -> setEmphasis(true);
		$printer -> selectPrintMode();
		$printer -> text(str_pad($res ['item_name'], 40, ' ', STR_PAD_RIGHT).str_pad($res['qty'], 8, ' ', STR_PAD_LEFT));
		$printer -> setEmphasis(false);
		$printer -> feed(1);
	}

	

	/* Footer */
	$printer -> feed(2);
	$printer -> setJustification(Printer::JUSTIFY_CENTER);
	$printer -> text($date . "\n");
	$printer -> text("Recipt no : ".$receipt_id."\n");
	$printer -> feed(1);
    
    /* Close printer */
	$printer -> cut();
    $printer -> close();


} catch (Exception $e) {
    echo "Couldn't print to this printer: " . $e -> getMessage() . "\n";
}
