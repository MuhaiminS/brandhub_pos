<?php 
session_start();
include("../functions.php");
include_once("../config.php");
chkAdminLoggedIn();
connect_dre_db();

if(isset($_GET['act']) && $_GET['act'] != '' && isset($_GET['id']) && $_GET['id'] > 0) {	
	$action = $_GET['act'];
	$user_id = $_GET['id'];	
	if($action == 'delete') {
		$qry = "DELETE FROM users WHERE id = '$user_id'";
		if(mysqli_query($GLOBALS['conn'], $qry)){
			redirect('manage_user.php?resp=succ');
		}
	}
	else if($action == 'activate') {
		$qry = "UPDATE users SET is_active = '1' WHERE id = '$user_id'";
		if(mysqli_query($GLOBALS['conn'], $qry)){
			redirect('manage_user.php?resp=succ');
		}
	}
	else if($action == 'deactivate') {
		$qry = "UPDATE users SET is_active = '0' WHERE id = $user_id";
		if(mysqli_query($GLOBALS['conn'], $qry)){
			redirect('manage_user.php?resp=succ');
		}
	}
}

function getUsers()
{
	$qry="SELECT U.id AS user_id, R.title, U.* FROM users AS U LEFT JOIN users_role AS R ON U.role_id = R.id ORDER BY U.id ASC";
	//echo $qry;
	$result=mysqli_query($GLOBALS['conn'], $qry);
	$num=mysqli_num_rows($result);
	if($num>0)
	{
		return $result;
	}
	else
	return false;
}
function getManufacturingUnitName($manufacturing_unit_id)
{
	$where = "WHERE id = '$manufacturing_unit_id'";
	$manufacturing_unit = getnamewhere('locations_manufacturing_units', 'name', $where);
	return $manufacturing_unit;
}
function getShopName($shop_id)
{
	$where = "WHERE id = '$shop_id'";
	$service = getnamewhere('locations_shops', 'shop_name', $where);
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
							<li><a href="javascript:void(0);">Manage Users</a></li>
						</ol>
					</div>
				</div>
				
				<div class="row">
					<div class="col-xs-12">
						<div class="box">
							<div class="box-header">
								<div class="box-name">
									<i class="fa fa-table"></i>
									<span>Users List</span>
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
											<th>User id</th>
											<th>Username</th>
											<th>Phone</th>
											<th>Role</th>
											<th>Name</th>
											<th>Email</th>
											<th>Action</th>
										</tr>
									</thead>
									<tbody>
										<?php
											$prs = getUsers();											
											if($prs != false) {
												$pcount = mysqli_num_rows($prs);
												if($pcount > 0) {
													for($p = 0; $p < $pcount; $p++) {
														$prow = mysqli_fetch_object($prs);
														$user_id = $prow->user_id;
														$user_name = $prow->user_name;
														$phone = $prow->phone;
														$role = $prow->title;
														$name = $prow->first_name.' '.$prow->last_name;
														$email = $prow->email;
														$status = ($prow->is_active) ? 'active' : 'deactive';
														$rev_status = ($prow->is_active) ? 'deactivate' : 'activate';
														$name_manf = getManufacturingUnitName($prow->manufacturing_unit_id);
														$shop_name = getShopName($prow->shop_id);
														$action_id = $prow->user_action;
														echo "<tr>";
														echo "<td>".safeTextOut($user_id)."</td>";
														echo "<td>".safeTextOut($user_name)."</td>";
														echo "<td>".safeTextOut($phone)."</td>";
														echo "<td>".safeTextOut($role)."</td>";
														echo "<td>".safeTextOut($name)."</td>";
														echo "<td>".safeTextOut($email)."</td>";
														echo "<td>";
															echo "<a href='add_user.php?id=".$user_id."&act=edit'>Edit</a>";
														echo "</td>";
														echo "</tr>";
													}
												}
											}
											else {
												echo "<tr>";
												echo "<td>No users found to list.</td>";
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
    if(id && confirm('Are you sure you want to delete this user?'))
    {
        window.location.href = site_url+'admin/manage_user.php?id='+id+'&act=delete';
    }
}
function changeStatus(status, id)
{
	var msg = 'Are you sure you want to Activate this user?';
	if(status == 'deactivate')
		msg = 'Are you sure you want to De-activate this user?';
    if(id && confirm(msg))
    {
        window.location.href = site_url+'/admin/manage_user.php?id='+id+'&act='+status;
    }
}
</script>
</body>
</html>