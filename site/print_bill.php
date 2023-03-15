<style>
@media print {
	body {font-family: Arial;}
	#wrapper_pr {width: 100%; margin:0 auto; text-align:center; color:#000; font-family: Arial; font-size:12px;}
	.bdd{border-top: 1px solid #000;}	
}
</style>
<?php $style_print = "font-family: Arial"; ?>
<div id="wrapper_pr">
<h2 style="text-transform:uppercase;font-size:13px; text-align:center;line-height: 0.5em;$style_print"><strong>CLT WEB POS</strong></h2>
<p style="font-size:12px; text-align:center;line-height: 0.5em;$style_print">Arumbakkam, Chennai</p>
<p style="font-size:12px; text-align:center;line-height: 0.5em;$style_print">Tel: 9790555814</p>
<!-- <p style="font-size:12px; text-align:left;line-height: 0.5em;$style_print">Biller name: <span style="float:right;"><?php //echo date(); ?><span></p> -->
<div style="clear:both;"></div>
<table class="table" cellspacing="0" border="0">
   <thead>
      <tr>
         <th style="font-size:12px;text-align:left; width:70%;$style_print">Items</th>
         <th style="font-size:12px;text-align:right; width:15%;$style_print">U.Price*Qty</th>
		 <th style="font-size:12px;text-align:right; width:15%;$style_print">Price</th>
      </tr>
   </thead>
   <tbody id="bg_val">
   <?php
   $total_amount = '';
	$gst_group = array();
	//$sgst_group = array();
	//echo '<pre>';print_r($result_arr);echo '</pre>';
	foreach($result_arr as $key => $res){	
		echo "<tr>";
		echo "<td style='font-size:12px;float:left; width:80%;$style_print'>".$res['item_name']."</td>";
		echo "<td style='font-size:12px;text-align:right; width:12%;$style_print'>".number_format((float)($res['price']), 2, '.', '')." X ".$res['qty']."</td>";
		echo "<td style='font-size:12px;text-align:right; width:8%;$style_print'>".number_format((float)$res['price']*$res['qty'], 2, '.', '')."</td>";
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
echo "<tr><td colspan='3'><div style='border-top:1px solid #000;padding:5px 0px;'></div></td></tr>";
echo "<tr><td style='font-size:12px;float:left; width:40%;$style_print'>Sub Total :</td><td colspan='2' style='font-size:12px;text-align: right; width:60%;$style_print'> ".number_format((float)$total_amount, 2, '.', '')."</td></tr>";
echo "<tr><td style='font-size:12px;float:left; width:40%;$style_print'>Discount :</td><td colspan='2' style='text-align: right;font-size:12px; width:60%;$style_print'> ".number_format((float)($discount), 2, '.', '')."</td></tr>";
echo "<tr><td style='font-size:12px;float:left; width:40%;$style_print'>Total :</td><td colspan='2' style='font-size:12px;text-align: right; width:60%;$style_print'> ".number_format((float)round($total_amount - $discount), 2, '.', '')."</td></tr>";
//echo "<tr><td colspan='3'><div style='border-bottom:1px solid #000;padding:5px 0px;'></div></td></tr>";
//echo "<tr><td style='font-size:12px;float:left; width:60%;$style_print'>Amount Given :</td><td colspan='2' style='font-size:12px;text-align: right; width:40%;$style_print'> ".number_format((float)$amount_given, 2, '.', '')."</td></tr>";
//echo "<tr><td style='font-size:12px;float:left; width:40%;$style_print'>Balance :</td><td colspan='2' style='text-align: right;font-size:12px; width:60%;$style_print'> ".number_format((float)($balance), 2, '.', '')."</td></tr>";

echo "<tr><td colspan='3'><div style='border-top:1px solid #000;padding:5px 0px;'></div></td></tr>";
foreach($gst_group as $key => $gst) {
echo "<tr><td colspan='3'style='font-size:12px;text-align: right;width:100% !important;$style_print'> CGST (".$key."%): ".number_format((float)$gst['cgst'], 2, '.', '')."&nbsp;&nbsp;&nbsp; SGST (".$key."%): ".number_format((float)$gst['sgst'], 2, '.', '')."</td></tr>";
}
?>

</tbody>
</table>
<div style="border-top:1px solid #000;">
   <p style="font-size:12px;text-align:center;">
      Thank you for your business!
   </p>
</div>
</div>
<script type="text/javascript">
	var content = document.getElementById('wrapper_pr').innerHTML;
	var win = window.open();				
	win.document.write(content);	
	win.print(content);
	win.window.close();
</script>