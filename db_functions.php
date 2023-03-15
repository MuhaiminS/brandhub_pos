<?php
class DB_Functions {
 
    private $db;
 
      // constructor
    function __construct() {
        require_once 'db_connect.php';	
        require_once 'config.php';		
		require 'lib/PHPMailer-master/PHPMailerAutoload.php';
		require_once('config.php');
		connect_dre_db();
			//echo BILL_TAX; die;
        // connecting to database
        $this->db = new DB_Connect();
        $this->db->connect();
        //$server_url = $this->db->getServerURL();		
    }	

	/*public function getServerURL()
	{
		//$dir_path = '/projects/cakegallery_pos';
		$dir_path = '/demo/cakegallery_pos';
		//$dir_path = '';
		$url = $_SERVER['DOCUMENT_ROOT']; //'http'.(isset($_SERVER['HTTPS'])?'s':'').'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		$url =  $url.$dir_path;  //parse_url($url);
		return $url;           //$pu["scheme"] . "://" . $pu["host"].$dir_path;
	}*/	
	
	//User functions
	public function isEmailExist($email) {
		$result = mysqli_query($GLOBALS['conn'], "SELECT email from ".DB_PRIFIX."users WHERE email = '$email'");
        $no_of_rows = mysqli_num_rows($result);
        if ($no_of_rows > 0) {
            // user existed 
            return true;
        } else {
            // user not existed
            return false;
        }
	}

	public function redirect($url)
	{
		echo "<script language=\"javascript\">window.location.href=\"$url\";</script>";
	}

	public function isUserExist($inputs) {
		//$email = $inputs['email'];
		$phone = $inputs['phone'];
		$user_pass = md5($inputs['user_pass']);

		$result = mysqli_query($GLOBALS['conn'], "SELECT * FROM ".DB_PRIFIX."users WHERE phone = '$phone' AND user_pass = '$user_pass'"); 
        
        $no_of_rows = mysqli_num_rows($result);
        if ($no_of_rows > 0) {
            $result = mysqli_fetch_assoc($result);            
            return $result;         
        } else {            
            return false;
        }
	}

	public function createUser($inputs)
	{		
		$user_name = $inputs['user_name'];
		$user_pass = md5($inputs['user_pass']);
		$role_id = $inputs['role_id'];
		$manufacturing_unit_id = $_POST['manufacturing_unit_id'];
		$shop_id = $_POST['shop_id'];
		$first_name = $inputs['first_name'];
		$last_name = $inputs['last_name'];
		$email = $inputs['email'];
		$phone = $inputs['phone'];
		$is_active = $inputs['is_active'];
		$created_at = $inputs['created_at'];
		$updated_at = $inputs['updated_at'];
		
		$result = mysqli_query($GLOBALS['conn'], "INSERT INTO ".DB_PRIFIX."users(user_name, user_pass, role_id, manufacturing_unit_id, shop_id, first_name, last_name, email, phone, is_active, created_at, updated_at) VALUES('$user_name', '$user_pass', '$role_id', '$manufacturing_unit_id', '$shop_id', '$first_name', '$last_name', '$email', '$phone', '$is_active', '$created_at', '$updated_at')");
        
        if ($result) {            
            $user_id = mysqli_insert_id($GLOBALS['conn']); 
            $result = mysqli_query($GLOBALS['conn'], "SELECT * FROM ".DB_PRIFIX."users WHERE id = $user_id");           
            return mysqli_fetch_assoc($result);
        } else {
            return false;
        }
	}
	public function updateFcmId($inputs){
		$phone = $inputs['phone'];
		$fcm_id = $inputs['fcm_id'];
		$result=mysqli_query($GLOBALS['conn'], "UPDATE ".DB_PRIFIX."users SET fcm_id='$fcm_id' where phone='$phone'");
		if($result){
			$result = mysqli_query($GLOBALS['conn'], "SELECT * FROM ".DB_PRIFIX."users WHERE phone = $phone");           
			return mysqli_fetch_assoc($result);
        } else {
            return false;
        }
	}
	
	
	public function createNewOrder($inputs)
	{
		$user_id = $inputs['user_id'];		
		$status = $inputs['status'];

		$result = mysqli_query($GLOBALS['conn'], "INSERT INTO ".DB_PRIFIX."orders(user_id, status) VALUES('$user_id', '$status')");
        
        if ($result) {            
            $order_id = mysqli_insert_id($GLOBALS['conn']); 
            $result = mysqli_query($GLOBALS['conn'], "SELECT * FROM ".DB_PRIFIX."orders WHERE id = $order_id");
            return mysqli_fetch_assoc($result);
        } else {
            return false;
        }
	}

	public function createOrder($inputs)
	{
		$user_id = $inputs['user_id'];
		$manufacturing_unit_id = $inputs['manufacturing_unit_id'];
		$shop_id = $inputs['shop_id'];
		$contact_name = $inputs['contact_name'];
		$contact_number = $inputs['contact_number'];
		$address = $inputs['address'];
		$latitude = $inputs['latitude'];
		$longitude = $inputs['longitude'];
		$advance_amount = $inputs['advance_amount'];
		$delivery_time = '00-00-00';//$inputs['delivery_time'];
		$delivery_date = '0000-00-00';//date('Y-m-d', strtotime($inputs['delivery_date']));
		$payment_status = $inputs['payment_status'];
		$status = $inputs['status'];
		$created_at = date("Y-m-d H:i:s");

		$result = mysqli_query($GLOBALS['conn'], "INSERT INTO ".DB_PRIFIX."orders(user_id, 	manufacturing_unit_id, shop_id, contact_name, contact_number, address, latitude, longitude, advance_amount, delivery_date, delivery_time, payment_status, status, created_at) VALUES('$user_id', '$manufacturing_unit_id', '$shop_id', '$contact_name', '$contact_number', '$address', '$latitude', '$longitude', '$advance_amount', '$delivery_date', '$delivery_time', '$payment_status', '$status', '$created_at')");
        
        if ($result) {            
            $order_id = mysqli_insert_id($GLOBALS['conn']); 
            $result = mysqli_query($GLOBALS['conn'], "SELECT * FROM ".DB_PRIFIX."orders WHERE id = $order_id");           
            return mysqli_fetch_assoc($result);
        } else {
            return false;
        }
	}
	public function confirmOrder($inputs){
		$order_id=$inputs['order_id'];
		$result=mysqli_query($GLOBALS['conn'], "UPDATE ".DB_PRIFIX."orders SET status='pending' where id='$order_id'");
		if($result){
			$result = mysqli_query($GLOBALS['conn'], "SELECT * FROM ".DB_PRIFIX."orders WHERE id = $order_id");           
            return mysqli_fetch_assoc($result);
		}else{
			return false;
		}
	}

	public function updateOrder($inputs)
	{
		$order_id = $inputs['order_id'];
		$manufacturing_unit_id = $inputs['manufacturing_unit_id'];
		$shop_id = $inputs['shop_id'];
		$contact_name = $inputs['contact_name'];
		$contact_number = $inputs['contact_number'];
		$address = $inputs['address'];
		$latitude = $inputs['latitude'];
		$longitude = $inputs['longitude'];
		$advance_amount = $inputs['advance_amount'];
		$delivery_time = $inputs['delivery_time'];
		$delivery_date = date('Y-m-d', strtotime($inputs['delivery_date']));
		$payment_status = $inputs['payment_status'];
		$status = $inputs['status'];

		$result = mysqli_query($GLOBALS['conn'], "UPDATE ".DB_PRIFIX."orders SET manufacturing_unit_id = '$manufacturing_unit_id', shop_id = '$shop_id', contact_name = '$contact_name', contact_number = '$contact_number', address = '$address', latitude = '$latitude', longitude = '$longitude', advance_amount = '$advance_amount', delivery_date = '$delivery_date', delivery_time = '$delivery_time', payment_status = '$payment_status', status = '$status' WHERE id = $order_id");
        
        if ($result) {            
            $result = mysqli_query($GLOBALS['conn'], "SELECT * FROM ".DB_PRIFIX."orders WHERE id = $order_id");           
            return mysqli_fetch_assoc($result);
        } else {
            return false;
        }
	}
	public function updateOrderPayStatus($inputs)
	{
		$order_id = $inputs['id'];
		$manufacturing_unit_id = $inputs['manufacturing_unit_id'];
		$shop_id = $inputs['shop_id'];
		$contact_name = $inputs['contact_name'];
		$contact_number = $inputs['contact_number'];
		$address = $inputs['address'];
		$latitude = $inputs['latitude'];
		$longitude = $inputs['longitude'];
		$advance_amount = $inputs['advance_amount'];
		$delivery_time = $inputs['delivery_time'];
		$delivery_date = date('Y-m-d', strtotime($inputs['delivery_date']));
		$payment_status = $inputs['payment_status'];
		$status = $inputs['status'];
		$paid_date=$inputs['paid_date'];

		$result = mysqli_query($GLOBALS['conn'], "UPDATE ".DB_PRIFIX."orders SET manufacturing_unit_id = '$manufacturing_unit_id', shop_id = '$shop_id', contact_name = '$contact_name', 
		contact_number = '$contact_number', address = '$address', latitude = '$latitude', longitude = '$longitude', advance_amount = '$advance_amount', 
		delivery_date = '$delivery_date', delivery_time = '$delivery_time', payment_status = '$payment_status', status = '$status' , paid_date='$paid_date' WHERE id = $order_id");
        
        if ($result) {            
            $result = mysqli_query($GLOBALS['conn'], "SELECT * FROM ".DB_PRIFIX."orders WHERE id = $order_id");           
            return mysqli_fetch_assoc($result);
        } else {
            return false;
        }
	}

	public function updateOrderStatus($inputs)
	{
		$order_id = $inputs['order_id'];		
		$status = $inputs['status'];
		

		$result = mysqli_query($GLOBALS['conn'], "UPDATE ".DB_PRIFIX."orders SET status = '$status' WHERE id = $order_id");
        
        if ($result) {            
            $result = mysqli_query($GLOBALS['conn'], "SELECT * FROM ".DB_PRIFIX."orders WHERE id = $order_id");           
            return mysqli_fetch_assoc($result);
        } else {
            return false;
        }
	}
	
	public function addOrderItems($inputs) {
		$order_id = $inputs['order_id'];
		$category_id = $inputs['category_id'];
		$flavour_id = $inputs['flavour_id'];
		$filling_id = $inputs['filling_id'];		
		$product_name = $inputs['product_name'];
		$product_code = $inputs['product_code'];
		$amount = $inputs['amount'];
		$weight = $inputs['weight'];
		$status = $inputs['status'];
		$msg_on_cake=$inputs['message_on_cake'];

		$result = mysqli_query($GLOBALS['conn'], "INSERT INTO ".DB_PRIFIX."order_items(order_id, category_id, flavour_id, filling_id, product_name, product_code, weight, amount, status,message_on_cake) VALUES('$order_id', '$category_id', '$flavour_id', '$filling_id', '$product_name', '$product_code', '$weight', '$amount', '$status','$msg_on_cake')");

		 if ($result) {            
            $order_item_id = mysqli_insert_id($GLOBALS['conn']); 
            $result = mysqli_query($GLOBALS['conn'], "SELECT * FROM ".DB_PRIFIX."order_items WHERE id = $order_item_id");           
            return mysqli_fetch_assoc($result);
        } else {
            return false;
        }
	}
	public function deleteImages($inputs){
		$order_item_id = $inputs['order_item_id'];
		$image_path=$inputs['image_path'];
		$image_name=$inputs['img_name'];
		$result=mysqli_query($GLOBALS['conn'], "Delete from ".DB_PRIFIX."order_item_images where order_item_id='$order_item_id' AND img_name='$image_name'");
	
		if($result){
		   $result = mysqli_query($GLOBALS['conn'], "SELECT * FROM ".DB_PRIFIX."order_items WHERE id = $order_item_id");           
            return mysqli_fetch_assoc($result);
        } else {
            return false;
        }

	}
		public function deleteAudio($inputs){
		$order_item_id = $inputs['order_item_id'];
		$audio_path=$inputs['audio_path'];
		$audio_name=$inputs['audio_name'];
		$result=mysqli_query($GLOBALS['conn'], "Delete from ".DB_PRIFIX."order_item_audios where order_item_id='$order_item_id' AND audio_name='$audio_name'");
	
		if($result){
		   $result = mysqli_query($GLOBALS['conn'], "SELECT * FROM ".DB_PRIFIX."order_items WHERE id = $order_item_id");           
            return mysqli_fetch_assoc($result);
        } else {
            return false;
        }

	}
	public function updateOrderItems($inputs) {
		$order_item_id = $inputs['order_item_id'];
		$category_id = $inputs['category_id'];
		$flavour_id = $inputs['flavour_id'];
		$filling_id = $inputs['filling_id'];		
		$product_name = $inputs['product_name'];
		$product_code = $inputs['product_code'];
		$amount = $inputs['amount'];
		$weight = $inputs['weight'];
		$status = $inputs['status'];
		$msg_on_cake=$inputs['message_on_cake'];

		$result = mysqli_query($GLOBALS['conn'], "UPDATE order_items ".DB_PRIFIX."SET category_id = '$category_id', flavour_id = '$flavour_id', filling_id = '$filling_id', product_name = '$product_name', product_code = '$product_code', weight = '$weight', amount = '$amount', status = '$status',message_on_cake='$msg_on_cake' WHERE id = $order_item_id");

		 if ($result) {
            $result = mysqli_query($GLOBALS['conn'], "SELECT * FROM ".DB_PRIFIX."order_items WHERE id = $order_item_id");           
            return mysqli_fetch_assoc($result);
        } else {
            return false;
        }
	}

	public function updateOrderItemsStatus($inputs) {
		$order_item_id = $inputs['order_item_id'];
		$status = $inputs['status'];
		$isCompleted=true;
		
		$result_ = mysqli_query($GLOBALS['conn'], "UPDATE ".DB_PRIFIX."order_items SET status = '$status' WHERE id = $order_item_id");
	

		 if ($result_) {
            $result = mysqli_query($GLOBALS['conn'], "SELECT * FROM ".DB_PRIFIX."order_items WHERE id = $order_item_id");   
			while($row = mysqli_fetch_assoc($result)){
					$order_id=$row['order_id'];
					$result1=mysqli_query($GLOBALS['conn'], "SELECT * FROM ".DB_PRIFIX."order_items WHERE order_id=$order_id");
					while($row1=mysqli_fetch_assoc($result1)){
						if($isCompleted){
							if($row1['status']=='completed'){
								$isCompleted=true;
							}else{
								$isCompleted=false;
							}
						}
					}
				}
		
			if($isCompleted){
				mysqli_query($GLOBALS['conn'], "UPDATE ".DB_PRIFIX."orders SET status='$status' WHERE id = $order_id");
			}
			            $result3 = mysqli_query($GLOBALS['conn'], "SELECT * FROM ".DB_PRIFIX."order_items WHERE id = $order_item_id");   

            return mysqli_fetch_assoc($result3);
        } else {
            return false;
        }
	}

	public function addOrderItemImages($inputs) {		
		$img_name = $inputs['img_name'];
		$order_item_id = $inputs['order_item_id'];		

		$result = mysqli_query($GLOBALS['conn'], "INSERT INTO ".DB_PRIFIX."order_item_images(order_item_id, img_name) VALUES('$order_item_id', '$img_name')");

		if ($result) {            
            $order_image_id = mysqli_insert_id($GLOBALS['conn']); 
            $result = mysqli_query($GLOBALS['conn'], "SELECT * FROM ".DB_PRIFIX."order_item_images WHERE id = $order_image_id");
            return mysqli_fetch_assoc($result);
        } else {
            return false;
        }
	}

	public function addOrderItemAudios($inputs) {
		
		$audio_name = $inputs['audio_name'];
		$order_item_id = $inputs['order_item_id'];		

		$result = mysqli_query($GLOBALS['conn'], "INSERT INTO ".DB_PRIFIX."order_item_audios(order_item_id, audio_name) VALUES('$order_item_id', '$audio_name')");

		 if ($result) {            
            $order_audio_id = mysqli_insert_id($GLOBALS['conn']); 
            $result = mysqli_query($GLOBALS['conn'], "SELECT * FROM ".DB_PRIFIX."order_item_audios WHERE id = $order_audio_id");           
            return mysqli_fetch_assoc($result);
        } else {
            return false;
        }
	}
	
	//Get orders
	public function getOrders($inputs)
	{
		$query = "SELECT * FROM ".DB_PRIFIX."orders WHERE 1";		
		if(isset($inputs['user_id']) && $inputs['user_id'] != '') {
			$user_id = $inputs['user_id'];			
			$query .= " AND user_id = \"".$user_id."\"";
		}
		if(isset($inputs['manufacturing_unit_id']) && $inputs['manufacturing_unit_id'] != '') {
			$manufacturing_unit_id = $inputs['manufacturing_unit_id'];		
			$query .= " AND manufacturing_unit_id = \"".$manufacturing_unit_id."\"";
		}
		if(isset($inputs['shop_id']) && $inputs['shop_id'] != '') {
			$shop_id = $inputs['shop_id'];		
			$query .= " AND shop_id = \"".$shop_id."\"";
		}
		if(isset($inputs['status']) && $inputs['status'] != '') {
			$status = $inputs['status'];
			$query .= " AND status = \"".$status."\"";
		}
		if(isset($inputs['delivery_date']) && $inputs['delivery_date'] != '') {
			$delivery_date = $inputs['delivery_date'];
			$query .= " AND delivery_date = \"".date('Y-m-d', strtotime($delivery_date))."\"";
		} else {
			$query .= " AND delivery_date >= CURRENT_DATE";
		}
		$query .= " ORDER BY delivery_time ASC,delivery_date ASC";
//echo $query;
		$result = mysqli_query($GLOBALS['conn'], $query);
		if ($result) {
		
			$result_arr = array();
			while ($row = mysqli_fetch_assoc($result)) {
			  		
				$extra['shop_name'] = '';
				$extra['user_name'] = '';
				if($row['user_id']>0){
					$user_details = $this->getUserDetails($row['user_id']);
					if($user_details) {
						$extra['user_name'] = $user_details['user_name'];
					}
				}
				if($row['shop_id']>0){
						$shop_details = $this->getShopDetails($row['shop_id']);
					if($shop_details) {
						$extra['shop_name'] = $shop_details['shop_name'];
					}
				}
			$result_arr[] = array_merge($row, $extra);

			}
			return $result_arr;
		}		
        else {
			return false;
		}		
	}	

	public function getOrdersByDate($inputs)
	{
	
		$query = "SELECT * FROM ".DB_PRIFIX."orders WHERE 1";		
		if(isset($inputs['user_id']) && $inputs['user_id'] != '') {
			$user_id = $inputs['user_id'];			
			$query .= " AND user_id = \"".$user_id."\"";
		}
	
		if(isset($inputs['shop_id']) && $inputs['shop_id'] != '') {
			$shop_id = $inputs['shop_id'];		
			$query .= " AND shop_id = \"".$shop_id."\"";
		}
		if(isset($inputs['status']) && $inputs['status'] != '') {
			$status = $inputs['status'];
			$query .= " AND status = \"".$status."\"";
		}
			if(isset($inputs['created_at']) && $inputs['created_at'] != '') {
			$created_at = date('Y-m-d', strtotime($inputs['created_at']));
			//$query .= " AND created_at = \"".date('Y-m-d', strtotime($created_at))."\"";
			$query .= " AND created_at BETWEEN '$created_at 00:00:00' AND '$created_at 23:59:59'";
		}
		$query .= " ORDER BY created_at ASC";
//echo $query;
		$result = mysqli_query($GLOBALS['conn'], $query);
		if ($result) {
		
			$result_arr = array();
			while ($row = mysqli_fetch_assoc($result)) {
			  		
				$extra['shop_name'] = '';
				$extra['user_name'] = '';
				if($row['user_id']>0){
					$user_details = $this->getUserDetails($row['user_id']);
					if($user_details) {
						$extra['user_name'] = $user_details['user_name'];
					}
				}
				if($row['shop_id']>0){
						$shop_details = $this->getShopDetails($row['shop_id']);
					if($shop_details) {
						$extra['shop_name'] = $shop_details['shop_name'];
					}
				}
			$result_arr[] = array_merge($row, $extra);

			}
			return $result_arr;
		}		
        else {
			return false;
		}		
	}	
	public function getOrdersByDateForSettleSale($inputs){
		$shop_id = $inputs['shop_id'];
		$from_date=$inputs['from_date'];
		$user_id=$inputs['user_id'];
		$to_date=$inputs['to_date'];
		$query = "SELECT * FROM ".DB_PRIFIX."orders ";
	
		if(isset($shop_id) && $shop_id != '') {
			$query .= " where shop_id='$shop_id' AND user_id='$user_id'";
		} 
	
		if((isset($from_date) && $from_date != '') && (isset($to_date) && $to_date != '' )) {
			
			if(isset($shop_id) && $shop_id != ''){
				$query .= " AND created_at BETWEEN  '$from_date' AND  '$to_date' ";
			}else{
				$query .= " where created_at BETWEEN  '$from_date' AND  '$to_date' ";
			}
			
		} 
			$query .= " ORDER BY id DESC";
//echo $query;
		$result = mysqli_query($GLOBALS['conn'], $query);
		if ($result) {
		
			$result_arr = array();
			while ($row = mysqli_fetch_assoc($result)) {
			  		
				$extra['shop_name'] = '';
				$extra['user_name'] = '';
				if($row['user_id']>0){
					$user_details = $this->getUserDetails($row['user_id']);
					if($user_details) {
						$extra['user_name'] = $user_details['user_name'];
					}
				}
				if($row['shop_id']>0){
						$shop_details = $this->getShopDetails($row['shop_id']);
					if($shop_details) {
						$extra['shop_name'] = $shop_details['shop_name'];
					}
				}
			$result_arr[] = array_merge($row, $extra);

			}
			return $result_arr;
		}		
        else {
			return false;
		}
	}
	
	public function getOrdersByDateForSettleSalePaid($inputs){
			$shop_id = $inputs['shop_id'];
		$from_date=$inputs['from_date'];
		$to_date=$inputs['to_date'];
		$user_id=$inputs['user_id'];
		$query = "SELECT * FROM ".DB_PRIFIX."orders ";
	
		if(isset($shop_id) && $shop_id != '') {
			$query .= " where shop_id='$shop_id' AND user_id='$user_id'";
		} 
	
		if((isset($from_date) && $from_date != '') && (isset($to_date) && $to_date != '' )) {
			
			if(isset($shop_id) && $shop_id != ''){
				$query .= " AND paid_date BETWEEN  '$from_date' AND  '$to_date' ";
			}else{
				$query .= " where paid_date BETWEEN  '$from_date' AND  '$to_date' ";
			}
			
		} 
			$query .= " ORDER BY id DESC";
//echo $query;
		$result = mysqli_query($GLOBALS['conn'], $query);
		if ($result) {
		
			$result_arr = array();
			while ($row = mysqli_fetch_assoc($result)) {
			  		
				$extra['shop_name'] = '';
				$extra['user_name'] = '';
				if($row['user_id']>0){
					$user_details = $this->getUserDetails($row['user_id']);
					if($user_details) {
						$extra['user_name'] = $user_details['user_name'];
					}
				}
				if($row['shop_id']>0){
						$shop_details = $this->getShopDetails($row['shop_id']);
					if($shop_details) {
						$extra['shop_name'] = $shop_details['shop_name'];
					}
				}
			$result_arr[] = array_merge($row, $extra);

			}
			return $result_arr;
		}		
        else {
			return false;
		}
	}

	public function getOrderItems($inputs)
	{
		if(!isset($inputs['order_id']) || $inputs['order_id'] == 0) 
			return false;		
		$order_id = $inputs['order_id'];
		$query = "SELECT * FROM ".DB_PRIFIX."order_items WHERE order_id = $order_id";                   
		$result = mysqli_query($GLOBALS['conn'], $query);
		if ($result) {
			$result_arr = array();
			while ($row = mysqli_fetch_assoc($result)) {
				$extra['category_title'] = '';
				$extra['flavour_name'] = '';
				$extra['filling_name'] = '';

				if($row['category_id'] > 0) {
					$cat_details = $this->getCategoryDetails($row['category_id']);
					if($cat_details) {
						$extra['category_title'] = $cat_details['category_title'];
					}
				}
				if($row['flavour_id'] > 0) {
					$flavour_details = $this->getCakeFlavourDetails($row['flavour_id']);
					if($flavour_details) {
						$extra['flavour_name'] = $flavour_details['flavour_name'];
					}
				}
				if($row['filling_id'] > 0) {
					$filling_details = $this->getCakeFillingDetails($row['filling_id']);
					if($filling_details) {
						$extra['filling_name'] = $filling_details['filling_name'];
					}
				}
				$result_arr[] = array_merge($row, $extra);
			}
			return $result_arr;
		}		
        else {
			return false;
		}		
	}	

	public function getOrderItemImages($inputs)
	{
		$query = "SELECT * FROM ".DB_PRIFIX."order_item_images WHERE 1";		
		if(isset($inputs['order_item_id']) && $inputs['order_item_id'] > 0) {
			$order_item_id = $inputs['order_item_id'];			
			$query .= " AND order_item_id = $order_item_id";
		}	
                   
		$result = mysqli_query($GLOBALS['conn'], $query);
		if ($result) {
			$result_arr = array();
			while ($row = mysqli_fetch_assoc($result)) {
			   $row['image_path'] = $row['img_name'];
			   $result_arr[] = $row;			
			}
			return $result_arr;
		}		
        else {
			return false;
		}		
	}

	public function getOrderItemAudios($inputs)
	{
		$query = "SELECT * FROM ".DB_PRIFIX."order_item_audios WHERE 1";		
		if(isset($inputs['order_item_id']) && $inputs['order_item_id'] > 0) {
			$order_item_id = $inputs['order_item_id'];			
			$query .= " AND order_item_id = $order_item_id";
		}	
                   
		$result = mysqli_query($GLOBALS['conn'], $query);
		if ($result) {
			$result_arr = array();
			while ($row = mysqli_fetch_assoc($result)) {
			   $row['audio_path'] = $row['audio_name'];
			   $result_arr[] = $row;			
			}
			return $result_arr;
		}		
        else {
			return false;
		}		
	}

	public function getFillings($inputs)
	{
		$query = "SELECT * FROM ".DB_PRIFIX."cake_filling WHERE 1";
                    
		$result = mysqli_query($GLOBALS['conn'], $query);
		if ($result) {
			$result_arr = array();
			while ($row = mysqli_fetch_assoc($result)) {
			   $result_arr[] = $row;			
			}
			return $result_arr;
		}		
        else {
			return false;
		}
	}	

	public function getFlavours($inputs)
	{
		$query = "SELECT * FROM ".DB_PRIFIX."cake_flavours WHERE 1";                    
		$result = mysqli_query($GLOBALS['conn'], $query);
		if ($result) {
			$result_arr = array();
			while ($row = mysqli_fetch_assoc($result)) {
			   $result_arr[] = $row;			
			}
			return $result_arr;
		}		
        else {
			return false;
		}
	}
	
	public function getCategory($inputs)
	{
		$query = "SELECT * FROM ".DB_PRIFIX."category WHERE 1";                    
		$result = mysqli_query($GLOBALS['conn'], $query);
		if ($result) {
			$result_arr = array();
			while ($row = mysqli_fetch_assoc($result)) {
			   $result_arr[] = $row;			
			}
			return $result_arr;
		}		
        else {
			return false;
		}
	}
	public function getItemCategory($inputs){
				$query = "SELECT * FROM ".DB_PRIFIX."item_category WHERE 1";                    
		$result = mysqli_query($GLOBALS['conn'], $query);
		if ($result) {
			$result_arr = array();
			while ($row = mysqli_fetch_assoc($result)) {
			   $result_arr[] = $row;			
			}
			return $result_arr;
		}		
        else {
			return false;
		}
	}
	public function getDrivers($inputs){
						$query = "SELECT * FROM ".DB_PRIFIX."drivers WHERE 1";                    
		$result = mysqli_query($GLOBALS['conn'], $query);
		if ($result) {
			$result_arr = array();
			while ($row = mysqli_fetch_assoc($result)) {
			   $result_arr[] = $row;			
			}
			return $result_arr;
		}		
        else {
			return false;
		}
	}
	public function getUser($inputs){
						$query = "SELECT * FROM ".DB_PRIFIX."users WHERE 1";                    
		$result = mysqli_query($GLOBALS['conn'], $query);
		if ($result) {
			$result_arr = array();
			while ($row = mysqli_fetch_assoc($result)) {
			   $result_arr[] = $row;			
			}
			return $result_arr;
		}		
        else {
			return false;
		}
	}
	public function getAllCustomerDetails(){
	    $query="SELECT * FROM ( SELECT * FROM ".DB_PRIFIX."sale_orders ORDER BY id DESC ) AS sale_orders GROUP BY contact_number";
	    $result=mysqli_query($GLOBALS['conn'], $query);
	    if($result){
	        $result_arr=array();
	        while($row=mysqli_fetch_assoc($result)){
	            $result_arr[]=$row;
	        }
	        return $result_arr;
	    }else{
	        return false;
	    }
	}
	public function getItemsByCategory($inputs){
		$cat_id=$inputs['cat_id'];
		$query = "SELECT * FROM ".DB_PRIFIX."items WHERE cat_id=$cat_id";                    
		$result = mysqli_query($GLOBALS['conn'], $query);
		if ($result) {
			$result_arr = array();
			while ($row = mysqli_fetch_assoc($result)) {
			   $result_arr[] = $row;			
			}
			return $result_arr;
		}		
        else {
			return false;
		}
	}
	public function getAllItemsByCategory(){
		//$cat_id=$inputs['cat_id'];
		$query = "SELECT * FROM ".DB_PRIFIX."items";                    
		$result = mysqli_query($GLOBALS['conn'], $query);
		if ($result) {
			$result_arr = array();
			while ($row = mysqli_fetch_assoc($result)) {
			   $result_arr[] = $row;			
			}
			return $result_arr;
		}		
        else {
			return false;
		}
	}
	public function getItemsFull($inputs){
		//$cat_id=$inputs['cat_id'];
		$query = "SELECT * FROM ".DB_PRIFIX."items WHERE 1";
		$result = mysqli_query($GLOBALS['conn'], $query);
		if ($result) {
			$result_arr = array();
			while ($row = mysqli_fetch_assoc($result)) {
			   $result_arr[] = $row;			
			}
			return $result_arr;
		}		
        else {
			return false;
		}
	}
	public function getItemAddtionalPrice($inputs){
		$query="SELECT * FROM ".DB_PRIFIX."item_additional_price" ;
		$result=mysqli_query($GLOBALS['conn'], $query);
		if($result){
			$result_arr=array();
			while($row=mysqli_fetch_assoc($result)){
				$result_arr[]=$row;
			}
			return $result_arr;
		}else{
			return false;
		}
	}

	public function getLocationsManufacturingUnits($inputs)
	{
		$query = "SELECT * FROM ".DB_PRIFIX."locations_manufacturing_units WHERE 1";                    
		$result = mysqli_query($GLOBALS['conn'], $query);
		if ($result) {
			$result_arr = array();
			while ($row = mysqli_fetch_assoc($result)) {
			   $result_arr[] = $row;			
			}
			return $result_arr;
		}		
        else {
			return false;
		}
	}

	public function getLocationsShops($inputs)
	{
		$query = "SELECT * FROM ".DB_PRIFIX."locations_shops WHERE 1";                    
		$result = mysqli_query($GLOBALS['conn'], $query);
		if ($result) {
			$result_arr = array();
			while ($row = mysqli_fetch_assoc($result)) {
			   $result_arr[] = $row;			
			}
			return $result_arr;
		}		
        else {
			return false;
		}
	}	

	public function reArrayFiles(&$file_post) {

		$file_ary = array();
		$file_count = count($file_post['name']);
		$file_keys = array_keys($file_post);

		for ($i=0; $i<$file_count; $i++) {
			foreach ($file_keys as $key) {
				$file_ary[$i][$key] = $file_post[$key][$i];
			}
		}

		return $file_ary;
	}

	public function getOrderDetails($inputs) {

		$query = "SELECT * FROM ".DB_PRIFIX."orders WHERE 1";		
		if(isset($inputs['order_id']) && $inputs['order_id'] > 0) {
			$order_id = $inputs['order_id'];			
			$query .= " AND id = $order_id";
		}	
                   
		$result = mysqli_query($GLOBALS['conn'], $query);
		if ($result) {
			$result_arr = array();
			while ($row = mysqli_fetch_assoc($result)) {
			   $result_arr[] = $row;			
			}
			return $result_arr;
		}		
        else {
			return false;
		}		
	}
	
	public function getOrderItemDetails($inputs) {

		$query = "SELECT * FROM ".DB_PRIFIX."order_items WHERE 1";		
		if(isset($inputs['order_item_id']) && $inputs['order_item_id'] > 0) {
			$order_item_id = $inputs['order_item_id'];			
			$query .= " AND id = $order_item_id";
		}	
                   
		$result = mysqli_query($GLOBALS['conn'], $query);
		if ($result) {
			$result_arr = array();
			while ($row = mysqli_fetch_assoc($result)) {
				//echo '<pre>';print_r($row);echo '</pre>';
				$result_arr = $row;
				
				$result_arr['category_title'] = '';
				$result_arr['flavour_name'] = '';
				$result_arr['filling_name'] = '';

				if($row['category_id'] > 0) {
					$cat_details = $this->getCategoryDetails($row['category_id']);
					if($cat_details) {
						$result_arr['category_title'] = $cat_details['category_title'];
					}
				}
				if($row['flavour_id'] > 0) {
					$flavour_details = $this->getCakeFlavourDetails($row['flavour_id']);
					if($flavour_details) {
						$result_arr['flavour_name'] = $flavour_details['flavour_name'];
					}
				}
				if($row['filling_id'] > 0) {
					$filling_details = $this->getCakeFillingDetails($row['filling_id']);
					if($filling_details) {
						$result_arr['filling_name'] = $filling_details['filling_name'];
					}
				}
			}
			return $result_arr;
		}		
        else {
			return false;
		}		
	}

	public function getOrderItemImageDetails($inputs) {

		$query = "SELECT * FROM ".DB_PRIFIX."order_item_images WHERE 1";		
		if(isset($inputs['order_image_id']) && $inputs['order_image_id'] > 0) {
			$order_image_id = $inputs['order_image_id'];			
			$query .= " AND id = $order_image_id";
		}	
                   
		$result = mysqli_query($GLOBALS['conn'], $query);
		if ($result) {
			$results = mysqli_fetch_assoc($result);            
			return $results;
		}		
        else {
			return false;
		}		
	}

	public function getOrderItemAudioDetails($inputs) {

		$query = "SELECT * FROM ".DB_PRIFIX."order_item_audios WHERE 1";		
		if(isset($inputs['order_audio_id']) && $inputs['order_audio_id'] > 0) {
			$order_audio_id = $inputs['order_audio_id'];			
			$query .= " AND id = $order_audio_id";
		}	
                   
		$result = mysqli_query($GLOBALS['conn'], $query);
		if ($result) {
			$results = mysqli_fetch_assoc($result);            
			return $results;
		}		
        else {
			return false;
		}		
	}

	public function getCakeFillingDetails($filling_id = 0) {
		if($filling_id == 0)
			return false;

		$query = "SELECT * FROM ".DB_PRIFIX."cake_filling WHERE id = $filling_id";                   
		$result = mysqli_query($GLOBALS['conn'], $query);
		if ($result) {
			$results = mysqli_fetch_assoc($result);            
			return $results;
		}		
        else {
			return false;
		}		
	}

	public function getCakeFlavourDetails($flavour_id = 0) {
		if($flavour_id == 0)
			return false;

		$query = "SELECT * FROM ".DB_PRIFIX."cake_flavours WHERE id = $flavour_id";
		$result = mysqli_query($GLOBALS['conn'], $query);
		if ($result) {
			$results = mysqli_fetch_assoc($result);            
			return $results;
		}		
        else {
			return false;
		}		
	}

	public function getCategoryDetails($category_id = 0) {
		if($category_id == 0)
			return false;

		$query = "SELECT * FROM ".DB_PRIFIX."category WHERE id = $category_id";                   
		$result = mysqli_query($GLOBALS['conn'], $query);
		if ($result) {
			$results = mysqli_fetch_assoc($result);            
			return $results;
		}		
        else {
			return false;
		}		
	}

	public function getManufacturingUnitDetails($manufacturing_unit_id = 0) {
		if($manufacturing_unit_id == 0)
			return false;

		$query = "SELECT * FROM ".DB_PRIFIX."locations_manufacturing_units WHERE id = $manufacturing_unit_id";                   
		$result = mysqli_query($GLOBALS['conn'], $query);
		if ($result) {
			$results = mysqli_fetch_assoc($result);            
			return $results;
		}		
        else {
			return false;
		}		
	}

	public function getShopDetails($shop_id = 0) {
		if($shop_id == 0)
			return false;

		$query = "SELECT * FROM ".DB_PRIFIX."locations_shops WHERE id = $shop_id";                   
		$result = mysqli_query($GLOBALS['conn'], $query);
		if ($result) {
			$results = mysqli_fetch_assoc($result);            
			return $results;
		}		
        else {
			return false;
		}		
	}

	public function getUserDetails($inputs) {

		$query = "SELECT * FROM ".DB_PRIFIX."users WHERE 1";		
		if(isset($inputs['user_id']) && $inputs['user_id'] > 0) {
			$user_id = $inputs['user_id'];			
			$query .= " AND id = $user_id";
		}	
                   
		$result = mysqli_query($GLOBALS['conn'], $query);
		if ($result) {			
			$results = mysqli_fetch_assoc($result);            
			return $results;
		}		
        else {
			return false;
		}		
	}

	public function getUserRole($inputs)
	{
		$query = "SELECT * FROM ".DB_PRIFIX."users_role ORDER BY id ASC";                   
		$result = mysqli_query($GLOBALS['conn'], $query);
		if ($result) {
			//return mysqli_fetch_assoc($result);
			$result_arr = array();
			while ($row = mysqli_fetch_assoc($result)) {
			   $result_arr[] = $row;			
			}
			return $result_arr;
		}		
        else {
			return false;
		}
	}

	public function getUserRoleDetails($inputs) {

		$query = "SELECT * FROM ".DB_PRIFIX."users_role WHERE 1";		
		if(isset($inputs['role_id']) && $inputs['role_id'] > 0) {
			$role_id = $inputs['role_id'];			
			$query .= " AND id = $role_id";
		}
		$result = mysqli_query($GLOBALS['conn'], $query);
		if ($result) {			
			$results = mysqli_fetch_assoc($result);            
			return $results;			
		}		
        else {
			return false;
		}		
	}

	
	public function getOrderStatusOptions($inputs) {
		$status = array('pending' => 'Pending',
						'progressing' => 'Progressing', 
						'ready_for_delivery' => 'Ready for delivery',
						'completed' => 'Completed',
						'delivered' => 'Delivered');
		return $status;
	}

	public function getnamewhere($tabname,$name,$where)     // pass the table name , name of field to return all the values
	{

					$qry="SELECT $name FROM ".DB_PRIFIX."$tabname $where";
					//echo $qry;
					$result=mysqli_query($GLOBALS['conn'], $qry);
					$num=mysqli_num_rows($result);
					$i=0;
					$varname = '';
					if($num>0)
					{
						while($row = mysqli_fetch_assoc($result)) {					   
						   $varname = $row[$name]; 
						}
						//$varname=mysqli_result($result,$i,$name);
						
					}
					return $varname;

	}

	public function getItemName($item_id)
   {
   	$where = "WHERE id = '$item_id'";
   	$service = $this->getnamewhere('items', 'name', $where);
   	return $service;
   }

   public function getDriverName($driver_id)
   {
   	$where = "WHERE id = '$driver_id'";
   	$driver = $this->getnamewhere('drivers', 'name', $where);
   	return $driver;
   }

	public function getuserName($user_id)
	{
		$where = "WHERE id = '$user_id'";
		$user_id = $this->getnamewhere('users', 'user_name', $where);
		return $user_id;
	}

	public function getShopName($shop_id)
	{
		$where = "WHERE id = '$shop_id'";
		$shop_id = $this->getnamewhere('locations_shops', 'shop_name', $where);
		return $shop_id;
	}

	public function setSettleSale($inputs){//echo '000'; die;
		 // echo '<pre>'; print_r($inputs); die;
		$shop_id = $inputs['shop_id'];
		$inputs['shop_name'] = $this->getShopName($inputs['shop_id']);
		$user_id = $inputs['user_id'];
		$user_name = $this->getuserName($inputs['user_id']);
		$cash_at_starting=(isset($inputs['cash_at_starting']) && $inputs['cash_at_starting'] !='') ? $inputs['cash_at_starting'] : '';
		$cash_sale=$inputs['cash_sale'];
		$card_sale=$inputs['card_sale'];
		$credit_sale=$inputs['credit_sale'];
		$delivery_sale=$inputs['delivery_sale'];
		$online_order_recovery=$inputs['online_order_recovery'];
		$credit_recover=$inputs['credit_recover'];
		$cg_advance=(isset($inputs['cg_advance']) && $inputs['cg_advance'] !='') ? $inputs['cg_advance'] : '';
		$cg_recover=(isset($inputs['cg_recover']) && $inputs['cg_recover'] !='') ? $inputs['cg_recover'] : '';
		$gross_total=$inputs['gross_total'];
		$discount=$inputs['discount'];
		$net_total=$inputs['net_total'];
		$total_cgst=$inputs['total_cgst'];
		$total_sgst=$inputs['total_sgst'];
		$total_gst =$total_sgst+$total_cgst;
		$cash_drawer=$inputs['cash_drawer'];
		$settle_date=date("Y-m-d H:i:s", strtotime($inputs['settle_date']));
		$settle_date1=date("Y-m-d", strtotime($inputs['settle_date']));
		$settle_date2=$settle_date1. ' 23:59:59';
		$last_settle_sale=$this->getSettleSale($inputs);
		$inputs['from_date']=$last_settle_sale[0]['settle_date'];
		$inputs['to_date']=$inputs['settle_date'];

		$total_vat = $inputs['cash_sale_vat']+$inputs['card_sale_vat'];
		//die;
		// echo '<pre>'; print_r($inputs); die;
		//$qry = "SELECT * FROM settle_sale WHERE shop_id='$shop_id' && user_id='$user_id' && settle_date between '$settle_date1' and '$settle_date2'";
		
		//$result=mysqli_query($GLOBALS['conn'], $qry);
		//$total_records=mysqli_num_rows($result);
		//if($total_records == 0)
		//{
			$sql = "INSERT INTO ".DB_PRIFIX."settle_sale(shop_id, user_id, cash_at_starting, cash_sale, card_sale , credit_sale, delivery_sale, online_order_recovery, credit_recover,cg_advance,
		cg_recover,gross_total,discount,net_total,cash_drawer,settle_date,total_vat,total_cgst,total_sgst,total_gst) 
		VALUES('$shop_id', '$user_id', '$cash_at_starting', '$cash_sale', '$card_sale' , '$credit_sale', '$delivery_sale', '$online_order_recovery', '$credit_recover','$cg_advance',
		'$cg_recover','$gross_total','$discount','$net_total','$cash_drawer','$settle_date','$total_vat','$total_cgst','$total_sgst','$total_gst')";
		//echo $sql;
			$result = mysqli_query($GLOBALS['conn'], $sql);
		//echo $sql; die;
			if($result){
				$settle_sale_id = mysqli_insert_id($GLOBALS['conn']);
				$result1 = mysqli_query($GLOBALS['conn'], "SELECT * FROM ".DB_PRIFIX."settle_sale WHERE id = $settle_sale_id");
				while ($row = mysqli_fetch_assoc($result1)) {
					   $result_arr[] = $row;			
					}

				$template = file_get_contents("mail.html");
    			foreach($inputs as $key => $value)
    			{
    				$template = str_replace('{{ '.$key.' }}', $value, $template);
    			}

				try {
					$mail = new PHPMailer;

					$mail->isSMTP();                            // Set mailer to use SMTP
					$mail->Host = 'smtp.gmail.com';             // Specify main and backup SMTP servers
					$mail->SMTPAuth = true;                     // Enable SMTP authentication
					$mail->Username = 'cakegallerymail@gmail.com';          // SMTP username
					$mail->Password = '1234567890clt'; // SMTP password
					$mail->SMTPSecure = 'tls';                  // Enable TLS encryption, `ssl` also accepted
					$mail->Port = 587;                          // TCP port to connect to

					$mail->setFrom('cakegallerymail@gmail.com', $inputs['shop_name'].' Top Art');
					//$mail->addAddress('rajmanibtech@gmail.com', 'POS WEB');
					$mail->addAddress('anandbe13@gmail.com', $inputs['shop_name'].' Top Art');
					//$mail->addAddress('raf.rafees@gmail.com', 'POS WEB');
					$mail->AddReplyTo('cakegallerymail@gmail.com', $inputs['shop_name'].' Top Art');
					$mail->Subject = $user_name.' Settle the sale on '.$settle_date;
					$mail->msgHTML($template);
				}
				catch (phpmailerException $mail) {
					echo $mail->errorMessage();
				}
                
    			/*$mail = new PHPMailer;
    			$mail->setFrom('anandbe13@gmail.com', 'Fish Market');
    			$mail->addAddress('anandbe13@gmail.com', 'Fish Market');
    			$mail->AddReplyTo('anandbe13@gmail.com', 'Fish Market');
    			$mail->Subject = $user_name.' Settle the sale on '.$to_date;
    			$mail->msgHTML($template);*/
    
    			if (!$mail->send()) {
    				echo "Mailer Error: " . $mail->ErrorInfo;
    			}
					return $result_arr;	
			}else{
				return false;
			}
		//}else {
			//return false;
		//}
		
	}
	public function getSettleSale($inputs){
		$shop_id = $inputs['shop_id'];
		$user_id = $inputs['user_id'];
		$result_arr = array();
		$result=mysqli_query($GLOBALS['conn'], "SELECT * from ".DB_PRIFIX."settle_sale where user_id='$user_id' && shop_id='$shop_id' ORDER BY id desc limit 1");
		if($result){
		
				while ($row = mysqli_fetch_assoc($result)) {
					   $result_arr[] = $row;			
					}
					return $result_arr;	
		}else{
		 return $result_arr;
		}
	}
	
	public function getAllSettle($inputs){
		$last_settle_sale=$this->getSettleSale($inputs);
		//print_r($last_settle_sale);exit;
		$inputs['from_date']=$last_settle_sale[0]['settle_date'];
		$open_drawer_log=$this->getOpenDrawerLog($inputs);
		$orders = $this->getSaleOrderItemDetailsList($inputs);		
		$pre_cake_orders=$this->getOrdersByDateForSettleSale($inputs);
		$pre_cake_orders_paid=$this->getOrdersByDateForSettleSalePaid($inputs);
		$credit_sale=$this->getCreditSale($inputs);
		$pay_back=$this->getAllPayBack($inputs);
		//$add_amt_rem=$this->getOrderAmount($inputs);
		$adv_amt_rem=$this->getAdvAmount($inputs);
		$orders_by_paid_date = $this->getSaleOrderItemDetailsListByPaidDate($inputs);
		$Grand_total=0;$Net_total=0;$CashSale=0;$CardSale=0;$CreditSale=0;$CashDrawerAmt=0;
		$cod_pending=false;
		$total_cgst = 0; $total_sgst = 0; $total_cgst1 = 0; $total_sgst1 = 0;
		$CashStartAmt=0;$DeliverySale=0;$CreditRecover=0;$DiscountTotal=0;$Cg_adv=0;$Cg_recover=0;$py_back=0;$add_amt_new=0;$DeliveryRecover=0;$adv_amt_new=0;$Online_Order_Recovery=0;$cash_sale_without_vat=0;$cash_sale_vat=0;$CashSale1=0;$total1=0;$CardSale1=0;
		if(!empty($orders_by_paid_date)) {
			foreach($orders_by_paid_date as $row){
				//echo '<pre>'; print_r($row); die;
				if($row['order_type'] == 'website_order') {
					if($inputs['user_id']==$row['user_id'] && $inputs['shop_id']==$row['shop_id']){
					$items=(array)json_decode($row['items'],true);
					if(count($items)!=0){
						$total = $total_cgst = $total_sgst = 0;
							  foreach($items as $items_row){
							$qty=$items_row['qty'];
							$price=$items_row['price'];
							$total+=($qty*$price);
							// print_r($qty); die;
							if(BILL_TAX == 'yes'){
								if(BILL_TAX_TYPE == 'GST')
								{
									$total_cgst += (($items_row['qty']*$items_row['price'])*$items_row['CGST'])/100;
									$total_sgst += (($items_row['qty']*$items_row['price'])*$items_row['SGST'])/100;
								}
							}
						}
						//VAT START
						if(BILL_TAX == 'yes'){
							if(BILL_TAX_TYPE == 'VAT')
						 {
							$total = $total+($total/100 * $row['vat']);
						}
						elseif(BILL_TAX_TYPE == 'GST')
						{
							$total_cgst1 += $total_cgst;
							$total_sgst1 += $total_sgst;
						}
					}
						// VAT END
						$discount=$row['discount'];
						$discount_price=0;
						if($inputs['discount_type']=='amount'){
							$discount_price=$discount;
						}else if($row['discount_type']=='percent'){
							 $discount_price=$total*($discount/100);
						}
						$total_by_paid = $total-$discount_price;
						}
						$Online_Order_Recovery=$Online_Order_Recovery+$total_by_paid;
					}
				} elseif($row['order_type'] == 'delivery') {

					if($inputs['user_id']==$row['user_id'] && $inputs['shop_id']==$row['shop_id']){
					$items=(array)json_decode($row['items'],true);
					if(count($items)!=0){
						$total=0;

						foreach($items as $items_row){
							$qty=$items_row['qty'];
							$price=$items_row['price'];
							$total+=($qty*$price);
						if(BILL_TAX == 'yes'){
								if(BILL_TAX_TYPE == 'GST')
								{
									echo $total_cgst += (($items_row['qty']*$items_row['price'])*$items_row['CGST'])/100;
									echo $total_sgst += (($items_row['qty']*$items_row['price'])*$items_row['SGST'])/100;
								}
							}
						}
						//VAT START
							if(BILL_TAX == 'yes'){
								if(BILL_TAX_TYPE == 'VAT')
							 {
								$total = $total+($total/100 * $row['vat']);
							}
							elseif(BILL_TAX_TYPE == 'GST')
							{
								$total_cgst1 += $total_cgst;
								$total_sgst1 += $total_sgst;
							}
			    		}
						// VAT END
						$discount=$row['discount'];
						$discount_price=0;
						if($inputs['discount_type']=='amount'){
							$discount_price=$discount;
						}else if($row['discount_type']=='percent'){
							 $discount_price=$total*($discount/100);
						}
						$total_by_paid = $total-$discount_price;
						}
						$DeliveryRecover=$DeliveryRecover+$total_by_paid;
					}
				}
				
			}
		}		
		if(!empty($pay_back)) {
			foreach($pay_back as $py_back_row){
				$py_back += $py_back_row['amount'];
			}
		}
		if(!empty($add_amt_rem)) {
			foreach($add_amt_rem as $add_amt){
				$add_amt_new += $add_amt['amount'];
			}
		}
		if(!empty($adv_amt_rem)) {
			foreach($adv_amt_rem as $adv_amt){
				$adv_amt_new += $adv_amt['advance_amount'];
			}
		}		
		if(!empty($orders)) {
			foreach($orders as $row){
				//print_r($row['receipt_id']);
				//print_r($row['items']);
			
				if($inputs['user_id']==$row['user_id'] && $inputs['shop_id']==$row['shop_id']){
				$items=(array)json_decode($row['items'],true);
				 // echo "<pre>"; print_r($row);
				if(count($items)!=0){
					//print_r($items);
					$total=0;$total1=0;
					 // print_r($items);
					foreach($items as $key=>$items_row){
						// print_r($items_row['item_id']);
						$qty=$items_row['qty'];
						$price=$items_row['price'];
						$tax_without_price=$items_row['tax_without_price'];
						if($row['order_type'] != 'combo'){
							 $total+=($qty*$price);
							$total1+=($qty*$price);

						if(BILL_TAX == 'yes'){
								if(BILL_TAX_TYPE == 'GST')
								{
									
									$total_cgst += ($items_row['qty']*$items_row['price']) - (($items_row['qty']*$items_row['price']*100)/($items_row['CGST']+100));
									$total_sgst += ($items_row['qty']*$items_row['price']) - (($items_row['qty']*$items_row['price']*100)/($items_row['SGST']+100));
								}
							}

						}
						// Combo Package 	
						 else {
							if(BILL_TAX == 'yes')
							{
							if(BILL_TAX_TYPE == 'VAT')
								 {
							if($key == 0) {
								$total =($row['combo_package_price']);
								$total1 =($row['combo_package_price']);

								}
								}
							    elseif(BILL_TAX_TYPE == 'GST')
								{
									if($key == 0) {
								$total = ($row['combo_package_price']);
								$total1 = ($row['combo_package_price']);

								}
						 	
						    
								}

			    			}	
						}
					}


					//VAT START
					if(BILL_TAX == 'yes'){
								if(BILL_TAX_TYPE == 'VAT')
							 {
								$total = $total+($total/100 * $row['vat']);
							}
							elseif(BILL_TAX_TYPE == 'GST')
							{
								if($row['order_type'] == 'combo')
								{
									
								$cgst = $row['combo_package_gst']/2;
						 		$sgst = $row['combo_package_gst']/2;
								$total_cgst += $row['combo_package_price']/100*$cgst;
		   						$total_sgst += $row['combo_package_price']/100*$sgst;
		   							//$total_cgst1 += $total_cgst; 
								//$total_sgst1 += $total_sgst; 

								}
								
								$total_cgst1 = $total_cgst;
						    $total_sgst1 = $total_sgst;
							}
			    		}
					// VAT END
			
					if($row['payment_type'] == 'card'){
						$CardSale=($CardSale+$total) - $row['discount'];
						$CardSale1=($CardSale1+$total1) - $row['discount'];						
					}

					if($row['payment_type'] == 'cash'){
						$CashSale=($CashSale+$total) - $row['discount'];

						$CashSale1=($CashSale1+$total1) - $row['discount'];


						//$cash_sale_with_discount = $CashSale - $row['discount'];
						//$cash_sale_without_vat = $CashSale - ($CashSale1 * ($row['vat']/100));
						//$cash_sale_vat = $CashSale * ($row['vat']/100);
					}
					
					if($row['payment_type'] == 'credit'){
						$CreditSale=($CreditSale+$total) - $row['discount'];
					} 
					if($row['payment_type'] == 'cod'){
						$DeliverySale=($DeliverySale+$total) - $row['discount'];
						
						if($row['payment_status']=='unpaid'){
							$cod_pending=true;
						}
						if($row['status']!='delivered'){
							$cod_pending=true;
						}
					}
					$discount=$row['discount'];
					$discount_price=0;
					if($inputs['discount_type']=='amount'){
						$discount_price=$discount;
					}else if($row['discount_type']=='percent'){
						 $discount_price=$total*($discount/100);
					}
					$DiscountTotal=$DiscountTotal+$discount_price;
					
					
				}
				}
			}
		}
		if(!empty($credit_sale)) {
			foreach($credit_sale as $row){
				if($row['type']=='debit'){
					$CreditRecover+=$row['amount'];
				}
			}
		}
		if(!empty($pre_cake_orders)) {
			foreach($pre_cake_orders as $row){
				$Cg_adv+=$row['advance_amount'];
			}
		}
		if(!empty($pre_cake_orders_paid)) {
			foreach($pre_cake_orders_paid as $row){
				//$Cg_recover+=$row[''];
				if($row['payment_status']=='paid'){
					$total=0;
					$inputs['order_id']=$row['id'];
					$order_items=$this->getOrderItems($inputs);
					//print_r($order_items);		
					foreach($order_items as $row_items){
						$total+=$row_items['amount'];
					}
					//VAT START
					if(BILL_TAX == 'yes' && BILL_COUNTRY == 'UAE') {
						$total = $total+($total/100 * $row['vat']);
					}
					// VAT END
					$Cg_recover_old+=$total-$row['advance_amount'];
				}
			}
		}
		$Cg_recover=$add_amt_new-$adv_amt_new;
		$Grand_total=$CashSale+$CardSale+$CreditSale+$CreditRecover+$DeliverySale+$Cg_adv+$Cg_recover+$DiscountTotal;
		$CashDrawerAmt=($CashSale+$Cg_adv+$Cg_recover+$CreditRecover+$DeliveryRecover+$Online_Order_Recovery)-$py_back;
		$Net_total=$Grand_total-$DiscountTotal;
		//VAT START
		/*if(BILL_TAX == 'yes' && BILL_COUNTRY == 'UAE') {
			$Grand_total = $Grand_total+($Grand_total/100 * $row['vat']);
			$CashDrawerAmt = $CashDrawerAmt+($CashDrawerAmt/100 * $row['vat']);
			$Net_total = $Net_total+($Net_total/100 * $row['vat']);
		}*/
		// VAT END
		
		$result_ar = array();
		$result_ar['cash_sale']=$CashSale;
		$result_ar['card_sale']=$CardSale;
		$result_ar['credit_sale']=$CreditSale;
		$result_ar['delivery_sale']=$DeliverySale;
		//$result_ar['paid_sale']=$PaidSale;
		$result_ar['credit_recover']=$CreditRecover;
		$result_ar['delivery_recover']=$DeliveryRecover;
		$result_ar['online_order_recovery']=$Online_Order_Recovery;		
		$result_ar['cg_advance']=$Cg_adv;
		$result_ar['cg_recover']=$Cg_recover;
		$result_ar['pay_back']=$py_back;
		$result_ar['gross_total']=$Grand_total;
		$result_ar['discount']=$DiscountTotal;
		$result_ar['net_total']= $Net_total;
		$result_ar['cash_drawer'] = $CashDrawerAmt;
		$result_ar['last_settle_date'] = $inputs['from_date'];
		//$result_ar['open_drawer_log']=sizeof($open_drawer_log);
		$result_ar['open_drawer_log'] = 0;
		$result_ar['cod_pending'] = $cod_pending;
		$result_ar['cash_sale_without_vat'] =  $CashSale1;
		$result_ar['cash_sale_vat']=$CashSale - $CashSale1;
		$result_ar['card_sale_without_vat']=$CardSale1;
		$result_ar['card_sale_vat']=$CardSale - $CardSale1;
		
		$result_ar['total_cgst']=$total_cgst1;
		$result_ar['total_sgst']=$total_sgst1;
		$result_ar['cash_sale']=$CashSale;
		$result_ar['card_sale']=$CardSale;
		
		 
		 // print_r($result_ar);die;
		return $result_ar;	
	}
	
	public function getSaleOrderItemDetails($inputs) {
		$shop_id = $inputs['shop_id'];
		$user_id = $inputs['user_id'];
		$order_type = $inputs['order_type'];
		$driver_id=$inputs['driver_id'];
		
		$payment_type = $inputs['payment_type'];
		$payment_status=$inputs['payment_status'];
		$status=$inputs['status'];
		$receipt_id=$inputs['receipt_id'];
		$ordered_date=$inputs['ordered_date'];
		$o_date=date("Y-m-d H:i:s", strtotime($inputs['ordered_date']));
		$contact_name = (isset($inputs['contact_name']) && $inputs['contact_name'] !='') ? $inputs['contact_name'] : '';
		$contact_number = (isset($inputs['contact_number']) && $inputs['contact_number'] !='') ? $inputs['contact_number'] : '';
		$address = (isset($inputs['address']) && $inputs['address'] !='') ? $inputs['address'] : '';		
		
		$discount = (isset($inputs['discount']) && $inputs['discount'] !='') ? $inputs['discount'] : '0.0';
		
		$qry = "SELECT * FROM ".DB_PRIFIX."sale_orders WHERE receipt_id = '$receipt_id'";
		//$qry = "SELECT * FROM `sale_orders` WHERE `receipt_id` LIKE '$receipt_id'";
    	$result=mysqli_query($GLOBALS['conn'], $qry);
    	$num=mysqli_num_rows($result);
		if($num > 0){
		    return false;
		} else {
		
		$result = mysqli_query($GLOBALS['conn'], "INSERT INTO ".DB_PRIFIX."sale_orders(shop_id, user_id, order_type, payment_type, discount , contact_name, contact_number, address,driver_id,
		ordered_date,payment_status,status,receipt_id) 
		VALUES('$shop_id', '$user_id', '$order_type', '$payment_type', '$discount' , '$contact_name', '$contact_number', '$address','$driver_id','$o_date','$payment_status','$status','$receipt_id')");
        if ($result) {            
            $sale_order_id = mysqli_insert_id($GLOBALS['conn']);
			$obj = (array)json_decode($inputs['items'], TRUE);
			
			$total_amount=0;
			for($i=0; $i<count($obj); $i++) {
            //foreach($inputs['items'] as $items) {
				$item_id = $obj[$i]['item_id'];
				$qty = $obj[$i]['item_qty'];
				$price = $obj[$i]['item_price'];
				$item_name=$obj[$i]['item_name'];
				$item_add_price_id=$obj[$i]['item_add_price_id'];
				$multiplle_val=$qty*$price;
				$total_amount+=$multiplle_val;
				$sale_order_id = $sale_order_id;
				$results = mysqli_query($GLOBALS['conn'], "INSERT INTO ".DB_PRIFIX."sale_order_items(item_id, qty, price, sale_order_id,item_name,item_add_price_id) VALUES('$item_id', '$qty', '$price', '$sale_order_id','$item_name','$item_add_price_id')");
			}
			if($payment_type=='credit'){
				$result_credit=mysqli_query($GLOBALS['conn'], "INSERT INTO ".DB_PRIFIX."credit_sale(name,number,type,amount,paid_date,sale_order_id,user_id,shop_id) 
				VALUES('$contact_name','$contact_number','credit','$total_amount','$o_date','$sale_order_id','$user_id','$shop_id')");
				
			}
			$result = mysqli_query($GLOBALS['conn'], "SELECT * FROM ".DB_PRIFIX."sale_orders WHERE id = $sale_order_id");  
            return mysqli_fetch_assoc($result);
        } else {
            return false;
        }
		}
	}
	public function getLastReceiptId($inputs){
		$shop_id=$inputs['shop_id'];
		$user_id=$inputs['user_id'];
		$query= "SELECT * FROM ".DB_PRIFIX."sale_orders where shop_id='$shop_id' AND user_id='$user_id' order by id DESC limit 1";
		$result=mysqli_query($GLOBALS['conn'], $query);
		if ($result) {
			$result_arr = array();
			while ($row = mysqli_fetch_assoc($result)) {
				$result_ar = array();
				$result_ar['id'] = $row['id'];
				$result_ar['user_id'] = $row['user_id'];
				$result_ar['shop_id'] = $row['shop_id'];
				$result_ar['contact_name'] = $row['contact_name'];
				$result_ar['contact_number'] = $row['contact_number'];
				$result_ar['address'] = $row['address'];
				$result_ar['order_type'] = $row['order_type'];
				$result_ar['payment_type'] = $row['payment_type'];
				$result_ar['payment_status'] = $row['payment_status'];
				$result_ar['status'] = $row['status'];
				$result_ar['driver_id']=$row['driver_id'];
				$result_ar['ordered_date']=$row['ordered_date'];
				$result_ar['discount']=$row['discount'];
				$result_ar['receipt_id']=$row['receipt_id'];
			
				
				$order_items_arr = array();
				$query2 = "SELECT * FROM ".DB_PRIFIX."sale_order_items WHERE sale_order_id='".$row['id']."'";
				$result2 = mysqli_query($GLOBALS['conn'], $query2);
				if ($result2) {
					while ($row2 = mysqli_fetch_assoc($result2)) {
						$order_items_arr[] = $row2;						
					}
				}		
				$result_ar['items'] = json_encode( $order_items_arr );
				$result_arr[] = $result_ar;
			}			
			return $result_arr;
		}		
        else {
			return false;
		}
	}
	public function setPayBack($inputs){
		$shop_id = $inputs['shop_id'];
		$user_id = $inputs['user_id'];
		$receipt_id=$inputs['receipt_id'];
		$item_id=$inputs['item_id'];
		$qty=$inputs['qty'];
		$sale_order_item_id=$inputs['sale_order_item_id'];
		$amount=$inputs['amount'];
		$sale_order_id=$inputs['sale_order_id'];
		$payback_date=$inputs['payback_date'];
		$query="INSERT INTO ".DB_PRIFIX."pay_back(shop_id,sale_order_item_id,user_id,receipt_id,item_id,qty,amount,payback_date,sale_order_id) 
		VALUES('$shop_id','$sale_order_item_id','$user_id','$receipt_id','$item_id','$qty','$amount','$payback_date','$sale_order_id')";
		$result=mysqli_query($GLOBALS['conn'], $query);
		   if ($result) {            
            $pay_back_id = mysqli_insert_id($GLOBALS['conn']);
				$result1 = mysqli_query($GLOBALS['conn'], "SELECT * FROM pay_back WHERE id = $pay_back_id");
				return mysqli_fetch_assoc($result1);
		   }else{
				return false;
			}
	}

	public function getPayBack($inputs){
		$receipt_id=$inputs['receipt_id'];
		$item_id=$inputs['item_id'];
		$query="SELECT * from ".DB_PRIFIX."pay_back where receipt_id='$receipt_id'";
		if(isset($item_id) && $item_id != ''){
			$query .=" AND item_id='$item_id'";
		}
		$result=mysqli_query($GLOBALS['conn'], $query);
		if($result){
			$result_arr = array();
			while($row=mysqli_fetch_assoc($result)){
				$result_arr[]=$row;
			}
			return $result_arr;
		}else{
			return false;
		}
	}
	public function getAllPayBack($inputs){
		$user_id=$inputs['user_id'];
		$shop_id=$inputs['shop_id'];
		$from_date=$inputs['from_date'];
		$to_date=$inputs['to_date'];
		$query="SELECT * from ".DB_PRIFIX."pay_back";
		if((isset($from_date) && $from_date != '') && (isset($to_date) && $to_date != '' )){
			$query .=" WHERE user_id='$user_id' AND shop_id='$shop_id' AND payback_date BETWEEN '$from_date' AND '$to_date'";
		}else{
			$query .=" where user_id='$user_id' AND shop_id='$shop_id'";
		}
		$result=mysqli_query($GLOBALS['conn'], $query);
		if($result){
			$result_arr = array();
			while($row=mysqli_fetch_assoc($result)){
				$result_arr[]=$row;
			}
			return $result_arr;
		}else{
			return false;
		}
	}
	public function getAdvAmount($inputs){
		$user_id=$inputs['user_id'];
		$shop_id=$inputs['shop_id'];
		$from_date=$inputs['from_date'];
		$to_date=$inputs['to_date'];
		$query="SELECT * from ".DB_PRIFIX."orders";
		if((isset($from_date) && $from_date != '') && (isset($to_date) && $to_date != '' )){
			$query .=" WHERE user_id='$user_id' AND shop_id='$shop_id' AND paid_date BETWEEN '$from_date' AND '$to_date'";
		}else{
			$query .=" where user_id='$user_id' AND shop_id='$shop_id'";
		}
		$result=mysqli_query($GLOBALS['conn'], $query);
		if($result){
			$result_arr = array();
			while($row=mysqli_fetch_assoc($result)){
				$result_arr[]=$row;
			}
			return $result_arr;
		}else{
			return false;
		}
	}
	public function getOrderAmount($inputs){
		$adv_amt=$this->getAdvAmount($inputs);	
		//print_r($adv_amt);
		if(!empty($adv_amt)) {
			foreach($adv_amt as $add) {
				$adv_id = $add['id'];			
				$query="SELECT * from ".DB_PRIFIX."order_items WHERE order_id='$adv_id'";
				$result=mysqli_query($GLOBALS['conn'], $query);
				if($result){
					$value_pl = array();
					while($row=mysqli_fetch_assoc($result)){
						$result_arr[]=$row;
					}
					$value_pl =  $result_arr;
				}else{
					return false;
				}
			}
		}
		return $value_pl;
		
	}
	public function getCreditSale($inputs){
		$shop_id=$inputs['shop_id'];
		$user_id=$inputs['user_id'];
		$from_date=$inputs['from_date'];
		$to_date=$inputs['to_date'];
		$query="SELECT * from ".DB_PRIFIX."credit_sale";
		if((isset($from_date) && $from_date != '') && (isset($to_date) && $to_date != '' )){
			$query .=" WHERE shop_id='$shop_id' AND paid_date BETWEEN '$from_date' AND '$to_date'";
		}else{
			$query .=" WHERE shop_id='$shop_id'";
		}
		if(isset($user_id) && $user_id != ''){
		$query .=" AND user_id='$user_id'";
		}
		//echo $query;
		$result=mysqli_query($GLOBALS['conn'], $query);
		if($result){
			$result_arr = array();
			while ($row = mysqli_fetch_assoc($result)) {
			   $result_arr[] = $row;			
			}
			return $result_arr;	
		}else{
			return false;
		}
	}
	public function getUpdateCredit($inputs){
		$shop_id=$inputs['shop_id'];
		$user_id=$inputs['user_id'];
		$contact_name=$inputs['name'];
		$contact_number=$inputs['number'];
		$type=$inputs['type'];
		$total_amount=$inputs['given_amt'];
		$paid_date = date("Y-m-d H:i:s", strtotime($inputs['paid_date']));
		$result_credit=mysqli_query($GLOBALS['conn'], "INSERT INTO ".DB_PRIFIX."credit_sale(name,number,type,amount,paid_date,user_id,shop_id) 
				VALUES('$contact_name','$contact_number','$type','$total_amount','$paid_date','$user_id','$shop_id')");

		if($result_credit){
			$query="SELECT * from credit_sale";
			$result=mysqli_query($GLOBALS['conn'], $query);
			if($result){
				$result_arr = array();
				while ($row = mysqli_fetch_assoc($result)) {
					   $result_arr[] = $row;			
					}
					return $result_arr;	
			}else{
				return false;
			}
		}else{
			return false;
		}		
	}
	public function getSaleOrderItemDetailsList($inputs) {//print_r($inputs); die;
		$shop_id = $inputs['shop_id'];
		$from_date=$inputs['from_date'];
		$user_id=$inputs['user_id'];
		$payment_type = (isset($inputs['payment_type']) && $inputs['payment_type'] !='') ? $inputs['payment_type'] : '';
		$order_type = (isset($inputs['order_type']) && $inputs['order_type'] !='') ? $inputs['order_type'] : '';
		$customer_number = (isset($inputs['customer_number']) && $inputs['customer_number'] !='') ? $inputs['customer_number'] : '';
		$receipt_id = (isset($inputs['receipt_id']) && $inputs['receipt_id'] !='') ? $inputs['receipt_id'] : '';
		$status_online_order = (isset($inputs['status_online_order']) && $inputs['status_online_order'] !='') ? $inputs['status_online_order'] : '';
		//echo $from_date;
		$to_date=$inputs['to_date'];
		$query = "SELECT * FROM ".DB_PRIFIX."sale_orders WHERE id !=''";
		if(isset($shop_id) && $shop_id != '') {
			$query .= " AND shop_id='$shop_id'";
		}		
		if((isset($from_date) && $from_date != '') && (isset($to_date) && $to_date != '' )) {
			$query .= " AND ordered_date BETWEEN '$from_date' AND  '$to_date' ";		
		}
		if(isset($payment_type) && $payment_type != '') {
			$query .= " AND payment_type='$payment_type'";
		}
		if(isset($user_id) && $user_id != '') {
			$query .= " AND user_id='$user_id'";
		}
		if(isset($order_type) && $order_type != '') {
			$query .= " AND order_type='$order_type'";
		}
		if(isset($inputs['receipt_id']) && $inputs['receipt_id'] != '') {			
			$query .= " AND receipt_id = '$receipt_id'";
		}
		if(isset($inputs['customer_number']) && $inputs['customer_number'] != '') {			
			$query .= " AND contact_number = '$customer_number'";
		}
		if($status_online_order != ''){
			if(isset($inputs['payment_status']) && $inputs['payment_status'] != '') {			
				$query .= " AND payment_status = 'unpaid'";
			}
		}

		if(isset($inputs['status']) && $inputs['status'] != '' && $status_online_order == '') {			
			$query .= " AND status != 'delivered'";
		}
		//For online order
		if(isset($inputs['status_online_order']) && $inputs['status_online_order'] != '') {			
			$query .= " AND status = '$status_online_order'";
		}
		$query .= " ORDER BY id DESC";
		// echo $query; die;
		$result = mysqli_query($GLOBALS['conn'], $query);
		if ($result) {
			$result_arr = array();
			while ($row = mysqli_fetch_assoc($result)) {
				$result_ar = array();
				$result_ar['id'] = $row['id'];
				$result_ar['user_id'] = $row['user_id'];
				$result_ar['shop_id'] = $row['shop_id'];
				$result_ar['contact_name'] = $row['contact_name'];
				$result_ar['contact_number'] = $row['contact_number'];
				$result_ar['address'] = $row['address'];
				$result_ar['order_type'] = $row['order_type'];
				$result_ar['payment_type'] = $row['payment_type'];
				$result_ar['payment_status'] = $row['payment_status'];
				$result_ar['status'] = $row['status'];
				$result_ar['driver_id']=$row['driver_id'];
				$result_ar['ordered_date']=$row['ordered_date'];
				$result_ar['discount']=$row['discount'];
				$result_ar['receipt_id']=$row['receipt_id'];
				$result_ar['delivered_in']=$row['delivered_in'];
				$result_ar['reject_reason']=$row['reject_reason'];
				$result_ar['vat']=$row['vat'];
				$result_ar['combo_package_price']=$row['combo_package_price'];
				$result_ar['combo_package_gst']=$row['combo_package_gst'];
				
				$order_items_arr = array();
				$query2 = "SELECT * FROM ".DB_PRIFIX."sale_order_items  WHERE sale_order_id='".$row['id']."'";
			
				$result2 = mysqli_query($GLOBALS['conn'], $query2);
				if ($result2) {
					while ($row2 = mysqli_fetch_assoc($result2)) {
						$order_items_arr[] = $row2;						
					}
				}		
				$result_ar['items'] = json_encode( $order_items_arr );
				$result_arr[] = $result_ar;
			}			
			return $result_arr;
		}		
        else {
			return false;
		}
	}
	
	public function getSalesDetailsuserwise($inputs)
	{
		//$date = date('Y-m-d');
		$last_settle_sale=$this->getSettleSale($inputs);
		//print_r($last_settle_sale);exit;
		$inputs['from_date']=$last_settle_sale[0]['settle_date'];
		$shop_id=$inputs['shop_id'];
		$from_date=$inputs['from_date'];
		$user_id=$inputs['user_id'];
		$to_date=$inputs['to_date'];
		$shop=$inputs['shop_id'];
		
		//echo '<pre>'; print_r($inputs); die;
		$qry="SELECT user_id, SUM(soi.qty*soi.price) as amount, driver_id FROM `sale_orders` so LEFT JOIN sale_order_items soi ON (soi.sale_order_id = so.id) WHERE order_type != 'combo'"; 
		if($shop != ''){
			$qry .=" AND shop_id = '$shop'";
		}
		
		if($user_id != ''){
			$qry .=" AND so.user_id= '$user_id'";
		}
		if($from_date != '' && $to_date != '') {
			$qry .= " AND so.ordered_date BETWEEN '$from_date' AND '$to_date' ";
		}
			$qry .="  GROUP BY so.driver_id ORDER BY so.driver_id ASC";
		//echo $qry;
		$result=mysqli_query($GLOBALS['conn'], $qry);
		//$num=mysqli_num_rows($result);	//echo "total result ".$num;
		if($result)
		{
			$result_arr = array();
			while ($row = mysqli_fetch_assoc($result)) {
			$result_arr[] = $row;
			}	
			return $result_arr;
		}
		else
		return false;
	}
	
	public function getSaleOrderItemDetailsListByPaidDate($inputs) {
		$shop_id = $inputs['shop_id'];
		$from_date=$inputs['from_date'];
		$user_id=$inputs['user_id'];
		//echo $from_date;
		$to_date=$inputs['to_date'];
		$query = "SELECT * FROM ".DB_PRIFIX."sale_orders";
		if(isset($shop_id) && $shop_id != '') {
			$query .= " where shop_id='$shop_id'";
		}
		
		if((isset($from_date) && $from_date != '') && (isset($to_date) && $to_date != '' )) {
			
			if(isset($shop_id) && $shop_id != ''){
				$query .= " AND paid_date BETWEEN  '$from_date' AND  '$to_date' AND payment_status = 'paid'";
			}else{
				$query .= " where paid_date BETWEEN  '$from_date' AND  '$to_date' AND payment_status = 'paid' ";
			}
			
		} 
		$query .= " ORDER BY id DESC";
		$result = mysqli_query($GLOBALS['conn'], $query);
		if ($result) {
			$result_arr = array();
			while ($row = mysqli_fetch_assoc($result)) {
				$result_ar = array();
				$result_ar['id'] = $row['id'];
				$result_ar['user_id'] = $row['user_id'];
				$result_ar['shop_id'] = $row['shop_id'];
				$result_ar['contact_name'] = $row['contact_name'];
				$result_ar['contact_number'] = $row['contact_number'];
				$result_ar['address'] = $row['address'];
				$result_ar['order_type'] = $row['order_type'];
				$result_ar['payment_type'] = $row['payment_type'];
				$result_ar['payment_status'] = $row['payment_status'];
				$result_ar['status'] = $row['status'];
				$result_ar['driver_id']=$row['driver_id'];
				$result_ar['ordered_date']=$row['ordered_date'];
				$result_ar['discount']=$row['discount'];
				$result_ar['receipt_id']=$row['receipt_id'];
				$result_ar['vat']=$row['vat'];
			
				
				$order_items_arr = array();
				$query2 = "SELECT * FROM ".DB_PRIFIX."sale_order_items  WHERE sale_order_id='".$row['id']."'";
			
				$result2 = mysqli_query($GLOBALS['conn'], $query2);
				if ($result2) {
					while ($row2 = mysqli_fetch_assoc($result2)) {
						$order_items_arr[] = $row2;						
					}
				}		
				$result_ar['items'] = json_encode( $order_items_arr );
				$result_arr[] = $result_ar;
			}			
			return $result_arr;
		}		
        else {
			return false;
		}
	}

	public function getSaleOdersItemDetailsListByReceipt($inputs){
		$receipt_id = $inputs['receipt_id'];
		$query = "SELECT * FROM ".DB_PRIFIX."sale_orders where receipt_id='$receipt_id'";
		$result = mysqli_query($GLOBALS['conn'], $query);
			if ($result) {
			$result_arr = array();
			while ($row = mysqli_fetch_assoc($result)) {
				$result_ar = array();
				$result_ar['id'] = $row['id'];
				$result_ar['user_id'] = $row['user_id'];
				$result_ar['shop_id'] = $row['shop_id'];
				$result_ar['contact_name'] = $row['contact_name'];
				$result_ar['contact_number'] = $row['contact_number'];
				$result_ar['address'] = $row['address'];
				$result_ar['order_type'] = $row['order_type'];
				$result_ar['payment_type'] = $row['payment_type'];
				$result_ar['payment_status'] = $row['payment_status'];
				$result_ar['status'] = $row['status'];
				$result_ar['driver_id']=$row['driver_id'];
				$result_ar['ordered_date']=$row['ordered_date'];
				$result_ar['discount']=$row['discount'];
				$result_ar['receipt_id']=$row['receipt_id'];
			    $result_ar['vat']=$row['vat'];
				
				$order_items_arr = array();
				$query2 = "SELECT * FROM ".DB_PRIFIX."sale_order_items WHERE sale_order_id='".$row['id']."'";
					
				$result2 = mysqli_query($GLOBALS['conn'], $query2);
				if ($result2) {
					while ($row2 = mysqli_fetch_assoc($result2)) {
					
						$query3 = "SELECT * FROM ".DB_PRIFIX."pay_back WHERE sale_order_item_id='".$row2['id']."'";
						$result3 = mysqli_query($GLOBALS['conn'], $query3);
						if ($result3) {
							while($row3 = mysqli_fetch_assoc($result3)){
							$total_rem = ($row2['qty'] - $row3['qty']);	
							$row2['qty']=$total_rem;			
							}
						}
						if($row2['qty']!=0){
						$order_items_arr[] = $row2;
						}
					}
				}	
				$result_ar['items'] = json_encode( $order_items_arr );
				$result_arr[] = $result_ar;
			}
				
			return $result_arr;
		}		
        else {
			return false;
		}

	}

	public function getPurchaseOrderItemDetails($inputs){
		$shop_id = $inputs['shop_id'];
		$user_id = $inputs['user_id'];
		$mfuits_id=$inputs['mfunits_id'];
		$status=$inputs['status'];
		$date_needed=date('Y-m-d',strtotime($inputs['date_needed']));
		$date_added = date("Y-m-d H:i:s");
		$result = mysqli_query($GLOBALS['conn'], "INSERT INTO ".DB_PRIFIX."purchase_orders(shop_id, user_id, manufacturing_unit_id, date_added, status,date_needed) VALUES('$shop_id', '$user_id', '$mfuits_id','$date_added', '$status','$date_needed')");
		if($result){
			$purchase_order_id=mysqli_insert_id($GLOBALS['conn']);
			$obj=(array)json_decode($inputs['items'],TRUE);
			for($i=0;$i<count($obj);$i++){
				$item_id = $obj[$i]['item_id'];
				$qty = $obj[$i]['qty'];
				$item_name=$obj[$i]['item_name'];
				$item_add_price_id=$obj[$i]['item_add_price_id'];
				$purchase_order_id=$purchase_order_id;
				$results = mysqli_query($GLOBALS['conn'], "INSERT INTO ".DB_PRIFIX."purchase_order_items(item_id, qty, purchase_order_id,item_name,item_add_price_id) VALUES('$item_id', '$qty', '$purchase_order_id','$item_name','$item_add_price_id')");
			}
			$result = mysqli_query($GLOBALS['conn'], "SELECT * FROM ".DB_PRIFIX."purchase_orders WHERE id = $purchase_order_id");  
            return mysqli_fetch_assoc($result);
		}else{return false; }
	}
	
		public function getPurchaseOrderItemDetailsList($inputs) {
		$mfunits_id = $inputs['mfunits_id'];
		$from_date=$inputs['from_date'];
		$to_date=$inputs['to_date'];
		$query = "SELECT * FROM ".DB_PRIFIX."purchase_orders";
		if(isset($shop_id) && $shop_id != '') {
			$query .= " where manufacturing_unit_id='$mfunits_id'";
		} 
		if((isset($from_date) && $from_date != '') && (isset($to_date) && $to_date != '' )) {
			
			if(isset($shop_id) && $shop_id != ''){
				$query .= " AND date_needed BETWEEN  '$from_date' AND  '$to_date' ";
			}else{
				$query .= " where date_needed BETWEEN  '$from_date' AND  '$to_date' ";
			}
			
		} 
		$query .= " ORDER BY id DESC";
	
		$result = mysqli_query($GLOBALS['conn'], $query);
		if ($result) {
			$result_arr = array();
			while ($row = mysqli_fetch_assoc($result)) {
				$result_ar = array();
				$result_ar['id'] = $row['id'];
				$result_ar['user_id'] = $row['user_id'];
				$result_ar['shop_id'] = $row['shop_id'];
				$shop_id = $row['shop_id'];
				$result_sh=mysqli_query($GLOBALS['conn'], "SELECT * from ".DB_PRIFIX."locations_shops where id=$shop_id");
				$shop=mysqli_fetch_assoc($result_sh);
				$result_ar['shop_name']=$shop['shop_name'];
				$result_ar['mfunits_id'] = $row['manufacturing_unit_id'];
				//$result_ar['contact_name'] = $row['contact_name'];
				//$result_ar['contact_number'] = $row['contact_number'];
				//$result_ar['address'] = $row['address'];
				//$result_ar['order_type'] = $row['order_type'];
				//$result_ar['payment_type'] = $row['payment_type'];
				//$result_ar['payment_status'] = $row['payment_status'];
				$result_ar['status'] = $row['status'];
				//$result_ar['driver_id']=$row['driver_id'];
				$result_ar['date_added']=$row['date_added'];
			
				
				$order_items_arr = array();
				$query2 = "SELECT * FROM ".DB_PRIFIX."purchase_order_items WHERE purchase_order_id='".$row['id']."'";
				$result2 = mysqli_query($GLOBALS['conn'], $query2);
				if ($result2) {
					while ($row2 = mysqli_fetch_assoc($result2)) {
						$order_items_arr[] = $row2;						
					}
				}		
				$result_ar['items'] = json_encode( $order_items_arr );
				$result_arr[] = $result_ar;
			}			
			return $result_arr;
		}		
        else {
			return false;
		}
	}
	
	public function updateSaleOrderStatus($inputs) {
		$sale_order_id = $inputs['id'];
		$status = $inputs['status'];
		$pay_status=$inputs['payment_status'];
		$paid_date=$inputs['paid_date'];
		$result_ = mysqli_query($GLOBALS['conn'], "UPDATE ".DB_PRIFIX."sale_orders SET status = '$status',payment_status='$pay_status', paid_date='$paid_date' WHERE id = $sale_order_id");
		 if ($result_) {
            $result = mysqli_query($GLOBALS['conn'], "SELECT * FROM ".DB_PRIFIX."sale_orders WHERE id = $sale_order_id");   
            return mysqli_fetch_assoc($result);
        } else {
            return false;
        }
	} 
	public function updatePurchaseOrderStatus($inputs) {
		$purchase_order_id = $inputs['id'];
		$status = $inputs['status'];
		$result_ = mysqli_query($GLOBALS['conn'], "UPDATE ".DB_PRIFIX."purchase_orders SET status = '$status' WHERE id = $purchase_order_id");
		 if ($result_) {
            $result = mysqli_query($GLOBALS['conn'], "SELECT * FROM ".DB_PRIFIX."purchase_orders WHERE id = $purchase_order_id");   
            return mysqli_fetch_assoc($result);
        } else {
            return false;
        }
	}
	
	public function getInsertOpenDrawerLog($inputs){
		$user_id=$inputs['user_id'];
		$reason=$inputs['reason'];
		$shop_id = $inputs['shop_id'];
	
		$open_date=date("Y-m-d H:i:s", strtotime($inputs['open_date']));
		$result_credit=mysqli_query($GLOBALS['conn'], "INSERT INTO ".DB_PRIFIX."open_drawer_log(user_id,shop_id,reason,open_date) 
				VALUES('$user_id','$shop_id','$reason','$open_date')");
		
		if($result_credit){
			$query="SELECT * from ".DB_PRIFIX."open_drawer_log where id=$result_credit";
			$result=mysqli_query($GLOBALS['conn'], $query);
			if($result){
			
			
					return mysqli_fetch_assoc($result);	
			}else{
				return false;
			}
		}else{
			return false;
		}		
	}
	public function getOpenDrawerLog($inputs){
		$shop_id = $inputs['shop_id'];
		$from_date=$inputs['from_date'];
		$user_id=$inputs['user_id'];
		$to_date=$inputs['to_date'];
		$query = "SELECT * FROM ".DB_PRIFIX."open_drawer_log";
		if(isset($shop_id) && $shop_id != '') {
			$query .= " where shop_id='$shop_id'";
		}
		if((isset($from_date) && $from_date != '') && (isset($to_date) && $to_date != '' )) {
			
			if(isset($shop_id) && $shop_id != ''){
				$query .= " AND open_date BETWEEN  '$from_date' AND  '$to_date' ";
			}else{
				$query .= " where open_date BETWEEN  '$from_date' AND  '$to_date' ";
			}
		}
		$query .= " ORDER BY id DESC";
		
		$result = mysqli_query($GLOBALS['conn'], $query);	
		if ($result) {
			$result_arr = array();
			while ($row = mysqli_fetch_assoc($result)) {
				$result_arr[] = $row;			
			}
			return $result_arr;
		}else{
			return false;
		}			
	}

	// web by kavitha
	public function setPayBackBluk($inputs){
		$shop_id = $inputs['shop_id'];
		$user_id = $inputs['user_id'];
		$receipt_id=$inputs['receipt_id'];
		$item_id=$inputs['item_id'];
		$qty=$inputs['qty'];
		$sale_order_item_id=$inputs['sale_order_item_id'];
		$amount=$inputs['amount'];
		$sale_order_id=$inputs['sale_order_id'];
		$payback_date=$inputs['payback_date'];
		$query="INSERT INTO ".DB_PRIFIX."pay_back(shop_id,sale_order_item_id,user_id,receipt_id,item_id,qty,amount,payback_date,sale_order_id) 
		VALUES('$shop_id','$sale_order_item_id','$user_id','$receipt_id','$item_id','$qty','$amount','$payback_date','$sale_order_id')";
		$result=mysqli_query($GLOBALS['conn'], $query);
		   if ($result) {            
            $pay_back_id = mysqli_insert_id($GLOBALS['conn']);
				$result1 = mysqli_query($GLOBALS['conn'], "SELECT * FROM pay_back WHERE id = $pay_back_id");
				return mysqli_fetch_assoc($result1);
		   }else{
				return false;
			}
	}

}
 
?>