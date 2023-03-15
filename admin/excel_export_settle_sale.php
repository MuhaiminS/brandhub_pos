<?php
//ini_set('display_errors', 1);
//error_reporting(E_ALL);
//echo "fhgtj";die;
//session_start();
include("../functions.php");
include_once("../config.php");
connect_dre_db();
function getUserName($user_id)
{
	$where = "WHERE id = '$user_id'";
	$service = getnamewhere('users', 'user_name', $where);
	return $service;
}

function getShopsList()
{
	$service = array();
	$query="SELECT * FROM locations_shops ORDER BY id ASC";
	$run = mysql_query($query);
	while($row = mysql_fetch_array($run)) {
		$shop_id = $row['id'];
		$service[$shop_id]['shop_id'] = $row['id'];
		$service[$shop_id]['shop_name'] = $row['shop_name'];
	}
	return $service;	

}

function getShopName($shop_id)
{
	$where = "WHERE id = '$shop_id'";
	$service = getnamewhere('locations_shops', 'shop_name', $where);
	return $service;
}

function getLastSettle($user_id, $settle_id) {

	$settle_date = '0';
	$query = "SELECT `settle_date` FROM `settle_sale` WHERE user_id = '$user_id' AND id < '$settle_id' order by settle_date DESC LIMIT 1";	
	$run = mysqli_query($GLOBALS['conn'], $query);
	while($row = mysqli_fetch_assoc($run)) {
		$settle_date = $row['settle_date'];		
	}
	//echo $settle_date; die;
	return $settle_date;
}


if(isset($_GET["page"])) {
	$page = (int)$_GET["page"];
} else {
	$page = 1;
}
$setLimit = 50;
$pageLimit = ($page * $setLimit) - $setLimit;

function getSettleSales($from_date ='', $to_date='', $shop='', $pageLimit='', $setLimit='', $export="")
{
	$date = date('Y-m-d');
	$qry="SELECT *, DATE_FORMAT(settle_date,'%Y-%m-%d') as settle_dat FROM settle_sale WHERE id != ''"; 
	
	if($shop != ''){
		$qry .=" AND shop_id = '$shop'";
	}
	
	if($from_date != '' && $to_date != '' ) {
		
		$qry .= " AND settle_date BETWEEN '$from_date 00:00:00' AND '$to_date 23:59:59' ";		
	} 
	if($from_date != '' && $to_date == '' ) {
		$qry .= " AND settle_date BETWEEN '$from_date 00:00:00' AND '$date 23:59:59' ";
	}

	if($from_date == '' && $to_date != '' ) {
		$qry .= " AND settle_date <= '$to_date 23:59:59'";
	}
	//if($export == '') {
		//$qry .=" ORDER BY settle_date DESC LIMIT $pageLimit, $setLimit";
	//} else {
		//$qry .=" ORDER BY settle_date DESC ";
	//}
	//echo $qry; die;
	$result=mysqli_query($GLOBALS['conn'], $qry);
	//$num=mysql_num_rows($result);	//echo "total result ".$num;
	if($result)
	{
		return $result;
	}
	else
	return false;
}


$reference_id = (isset($_GET['reference_id']) && $_GET['reference_id'] !='') ? $_GET['reference_id'] : '';
$payment_status = (isset($_GET['payment_status']) && $_GET['payment_status'] !='') ? $_GET['payment_status'] : '';
$from_date = (isset($_GET['from_date']) && $_GET['from_date'] !='') ? $_GET['from_date'] : '';
$to_date = (isset($_GET['to_date']) && $_GET['to_date'] !='') ? $_GET['to_date'] : '';



$user = (isset($_GET['user']) && $_GET['user'] !='') ? $_GET['user'] : '';
$receipt_id = (isset($_GET['receipt_id']) && $_GET['receipt_id'] !='') ? $_GET['receipt_id'] : '';
$payment_type = (isset($_GET['payment_type']) && $_GET['payment_type'] !='') ? $_GET['payment_type'] : '';
$from_date = (isset($_GET['from_date']) && $_GET['from_date'] !='') ? $_GET['from_date'] : '';
$to_date = (isset($_GET['to_date']) && $_GET['to_date'] !='') ? $_GET['to_date'] : '';


$table = '';
$filename = time();

$table .= '<table border="1" cellspacing="0" bordercolor="#222"><tr>'; 
        $table .= '<td style="background-color:#244062; color:#fff;">Id</td>';
        $table .= '<td style="background-color:#244062; color:#fff;">Settle Date</td>';
        $table .= '<td style="background-color:#244062; color:#fff;">User</td>';
        $table .= '<td style="background-color:#244062; color:#fff;">Shop</td>';
		$table .= '<td style="background-color:#244062; color:#fff;">Cash Sale</td>';	
        $table .= '<td style="background-color:#244062; color:#fff;">Card Sale</td>';      
        $table .= '<td style="background-color:#244062; color:#fff;">Delivery Sale</td>';
	  $table .= '<td style="background-color:#244062; color:#fff;">Sale Total</td>';
		 $table .= '<td style="background-color:#244062; color:#fff;">Discount</td>';
 if(BILL_TAX == 'yes'){ if(BILL_TAX_TYPE == 'VAT'){ 
		 $table .= '<td style="background-color:#244062; color:#fff;">VAT</td>';
} elseif(BILL_TAX_TYPE == 'GST'){
	 $table .= '<td style="background-color:#244062; color:#fff;">GST</td>';
	 } }
		 $table .= '<td style="background-color:#244062; color:#fff;">Net Total</td>';
        $table .= '</tr>';

  $grand_total = $total_vat1 = "0.00";												
	$prs = getSettleSales($from_date, $to_date);
	if($prs != false) {
		$pcount = mysqli_num_rows($prs);
		if($pcount > 0) {
			for($p = 0; $p < $pcount; $p++) {
				$prow = mysqli_fetch_object($prs);
				$id = $prow->id;
				$user_id = $prow->user_id;
				$settle_dat = $prow->settle_date;
				$cash_sale = ($prow->cash_sale !='') ? $prow->cash_sale :  '0.00' ;
				$card_sale = ($prow->card_sale !='') ? $prow->card_sale :  '0.00' ;
				$total_cgst = ($prow->total_cgst !='') ? $prow->total_cgst :  '0.00' ;
				$total_sgst = ($prow->total_sgst !='') ? $prow->total_sgst :  '0.00' ;
				$total_gst = ($prow->total_gst !='') ? $prow->total_gst :  '0.00' ;
				$delivery_sale = ($prow->delivery_sale !='') ? $prow->delivery_sale : '0.00';
				$discount = ($prow->discount !='') ? $prow->discount :  '0.00' ;
				$user_name = getUserName($prow->user_id);
				//$name = getManufacturingUnitName($prow->manufacturing_unit_id);
				$shop_name = getShopName($prow->shop_id);
				$total_without_discount = ($prow->gross_total !='') ? $prow->gross_total :  '0.00' ;
				$total_with_discount = $prow->net_total;
				$total = "0.00";
				$total_vat = $prow->total_vat;
				
				$grand_total += $total_without_discount-$discount;
				$total_vat1 += $total_vat;
				
        $table .= "<tr>";
			$table .=  "<td>".$id."</td>";
			$table .=  "<td>".$settle_dat."</td>";
			$table .=  "<td>".$user_name."</td>";	
			$table .=  "<td>".$shop_name."</td>";
			$table .=  "<td>".$cash_sale."</td>";
			$table .=  "<td>".$card_sale."</td>";
			$table .=  "<td>".$delivery_sale."</td>";
			$table .=  "<td>".number_format($total_without_discount, 2)."</td>";
			$table .=  "<td>".$discount."</td>";
			if(BILL_TAX == 'yes'){ if(BILL_TAX_TYPE == 'VAT'){ 
			$table .=  "<td>".$total_vat."</td>";
			} elseif(BILL_TAX_TYPE == 'GST'){ 
			$table .=  "<td>".number_format($total_gst, 2)."</td>";
			}}
			$table .=  "<td style='background-color:#ffff00;'>".number_format(($total_without_discount-$discount), 2)."</td>";
			$table .=  "</tr>";
		}
		$table .=  "<tr>";
		$table .=  "<td colspan='9'>Final balance</td>";
		$table .=  "<td style='background-color:#ffff00;'>".number_format($total_vat1, 2)."</td>";
		$table .=  "<td style='background-color:#ffff00;'>".number_format($grand_total, 2)."</td>";
		$table .=  "</tr>";
		} }
		$table .= '</table>';
header("Pragma: public");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
header("Content-Type: application/force-download");
header("Content-Type: application/octet-stream");
header("Content-Type: application/download");;
header("Content-Disposition: attachment;filename=$filename.xls "); 
header("Content-Transfer-Encoding: binary ");
echo $table;
///echo "<script>window.close();</script>";
?>