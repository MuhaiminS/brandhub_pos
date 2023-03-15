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
    $run = mysqli_query($GLOBALS['conn'], $query);  
    while ($row = mysqli_fetch_array($run)) {
      $customer[$row['id']] = $row['cus_det'];
    }
    return $customer;
   }
   //$category_img_dir = "../category_images/";
   ?>
<!-- Start include Header -->
<?php include('header.php'); ?>
<!-- End include Header -->
<!--Start Container-->
<div id="main" class="container-fluid">
   <div class="row">
      <!-- START left bar -->
      <?php include('left.php'); ?>
      <!-- END left bar -->
      <!--Start Content-->
      <div id="content" class="col-xs-12 col-sm-10">
         <div id="about">
            <div class="about-inner">
               <h4 class="page-header">Open-source admin theme for you</h4>
               <p>DevOOPS team</p>
               <p>Homepage - <a href="http://devoops.me" target="_blank">http://devoops.me</a></p>
               <p>Email - <a href="mailto:devoopsme@gmail.com">devoopsme@gmail.com</a></p>
               <p>Twitter - <a href="http://twitter.com/devoopsme" target="_blank">http://twitter.com/devoopsme</a></p>
            </div>
         </div>
         <div class="preloader">
            <img src="img/devoops_getdata.gif" class="devoops-getdata" alt="preloader"/>
         </div>
         <div id="">
            <!-- CATEGORY START -->
            <div class="row">
               <div id="breadcrumb" class="col-xs-12">
                  <a href="#" class="show-sidebar">
                  <i class="fa fa-bars"></i>
                  </a>
                  <ol class="breadcrumb pull-left">
                     <li><a href="index.php">Dashboard</a></li>
                     <li><a href="javascript:void(0);">Manage Credit recovery</a></li>
                  </ol>
               </div>
            </div>
            <div class="row">
               <label class="control-label" style="margin-left:15px;">Search</label>
               <form action="credit_recovery_list.php">
                  <input type="hidden" class="form-control" name="page" id="page" value="<?php echo $page; ?>"/>
                  <div class="form-group search_val" style="margin-bottom:0px;">
                     <div class="col-sm-3">
                        <div class="row form-group">
                           <select class="" name="customer_id" id="customer_id">
                              <?php $customer = getCustomer();
                                 foreach($customer as $key=>$cus) { 
                                  $selected = ($key == $customer_id) ? "selected = selected" : "";?>
                              <option value="<?php echo $key; ?>"><?php echo $cus; ?></option>
                              <?php } ?>
                           </select>
                        </div>
                     </div>
                     <div class="col-sm-3">
                        <input type="submit" value="Search" class="aa-search-btn">	
                        <style>
                           .reset {										
                           border: 1px solid #B2BEB5;
                           color: #000;										
                           padding: 0.2em;
                           text-align: center;
                           text-decoration: none;										
                           }
                           .reset:hover {
                           border: 1px solid #0078d7;
                           text-decoration: none;
                           color: #0078d7;
                           }
                        </style>
                        <!-- <a href="manage_sale_orders.php" class="reset btn-default">Reset search</a> -->
                        <a href="credit_recovery_list.php" style="font-size: 20px;" class="aa-search-btn reset_btn" title="Reset"><i class="fa fa-repeat" ></i></a>	
                        <?php if($customer_id != '') { ?>
                        <span title="Excel" class="print2" style="font-size: 20px;"><a target="_blank" href="excel_export_credit.php?sale=Counter Sale&order_type=counter_sale&get_type=excel&customer_id=<?php echo $customer_id; ?>" class="print excel_me"><i class="fa fa-file-excel-o"></i></a></span>
                        <?php } else { ?>
                        <span title="Excel" class="print2" style="font-size: 20px;"><a target="_blank" href="excel_export_credit.php?sale=Counter Sale&order_type=counter_sale&get_type=excel" class="print excel_me"><i class="fa fa-file-excel-o"></i></a></span>
                        <?php } ?>
                     </div>
                  </div>
               </form>
               <div class="col-xs-12">
                  <div class="box">
                     <div class="box-header">
                        <div class="box-name">
                           <i class="fa fa-table"></i>
                           <span>Manage Credit recovery List</span>
                        </div>
                        <div class="box-icons">
                           <a class="collapse-link">
                           <i class="fa fa-chevron-up"></i>
                           </a>
                           <a class="expand-link">
                           <i class="fa fa-expand"></i>
                           </a>
                           <a class="close-link">
                           <i class="fa fa-times"></i>
                           </a>
                        </div>
                        <div class="no-move"></div>
                     </div>
                     <div class="box-content no-padding">
                        <table class="table table-striped table-bordered table-hover table-heading no-border-bottom">
                           <thead>
                              <tr>
                                 <th>#</th>
                                 <th>Customer detail</th>
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
                                 			$credit = $prow->credit;
                                 			$debit = $prow->debit;
                                 			$number = $prow->number;
                                 			echo "<tr>";
                                 			echo "<td>".($p+1)."</td>";
                                 			echo "<td>".safeTextOut($name).' - '.$number."</td>";
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
                  </div>
                  <?php //if($customer_id == '') {echo displayPaginationBelows($setLimit, $page, $customer_id);} ?>
               </div>
            </div>
            <!-- CATEGORY END -->
         </div>
      </div>
      <!--End Content-->
   </div>
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
				$credit = $prow->credit;
				$debit = $prow->debit;
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
                  <label  class="col-sm-3 control-label">Enter amount</label>
                  <div class="col-sm-9">
                     <input type="text" class="form-control" id="amount" name="amount" value="" required>
                  </div>
               </div>
               <div class="form-group">
                  <div class=" col-sm-12">
                     <button type="button" id="<?php echo $customer_id; ?>" class="btn btn-default pay_update" style="float: right;">Update</button>
                  </div>
               </div>
            </form>
         </div>
      </div>
   </div>
</div>
<?php } } } ?>
<!--End Container-->
<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<!--<script src="http://code.jquery.com/jquery.js"></script>-->
<script src="plugins/jquery/jquery.min.js"></script>
<script src="plugins/jquery-ui/jquery-ui.min.js"></script>
<!-- Include all compiled plugins (below), or include individual files as needed -->
<script src="plugins/bootstrap/bootstrap.min.js"></script>
<script src="plugins/justified-gallery/jquery.justifiedGallery.min.js"></script>
<script src="plugins/tinymce/tinymce.min.js"></script>
<script src="plugins/tinymce/jquery.tinymce.min.js"></script>
<script src="plugins/bootstrapvalidator/bootstrapValidator.min.js"></script>
<!-- All functions for this theme + document.ready processing -->
<script src="js/devoops.js"></script>
<script type="text/javascript">
   var site_url = "<?php echo getServerURL(); ?>";
   function deleteIt(id)
   {
       if(id && confirm('Are you sure you want to delete this category?'))
       {
           window.location.href = site_url+'/admin/credit_recovery_list.php?id='+id+'&act=delete';
       }
   }
</script>
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
   	alert("updated!");
   	location.reload();			
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
</body>
</html>