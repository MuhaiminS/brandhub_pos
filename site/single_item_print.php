<?php
   session_start();
   include("functions.php");
   include_once("config.php");
   include_once('barcode.php');
   connect_dre_db();
   //chkAdminLoggedIn();
   $server_url = getServerURL();
   $pay = '0';
   $sale_order_id = $_GET['id'];
   $redirect = $_GET['re'];
   if($redirect == 'counter_sale.php') {
   	redirect($redirect);
   }
   function getDriverName($driver_id)
   {
   	$where = "WHERE id = '$driver_id'";
   	$service = getnamewhere('drivers', 'name', $where);
   	return $service;
   }
   $pay = (isset($_GET['pay']) && $_GET['pay'] !='') ? $_GET['pay'] : '';
   $remarks1 = (isset($_GET['remark']) && $_GET['remark'] !='') ? $_GET['remark'] : '';
   $deliver = (isset($_GET['deliver']) && $_GET['deliver'] !='') ? $_GET['deliver'] : '';
   $combo = (isset($_GET['combo']) && $_GET['combo'] !='') ? $_GET['combo'] : '';
   	$result = mysqli_query($GLOBALS['conn'], "SELECT * FROM ".DB_PRIFIX."sale_orders WHERE id = $sale_order_id");  
   	$sale_insert =  mysqli_fetch_assoc($result);
   	$sale_order_id = $sale_insert['id'];
   	$remarks = $sale_insert['remarks'];
   	$discount = $sale_insert['discount'];
   	$amount_given = $sale_insert['amount_given'];
   	$balance = $sale_insert['balance_amount'];
   	$receipt_id = $sale_insert['receipt_id'];
   	$payment_type = $sale_insert['payment_type'];
   	$order_type = $sale_insert['order_type'];
   	$card_num = $sale_insert['card_num'];
   	$floor_no = $sale_insert['floor_id'];
   	$table_no = $sale_insert['table_id'];
   	$combo_package_name = $sale_insert['combo_package_name'];
   	$combo_package_price = $sale_insert['combo_package_price'];
   	$driver_name = getDriverName($sale_insert['driver_id']);
   	$order_type = $sale_insert['order_type'];
   	$ordered_date = date("d-m-Y", strtotime($sale_insert['ordered_date']));
   	$customer_id = $sale_insert['customer_id'];
   	$cus_details = getCustomerDetail($customer_id);
   	$result_arr = array();
   	$sql = "SELECT * FROM ".DB_PRIFIX."sale_order_items WHERE sale_order_id = $sale_order_id";
   	$result_val = mysqli_query($GLOBALS['conn'], $sql);
   	while ($row = mysqli_fetch_assoc($result_val)) {
   		$result_arr[] = $row;			
   	}
   
   	if($pay == 'given') {
   		$table_id = $sale_insert['table_id'];
   		$num_members = $sale_insert['num_members'];
   		$table_result=mysqli_query($GLOBALS['conn'], "SELECT * from ".DB_PRIFIX."table_management where table_id=$table_id");
   				
   		if($row=mysqli_fetch_assoc($table_result)){
   			$filled_seats=$row['filled_seats'];
   
   			if(isset($num_members)){
   				$seats=$filled_seats-$num_members;			
   				mysqli_query($GLOBALS['conn'], "UPDATE ".DB_PRIFIX."table_management set filled_seats=$seats WHERE table_id=$table_id");
   			}
   		}
   	}
   	//echo "<pre>"; print_r($result_arr);die;
   	//Barcode Print
   	$img			=	code128BarCode($receipt_id, 1);
   	ob_start();
   	imagepng($img);	
   	//Get the image from the output buffer	
   	$output_img		=	ob_get_clean();
   ?>
<style>
   @media print {
   body {font-family: cursive;}
   #wrapper_pr {width: 100%; margin:0 auto; text-align:center; color:#000; font-family: cursive; font-size:12px;}
   .bdd{border-top: 1px solid #000;}	
   }
</style>
<?php $style_print = "font-family: cursive"; ?>
<div id="wrapper_pr">
   <p style="font-size:10px; text-align:left;line-height: 0.5em;$style_print"><?php echo '<span>'.$receipt_id.'</span>'; ?><span style="float:right;"><?php echo date("Y-m-d H:i:s"); ?><span></p>
   <div style="text-align: center;"><img style="text-align: center;" src="data:image/png;base64,<?php echo CLIENT_LOGO; ?>"></div>
   <h2 style="text-transform:uppercase;font-size:13px; text-align:center;line-height: 0.5em;$style_print"><strong><?php echo CLIENT_NAME; ?></strong></h2>
   <p style="font-size:12px; text-align:center;line-height: 0.5em;$style_print"><?php echo CLIENT_ADDRESS; ?></p>
   <p style="font-size:12px; text-align:center;line-height: 0.5em;$style_print"><?php echo CLIENT_NUMBER; ?></p>
   <p style="font-size:12px; text-align:center;line-height: 0.5em;$style_print"><?php echo CLIENT_WEBSITE; ?></p>
   <!-- <p style="font-size:12px; text-align:left;line-height: 0.5em;$style_print">Order type: <strong><?php //echo ucfirst(str_replace("_", ' ', $order_type)); ?></strong></p> -->
   <?php if($order_type == 'delivery' && $payment_type != '') { ?>
   <p style="font-size:12px; text-align:left;line-height: 0.5em;$style_print">Payment menthod: <strong><?php echo $payment_type; ?></strong></p>
   <?php } if($remarks1 == 'yes') {?>
   <p style="font-size:12px; text-align:left;line-height: 0.5em;$style_print">Remarks: <strong><?php echo $remarks; ?></strong></p>
   <?php } ?>
   <?php if($card_num > 0) {?>
   <p style="font-size:12px; text-align:left;line-height: 0.5em;$style_print">Card number: <strong><?php echo $card_num; ?></strong></p>
   <?php } ?>
   <?php if($order_type == 'dine_in') { ?>
   <p style="font-size:12px; text-align:left;line-height: 0.5em;$style_print">Floor no: <strong><?php echo $floor_no; ?></strong>&nbsp;&nbsp;Table no: <strong><?php echo $table_no; ?></strong></p>
   <?php } ?>
   <?php if($combo == 'yes') {?>
   <p style="font-size:12px; text-align:left;line-height: 0.5em;$style_print">Customer name:<strong><?php echo $cus_details['customer_name']; ?></strong></p>
   <p style="font-size:12px; text-align:left;line-height: 0.5em;$style_print">Customer number:<strong><?php echo $cus_details['customer_number']; ?></strong></p>
   <p style="font-size:12px; text-align:left;line-height: 0.5em;$style_print">Customer address:<strong><?php echo $cus_details['customer_address']; ?></strong></p>
   <?php } ?>
<h2 style="text-align:center;">TAX INVOICE</h2>   
   <?php if($order_type != 'combo') { ?>
   <table class="table" cellspacing="0" width="100%" border="0">
      <thead>
         <?php 
            echo "<tr><td colspan='4'><div style='border-top:1px solid #000;padding:5px 0px;'></div></td></tr>";
            ?>      
         <tr>
            <th style="font-size:12px;text-align:left; width:60%;$style_print">Items</th>
            <th style="font-size:12px;text-align:right; width:10%;$style_print">Price</th>
            <th style="font-size:12px;text-align:right; width:10%;$style_print">Qty</th>
            <th style="font-size:12px;text-align:right; width:15%;$style_print">Price</th>
         </tr>
      </thead>
      <tbody id="bg_val">
         <?php
            echo "<tr><td colspan='4'><div style='border-top:1px solid #000;padding:5px 0px;'></div></td></tr>";
               $total_amount = $tax_pecr = '0';
            	$gst_group = array();
            	//$sgst_group = array();
            	//echo '<pre>';print_r($result_arr);echo '</pre>';
            	foreach($result_arr as $key => $res){	
            		echo "<tr>";
            		echo "<td style='font-size:11px;float:left; width:80%;$style_print'>".$res['item_name']."</td>";
            echo "<td style='font-size:11px;text-align:right; width:12%;$style_print'>".number_format((float)($res['price']), 2)."</td>";		
            echo "<td style='font-size:11px;text-align:center; width:12%;$style_print'>".$res['qty']. "</td>";
            		echo "<td style='font-size:11px;text-align:right; width:8%;$style_print'>".number_format((float)$res['price']*$res['qty'], 2, '.', '')."</td>";
            		echo "</tr>";
            		$multiplle_val=$res['price']*$res['qty'];
            		$total_amount+=$multiplle_val;
            		//echo $key.'<br>';
            		$item_price_single = $res['tax_without_price'];
            		if (!isset($gst_group[$res['CGST']]['cgst'])) {
            			$gst_group[$res['CGST']]['cgst'] = ($item_price_single / 100) * ($res['CGST']) * $res['qty'];
            			$gst_group[$res['SGST']]['sgst'] = ($item_price_single / 100) * ($res['SGST']) * $res['qty'];
            		}
            		else {
            			$gst_group[$res['CGST']]['cgst'] = $gst_group[$res['CGST']]['cgst'] + ($item_price_single / 100) * ($res['CGST']) * $res['qty'];
            			$gst_group[$res['SGST']]['sgst'] = $gst_group[$res['SGST']]['sgst'] + ($item_price_single / 100) * ($res['SGST']) * $res['qty'];
            		}
            	}
            
            	
            
            	//echo '<pre>';print_r($gst_group);echo '</pre>';
            echo "<tr><td colspan='4'><div style='border-top:1px solid #000;padding:5px 0px;'></div></td></tr>";
            echo "<tr><td colspan='2' style='font-size:12px; width:45%;$style_print'>Sub Total:</td><td colspan='2' style='font-size:12px;text-align: right; width:60%;$style_print'> ".number_format((float)$total_amount, 2, '.', '')."</td></tr>";
            echo "<tr><td colspan='2' style='font-size:12px; width:45%;$style_print'>Discount :</td><td colspan='2' style='text-align: right;font-size:12px; width:60%;$style_print'> ".number_format((float)($discount), 2, '.', '')."</td></tr>";
            
            if(BILL_TAX == 'yes') {
            	if(BILL_COUNTRY == 'UAE') {
            		$tax_pecr = ($total_amount / 100) * (BILL_TAX_VAL);
            		echo "<tr><td colspan='2' style='font-size:12px; width:100%;$style_print'> VAT (".BILL_TAX_VAL."%):</td><td colspan='2' style='text-align: right;font-size:12px; width:100%;$style_print'> ".number_format((float)($tax_pecr), 2, '.', '')."</td></tr>";
            	}
            }
            echo "<tr><td colspan='4'><div style='border-top:1px solid #000;padding:5px 0px;'></div></td></tr>";
            echo "<tr><td colspan='2' style='font-weight: bold;font-size:16px;width:100%;$style_print'>Grand Total :</td><td colspan='2' style='font-weight: bold;font-size:16px;text-align: right; width:100%;$style_print'> ".number_format((float)($total_amount - $discount + $tax_pecr), 2, '.', '')."</td></tr>";
            
            if($pay == 'given') {
            	echo "<tr><td colspan='4'><div style='border-top:1px solid #000;padding:5px 0px;'></div></td></tr>";
            	echo "<tr><td style='font-size:12px;float:left; width:50%;$style_print'>Amount given :</td><td colspan='2' style='text-align: right;font-size:12px; width:50%;$style_print';> ".number_format((float)($amount_given), 2, '.', '')."</td></tr>";
            	echo "<tr><td style='font-size:12px;float:left; width:40%;$style_print'>Balance :</td><td colspan='2' style='font-size:12px;text-align: right; width:60%;$style_print'> ".number_format((float)($amount_given - ($total_amount - $discount + $tax_pecr)), 2, '.', '')."</td></tr>";
            }
            if(BILL_TAX == 'yes') {
            	if(BILL_COUNTRY != 'UAE') {
            		echo "<tr><td colspan='3'><div style='border-top:1px solid #000;padding:5px 0px;'></div></td></tr>";
            		foreach($gst_group as $key => $gst) {
            		echo "<tr><td colspan='3'style='font-size:12px;text-align: right;width:100% !important;$style_print'> CGST (".$key."%): ".number_format((float)$gst['cgst'], 2, '.', '')."&nbsp;&nbsp;&nbsp; SGST (".$key."%): ".number_format((float)$gst['sgst'], 2, '.', '')."</td></tr>";
            		}
            	}
            }
            ?>
      </tbody>
   </table>
   <?php } else { ?>
   <table class="table" cellspacing="0" border="0">
      <thead>
         <?php 
            echo "<tr><td colspan='2'><div style='border-top:1px solid #000;padding:5px 0px;'></div></td></tr>";
            ?>      
         <tr>
            <th style="font-size:12px;text-align:left; width:85%;$style_print">Items</th>
            <th style="font-size:12px;text-align:right; width:15%;$style_print">Price</th>
         </tr>
      </thead>
      <tbody id="bg_val">
         <?php
            echo "<tr><td colspan='2'><div style='border-top:1px solid #000;padding:5px 0px;'></div></td></tr>";
               $total_amount = $tax_pecr = '0';
            	$gst_group = array();
            	echo "<tr>";
            		echo "<td style='font-size:11px;float:left; width:85%;$style_print'>".$combo_package_name."</td>";
            		echo "<td style='font-size:11px;text-align:right; width:15%;$style_print'>".$combo_package_price."</td>";
            	echo "</tr>";
            	foreach($result_arr as $key => $res){	
            		echo "<tr>"; ?>
         <td colspan='2' style='font-size:11px;float:left; width:100%;$style_print'>&nbsp;- <?php echo $res['item_name']; ?>&nbsp;&nbsp;(<?php if($res['date_completed']) {echo date("d-m-Y", strtotime($sale_insert['ordered_date']));} else { echo "Pending"; } ?>)</td>
         <?php echo "</tr>"; }
            //echo '<pre>';print_r($gst_group);echo '</pre>';
            echo "<tr><td colspan='2'><div style='border-top:1px solid #000;padding:5px 0px;'></div></td></tr>";
            echo "<tr><td colspan='1' style='font-size:12px; width:45%;$style_print'>Sub Total:</td><td colspan='1' style='font-size:12px;text-align: right; width:60%;$style_print'> ".number_format((float)$combo_package_price, 2, '.', '')."</td></tr>";
            
            if(BILL_TAX == 'yes') {
            if(BILL_COUNTRY == 'UAE') {
            	$tax_pecr = ($combo_package_price / 100) * (BILL_TAX_VAL);
            	echo "<tr><td colspan='1' style='font-size:12px; width:100%;$style_print'> VAT (".BILL_TAX_VAL."%):</td><td colspan='1' style='text-align: right;font-size:12px; width:100%;$style_print'> ".number_format((float)($tax_pecr), 2, '.', '')."</td></tr>";
            }
            }
            echo "<tr><td colspan='2'><div style='border-top:1px solid #000;padding:5px 0px;'></div></td></tr>";
            echo "<tr><td colspan='1' style='font-weight: bold;font-size:16px;width:100%;$style_print'>Grand Total :</td><td colspan='1' style='font-weight: bold;font-size:16px;text-align: right; width:100%;$style_print'> ".number_format((float)($combo_package_price - $discount + $tax_pecr), 2, '.', '')."</td></tr>";
            
            if($pay == 'given') {
            echo "<tr><td colspan='2'><div style='border-top:1px solid #000;padding:5px 0px;'></div></td></tr>";
            echo "<tr><td style='font-size:12px;float:left; width:50%;$style_print'>Amount given :</td><td colspan='1' style='text-align: right;font-size:12px; width:50%;$style_print';> ".number_format((float)($amount_given), 2, '.', '')."</td></tr>";
            echo "<tr><td style='font-size:12px;float:left; width:40%;$style_print'>Balance :</td><td colspan='1' style='font-size:12px;text-align: right; width:60%;$style_print'> ".number_format((float)($amount_given - ($total_amount - $discount + $tax_pecr)), 2, '.', '')."</td></tr>";
            }
            ?>
      </tbody>
   </table>
   <?php } ?>
   <div style="border-top:1px solid #000;">
      <p style="font-size:12px;text-align:center;$style_print"><?php echo BILL_FOOTER; ?></p>
      <p style="font-size:12px;text-align:center;"><?php //echo '<img src="data:image/png;base64,' . base64_encode($output_img) . '" />'; ?></p>
      <?php if($order_type != 'combo') { ?>
      <p style="font-size:12px; text-align:center;line-height: 0.5em;$style_print">Staff: <?php echo $driver_name; ?></p>
      <?php } ?>
   </div>
</div>
<script type="text/javascript">
   var content = document.getElementById('wrapper_pr').innerHTML;
   var win = window.open();				
   win.document.write(content);	
   win.print(content);
   win.window.close();
</script>
<?php redirect($redirect); ?>