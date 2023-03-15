<?php 
   session_start();
   require_once 'db_functions.php';
   $function = New DB_Functions(); 
   $getUserDetails = $function->getUserDetails($_SESSION['user_id']);
   $getUserDetails = explode(",", $getUserDetails['user_action']);
   if (!in_array('settle_sale',$getUserDetails)){
   $function->redirect('index.php');
   }
   $inputs['shop_id'] = $_SESSION['shop_id'];
   $inputs['user_id'] = $_SESSION['user_id'];
   $inputs['discount_type'] = 'amount';
   $inputs['to_date'] = date("Y-m-d H:i:s");
   $settle_sale = $function->getAllSettle($inputs);
   //echo '<pre>'; print_r($settle_sale ); die;
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
                  <title>Settle sale</title>
                  <meta name="description" content="" />
                  <meta name="keywords" content="" />
                  <meta name="viewport" content="width=device-width, initial-scale=1">
                  <link rel="stylesheet" href="css/bootstrap.min.css">
                  <link rel="stylesheet" href="css/main.css">
                  <link rel="icon" type="image/png" sizes="32x32" href="img/favicon.ico">
                  <link rel="stylesheet" href="css/bootstrap.css">
               </head>
               <style>
                  .nav-tabs>li>a{background: #f56212; color: #fff;}
                  .nav-tabs>li.active>a,.nav-tabs>li.active>a:focus{background: #000; color: #fff;}
                  .nav-tabs>li>a:hover,.nav-tabs>li.active>a:hover{background: #000; color: #fff;}
                  .nav-tabs>li{width: 50%;}
                  .tabss a{text-align:center;}
                  .table-responsive h3 {margin-top: 5px !important;}
                  table tr {border-bottom: 1px solid #ccc;}
                  .table>tbody>tr>td{vertical-align: middle;}
               </style>
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
                                 <a href="index.php"><img src="img/out.png" alt="logo"></a>
                              </div>
                           </div>
                           <div class="col-sm-4">
                              <div class="counter-head">
                                 Settle Sale
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
                  <div class="settle-sale-three">
                     <div class="container">
                        <div class="table-responsive">
                           <table class="table table-responsive">
                              <tbody>
                                 <tr>
                                    <td>Gross Total</td>
                                    <td><?php echo CURRENCY.' '.$settle_sale['gross_total']; ?></td>
                                 </tr>
                                 <tr>
                                    <td>Discount Amount</td>
                                    <td><?php echo CURRENCY.' '.$settle_sale['discount']; ?></td>
                                 </tr>
                                 <tr>
                                    <td>Net Total</td>
                                    <td><?php echo CURRENCY.' '.$settle_sale['net_total']; ?></td>
                                 </tr>
                              </tbody>
                           </table>
                        </div>
                        <ul class="nav nav-tabs tabss">
                           <li class="active"><a data-toggle="tab" href="#x-factor">Drawer Sale</a></li>
                           <li><a data-toggle="tab" href="#y-factor">Settle Sale</a></li>
                        </ul>
                        <div class="tab-content">
                           <div id="x-factor" class="tab-pane fade in active table-responsive" >
                              <table class="table table-responsive">
                                 <tbody>
                                    <?php if(BILL_TAX == 'yes'){
                                       if(BILL_TAX_TYPE == 'VAT')
                                       { ?>
                                    <tr>
                                       <td>
                                          <h3>Total Cash Sale</h3>
                                       </td>
                                       <td>
                                          <h3><?php echo CURRENCY.' '.number_format((float)$settle_sale['cash_sale_without_vat'], 2, '.', ''); ?></h3>
                                       </td>
                                    </tr>
                                    <?php }
                                       elseif(BILL_TAX_TYPE == 'GST')
                                       { ?>
                                    <tr>
                                       <td>
                                          <h3>Total Cash </h3>
                                       </td>
                                       <td>
                                          <h3><?php echo CURRENCY.' '.number_format((float)$settle_sale['cash_sale'], 2, '.', ''); ?></h3>
                                       </td>
                                    </tr>
                                    <?php	}
                                       } ?>
                                    <?php if(BILL_TAX == 'yes') { if(BILL_TAX_TYPE == 'VAT') { ?>
                                    <tr>
                                       <td>
                                          <h3>Total Cash VAT/h3>
                                       </td>
                                       <td>
                                          <h3><?php echo CURRENCY.' '.number_format((float)$settle_sale['cash_sale_vat'], 2, '.', ''); ?></h3>
                                       </td>
                                    </tr>
                                    <?php } } ?>
                                    <?php if(BILL_TAX == 'yes') {
                                       if(BILL_TAX_TYPE == 'VAT')
                                       { ?>
                                    <tr>
                                       <td>
                                          <h3>Total Card Sale<br>مجموع بطاقة بيع</h3>
                                       </td>
                                       <td>
                                          <h3><?php echo CURRENCY.' '.number_format((float)$settle_sale['card_sale_without_vat'], 2, '.', ''); ?></h3>
                                       </td>
                                    </tr>
                                    <?php }
                                       elseif(BILL_TAX_TYPE == 'GST')
                                       { ?>
                                    <tr>
                                       <td>
                                          <h3>Total Card Sale</h3>
                                       </td>
                                       <td>
                                          <h3><?php echo CURRENCY.' '.number_format((float)$settle_sale['card_sale'], 2, '.', ''); ?></h3>
                                       </td>
                                    </tr>
                                    <?php } } ?> 
                                    <?php if(BILL_TAX == 'yes') { if(BILL_TAX_TYPE == 'VAT') { ?>
                                    <tr>
                                       <td>
                                          <h3>Total Card VAT<br>إجمالي ضريبة القيمة المضافة على البطاقة</h3>
                                       </td>
                                       <td>
                                          <h3><?php echo CURRENCY.' '.number_format((float)$settle_sale['card_sale_vat'], 2, '.', ''); ?></h3>
                                       </td>
                                    </tr>
                                    <?php 	} } ?>
                                    <?php if(BILL_TAX_TYPE == 'GST')
                                       { ?>
                                    <?php }  ?>
                                    <tr>
                                       <td>
                                          <h3>Final Total<br></h3>
                                       </td>
                                       <td>
                                          <h3><?php echo CURRENCY.' '.number_format((float)$settle_sale['cash_drawer'], 2, '.', ''); ?></h3>
                                       </td>
                                    </tr>
                                 </tbody>
                              </table>
                              <a href="settle_sale_print.php" style="width:100%;" type="submit" class="btn settle-submit">Print</a>
                           </div>
                           <div id="y-factor" class="tab-pane fade in table-responsive" >
                              <table class="table table-responsive">
                                 <tbody>
                                    <?php if(BILL_TAX == 'yes'){
                                       if(BILL_TAX_TYPE == 'VAT')
                                       { ?>
                                    <!-- <tr>
                                       <td>
                                          <h3>Total Cash Sale</h3>
                                       </td>
                                       <td>
                                          <h3><?php //echo CURRENCY.' '.number_format((float)$settle_sale['cash_sale_without_vat'], 2, '.', ''); ?></h3>
                                       </td>
                                    </tr> -->
                                    <?php }
                                       elseif(BILL_TAX_TYPE == 'GST')
                                       { ?>
                                    <tr>
                                       <td>
                                          <h3>Total Cash </h3>
                                       </td>
                                       <td>
                                          <h3><?php echo CURRENCY.' '.number_format((float)$settle_sale['cash_sale'], 2, '.', ''); ?></h3>
                                       </td>
                                    </tr>
                                    <?php	}
                                       } ?>
                                    <?php if(BILL_TAX == 'yes') { if(BILL_TAX_TYPE == 'VAT') { ?>
                                    <tr>
                                       <td>
                                          <h3>Total Cash VAT</h3>
                                       </td>
                                       <td>
                                          <h3><?php echo CURRENCY.' '.number_format((float)$settle_sale['cash_sale_vat'], 2, '.', ''); ?></h3>
                                       </td>
                                    </tr>
                                    <?php } } ?>
                                    <?php if(BILL_TAX == 'yes'){
                                       if(BILL_TAX_TYPE == 'VAT')
                                       { ?>
                                    <!-- <tr>
                                       <td>
                                          <h3>Total Card Sale<br>مجموع بطاقة بيع</h3>
                                       </td>
                                       <td>
                                          <h3><?php //echo CURRENCY.' '.number_format((float)$settle_sale['card_sale_without_vat'], 2, '.', ''); ?></h3>
                                       </td>
                                    </tr> -->
                                    <?php } } ?>
                                    <?php  if(BILL_TAX_TYPE == 'GST')
                                       { ?>
                                    <!-- <tr>
                                       <td>
                                          <h3>Total Card Sale</h3>
                                       </td>
                                       <td>
                                          <h3><?php //echo CURRENCY.' '.number_format((float)$settle_sale['card_sale'], 2, '.', ''); ?></h3>
                                       </td>
                                    </tr> -->
                                    <?php }  ?> 
                                    <?php if(BILL_TAX == 'yes') { if(BILL_TAX_TYPE == 'VAT') { ?>
                                    <tr>
                                       <td>
                                          <h3>Total Card VAT<br>إجمالي ضريبة القيمة المضافة على البطاقة</h3>
                                       </td>
                                       <td>
                                          <h3><?php echo CURRENCY.' '.number_format((float)$settle_sale['card_sale_vat'], 2, '.', ''); ?></h3>
                                       </td>
                                    </tr>
                                    <?php 	} }
                                       if(BILL_TAX_TYPE == 'GST')
                                       { ?>
                                    <!-- <tr>
                                       <td>
                                          <h3>Total GST</h3>
                                          <p>(Total Amount In SGST + CGST)</p>
                                          <p><?php //echo "Amount of GST = ".$settle_sale['total_cgst']." + ".$settle_sale['total_cgst']; ?></p>
                                       </td>
                                      <td>
                                          <h3><?php //echo CURRENCY.' '.number_format((float)$settle_sale['total_sgst']+$settle_sale['total_cgst'],2, '.', ''); ?></h3>
                                       </td>
                                    </tr> -->
                                    <?php }  ?>
                                    <tr>
                                       <td>
                                          <h3>Final Total<br></h3>
                                       </td>
                                       <td>
                                          <h3><?php echo CURRENCY.' '.number_format((float)$settle_sale['cash_drawer'], 2, '.', ''); ?></h3>
                                       </td>
                                    </tr>
                                 </tbody>
                              </table>
                              <a style="width:100%;" type="submit" class="btn set-bt settle-submit">Submit</a>
                           </div>
                        </div>
                     </div>
                  </div>
                  <script src="js/jquery-3.2.1.min.js"></script>
                  <script src="js/bootstrap.min.js"></script>
                  <script type="text/javascript">
                     $(".set-bt").click(function () {
                     	if(confirm('Are you sure you want to Settle?'))
                     	{
                     		window.location.href = 'settle_sale_print.php?set=yes';
                     	}
                     });
                  </script>
                  <?php include('script_common.php'); ?>
               </body>
            </html>