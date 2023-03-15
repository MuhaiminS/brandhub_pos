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
						Users
						<!--<small>Optional description</small>-->
					</h1>
					<ol class="breadcrumb">
						<li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
						<li class="active">Users</li>
					</ol>
				</section>
				<!-- Main content -->
				<section class="content container-fluid">
					<div class="row">
						<div class="col-xs-12">
							<div class="box">
								<div class="box-header">
									<h3 class="box-title">Manage Users</h3>
								</div>
								<!-- /.box-header -->
								<div class="box-body">
									<table id="example2" class="table table-bordered table-hover">
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
														if($rev_status == 'deactivate')
                                                                 {
                                                                  $add_class = 'btn-info';
                                                                 }
                                                                 else
                                                                 {
                                                                  $add_class = 'btn-success';
                                                                 }
                                                        														
														
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
														//echo "<td>".str_replace('_', ' ',str_replace(',', '<br>', $action_id))."</td>";
														echo "<td>";
															echo "<a href='users_add.php?id=".$user_id."&act=edit'  class='tip btn btn-warning btn-xs'><i class='fa fa-edit'></i></a>";
														//	echo " | <a href='javascript:void(0)' onclick='changeStatus(\"$rev_status\", \"$user_id\");'>".ucfirst($rev_status)."</a>";
														//	echo '<a href="javascript:void(0)" onclick="changeStatus(\''.$rev_status.'\', \''.$user_id.'\');" title="Change Status" class="tip btn '.$add_class.' btn-xs"><i class="fa fa-exchange"></i>'.ucfirst($rev_status).'</a>';
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
		<!-- Optionally, you can add Slimscroll and FastClick plugins.
			Both of these plugins are recommended to enhance the
			user experience. -->
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