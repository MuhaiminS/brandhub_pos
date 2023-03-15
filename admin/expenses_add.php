<?php 

session_start();
include("../functions.php");
include_once("../config.php");
chkAdminLoggedIn();
connect_dre_db();

$trn_no = '';
$company_name = '';
$purchase_date = '';
$reference_id = '';
$status = '';
$payment_status = '';
$product_id = array();
$qty = array();
$unit_price = array();
$total_amount = array();
$tax = array();
$payment_type = array();
$supplier_vat = '';
$sub_total = '';
$vat_amount = '';
$net_total = '';
$description = '';
$expense_category_id = '';

$action = 'add';
$update_img_tbl = false;

if(isset($_POST['items_post'])) {
  
  $trn_no = $_POST['trn_no'];
  $company_name = $_POST['company_name'];
  $purchase_date = date("Y-m-d",strtotime($_POST['purchase_date']));
  $reference_id = $_POST['reference_id'];
  $supplier_vat = $_POST['supplier_vat'];
  //$status = $_POST['status'];
  $payment_status = $_POST['payment_status'];
  $sub_total = $_POST['sub_total'];
  $vat_amount = $_POST['vat_amount'];
  $net_total = $_POST['net_total'];
  $description = $_POST['description'];
  $expense_category_id = $_POST['expense_category_id'];
  $is_active = 1;
  $date_added = date("Y-m-d H:i:s");
  $date_updated = date("Y-m-d H:i:s");
  
  if(isset($_POST['id']) && $_POST['id'] > 0) {
    $id = $_POST['id'];   
    $qry = "UPDATE expense SET trn_no= '$trn_no', reference_id = '".safeTextIn($reference_id)."', company_name = '".safeTextIn($company_name)."', payment_status = '".safeTextIn($payment_status)."', sub_total = '".safeTextIn($sub_total)."', purchase_date = '".safeTextIn($purchase_date)."', is_active = '".safeTextIn($is_active)."', date_updated = '".safeTextIn($date_updated)."', supplier_vat = '".safeTextIn($supplier_vat)."', vat_amount = '".safeTextIn($vat_amount)."', net_total = '".safeTextIn($net_total)."', expense_category_id = '$expense_category_id', description = '$description' WHERE id = '$id'";
    //  echo $qry; die;
    if(mysqli_query($GLOBALS['conn'], $qry)){
    }
  redirect('expenses.php?resp=updatesucc');
  }
  else {    
    $qry = "INSERT INTO `expense`(`trn_no`, `reference_id`, `company_name`, `payment_status`, `sub_total`, `purchase_date`, `is_active`, `date_added`, `date_updated`, supplier_vat, vat_amount, net_total, expense_category_id, description) VALUES ('".safeTextIn($trn_no)."', '".safeTextIn($reference_id)."', '".safeTextIn($company_name)."', '".safeTextIn($payment_status)."', '".safeTextIn($sub_total)."', '".safeTextIn($purchase_date)."', '$is_active', '$date_added', '$date_updated', '$supplier_vat', '$vat_amount', '$net_total', '$expense_category_id', '$description')";
    //echo $qry; die;
    if(mysqli_query($GLOBALS['conn'], $qry)){   
      $id = mysqli_insert_id($GLOBALS['conn']);
      
      /*foreach($_POST['product_id'] as $key => $value) {
        $product_id = $_POST['product_id'][$key];
        $qty = $_POST['qty'][$key];
        $unit_price = $_POST['unit_price'][$key];
        $total_amount = $_POST['total_amount'][$key];
        $tax = $_POST['tax'][$key];
        //$payment_type = $_POST['payment_type'][$key];
        
        $qry1 = "INSERT INTO purchase_order_items (purchase_id, item_name, qty, unit_price, total_amount, tax) VALUES ('$id', '$product_id', '$qty', '$unit_price', '$total_amount', '$tax')";    

        if(mysqli_query($GLOBALS['conn'], $qry1)){    
          $id1 = mysqli_insert_id($GLOBALS['conn']);
        }
      }*/
    }   
  } 
    //die;
  redirect('expenses.php?resp=addsucc');
}

function getExpenseCategoryList()
   {
    $service = array();
    $query="SELECT * FROM ".DB_PRIFIX."expense_category ORDER BY id ASC";
    $run = mysqli_query($GLOBALS['conn'], $query);
    while($row = mysqli_fetch_array($run)) {
      $expense_id = $row['id'];
      $service[$expense_id]['expense_id'] = $row['id'];
      $service[$expense_id]['expense_name'] = $row['expense_name'];
    }
    return $service;  
   
   } 

if(isset($_GET['act']) && $_GET['act'] != '' && isset($_GET['id']) && $_GET['id'] > 0) {  
  $action = $_GET['act'];
  $id = $_GET['id'];
  if($action == 'edit') {
    $edit_query = "SELECT * FROM expense WHERE id = '$id'";
    $run_edit = mysqli_query($GLOBALS['conn'], $edit_query);    
    while ($edit_row = mysqli_fetch_array($run_edit)) {
      $id = $edit_row['id'];
      $trn_no = $edit_row['trn_no'];
      $company_name = $edit_row['company_name'];
      $purchase_date = $edit_row['purchase_date'];
      $reference_id = $edit_row['reference_id'];
      $supplier_vat = $edit_row['supplier_vat'];
      //$status = $edit_row['status'];
      $payment_status = $edit_row['payment_status'];
      $sub_total = $edit_row['sub_total'];
      $vat_amount = $edit_row['vat_amount'];
      $net_total = $edit_row['net_total'];
      $description = $edit_row['description'];
      $expense_category_id = $edit_row['expense_category_id'];
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
        Add Expense
        <!--<small>Optional description</small>-->
      </h1>      
    <ol class="breadcrumb">
        <li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="products.php">Expenses</a></li>
        <li class="active">Add Expense</li>
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
              
            
             <form id="itemsForm" method="post" action=""  enctype="multipart/form-data">
            <div class="col-md-6">
                  <input type="hidden" name="items_post" value="1" />
                  <input type="hidden" name="id" value="<?php echo $id; ?>" />
                        
                    
                    <div class="form-group">
                      <label class="control-label">TRN No</label>
                     
                        <input type="text" class="form-control" name="trn_no" id="trn_no" value="<?php echo $trn_no; ?>" required/>
                     
                      <!--<a href="add_items.php" class="pj-button add_product" style="display:None">Add Product</a>-->
                    </div>
                    <div class="form-group">
                      <label class=" control-label">Company Name</label>
                      
                        <input type="text" class="form-control" name="company_name" id="company_name" value="<?php echo $company_name; ?>" />
                      </div>
              
                    <div class="form-group">
                      <label class=" control-label">Purchase Date</label>
                      
                        <span class="pj-form-field-custom pj-form-field-custom-after float_left r5">
                          <input type="text" name="purchase_date" id="purchase_date" placeholder="Purchase Date" class="form-control" readonly="readonly"  value="<?php echo $purchase_date; ?>" />
                        </span>
                      </div>
                                
                    <div class="form-group">
                      <label class=" control-label">Invoice No</label>
                                            
                        <input type="text" name="reference_id" id="reference_id" class="form-control" required value="<?php echo $reference_id; ?>" />
                      </div>
             
                    <div class="form-group box-content2">
                      <label class=" control-label">VAT %</label>
                                            
                        <input type="number" name="supplier_vat" id="supplier_vat" value="5" class="form-control" required value="<?php echo $supplier_vat; ?>" />
                      </div>
                   
                    <div class="form-group">
                      <label class=" control-label">Payment Status</label>
                                            
                        <select name="payment_status" id="payment_status" class="form-control" required>          
                          <option value="paid" <?php if($payment_status == 'paid') { echo 'selected'; } ?>>Paid</option>
                          <option value="not_paid" <?php if($payment_status == 'not_paid') { echo 'selected'; } ?>>Not Paid</option>          
                        </select>
                      </div>
               
                    <div class="form-group box-content2">
                      <label class=" control-label">Description</label>
                                         
                        <input type="text" name="description" id="description" class="form-control" value="<?php echo $description; ?>" />
                      </div>
                 
                    <div class="form-group">
                      <label class=" control-label">Category</label>
                                            
                        <select id="expense_category_id" required name="expense_category_id" class="form-control">
                        <?php 
                          $expense_list = getExpenseCategoryList();
                          foreach ($expense_list as $key => $value) { 
                            $selected = ($key == $expense_category_id) ? "selected = selected" : "";
                            echo "<option value=\"".$value['expense_id']."\" ".$selected.">".$value['expense_name']."</option>";
                          }
                        ?>
                        </select>
                      </div>
            
                    <div class="form-group box-content1">
                      <label class=" control-label">Sub Total</label>
                                            
                        <input type="number" name="sub_total" id="sub_total" class="form-control" required value="<?php echo $sub_total; ?>" />
                      </div>
                    
                    <div class="form-group">
                      <label class=" control-label">VAT Amount</label>
                                            
                        <input type="text" name="vat_amount" id="vat_amount" class="form-control" readonly  value="<?php echo $vat_amount; ?>" />
                      </div>
                   
                    <div class="form-group">
                      <label class=" control-label">Net Total</label>
                                            
                        <input type="text" name="net_total" id="net_total" class="form-control" readonly  value="<?php echo $net_total; ?>" />
                      </div>
                                  
                          
                  <div class="form-group">
                    <div class="col-sm-9 col-sm-offset-3">
                      <?php 
                        if(isset($action) && $action == 'edit') {
                      ?>
                        <button type="submit" class="btn btn-primary">Update</button>
                        <a href="expenses.php" class="btn btn-primary">Cancel</a>
                      <?php } 
                        else { 
                      ?>
                        <button type="submit" class="btn btn-primary">Submit</button>
                      <?php 
                        } 
                      ?>
                    </div>
                  </div>
              </div>
                </form>
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
    $('#itemsForm').bootstrapValidator();
    // $("#tblproductClone").formValidation();
 
     $('#purchase_date').datepicker({
      autoclose: true
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
      
      if($unit_price !='' && $qty !='') {
        result = $qty * $unit_price;
        $('#total_amount_'+$index).val(result);
      } else {
        $('#total_amount_'+$index).val('');
      }
    }).on("keyup", ".unit_price", function (e) {
      if (/\D/g.test(this.value)){
        this.value = this.value.replace(/\D/g,'')
      }
      var $unit_price = $(this).val();
      var $index = $(this).attr('data-index');
      var $qty = $("#qty_" + $index).val();

      $qty = !isNaN($qty) ? $qty : 0;
      $unit_price = !isNaN($unit_price) ? $unit_price : 0;

      var result = 0;
      
      if($unit_price !='' && $qty !='') {
        result = $qty * $unit_price;
        $('#total_amount_'+$index).val(result);
      } else {
        $('#total_amount_'+$index).val('');
      }
    });
    
    /*function getProducts($form, $index) {
      $.get("get_items.php", $form.serialize()).done(function (data) {
        //$("a").attr("href", "index.php?controller=pjAdminSuppliers&action=pjActionAddProduct&id="+$("#supplier_id").val());
        $(".add_product").show();
        var index = $index;//$( ".product_id" ).attr('data-index');       
        $("#product_id_"+index).html(data);
      });
    }*/

    $(".box-content1").on("keyup", "#sub_total", function (e) {
      var sub_total = $(this).val();
      var supplier_vat = $('#supplier_vat').val();
      if(supplier_vat > 0) {
        var vat_amount = (sub_total*supplier_vat)/100;
      } else {
        var vat_amount = supplier_vat;
      }
      $('#vat_amount').val(vat_amount);
      $('#net_total').val(Number(vat_amount)+Number(sub_total));
    });

    $(".box-content2").on("keyup", "#supplier_vat", function (e) {
      var supplier_vat = $(this).val();
      var sub_total = $('#sub_total').val();
      if(supplier_vat > 0) {
        var vat_amount = (sub_total*supplier_vat)/100;
      } else {
        var vat_amount = supplier_vat;
      }
      $('#vat_amount').val(vat_amount);
      $('#net_total').val(Number(vat_amount)+Number(sub_total));
    });

  });

     </script>

 

</body>
</html>