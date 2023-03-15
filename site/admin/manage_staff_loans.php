<?php 
session_start();
include("../functions.php");
include_once("../config.php");
chkAdminLoggedIn();
connect_dre_db();

if(isset($_GET['act']) && $_GET['act'] != '' && isset($_GET['id']) && $_GET['id'] > 0) {	
	$action = $_GET['act'];
	$loan_id = $_GET['id'];	
	if($action == 'delete') {
		$qry = "DELETE FROM staff_loans WHERE id = '$loan_id'";
		if(mysqli_query($GLOBALS['conn'],$qry)){
			redirect('manage_staff_loans.php?resp=succ');
		}
	}
	else if($action == 'activate') {
		$qry = "UPDATE drivers SET is_active = '1' WHERE id = '$driver_id'";
		if(mysql_query($qry)){
			redirect('manage_drivers.php?resp=succ');
		}
	}
	else if($action == 'deactivate') {
		$qry = "UPDATE drivers SET is_active = '0' WHERE id = $driver_id";
		if(mysqli_query($qry)){
			redirect('manage_drivers.php?resp=succ');
		}
	}
}

function getStaffsLoans()
{
	$qry="SELECT id, staff_id, SUM(CASE WHEN loan_type='credit' THEN loan_amount END) as credit, SUM(CASE WHEN loan_type='debit' THEN loan_amount END) as debit FROM staff_loans GROUP BY staff_id ORDER BY staff_id DESC";
	$result=mysqli_query($GLOBALS['conn'], $qry);
	$num=mysqli_num_rows($result);
	if($num>0)
	{
		return $result;
	}
	else
	return false;
}

function getStaffName($staff_id)
{
	$where = "WHERE id = '$staff_id'";
	$service = getnamewhere('drivers', 'name', $where);
	return $service;
}

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
							<li><a href="javascript:void(0);">Manage Staff Loans</a></li>
						</ol>
					</div>
				</div>
				
				<div class="row">
					<div class="col-xs-12">
						<div class="box">
							<div class="box-header">
								<div class="box-name">
									<i class="fa fa-table"></i>
									<span>Staff Loans List</span>
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
											<th>Id</th>
											<th>Staff Name</th>
											<th>Debit & Credit</th>
											<th>Balance to pay</th>
											<th>Action</th>
										</tr>
									</thead>
									<tbody>
										<?php
											$prs = getStaffsLoans();											
											if($prs != false) {
												$pcount = mysqli_num_rows($prs);
												if($pcount > 0) {
													for($p = 0; $p < $pcount; $p++) {
														$prow = mysqli_fetch_object($prs);
														$id = $prow->id;														
														$staff = ($prow->staff_id !='') ? getStaffName($prow->staff_id) : '';
														$credit = ($prow->credit != '') ? $prow->credit: 0;
														$debit = ($prow->debit != '') ? $prow->debit: 0;
														/*$loan_amount = $prow->loan_amount;
														$loan_type = $prow->loan_type;
														$loan_date = $prow->loan_date;*/
														//$status = ($prow->is_active) ? 'active' : 'deactive';
														//$rev_status = ($prow->is_active) ? 'deactivate' : 'activate';
														echo "<tr>";
														echo "<td>".safeTextOut($id)."</td>";
														//echo "<td>".safeTextOut($name)."</td>";
														echo "<td>".safeTextOut($staff)."</td>";
														echo "<td>Credit: ".$credit.", Debit: ".$debit."</td>";
														echo "<td>".($credit - $debit)."</td>";					
														echo "<td>";
															echo "<a href='add_staff_loan.php'>Credit / Debit Loan</a>";
															//echo " | <a href='javascript:void(0)' onclick='deleteIt($id);'>Delete</a>";
															//echo " | <a href='javascript:void(0)' onclick='changeStatus(\"$rev_status\", \"$id\");'>".ucfirst($rev_status)."</a>";
														echo "</td>";
														echo "</tr>";
													}
												}
											}
											else {
												echo "<tr>";
												echo "<td>No Staffs Salary found to list.</td>";
												echo "</tr>";
											}
										?>						
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
				<!-- CATEGORY END -->
			</div>
		</div>
		<!--End Content-->
	</div>
</div>
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
<script src="js/jquery-bizzpro-login.js"></script>
<script type="text/javascript">

var site_url = "<?php echo getServerURL(); ?>";
function deleteIt(id)
{
    if(id && confirm('Are you sure you want to delete this Staff loan?'))
    {
        window.location.href = site_url+'/admin/manage_staff_loans.php?id='+id+'&act=delete';
    }
}
function changeStatus(status, id)
{
	var msg = 'Are you sure you want to Activate this Staff?';
	if(status == 'deactivate')
		msg = 'Are you sure you want to De-activate this Staff?';
    if(id && confirm(msg))
    {
        window.location.href = site_url+'/admin/manage_drivers.php?id='+id+'&act='+status;
    }
}
</script>
</body>
</html>