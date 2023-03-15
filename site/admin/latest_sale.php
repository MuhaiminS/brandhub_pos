<?php 
session_start();
include("../functions.php");
include_once("../config.php");
connect_dre_db();
chkAdminLoggedIn();

$sql = "SELECT DATE_FORMAT(t1.settle_date,'%Y-%m-%d') as settle_date, t1.delivery_sale, t2.shop_name, t1.discount, t1.cash_sale, t1.card_sale, ifnull(t1.cash_sale, 0) + ifnull(t1.card_sale, 0) AS sale_total FROM settle_sale AS t1 LEFT OUTER JOIN locations_shops AS t2 ON t2.id=t1.shop_id WHERE  DATE_FORMAT(t1.settle_date,'%Y-%m-%d') = adddate(CURDATE(), 0) ORDER BY t1.shop_id ASC";
$result = mysql_query($sql);
$num=mysql_num_rows($result);
	//echo "total result ".$num;
if($num>0)
{	
	while($row = mysql_fetch_object($result)) {
		$sale_arr[] = $row;
	
	}


$shop = '0';
foreach($sale_arr as $sale){
	$sales_arr[$sale->shop_name]['shop_name'] = $sale->shop_name;
	if($sale->shop_name == $shop) {		
		$sales_arr[$sale->shop_name]['cash_sale'] += $sale->cash_sale;
		$sales_arr[$sale->shop_name]['card_sale'] += $sale->card_sale;
		$sales_arr[$sale->shop_name]['delivery_sale'] += $sale->delivery_sale;
		$sales_arr[$sale->shop_name]['discount'] += $sale->discount;
		$sales_arr[$sale->shop_name]['sale_total'] += $sale->sale_total;
	} else {		
		$sales_arr[$sale->shop_name]['cash_sale'] = $sale->cash_sale;
		$sales_arr[$sale->shop_name]['card_sale'] = $sale->card_sale;
		$sales_arr[$sale->shop_name]['delivery_sale'] = $sale->delivery_sale;
		$sales_arr[$sale->shop_name]['discount'] = $sale->discount;
		$sales_arr[$sale->shop_name]['sale_total'] = $sale->sale_total;
		$shop = $sale->shop_name;
	}
}
}
//echo "<pre>"; print_r($sales_arr);exit;



$site_url = getServerURL();
?>

					<div class="pre-sale">
						 <div class="tile-heading panel-lsale">Latest Sale Reports <span class="pull-right"></span></div>
						<div class="box-content no-padding">
							<table class="table table-striped table-bordered table-hover table-heading no-border-bottom">							
								<tr>
									<th>Shop Name</th>
									<?php if($num>0) { foreach($sales_arr as $sale) { 
									echo "<td>".$sale['shop_name']."</td>";									
									} }?>
								</tr>
								<tr>
									<th>Cash sale</th>
									<?php if($num>0) { foreach($sales_arr as $sale) { 
									echo "<td>".$sale['cash_sale']."</td>";								
									} }?>
								</tr>
								<tr>
									<th>Card Sale</th>
									<?php if($num>0) { foreach($sales_arr as $sale) { 
									echo "<td>".$sale['card_sale']."</td>";	
								 } }?>
								 </tr>
								 <tr>
									<th>Total Delivery</th>	
									<?php if($num>0) { foreach($sales_arr as $sale) { 
									echo "<td>".$sale['delivery_sale']."</td>";
								 } }?>
								 </tr>								
								 <tr>
									<th>Discount</th>	
									<?php if($num>0) { foreach($sales_arr as $sale) { 
									echo "<td>".$sale['discount']."</td>";
								 } }?>
								 </tr>	
								 <tr>
									<th>Total Sales</th>	
									<?php if($num>0) { foreach($sales_arr as $sale) { 
									echo "<td style='color:blue'>".bcsub($sale['sale_total'], $sale['discount'])."</td>";
								 } }?>
								 </tr>	
							</table>
						</div>	
						<div class="tile-footer5"><a href="<?php echo $site_url; ?>/admin/manage_sale_orders.php">View more...</a></div>
					</div>
				