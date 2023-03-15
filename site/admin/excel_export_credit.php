<?php
//ini_set('display_errors', 1);
//error_reporting(E_ALL);
//echo "fhgtj";die;
//session_start();
include("../functions.php");
include_once("../config.php");
connect_dre_db();

$customer_id = (isset($_GET['customer_id']) && $_GET['customer_id'] !='') ? $_GET['customer_id'] : '';

function getCreditPost($customer_id = '')
{
	$qry="SELECT customer_id, name, number, SUM(CASE WHEN type='credit' THEN amount END) as credit,
       SUM(CASE WHEN type='debit' THEN amount END) as debit FROM credit_sale";
	if($customer_id != ''){
		$qry .=" WHERE customer_id = '$customer_id'";
	}
	$qry .=" GROUP BY customer_id ORDER BY customer_id DESC";
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
$table = '';
$filename = time();

$table .= '<table border="1" cellspacing="0" bordercolor="#222"><tr>'; 
        $table .= '<td style="background-color:#244062; color:#fff;">Id</td>';
        $table .= '<td style="background-color:#244062; color:#fff;">Customer name</td>';
        $table .= '<td style="background-color:#244062; color:#fff;">Customer number</td>';
		$table .= '<td style="background-color:#244062; color:#fff;">Balance</td>';
        $table .= '</tr>';

		$grand_total = "0.00";												
$prs = getCreditPost($customer_id);										
if($prs != false) {
	$pcount = mysqli_num_rows($prs);
	if($pcount > 0) {
		for($p = 0; $p < $pcount; $p++) {
			$prow = mysqli_fetch_object($prs);
			$customer_id = $prow->customer_id;
			$name = $prow->name;
			$number = $prow->number;
			$credit = $prow->credit;
			$debit = $prow->debit;
			$total = "0.00";

        $table .= "<tr>";
			$table .=  "<td>".$customer_id."</td>";
			$table .=  "<td>".$name."</td>";		
			$table .=  "<td>".$number."</td>";
			$table .=  "<td style='background-color:#ffff00;'>".($credit - $debit)."</td>";
			$table .=  "</tr>";
		} } }
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