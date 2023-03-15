<style>
 .modal th {width: 35%;}
</style>
<?php

$prspop = getorders();
if($prspop != false) {
	$pcount = mysql_num_rows($prspop);	
	if($pcount > 0) {
		for($p = 0; $p < $pcount; $p++) {
			$prow = mysql_fetch_object($prspop);			
			$id = $prow->id;		
				
				 ?>
					<div class="modal fade" id="myModal<?php echo $id; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
					  <div class="modal-dialog" role="document">
						<div class="modal-content">
						  <div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color:red;"><span aria-hidden="true">&times;</span></button>
							<h4 class="modal-title" id="myModalLabel">Order Items </h4>
						  </div>
						  <div class="modal-body">
							<table class="table table-striped table-bordered table-hover table-heading no-border-bottom">
								<thead>
									<tr>
										<th>#</th>
										<th>Category</th>
										<!--<th>Product Name&Code</th>-->
										<th>Amount</th>
										<th>Weight</th>
										<th>Status</th>											
									</tr>
								</thead>
								<tbody>
									<?php
										$prs1 = getorderitems($id);											
										if($prs1 != false) {
											$pcount1 = mysql_num_rows($prs1);
											if($pcount1 > 0) {
												for($p1 = 0; $p1 < $pcount1; $p1++) {
													$prow1 = mysql_fetch_object($prs1);
													$order_item_id = $prow1->id;
													$product_name = $prow1->product_name;
													$product_code = $prow1->product_code;
													$amount = $prow1->amount;
													$weight = $prow1->weight;
													$flavour_id = $prow1->flavour_id;
													$filling_id = $prow1->filling_id;
													$filling_name = getFillingName($prow1->filling_id);
													$flavour_name = getFlavourName($prow1->flavour_id);
													$category_id = $prow1->category_id;
													$order_id = $prow1->order_id;
													$status = $prow1->status;
													$category_name = getcategoryName($prow1->category_id);
													echo "<tr>";
													echo "<td>".$order_item_id."</td>";
													echo "<td>".$category_name."</td>";
													//echo "<td>".$product_name."<br><br><p>code:".$product_code."</p></td>";
													echo "<td>".$amount."</td>";
													echo "<td>".$weight."</td>";
													echo "<td>".$status_arr[$status]."</td>";														
													echo "</tr>";
												}
											}
										}
										else {
											echo "<tr>";
											echo "<td colspan='3'>No Order Item found to list.</td>";
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