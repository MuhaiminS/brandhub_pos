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
		if(mysqli_query($GLOBALS['conn'], $qry)){
			redirect('manage_items.php?resp=succ');
		}
	}
}

function getcategoryName($category_id)
{
	$where = "WHERE id = '$category_id'";
	$category = getnamewhere('category', 'category_title', $where);
	return $category;
}

function getCategoryList()
{
	$service = array();
	$query="SELECT * FROM item_category WHERE active != '0' ORDER BY category_title ASC";
	$run = mysqli_query($GLOBALS['conn'], $query);
	while($row = mysqli_fetch_array($run)) {
		$cat_id = $row['id'];
		$service[$cat_id]['cat_id'] = $row['id'];
		$service[$cat_id]['category_title'] = $row['category_title'];
	}
	return $service;	

}

if(isset($_GET["page"])) {
	$page = (int)$_GET["page"];
} else {
	$page = 1;
}
$setLimit = 50;
$pageLimit = ($page * $setLimit) - $setLimit;

$name = (isset($_GET['name']) && $_GET['name'] !='') ? $_GET['name'] : '';
$category = (isset($_GET['category']) && $_GET['category'] !='') ? $_GET['category'] : '';

function getItemsPost($name, $category,$setLimit ,$pageLimit)
{	
	$qry="SELECT i.id,i.price,i.active,i.name,i.other_name,i.image,i.cat_id,c.category_title, i.stock FROM items AS i LEFT JOIN item_category AS c ON c.id = i.cat_id WHERE i.active = '1' AND c.active = '1'";
	if($category != ''){
		$qry .=" AND c.id = $category";
	}
	if($name != ''){
		$qry .=" AND i.name LIKE '%$name%'";
	}
	$qry .=" ORDER BY i.id DESC LIMIT $pageLimit, $setLimit";
	//echo $qry;
	$result=mysqli_query($GLOBALS['conn'], $qry);
	//$num=mysqli_num_rows($result);
	//echo "total result ".$num;
	if($result)
	{
		return $result;
	}
	else
	return false;
}
$items_img_dir = "../item_images/";

function displayPaginationBelows($per_page,$page, $name, $category) {
    $page_url="?";	
	$query="SELECT count(*) as totalCount FROM items AS i LEFT JOIN item_category AS c ON c.id = i.cat_id WHERE i.active = '1'";
	if($category != ''){
		$query .=" AND c.id = $category";
	}
	if($name != ''){
		$query .=" AND i.name LIKE '%$name%'";
	}
	//print_r($query);exit;
	$rec = mysqli_fetch_array(mysqli_query($GLOBALS['conn'], $query));
	$total = $rec['totalCount'];
	$adjacents = "2";
	$page = ($page == 0 ? 1 : $page); 
	$start = ($page - 1) * $per_page; 
	$prev = $page - 1; 
	$next = $page + 1;
	$setLastpage = ceil($total/$per_page);
	$lpm1 = $setLastpage - 1;
	$setPaginate = "";
	if($setLastpage > 1)
	{  
		$setPaginate .= "<ul class='setPaginate'>";
				$setPaginate .= "<li class='setPage'>Page $page of $setLastpage</li>";
		if ($setLastpage < 7 + ($adjacents * 2))
		{  
			for ($counter = 1; $counter <= $setLastpage; $counter++)
			{
				if ($counter == $page)
					$setPaginate.= "<li><a class='current_page'>$counter</a></li>";
				else
					$setPaginate.= "<li><a href='{$page_url}page=$counter&name=$name&category=$category'>$counter</a></li>";
			}
		}
		elseif($setLastpage > 5 + ($adjacents * 2))
		{
			if($page < 1 + ($adjacents * 2)) 
			{
				for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++)
				{
					if ($counter == $page)
						$setPaginate.= "<li><a class='current_page'>$counter</a></li>";
					else
						$setPaginate.= "<li><a href='{$page_url}page=$counter&name=$name&category=$category'>$counter</a></li>";
				}
				$setPaginate.= "<li class='dot'>...</li>";
				$setPaginate.= "<li><a href='{$page_url}page=$lpm1&name=$name&category=$category'>$lpm1</a></li>";
				$setPaginate.= "<li><a href='{$page_url}page=$setLastpage&name=$name&category=$category'>$setLastpage</a></li>"; 
			}
			elseif($setLastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2))
			{
				$setPaginate.= "<li><a href='{$page_url}page=1&name=$name&category=$category'>1</a></li>";
				$setPaginate.= "<li><a href='{$page_url}page=2&name=$name&category=$category'>2</a></li>";
				$setPaginate.= "<li class='dot'>...</li>";
				for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++)
				{
					if ($counter == $page)
						$setPaginate.= "<li><a class='current_page'>$counter</a></li>";
					else
						$setPaginate.= "<li><a href='{$page_url}page=$counter&name=$name&category=$category'>$counter</a></li>";
				}
				$setPaginate.= "<li class='dot'>..</li>";
				$setPaginate.= "<li><a href='{$page_url}page=$lpm1&name=$name&category=$category'>$lpm1</a></li>";
				$setPaginate.= "<li><a href='{$page_url}page=$setLastpage&name=$name&category=$category'>$setLastpage</a></li>"; 
			}
			else
			{
				$setPaginate.= "<li><a href='{$page_url}page=1&name=$name&category=$category'>1</a></li>";
				$setPaginate.= "<li><a href='{$page_url}page=2&name=$name&category=$category'>2</a></li>";
				$setPaginate.= "<li class='dot'>..</li>";
				for ($counter = $setLastpage - (2 + ($adjacents * 2)); $counter <= $setLastpage; $counter++)
				{
					if ($counter == $page)
						$setPaginate.= "<li><a class='current_page'>$counter</a></li>";
					else
						$setPaginate.= "<li><a href='{$page_url}page=$counter&name=$name&category=$category'>$counter</a></li>";
				}
			}
		}
		if ($page < $counter - 1){
			$setPaginate.= "<li><a href='{$page_url}page=$next&name=$name&category=$category'>Next</a></li>";
			$setPaginate.= "<li><a href='{$page_url}page=$setLastpage&name=$name&category=$category'>Last</a></li>";
		}else{
			$setPaginate.= "<li><a class='current_page'>Next</a></li>";
			$setPaginate.= "<li><a class='current_page'>Last</a></li>";
		}
		$setPaginate.= "</ul>\n"; 
	}
	return $setPaginate;
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
							<li><a href="javascript:void(0);">Manage Products</a></li>
						</ol>
					</div>
				</div>
				
				<div class="row">
					<label class="control-label" style="margin-left:15px;">Search</label>
					<form action="manage_items.php">
					<input type="hidden" class="form-control" name="page" id="page" value="<?php echo $page; ?>"/>
					<div class="form-group" style="margin-bottom:0px;">	
						<div class="col-sm-2">					
							<input type="text" class="form-control" name="name" id="name" placeholder="Product Name" value="<?php echo $name; ?>"/>					
							<span id="loader"></span>
						</div>
						<div class="col-sm-2">
							
							<select name="category" id="category" class="form-control">
								<option value="">  --Category--  </option>
								<?php $cat_list = getCategoryList();
								foreach ($cat_list as $cat)
								{ 
									?><option value="<?php echo $cat['cat_id']; ?>" <?php echo ($category == $cat['cat_id']) ? ' selected="selected"' : Null; ?> ><?php echo $cat['category_title']; ?></option><?php
								}
								?>
							</select>
							<span id="loader"></span>
								
							<!--<input type="text" class="form-control" name="category" id="category" placeholder="Category" value="<?php echo $category; ?>"/>					
							<span id="loader"></span> -->
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
							<a href="manage_items.php" class="reset btn-default">Reset search</a>
						</div>						
					</div>
					</form>
					<div class="col-xs-12">
						<div class="box">
							<div class="box-header">
								<div class="box-name">
									<i class="fa fa-table"></i>
									<span>Manage Products List</span>
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
											<th>Category</th>
											<th>Product Name</th>
											<th>Image</th>
											<th>Price</th>
											<th>Action</th>
										</tr>
									</thead>
									<tbody>
										<?php
											$prs = getItemsPost($name, $category,$setLimit ,$pageLimit);										
											if($prs != false) {
												$pcount = mysqli_num_rows($prs);
												if($pcount > 0) {
													for($p = 0; $p < $pcount; $p++) {
														$prow = mysqli_fetch_object($prs);														
														$id = $prow->id;
														$category_title = $prow->category_title;
														$name1 = $prow->name;
														//$arabic_name = (isset($prow->arabic_name) && $prow->arabic_name !='') ?  "(".safeTextOut($prow->arabic_name).")" : '';
														$price = $prow->price;
														$image = $prow->image;
														$stock = $prow->stock;
														//$category = getcategoryName($cat_id);
														echo "<tr>";														
														echo "<td>".$id."</td>";
														echo "<td>".$category_title."</td>";
														echo "<td>".safeTextOut($name1)."</td>";														
														echo "<td><img src=\"".$items_img_dir.$image."\" width=\"100\" height=\"100\"  alt=\"".$name."\" /></td>";
														echo "<td>".$price."</td>";
														//echo "<td>".$stock."</td>";
														echo "<td><a href='add_items.php?id=".$id."&act=edit'>Edit</a> | <a href='javascript:void(0)' onclick='deleteIt($id);'>Delete</a> | <a style='cursor:pointer;' data-toggle='modal' data-target='#settinsModal_$id'>Stock</a></td>";
														echo "</tr>";
													}
												}
											}
											else {
												echo "<tr>";
												echo "<td>No Items found to list.</td>";
												echo "</tr>";
											}
										?>						
									</tbody>
								</table>
							</div>
						</div>
						<?php echo displayPaginationBelows($setLimit,$page, $name, $category); ?>
					</div>
				</div>
				<!-- CATEGORY END -->
			</div>
		</div>
		<!--End Content-->
	</div>
</div>
<?php 
$prs = getItemsPost($name, $category,$setLimit ,$pageLimit);
//}										
if($prs != false) {
	$pcount = mysqli_num_rows($prs);
	if($pcount > 0) {
		for($p = 0; $p < $pcount; $p++) {
			$prow = mysqli_fetch_object($prs);
			$product_id = $prow->id;
			$name = $prow->name;
			$stock = $prow->stock;
?>
<div id="settinsModal_<?php echo $product_id; ?>" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Stock update</h4>
      </div>
      <div class="modal-body">
	  <h3><?php echo $name; ?></h3>
		<form id="stock_update_<?php echo $product_id; ?>" class="form-horizontal" role="form">
		<input type="hidden" name="product_id" value="<?php echo $product_id;?>">
		<input type="hidden" name="stock_old" value="<?php echo $stock;?>">
                  <div class="form-group">
                    <label  class="col-sm-3 control-label">Stock In</label>
                    <div class="col-sm-9">
						<input readonly class="form-control" type="text" id="stock_old" value="<?php echo $stock;?>"/>
                    </div>
                  </div>
				  <div class="form-group">
                    <label  class="col-sm-3 control-label">Stock</label>
                    <div class="col-sm-9">
						<input class="form-control" type="text" id="stock_new" name="stock_new" value="" required/>
                    </div>
                  </div>
				  <div class="form-group">
                    <label  class="col-sm-3 control-label">Action type</label>
                    <div class="col-sm-9">
						<label for="add"><input class="" checked type="radio" id="add" name="action_type" value="add" /> Add</label><br/>
						<label for="sub"><input class="" type="radio" id="sub" name="action_type" value="sub" /> Minus</label>
                    </div>
                  </div>
                  <div class="form-group">
                    <div class=" col-sm-12">
                      <button type="button" id="<?php echo $product_id; ?>" class="btn btn-default stock_update" style="float: right;">Update</button>
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
<script src="js/jquery-bizzpro-login.js"></script>
<script type="text/javascript">

var site_url = "<?php echo getServerURL(); ?>";
function deleteIt(id)
{
    if(id && confirm('Are you sure you want to delete this Item?'))
    {
        window.location.href = site_url+'/admin/manage_items.php?id='+id+'&act=delete';
    }
}
</script>
<script>
	 $('.stock_update').on('click', function() {
	 var product_id = $(this).attr('id')
	 var form_data = $('#stock_update_'+product_id).serialize();
	 $.ajax({
		url: 'stock_update.php',
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
</script>
</body>
</html>