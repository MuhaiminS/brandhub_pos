<?php
//ini_set('display_errors', 1);
//error_reporting(E_ALL);
//echo "fhgtj";die;
//session_start();
include("../functions.php");
include_once("../config.php");
connect_dre_db();
$shop ='';

function getExpenseCategory($expense_category_id)
{
	$where = "WHERE id = '$expense_category_id'";
	$service = getnamewhere('expense_category', 'expense_name', $where);
	return $service;
}

$from_date = (isset($_GET['from_date']) && $_GET['from_date'] !='') ? $_GET['from_date'] : '';
$to_date = (isset($_GET['to_date']) && $_GET['to_date'] !='') ? $_GET['to_date'] : '';

function getSaleorders($from_date ='', $to_date='')
{
	$date = date('Y-m-d');
	$qry= "SELECT * FROM expense WHERE 1";
	
	
	if($from_date != '' && $to_date != '' ) {
		
		$qry .= " AND date_added BETWEEN '$from_date 00:00:00' AND '$to_date 23:59:59' ";		
	} 
	if($from_date != '' && $to_date == '' ) {
		$qry .= " AND date_added >= '$from_date 23:59:59'";
	}

	if($from_date == '' && $to_date != '' ) {
		$qry .= " AND date_added <= '$to_date 23:59:59'";
	}
	$qry .=" ORDER BY id DESC";
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

$from_date = (isset($_GET['from_date']) && $_GET['from_date'] !='') ? $_GET['from_date'] : '';
$to_date = (isset($_GET['to_date']) && $_GET['to_date'] !='') ? $_GET['to_date'] : '';


$table = '';
$filename = time();

$table .= '<p>From date: '.$from_date.'</p>';
$table .= '<p>To date: '.$to_date.'</p>';

$table .= '<table border="1" cellspacing="0" bordercolor="#222"><tr>'; 
        $table .= '<td style="background-color:#244062; color:#fff;">Id</td>';
		$table .= '<td style="background-color:#244062; color:#fff;">Date</td>';
		$table .= '<td style="background-color:#244062; color:#fff;">TRN</td>';
		$table .= '<td style="background-color:#244062; color:#fff;">Expense category</td>';
		$table .= '<td style="background-color:#244062; color:#fff;">Company</td>';
		$table .= '<td style="background-color:#244062; color:#fff;">Status</td>';
		$table .= '<td style="background-color:#244062; color:#fff;">Description</td>';
        $table .= '<td style="background-color:#244062; color:#fff;">Amount</td>';
        $table .= '<td style="background-color:#244062; color:#fff;">VAT</td>';
		 $table .= '<td style="background-color:#244062; color:#fff;">Total amount</td>';
        $table .= '</tr>';

  $grand_total = $amount_total = $vat_total = "0.00";												
	$prs = getSaleorders($from_date, $to_date);
	if($prs != false) {
		$pcount = mysqli_num_rows($prs);
		if($pcount > 0) {
			$total_vat = $total12 = $total11 ="0.00";
			for($p = 0; $p < $pcount; $p++) {
				$prow = mysqli_fetch_object($prs);
				$id = $prow->id;
				$amount = $prow->net_total;
				$vat = $prow->vat_amount;
				$trn_no = $prow->trn_no;
				$company_name = $prow->company_name;
				$payment_status = $prow->payment_status;
				$payment_status = ($prow->payment_status == 'paid') ? 'Paid' : 'Not Paid';
				$expense_category_id = getExpenseCategory($prow->expense_category_id);
				$description = $prow->description;
				$date_added = $prow->date_added;
				$total = $amount + $vat;
				
				$grand_total += $amount;
				$vat_total += $vat;
				$amount_total += $total;

        $table .= "<tr>";
			$table .=  "<td>".$id."</td>";
			$table .=  "<td>".$date_added."</td>";
			$table .=  "<td>".$trn_no."</td>";
			$table .=  "<td>".$expense_category_id."</td>";	
			$table .=  "<td>".$company_name."</td>";	
			$table .=  "<td>".$payment_status."</td>";	
			$table .=  "<td>".$description."</td>";
			$table .=  "<td>".$amount."</td>";			
			$table .=  "<td>".$vat."</td>";	
			$table .=  "<td>".$total."</td>";
			$table .=  "</tr>";
		} 
		$table .=  "<tr>";
		$table .=  "<td colspan='7'>Final balance</td>";
		$table .=  "<td colspan='1' style='background-color:#ffff00;'>".number_format($grand_total, 2)."</td>";
		$table .=  "<td colspan='1' style='background-color:#ffff00;'>".number_format($vat_total, 2)."</td>";
		$table .=  "<td colspan='1' style='background-color:#ffff00;'>".number_format($amount_total, 2)."</td>";
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