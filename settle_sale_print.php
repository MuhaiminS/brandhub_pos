<?php 
   session_start();
   require_once 'db_functions.php';
   $function = New DB_Functions(); 
   $inputs['shop_id'] = $_SESSION['shop_id'];
   $inputs['user_id'] = $_SESSION['user_id'];
   $inputs['discount_type'] = 'amount';
   $inputs['to_date'] = date("Y-m-d H:i:s");
   $settle_sale = $function->getAllSettle($inputs);
   $set = (isset($_GET['set']) && $_GET['set'] !='') ? $_GET['set'] : '';

   $inputs1['from_date'] = date('Y-m-d 00:00:00');
   $inputs1['to_date'] = date('Y-m-d 23:59:59');
   $inputs1['shop_id'] = $_SESSION['shop_id'];
   $inputs1['user_id'] = $_SESSION['user_id'];
   $inputs1['order_type'] = 'delivery';
   $inputs1['payment_type'] = '';
   $inputs1['payment_status'] = 'unpaid';
   $inputs1['status'] = 'pending';
   $sale_orders = $function->getSaleOrderItemDetailsList($inputs1); 
   $user_sales = $function->getSalesDetailsuserwise($inputs);  
   //echo '<pre>'; print_r($user_orders); die;
$redirect = "settle_sale.php";

function getUserName($user_id)
{
	$where = "WHERE id = '$user_id'";
	$service = $function->getnamewhere('drivers', 'name', $where);
	return $service;
}
 ?>
 <?php if((count($sale_orders) > 0) && ($set == 'yes')) { 
	echo "<script>alert('COD items still pending. So cant able to settle');</script>";
	$function->redirect($redirect);
	exit;
}
?>
  <style>
@media print {
	body {font-family: Arial;}
	#wrapper_pr {width: 100%; margin:0 auto; text-align:center; color:#000; font-family: Arial; font-size:12px;}
	.bdd{border-top: 1px solid #000;}	
}
</style>
<?php $style_print = "font-family: Arial"; 
?>
<div id="wrapper_pr">
<h2 style="text-transform:uppercase;font-size:13px; text-align:center;line-height: 0.5em;$style_print"><strong></strong></h2>
<div style="clear:both;"></div>
<table class="table" cellspacing="0" border="0">
   <thead>
      <tr>
         <th style="font-size:12px;text-align:left; width:70%;$style_print">Date</th>
         <th style="font-size:12px;text-align:right; width:30%;$style_print"><?php echo date("Y-m-d H:i:s"); ?></th>
      </tr>
   </thead>
   <tbody id="bg_val">
   <?php
	if(BILL_TAX == 'yes'){
       if(BILL_TAX_TYPE == 'VAT')
       { 
	echo "<tr>";
	echo "<td style='font-size:12px;float:left; width:70%;$style_print'>Total Cash Sale/مجموع البيع النقدي</td>";
	echo "<td style='font-size:12px;text-align:right; width:30%;$style_print'>INR ".number_format((float)$settle_sale['cash_sale_without_vat'], 2, '.', '')."</td>";
	echo "</tr>";
	  } elseif(BILL_TAX_TYPE == 'GST') {
	  	echo "<tr>";
	echo "<td style='font-size:12px;float:left; width:70%;$style_print'>Total Cash Sale</td>";
	echo "<td style='font-size:12px;text-align:right; width:30%;$style_print'>INR ".number_format((float)$settle_sale['cash_sale'], 2, '.', '')."</td>";
	echo "</tr>";
    }}
    if(BILL_TAX == 'yes') { if(BILL_TAX_TYPE == 'VAT') {
	echo "<tr>";
	echo "<td style='font-size:12px;float:left; width:70%;$style_print'>Total Cash VAT/مجموع ضريبة القيمة المضافة النقدية</td>";
	echo "<td style='font-size:12px;text-align:right; width:30%;$style_print'>INR ".number_format((float)$settle_sale['cash_sale_vat'], 2, '.', '')."</td>";
	echo "</tr>";
	 } } 
     if(BILL_TAX == 'yes') {
       if(BILL_TAX_TYPE == 'VAT')
       {
	echo "<tr>";
	echo "<td style='font-size:12px;float:left; width:70%;$style_print'>Total Card Sale/مجموع بطاقة بيع</td>";
	echo "<td style='font-size:12px;text-align:right; width:30%;$style_print'>INR ".number_format((float)$settle_sale['card_sale_without_vat'], 2, '.', '')."</td>";
	echo "</tr>";
	}
	   elseif(BILL_TAX_TYPE == 'GST')
	   { 
	echo "<tr>";
	echo "<td style='font-size:12px;float:left; width:70%;$style_print'>Total Card Sale</td>";
	echo "<td style='font-size:12px;text-align:right; width:30%;$style_print'>INR ".number_format((float)$settle_sale['card_sale'], 2, '.', '')."</td>";
	echo "</tr>";
	} }
	 if(BILL_TAX == 'yes') { if(BILL_TAX_TYPE == 'VAT') {
	echo "<tr>";
	
	echo "<td style='font-size:12px;float:left; width:70%;$style_print'>Total Card VAT/إجمالي ضريبة القيمة المضافة على البطاقة</td>";
	echo "<td style='font-size:12px;text-align:right; width:30%;$style_print'>INR ".number_format((float)$settle_sale['card_sale_vat'], 2, '.', '')."</td>";
	echo "</tr>";
	} }if(BILL_TAX_TYPE == 'GST')
      {
      	echo "<tr>";
	
	// echo "<td style='font-size:12px;float:left; width:70%;$style_print'>Total GST</td>";
	// echo "<td style='font-size:12px;text-align:right; width:30%;$style_print'>INR ".number_format((float)($settle_sale['total_sgst']+$settle_sale['total_cgst']), 2, '.', '')."</td>";
	echo "</tr>";
   }  
	echo "<tr>";
	echo "<td style='font-size:12px;float:left; width:70%;$style_print'>Cash Drawer</td>";
	echo "<td style='font-size:12px;text-align:right; width:30%;$style_print'>INR ".number_format((float)$settle_sale['cash_drawer'], 2, '.', '')."</td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td style='font-size:12px;float:left; width:70%;$style_print'>Gross Total</td>";
	echo "<td style='font-size:12px;text-align:right; width:30%;$style_print'>INR ".number_format((float)($settle_sale['cash_sale_without_vat']+$settle_sale['cash_sale_vat']+$settle_sale['card_sale_without_vat']+$settle_sale['card_sale_vat']), 2, '.', '')."</td>";
	echo "</tr>";
?>

</tbody>
</table>
<table class="table" cellspacing="0" border="0">
	<thead>
	      <tr>
	         <th style="font-size:12px;text-align:left; width:70%;$style_print">User name</th>
	         <th style="font-size:12px;text-align:right; width:30%;$style_print">Amount</th>
	      </tr>
	   </thead>
	   <tbody id="bg_val1">
	   <?php if($user_sales) { 
	   foreach($user_sales as $sale) {
	   echo "<tr>";
		echo "<td style='font-size:12px;float:left; width:70%;$style_print'>".$function->getDriverName($sale['driver_id'])."</td>";
		echo "<td style='font-size:12px;text-align:right; width:30%;$style_print'>INR ".$sale['amount']."</td>";
		echo "</tr>";
	    } } ?>
	   </tbody>
   <tbody id="bg_val">
</table>
</div>
<script type="text/javascript">
	var content = document.getElementById('wrapper_pr').innerHTML;
	var win = window.open();				
	win.document.write(content);	
	win.print(content);
	win.window.close();
</script>

<?php
$settle_sale['settle_date'] = date("Y-m-d H:i:s");
$settle_sale['shop_id'] = $_SESSION['shop_id'];
$settle_sale['user_id'] = $_SESSION['user_id'];
$settle_sale['discount_type'] = 'amount';
$settle_sale['to_date'] = date("Y-m-d H:i:s");
if($set == 'yes'){//echo '<pre>'; print_r($settle_sale);
	$settle_sale = $function->setSettleSale($settle_sale);	
	if($settle_sale){
		$function->redirect($redirect);
	}
} else {
	$function->redirect($redirect);
}
?>