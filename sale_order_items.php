<style>
 .modal th {width: 35%;}
</style>

<?php if(!empty($sale_orders)) {//echo '<pre>'; print_r($sale_orders);
		foreach($sale_orders as $sale_order) { 
			if(!empty($sale_order['items'])) {
				$sale_order_item = json_decode($sale_order['items']); 
				$total_price = "0.00";
				foreach($sale_order_item as $item) { 													
					$price = $item->price;
					$qty = $item->qty;
					$total_price +=$price*$qty;
					}
			} ?>
		<div class="modal fade" id="myModal<?php echo $sale_order['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
		  <div class="modal-dialog" role="document">
			<div class="modal-content">
			  <div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color:red;"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="myModalLabel">Sale Order Items</h4>
			  </div>
			  <div class="modal-body">
			  <table class="table table-striped table-bordered table-hover table-heading no-border-bottom">
				<tbody>
					<tr class="cod_custom_table_body_totaltrs">
						   <td>	Receipt No<span class="totalprice"><?php echo $sale_order['receipt_id']; ?></span></td>
						   <td>Customer name<span class="totalprice"><?php echo $sale_order['contact_name']; ?></span></td>
						</tr>
						<tr class="cod_custom_table_body_totaltrs">
						   <td>Order date<span class="totalprice"><?php echo  date("d-m-Y", strtotime($sale_order['ordered_date'])); ?></span></td>
						   <td>Customer number<span class="totalprice"><?php echo $sale_order['contact_number']; ?></span></td>
						</tr>
						<?php if($sale_order['order_type'] != 'combo') { ?>
						<tr class="cod_custom_table_body_totaltrs">
						<?php if(BILL_TAX == 'yes'){
								if(BILL_TAX_TYPE == 'VAT')
							 { ?>
						    <td>Total<span class="totalprice">INR <?php echo number_format($total_price+($total_price/100 * $sale_order['vat']), 2); ?></span></td>
						    <?php }
							elseif(BILL_TAX_TYPE == 'GST')
							{ ?>
								 <td>Total<span class="totalprice"><?php echo number_format($total_price, 2); ?></span></td>
					    <?php } else { ?>
							<td>Total<span class="totalprice">INR <?php echo number_format($total_price, 2); ?></span></td>
						<?php } ?>
						   <td></td>
						</tr>
						 <?php } } else { ?>
						<tr class="cod_custom_table_body_totaltrs">
						<?php if(BILL_TAX == 'yes'){
								if(BILL_TAX_TYPE == 'VAT')
							 { ?>
						    <td>Total<span class="totalprice">INR <?php echo number_format($sale_order['combo_package_price']+($sale_order['combo_package_price']/100 * $sale_order['vat']), 2); ?></span></td>
					    <?php }
							elseif(BILL_TAX_TYPE == 'GST')
							{ 
								$total_cgst=$total_sgst=0.00;

								$cgst = $sale_order['combo_package_gst']/2;
						 		$sgst = $sale_order['combo_package_gst']/2;
								$total_cgst += $sale_order['combo_package_price']/100*$cgst;
		   						$total_sgst += $sale_order['combo_package_price']/100*$sgst;
		   						$gst=$total_sgst+$total_cgst;?>

								<td>Total<span class="totalprice"><?php echo number_format($sale_order['combo_package_price']+$gst, 2); ?></span></td>
								<?php } } else { ?>
							<td>Total<span class="totalprice">INR <?php echo number_format($sale_order['combo_package_price'], 2); ?></span></td>
						<?php } ?>
						   <td></td>
						</tr>
						<?php } ?>
						<tr class="cod_custom_table_body_totaltrs">
						   <td>Discount<span class="disprice"><?php echo number_format($sale_order['discount'], 2); ?></span></td>
							<td>Address<span class="totalprice"><?php echo $sale_order['address']; ?></span></td>
						</tr>
						<?php $page_name = basename($_SERVER['PHP_SELF']); 
						if($page_name == 'online_order_log.php') {?>
					<tr>
					   <td>
						  <div class="form-group">
							 <label for="sel1">Payment Status</label>
							 <select class="form-control payment_status" data-id = <?php echo $sale_order['id']; ?> name="payment_status_<?php echo $sale_order['id']; ?>" id="payment_status_<?php echo $sale_order['id']; ?>">
								<option value="paid" <?php if($sale_order['payment_status'] == 'paid') { echo "Selected"; } ?>>Paid/دفع</option>
								<option value="unpaid" <?php if($sale_order['payment_status'] == 'unpaid') { echo "Selected"; } ?>>Un Paid/غير مدفوع</option>
							 </select>
						  </div>
					   </td>
					   <td>
						  <div class="form-group">
							 <label for="sel1">Status<br></label>
							 <select class="form-control required order_status" data-id = <?php echo $sale_order['id']; ?> name="status_<?php echo $sale_order['id']; ?>" id="status_<?php echo $sale_order['id']; ?>">
								<option value="pending" <?php if($sale_order['status'] == 'pending') { echo "Selected"; } ?> >Pending</option>
								<option value="conform" <?php if($sale_order['status'] == 'conform') { echo "Selected"; } ?> >Confirm</option>
								<option value="out_for_delivery" <?php if($sale_order['status'] == 'out_for_delivery') { echo "Selected"; } ?> >Out for Delivered</option>
								<option value="delivered" <?php if($sale_order['status'] == 'delivered') { echo "Selected"; } ?>>Delivered</option>
								<option value="reject" <?php if($sale_order['status'] == 'reject') { echo "Selected"; } ?>>Rejected</option>
							 </select>
						  </div>
					   </td>
					</tr>
					<tr>
						<td colspan="2">
						<?php 
						$deliver_in = $reject_reason = '';
						if($sale_order['status'] == 'conform') {
							$deliver_in = $sale_order['delivered_in'];							
						}
						if($sale_order['status'] == 'reject') {
							$reject_reason = $sale_order['reject_reason'];							
						} ?>
							<div class="form-group" id="reject_reason_<?php echo $sale_order['id']; ?>" style="display:none;">
							 <label for="sel1">Reason to reject</label>
							 <textarea maxlength="130" class="form-control" name="reject_reason_<?php echo $sale_order['id']; ?>" id="reject_reasons_<?php echo $sale_order['id']; ?>" required></textarea>
						  </div>
						  <?php if($deliver_in) { 
						  $deliver_in = explode('_', $deliver_in) ?>
						  <p>Old time <?php echo $deliver_in[1]; ?></p>
						  <?php } ?>
						  <div class="form-group" id="deliver_in_val_<?php echo $sale_order['id']; ?>" style="display:none;">
							 <label for="sel1">Deliver in</label>	
							 <select class="form-control required deliver_in_val" name="delivered_in_<?php echo $sale_order['id']; ?>"  id="delivered_in_<?php echo $sale_order['id']; ?>" >
								<?php
								for($hours=0; $hours<24; $hours++) // the interval for hours is '1'
								for($mins=0; $mins<60; $mins+=30) // the interval for mins is '30'
									echo '<option value='.str_pad($hours,2,'0',STR_PAD_LEFT).':'
												   .str_pad($mins,2,'0',STR_PAD_LEFT).'>'.str_pad($hours,2,'0',STR_PAD_LEFT).':'
												   .str_pad($mins,2,'0',STR_PAD_LEFT).' Hrs</option>';
								?>
								
							 </select>
						  </div>
						  <?php if($reject_reason) { ?> 
						  <p>Old Reason<?php echo $reject_reason; ?></p>
						  <?php } ?>
						</td>
					</tr>
					<tr>
					<tr>
						<td colspan="2">
							<button style="float: right; display:none;" type="button" data-id = <?php echo $sale_order['id']; ?> id="order_update_<?php echo $sale_order['id']; ?>" class="order_update btn btn-default">Save</button>
						</td>
					</tr>
					<?php } ?>
				</tbody>
			  </table>
				<table class="table table-striped table-bordered table-hover table-heading no-border-bottom">
					<thead>
						<tr>
							<th>#</th>
							<th>Items</th>
							<th>Qty</th>
							<th>Price</th>										
						</tr>
					</thead>
					<tbody>
						<?php 
						if(!empty($sale_order['items'])) {
							$sale_order_item = json_decode($sale_order['items']); 
								$total = "0.00";
								foreach($sale_order_item as $item) { 							
									$sale_order_item_id = $item->id;													
									$price = $item->price;
									$qty = $item->qty;
									$item_name = $function->getItemName($item->item_id);
									$price_qty = $price * $qty;
									$total += $price_qty;													
									echo "<tr>";
									echo "<td>".$sale_order_item_id."</td>";
									echo "<td>".$item_name."</td>";													
									echo "<td>".$qty."</td>";
									echo "<td>".number_format($price_qty, 2)."</td>";																					
									echo "</tr>";													
								}
								echo "<tr>";
								echo "<td colspan='3'>Total</td>";
								echo "<td>".number_format($total, 2)."</td>";
								echo "</tr>";								
							}else {
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
	<?php }
	}else {
		//echo "<tr>";
		//echo "<td>No Orders found to list.</td>";
		//echo "</tr>";
	}
?>
