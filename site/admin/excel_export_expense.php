<?php
//ini_set('display_errors', 1);
//error_reporting(E_ALL);
//echo "fhgtj";die;
//session_start();
include("../functions.php");
include_once("../config.php");
connect_dre_db();
$shop ='';
function getUserName($user_id)
{
	$where = "WHERE id = '$user_id'";
	$service = getnamewhere('users', 'user_name', $where);
	return $service;
}

function getItemNames($item_id)
{
	$where = "WHERE id = '$item_id'";
	$service = getnamewhere('items', 'name', $where);
	return $service;
}

function getExpenseCategory($expense_category_id)
{
	$where = "WHERE id = '$expense_category_id'";
	$value = getnamewhere('expense_category', 'expense_name', $where);
	return $value;
}

function getShopsList()
{
	$service = array();
	$query="SELECT * FROM locations_shops ORDER BY id ASC";
	$run = mysqli_query($GLOBALS['conn'], $query);
	while($row = mysqli_fetch_array($run)) {
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
function getSaleOrderitems($id)
{	
	$id = isset($id) ? $id : '';
	$qry="SELECT * FROM sale_order_items WHERE  sale_order_id = '".$id."'";
	
	//echo $qry;
	$result=mysqli_query($GLOBALS['conn'], $qry);
	$num=mysqli_num_rows($result);
	
	if($num>0)
	{
		return $result;
	}
	else
	return false;
}	

/*function getnamewhere($tabname,$name,$where)     // pass the table name , name of field to return all the values
{

				$qry="SELECT $name FROM $tabname $where";
				//echo $qry;
				$result=mysqli_query($GLOBALS['conn'], $GLOBALS['conn'], $qry);
				$num=mysqli_num_rows($result);
				$i=0;
				$varname = '';
				if($num>0)
				{
					while($row = mysqli_fetch_assoc($result)) {					   
					   $varname = $row[$name]; 
					}
					//$varname=safeTextOut(mysqli_result($result,$i,$name));
					
				}
				return $varname;

}*/


$reference_id = (isset($_GET['reference_id']) && $_GET['reference_id'] !='') ? $_GET['reference_id'] : '';
$payment_status = (isset($_GET['payment_status']) && $_GET['payment_status'] !='') ? $_GET['payment_status'] : '';
$from_date = (isset($_GET['from_date']) && $_GET['from_date'] !='') ? $_GET['from_date'] : '';
$to_date = (isset($_GET['to_date']) && $_GET['to_date'] !='') ? $_GET['to_date'] : '';

function getSaleorders($reference_id = '',$payment_status = '', $from_date ='', $to_date='', $shop='')
{
	$date = date('Y-m-d');
	$qry="SELECT * FROM expense WHERE 1";
	
	if($reference_id != ''){
		$qry .=" AND reference_id = '$reference_id'";
	}
	if($payment_status != ''){
		$qry .=" AND payment_status = '$payment_status'";
	}
	if($from_date != '' && $to_date != '' ) {
		
		$qry .= " AND purchase_date BETWEEN '$from_date 00:00:00' AND '$to_date 23:59:59' ";		
	} 
	if($from_date != '' && $to_date == '' ) {
		$qry .= " AND purchase_date >= '$from_date 23:59:59'";
	}

	if($from_date == '' && $to_date != '' ) {
		$qry .= " AND purchase_date <= '$to_date 23:59:59'";
	}
	$qry .=" ORDER BY id ASC";
	//echo $qry;
	$result=mysqli_query($GLOBALS['conn'], $qry);
	$num=mysqli_num_rows($result);
	//echo "total result ".$num;
	if($num>0)
	{
		return $result;
	}
	else
	return false;
}

function getPurchaseOrderitems($id)
{	
	$id = isset($id) ? $id : '';
	$qry="SELECT * FROM purchase_order_items WHERE purchase_id = '".$id."'";
	
	//echo $qry;
	$result=mysqli_query($GLOBALS['conn'], $qry);
	$num=mysqli_num_rows($result);
	
	if($num>0)
	{
		return $result;
	}
	else
	return false;
}	

$status_arr =  array(
	'pending' => 'Pending',
	'progressing' => 'Progressing',
	'ready_for_delivery' => 'Ready for delivery',
	'completed' => 'Completed',
	'delivered' => 'Delivered',
	'cancel' => 'canceled',
	'draft' => 'Draft'
);

$user = (isset($_GET['user']) && $_GET['user'] !='') ? $_GET['user'] : '';
$receipt_id = (isset($_GET['receipt_id']) && $_GET['receipt_id'] !='') ? $_GET['receipt_id'] : '';
$payment_type = (isset($_GET['payment_type']) && $_GET['payment_type'] !='') ? $_GET['payment_type'] : '';
$from_date = (isset($_GET['from_date']) && $_GET['from_date'] !='') ? $_GET['from_date'] : '';
$to_date = (isset($_GET['to_date']) && $_GET['to_date'] !='') ? $_GET['to_date'] : '';


$table = '';
$filename = time();

$table .= '<table border="1" cellspacing="0" bordercolor="#222"><tr>'; 
        $table .= '<td style="background-color:#244062; color:#fff;">Id</td>';
        $table .= '<td style="background-color:#244062; color:#fff;">Invoice No</td>';
        $table .= '<td style="background-color:#244062; color:#fff;">Purchase Date</td>';
		//$table .= '<td style="background-color:#244062; color:#fff;">Category</td>';
        $table .= '<td style="background-color:#244062; color:#fff;">TRN Nos</td>';
		$table .= '<td style="background-color:#244062; color:#fff;">Company name</td>';
		$table .= '<td style="background-color:#244062; color:#fff;">Description</td>';
        $table .= '<td style="background-color:#244062; color:#fff;">Payment Status</td>';      
        $table .= '<td style="background-color:#244062; color:#fff;">Sub Total</td>';
	  $table .= '<td style="background-color:#244062; color:#fff;">VAT Amount</td>';
		 $table .= '<td style="background-color:#244062; color:#fff;">Net amount</td>';
        $table .= '</tr>';

  $vat_total1 = $net_total1 = $sub_total1 = "0.00";												
	$prs = getSaleorders($reference_id,$payment_status, $from_date, $to_date, $shop);
	if($prs != false) {
		$pcount = mysqli_num_rows($prs);
		if($pcount > 0) {
			for($p = 0; $p < $pcount; $p++) {
				$prow = mysqli_fetch_object($prs);
				$id = $prow->id;
				$reference_id = $prow->reference_id;
				$trn_no = $prow->trn_no;
				$company_name = $prow->company_name;
				$payment_status = $prow->payment_status;
				$purchase_date = $prow->purchase_date;
				$sub_total = $prow->sub_total;
				$vat_total = $prow->vat_amount;
				$net_total = $prow->net_total;
				$description = $prow->description;
				$expense_category = $prow->expense_category_id;
				$payment_status = ($prow->payment_status == 'paid') ? 'Paid' : 'Not Paid';
				$rev_payment_status = ($prow->payment_status == 'paid') ? 'Paid' : 'Not Paid';
				
				$sub_total1 += $sub_total;
				$vat_total1 += $vat_total;
				$net_total1 += $net_total;
				
        $table .= "<tr>";
			$table .=  "<td>".$id."</td>";
			$table .=  "<td>".$reference_id."</td>";
			$table .=  "<td>".$purchase_date."</td>";
			//$table .=  "<td>".getExpenseCategory($expense_category)."</td>";
			$table .=  "<td>".$trn_no."</td>";
			$table .=  "<td>".$company_name."</td>";
			$table .=  "<td>".$description."</td>";
			$table .=  "<td>".ucfirst($payment_status)."</td>";
			$table .=  "<td>".$sub_total."</td>";
			$table .=  "<td>".$vat_total."</td>";
			$table .=  "<td>".$net_total."</td>";
			$table .=  "</tr>";
		}
		$table .=  "<tr>";
		$table .=  "<td colspan='7'>Final balance</td>";
		$table .=  "<td>".number_format($sub_total1, 2)."</td>";
		$table .=  "<td>".number_format($vat_total1, 2)."</td>";
		$table .=  "<td>".number_format($net_total1, 2)."</td>";
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