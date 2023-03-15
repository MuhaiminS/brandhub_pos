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
			redirect('staff_loans.php?resp=succ');
		}
	}
	else if($action == 'activate') {
		$qry = "UPDATE drivers SET is_active = '1' WHERE id = '$driver_id'";
		if(mysql_query($qry)){
			redirect('drivers.php?resp=succ');
		}
	}
	else if($action == 'deactivate') {
		$qry = "UPDATE drivers SET is_active = '0' WHERE id = $driver_id";
		if(mysqli_query($qry)){
			redirect('drivers.php?resp=succ');
		}
	}
}

function getStaffsLoans()
{
	$qry="SELECT * from staff_loans Order BY id DESC";
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
						Staff Loans
						<!--<small>Optional description</small>-->
					</h1>
					<ol class="breadcrumb">
						<li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
						<li class="active">Staff Loans</li>
					</ol>
				</section>
				<!-- Main content -->
				<section class="content container-fluid">
					<div class="row">
						<div class="col-xs-12">
							<div class="box">
								<div class="box-header">
									<h3 class="box-title">Staff Loans</h3>
								</div>
								<!-- /.box-header -->
								<div class="box-body">
									<!-- Success msg display -->
									<?php include("common/info.php"); ?>
									<table id="example2" class="table table-bordered table-hover">
										<thead>
										<tr>
											<th>Id</th>
											<th>Staffs</th>
											<th>Credit/Debit</th>
											<th>Loan Amount</th>											
											<th>Date</th>
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
														$loan_amount = $prow->loan_amount;
														$loan_type = $prow->loan_type;
														$loan_date = $prow->loan_date;
														//$status = ($prow->is_active) ? 'active' : 'deactive';
														//$rev_status = ($prow->is_active) ? 'deactivate' : 'activate';
														echo "<tr>";
														echo "<td>".safeTextOut($id)."</td>";
														//echo "<td>".safeTextOut($name)."</td>";
														echo "<td>".safeTextOut($staff)."</td>";
														echo "<td>".safeTextOut($loan_type)."</td>";
														echo "<td>".safeTextOut($loan_amount)."</td>";														
														echo "<td>".safeTextOut($loan_date)."</td>";
														echo "<td>";
															echo "<a href='staff_loans_add.php?id=".$id."&act=edit' class='tip btn btn-warning btn-xs'><i class='fa fa-edit'></i>
															 <a href='javascript:void(0)' onclick='deleteIt($id);' class='tip btn btn-danger btn-xs'><i class='fa fa-trash-o'></i></a>";
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
			<!-- /.content-wrapper -->
			<?php include("common/footer.php"); ?>
			<?php include("common/sidebar-right.php"); ?>
		</div>
		<!-- ./wrapper -->
		<!-- REQUIRED JS SCRIPTS -->
		<?php include("common/footer-scripts.php"); ?>
<script type="text/javascript">

var site_url = "<?php echo getServerURL(); ?>";
function deleteIt(id)
{
    if(id && confirm('Are you sure you want to delete this Staff loan?'))
    {
        window.location.href = site_url+'/admin/staff_loans.php?id='+id+'&act=delete';
    }
}
function changeStatus(status, id)
{
	var msg = 'Are you sure you want to Activate this Staff?';
	if(status == 'deactivate')
		msg = 'Are you sure you want to De-activate this Staff?';
    if(id && confirm(msg))
    {
        window.location.href = site_url+'/admin/drivers.php?id='+id+'&act='+status;
    }
}
</script>
		<!-- Optionally, you can add Slimscroll and FastClick plugins.
			Both of these plugins are recommended to enhance the
			user experience. -->
	</body>
</html>