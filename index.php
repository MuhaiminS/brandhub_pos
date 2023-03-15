<?php
   session_start();
   include("functions_web.php");
   include_once("config.php");
   connect_dre_db();
   chkUserLoggedIn();
   $getUserDetails = getUserDetails($_SESSION['user_id']);
	$getUserDetails = explode(",", $getUserDetails['user_action']);	

	$from_date = date('Y-m-d').' 00:00:00';
	$to_date = date('Y-m-d'). ' 23:59:59';

	$sql = "SELECT COUNT(*) as count FROM ".DB_PRIFIX."sale_orders WHERE ordered_date BETWEEN '$from_date' AND '$to_date' AND order_type = 'website_order' AND status = 'pending'";
	$res = mysqli_fetch_assoc(mysqli_query($GLOBALS['conn'], $sql));
	$pending_count = $res['count'];
	//print_r($res);
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
                  <title>Main page</title>
                  <meta name="description" content="" />
                  <meta name="keywords" content="" />
                  <meta name="viewport" content="width=device-width, initial-scale=1">
                  <link rel="stylesheet" href="css/bootstrap.min.css">
                  <link rel="stylesheet" href="css/main.css">
                  <link rel="icon" type="image/png" sizes="32x32" href="img/favicon.png">
                  <!-- LIGHTBOX CSS -->
               </head>
			   <style>
			   .rem-orders {
					position: absolute;
					top: 0px;
					left: 25px;
					font-size: 19px !important;
					font-weight: bold;
					border: 3px solid;
					border-radius: 50px;
					padding: 3px;
					animation: blinker 1s linear infinite;
					width: 40px;
					height: 40px;
				}
				@keyframes blinker {  
				  50% { opacity: 0; }
				}
			   </style>
               <body>
                  <!--[if lt IE 7]>
                  <p class="browsehappy">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
                  <![endif]-->
                  <header>
                     <!--logo-start-->
                     <div class="container-fluid">
                        <div class="row">
                           <div class="col-sm-4">
                              <div class="logo">
                                 <!-- <img src="img/logo.png" alt="logo"> -->
                              </div>
                           </div>
                           <div class="col-sm-4">
                              <div class="home-head">
                                 <?php echo CLIENT_NAME; ?>
                              </div>
                           </div>
                           <div class="col-sm-4">
                              <div class="log-box">
                                 <div class="icon-box-one">
                                    <p><?php echo date("d-m-Y"); ?></p>
                                    <span><a href="#"><img src="img/logout.png"></a></span>
                                    <a href="logout.php"><span class="style-two">Log Out<br></span></a>
                                 </div>
                                 <div class="icon-box-two">
                                    <span class="style-two">
                                    <?php echo ucfirst($_SESSION['user_name']); ?>
                                    </span>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                  </header>
                  <div style="clear:both"></div>
                  <div class="product-box">
                     <div class="container-fluid">
					 <?php if (in_array('counter_sale',$getUserDetails)){ ?>
                        <!--<div class="col-sm-3">
                           <a href="counter_sale.php">
                              <div class="prod-sale">
                                 <span><img src="img/product/counter.png"></span>
                                 <p>COUNTER SALE</p>
                              </div>
                           </a>
                        </div>-->
					 <?php } if (in_array('delivery_sale',$getUserDetails)){ ?>
                        <div class="col-sm-4">
                           <a href="delivery_sale.php">
                              <div class="prod-sale">
                                 <span><img src="img/product/counter.png"></span>
                                 <p>SALE</p>
                              </div>
                           </a>
                        </div>
						<!-- <div class="col-sm-3">
						<a href="combo_package.php">
                           <div class="prod-sale">
                              <span><img src="img/product/delivery-sale.png"></span>
                              <p>COMBO PACKAGE</p>
                           </div>
						</a>
					</div>
					<div class="col-sm-3">
						<a href="combo_package_search.php">
                           <div class="prod-sale">
                              <span><img src="img/product/delivery-sale.png"></span>
                              <p>COMBO PACKAGE SEARCH</p>
                           </div>
						</a>
					</div> -->
					<?php } if (in_array('reports',$getUserDetails)){ ?>
                        <div class="col-sm-4">
							<a href="sale_report.php">
							   <div class="prod-sale">
								  <span><img src="img/product/reports.png"></span>
								  <p>REPORTS<br></p>
							   </div>
							</a>
                        </div>
					<?php } if (in_array('settle_sale',$getUserDetails)){ ?>
                        <div class="col-sm-4">
						<a href="settle_sale.php">
                           <div class="prod-sale">
                              <span><img src="img/product/settle-sale.png"></span>
                              <p>SETTLE SALE</p>
                           </div>
						</a>
					</div>
					<?php } if (in_array('cod_log',$getUserDetails)){ ?>
					
					<?php } if (in_array('online_order_log',$getUserDetails)){ ?>
                        
					<?php } if (in_array('sale_order_details',$getUserDetails)){ ?>
                       
                        <div class="col-sm-4">
						<a href="sale_order_details.php">
                           <div class="prod-sale">
                              <span><img src="img/product/sale-order.png"></span>
                              <p>SALE ORDER DETAILS</p>
                           </div>
						</a>
                        </div>
					<?php } if (in_array('cash_back',$getUserDetails)){ ?>
					<?php } ?>
					<?php if (in_array('barcode_print',$getUserDetails)){ ?>
					<?php } ?>
					<div class="col-sm-4">
						<a href="item_wise_report.php">
                           <div class="prod-sale">
                              <span><img src="img/product/sale-order.png"></span>
                              <p>SERVICE REPORT</p>
                           </div>
						</a>
                        </div>
					<!-- <img src="img/banner.jpg" class="img-responsive" style="width: 100% !important;"> -->
                     </div>
                  </div>
                  <script src="js/jquery-3.2.1.min.js"></script>
                  <script src="js/bootstrap.min.js"></script>
				  <script type="text/javascript">
    window.onload = maxWindow;

    function maxWindow() {
        window.moveTo(0, 0);


        if (document.all) {
            top.window.resizeTo(screen.availWidth, screen.availHeight);
        }

        else if (document.layers || document.getElementById) {
            if (top.window.outerHeight < screen.availHeight || top.window.outerWidth < screen.availWidth) {
                top.window.outerHeight = screen.availHeight;
                top.window.outerWidth = screen.availWidth;
            }
        }
    }

</script> 
               </body>
            </html>