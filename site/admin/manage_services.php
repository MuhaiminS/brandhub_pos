<?php 
session_start();
include("../functions.php");
include_once("../config.php");
chkAdminLoggedIn();
connect_dre_db();
if(isset($_GET['act']) && $_GET['act'] != '' && isset($_GET['id']) && $_GET['id'] > 0) {	
	$action = $_GET['act'];
	$id = $_GET['id'];
	if($action == 'delete') {
		//$qry="DELETE FROM items WHERE id = $id";
		$qry = "UPDATE items SET active = '0' WHERE id = $id";
		if(mysql_query($qry)){
			redirect('manage_services.php?resp=succ');
		}
	}
}

function getcategoryName($category_id)
{
	$where = "WHERE id = '$category_id'";
	$category = getnamewhere('category', 'category_title', $where);
	return $category;
}

$name = (isset($_GET['name']) && $_GET['name'] !='') ? $_GET['name'] : '';
$category = (isset($_GET['category']) && $_GET['category'] !='') ? $_GET['category'] : '';

function getItemsPost($name, $category)
{	
	$qry="SELECT i.id,i.price,i.active,i.name,i.arabic_name,i.car_ids FROM items AS i WHERE i.active = '1'";	
	if($name != ''){
		$qry .=" AND i.name LIKE '%$name%'";
	}
	$qry .=" ORDER BY i.id DESC";
	//echo $qry;
	$result=mysql_query($qry);
	$num=mysql_num_rows($result);
	//echo "total result ".$num;
	if($num>0)
	{
		return $result;
	}
	else
	return false;
}
$items_img_dir = "../item_images/";

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
							<li><a href="javascript:void(0);">Manage Services</a></li>
						</ol>
					</div>
				</div>
				
				<div class="row">				
					<div class="col-xs-12">
						<div class="box">
							<div class="box-header">
								<div class="box-name">
									<i class="fa fa-table"></i>
									<span>Manage Services List</span>
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
											<th>Service Name</th>
											<th>Service Cost</th>											
											<th>Cars</th>
											<th>Action</th>
										</tr>
									</thead>
									<tbody>
										<?php
											$prs = getItemsPost($name, $category);											
											if($prs != false) {
												$pcount = mysql_num_rows($prs);
												if($pcount > 0) {
													for($p = 0; $p < $pcount; $p++) {
														$prow = mysql_fetch_object($prs);														
														$id = $prow->id;
														$name = $prow->name;
														$arabic_name = (isset($prow->arabic_name) && $prow->arabic_name !='') ?  "(".safeTextOut($prow->arabic_name).")" : '';
														$price = $prow->price;
														$car_ids = $prow->car_ids;
														echo "<tr>";														
														echo "<td>".$id."</td>";
														echo "<td>".safeTextOut($name).$arabic_name."</td>";
														echo "<td>".$price."</td>";
														echo "<td>".$car_ids."</td>";
														echo "<td><a href='add_services.php?id=".$id."&act=edit'>Edit</a> | <a href='javascript:void(0)' onclick='deleteIt($id);'>Delete</a></td>";
														echo "</tr>";
													}
												}
											}
											else {
												echo "<tr>";
												echo "<td>No services to list.</td>";
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
    if(id && confirm('Are you sure you want to delete this Item?'))
    {
        window.location.href = site_url+'/admin/manage_services.php?id='+id+'&act=delete';
    }
}
</script>
</body>
</html>