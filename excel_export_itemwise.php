<?php
   session_start();
   include("functions.php");
   include_once("config.php");
   chkAdminLoggedIn();
   connect_dre_db();
   if (isset($_GET['act']) && $_GET['act'] != '' && isset($_GET['id']) && $_GET['id'] > 0) {
       $action = $_GET['act'];
       $id     = $_GET['id'];
       if ($action == 'delete') {
           $qry = "UPDATE items SET active = '0' WHERE id = $id";
           if (mysqli_query($GLOBALS['conn'], $qry)) {
               redirect('products.php?resp=succ');
           }
       }
   }
   
   function getcategoryName($category_id)
   {
       $where    = "WHERE id = '$category_id'";
       $category = getnamewhere('category', 'category_title', $where);
       return $category;
   }
   
   function getIncrediantName($incrediant_id)
   {
       $where    = "WHERE id = '$incrediant_id'";
       $category = getnamewhere('incrediants', 'name', $where);
       return $category;
   }
   
   function getCategoryList()
   {
       $service = array();
       $query   = "SELECT * FROM item_category WHERE active != '0' ORDER BY category_title ASC";
       $run     = mysqli_query($GLOBALS['conn'], $query);
       while ($row = mysqli_fetch_array($run)) {
           $cat_id                             = $row['id'];
           $service[$cat_id]['cat_id']         = $row['id'];
           $service[$cat_id]['category_title'] = $row['category_title'];
       }
       return $service;
       
   }

   function group_by($key, $data) {
    $result = array();

    foreach($data as $val) {
        if(array_key_exists($key, $val)){
            $result[$val[$key]][] = $val;
        }else{
            $result[""][] = $val;
        }
    }

    return $result;
}
   
   if (isset($_GET["page"])) {
       $page = (int) $_GET["page"];
   } else {
       $page = 1;
   }
   $setLimit  = 500;
   $pageLimit = ($page * $setLimit) - $setLimit;
   
   $name     = (isset($_GET['name']) && $_GET['name'] != '') ? $_GET['name'] : '';
   $category = (isset($_GET['category']) && $_GET['category'] != '') ? $_GET['category'] : '';
   
   function getSaleOrderItemDetailsListItemWise($from_date = '', $to_date = '', $shop = '', $pageLimit = '', $setLimit = '', $export = "")
   {
       
       $qry = "SELECT soi.item_id, soi.item_name, soi.price,soi.qty as qty,(SUM(soi.qty)*soi.price) as amount FROM sale_order_items as soi LEFT JOIN sale_orders as so ON (so.id = soi.sale_order_id) WHERE '1' ";
       
       if ($from_date != '' && $to_date != '') {
           $qry .= " AND so.ordered_date BETWEEN '$from_date 00:00:00' AND '$to_date 23:59:59' ";
       }
       $qry .= "  GROUP BY soi.item_name,soi.price ORDER BY item_name ASC ";
       // echo $qry; 
       $result = mysqli_query($GLOBALS['conn'], $qry);
       if ($result) {
           /*$result_arr = array();
           while ($row = mysqli_fetch_assoc($result)) {
           $result_arr[] = $row;            
           }
           return $result_arr;*/
           return $result;
       } else {
           return false;
       }
   }
   
   
   function getReceipeIncrediantList($id)
   {
       $id  = isset($id) ? $id : '';
       $qry = "SELECT * FROM receipe_incrediant_manage WHERE receipe_id = '" . $id . "'";
       
       //echo $qry;
       $result = mysqli_query($GLOBALS['conn'], $qry);
       $num    = mysqli_num_rows($result);
       
       if ($num > 0) {
           return $result;
       } else
           return false;
   }
   
   function getItemNames($receipe_id)
   {
       $where   = "WHERE id = '$receipe_id'";
       $service = getnamewhere('items', 'name', $where);
       return $service;
   }
   function getIncunitNames($incunit_id)
   {
       $where   = "WHERE id = '$incunit_id'";
       $service = getnamewhere('incrediant_units', 'unit_name', $where);
       return $service;
   }
   $items_img_dir = "../item_images/";
   
   $shops     = (isset($_GET['shop']) && $_GET['shop'] != '') ? $_GET['shop'] : '';
   $from_date = (isset($_GET['from_date']) && $_GET['from_date'] != '') ? $_GET['from_date'] : date('Y-m-d');
   $to_date   = (isset($_GET['to_date']) && $_GET['to_date'] != '') ? $_GET['to_date'] : date('Y-m-d');
   
   



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






$status_arr =  array(
	'pending' => 'Pending',
	'progressing' => 'Progressing',
	'ready_for_delivery' => 'Ready for delivery',
	'completed' => 'Completed',
	'delivered' => 'Delivered',
	'cancel' => 'canceled',
	'draft' => 'Draft'
);





$table = '';
$filename = time();

$table .= '<table border="1" cellspacing="0" bordercolor="#222"><tr>'; 
       
        $table .= '<td style="background-color:#244062; color:#fff;">Item</td>';
        $table .= '<td style="background-color:#244062; color:#fff;">Qty</td>';
        $table .= '<td style="background-color:#244062; color:#fff;">Amount</td>';
        $table .= '</tr>';


                                  
                                    $prs = getSaleOrderItemDetailsListItemWise($from_date, $to_date, $shops, $pageLimit, $setLimit, $export = "");
                                    if ($prs != false) {
                                        $pcount = mysqli_num_rows($prs);
                                        $itmsales_arr = array();
                                        if ($pcount > 0) {
                                            for ($p = 0; $p < $pcount; $p++) {
                                                $prow  = mysqli_fetch_object($prs);
                                                $wgtnqty = $prow->qty;
                                                $total = $prow->amount;
                                                $itmsales_arr[$prow->item_id]['id'] = $prow->item_id;  
                                                // $itmsales_arr[$prow->item_id]['unit'] = $prow->item_unit;
                                                $itmsales_arr[$prow->item_id]['name'] = $prow->item_name;
                                                if(isset($itmsales_arr[$prow->item_id]['wgtnqty']))
                                                  $itmsales_arr[$prow->item_id]['wgtnqty'] += $wgtnqty;
                                                else $itmsales_arr[$prow->item_id]['wgtnqty'] = $wgtnqty;

                                                if(isset($itmsales_arr[$prow->item_id]['tot_amnt']))
                                                  $itmsales_arr[$prow->item_id]['tot_amnt'] += $total;
                                                else
                                                  $itmsales_arr[$prow->item_id]['tot_amnt'] = $total;
                                            }
                                        }
                                    }
                                 $i = $grand_total = $total_qty = 0;
                                 if(isset($itmsales_arr) && count($itmsales_arr) > 0) {
                                  foreach($itmsales_arr as $itmsales) {
                                
                                   
			
			$table .= "<tr>";
			// $table .=  "<td>".$i = $i+1."</td>";
			$table .=  "<td>".$itmsales['name']."</td>";
			$table .=  "<td>".$itmsales['wgtnqty']."</td>";
			$table .=  "<td>".number_format($itmsales['tot_amnt'],2)."</td>";
			$table .=  "</tr>";

          
                                    $total_qty += $itmsales['wgtnqty'];
                                    $grand_total += $itmsales['tot_amnt'];
                                    }                                 
                                    } else {
                                    echo "<tr>";
                                    echo "<td>No items found to list.</td>";
                                    echo "</tr>";
                                    }
                                 

		$table .=  "<tr>";
		$table .=  "<td colspan='2'>Total</td>";
		$table .=  "<td>".number_format($grand_total, 2)."</td>";
		
		$table .=  "</tr>";
		
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
echo "<script>window.close();</script>";
?>