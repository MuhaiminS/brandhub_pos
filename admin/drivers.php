<?php 
session_start();
include("../functions.php");
include_once("../config.php");
chkAdminLoggedIn();
connect_dre_db();

if(isset($_GET['act']) && $_GET['act'] != '' && isset($_GET['id']) && $_GET['id'] > 0) {	
	$action = $_GET['act'];
	$driver_id = $_GET['id'];	
	if($action == 'delete') {
		$qry = "DELETE FROM drivers WHERE id = '$driver_id'";
		if(mysqli_query($GLOBALS['conn'],$qry)){
			redirect('drivers.php?resp=deletesucc');
		}
	}
	else if($action == 'activate') {
		$qry = "UPDATE drivers SET is_active = '1' WHERE id = '$driver_id'";
		//echo $qry;
		if(mysqli_query($GLOBALS['conn'], $qry)){
			redirect('drivers.php?resp=deactivatedsucc');
		}
	}
	else if($action == 'deactivate') {
		$qry = "UPDATE drivers SET is_active = '0' WHERE id = '$driver_id'";
		//echo $qry;
		if(mysqli_query($GLOBALS['conn'], $qry)){
			redirect('drivers.php?resp=activatedsucc');
		}
	}
}

function getdrivers()
{
	$qry="SELECT * from drivers Order BY id DESC";
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
	$service = getnamewhere('locations_manufacturing_units', 'name', $where);
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
        Drivers
        <!--<small>Optional description</small>-->
      </h1>
      <ol class="breadcrumb">
        <li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Drivers</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content container-fluid">

      <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-header">
              <h3 class="box-title">Manage Drivers</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
			<?php include("common/info.php"); ?>			
              <table id="example2" class="table table-bordered table-hover">
                <thead>
                <tr>
                  <th>#</th>
                  <th>Name</th>
				  <th>Phone</th>
				  <th>Date of Joining</th>
                  <th>Action</th>
                </tr>
                </thead>
              <tbody>
				<?php
					$prs = getdrivers();											
					if($prs != false) {
						$pcount = mysqli_num_rows($prs);
						if($pcount > 0) {
							for($p = 0; $p < $pcount; $p++) {
								$prow = mysqli_fetch_object($prs);
								$id = $prow->id;
								//$manufacturing = ($prow->manufacturing_unit_id !='') ? getManufacturingUnitName($prow->manufacturing_unit_id) : '';
								$name = $prow->name;
								$phone = $prow->phone;
								$doj = $prow->doj;
								//$status = ($prow->is_active) ? 'active' : 'deactive';
								$rev_status = ($prow->is_active) ? 'deactivate' : 'activate';
								if($rev_status == 'deactivate')
									{
									 $add_class = 'btn-info';
									}
									else
									{
									 $add_class = 'btn-success';
									}
								echo "<tr>";
								echo "<td>".safeTextOut($id)."</td>";
								echo "<td>".safeTextOut($name)."</td>";														
								echo "<td>".safeTextOut($phone)."</td>";
								echo "<td>".safeTextOut($doj)."</td>";
								echo "<td>";									
									echo '<div class="text-center">';
									  echo '<div class="btn-group">';
										echo '<a href="drivers_add.php?id='.$id.'&act=edit" title="Edit Driver" class="tip btn btn-warning btn-xs"><i class="fa fa-edit"></i></a>';
										echo '<a href="javascript:void(0)" onclick="changeStatus(\''.$rev_status.'\', \''.$id.'\');" title="Change Status" class="tip btn '.$add_class.' btn-xs"><i class="fa fa-exchange"></i>'.ucfirst($rev_status).'</a>';
										echo '<a href="javascript:void(0)" onclick="deleteIt('.$id.');" title="Delete Driver" class="tip btn btn-danger btn-xs"><i class="fa fa-trash-o"></i></a>';
									  echo "</div>";
									echo "</div>";
								echo "</td>";

								echo "</tr>";
							}
						}
					}
					else {
						echo "<tr>";
						echo "<td>No drivers found to list.</td>";
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

	function deleteIt(id)
{
    if(id && confirm('Are you sure you want to delete this Driver?'))
    {
        window.location.href = site_url+'/admin/drivers.php?id='+id+'&act=delete';
	
    }
}

function changeStatus(status, id)
{
	var msg = 'Are you sure you want to De-activate this Driver?';
	if(status == 'deactivate')
		msg = 'Are you sure you want to activate this Driver?';
    if(id && confirm(msg))
    {
        window.location.href = site_url+'/admin/drivers.php?id='+id+'&act='+status;
    }
}
</script>
</body>
</html>