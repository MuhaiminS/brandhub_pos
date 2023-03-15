<?php 
	$prs = getCreditPost($customer_id);											
	if($prs != false) {
		$pcount = mysqli_num_rows($prs);
		if($pcount > 0) {
			for($p = 0; $p < $pcount; $p++) {
				$prow = mysqli_fetch_object($prs);
				$customer_id = $prow->customer_id;
				$name = $prow->name;
				$credit = ($prow->credit != '') ? $prow->credit: 0;
				$credit = $credit + ($credit/100)*5;
				$debit = ($prow->debit != '') ? $prow->debit: 0;
				$number = $prow->number; ?>
<div id="settinsModal_<?php echo $customer_id; ?>" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Credit recovery Pay</h4>
			</div>
			<div class="modal-body">
				<form id="pay_setting_<?php echo $customer_id; ?>" class="form-horizontal" role="form">
					<input type="hidden" name="customer_id" value="<?php echo $customer_id;?>">
					<div class="form-group">
						<label  class="col-sm-3 control-label">Name</label>
						<div class="col-sm-9">
							<input readonly class="form-control" type="text" id="name" name="name" value="<?php echo $name; ?>" />
						</div>
					</div>
					<div class="form-group">
						<label  class="col-sm-3 control-label">Number</label>
						<div class="col-sm-9">
							<input readonly class="form-control" type="text" id="number" name="number" value="<?php echo $number; ?>" />
						</div>
					</div>
					<div class="form-group">
						<label  class="col-sm-3 control-label">Credit</label>
						<div class="col-sm-9">
							<input readonly class="form-control" type="text" id="credit" name="credit" value="<?php echo $credit; ?>" />
						</div>
					</div>
					<div class="form-group">
						<label  class="col-sm-3 control-label">Debit</label>
						<div class="col-sm-9">
							<input readonly class="form-control" type="text" id="debit" name="debit" value="<?php echo $debit; ?>" />
						</div>
					</div>
					<div class="form-group">
						<label  class="col-sm-3 control-label">Balance to pay</label>
						<div class="col-sm-9">
							<input readonly class="form-control" type="text" id="balance_to_pay" name="balance_to_pay" value="<?php echo $credit - $debit; ?>" />
						</div>
					</div>
					<div class="form-group">
						<label  class="col-sm-3 control-label">Enter amount</label>
						<div class="col-sm-9">
							<input type="text" class="form-control" id="amount" name="amount" value="" required>
						</div>
					</div>
					<div class="form-group">
						<div class=" col-sm-12">
							<button type="button" id="<?php echo $customer_id; ?>" class="btn btn-default pay_update" style="float: right;">Pay</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
<?php 
	}	 
	}
	}
	?>