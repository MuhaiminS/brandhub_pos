<?php 
session_start();
include("../functions.php");
include_once("../config.php");
chkAdminLoggedIn();
connect_dre_db();
$server_url = getServerURL();

function getUserName($user_id)
{
	$where = "WHERE id = '$user_id'";
	$service = getnamewhere('users', 'user_name', $where);
	return $service;
}

function getShopsList()
{
	$service = array();
	$query="SELECT * FROM locations_shops ORDER BY id ASC";
	$run = mysql_query($query);
	while($row = mysql_fetch_array($run)) {
		$shop_id = $row['id'];
		$service[$shop_id]['shop_id'] = $row['id'];
		$service[$shop_id]['shop_name'] = $row['shop_name'];
	}
	return $service;	

}
function getUserList()
{
	$users = array();
	$query = "SELECT * FROM users ORDER BY id ASC";
	$run = mysqli_query($GLOBALS['conn'], $query);
	while($row = mysqli_fetch_array($run)) {
		$user_id = $row['id'];
		$users[$user_id] = $row['user_name'];
}
return $users;
}
function getShopName($shop_id)
{
	$where = "WHERE id = '$shop_id'";
	$service = getnamewhere('locations_shops', 'shop_name', $where);
	return $service;
}

function getLastSettle($user_id, $settle_id) {

	$settle_date = '0';
	$query = "SELECT `settle_date` FROM `settle_sale` WHERE user_id = '$user_id' AND id < '$settle_id' order by settle_date DESC LIMIT 1";	
	$run = mysqli_query($GLOBALS['conn'], $query);
	while($row = mysqli_fetch_assoc($run)) {
		$settle_date = $row['settle_date'];		
	}
	//echo $settle_date; die;
	return $settle_date;
}


if(isset($_GET["page"])) {
	$page = (int)$_GET["page"];
} else {
	$page = 1;
}
$setLimit = 50;
$pageLimit = ($page * $setLimit) - $setLimit;

function getSettleSales($from_date ='', $to_date='', $shop='', $pageLimit='', $setLimit='', $export="")
{
	$date = date('Y-m-d');
	$qry="SELECT *, DATE_FORMAT(settle_date,'%Y-%m-%d') as settle_dat FROM settle_sale WHERE id != ''"; 
	
	if($shop != ''){
		$qry .=" AND shop_id = '$shop'";
	}
	
	if($from_date != '' && $to_date != '' ) {
		
		$qry .= " AND settle_date BETWEEN '$from_date 00:00:00' AND '$to_date 23:59:59' ";		
	} 
	if($from_date != '' && $to_date == '' ) {
		$qry .= " AND settle_date BETWEEN '$from_date 00:00:00' AND '$date 23:59:59' ";
	}

	if($from_date == '' && $to_date != '' ) {
		$qry .= " AND settle_date <= '$to_date 23:59:59'";
	}
	if($export == '') {
		$qry .=" ORDER BY settle_date DESC LIMIT $pageLimit, $setLimit";
	} else {
		$qry .=" ORDER BY settle_date DESC ";
	}
	//echo $qry;
	$result=mysqli_query($GLOBALS['conn'], $qry);
	//$num=mysql_num_rows($result);	//echo "total result ".$num;
	if($result)
	{
		return $result;
	}
	else
	return false;
}



$shops = (isset($_GET['shop']) && $_GET['shop'] !='') ? $_GET['shop'] : '';
$from_date1 = (isset($_GET['from_date']) && $_GET['from_date'] !='') ? $_GET['from_date'] : '';
$to_date1 = (isset($_GET['to_date']) && $_GET['to_date'] !='') ? $_GET['to_date'] : ''; 

$shop_income_arr = $shop_exp_arr = $shop_rent_exp_arr = $shop_salary_exp_arr = $shop_settle_arr = array();

	$br_name_arr = array(); 
	foreach($tpl['shopes_arr'] as $bran) 
		$br_name_arr[$bran['id']] = $bran['b_shop'];

	foreach($_REQUEST['st_shop'] as $key => $val) {		
		if(!in_array($val, $br_name_arr)) {
			$shop_income_arr[$br_name_arr[$val]] = number_format(0, 2, '.', '');
			$shop_settle_arr[$br_name_arr[$val]] = number_format(0, 2, '.', '');
			$shop_exp_arr[$br_name_arr[$val]] = number_format(0, 2, '.', '');
			$shop_rent_exp_arr[$br_name_arr[$val]] = number_format(0, 2, '.', '');
			$shop_salary_exp_arr[$br_name_arr[$val]] = number_format(0, 2, '.', '');
			$data_purchase_sal[$br_name_arr[$val]] = number_format(0, 2, '.', '');			
		}
	}

	$shop_income = $total_vat =0;
	foreach($tpl['data_shop_sal'] as $shop_sal) {	
		$shop_income_tot = $shop_sal['sal_cash_amount'] + $shop_sal['sal_card_amount'] + $shop_sal['sal_cheque_amount'];
		$shop_income_arr[$shop_sal['shop_name']] += $shop_income_tot;				
		$shop_income += $shop_income_tot;
	}
	
	$shop_settle_income = $total_settle_vat = 0;
	foreach($tpl['data_shop_settle_sal'] as $data_shop_sett) {	
		$shop_settle_tot = $data_shop_sett['net_total'] - $data_shop_sett['gross_total_tax'];
		$shop_settle_arr[$data_shop_sett['shop_id']] += $shop_settle_tot;
		$total_settle_vat += $data_shop_sett['gross_total_tax'];		
		$shop_settle_income += $shop_settle_tot + $data_shop_sett['gross_total_tax'];	
	}	

	$purchase_income = $pur_total_vat = 0;
	foreach($tpl['data_purchase_sal'] as $data_purchase) {	
		$purchase_income_tot = $data_purchase['total_amount'];
		$purchase_income_arr[$data_purchase['shop_name']] += $purchase_income_tot;
		$pur_total_vat += ($purchase_income_tot/100 * 5);		
		$purchase_income += $purchase_income_tot;
	}
	
	$shop_exp = 0;
	foreach($tpl['data_shop_exp'] as $da_shop_exp) {
		$shop_exp_tot = $da_shop_exp['exp_cash_amount'] + $da_shop_exp['exp_card_amount'] + $da_shop_exp['exp_cheque_amount'];
		$shop_exp_arr[$da_shop_exp['shop_name']] += $shop_exp_tot;
		$shop_exp += $shop_exp_tot;
	}

	
	$shop_rent_exp = 0;
	foreach($tpl['data_shop_rent'] as $shop_rent) {
		$shop_rent_exp_tot = $shop_rent['total_amount'];
		$shop_rent_exp_arr[$shop_rent['shop_name']] += $shop_rent_exp_tot;
		$shop_rent_exp += $shop_rent_exp_tot;		
	}

	
	$shop_salary_exp = 0;
	foreach($tpl['data_shop_salary'] as $shop_salary) {		
		$shop_salary_exp_tot = ($shop_salary['basic_salary'] + $shop_salary['allowance'] + $shop_salary['additional']) - ($shop_salary['deduct'] + $shop_salary['installments']);
		$shop_salary_exp_arr[$shop_salary['shop_name']] += $shop_salary_exp_tot;
		$shop_salary_exp += $shop_salary_exp_tot;
	}
		$grand_total = ($shop_exp + $shop_rent_exp + $shop_salary_exp + $purchase_income + $local_purchase_income);
		$grand_total = number_format($grand_total, 2, '.', '');
		
		$grand_total_sale = ($shop_income + $shop_settle_income);
		$grand_total_sale = number_format($grand_total_sale, 2, '.', '');
	

?>
<!DOCTYPE html>
<!--
	This is a starter template page. Use this page to start your new project from
	scratch. This page gets rid of all links and provides the needed markup only.
	-->
<html>
	<head>
		<style>
			.rTable1 {
				---overflow-y: scroll; height: 300px; width: 100%;
			}
			.rTableRow
			{
				/*min-height: 450px;*/
			}
			.print{	    
				--background: #e9f5fa none repeat scroll 0 0;
				--border: 2px solid #032a86;
				--box-shadow: none;
				--color: #fff;
				cursor: pointer;
				--font: bold 12px/18px Verdana,sans-serif;
				padding: 1px 24px;
				text-transform: uppercase;
			}
			.ui-datepicker-calendar {
				display: none;
			}		
		</style>
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
						Report - Settle Sale
						<!--<small>Optional description</small>-->
					</h1>
					<ol class="breadcrumb">
						<li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
						<li class="active">Settle Sale</li>
					</ol>
				</section>
				<!-- Main content -->
				<section class="content container-fluid">
					<div class="row">
						<div class="col-xs-12">
							<div class="box">
								<div class="box-header">
									<h3 class="box-title">Settle Sale</h3>
								</div>
								<!-- /.box-header -->
								<div class="box-body">
									<div id="form" class="panel panel-warning" style="display: block;">
										<div class="panel-body">
											<form action="reports_settle_sales.php" accept-charset="utf-8">												
												<input type="hidden" name="report" value="1" />		
												<p>
													<label class="title">Month/Year</label>
													<span class="pj-form-field-custom pj-form-field-custom-after float_left r5">
														<input type="text" name="from_date" id="datepick_acc1" value="<?php echo $_REQUEST['from_date']; ?>" class="form-control w80 datepick_acc1 pointer" readonly="readonly" rel="" rev="" />
														<span class="pj-form-field-after"><abbr class="pj-form-field-icon-date"></abbr></span>
													</span>
													<span class="pj-form-field-custom pj-form-field-custom-after float_left r5">
														TO
													</span>
													<span class="pj-form-field-custom pj-form-field-custom-after float_left r5">
														<input type="text" name="to_date" id="datepick_acc2" value="<?php echo $_REQUEST['to_date']; ?>" class="form-control w80 datepick_acc2 pointer" readonly="readonly" rel="" rev="" />
														<span class="pj-form-field-after"><abbr class="pj-form-field-icon-date"></abbr></span>
													</span>
												</p>												
												<p>
													<label class="title">&nbsp;</label>
													<input type="submit" value="Generate Reports"  class="pj-button generate_sales" />
												</p>		
											</form>
										</div>
									</div>									
									<div class="row">
										<div class="col-md-6"></div>
										<div class="col-md-6 text-right pr0">
											<div class="dt-buttons btn-group">
												<a class="btn btn-default buttons-print buttons-html5 print_me" tabindex="0" aria-controls="SLData" href="#"><span>Print</span></a>
												<!--<a class="btn btn-default buttons-copy buttons-html5" tabindex="0" aria-controls="SLData" href="#"><span>Copy</span></a>
												<a class="btn btn-default buttons-excel buttons-html5" tabindex="0" aria-controls="SLData" href="#"><span>Excel</span></a>-->
												<a target="_blank"  href="excel_export_settle_sale.php?sale=Counter Sale&order_type=counter_sale&get_type=excel&from_date=<?php echo $from_date1; ?>&to_date=<?php echo $to_date1; ?>" class="btn btn-default buttons-csv buttons-html5 export" tabindex="0" aria-controls="SLData" href="#"><span>CSV</span></a>
												<!--<a class="btn btn-default buttons-pdf buttons-html5" tabindex="0" aria-controls="SLData" href="#"><span>PDF</span></a>
												<a class="btn btn-default buttons-collection buttons-colvis" tabindex="0" aria-controls="SLData" href="#"><span>Columns</span></a>-->
											</div>
										</div>
									</div>
									<?php if(isset($_REQUEST['report']) && !empty($_REQUEST['report'])) { ?>
									<div class="rTable1">			
										<div class="rTable table_acc1">			
												
											<div class="rTableRow">
												 <div class="rTableCell rTableCell1"><span style="font-weight: bold; color:blue;">Income</div>						 						 
											</div>
											<div class="rTableRow">
												<div class="rTableHead rTableHead1">
													<span class="branch_na" style="font-weight: bold; color:black;">Sales: </span>							
												</div>							 	
											</div>
											<?php if(!empty($branch_income_arr)) { 
											 foreach($branch_income_arr as $key => $b_income_arr) { ?>
												<div class="rTableRow">
													 <div class="rTableHead rTableHead1">
														<span class="branch_na"><?php echo 'KFS Wears: '; ?></span>
														<span class="branch_tot" ><?php echo number_format($b_income_arr, 2, '.', ''); ?></span>								
													</div>							 	
												</div>						
											<?php } ?>				
											<div class="rTableRow">
												<div class="rTableHead rTableHead1">
													<span class="branch_na" style="font-weight: bold; color:red;">Total</span>
													<span class="branch_tot" style="font-weight: bold;" ><?php echo number_format($branch_income, 2, '.', ''); ?></span>
												</div>							 	
											</div>
															
											<?php } else {?>
											<div class="rTableRow">
												<div class="rTableHead rTableHead1">
													<span class="branch_na" style="font-weight: bold; color:red;">No records found</span>							
												</div>							 	
											</div>
											<?php } ?>
											<div class="rTableRow">
												<div class="rTableHead rTableHead1">
													<span class="branch_na" style="font-weight: bold; color:black;">Settle Sales: </span>							
												</div>							 	
											</div>
											<?php if(!empty($branch_settle_income)) { 
											 foreach($branch_settle_arr as $key => $branch_set) { ?>
												<div class="rTableRow">
													 <div class="rTableHead rTableHead1">
														<span class="branch_na"><?php echo 'KFS Wears'; ?></span>
														<span class="branch_tot" ><?php echo number_format($branch_set, 2, '.', ''); ?></span>								
													</div>							 	
												</div>						
											<?php } ?>
											<div class="rTableRow">
												<div class="rTableHead rTableHead1">
													<span class="branch_na" style="font-weight: bold; color:red;">Total VAT</span>
													<span class="branch_tot" style="font-weight: bold;" ><?php echo number_format($total_settle_vat , 2, '.', ''); ?></span>
												</div>							 	
											</div>
											<div class="rTableRow">
												<div class="rTableHead rTableHead1">
													<span class="branch_na" style="font-weight: bold; color:red;">Total</span>
													<span class="branch_tot" style="font-weight: bold;" ><?php echo number_format($branch_settle_income, 2, '.', ''); ?></span>
												</div>							 	
											</div>
															
											<?php } else {?>
											<div class="rTableRow">
												<div class="rTableHead rTableHead1">
													<span class="branch_na" style="font-weight: bold; color:red;">No records found</span>							
												</div>							 	
											</div>
											<?php } ?>
											<div class="rTableRow">
												<div class="rTableHead rTableHead1">
													<span class="branch_na" style="font-weight: bold; color:green;">Grand Total</span>
													<span class="branch_tot" style="font-weight: bold;"><b><?php echo number_format($grand_total_sale, 2, '.', ''); ?></b></span>
												</div>							 	
											</div>
										</div>		
										<div class="rTable table_acc2">					
												
											<div class="rTableRow">						 
												 <div class="rTableCell rTableCell1"><span style="font-weight: bold; color:blue;">Expenes:</div>							 
											</div>
											<div class="rTableRow">
												<div class="rTableHead rTableHead1">
													<span class="branch_na" style="font-weight: bold; color:black;">Purchases: </span>							
												</div>							 	
											</div>
											<?php if(!empty($purchase_income_arr)) { 
											foreach($purchase_income_arr as $key => $purchase_inco) { 
											?>
												<div class="rTableRow">
													 <div class="rTableHead rTableHead1">
														<span class="branch_na"><?php echo $key.' : '; ?></span>
														<span class="branch_tot" ><?php echo number_format($purchase_inco, 2, '.', ''); ?></span>								
													</div>							 	
												</div>						
											<?php } ?>
											<div class="rTableRow">
												<div class="rTableHead rTableHead1">
													<span class="branch_na" style="font-weight: bold; color:red;">Total VAT</span>
													<span class="branch_tot" style="font-weight: bold;" ><?php echo number_format($pur_total_vat, 2, '.', ''); ?></span>
												</div>	
												<div class="rTableHead rTableHead1">
													<span class="branch_na" style="font-weight: bold; color:red;">Total</span>
													<span class="branch_tot" style="font-weight: bold;" ><?php echo number_format($purchase_income + $pur_total_vat, 2, '.', ''); ?></span>
												</div>							 	
											</div>
											<?php } else {?>
											<div class="rTableRow">
												<div class="rTableHead rTableHead1">
													<span class="branch_na" style="font-weight: bold; color:red;">No records found</span>							
												</div>							 	
											</div>
											<?php } ?>
											
											<div class="rTableRow">
												<div class="rTableHead rTableHead1">
													<span class="branch_na" style="font-weight: bold; color:black;">Expenes:</span>							
												</div>							 	
											</div>
											<?php if(!empty($branch_exp_arr)) { 
											foreach($branch_exp_arr as $key => $b_exp_arr) { ?>
												<div class="rTableRow">
													 <div class="rTableHead rTableHead1">
														<span class="branch_na"><?php echo $key.' : '; ?></span>
														<span class="branch_tot" ><?php echo number_format($b_exp_arr, 2, '.', ''); ?></span>								
													</div>							 	
												</div>						
											<?php } ?>
											<div class="rTableRow">
												<div class="rTableHead rTableHead1">
													<span class="branch_na" style="font-weight: bold; color:red;">Total</span>
													<span class="branch_tot" style="font-weight: bold;" ><?php echo number_format($branch_exp, 2, '.', ''); ?></span>
												</div>							 	
											</div>
											<?php } else {?>
											<div class="rTableRow">
												<div class="rTableHead rTableHead1">
													<span class="branch_na" style="font-weight: bold; color:red;">No records found</span>							
												</div>							 	
											</div>
											<?php } ?>

											<div class="rTableRow">
												<div class="rTableHead rTableHead1">
													<span class="branch_na" style="font-weight: bold; color:black;">Rent:</span>							
												</div>							 	
											</div>
											<?php if(!empty($branch_rent_exp_arr)) { 
											foreach($branch_rent_exp_arr as $key => $b_rent_exp_arr) { ?>
												<div class="rTableRow">
													 <div class="rTableHead rTableHead1">
														<span class="branch_na"><?php echo $key.' : '; ?></span>
														<span class="branch_tot" ><?php echo number_format($b_rent_exp_arr, 2, '.', ''); ?></span>								
													</div>							 	
												</div>						
											<?php } ?>
											<div class="rTableRow">
												<div class="rTableHead rTableHead1">
													<span class="branch_na" style="font-weight: bold; color:red;">Total</span>
													<span class="branch_tot" style="font-weight: bold;" ><?php echo number_format($branch_rent_exp, 2, '.', ''); ?></span>
												</div>							 	
											</div>
											<?php } else {?>
											<div class="rTableRow">
												<div class="rTableHead rTableHead1">
													<span class="branch_na" style="font-weight: bold; color:red;">No records found</span>							
												</div>							 	
											</div>
											<?php } ?>
											
											<div class="rTableRow">
												<div class="rTableHead rTableHead1">
													<span class="branch_na" style="font-weight: bold; color:black;">Salary:</span>							
												</div>							 	
											</div>
											<?php if(!empty($branch_salary_exp_arr)) { 
											foreach($branch_salary_exp_arr as $key => $d_branch_salary) { ?>
												<div class="rTableRow">
													 <div class="rTableHead rTableHead1">
														<span class="branch_na"><?php echo $key.' : '; ?></span>
														<span class="branch_tot" ><?php echo number_format($d_branch_salary, 2, '.', ''); ?></span>								
													</div>							 	
												</div>						
											<?php } ?>
											<div class="rTableRow">
												<div class="rTableHead rTableHead1">
													<span class="branch_na" style="font-weight: bold; color:red;">Total</span>
													<span class="branch_tot" style="font-weight: bold;"><?php echo number_format($branch_salary_exp, 2, '.', ''); ?></span>
												</div>							 	
											</div>
											<?php } else {?>
											<div class="rTableRow">
												<div class="rTableHead rTableHead1">
													<span class="branch_na" style="font-weight: bold; color:red;">No records found</span>							
												</div>							 	
											</div>
											<?php } ?>
											<div class="rTableRow">
												<div class="rTableHead rTableHead1">
													<span class="branch_na"></span>
													<span class="branch_tot"></span>
												</div>							 	
											</div>

											<div class="rTableRow">
												<div class="rTableHead rTableHead1">
													<span class="branch_na" style="font-weight: bold; color:green;">Grand Total</span>
													<span class="branch_tot" style="font-weight: bold;"><b><?php echo number_format($grand_total, 2, '.', ''); ?></b></span>
												</div>							 	
											</div>

										</div>						
									</div>
									<?php } ?>												
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
			    if(id && confirm('Are you sure you want to delete this category?'))
			    {
			        window.location.href = site_url+'/admin/manage_orders.php?id='+id+'&act=delete';
			    }
			}
			
			$(document).on('click', '.print_me', function(e) {
				$(".show_titles").show();
				var content = document.getElementById('settle_sale_repots').innerHTML;
				var win = window.open();	
				//win.document.write('<link href="css/style_v1.css" rel="stylesheet">');
				//win.document.write('<link href="core/framework/libs/pj/css/pj-table.css" rel="stylesheet" type="text/css" />');			
				win.document.write(content);	
				win.print();
				$(".show_titles").hide();
				win.window.close();
			});
			
			//export
			$(".show_titles").hide();
			function exportTableToCSV($table, filename) {
				   var $rows = $table.find('tr:has(th),tr:has(td)'),
					// Temporary delimiter characters unlikely to be typed by keyboard
					// This is to avoid accidentally splitting the actual contents
					tmpColDelim = String.fromCharCode(11), // vertical tab character
					tmpRowDelim = String.fromCharCode(0), // null character
					// actual delimiter characters for CSV format
					colDelim = '","',
					rowDelim = '"\r\n"',

					// Grab text from table into CSV formatted string
					csv = '"' + $rows.map(function (i, row) {
					 var $row = $(row),
					  $cols = $row.find('th,td');

					 return $cols.map(function (j, col) {
					  var $col = $(col),
					   text = $col.text();

					  return text.replace(/"/g, '""'); // escape double quotes

					 }).get().join(tmpColDelim);

					}).get().join(tmpRowDelim)
					 .split(tmpRowDelim).join(rowDelim)
					 .split(tmpColDelim).join(colDelim) + '"',

					// Data URI
					csvData = 'data:application/csv;charset=utf-8,' + encodeURIComponent(csv);

				   $(this)
					.attr({
					'download': filename,
					 'href': csvData,
					 'target': '_blank'
				   });
					$(".show_titles").hide();
			  }

			  // This must be a hyperlink  
				// $(".export").on('click', function (event) {
			   $(document).on('click', '.excel_me', function(event) {
					$(".show_titles").show();
					exportTableToCSV.apply(this, [$('#settle_sale_repots>table'), 'export_settle_reports.csv']);        
			  });

			</script>
	</body>
</html>