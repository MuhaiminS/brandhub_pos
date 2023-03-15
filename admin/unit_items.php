<?php	
include_once("../functions.php");
include_once("../config.php");
connect_dre_db();
//$id = ;
$result = array();

function unitName($unit_id)
{
	$where = "WHERE id = '$unit_id'";
	$service = getnamewhere('item_units', 'unit_name', $where);
	return $service;
}

//$id = mysql_real_escape_string($id);
$id = (isset($_POST['item_id']) && $_POST['item_id'] !='') ? $_POST['item_id']: '';
$unit = (isset($_POST['unit']) && $_POST['unit'] !='') ? $_POST['unit']: '';
//$barcode_id = (isset($_POST['barcode_id']) && $_POST['barcode_id'] !='') ? mysql_real_escape_string($_POST['barcode_id']): '';

if($id != '') {
	$res = mysqli_query($GLOBALS['conn'], "SELECT * FROM item_price WHERE product_id = '$id'");	
	if($res) {  
		while ($row = mysqli_fetch_assoc($res)) {		
			$result[] = $row;			
		}
		$unit_list = $result;
		if(!empty($unit_list)) {			
			foreach ($unit_list as $key => $value) { $unit_name = unitName($value['unit_id']);
				$checked='' ;$class="unit_price_frm";				
				if($unit == $value['unit_id']) { $checked = "checked"; }							
				echo "<option value=\"".$value['unit_id']."\" ".$selected.">".ucfirst($unit_name)."</option>";											
			}			
		} else {
			//echo "No Units found";
		}
	} else {
		//echo "No Units found";
	}
}

?>
