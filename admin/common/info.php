<?php if(isset($_GET['resp']) && $_GET['resp'] == 'addsucc') { ?>
	<div class="alert alert-success alert-dismissible">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		<h4><i class="icon fa fa-check"></i> Success!</h4>
		Added successfully.
	</div>
<?php } ?>

<?php if(isset($_GET['resp']) && $_GET['resp'] == 'updatesucc') { ?>
	<div class="alert alert-success alert-dismissible">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		<h4><i class="icon fa fa-check"></i> Success!</h4>
		Updated successfully.
	</div>
<?php } ?>

<?php if(isset($_GET['resp']) && $_GET['resp'] == 'deletesucc') { ?>
	<div class="alert alert-success alert-dismissible">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		<h4><i class="icon fa fa-check"></i> Success!</h4>
		Deleted successfully.
	</div>
<?php } ?>

<?php if(isset($_GET['resp']) && $_GET['resp'] == 'paidsucc') { ?>
	<div class="alert alert-success alert-dismissible">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		<h4><i class="icon fa fa-check"></i> Success!</h4>
		Payment paid successfully.
	</div>
<?php } ?>

<?php if(isset($_GET['resp']) && $_GET['resp'] == 'notpaid') { ?>
	<div class="alert alert-success alert-dismissible">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		<h4><i class="icon fa fa-check"></i> Success!</h4>
		Payment Not paid.
	</div>
<?php } ?>

<?php if(isset($_GET['resp']) && $_GET['resp'] == 'activatedsucc') { ?>
<div class="alert alert-success alert-dismissible">
	<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
	<h4><i class="icon fa fa-check"></i> Success!</h4>
	Detail activated successfully.
</div>
<?php } ?>

<?php if(isset($_GET['resp']) && $_GET['resp'] == 'deactivatedsucc') { ?>
<div class="alert alert-success alert-dismissible">
	<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
	<h4><i class="icon fa fa-check"></i> Success!</h4>
	Detail deactivated successfully.
</div>
<?php } ?>