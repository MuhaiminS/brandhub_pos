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
   
   function getSaleOrderItemDetailsListItemWise($from_date = '', $to_date = '', $shop = '', $pageLimit = '', $setLimit = '', $export = "",$items ='')
   {
       
       $qry = "SELECT soi.item_id, soi.item_name, soi.price,soi.qty as qty,(SUM(soi.qty)*soi.price) as amount FROM sale_order_items as soi LEFT JOIN sale_orders as so ON (so.id = soi.sale_order_id) WHERE '1' ";
       
       if ($from_date != '' && $to_date != '') {
           $qry .= " AND so.ordered_date BETWEEN '$from_date 00:00:00' AND '$to_date 23:59:59' ";
       }
       if($items != ''){
		 $qry .= "  AND soi.item_id = '$items'";
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
	$items = (isset($_GET['item_id']) && $_GET['item_id'] !='') ? $_GET['item_id'] : ''; 

	function getItemList(){
		$item_list = array();
		$query="SELECT * FROM items ORDER BY id ASC";
		$run = mysqli_query($GLOBALS['conn'], $query);
		while($row = mysqli_fetch_array($run)) {
			$item_id = $row['id'];
			$item_list[$item_id]['item_id'] = $row['id'];
			$item_list[$item_id]['name'] = $row['name'];
		}
		return $item_list;	

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
                  <title>Report Itemwise</title>
                  <meta name="description" content="" />
                  <meta name="keywords" content="" />
                  <meta name="viewport" content="width=device-width, initial-scale=1">
                  <link rel="stylesheet" href="css/bootstrap.min.css">
                  <link rel="stylesheet" href="css/main.css">
                  <link rel="icon" type="image/png" sizes="32x32" href="img/favicon.ico">
                  <link rel="stylesheet" href="css/bootstrap.css">
                  <link href="css/jquery-ui.css" rel="stylesheet">
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
                                 <a href="index.php"><img src="img/out.png" alt="logo"></a>
                              </div>
                           </div>
                           <div class="col-sm-4">
                              <div class="counter-head">
                               Item Wise Report
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
                  <div class="sale-report-body">
                     <div class="container">
                        <form method="get">
                           <div class="sale-report-box">
                              <table class="table">
                                 <tbody>
                                    <tr style="border:none; border:0 !important;">
                                    	<td>
                                              <div class="col-sm-12">
                            <div class="form-group">
    									<select name="item_id" class="form-control">
                        <label>Service</label>
    										<option value="">  --Select Service--  </option>
    										<?php $item_list = getItemList();
    										foreach ($item_list as $item_lo)
    										{ 
    											?><option value="<?php echo $item_lo['item_id']; ?>" <?php echo ($items == $item_lo['item_id']) ? ' selected="selected"' : Null; ?> ><?php echo $item_lo['name']; ?></option><?php
    										}
    										?>
    									</select> 
                      </div>                    
                      </div>   									
    								</td>
                                       <td>From Date: <input name="from_date" class="from_date_pickr" id="from_date" value="<?php
                                          echo $from_date;
                                          ?>"/><img src="img/calender.png" class="from_date_pickr"></span>
                                          To Date: <input name="to_date" class="to_date_pickr" id="to_date" value="<?php
                                             echo $to_date;
                                             ?>" /><img src="img/calender.png" class="to_date_pickr"></span>
                                       </td>
                                       <td><button style="" class="btn btn-primary">FILTER</button></td>
                                       <td> <a href="item_wise_report.php" class="btn btn-info">Reset</a> </td>
                                       
                                       <div class="print1" style="float: right;
                                    color: #000ba6;
                                    font-size: 21px;">

                                    <input type="button" class="panel panel-warning print_me" value="Print" /></div>
                                    </tr>
                                    <a href="excel_export_itemwise.php?from_date=<?php echo $from_date; ?>&to_date=<?php echo $to_date; ?>" class="btn btn-warning"><span style="font-size: 12px;font-weight: bold; margin-right: 10px ">Click</span>Excel Export</a>
                                    <br>
                                 </tbody>
                              </table>
                           </div>
                        </form>
                        <div class="report-box">
                           <table class="table table-bordered bord">
                              <thead>
                                 <tr bordercolor="#f56212;">
                                    <th>S.No</th>
                                    <th>Service</th>
                                    <th style="text-align: center;">Qty</th>
                                    <th style="text-align: center;">Amount</th>
                                 </tr>
                              </thead>
                              <tbody>
                                 <?php                                    
                                    $prs = getSaleOrderItemDetailsListItemWise($from_date, $to_date, $shops, $pageLimit, $setLimit, $export = "",$items);
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
                                 ?>
                                   <tr>
                                      <td><?php echo $i = $i+1;?></td>
                                      <td><?php echo $itmsales['name']; ?></td>
                                      <td style="text-align: right;"><?php echo $itmsales['wgtnqty']; ?></td>
                                      <td style="text-align: right;"><?php echo number_format($itmsales['tot_amnt'],2);?></td>
                                   </tr>
                                 <?php
                                    $total_qty += $itmsales['wgtnqty'];
                                    $grand_total += $itmsales['tot_amnt'];
                                    }                                 
                                    } else {
                                    echo "<tr>";
                                    echo "<td>No items found to list.</td>";
                                    echo "</tr>";
                                    }
                                    ?>                                    
                              </tbody>
                              <tr>
                                 <td  style="font-weight: bold;" colspan="2">Total</td>
                                 <td style="font-weight: bold;text-align: right;"><?php echo $total_qty;?></td>
                                  <td style="text-align: right;font-weight: bold;"><?php echo number_format($grand_total,2);?> <?php echo CURRENCY; ?></td>
                              </tr>
                           </table>                               
                          
                        </div>
                     </div>
                  </div>
                  <div class="box-content no-padding" id="itemwise_print" style=" display:none;" >
            <h4 class="text-primary text-center"><center><?php echo CLIENT_NAME; ?></center></h4>
            <h4 class="text-primary text-center"><center>Service Wise Sales</center></h4>
            <h4 class="text-center"><?php echo date('d/m/Y',strtotime($from_date)); ?> TO <?php echo date('d/m/Y',strtotime($to_date)); ?></h4>
            <table width="100%" class="table table-striped table-bordered table-hover table-heading no-border-bottom" border="1px solid black">
              <thead>
                <tr>
                 <th>S.No</th>
                  <th>Service</th>
                  <th style="text-align: center;">Qty</th>
                  <th style="text-align: center;">Amount</th>
                </tr>
              </thead>
              <tbody style=" background: #fff none repeat scroll 0 0 !important;">
                <?php                                    
                        $prs = getSaleOrderItemDetailsListItemWise($from_date, $to_date, $shops, $pageLimit, $setLimit, $export = "",$items);
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
                                 ?>
               <tr>
                                      <td><?php echo $i = $i+1;?></td>
                                      <td><?php echo $itmsales['name']; ?></td>
                                      <td style="text-align: right;"><?php echo $itmsales['wgtnqty']; ?></td>
                                      <td style="text-align: right;"><?php echo number_format($itmsales['tot_amnt'],2);?></td>
                                   </tr>
                                    <?php
                                    $total_qty += $itmsales['wgtnqty'];
                                    $grand_total += $itmsales['tot_amnt'];
                                    }                                 
                                    } else {
                                    echo "<tr>";
                                    echo "<td>No items found to list.</td>";
                                    echo "</tr>";
                                    }
                                    ?> 
                  
                </tr>
              </tbody>
               <tr>
                                 <td style="font-weight: bold;" colspan="2">Total</td>
                                <td style="font-weight: bold;text-align: right;"><?php echo $total_qty;?></td>
                                <td style="text-align: right;font-weight: bold;"><?php echo number_format($grand_total,2);?> <?php echo CURRENCY; ?></td>
                              </tr>
            </table>
          </div>
                  <script src="js/jquery-3.2.1.min.js"></script> 
                  <script src="js/bootstrap.min.js"></script>
                  <script src="js/jquery-date.js"></script>
                  <script src="js/jquery-ui.js"></script>
                  <script type="text/javascript">
                     $( ".from_date_pickr" ).datepicker({dateFormat: 'yy-mm-dd'});
                     $( ".to_date_pickr" ).datepicker({dateFormat: 'yy-mm-dd'});
                     
                  </script>
                  <script type="text/javascript"> 

					$(document).on('click', '.print_me', function(e) {
						$(".hide_print").hide();
						var content = document.getElementById('itemwise_print').innerHTML;
						var win = window.open();	
						//win.document.write('<link href="css/style_v1.css" rel="stylesheet">');
						//win.document.write('<link href="core/framework/libs/pj/css/pj-table.css" rel="stylesheet" type="text/css" />');
						win.document.write('<style>table {border-collapse: collapse;} table, td, th {border: 1px solid black;}</style>');
						win.document.write(content);	
						win.print();
						win.window.close();
					});

                    
                     //export
                     $(".show_titles").hide();
                     function exportTableToCSV($table, filename) {
                            var $rows = $table.find('tr:has(th),tr:has(td)'),
                             // Temporary delimiter characters unlikely to be typed by keyboard
                             // This is to avoid accidentally splitting the actual contents
                             tmpColDelim = String.fromCharCode(11), // vertical tab character
                             tmpRowDelim = String.fromCharCode(0), // null character
                             // actual delimiter characters for CSV format
                             colDelim = '","',
                             rowDelim = '"\r\n"',
                     
                             // Grab text from table into CSV formatted string
                             csv = '"' + $rows.map(function (i, row) {
                              var $row = $(row),
                               $cols = $row.find('th,td');
                     
                              return $cols.map(function (j, col) {
                               var $col = $(col),
                                text = $col.text();
                     
                               return text.replace(/"/g, '""'); // escape double quotes
                     
                              }).get().join(tmpColDelim);
                     
                             }).get().join(tmpRowDelim)
                              .split(tmpRowDelim).join(rowDelim)
                              .split(tmpColDelim).join(colDelim) + '"',
                     
                             // Data URI
                             csvData = 'data:application/csv;charset=utf-8,' + encodeURIComponent(csv);
                     
                            $(this)
                             .attr({
                             'download': filename,
                              'href': csvData,
                              'target': '_blank'
                            });
                             $(".show_titles").hide();
                       }
                     
                       // This must be a hyperlink  
                         // $(".export").on('click', function (event) {
                        $(document).on('click', '.excel_me', function(event) {
                             $(".show_titles").show();
                             exportTableToCSV.apply(this, [$('#item_wise_repots>table'), 'export_settle_reports.csv']);        
                       });
                     
                  </script>
                  <?php
                     //include('common_script.php');
                     ?>
               </body>
            </html>