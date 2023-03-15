<?php
session_start();
include("functions.php");
include_once("config.php");
include_once('barcode.php');
connect_dre_db();
//chkAdminLoggedIn();
mysqli_set_charset($GLOBALS['conn'], 'UTF8');
$server_url = getServerURL();
$pay = '';
$sale_order_id = $_GET['id'];
$redirect = $_GET['re'];
if($redirect == 'counter_sale.php') {
   //redirect($redirect);
}
 
$pay = (isset($_GET['pay']) && $_GET['pay'] !='') ? $_GET['pay'] : '';
$remarks1 = (isset($_GET['remark']) && $_GET['remark'] !='') ? $_GET['remark'] : '';
$deliver = (isset($_GET['deliver']) && $_GET['deliver'] !='') ? $_GET['deliver'] : '';
$combo = (isset($_GET['combo']) && $_GET['combo'] !='') ? $_GET['combo'] : '';
   $result = mysqli_query($GLOBALS['conn'], "SELECT * FROM ".DB_PRIFIX."sale_orders WHERE id = $sale_order_id");  
   $sale_insert =  mysqli_fetch_assoc($result);
   $sale_order_id = $sale_insert['id'];
   $driver_id = $sale_insert['driver_id'];
   $remarks = $sale_insert['remarks'];
   $discount = $sale_insert['discount'];
   $amount_given = $sale_insert['amount_given'];
   $balance = $sale_insert['balance_amount'];
   // $drop_of_date = date("d-m-Y H:i:s", strtotime($sale_insert['drop_of_date']));
   $receipt_id = $sale_insert['receipt_id'];
   $payment_type = $sale_insert['payment_type'];
   $order_type = $sale_insert['order_type'];
   $card_num = $sale_insert['card_num'];
   $floor_no = $sale_insert['floor_id'];
   $table_no = $sale_insert['table_id'];
   $combo_package_name = $sale_insert['combo_package_name'];
   $combo_package_price = $sale_insert['combo_package_price'];
   $combo_package_gst = $sale_insert['combo_package_gst'];
   $order_type = $sale_insert['order_type'];
   $driver_name = getDriverName($sale_insert['driver_id']);
   if($order_type == 'counter_sale') {
      $order_type = 'Counter';
   }
   $ordered_date = date("d-m-Y H:i:s", strtotime($sale_insert['ordered_date']));
   $contact_name=$sale_insert['contact_name'];
   $contact_number=$sale_insert['contact_number'];
   $address=$sale_insert['address'];
   //$customer_id = $sale_insert['customer_id'];
   //$cus_details = getCustomerDetail($customer_id);
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
   //echo "<pre>"; print_r($sale_insert);die;
   //Barcode Print
   $img        =  code128BarCode($receipt_id, 1);
   ob_start();
   imagepng($img);   
   //Get the image from the output buffer 
   $output_img    =  ob_get_clean();
   
   function getDriverName($driver_id)
    {
      $where = "WHERE id = '$driver_id'";
      $service = getnamewhere('drivers', 'name', $where);
      return $service;
    }
   
   $gst_result = array();
   $per_gst_res = array();

if(BILL_TAX == 'yes') {
   if(BILL_TAX_TYPE == 'GST') {
      foreach ($result_arr as $element) {
          $gst_result[$element['CGST']][] = $element;
      }

   
      foreach ($gst_result as $key => $value) 
      {
         $per_gst_res[$key]['price'] = $per_gst_res[$key]['CGST'] = $per_gst_res[$key]['SGST'] = 0;
         for($is=0;$is<count($gst_result[$key]);$is++)
         {
         $per_gst_res[$key]['price'] += ($value[$is]['price']*$value[$is]['qty']);
         // $per_gst_res[$key]['CGST'] += (($value[$is]['price']*$value[$is]['qty'])*$key)/100;
         // $per_gst_res[$key]['SGST'] += (($value[$is]['price']*$value[$is]['qty'])*$key)/100;

         $per_gst_res[$key]['CGST'] += ($value[$is]['price']*$value[$is]['qty']) - (($value[$is]['price']*$value[$is]['qty'])*100)/($key+100);
         $per_gst_res[$key]['SGST'] += ($value[$is]['price']*$value[$is]['qty']) - (($value[$is]['price']*$value[$is]['qty'])*100)/($key+100);
         }
      }

   }
}

$total_amount_net = $total_amount_cgst = $total_amount_sgst = 0;
foreach ($per_gst_res as $key => $value) 
   {
      $total_amount_net += $value['price'];
      $total_amount_cgst += $value['CGST'];
      $total_amount_sgst += $value['SGST'];
   }

?>
<style>
@media print {
   body {font-family: Arial;}
   #wrapper_pr {width: 100%; margin:0 auto; text-align:center; color:#000; font-family: Arial; font-size:12px;}
   .bdd{border-top: 1px solid #000;}   
}
</style>
<?php $style_print = "font-family: Arial"; ?>
<meta charset="UTF-8" />
<div id="wrapper_pr">
<div style="margin-top:50px;"></div>
<div style="text-align: center;"><img style="text-align: center;" src="data:image/png;base64,<?php echo CLIENT_LOGO; ?>"></div>
<h2 style="text-transform:uppercase;font-size:13px; text-align:center;line-height: 0.5em;$style_print"><strong><?php echo CLIENT_NAME; ?></strong></h2>
<p style="font-size:12px;font-weight: bold; text-align:center;line-height: 0.5em;$style_print"><?php echo CLIENT_ADDRESS; ?></p>
<p style="font-size:12px; font-weight: bold;text-align:center;line-height: 0.5em;$style_print">Tel: <?php echo CLIENT_NUMBER; ?></p>
<!-- <p style="font-size:13px; font-weight: bold;text-align:center;line-height: 0.5em;$style_print">TAX INVOICE(WASHING)</p> -->
<p style="font-size:13px; font-weight: bold;text-align:center;line-height: 0.5em;$style_print"><?php echo CLIENT_WEBSITE; ?></p>
<?php if($deliver == 'yes') {?>
<?php if($contact_name !== '') {?>
<p style="font-size:12px; text-align:left;line-height: 1em;$style_print">Customer Name:<strong><?php echo $contact_name; ?></strong></p>
<?php } ?>
<?php if($contact_number !== '') {?>
<p style="font-size:12px; text-align:left;line-height: 0.5em;$style_print">Contact Number:<strong><?php echo $contact_number; ?></strong></p>
<?php } ?>
<?php if($address !== '') {?>
<p style="font-size:12px; text-align:left;line-height: 0.5em;$style_print">Address:<strong><?php echo $address; ?></strong></p>
<?php } ?>
     
<?php } ?>
<p style="font-size:12px; text-align:left;line-height: 0.5em;$style_print">Date: <span><?php echo $ordered_date; ?><span></p>
<p style="font-size:12px; text-align:left;line-height: 0.5em;$style_print">Receipt No: <?php echo $receipt_id; ?></p>
<!-- <p style="font-size:12px; text-align:left;line-height: 0.5em;$style_print">Order type: <strong><?php echo ucfirst(str_replace("_", ' ', $order_type)); ?></strong></p> -->
<?php if($order_type != 'delivery' && $payment_type != '') { ?>
<!-- <p style="font-size:12px; text-align:left;line-height: 0.5em;$style_print">Payment method: <strong><?php //echo $payment_type; ?></strong></p> -->
<?php } if($remarks1 == 'yes') {?>
<p style="font-size:12px; text-align:left;line-height: 0.5em;$style_print">Remarks: <strong><?php echo $remarks; ?></strong></p>
<?php } ?>
<?php if($card_num > 0) {?>
<p style="font-size:12px; text-align:left;line-height: 0.5em;$style_print">Card number: <strong><?php echo $card_num; ?></strong></p>
<?php } ?>
<?php if($order_type == 'dine_in') { ?>
<p style="font-size:12px; text-align:left;line-height: 0.5em;$style_print">Floor no: <strong><?php echo $floor_no; ?></strong>&nbsp;&nbsp;Table no: <strong><?php echo $table_no; ?></strong></p>
<?php } ?>
<!-- <h1 style="text-align:center; font-size:22px;">TAX INVOICE</h1> -->
<div style="clear:both;"></div></hr>
  <?php if($order_type != 'combo') { ?>
<table class="table" cellspacing="0" border="0" width="100%">
   <thead>
      <tr>
         <th style="font-size:12px;text-align:left; width:5%;$style_print">S.No</th>
         <th style="font-size:12px;text-align:center; width:40%;$style_print">Items</th>
         <th style="font-size:12px;text-align:center; width:10%;$style_print">Qty</th>
         <th style="font-size:12px;text-align:right; width:15%;$style_print">U.Price</th>
         <!-- <th style="font-size:12px;text-align:right; width:15%;$style_print">Tax</th> -->
         <th style="font-size:12px;text-align:right; width:15%;$style_print">Amount</th>
      </tr>
   </thead>
   <tbody id="bg_val">
   <?php
         $i=0;
   $total_amount = $tax_pecr = '0';
   $gst_group = array();
   //$sgst_group = array();
   //echo '<pre>';print_r($result_arr);echo '</pre>';
   foreach($result_arr as $key => $res){
      // $other_item = $res['other_item_name'];
      //$other_item = iconv('utf-8', 'us-ascii//TRANSLIT', $other_item);
      $i++;
      $tax = ($res['price']*$res['qty'] / 100) * (BILL_TAX_VAL);
      echo "<tr>";
      echo "<td style='font-size:12px;$style_print'>".$i."</td>";
      echo "<td style='font-size:12px;$style_print'>".$res['item_name']."</td>";
      echo "<td style='font-size:12px;text-align:center; $style_print'>".$res['qty']."</td>";
      echo "<td style='font-size:12px;text-align:right; $style_print'>".number_format((float)($res['price']), 2, '.', '')."</td>";
      // echo "<td style='font-size:12px;text-align:right; $style_print'>".number_format((float)$tax, 2, '.', '')."</td>";
      echo "<td style='font-size:12px;text-align:right; $style_print'>".number_format((float)$res['price']*$res['qty'], 2, '.', '')."</td>";
      echo "</tr>";
      //Notes Row
   /*   if($res['notes'] != '') {
         echo "<tr>";
         echo "<td style='font-size:12px;text-align:right; width:20%;$style_print'></td>";
         // echo "<td style='font-size:12px; width:80%;$style_print'>Notes: ".$res['notes']."</td>";
         echo "<td colspan='3' style='font-size:12px;text-align:right; width:20%;$style_print'></td>";
         echo "</tr>";
      }  */      

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
echo "<tr><td colspan='5'><div style='border-top:1px solid #000;padding:5px 0px;'></div></td></tr>";
echo "<tr>
      <td colspan='3'  style='font-size:12px;font-weight:bold;$style_print'>Sub Total:</td>
      <td colspan='4' style='font-size:12px;text-align: right; $style_print'> ".number_format((float)$total_amount-$total_amount_cgst
-$total_amount_sgst, 2, '.', '')."</td>
   </tr>";
// echo "<tr>
//       <td colspan='3' style='font-size:12px;font-weight:bold;$style_print'>Discount:</td>
//       <td colspan='4' style='text-align: right;font-size:12px; $style_print'> ".number_format((float)($discount), 2, '.', '')."</td>
//    </tr>";

if(BILL_TAX == 'yes') {
   if(BILL_TAX_TYPE == 'VAT') {
      $tax_pecr = (($total_amount - $discount) / 100) * (BILL_TAX_VAL);
      echo "<tr>
            <td colspan='3' style='font-size:12px;font-weight:bold; width:40%;$style_print'> VAT (".BILL_TAX_VAL."%):</td>
            <td colspan='4' style='text-align: right;font-size:12px; width:60%;$style_print'> ".number_format((float)($tax_pecr), 2, '.', '')."</td>
         </tr>";
   }
   elseif (BILL_TAX_TYPE == 'GST') 
   {
      $tax_pecr = 0;
      //$tax_pecr = (($total_amount - $discount) / 100) * (BILL_TAX_VAL);
      echo "<tr>
            <td colspan='3' style='font-size:12px;font-weight:bold; width:40%;$style_print'> CGST:</td>
            <td colspan='4' style='text-align: right;font-size:12px; width:60%;$style_print'> ".number_format((float)($total_amount_cgst), 2, '.', '')."</td>
         </tr>";

      echo "<tr>
            <td colspan='3' style='font-size:12px;font-weight:bold; width:40%;$style_print'> SGST:</td>
            <td colspan='4' style='text-align: right;font-size:12px; width:60%;$style_print'> ".number_format((float)($total_amount_sgst), 2, '.', '')."</td>
         </tr><tr></tr>";
      echo "<tr><td colspan='7'>
      <table style='width: 100%;'>
      <tr>
      <td colspan=5 style='font-size:13px; font-weight: bold;text-align:center;line-height: 0.5em;$style_print'>GST BREAKUP DETAILS</td>
      </tr>
      <tr>
      <th style='font-size:12px;text-align:left; width:20%;$style_print'>GST%</th>
      <th style='font-size:12px;text-align:left; width:20%;$style_print'>Amount</th>
      <th style='font-size:12px;text-align:left; width:20%;$style_print'>SGST</th>
      <th style='font-size:12px;text-align:left; width:20%;$style_print'>CGST</th>
      <th style='font-size:12px;text-align:left; width:20%;$style_print'>Total</th></tr>";
      echo "<tr><td colspan='7'><div style='border-top:1px solid #000;padding:2px 0px;'></div></td></tr>";
         $total_amount_net = $total_amount_sub = $total_amount_cgst = $total_amount_sgst = 0;
               foreach ($per_gst_res as $key => $value) 
               {
                  echo "<tr>
                  <td style='font-size:12px;text-align:left; width:20%;$style_print'>".($key*2)."%</td>
                  <td style='font-size:12px;text-align:left; width:20%;$style_print'>".number_format((float)($value['price']-$value['CGST']-$value['SGST']), 2, '.', '')."</td>
                  <td style='font-size:12px;text-align:left; width:20%;$style_print'>".number_format((float)($value['SGST']), 2, '.', '')."</td>
                  <td style='font-size:12px;text-align:left; width:20%;$style_print'>".number_format((float)($value['CGST']), 2, '.', '')."</td>
                  <td style='font-size:12px;text-align:left; width:20%;$style_print'>".number_format((float)($value['price']), 2, '.', '')."</td></tr>";
               /*$printer -> text(str_pad(($key*2)." %", 6, ' ', STR_PAD_RIGHT).str_pad(number_format((float)($value['price']-$value['CGST']-$value['SGST']), 2, '.', ''), 12, ' ', STR_PAD_BOTH).str_pad(number_format((float)($value['SGST']), 2, '.', ''), 10, ' ', STR_PAD_BOTH).str_pad(number_format((float)($value['CGST']), 2, '.', ''), 10, ' ', STR_PAD_BOTH).str_pad(number_format((float)($value['price']), 2, '.', ''), 10, ' ', STR_PAD_LEFT));*/
               $total_amount_net += $value['price'];
               $total_amount_sub += $value['price']-$value['CGST']-$value['SGST'];
               $total_amount_cgst += $value['CGST'];
               $total_amount_sgst += $value['SGST'];
               }
               echo "<tr><td colspan='6'><div style='border-top:1px solid #000;padding:2px 0px;'></div></td></tr>";
               echo "<tr>
                  <td style='font-size:12px;text-align:left; width:20%;$style_print'>Total</td>
                  <td style='font-size:12px;text-align:left; width:20%;$style_print'>".number_format((float)($total_amount_sub), 2, '.', '')."</td>
                  <td style='font-size:12px;text-align:left; width:20%;$style_print'>".number_format((float)($total_amount_sgst), 2, '.', '')."</td>
                  <td style='font-size:12px;text-align:left; width:20%;$style_print'>".number_format((float)($total_amount_cgst), 2, '.', '')."</td>
                  <td style='font-size:12px;text-align:left; width:20%;$style_print'>".number_format((float)($total_amount_net), 2, '.', '')."</td></tr>";
                  echo "<tr><td colspan='6'><div style='border-top:1px solid #000;padding:2px 0px;'></div></td></tr>";
      echo "</table></td></tr><tr></tr>";
   }
}

echo "<tr>
      <td colspan='3' style='font-size:15px; font-weight:bold;width:70%;$style_print'>Grand Total:</td>
      <td colspan='4' style='font-size:15px;font-weight:bold;text-align: right; width:30%;$style_print'> ".number_format((float)($total_amount - $discount + $tax_pecr), 2, '.', '')."</td>
   </tr>";


if($pay == 'given' && $payment_type != 'credit') {
   echo "<tr><td colspan='5'><div style='border-top:1px solid #000;padding:5px 0px;'></div></td></tr>";
   echo "<tr><td style='font-size:12px; width:60%;$style_print'>Amount given:</td><td colspan='2' style='text-align: right;font-size:12px; width:40%;$style_print';> ".number_format((float)($amount_given), 2, '.', '')."</td></tr>";
   echo "<tr><td style='font-size:12px;width:40%;$style_print'>Balance:</td><td colspan='2' style='font-size:12px;text-align: right; width:60%;$style_print'> ".number_format((float)($amount_given - ($total_amount - $discount + $tax_pecr)), 2, '.', '')."</td></tr>";

}
if(BILL_TAX == 'yes') {
   if(BILL_COUNTRY != 'UAE') {
      echo "<tr><td colspan='5'><div style='border-top:1px solid #000;padding:5px 0px;'></div></td></tr>";
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
               $total_amount_cgst = $combo_package_gst/2;
               $total_amount_sgst = $combo_package_gst/2;

               foreach($result_arr as $key => $res){  
                  echo "<tr>"; ?>
         <td colspan='2' style='font-size:11px;float:left; width:100%;$style_print'>&nbsp;- <?php echo $res['item_name']; ?>&nbsp;&nbsp;(<?php if($res['date_completed']) {echo date("d-m-Y", strtotime($sale_insert['ordered_date']));} else { echo "Pending"; } ?>)</td>
         <?php echo "</tr>"; }
            //echo '<pre>';print_r($gst_group);echo '</pre>';
            echo "<tr><td colspan='2'><div style='border-top:1px solid #000;padding:5px 0px;'></div></td></tr>";
            echo "<tr><td colspan='1' style='font-size:12px; width:45%;$style_print'>Sub Total:</td><td colspan='1' style='font-size:12px;text-align: right; width:60%;$style_print'> ".number_format((float)$combo_package_price, 2, '.', '')."</td></tr>";
            
            if(BILL_TAX == 'yes') {
            if(BILL_TAX_TYPE == 'vat') {
               $tax_pecr = ($combo_package_price / 100) * (BILL_TAX_VAL);
               echo "<tr><td colspan='1' style='font-size:12px; width:100%;$style_print'> VAT (".BILL_TAX_VAL."%):</td><td colspan='1' style='text-align: right;font-size:12px; width:100%;$style_print'> ".number_format((float)($tax_pecr), 2, '.', '')."</td></tr>";
            }
             elseif (BILL_TAX_TYPE == 'GST') 
   {
            
             
      $tax_pecr = 0;
      //$tax_pecr = (($total_amount - $discount) / 100) * (BILL_TAX_VAL);
      echo "<tr>
            <td colspan='1' style='font-size:12px;font-weight:bold; width:40%;$style_print'> CGST:</td>
            <td colspan='1' style='text-align: right;font-size:12px; width:60%;$style_print'> ".number_format((float)($total_amount_cgst), 2, '.', '')."</td>

         </tr>";

      echo "<tr>
            <td colspan='1' style='font-size:12px;font-weight:bold; width:40%;$style_print'> SGST:</td>
            <td colspan='1' style='text-align: right;font-size:12px; width:60%;$style_print'> ".number_format((float)($total_amount_sgst), 2, '.', '')."</td>
         </tr><tr></tr>";
      
   }
}



            echo "<tr><td colspan='2'><div style='border-top:1px solid #000;padding:5px 0px;'></div></td></tr>";
            echo "<tr><td colspan='1' style='font-weight: bold;font-size:16px;width:100%;$style_print'>Grand Total :</td><td colspan='1' style='font-weight: bold;font-size:16px;text-align: right; width:100%;$style_print'> ".number_format((float)($combo_package_price - $discount + $combo_package_gst), 2, '.', '')."</td></tr>";
            
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
<!--    <p style="font-size:12px;text-align:left;">
   1. We shall not be responsible for any change of colour or shrinkage of garments.<br/>
   2. Compensation for damaged garments shall be limited to a maximum of 5 times of cleaning charge.<br/>
   3. Delivery is not taken within 2 months, no claim will be entertained.<br/>
   4. We shall not be responsible for valuable left in the pockets of your garments.
</p> -->
</div>
<div style="border-top:1px">
   <p style="font-size:12px;text-align:center;"><?php echo BILL_FOOTER; ?></p>
   
   <!--<p style="font-size:12px;text-align:center;"><?php //echo '<img src="data:image/png;base64,' . base64_encode($output_img) . '" />'; ?></p>
   <p style="font-size:12px;text-align:center;font-weight:bold;"><?php //echo '<span>'.$receipt_id.'</span>'; ?></p>-->
    <!-- <?php //if($order_type != 'combo') { ?>
      <p style="font-size:12px; text-align:center;line-height: 0.5em;$style_print">Staff: <?php //echo $driver_name; ?></p>
      <?php //} ?> -->
</div>
<!-- <div style="margin-top:20px;">
   <table width="100%;">
   <td style="font-size:12px;text-align:left;">Signature...........</td>
   <td style="font-size:12px;text-align:right;">Rec:Signature</td>
   </table>
</div> -->
</div>
<script type="text/javascript">
   var content = document.getElementById('wrapper_pr').innerHTML;
   var win = window.open();            
   win.document.write(content);  
   win.print(content);
   win.window.close();
</script>
<?php if($order_type == 'delivery1') { //echo '123'; die;?>
<script type="text/javascript">
   var content = document.getElementById('wrapper_pr').innerHTML;
   var win = window.open();            
   win.document.write(content);  
   win.print(content);
   win.window.close();
</script>
<?php } ?>
<?php redirect($redirect); ?>