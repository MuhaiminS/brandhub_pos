<?php 
   session_start();
   include("../functions.php");
   include_once("../config.php");
   chkAdminLoggedIn();
   connect_dre_db();
   $customer_id = (isset($_GET['customer_id']) && $_GET['customer_id'] !='') ? $_GET['customer_id'] : '';
   
   if(isset($_GET['act']) && $_GET['act'] != '' && isset($_GET['id']) && $_GET['id'] > 0) {	
   	$action = $_GET['act'];
   	$cat_id = $_GET['id'];
   	if($action == 'delete') {
   		$qry="DELETE FROM credit_sale WHERE id = $cat_id";
   		if(mysqli_query($GLOBALS['conn'], $qry)){
   			redirect('manage_item_category.php?resp=succ');
   		}
   	}
   }
   
   if(isset($_GET["page"])) {
   	$page = (int)$_GET["page"];
   } else {
   	$page = 1;
   }
   $setLimit = 10;
   $pageLimit = ($page * $setLimit) - $setLimit;
   
   function getCreditPost($customer_id = '')
   {
   	$qry="SELECT customer_id, name, number, SUM(CASE WHEN type='credit' THEN amount END) as credit,
          SUM(CASE WHEN type='debit' THEN amount END) as debit FROM credit_sale";
   	if($customer_id != ''){
   		$qry .=" WHERE customer_id = '$customer_id'";
   	}
   	$qry .=" GROUP BY customer_id ORDER BY customer_id DESC";
   	//$qry .=" LIMIT $pageLimit, $setLimit";
   	//echo $qry;
   	$result=mysqli_query($GLOBALS['conn'], $qry);
   	$num=mysqli_num_rows($result);
   	//echo "total result ".$num;
   	if($num>0)
   	{
   		return $result;
   	}
   	else
   	return false;
   }
   
   function getCustomer()
   {
    $customer = array();
    $query = "SELECT cd.customer_id as id, CONCAT(cd.customer_name, ' - ', cd.customer_number) as cus_det FROM customer_details as cd LEFT JOIN credit_sale as cs ON(cs.customer_id = cd.customer_id) GROUP BY cs.customer_id";
	echo $query;
    $run = mysqli_query($GLOBALS['conn'], $query);  
    while ($row = mysqli_fetch_array($run)) {
      $customer[$row['id']] = $row['cus_det'];
    }
    return $customer;
   }
   //$category_img_dir = "../category_images/";
   ?>
<!DOCTYPE html>
<!--
	This is a starter template page. Use this page to start your new project from
	scratch. This page gets rid of all links and provides the needed markup only.
	-->
<html>
	<head>
		<?php include("common/header.php"); ?>     
		<?php include("common/header-scripts.php"); ?>
	</head>
	<!--
		BODY TAG OPTIONS:
		=================
		Apply one or more of the following classes to get the
		desired effect
		|---------------------------------------------------------|
		| SKINS         | skin-blue                               |
		|               | skin-black                              |
		|               | skin-purple                             |
		|               | skin-yellow                             |
		|               | skin-red                                |
		|               | skin-green                              |
		|---------------------------------------------------------|
		|LAYOUT OPTIONS | fixed                                   |
		|               | layout-boxed                            |
		|               | layout-top-nav                          |
		|               | sidebar-collapse                        |
		|               | sidebar-mini                            |
		|---------------------------------------------------------|
		-->
	<body class="hold-transition skin-blue sidebar-mini">
		<div class="wrapper">
			<?php include("common/topbar.php"); ?>
			<?php include("common/sidebar.php"); ?>
			<!-- Content Wrapper. Contains page content -->
			<div class="content-wrapper">
				<!-- Content Header (Page header) -->
				<section class="content-header">
					<h1>
						Credit Recovery 
						<!--<small>Optional description</small>-->
					</h1>
					<ol class="breadcrumb">
						<li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
						<li class="active">Credit Recovery</li>
					</ol>
				</section>
				<!-- Main content -->
				<section class="content container-fluid">
					<div class="row">
						<div class="col-xs-12">
							<div class="box">
								<div class="box-header">
									<h3 class="box-title">Credit Covery</h3>
								</div>
								<!-- /.box-header -->
								<div class="box-body">
									<!-- Success msg display -->
									<?php include("common/info.php"); ?>
									<table id="example2" class="table table-bordered table-hover">
										<thead>
											<tr>
												<th>#</th>
												 <th>Customer detail</th>
												 <th>Debit & Credit</th>
												 <th>Balance to pay</th>
												 <th>Action</th>
											</tr>
										</thead>
										<tbody>
										 <?php
                                 $prs = getCreditPost($customer_id);											
                                 if($prs != false) {
                                 	$pcount = mysqli_num_rows($prs);
                                 	if($pcount > 0) {
                                 		for($p = 0; $p < $pcount; $p++) {
											$prow = mysqli_fetch_object($prs);
											$customer_ids = $prow->customer_id;
											$name = $prow->name;
											$credit = ($prow->credit != '') ? $prow->credit: 0;
											$credit = $credit + ($credit/100)*5;
											$debit = ($prow->debit != '') ? $prow->debit: 0;
											$number = $prow->number;
											echo "<tr>";
											echo "<td>".($p+1)."</td>";
											echo "<td>".safeTextOut($name).' - '.$number."</td>";
											echo "<td>Credit: ".$credit.", Debit: ".$debit."</td>";
											echo "<td>".($credit - $debit)."</td>";
											echo "<td><a style='cursor:pointer;' data-toggle='modal' data-target='#settinsModal_$customer_ids'>Pay</a></td>";
											echo "</tr>"; ?>
											<?php
												 }
												 }
												 }
												 else {
												 echo "<tr>";
												 echo "<td>No Credit found to list.</td>";
												 echo "</tr>";
												 }
												 ?>		
										</tbody>
									</table>
								</div>
								<!-- /.box-body -->
							</div>
							<!-- /.box -->
						</div>
						<!-- /.col -->
					</div>
					<!-- /.row -->
				</section>
				<!-- /.content -->
			</div>
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
				$number = $prow->number;
   ?>
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
<?php } } } ?>
			<!-- /.content-wrapper -->
			<?php include("common/footer.php"); ?>
			<?php include("common/sidebar-right.php"); ?>
		</div>
		<!-- ./wrapper -->
		<!-- REQUIRED JS SCRIPTS -->
		<?php include("common/footer-scripts.php"); ?>
		<script>
   $('.pay_update').on('click', function() {
   var customer_id = $(this).attr('id')
   var form_data = $('#pay_setting_'+customer_id).serialize();
   $.ajax({
   url: 'credit_recovery_pay.php',
   type: 'post',
   dataType: 'json',
   data: form_data,
   success: function(json) {
	   if(json == 'error') {
		   alert("Please enter the amount");
	   } else {
			alert("Your payment has been successfully processed!");
			location.reload();			
	   }
   }
   });
   //}
   }); 
   
   function Select2Tests(){
   $("#customer_id").select2();
   }
   $(document).ready(function() {
   // Load script of Select2 and run this
   LoadSelect2Script(Select2Tests);
   WinMove();
   });
</script>
		<script type="text/javascript">
			var site_url = "<?php echo getServerURL(); ?>";
			function deleteIt(id)
			{
			    if(id && confirm('Are you sure you want to delete this Insurance?'))
			    {
			        window.location.href = site_url+'/admin/insurance.php?id='+id+'&act=delete';
			    }
			}
		</script>
		<!-- Optionally, you can add Slimscroll and FastClick plugins.
			Both of these plugins are recommended to enhance the
			user experience. -->
	</body>
</html>