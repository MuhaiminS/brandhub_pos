<?php 
	session_start();
	include("../functions.php");
	include_once("../config.php");
	chkAdminLoggedIn();
	connect_dre_db();
	
	$cat_id = '';
	$parent_id = 0;
	$name = '';
	$price = '';
	$items_details = '';
	$weight = '';
	$unit = '';
	$image = '';
	$barcode_id = '';
	$cgst = '';
	$sgst = '';
	$stock = '';
	$manuf_date = '';
	$inward_date = '';
	$expiry_date = '';
	$action = 'add';
	$update_img_tbl = false;
	$items_img_dir = "../item_images/";
	
	//$barcode_id = randomString();
	
	if(isset($_POST['items_post'])) {
		$name = $_POST['name'];
		//$sgst = $_POST['sgst'];
	    $barcode_id = $_POST['barcode_id'];
	    $cat_id = $_POST['cat_id'];
		$unit = $_POST['unit'];
		$price = $_POST['price'];
		$weight = $_POST['weight'];
		$sgst = $_POST['sgst'];
		$cgst = $_POST['cgst'];
		//$manuf_date = $_POST['manuf_date'];
	
	//	$inward_date = $_POST['inward_date'];
		$image = $_FILES['image']['name'];
		
		if(isset($_POST['id']) && $_POST['id'] > 0) {
			$id = $_POST['id'];		
			$qry = "UPDATE items SET cat_id='$cat_id', barcode_id= '$barcode_id', name = '".safeTextIn($name)."', price = '".safeTextIn($price)."', weight = '".safeTextIn($weight)."', cgst = '".safeTextIn($cgst)."', sgst = '".safeTextIn($sgst)."',  unit = '".safeTextIn($unit)."' WHERE id = '$id'";
			// echo $qry; die;
			if(mysqli_query($GLOBALS['conn'], $qry)){
			}
		}
		else {
			/*$result_rad = mysqli_query($GLOBALS['conn'], "SELECT `barcode_id` FROM `items` WHERE `barcode_id` = '".$barcode_id."'");
			if(mysqli_num_rows($result_rad)) {
				$barcode_id = randomString();
				$result_rad_long= mysqli_query($GLOBALS['conn'], "SELECT `barcode_id` FROM `items` WHERE `barcode_id` = '".$barcode_id."'");
				if(mysqli_num_rows($result_rad_long)) {
					echo"<script>
					alert('Something went worng...');
					</script>";
				}
			} else {*/
				$stock = $_POST['stock'];
				$qry = "INSERT INTO items (cat_id, name, price, weight, unit, barcode_id, cgst,sgst,stock) VALUES ('$cat_id', '".safeTextIn($name)."', '".safeTextIn($price)."', '".safeTextIn($weight)."', '".safeTextIn($unit)."', '".safeTextIn($cgst)."','".safeTextIn($sgst)."','".safeTextIn($barcode_id)."', '$stock')";
				//echo $qry; die;
				if(mysqli_query($GLOBALS['conn'], $qry)){		
					$id = mysqli_insert_id($GLOBALS['conn']);
				}
			//}
		}
	
		if (file_exists($_FILES['image']['tmp_name'])) {		
			$userfile_name = "_items";
			$objectname = "image";
			if (isset($_FILES[$objectname]['name'])) {
				$path = $items_img_dir;
				$new_width = 340;
				$new_height = 330;
				$userfile_name = saveProductItemImage($userfile_name, $objectname, $path, $new_height, $new_width);
				$qry = "UPDATE items SET image = '$userfile_name' WHERE id = '$id'";
				if(mysqli_query($GLOBALS['conn'], $qry)){
				}			
			}
		}
			// die;
		redirect('products.php?resp=addsucc');
	}
	
	if(isset($_GET['act']) && $_GET['act'] != '' && isset($_GET['id']) && $_GET['id'] > 0) {	
		$action = $_GET['act'];
		$id = $_GET['id'];
		if($action == 'edit') {
			$edit_query = "SELECT * FROM items WHERE id = '$id'";
			$run_edit = mysqli_query($GLOBALS['conn'], $edit_query);		
			while ($edit_row = mysqli_fetch_array($run_edit)) {
				$id = $edit_row['id'];
				$cat_id = $edit_row['cat_id'];
				$name = $edit_row['name'];
				$price = $edit_row['price'];
				$sgst = $edit_row['sgst'];
				$image = $edit_row['image'];
				$cgst = $edit_row['cgst'];
				$barcode_id = $edit_row['barcode_id'];
				$unit = $edit_row['unit'];
				$weight = $edit_row['weight'];
				$stock = $edit_row['stock'];
				
				//$inward_date = $edit_row['inward_date'];
			}
		}
	}
	
	function getCategorieList()
	{
		$cat = array();
		$query = "SELECT * FROM item_category ORDER BY id ASC";
		$run = mysqli_query($GLOBALS['conn'], $query);
		while($row = mysqli_fetch_array($run)) {
			$cat_id = $row['id'];
			$cat[$cat_id] = $row['category_title'];
		}
		return $cat;	
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
						Add Products
						<!--<small>Optional description</small>-->
					</h1>
					<ol class="breadcrumb">
						<li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
						<li><a href="products.php">Products</a></li>
						<li class="active">Add Products</li>
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
								<div class="col-md-6 ">
									<form action="products_add.php" method="post" id="defaultForm" enctype="multipart/form-data">
										<input type="hidden" name="items_post" value="1" />
										<input type="hidden" name="id" id="id" value="<?php echo $id ?>" />        
										<div class="form-group">
											<label class="control-label">Name</label>
											<input type="text" class="form-control" name="name" id="name" value="<?php echo safeTextOut(htmlspecialchars($name)); ?>" required/>
										</div>
										<!--<div class="form-group">-->
										<!--	<label class="control-label">Other Name</label>-->
										<!--	<input type="text" class="form-control" name="other_name" id="other_name" value="<?php //echo safeTextOut(htmlspecialchars($other_name)); ?>"/>-->
										<!--</div>-->
										<!--<div class="form-group">-->
										<!--	<label class="control-label">Cost Price</label>-->
										<!--	<input type="text" class="form-control" name="cost_price" id="cost_price" value="<?php //echo safeTextOut(htmlspecialchars($cost_price)); ?>" required/>-->
										<!--</div>-->
										<div class="form-group">
											<label class="control-label">Price</label>
											<input type="text" class="form-control" name="price" id="price" value="<?php echo safeTextOut(htmlspecialchars($price)); ?>" required/>
										</div>
										<!-- <div class="form-group">
											<label class="control-label">Weight</label>
											<input type="text" class="form-control" name="weight" id="weight" value="<?php echo safeTextOut(htmlspecialchars($weight)); ?>" />
										</div>
										<div class="form-group">
											<label class="control-label">Unit</label>
											<select class="form-control" name="unit" id="unit">
												<option value="">-- Select a Unit --</option>
												<?php 
													// $unit_list = getUnitList();
													// foreach ($unit_list as $key => $value) {
													// 	$selected = ($key == $unit) ? "selected = selected" : "";
													// 	echo "<option value=\"".$key."\" ".$selected.">".ucfirst($value)."</option>";
													// }
													?>
											</select>
										</div> -->
										<div class="form-group">
											<label class="control-label">Category</label>
											<select class="form-control" name="cat_id" id="cat_id" required>
												<option value="">-- Select a Category --</option>
												<?php 
													$cat_list = getCategorieList();
													foreach ($cat_list as $key => $value) {
														$selected = ($key == $cat_id) ? "selected = selected" : "";
														echo "<option value=\"".$key."\" ".$selected.">".$value."</option>";
													}
													?>
											</select>
										</div>
										<div class="form-group">
											<label class="control-label" for="form-styles">Image</label>
											<div class="row">
												<div class="col-sm-12">
													<input type="file" class="form-control" name="image" />
													<p><small>Dimension: 340 X 330</small></p>
												</div>
											</div>
											<?php if(isset($action) && $action == 'edit') { ?>
											<div class="row">
												<div class="col-sm-12">
													<p><small>Image</small></p>
													<div>
														<img src="<?php echo $items_img_dir.$image; ?>" width="100" height="80" alt="<?php echo $name; ?>" />
													</div>
												</div>
											</div>
											<?php } ?>
										</div>
										<div class="form-group">
											<label class="control-label">Stock</label>
											<input type="text" <?php if(isset($action) && $action == 'edit') {echo "disabled";} ?> class="form-control" name="stock" id="stock" value="<?php echo safeTextOut(htmlspecialchars($stock)); ?>" />
										</div>
										<!-- <div class="form-group">
											<label class="control-label">CGST %</label>
											<input type="text" class="form-control" name="cgst" id="cgst" value="<?php// echo //safeTextOut(htmlspecialchars($cgst)); ?>" />
										</div>
										<div class="form-group">
											<label class="control-label">SGST %</label>
											<input type="text" class="form-control" name="sgst" id="sgst" value="<?php// echo //safeTextOut(htmlspecialchars($sgst)); ?>" />
										</div> -->
										<!-- <div class="form-group">
											<label class="control-label">Barcode num</label>
											<input type="text" class="form-control" name="barcode_id" id="barcode_id" value="<?php //echo safeTextOut(htmlspecialchars($barcode_id)); ?>" />
										</div> -->

										<div class="form-group">
											<?php 
												if(isset($action) && $action == 'edit') {
												?>
											<button type="submit" class="btn btn-primary">Update</button>
											<a href="products.php" class="btn btn-primary">Cancel</a>
											<?php } 
												else { 
												?>
											<button type="submit" class="btn btn-primary">Submit</button>
											<?php 
												} 
												?>
										</div>
									</form>
								</div>
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
			$(document).ready(function() {
			
			$('#defaultForm').bootstrapValidator({
			//        live: 'disabled',
			      message: 'This value is not valid',
			      feedbackIcons: {
			          valid: 'glyphicon glyphicon-ok',
			          invalid: 'glyphicon glyphicon-remove',
			          validating: 'glyphicon glyphicon-refresh'
			      },
			      fields: {
			          name: {
			              validators: {
			                  notEmpty: {
			                      message: 'The Product name is required and cannot be empty'
			                  }
			              }
			          },
			          price: {
			              validators: {
			                  notEmpty: {
			                      message: 'The Price Amount is required and cannot be empty'
			                  },
			                  regexp:{
			                  	regexp: /^\d*(\.\d{0,2})?$/,
			                  	message: 'The Price Amount is Numbers only'
			                  }
			
			              }
			          },
			        /*  username: {
			              message: 'The username is not valid',
			              validators: {
			                  notEmpty: {
			                      message: 'The username is required and cannot be empty'
			                  },
			                  stringLength: {
			                      min: 6,
			                      max: 30,
			                      message: 'The username must be more than 6 and less than 30 characters long'
			                  },
			                  regexp: {
			                      regexp: /^[a-zA-Z0-9_\.]+$/,
			                      message: 'The username can only consist of alphabetical, number, dot and underscore'
			                  },
			                  remote: {
			                      url: 'remote.php',
			                      message: 'The username is not available'
			                  },
			                  different: {
			                      field: 'password',
			                      message: 'The username and password cannot be the same as each other'
			                  }
			              }
			          },
			          email: {
			              validators: {
			                  emailAddress: {
			                      message: 'The input is not a valid email address'
			                  }
			              }
			          },
			          password: {
			              validators: {
			                  notEmpty: {
			                      message: 'The password is required and cannot be empty'
			                  },
			                  identical: {
			                      field: 'confirmPassword',
			                      message: 'The password and its confirm are not the same'
			                  },
			                  different: {
			                      field: 'username',
			                      message: 'The password cannot be the same as username'
			                  }
			              }
			          },
			          confirmPassword: {
			              validators: {
			                  notEmpty: {
			                      message: 'The confirm password is required and cannot be empty'
			                  },
			                  identical: {
			                      field: 'password',
			                      message: 'The password and its confirm are not the same'
			                  },
			                  different: {
			                      field: 'username',
			                      message: 'The password cannot be the same as username'
			                  }
			              }
			          },
			          birthday: {
			              validators: {
			                  date: {
			                      format: 'YYYY/MM/DD',
			                      message: 'The birthday is not valid'
			                  }
			              }
			          },*/
			          cat_id: {
			              validators: {
			                  notEmpty: {
			                      message: 'The Product Category is required'
			                  }
			              }
			          },
			          weight: {
			              validators: {
			                  regexp:{
			                  	regexp: /^\d*(\.\d{0,2})?$/,
			                  	message: 'The Weight Should be in Numbers only'
			                  }
			              }
			          },
			     /*     'languages[]': {
			              validators: {
			                  notEmpty: {
			                      message: 'Please specify at least one language you can speak'
			                  }
			              }
			          },
			          'programs[]': {
			              validators: {
			                  choice: {
			                      min: 2,
			                      max: 4,
			                      message: 'Please choose 2 - 4 programming languages you are good at'
			                  }
			              }
			          },*/
			         /* captcha: {
			              validators: {
			                  callback: {
			                      message: 'Wrong answer',
			                      callback: function(value, validator) {
			                          var items = $('#captchaOperation').html().split(' '), sum = parseInt(items[0]) + parseInt(items[2]);
			                          return value == sum;
			                      }
			                  }
			              }
			          }*/
			      }
			  });
			
			  // Validate the form manually
			  $('#validateBtn').click(function() {
			      $('#defaultForm').bootstrapValidator('validate');
			  });
			
			  $('#resetBtn').click(function() {
			      $('#defaultForm').data('bootstrapValidator').resetForm(true);
			  });
			
			});
		</script>
		</script>
	</body>
</html>