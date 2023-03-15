<?php
   session_start();
   include("functions_web.php");
   include_once("config.php");
   connect_dre_db();
   chkUserLoggedIn();
   $getUserDetails = getUserDetails($_SESSION['user_id']);
   $getUserDetails = explode(",", $getUserDetails['user_action']);
   $items = getReaminCombaoBill();
   $main_id = (isset($_GET['sale_id']) && $_GET['sale_id'] !='') ? $_GET['sale_id'] : '';
   if($main_id != '') {
   	$single_combo_bill = getCombaoBillSingle($main_id);
   	//echo '<pre>'; print_r($single_combo_bill); die;
   } else {
   	//redirect('index.php');
   }
   $driver_list = getDrivers();
   if(isset($_POST['item_update'])) {
	 //echo '<pre>'; print_r($_POST); die;
	 if($_POST['completed_date']) {
		 foreach($_POST['completed_date'] as $key=>$dates) {
			 //echo $key.'-'.$dates.'-'.$_POST['staff_id'][$key];
			 if($dates) {
				$staff_id = $_POST['staff_id'][$key];
				$sql = "UPDATE sale_order_items SET staff_id = '$staff_id', date_completed = '$dates' WHERE id = '$key'";
				//echo $sql.'<br>';
				mysqli_query($GLOBALS['conn'], $sql);
			 }			 
		 }
		 redirect('combo_package_search.php?sale_id='.$main_id);
	 }
   }
   ?>
<!DOCTYPE html>
<!--[if lt IE 7]>      
<html class="no-js lt-ie9 lt-ie8 lt-ie7">
   <![endif]-->
   <!--[if IE 7]>         
   <html class="no-js lt-ie9 lt-ie8">
      <![endif]-->
      <!--[if IE 8]>         
      <html class="no-js lt-ie9">
         <![endif]-->
         <!--[if gt IE 8]><!--> 
         <html class="no-js">
            <!--<![endif]-->
            <html>
               <head>
                  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
                  <meta charset="UTF-8" />
                  <title>Combo Package</title>
                  <meta name="description" content="" />
                  <meta name="keywords" content="" />
                  <meta name="viewport" content="width=device-width, initial-scale=1">
                  <link rel="stylesheet" href="css/bootstrap.min.css">
                  <link rel="stylesheet" href="css/main.css">
                  <link rel='stylesheet' href='css/counter.css'>
                  <link href="admin/plugins/select2/select2.css" rel="stylesheet">
				  <link href="css/jquery-ui.css" rel="stylesheet">
                  <link rel="icon" type="image/png" sizes="32x32" href="img/favicon.png">
                  <style>
                     .table-val-result, .table-val-result p{text-align: center; font-weight: bold;}
                     .table-val {
                     padding-right: 9px;
                     padding-left: 9px;
                     border-radius: 9px;
                     padding-top: 9px;
                     padding-bottom: 9px;
                     color: #fff;
                     }
                     .success{background: green;}
                     .danger{background: red;}
                     .normal{background: orange;}
                     .border-btm{border-bottom: 1px solid #ccc;}
                     .table-bdy{
                     text-align: center;
                     padding: 30px;
                     border: 1px solid #ccc;
                     border-radius: 10px;
                     font-size: 20px;
                     color: #fff;
                     font-weight: bold;
                     }
                     .table-str .col-sm-2{margin-top: 5px;}
                     .table-remain-cnt {
                     position: absolute;
                     top: 5px;
                     float: right;
                     font-size: 12px;
                     right: 25px;
                     }
                     .border-btm select{
                     height: 45px;
                     font-size: 20px;
                     }
                  </style>
               </head>
               <body>
                  <!--[if lt IE 7]>
                  <p class="browsehappy">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
                  <![endif]-->
                  <header>
                     <!--logo-start-->
                     <div class="container-fluid top-head">
                        <div class="row">
                           <div class="col-sm-4">
                              <div class="logo-s">
                                 <a href="index.php"><img src="img/out.jpg" alt="logo"></a>
                              </div>
                           </div>
                           <div class="col-sm-4">
                              <div class="counter-head">
                                 COMBO PACKAGE BILL
                              </div>
                           </div>
                           <div class="col-sm-4">
                              <div class="log-box-s">
                                 <div class="log-box-s-one">
                                    <a href="index.php">
                                       <span><img src="img/home.png"></span>
                                       <p>HOME</p>
                                    </a>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                  </header>
                  <div style="clear:both"></div>
                  <div class="conter-product">
                     <div class="container table-str">
                        <form method="get">
                           <h4>Choose Bill</h4>
                           <select class="items_lists" name="sale_id" id="sale_id" required style="width:100%;">
                              <option value="">-- Select Bill--</option>
                              <?php foreach($items as $item) { ?>
                              <option value="<?php echo $item['id']; ?>"><?php echo $item['contact_number']; ?> - <?php echo $item['receipt_id']; ?></option>
                              <?php } ?>
                           </select>
                           <button style="margin-top:20px;" type="submit" class="report-btn">SUBMIT</button>
                        </form>

						<?php if($main_id != '') { ?>
						<form method="post">
						<input type="hidden" name="item_update" value="1" />
                        <div class="sub--cls">
                           <h3>Customer Details</h3>
                           <?php //foreach($single_combo_bill as $single_bill) { ?>
                           <p>Name :<?php echo $single_combo_bill[0]['contact_name']; ?></p>
                           <p>Phone :<?php echo $single_combo_bill[0]['contact_number']; ?></p>
                           <p>Address :<?php echo $single_combo_bill[0]['address']; ?></p>
                           <?php //} ?>
                        </div>
                        <div class="sub--cls">
                           <h3>Item Details</h3><a href="single_item_print.php?id=<?php echo $main_id; ?>&re=index.php&combo=yes" style="float:right; color:#fff; background :red; padding:5px 10px; margin-bottom:10px;" >Print</a>
                           <table class="table table-bordered bord">
                              <thead>
                                 <tr bordercolor="#f56212;">
                                    <th>S.no</th>
                                    <th>Item</th>
                                    <th>Staff Name</th>
                                    <th>Completed date</th>
                                 </tr>
                              </thead>
                              <?php foreach($single_combo_bill as $key=>$bill) { ?>
                              <tr>
                                 <td><?php echo $key+1; ?></td>
                                 <td><?php echo $bill['item_name']; ?></td>
                                 <?php if($bill['staff_id'] != '0') { ?>
                                 <td><?php echo getdrivername($bill['staff_id']); ?></td>
                                 <?php } else { ?>					
                                 <td>
                                    <?php if($driver_list) { ?>
                                    <select name="staff_id[<?php echo $bill['id']; ?>]" id="staff_id[<?php echo $bill['id']; ?>]" class="form-control">
                                       <?php foreach($driver_list as $driver) { ?>
                                       <option value="<?php echo $driver['id']; ?>"><?php echo $driver['name']; ?></option>
                                       <?php } ?>
                                    </select>
                                    <?php } ?>
                                 </td>
                                 <?php } ?>
                                 <?php if($bill['date_completed'] != '') { ?>
                                 <td><?php echo date("d-m-Y", strtotime($bill['date_completed']));; ?></td>
                                 <?php } else { ?>
								 <td>
                                 <input class="form-control datepicker-set" name="completed_date[<?php echo $bill['id']; ?>]" id="completed_date[<?php echo $bill['id']; ?>]" value=""/>
								 </td>
								 <?php } ?>
                              </tr>
                              <?php } ?>
                           </table>
						   <button style="margin-top:20px;" type="submit" class="report-btn">SUBMIT</button>
                        </div>
						</form>
						<?php } ?>
                     </div>
                  </div>
                  <script src="js/jquery-3.2.1.min.js"></script> 
                  <script src="js/bootstrap.min.js"></script>
                  <script src="admin/plugins/select2/select2.js"></script>
                  <script></script>
                  <script type="text/javascript">
                     $(".items_lists").select2();
                  </script>
				  <script src="js/jquery-date.js"></script>
				  <script src="js/jquery-ui.js"></script>
				  <script type="text/javascript">
					 $( ".datepicker-set" ).datepicker({dateFormat: 'yy-mm-dd'});
				  </script>
                  <?php include('script_common.php'); ?>
               </body>
            </html>