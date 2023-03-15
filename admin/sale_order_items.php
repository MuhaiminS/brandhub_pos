<style>
	.modal th {width: 35%;}
</style>
<?php
	$prspop = getSaleorders($receipt_ids,$payment_types,$from_date1, $to_date1,$shops, $pageLimit, $setLimit);
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
				<h4 class="modal-title" id="myModalLabel">Sale Order Items</h4>
			</div>
			<div class="modal-body">
				<table class="table table-striped table-bordered table-hover table-heading no-border-bottom">
					<thead>
						<tr>
							<th>#</th>
							<th>Items</th>
							<!--<th>Product Name&Code</th>-->
							<th>Qty</th>
							<th>Price</th>
						</tr>
					</thead>
					<tbody>
						<?php $total = $net_total = "0.00";
							$prs1 = getSaleOrderitems($id);											
							if($prs1 != false) {
								$pcount1 = mysqli_num_rows($prs1);
								if($pcount1 > 0) {
									for($p1 = 0; $p1 < $pcount1; $p1++) {
										$prow1 = mysqli_fetch_object($prs1);
										$sale_order_item_id = $prow1->id;													
										$price = $prow1->price;
										$qty = $prow1->qty;
										$cgst = $prow1->CGST;
										$sgst = $prow1->SGST;
										$item_name = $prow1->item_name;
										$price_qty = $price * $qty;
										//$price_with_gst = $price + (($price / 100) * ($cgst + $sgst));
										$total += $price_qty;
										//$total = $net_total + (($net_total / 100) * ($cgst + $sgst));
										//$order_id = $prow1->order_id;
										//$status = $prow1->status;
										//$category_name = getcategoryName($prow1->category_id);
										echo "<tr>";
										echo "<td>".$sale_order_item_id."</td>";
										echo "<td>".$item_name."</td>";
										//echo "<td>".$product_name."<br><br><p>code:".$product_code."</p></td>";
										echo "<td>".$qty."</td>";
										echo "<td>".number_format($price, 2)."</td>";																						
										echo "</tr>";													
									}
									echo "<tr>";
									echo "<td colspan='3'>Total</td>";
									echo "<td>".number_format($total, 2)."</td>";
									echo "</tr>";
								}
							}
							else {
								echo "<tr>";
								echo "<td colspan='4'>No Order Item found to list.</td>";
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