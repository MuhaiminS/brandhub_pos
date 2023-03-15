<?php 
	session_start();
	include("../functions.php");
	include_once("../config.php");
	chkAdminLoggedIn();
	connect_dre_db();
	
	$id = '';
	$trn_no = '';
	$supplier_id = '';
	$purchase_date = '';
	$company_name='';
	$reference_id='';
	$vat='';
	//$payment_options = '';
	//$total_amount = array();
	//$tax = array();
	//$credit_period = '';
	//$net_total = '';
	//$items_id= array();
	$qty = array();
	//$expiry_date = '';
	//$cost_price ='';
	$status = '';
	$action = 'add';
	//$update_img_tbl = false;
	$date_added = date("Y-m-d H:i:s");
	$date_updated = date("Y-m-d H:i:s");
	
	//$supplier_vat='';
	//$gross_total='';
	$payment_status = '';
	$unit_id = '';
	
	
	$items1 = getItemsList("ASC");
	
	if(isset($_POST['items_post'])) {
		
		//echo '<pre>'; print_r($_POST); die;
		//$trn_no = $_POST['trn_no'];
		$supplier_id= $_POST['supplier_id'];
		$company_name= $_POST['company_name'];
		$reference_id= $_POST['reference_id'];
		$purchase_date = date('Y-m-d',strtotime($_POST['purchase_date']));
		$status = $_POST['status'];
		$payment_status = $_POST['payment_status'];
		$unit_id = $_POST['unit_id'];
		//$payment_options = $_POST['payment_options'];
		//$supplier_vat = (isset($_POST['supplier_vat'])) ? $_POST['supplier_vat'] : '0';
		//$net_total = $_POST['net_total'];
		//$net_total_amount = 0;
		//if(!empty($net_total)) {
		//	foreach($net_total as $net) {
			////	$net_total_amount += $net;			
		//	}
			
		//}
		//$is_active = 1;
		$date_added = date("Y-m-d H:i:s");
		$date_updated = date("Y-m-d H:i:s");
		if(isset($_POST['id']) && $_POST['id'] > 0) {
			$id = $_POST['id'];		
			$qry1= "UPDATE purchase_orders SET supplier_id= '$supplier_id',company_name= '$company_name',purchase_date = '$purchase_date',
			reference_id = '".safeTextIn($reference_id)."',status= '".safeTextIn($status)."',date_updated = '$date_updated' WHERE id = '$id'";
			//echo $qry; die;
			if(mysqli_query($GLOBALS['conn'], $qry1)){
				$qry = "DELETE FROM purchase_order_items WHERE purchase_id = '$id'";
				if(mysqli_query($GLOBALS['conn'],$qry)){					
				}
			
				if(!empty($_POST['items_id'])) {
				foreach($_POST['items_id'] as $key => $value) {
					
					$items_id = $_POST['items_id'][$key];
					$items_name = $_POST['items_name'][$key];
					$unit_id = $_POST['unit_id'][$key];
					$unit_name = getUnitName($_POST['unit_id'][$key]);
					$qty = $_POST['qty'][$key];
					$stock = $_POST['stock'][$key];
					$unit_price = $_POST['unit_price'][$key];						
					$vat = $_POST['vat'][$key];
					$total_amount = $_POST['total_amount'][$key];					

					$qry1 = "INSERT INTO purchase_order_items(purchase_id,product_id,product_name,qty,stock,unit_price,tax,total_amount,unit_id,unit_name) VALUES ('$id','$items_id','".safeTextIn($items_name)."','".safeTextIn($qty)."','".safeTextIn($stock)."','".safeTextIn($unit_price)."','".safeTextIn($vat)."','".safeTextIn($total_amount)."', '".safeTextIn($unit_id)."', '".safeTextIn($unit_name)."')";		
					//echo $qry1; die;
					if(mysqli_query($GLOBALS['conn'], $qry1)){		
						$id1 = mysqli_insert_id($GLOBALS['conn']);
						//ADD Stock
						if($status == 'received') {							
							$sql = "SELECT * FROM item_price WHERE unit_id = '$unit_id' AND product_id = '$items_id'";								
							$item_details = mysqli_fetch_assoc(mysqli_query($GLOBALS['conn'], $sql));
							$item_id_i = $item_details['id'];
							$stock = $item_details['stock'];
							$stock_added = $stock + $qty;
							mysqli_query($GLOBALS['conn'], "UPDATE item_price SET stock = '$stock_added' WHERE id = '$item_id_i'");						
						}
					}
				}
				}
			}	
			
		}
		else {		
			$qry = "INSERT INTO `purchase_orders`( supplier_id, company_name, purchase_date, reference_id,status,date_added,date_updated) VALUES ('$supplier_id','$company_name','$purchase_date','$reference_id','".safeTextIn($status)."','$date_added','$date_updated')";
	
			if(mysqli_query($GLOBALS['conn'], $qry)){			
				$id = mysqli_insert_id($GLOBALS['conn']);
				
					if(!empty($_POST['items_id'])) {
					foreach($_POST['items_id'] as $key => $value) {
						
						$items_id = $_POST['items_id'][$key];
						$items_name = $_POST['items_name'][$key];
						$unit_id = $_POST['unit_id'][$key];
						$unit_name = getUnitName($_POST['unit_id'][$key]);
						$qty = $_POST['qty'][$key];
						$stock = $_POST['stock'][$key];
						$unit_price = $_POST['unit_price'][$key];						
						$vat = $_POST['vat'][$key];
						$total_amount = $_POST['total_amount'][$key];
						
						$qry1 = "INSERT INTO purchase_order_items(purchase_id,product_id,product_name,qty,stock,unit_price,tax,total_amount,unit_id,unit_name) VALUES ('$id','$items_id','".safeTextIn($items_name)."','".safeTextIn($qty)."','".safeTextIn($stock)."','".safeTextIn($unit_price)."','".safeTextIn($vat)."','".safeTextIn($total_amount)."', '".safeTextIn($unit_id)."', '".safeTextIn($unit_name)."')";		
						//echo $qry1; die;
						if(mysqli_query($GLOBALS['conn'], $qry1)){		
							$id1 = mysqli_insert_id($GLOBALS['conn']);
	
							//ADD Stock
							if($status == 'received') {
								$sql = "SELECT * FROM item_price WHERE unit_id = '$unit_id' AND product_id = '$items_id'";								
								$item_details = mysqli_fetch_assoc(mysqli_query($GLOBALS['conn'], $sql));
								$item_id_i = $item_details['id'];
								$stock = $item_details['stock'];
								$stock_added = $stock + $qty;
								mysqli_query($GLOBALS['conn'], "UPDATE item_price SET stock = '$stock_added' WHERE id = '$item_id_i'");
							}	
						}
					}
				}
			}		
		}	
	 // die;
	  redirect('purchases.php?resp=addsucc');
	}
	
	$purchase_items = array();
	if(isset($_GET['act']) && $_GET['act'] != '' && isset($_GET['id']) && $_GET['id'] > 0) {	
		$action = $_GET['act'];
		$id = $_GET['id'];
		if($action == 'edit') {
			$edit_query = "SELECT * FROM purchase_orders WHERE id = '$id'";
			$run_edit = mysqli_query($GLOBALS['conn'], $edit_query);		
			while ($edit_row = mysqli_fetch_array($run_edit)) {
				$id = $edit_row['id'];
				$company_name = $edit_row['company_name'];
				$supplier_id = $edit_row['supplier_id'];
				$purchase_date = $edit_row['purchase_date'];
				$reference_id = $edit_row['reference_id'];
				//$payment_options = $edit_row['payment_options'];
				//$credit_period = $edit_row['credit_period'];
				//$net_total = $edit_row['net_total'];
				$payment_status = $edit_row['payment_status'];
				$status = $edit_row['status'];
				//$gross_total = $net_total + ($net_total*$supplier_vat)/100 ;
			}
			
			$purchase_items = getPurchaseOrderItems($id);
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
	function getSuppliersList()
	{
		$suppliers = array();
		$query = "SELECT * FROM suppliers ORDER BY id ASC";
		$run = mysqli_query($GLOBALS['conn'], $query);
		while ($row = mysqli_fetch_array($run)) {
			$suppliers_id = $row['id'];
			$suppliers[$suppliers_id] = $row['supplier_name'];
		}
		return $suppliers;
	}
	function getItemsList()
	{
		$items = array();
		$query = "SELECT * FROM items ORDER BY id ASC";		
		$run = mysqli_query($GLOBALS['conn'], $query);
		while ($row = mysqli_fetch_array($run)) {
			$items[] = $row;
		}
		return $items;
	}

	function getProductUnitList($item_id)
	{
		$items = array();
		$query = "SELECT * FROM item_price WHERE product_id = '$item_id' ORDER BY id ASC";		
		$run = mysqli_query($GLOBALS['conn'], $query);
		while ($row = mysqli_fetch_array($run)) {
			$id = $row['unit_id'];
			$items[$id] = $row['unit_id'];
			$items[$id] = getUnitName($row['unit_id']);
		}
		return $items;
	}


	function getUnitName($unit_id)
	{
		$where = "WHERE id = '$unit_id'";
		$service = getnamewhere('item_units', 'unit_name', $where);
		return $service;
	}
	
	function getPurchaseOrderItems($purchase_id){
		$query = "SELECT * FROM purchase_order_items WHERE purchase_id = '$purchase_id'";  
		$result = mysqli_query($GLOBALS['conn'], $query);
		if ($result) {
			$result_arr = array();
			while ($row = mysqli_fetch_assoc($result)) {
			   $result_arr[] = $row;			
			}
			return $result_arr;
		}		
		else {
			return false;
		}
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
		<link href="plugins/chartist.min.css" rel="stylesheet">
		<script type="text/javascript" src="plugins/tiny_mce.js"></script>
		<script type="text/javascript">
			tinyMCE.init({
			  // General options
			  mode : "textareas",
			  theme : "advanced",
			  plugins : "autolink,lists,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,wordcount,advlist,autosave",
			
			  // Theme options
			  theme_advanced_buttons1 : "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect",
			  theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
			  theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
			  theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,pagebreak,restoredraft",
			  theme_advanced_toolbar_location : "top",
			  theme_advanced_toolbar_align : "left",
			  theme_advanced_statusbar_location : "bottom",
			  theme_advanced_resizing : true,
			
			  // Example content CSS (should be your site CSS)
			  //content_css : "css/content.css",
			  
			  setup : function(ed) {
			    ed.onChange.add(function(ed) {
			      console.log('sssss');
			      tinyMCE.triggerSave();
			    });
			  },
			
			  // Drop lists for link/image/media/template dialogs
			  template_external_list_url : "lists/template_list.js",
			  external_link_list_url : "lists/link_list.js",
			  external_image_list_url : "lists/image_list.js",
			  media_external_list_url : "lists/media_list.js",
			
			  // Style formats
			  style_formats : [
			    {title : 'Bold text', inline : 'b'},
			    {title : 'Red text', inline : 'span', styles : {color : '#ff0000'}},
			    {title : 'Red header', block : 'h1', styles : {color : '#ff0000'}},
			    {title : 'Example 1', inline : 'span', classes : 'example1'},
			    {title : 'Example 2', inline : 'span', classes : 'example2'},
			    {title : 'Table styles'},
			    {title : 'Table row 1', selector : 'tr', classes : 'tablerow1'}
			  ],
			
			  // Replace values for the template plugin
			  template_replace_values : {
			    username : "Some User",
			    staffid : "991234"
			  }
			});
		</script>   
		<style>
			.error{
			color:red;
			}
			.pj-table-icon-delete.btnDeleteProduct {
			color: #FFF;
			background: #f56212;
			padding: 5px 11px;
			}
			#btnAddproduct {
			background: #f56212;
			border: 1px solid #f56212;
			color: #fff;
			padding: 5px 17px;
			border-radius: 5px;
			}
		</style>
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
        Add Purchase
        <!--<small>Optional description</small>-->
      </h1>      
	  <ol class="breadcrumb">
        <li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="products.php">Purchases</a></li>
        <li class="active">Add Purchase</li>
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
              <form id="itemsForm" method="post" action="" class="form-horizontal" enctype="multipart/form-data"  data-fv-framework="bootstrap"   data-fv-message="This value is not valid"   data-fv-icon-valid="glyphicon glyphicon-ok"   data-fv-icon-invalid="glyphicon glyphicon-remove"   data-fv-icon-validating="glyphicon glyphicon-refresh">
            <div class="col-md-6 col-md-offset-3">
                  <input type="hidden" name="items_post" value="1" /> 
				  <input type="hidden" name="id" value="<?php echo $id; ?>" />
      			  <div class="form-group">
					<label class="control-label">Suppliers Name</label>
						<select class="form-control" name="supplier_id" id="supplier_id">
							<option value="">-- Select a supplier --</option>
							<?php
								$supplier_list = getSuppliersList();
								foreach ($supplier_list as $key => $value) {
								$selected = ($key == $supplier_id) ? "selected = selected" : "";
								echo "<option value=\"".$key."\" ".$selected.">".$value."</option>";
								}
								?>
						</select>
					</div>                   
					<div class="form-group">
					<label class="control-label">Company Name</label>
					<input type="text" class="form-control" name="company_name" id="company_name" placeholder="Companp Name" value="<?php echo safeTextOut(htmlspecialchars($company_name)); ?>" />
					</div>
					
					<div class="form-group">
					<label class="control-label">Purchase Date</label>
					<input type="text" class="form-control" name="purchase_date" id="purchase_date" placeholder="Purchase Date" value="<?php echo safeTextOut(htmlspecialchars($purchase_date)); ?>" />
					
					</div>
                    <div class="form-group">
                      <label class=" control-label">Reference Id</label>
                        <input type="text" name="reference_id" id="reference_id" class="form-control" placeholder="Reference Id" value="<?php echo safeTextOut(htmlspecialchars($reference_id)); ?>" required />
					</div>
					 <div class="form-group">
						<label class=" control-label">Payment Status</label>
						  <select name="payment_status" id="payment_status" class="form-control" required>          
                         <option value="paid"<?php if($payment_status == 'paid') { echo 'selected'; } ?>>Paid</option>
						 <option value="not_paid"<?php if($payment_status == 'not_paid') { echo 'selected'; } ?>>Not Paid</option>						
					  </select>
                      </div>					
					  <div class="form-group">
						<label class=" control-label">Status</label>
						  <select name="status" id="status" class="form-control" required>          
                         <option value="pending"<?php if($status == 'pending') { echo 'selected'; } ?>>Pending</option>
						 <option value="ordered"<?php if($status == 'ordered') { echo 'selected'; } ?>>Ordered</option>
						 <option value="received"<?php if($status == 'received') { echo 'selected'; } ?>>Received</option>
					  </select>
                      </div>
                    <div class="form-group">
					<label class="control-label">Select Product</label>
					<select  id='standard' name='standard' class='form-control'>
						<option value=""> -- No value -- </option>
						<?php
						foreach($items1 as $item) {
						$item_vat = ($item['vat']!='') ? $item['vat'] : '0.00';	?>
							<option data-name="<?php echo $item['name']; ?>" data-price="<?php echo $item['price']; ?>" data-vat="<?php echo $item_vat; ?>" data-other="<?php echo $item['other_name']; ?>" value="<?php echo $item['id'].'~~'.$item['name'].'~~'.$item['price'].'~~'.$item_vat.'~~'.$item['other_name'].'~~'.$item['barcode_id']; ?>"><?php echo $item['name']; ?> - (<?php echo $item['price']; ?>)</option>
						<?php } ?>
					</select>
					</div>
				   <p>&nbsp;</p>
                    <p>&nbsp;</p> 
                    </div>
                     <div class="col-md-12 box-content">
                       <table id="tblproduct" class="table table-striped table-bordered table-hover table-heading no-border-bottom" style="width: 96%">
                       <thead>
							<tr>
								<th style="width:18%">Product</th>
								<th style="width:10%">Unit</th>
								<th style="width:10%">Stock</th>
								<th style="width:10%">Qty</th>														
								<th style="width:10%">Unit Price</th>
								<th style="width:10%">Total Amount</th>														
								<th style="width:10%">Tax</th>
								<th style="width:2%">X</th>
							</tr>
						</thead>
                        <tbody>
							<?php $tota1_amount_with_vat = $tota1_amount_without_vat = $vat = $total_vat = 0;
								foreach($purchase_items as $items){
								$tota1_amount_without_vat += $items['total_amount'];
								$vat = ($items['total_amount']/100 * $items['tax']);
								$total_vat += $vat;
								$tota1_amount_with_vat += $items['total_amount'] + $vat;
								?>
								<tr>
									<td>
										<input type="hidden" id="items_id_{<?php echo $items['id']; ?>}" name="items_id[{<?php echo $items['id']; ?>}]" data-index="{<?php echo $items['id']; ?>}" value="<?php echo $items['product_id']; ?>" class="items_id pj-form-field" />
										<input type="hidden" id="items_name_{<?php echo $items['id']; ?>}" name="items_name[{<?php echo $items['id']; ?>}]" data-index="{<?php echo $items['id']; ?>}" value="<?php echo $items['product_name']; ?>" class="items_name pj-form-field" />
										<div id="items_name_span_{<?php echo $items['id']; ?>}"style="width:100%" ><?php echo $items['product_name']; ?></div>
									</td>
									<!-- <td>
										<span class="inline_block">
										<select id="unit_id_{<?php echo $items['id']; ?>}" name="unit_id[{<?php echo $items['id']; ?>}]" data-index="{<?php echo $items['id']; ?>}" class="unit_id pj-form-field w20" style="width:100%"  required />
											<?php
												$unit_list = getProductUnitList($items['product_id']);
												foreach ($unit_list as $key => $value) {
												$selected = ($key == $items['unit_id']) ? "selected = selected" : "";
												echo "<option value="".$key."" ".$selected.">".$value."</option>";
												}
												?>
										</select>
										</span>
									</td>
									<td>
										<span class="inline_block">
											<input style="width:100%" type="text" value="<?php echo $items['stock']; ?>" id="stock_{<?php echo $items['id']; ?>}" name="stock[{<?php echo $items['id']; ?>}]" data-index="{<?php echo $items['id']; ?>}" class="stock pj-form-field w80" required/>
										</span>
									</td> -->									
									<td>
										<span class="inline_block">
											<input style="width:100%" type="text" value="<?php echo $items['qty']; ?>" id="qty{<?php echo $qty_['id']; ?>}" name="qty[{<?php echo $items['id']; ?>}]" data-index="{<?php echo $items['id']; ?>}" class="qty pj-form-field w80" required/>
										</span>
									</td>
									<td>
										<span class="inline_block">
											<input style="width:100%" type="text" value="<?php echo $items['unit_price']; ?>" id="unit_price_{<?php echo $items['id']; ?>}" name="unit_price[{<?php echo $items['id']; ?>}]" data-index="{<?php echo $items['id']; ?>}" class="unit_price pj-form-field" required />
										</span>
									</td>
									<td>
										<span class="inline_block">
											<input style="width:100%" type="text" value="<?php echo $items['total_amount']; ?>" id="total_amount_{<?php echo $items['id']; ?>}" name="total_amount[{<?php echo $items['id']; ?>}]" data-index="{<?php echo $items['id']; ?>}" class="total_amount pj-form-field" required />
										</span>
									</td>									
									<td>
										<span class="inline_block">
											<input style="width:100%" type="text" value="<?php echo $items['tax']; ?>" id="vat_<?php echo $items['id']; ?>" name="vat[{<?php echo $items['id']; ?>}]" data-index="<?php echo $items['id']; ?>" class="vat pj-form-field w40 number" />
										</span>
									</td>
									<td><a class="pj-table-icon-delete btnDeleteProduct" title="Delete" href="#" data-id="">X</a></td>
								</tr>	
							<?php
							}
							?>
						</tbody>
                      </table>
                      
                      <br><br>
					  
					<p>Total Amount: <span id="tot_amount"><?php echo $tota1_amount_without_vat; ?></span></p>
					<p>Total VAT: <span id="tot_vat"><?php echo $total_vat; ?></span></p>
					<p>Total Amount With VAT: <span id="tot_amount_with_vat"><?php echo $tota1_amount_with_vat; ?></span></p>
					<br></br>
                    
                         <div class="form-group col-md-6 col-md-offset-3 ">
                      <?php 
                        if(isset($action) && $action == 'edit') {
                      ?>
                        <button type="submit" class="btn btn-primary btn-lg">Update</button>
                        <a href="purchases.php" class="btn btn-primary btn-lg">Cancel</a>
                      <?php } 
                        else { 
                      ?>
                        <button type="submit" class="btn btn-primary btn-lg">Submit</button>
                      <?php 
                        } 
                      ?>
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

 <table id="tblproductClone" style="display: none;width:100%">
	<tbody>
		<tr>
			<td>
				<input type="hidden" id="items_id_{INDEX}" name="items_id[{INDEX}]" data-index="{INDEX}" class="items_id pj-form-field"  />
				<input type="hidden" id="items_name_{INDEX}" name="items_name[{INDEX}]" data-index="{INDEX}" class="items_name pj-form-field"   />
				<div id="items_name_span_{INDEX}" style="width:100%"></div>	  
			</td>
			<!-- <td>
				<span class="inline_block" id="unit_details_{INDEX}">
				<select id="unit_id_{INDEX}" name="unit_id[{INDEX}]" data-index="{INDEX}" class="unit_id pj-form-field w20" style="width:100%"  required />
				</select>
				</span>
			</td>
			<td>
				<span class="inline_block">
					<input type="text" id="stock_{INDEX}" name="stock[{INDEX}]" data-index="{INDEX}" class="stock pj-form-field w20 digits" style="width:100%"  required />
				</span>
			</td> -->				
			<td>
				<span class="inline_block">
					<input type="text" id="qty_{INDEX}" name="qty[{INDEX}]" data-index="{INDEX}" class="qty pj-form-field w20 digits" style="width:100%"  required />
				</span>
			</td>
			 <td>
				<span class="inline_block">
					<input type="text" id="unit_price_{INDEX}" name="unit_price[{INDEX}]" data-index="{INDEX}" class="unit_price pj-form-field w20 number" style="width:100%" required />
				</span>
			</td>
			 <td>
				<span class="inline_block">
					<input type="text" id="total_amount_{INDEX}" name="total_amount[{INDEX}]" data-index="{INDEX}" class="total_amount pj-form-field w20 number" readonly style="width:100%" required />
				</span>
			</td>
			 <td>
				<span class="inline_block">
					<input type="text" value="5" id="vat_{INDEX}" name="vat[{INDEX}]" data-index="{INDEX}" class="vat pj-form-field w20 number" style="width:100%" required />
				</span>					
			</td>
			<td><a class="pj-table-icon-delete btnDeleteProduct" title="Delete" href="#" data-id="{INDEX}" style="width:100%">X</a></td>
		</tr>
	</tbody>
</table>


  <?php include("common/footer.php"); ?>

  <?php include("common/sidebar-right.php"); ?>
</div>
<!-- ./wrapper -->

<!-- REQUIRED JS SCRIPTS -->

<?php include("common/footer-scripts.php"); ?>

<script src="js/validation.js"></script>
<!-- All functions for this theme + document.ready processing -->

 <script src='barcode/jquery-customselect.js'></script>
<link href='barcode/jquery-customselect.css' rel='stylesheet' />
<script type="text/javascript">

$(document).ready(function() {
	$(function() {
	  $("#standard").customselect();	 	  
	});
	$(function() {	 
	  $("#supplier_id").customselect();	  
	});
	// Create Wysiwig editor for textare
	//TinyMCEStart('#items_content', null);
	//TinyMCEStart('#wysiwig_full', 'extreme');
	// Add slider for change test input length
	//FormLayoutExampleInputLength($( ".slider-style" ));
	// Initialize datepicker
	//$('#input_date').datepicker({setDate: new Date()});
	// Load Timepicker plugin
	//LoadTimePickerScript(DemoTimePicker);
	// Add tooltip to form-controls
	$('.form-control').tooltip();
	//LoadSelect2Script(DemoSelect2);
	// Load example of form validation
	//LoadBootstrapValidatorScript(DemoFormValidator);
	// Add drag-n-drop feature to boxes
	$( "#purchase_date" ).datepicker({dateFormat: 'yy-mm-dd'});
	WinMove();

	
	$( "#invoice_date" ).datepicker({dateFormat: 'yy-mm-dd'});
	//$( ".expiry_date" ).datepicker({dateFormat: 'yy-mm-dd'});
});
</script>
<script type="text/javascript">
var slug = function(str) {
    var $slug = '';
    var trimmed = $.trim(str);
    $slug = trimmed.replace(/[^a-z0-9-]/gi, '-').
    replace(/-+/g, '-').
    replace(/^-|-$/g, '');
    return $slug.toLowerCase();
}
//$( "#manuf_date" ).datepicker({dateFormat: 'yy-mm-dd'});
//$( "#expiry_date" ).datepicker({dateFormat: 'yy-mm-dd'});
//$( "#inward_date" ).datepicker({dateFormat: 'yy-mm-dd'});

	$("#itemsForm").validate();
	var $itemsForm = $("#itemsForm");
    $('#standard').on('change', function() {
		//$(document).on("change", "#standard", function (e) {
		var $tr,
			$tbody = $("#tblproduct tbody"),
			index = Math.ceil(Math.random() * 999999),
			h = $tbody.find("tr:last").find("td:first").html(),
			i = (h === null) ? 0 : parseInt(h, 10);
		
		i = !isNaN(i) ? i : 0;				
		$tr = $("#tblproductClone").find("tbody").clone();
		$tbody.find(".notFound").remove();
		var tr_html = $tr.html().replace(/\{INDEX\}/g, 'x_' + index);
		//tr_html = tr_html.replace(/\{PTCLASS\}/g, 'pj-payment-type');
		//tr_html = tr_html.replace(/\{ACLASS\}/g, 'pj-payment-amount');
		//tr_html = tr_html.replace(/\{SCLASS\}/g, 'pj-payment-status');
		//alert($("#supplier_id").val());
		//if($("#supplier_name").val() != '') { alert(1);
			$tbody.append(tr_html);
			/*if ($itemsForm.length > 0 && $itemsForm.validate) {
				getProducts.call(null, $itemsForm, 'x_' + index);
			} else {
				getProducts.call(null, $itemsForm, 'x_' + index);
			}*/


		var data = $(this).val();					
		var arr = data.split('~~');
		var id = arr[0];
		var name = arr[1];
		var unit_price = arr[2];
		var item_tax = arr[3];		
		var desc = arr[4];
		var item_name_with_desc = name + desc;

		$('.unit_price_append').html('');	
			$.post("unit_items.php", { 
				'item_id' : id },
			function(data) {							
				$('#unit_id_x_'+index).html(data);											
		});


			$( "#items_id_x_"+index).val(id);
			$( "#items_name_x_"+index).val(item_name_with_desc);
			$( "#items_name_span_x_"+index).text(item_name_with_desc);
			//$( "#vat_x_"+index).val(item_tax);
			//$( "#qty_x_"+index).val(1);
			//$( "#price_x_"+index).val($("#standard option:selected").data('price'));
			//$( "#total_amount_x_"+index).val($("#standard option:selected").data('price'));
			
			$tbody.find("tr:last").find(".spin").spinner({
				min: 0,
				step: 1
			});
		//} else {
			//$(".err").show();
		//}
		$( "#expiry_date_x_"+index).datepicker({dateFormat: 'yy-mm-dd'});
        $(this).val('');
	});
        $(document).on("click", ".btnDeleteProduct", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}			
			var index = $(this).data('id');
				
			if($("#items_id_"+index).val() == $("#btnAddproduct option:selected").val()) {
				//$("#btnAddproduct option:selected").val('');
				//$("#btnAddproduct option:selected").text('');
				$("#btnAddproduct option:selected").prop("selected", false);
			}

			var $tr = $(this).closest("tr");
			$tr.css("backgroundColor", "#FFB4B4").fadeOut("slow", function () {
				$tr.remove();
				//calPayment();
				var tot_amount = 0;
				$('.total_amount').each(function (){
					if (!isNaN(this.value) && this.value.length != 0) {
						tot_amount = tot_amount + parseFloat(this.value);
					}
				});
				$("#tot_amount").text(tot_amount.toFixed(2));
				$("#tot_vat").text((tot_amount/100 * 5).toFixed(2));
				$("#tot_amount_with_vat").text((tot_amount + parseFloat(tot_amount/100 * 5)).toFixed(2));
			});
			return false;
		});

		$(".box-content").on("keyup", ".qty", function (e) {
			if (/\D/g.test(this.value)){
				this.value = this.value.replace(/\D/g,'')
			}
			var $qty = $(this).val();
			var $index = $(this).attr('data-index');
			var $unit_price = $("#unit_price_" + $index).val();
				
			$qty = !isNaN($qty) ? $qty : 0;
			$unit_price = !isNaN($unit_price) ? $unit_price : 0;

			var result = 0;
			var tot_amount = 0;
			
			if($unit_price !='' && $qty !='') {
				$("#tot_amount").text('');				
				result = $qty * $unit_price;
				$('#total_amount_'+$index).val(result);				
			} else {
				//$("#tot_amount").text(0);				
				//$("#tot_vat").text(0);
				//$("#tot_amount_with_vat").text(0);
				$('#total_amount_'+$index).val('');
			}
			$('.total_amount').each(function (){
				if (!isNaN(this.value) && this.value.length != 0) {
					tot_amount = tot_amount + parseFloat(this.value);
				}
			});
			$("#tot_amount").text(tot_amount);
			$("#tot_vat").text((tot_amount/100 * 5));
			$("#tot_amount_with_vat").text((tot_amount + parseFloat(tot_amount/100 * 5)).toFixed(2));
		}).on("keyup", ".unit_price", function (e) {
			if (/\D/g.test(this.value)){
				//this.value = this.value.replace(/\D/g,'')
			}
			var $unit_price = $(this).val();
			var $index = $(this).attr('data-index');
			var $qty = $("#qty_" + $index).val();

			$qty = !isNaN($qty) ? $qty : 0;
			$unit_price = !isNaN($unit_price) ? $unit_price : 0;

			var result = 0;
			var tot_amount = 0;
			
			if($unit_price !='' && $qty !='') {
				$("#tot_amount").text('');				
				result = $qty * $unit_price;
				$('#total_amount_'+$index).val(result.toFixed(2));				
			} else {
				//$("#tot_amount").text(0);				
				//$("#tot_vat").text(0);
				//$("#tot_amount_with_vat").text(0);
				$('#total_amount_'+$index).val('');
			}
			$('.total_amount').each(function (){
					if (!isNaN(this.value) && this.value.length != 0) {
						tot_amount = tot_amount + parseFloat(this.value);
					}
				});
				$("#tot_amount").text(tot_amount.toFixed(2));
				$("#tot_vat").text((tot_amount/100 * 5).toFixed(2));
				$("#tot_amount_with_vat").text((tot_amount + parseFloat(tot_amount/100 * 5)).toFixed(2));
		}).on("keyup", ".stock", function (e) {
			if (/\D/g.test(this.value)){
				this.value = this.value.replace(/\D/g,'')
			}			
		});		

		/*$(".box-content").on("keyup", ".qty", function (e) {
			
			var $qty = $(this).val();
			var $index = $(this).attr('data-index');
			var $unit_price = $("#price_" + $index).val();
			var $vat = $("#vat_" + $index).val();
			var $discount = $("#discount_" + $index).val();
			if($discount == '') { $discount = '0.00'; }
			if($vat == '') { $vat = '0.00'; }
				
			$qty = !isNaN($qty) ? $qty : 0;
			$unit_price = !isNaN($unit_price) ? $unit_price : 0;

			var result = 0;
			
			if($unit_price !='' && $qty !='') {
				result = $qty * $unit_price;
				$('#total_amount_'+ $index).val(result);
				var disc_sub_tot = (parseFloat(result) - parseFloat($discount)).toFixed(2);
				$('#sub_total_'+ $index).val(disc_sub_tot);
				var vat_amount = (parseFloat($vat) * parseFloat(disc_sub_tot/100)).toFixed(2);
				$('#vat_total_'+$index).val(vat_amount);
				var net_toatl = ((parseFloat(result) + parseFloat(vat_amount)) - $discount).toFixed(2);
				$('#net_total_'+$index).val(net_toatl);
			} else {
				$('#total_amount_'+$index).val('');
			}			

		}).on("keyup", ".price", function (e) {
			
			var $unit_price = $(this).val();
			var $index = $(this).attr('data-index');
			var $qty = $("#qty_" + $index).val();
			var $vat = $("#vat_" + $index).val();
			var $discount = $("#discount_" + $index).val();
			if($discount == '') { $discount = '0.00'; }
			if($vat == '') { $vat = '0.00'; }

			$qty = !isNaN($qty) ? $qty : 0;
			$unit_price = !isNaN($unit_price) ? $unit_price : 0;

			var result = vat_amount = net_toatl = disc_sub_tot = 0;
			
			if($unit_price !='' && $qty !='') {
				result = $qty * $unit_price;
				$('#total_amount_'+ $index).val(result);
				var disc_sub_tot = (parseFloat(result) - parseFloat($discount)).toFixed(2);
				$('#sub_total_'+ $index).val(disc_sub_tot);
				var vat_amount = (parseFloat($vat) * parseFloat(disc_sub_tot/100)).toFixed(2);
				$('#vat_total_'+$index).val(vat_amount);
				var net_toatl = ((parseFloat(disc_sub_tot) + parseFloat(vat_amount))).toFixed(2);
				$('#net_total_'+$index).val(net_toatl);
			} else {
				$('#total_amount_'+$index).val('');
			}

		}).on("keyup", ".vat", function (e) {			
			var $vat = $(this).val();
			var $index = $(this).attr('data-index');
			var $unit_price = $("#price_" + $index).val();
			var $qty = $("#qty_" + $index).val();			
			var $discount = $("#discount_" + $index).val();
			if($discount == '') { $discount = '0.00'; }			
				
			$qty = !isNaN($qty) ? $qty : 0;
			$unit_price = !isNaN($unit_price) ? $unit_price : 0;	
			
			var result = vat_amount = net_toatl = 0;
			if($unit_price !='' && $qty !='') {
				result = (parseFloat($qty) * parseFloat($unit_price)) - parseFloat($discount);				
				var vat_amount = (parseFloat($vat) * parseFloat(result/100)).toFixed(2);
				$('#vat_total_'+$index).val(vat_amount);
				var net_toatl = ((parseFloat(result) + parseFloat(vat_amount))).toFixed(2);
				$('#net_total_'+$index).val(net_toatl);
			} else {
				$('#total_amount_'+$index).val('');
			}
			
		}).on("keyup", ".discount", function (e) {
			
			var $discount = $(this).val();
			var $index = $(this).attr('data-index');
			var $unit_price = $("#price_" + $index).val();
			var $qty = $("#qty_" + $index).val();			
			var $vat = $("#vat_" + $index).val();
			if($discount == '') { $discount = '0.00'; }	
			if($vat == '') { $vat = '0.00'; }
				
			$qty = !isNaN($qty) ? $qty : 0;
			$unit_price = !isNaN($unit_price) ? $unit_price : 0;	

			var result = vat_amount = net_toatl = disc_sub_tot = 0;
			if($unit_price !='' && $qty !='') {
				result = $qty * $unit_price;
				$('#total_amount_'+ $index).val(result);
				var disc_sub_tot = (parseFloat(result) - parseFloat($discount)).toFixed(2);
				$('#sub_total_'+ $index).val(disc_sub_tot);
				var vat_amount = (parseFloat($vat) * parseFloat(disc_sub_tot/100)).toFixed(2);
				$('#vat_total_'+$index).val(vat_amount);
				var net_toatl = ((parseFloat(disc_sub_tot) + parseFloat(vat_amount))).toFixed(2);
				$('#net_total_'+$index).val(net_toatl);
			} else {
				$('#total_amount_'+$index).val('');
			}
			
		});*/
		
		/*function getProducts($form, $index) {
			$.get("get_items.php", $form.serialize()).done(function (data) {
				//$("a").attr("href", "index.php?controller=pjAdminSuppliers&action=pjActionAddProduct&id="+$("#supplier_id").val());
				$(".add_product").show();
				var index = $index;//$( ".product_id" ).attr('data-index');				
				$("#product_id_"+index).html(data);
			});
		}*/

		$("#itemsForm").on("keyup", "#net_total, #supplier_vat", function (e) {
			var net_total = $('#net_total').val();
			var supplier_vat = $('#supplier_vat').val();
			if(supplier_vat > 0) {
				var vat_amount = (net_total*supplier_vat)/100;
			} else {
				var vat_amount = supplier_vat;
			}
			//$('#vat_amount').val(vat_amount);
			$('#gross_total').val(Number(vat_amount)+Number(net_total));
		});


</script>
</body>
</html>