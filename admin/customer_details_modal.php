<style>
	.modal th {width: 35%;}
</style>
<?php
	
$prs = getCustomerDetails();											
	if($prs != false) {
		$pcount = mysqli_num_rows($prs);
		if($pcount > 0) {
			for($p = 0; $p < $pcount; $p++) {
				$prow = mysqli_fetch_object($prs);
				$customer_id = $prow->customer_id;
					$customer_name = $prow->customer_name;

					 ?>
<div class="modal fade" id="myModal<?php echo $customer_id; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color:red;"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="myModalLabel">This Is <strong> "<?php echo $customer_name; ?>" </strong> Orders</h4>
			</div>
			<div class="modal-body">
				<table class="table table-striped table-bordered table-hover table-heading no-border-bottom">
					<thead>
						<tr>
							<th>#</th>
							<th>Date Of Orders</th>
							<th>Total</th>
						</tr>
					</thead>
					<tbody>
						<?php $total = "0.00";
							$prs1 = getCustomerOrders($customer_id);											
							if($prs1 != false) {
								$pcount1 = mysqli_num_rows($prs1);
								if($pcount1 > 0) {
									for($p1 = 0; $p1 < $pcount1; $p1++) {
										$prow1 = mysqli_fetch_object($prs1);											
										$amount = $prow1->amount;
										$ordered_date = $prow1->order_date;
										$total_vat = $prow1->total_vat;
										$total += $amount + $total_vat;
										
										echo "<tr>";
										echo "<td>".($p1 + 1)."</td>";
										echo "<td>".$ordered_date."</td>";
										echo "<td>".number_format(($amount + $total_vat), 2)."</td>";
										echo "</tr>";													
									}
									echo "<tr>";
									echo "<td colspan='2'>Total</td>";
									echo "<td>".number_format($total, 2)."</td>";
									echo "</tr>";
								}
							}
							else {
								echo "<tr>";
								echo "<td colspan='2'>No Order found to list.</td>";
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