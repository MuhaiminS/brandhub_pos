<style>
	.modal th {width: 35%;}
</style>
<?php
	$prspop = getSaleorders($reference_ids,$payment_status,$from_date1, $to_date1,$shops, $pageLimit, $setLimit);	
	if($prspop != false) {
		$pcount = mysqli_num_rows($prspop);	
		if($pcount > 0) {
			for($p = 0; $p < $pcount; $p++) {
				$prow = mysqli_fetch_object($prspop);			
				$id = $prow->id;		
					
					 ?>
<div class="modal fade" id="myModal<?php echo $id; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color:red;"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="myModalLabel">Purchase Order Items</h4>
			</div>
			<div class="modal-body">
				<table class="table table-striped table-bordered table-hover table-heading no-border-bottom">
					<thead>
						<tr>
							<th>#</th>
							<th>Items</th>
							<!-- <th>Unit</th>
							<th>Stock</th> -->
							<th>Qty</th>
							<th>Price</th>							
							<th>VAT Amount</th>
							<th>Total Amount</th>
						</tr>
					</thead>
					<tbody>
						<?php $tot_amount = $tot_vat = $tot_withvat = '0.00';
								$tot_vat_amount = '0.00';
								$tot_withvat_amount = $tot_tax = '0.00';
							$prs1 = getPurchaseOrderitems($id);											
							if($prs1 != false) {
								$pcount1 = mysqli_num_rows($prs1);
								if($pcount1 > 0) {
									for($p1 = 0; $p1 < $pcount1; $p1++) {
										$prow1 = mysqli_fetch_object($prs1);
										$pur_order_item_id = $prow1->id;													
										$price = $prow1->unit_price;
										$unit = $prow1->unit_name;
										$qty = $prow1->qty;
										$stock = $prow1->stock;
										$total_amount = $prow1->total_amount;
										$tax = $prow1->tax;
										$payment_type = $prow1->payment_type;
										//$item_name = getItemNames($prow1->item_id);
										$item_name = $prow1->product_name;
										$price_qty = $price * $qty;
										
										$tot_vat = ($price_qty/100 * $tax);
										$tot_withvat = ($price_qty + ($price_qty/100 * $tax));
										
										$tot_vat_amount += ($price_qty/100 * $tax);
										$tot_withvat_amount += ($price_qty + ($price_qty/100 * $tax));
										
										echo "<tr>";
										echo "<td>".$pur_order_item_id."</td>";
										echo "<td>".$item_name."</td>";
										//echo "<td>".$unit."</td>";
										//echo "<td>".$stock."</td>";																				
										echo "<td>".$qty."</td>";
										echo "<td>".number_format($price, 2)."</td>";
										echo "<td>".number_format($tot_vat, 2)."</td>";
										echo "<td>".number_format($tot_withvat, 2)."</td>";
										echo "</tr>";													
									}
									echo "<tr>";
									echo "<td colspan='4'>Total</td>";
									echo "<td>".number_format($tot_vat_amount, 2)."</td>";
									echo "<td>".number_format($tot_withvat_amount, 2)."</td>";
									echo "</tr>";
								}
							}
							else {
								echo "<tr>";
								echo "<td colspan='7'>No Order Item found to list.</td>";
								echo "</tr>";
							}
							?>						
					</tbody>
				</table>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>
<?php } } }
	else {
		echo "<tr>";
		echo "<td>No Orders found to list.</td>";
		echo "</tr>";
	}
	?>