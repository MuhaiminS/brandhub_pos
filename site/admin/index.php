<?php 
session_start();
include("../functions.php");
include_once("../config.php");
connect_dre_db();
chkAdminLoggedIn();
$balance_recover = array();
$user_count = mysqli_fetch_array(mysqli_query($GLOBALS['conn'], "SELECT COUNT(*) as id FROM `users` WHERE `is_active`='1'"));
$users = $user_count[0];

$items_count = mysqli_fetch_array(mysqli_query($GLOBALS['conn'], "SELECT COUNT(*) as id FROM `items` WHERE `active`='1'"));
$items = $items_count[0];

$locations_shops = mysqli_fetch_array(mysqli_query($GLOBALS['conn'], "SELECT COUNT(*) as id FROM `locations_shops`"));
$shops = $locations_shops[0];

//$drivers_count = mysqli_fetch_array(mysqli_query($GLOBALS['conn'], 'SELECT COUNT(*) AS id FROM drivers WHERE `is_active` = "1"'));
//$drivers = $drivers_count[0];

$customers_count = mysqli_fetch_array(mysqli_query($GLOBALS['conn'], 'SELECT COUNT(*) AS customer_id FROM customer_details'));
$customers = $customers_count[0];

$debit = $credit = $balance = '0';
$result = mysqli_query($GLOBALS['conn'], "SELECT customer_id, name, number, SUM(CASE WHEN type='credit' THEN amount END) as credit, SUM(CASE WHEN type='debit' THEN amount END) as debit FROM credit_sale GROUP BY customer_id ORDER BY id DESC");
$num=mysqli_num_rows($result);
	//echo "total result ".$num;
if($num>0)
{	
	while($row = mysqli_fetch_array($result)) {
		//print_r($row);
		$debit	+= $row['debit'];
		$credit	+= $row['credit'];
		$balance += $credit - $debit;
	}
}
$site_url = getServerURL();
//echo $site_url; die;
?>

<?php include('header.php'); ?>
<link href="css/index.css" rel="stylesheet">
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
					<p>Donate - BTC 123Ci1ZFK5V7gyLsyVU36yPNWSB5TDqKn3</p>
				</div>
			</div>
			<div class="preloader">
				<img src="img/devoops_getdata.gif" class="devoops-getdata" alt="preloader"/>
			</div>
			<div id="">
				<!--Start Breadcrumb-->
				<div class="row">
					<div id="breadcrumb" class="col-xs-12">
						<a href="#" class="show-sidebar">
							<i class="fa fa-bars"></i>
						</a>
						<ol class="breadcrumb pull-left">
							<li><a href="index.php">Home</a></li>
							<li><a href="javascript:void(0);">Dashboard</a></li>
						</ol>
					</div>
				</div>
				<!--End Breadcrumb-->
				<!--Start Dashboard 1-->
				<div id="dashboard-header" class="row">
					<div class="col-xs-12 col-sm-4 col-md-5">
						<h3>Hello, <?php echo $_SESSION['user_name']; ?>!</h3>
					</div>	
				</div>
				<div class="col-lg-3 col-md-3 col-sm-6">
					<div class="tile">
						  <div class="tile-heading">Total Items <span class="pull-right"></span>
						  </div>
						  <div class="tile-body"><i class="fa fa-thumb-tack"></i>
							<h2 class="pull-right"><?php echo $items; ?></h2>
						  </div>
							<div class="tile-footer"><a href="<?php echo $site_url; ?>/admin/manage_items.php">View more...</a>
							</div>
					</div>
				</div>
				<div class="col-lg-3 col-md-3 col-sm-6">
					<div class="tile user">
						  <div class="tile-heading panel-user">Total Staffs <span class="pull-right"></span>
						  </div>
						  <div class="tile-body"><i class="fa fa-user"></i>
							<h2 class="pull-right"><?php echo $users; ?></h2>
						  </div>
							<div class="tile-footer3"><a href="<?php echo $site_url; ?>/admin/manage_drivers.php">View more...</a>
							</div>
					</div>
				</div>
				<div class="col-lg-3 col-md-3 col-sm-6">
					<div class="tile driver">
						  <div class="tile-heading panel-driver">Total Customers <span class="pull-right"></span>
						  </div>
						  <div class="tile-body"><i class="fa fa-users"></i>
							<h2 class="pull-right"><?php echo $customers; ?></h2>
						  </div>
							<div class="tile-footer2"><a href="#">View more...</a>
							</div>
					</div>
				</div>				
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
<!--<script src="plugins/tinymce/tinymce.min.js"></script>
<script src="plugins/tinymce/jquery.tinymce.min.js"></script>-->
<!-- All functions for this theme + document.ready processing -->
<script src="js/devoops.js"></script>
<script src="js/jquery-bizzpro-login.js"></script>
<script>
	function latestSale() {		
		$.ajax({
			url: 'latest_sale.php',
			type: 'GET',
			data: {},
			success:function(data){
				//alert(data);
				$('.latest_sales').empty();
				$('.latest_sales').append(data);
				return false;
			}
		});
	}
	setInterval(function(){ 
	   latestSale();
	},20000);
</script>
</body>
</html>