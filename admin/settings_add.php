<?php 
	session_start();
	include("../functions.php");
	include_once("../config.php");
	chkAdminLoggedIn();
	connect_dre_db();
	$CLIENT_LOGO = '';
	$action = 'add';
	
	if($_POST) {
		foreach($_POST as $key => $ps) {
			$qry = "UPDATE settings SET set_value = '".$ps."' WHERE set_name = '$key'";
			if(mysqli_query($GLOBALS['conn'],$qry)){		
				//header('Location: manage_page.php?resp=updatesucc');
			}
		}
		//Image to string
		$CLIENT_LOGO = $_FILES['CLIENT_LOGO']['name'];
	    $image_tmp = $_FILES['CLIENT_LOGO']['tmp_name'];
		if (file_exists($_FILES['CLIENT_LOGO']['tmp_name'])) {
		    $CLIENT_LOGO = $_FILES['CLIENT_LOGO']['name'];
	        $image_tmp = $_FILES['CLIENT_LOGO']['tmp_name'];
		    $data = file_get_contents($image_tmp);
	        $CLIENT_LOGO = base64_encode($data);
	    	//print_r($CLIENT_LOGO); die;
				$qry = "UPDATE settings SET set_value = '".$CLIENT_LOGO."' WHERE set_name = 'CLIENT_LOGO'";
				if(mysqli_query($GLOBALS['conn'], $qry)){
				}
		}
		
			
		redirect('settings_add.php');
	}
	
	$query = "SELECT * FROM settings";
	$result = mysqli_query($GLOBALS['conn'], $query);
	if ($result) {
		$result_arr = array();
		while ($row = mysqli_fetch_assoc($result)) {
		   $result_arr[] = $row;			
		}
	}
	//echo '<pre>'; print_r($result_arr);
	foreach($result_arr as $edit_row) {//echo $edit_row['set_name']; die;
		$set_name[] = $edit_row['set_name'];
		$set_value[] = $edit_row['set_value'];
	}
	$final_array = array_combine($set_name, $set_value);
	//echo '<pre>'; print_r($final_array);
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
						Add Settings
						<!--<small>Optional description</small>-->
					</h1>
					<ol class="breadcrumb">
						<li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
						<li><a href="settings_add.php"></a></li>
						<li class="active">Add Setting</li>
					</ol>
				</section>
				<!-- Main content -->
				<section class="content container-fluid">
					<!-- SELECT2 EXAMPLE -->
					<div class="box box-default">
						<div class="box-header with-border">
							<h3 class="box-title">Please fill in the details below</h3>
							<div class="box-tools pull-right">
								<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
								<button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
							</div>
						</div>
						<!-- /.box-header -->
						<div class="box-body">
							<div class="row">
								<form action="" method="post" id="tableform" enctype="multipart/form-data">
									<div class="col-md-6">
										<div class="form-group">
											<label>Client address</label>
											<input type="text" class="form-control" name="CLIENT_ADDRESS" value="<?php echo $final_array['CLIENT_ADDRESS']; ?>" />
										</div>
										<div class="form-group">
											<label>Shop number</label>
											<input type="text" class="form-control"  name="CLIENT_NUMBER"  value="<?php echo $final_array['CLIENT_NUMBER']; ?>"/>
										</div>
										<!-- <div class="form-group">
											<label>Client TAX</label>
											<input type="text" class="form-control"  name="CLIENT_WEBSITE"  value="<?php //echo $final_array['CLIENT_WEBSITE']; ?>" />
										</div> -->
										<div class="form-group">
											<label>Recipit pre</label>
											<input type="text" class="form-control"  name="RECIPT_PRE"  value="<?php echo $final_array['RECIPT_PRE']; ?>" />
											<p style="font-style: italic;">(ex: CLT-)</p>
										</div>
										<!-- <div class="form-group">
											<label class="col-sm-3 control-label">Is this Direct Print.?</label>
												<select class="form-control" name="DIRECT_PRINT" id="DIRECT_PRINT">	
													 <option value=''>---Select---</option> 
													<option value="yes" <?php //if($final_array['DIRECT_PRINT'] == 'yes') { echo "Selected"; } ?>>Yes</option>
													<option value="no" <?php //if($final_array['DIRECT_PRINT'] == 'no') { echo "Selected"; } ?>>No</option>
												</select>
										</div> -->
										<div class="form-group">
											<label>Currency</label>
											<input type="text" class="form-control"  name="CURRENCY"  value="<?php echo $final_array['CURRENCY']; ?>" />
											<p style="font-style: italic;">(ex: INR)</p>
										</div>
										<div class="form-group">
											<label>Bill footer</label>
											<input type="text" class="form-control"  name="BILL_FOOTER"  value="<?php echo $final_array['BILL_FOOTER']; ?>" />
										</div>
										<!-- <div class="form-group">
											<label>SMS Owner number</label>
											<input type="text" class="form-control"  name="OWNER_NUM"  value="<?php// echo $final_array['OWNER_NUM']; ?>" />
										</div> -->
										<!-- /.form-group -->
										<div class="form-group">
											<button type="submit" class="btn btn-primary">Submit</button>
										</div>
									</div>
									<!-- /.col -->
									<div class="col-md-6">
										<div class="form-group">
											<label for="exampleInputFile">Company logo</label>
											<input type="file" id="CLIENT_LOGO" name="CLIENT_LOGO">
											<!--  <p class="help-block">Please upload License.</p>-->
										</div>
									</div>
								</form>
								<!-- /.col -->
								<!-- /.col -->
							</div>
							<!-- /.row -->
						</div>
						<!-- /.box-body -->
						<div class="box-footer">
							<!--Visit <a href="https://select2.github.io/">Select2 documentation</a> for more examples and information about
								the plugin.-->
						</div>
					</div>
					<!-- /.box -->
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
			$('#tableform').bootstrapValidator({
			message: 'This value is not valid',
			fields: {
			category_title: {
				message: 'The category title is not valid',
				validators: {
					notEmpty: {
						message: 'The category title is required and can\'t be empty'
					},
					stringLength: {
						min: 3,
						max: 100,
						message: 'The category title must be more than 3 and less than 30 characters long'
					}
				}
			},
					table_no: {
				validators: {
					notEmpty: {
						message: 'The table number is required'
					}
				}
			},
				   no_of_seats: {
					validators: {
						notEmpty: {
							message: 'The no of seat is required'
						}
					}
				},
						floor_id: {
						validators: {
							notEmpty: {
								message: 'The floor  is required'
							}
						}
					},
				
				}
			});
		</script>
	</body>
</html>