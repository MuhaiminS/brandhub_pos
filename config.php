<?php 
//define("DB_PRIFIX", "equipment_pos_");
define("DB_PRIFIX", "");
function connect_dre_db()
{		
	//Local
	define("DB_HOSTT", "localhost");
    define("DB_USERR", "root");
    define("DB_PASSWORDD", "");
    define("DB_DATABASEE", "brand_hub");
  
    
    $conn = mysqli_connect(DB_HOSTT, DB_USERR, DB_PASSWORDD, DB_DATABASEE);
 
    // return database handler
    $GLOBALS['conn'] = $conn;

	$query = "SELECT * FROM ".DB_PRIFIX."settings";
	$result = mysqli_query($GLOBALS['conn'], $query);
	if ($result) {
		$result_arr = array();
		while ($row = mysqli_fetch_assoc($result)) {
		   $result_arr[] = $row;			
		}
	}		
	//echo '<pre>'; print_r($result_arr);

	//Client details
	/*define("CLIENT_NAME", "CLT WEB POS");
	define("CLIENT_ADDRESS", "Dubai");
	define("CLIENT_NUMBER", "123456790");
	define("CLIENT_WEBSITE", "www.connectivelinkstechnology.com");
	//Recipt pre details
	define("RECIPT_PRE", "CLT-");
	define("CURRENCY", "AED");
	define("BILL_TAX", "no");
	define("BILL_FOOTER", "Thank you for your business!");
	
	 //SMS integration
	define("API_KEY", "2sYDrDoDx9z4");
	define("FROM_NUM", "+971551077843");
	define("OWNER_NUM", "+971551077843");*/
	foreach($result_arr as $res){
		define($res['set_name'], $res['set_value']);
	}
	define("BILL_TAX", "no");
	define("BILL_COUNTRY", "UAE");
    define("BILL_TAX_TYPE", "GST");
	define("BILL_TAX_INC", "no");
	//define("BILL_TAX_VAL", "5");
	define("FROM_NUM", "+971551177846");
	define("COUNTER_PRINTER", "counter-printer1");

	return $GLOBALS['conn'];
}
function getServerURL(){
	$dir_path = '/brand_hub/';
	//$dir_path = '/pos/avenue_ladies_salon_web';
    $url = 'http'.(isset($_SERVER['HTTPS'])?'s':'').'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	$pu = parse_url($url);
    return $pu["scheme"] . "://" . $pu["host"].$dir_path;
}
?>