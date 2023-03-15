<?php
//ini_set('display_errors', 1);
//error_reporting(E_ALL);
//echo "fhgtj";die;
//session_start();
include("../functions.php");
include_once("../config.php");
connect_dre_db();

$item_id = (isset($_GET['item_id']) && $_GET['item_id'] !='') ? $_GET['item_id'] : '';

function getStockPost($item_id = '')
{
	$qry="SELECT id, name, stock, price FROM items WHERE 1";
	if($item_id){
	    $qry .= " AND id = $item_id";
	}
	$qry .= " AND active != '0' ORDER BY name ASC";
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
        $table .= '<td style="background-color:#244062; color:#fff;">S.no</td>';
        $table .= '<td style="background-color:#244062; color:#fff;">Item name</td>';
        $table .= '<td style="background-color:#244062; color:#fff;">Stock</td>';
        $table .= '<td style="background-color:#244062; color:#fff;">Unit price</td>';
        $table .= '<td style="background-color:#244062; color:#fff;">Price</td>';
        $table .= '</tr>';

$prs = getStockPost($item_id);										
if($prs != false) {
	$pcount = mysqli_num_rows($prs);
	if($pcount > 0) {
		for($p = 0; $p < $pcount; $p++) {
			$prow = mysqli_fetch_object($prs);
			$name = $prow->name;
			$stock = $prow->stock;
			$price = $prow->price;
			$total = "0.00";

        $table .= "<tr>";
			$table .=  "<td>".($p+1)."</td>";
			$table .=  "<td>".$name."</td>";
			$table .=  "<td style='background-color:#ffff00;'>".($stock)."</td>";
			$table .=  "<td>".number_format((float)$price, 2, '.', '')."</td>";
			$table .=  "<td>".number_format((float)$stock*$price, 2, '.', '')."</td>";
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