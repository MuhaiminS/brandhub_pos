<?php 
  $page = $_SERVER['PHP_SELF'];
  $page = basename($page,".php");
  ?>
<style type="text/css">
  .menu-open ul {display: block !important;}
</style>
<!-- Left side column. contains the logo and sidebar -->

  <aside class="main-sidebar">



    <!-- sidebar: style can be found in sidebar.less -->

    <section class="sidebar">



      <!-- Sidebar user panel (optional) -->

      <div class="user-panel">

        <div class="pull-left image">

          <img src="dist/img/oversee_logo_400x400.jpg" class="img-circle" alt="User Image">

        </div>

        <div class="pull-left info">

          <p><?php echo (isset($_SESSION['user_name'])) ? $_SESSION['user_name'] : ''; ?></p>

          <!-- Status -->

          <a href="#"><i class="fa fa-circle text-success"></i> Online</a>

        </div>

      </div>



      <!-- search form (Optional) -->

      <!--<form action="#" method="get" class="sidebar-form">

        <div class="input-group">

          <input type="text" name="q" class="form-control" placeholder="Search...">

          <span class="input-group-btn">

              <button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i>

              </button>

            </span>

        </div>

      </form>-->

      <!-- /.search form -->



      <!-- Sidebar Menu -->

      <ul class="sidebar-menu" data-widget="tree">

        <li class="header">MAIN NAVIGATION</li>



        <!-- Optionally, you can add icons to the links -->

        <li  class="<?php if($page == 'index'){ echo "active"; } ?>"><a href="index.php"><i class="fa fa-dashboard"></i> <span>Dashboard</span></a></li>

        

		<!-- Products -->

        <li class="treeview <?php  if($page == 'products' || $page == 'products_add'|| $page == 'product_unit'|| $page == 'stock_product') { echo 'active menu-open'; } ?>">

          <a href="#"><i class="fa fa-barcode"></i> <span>Products</span>

            <span class="pull-right-container">

                <i class="fa fa-angle-left pull-right"></i>

              </span>

          </a>

          <ul class="treeview-menu">

            <li class="<?php if($page == 'products'){ echo "active"; } ?>"><a href="products.php"><i class="fa fa-circle-o"></i> List Products</a></li>

            <li class="<?php if($page == 'products_add'){ echo "active"; } ?>"><a href="products_add.php"><i class="fa fa-circle-o"></i> Add Products</a></li>
			
			

            <!--<li><a href="stocks_add.php"><i class="fa fa-circle-o"></i> Add Stocks</a></li>

            <li><a href="#"><i class="fa fa-circle-o"></i> Import Products</a></li>

			<li class="divider"></li>

            <li><a href="#"><i class="fa fa-circle-o"></i> Print Barcodes</a></li>

            <li><a href="#"><i class="fa fa-circle-o"></i> Print Labels</a></li>-->

          </ul>

        </li>



		<!-- Categories -->

        <li class="treeview <?php  if($page == 'categories' || $page == 'categories_add') { echo 'active menu-open'; } ?> ">

          <a href="#"><i class="fa fa-folder"></i> <span>Categories</span>

            <span class="pull-right-container">

                <i class="fa fa-angle-left pull-right"></i>

              </span>

          </a>

          <ul class="treeview-menu">

            <li class="<?php if($page == 'categories'){ echo "active"; } ?>"><a href="categories.php"><i class="fa fa-circle-o"></i> List Categories</a></li>

            <li class="<?php if($page == 'categories_add'){ echo "active"; } ?>"><a href="categories_add.php"><i class="fa fa-circle-o"></i> Add Categories</a></li>

            <!--<li><a href="#"><i class="fa fa-circle-o"></i> Import Categories</a></li>-->

			<li class="divider"></li>
|
          </ul>

        </li>



		<!-- Sales -->

		<li class="treeview <?php if($page == 'sale_orders'||$page == 'combo_sale'){ echo "active menu-open"; } ?>">

          <a href="#"><i class="fa fa fa-shopping-cart"></i> <span>Sales</span>

            <span class="pull-right-container">

                <i class="fa fa-angle-left pull-right"></i>

              </span>

          </a>

          <ul class="treeview-menu">

            <li class="<?php if($page == 'sale_orders'){ echo "active"; } ?>"><a href="sale_orders.php"><i class="fa fa-circle-o"></i> Counter Sales</a></li>
             <!-- <li class="<?php //if($page == 'combo_sale'){ echo "active"; } ?>"><a href="combo_sale.php"><i class="fa fa-circle-o"></i> Combo Sale</a></li> -->

            <!--<li class="<?php //if($page == 'delivery_sale'){ echo "active"; } ?>"><a href="delivery_sale.php?order_type=delivery_sale"><i class="fa fa-circle-o"></i> Delivery Sales</a></li>-->

            <!-- <li class="<?php //if($page == 'whole_sale'){ echo "active"; } ?>"><a href="whole_sale.php?order_type=whole_sale"><i class="fa fa-circle-o"></i>Wholesale Report</a></li> -->
			
			

          </ul>

        </li>
        <!-- <li class="treeview <?php //if($page == 'combo_package'||$page == 'combo_package_add'){ echo "active menu-open"; } ?>">

          <a href="#"><i class="fa fa-bolt"></i> <span>Combo Package</span>

            <span class="pull-right-container">

                <i class="fa fa-angle-left pull-right"></i>

              </span>

          </a>

          <ul class="treeview-menu">

            <li class="<?php// if($page == 'combo_package'){ echo "active"; } ?>"><a href="combo_package.php"><i class="fa fa-circle-o"></i> Combo Package</a></li>
             <li class="<?php //if($page == 'combo_package_add'){ echo "active"; } ?>"><a href="combo_package_add.php"><i class="fa fa-circle-o"></i>Add Combo Package</a></li>

           <li class="<?php //if($page == 'delivery_sale'){ echo "active"; } ?>"><a href="delivery_sale.php?order_type=delivery_sale"><i class="fa fa-circle-o"></i> Delivery Sales</a></li>

           <li class="<?php //if($page == 'whole_sale'){ echo "active"; } ?>"><a href="whole_sale.php?order_type=whole_sale"><i class="fa fa-circle-o"></i>Wholesale Report</a></li> 
      
      

          </ul>

        </li> -->


		<!-- Credit sales -->

		<!--<li class="<?php //if($page == 'credit_sales'){ echo "active"; } ?>"><a href="credit_sales.php"><i class="fa fa-shopping-cart"></i> <span>Credit Sales</span></a></li>-->



		

            



		<!-- People -->

        <li class="treeview <?php if($page == 'drivers' || $page == 'drivers_add' || $page == 'customers' || $page == 'customers_add' || $page == 'staff_salary'|| $page == 'staff_salary_add'|| $page == 'staff_loans'|| $page == 'staff_loans_add' ) { echo 'active menu-open'; } ?>">

          <a href="#"><i class="fa fa-users"></i> <span>People</span>

            <span class="pull-right-container">

                <i class="fa fa-angle-left pull-right"></i>

              </span>

          </a>

          <ul class="treeview-menu">
			<li class="divider"></li>

            <li class="<?php if($page == 'drivers'){ echo "active"; } ?>"><a href="drivers.php"><i class="fa fa-circle-o"></i> List Staff</a></li>

            <li class="<?php if($page == 'drivers_add'){ echo "active"; } ?>"><a href="drivers_add.php"><i class="fa fa-circle-o"></i> Add Staff</a></li>

            <li class="<?php if($page == 'customers'){ echo "active"; } ?>"><a href="customers.php"><i class="fa fa-circle-o"></i> List Customers</a></li>

<li class="<?php if($page == 'customers_add'){ echo "active"; } ?>"><a href="customers_add.php"><i class="fa fa-circle-o"></i> Add Customers</a></li>
             <!-- <li class="<?php //if($page == 'staff_salary'){ echo "active"; } ?>"><a href="staff_salary.php"><i class="fa fa-circle-o"></i> List Staff Salary</a></li>
              <li class="<?php //if($page == 'staff_salary_add'){ echo "active"; } ?>"><a href="staff_salary_add.php"><i class="fa fa-circle-o"></i> Add Staff Salary</a></li>
              <li class="<?php //if($page == 'staff_loans'){ echo "active"; } ?>"><a href="staff_loans.php"><i class="fa fa-circle-o"></i>  Staff Loans</a></li>
                <li class="<?php //if($page == 'staff_loans_add'){ echo "active"; } ?>"><a href="staff_loans_add.php"><i class="fa fa-circle-o"></i>Add  Staff Loans</a></li>
                 <li class="<?php //if($page == 'users'){ echo "active"; } ?>"><a href="users.php"><i class="fa fa-circle-o"></i>List User</a></li> -->
          </ul>
        </li>
        <li class="treeview <?php  if($page == 'settings_add') { echo 'active menu-open'; } ?> ">

          <a href="#"><i class="fa fa fa-gears"></i> <span>Settings</span>

            <span class="pull-right-container">

                <i class="fa fa-angle-left pull-right"></i>

              </span>
          </a>

          <ul class="treeview-menu">

            <li class="<?php if($page == 'settings_add'){ echo "active"; } ?>"><a href="settings_add.php"><i class="fa fa-circle-o"></i> Settings</a></li>

			<li class="divider"></li>

            <!--<li><a href="#"><i class="fa fa-circle-o"></i> List Users Roles</a></li>

            <li><a href="#"><i class="fa fa-circle-o"></i> Add Users Role</a></li>

			<li class="divider"></li>

            <li><a href="#"><i class="fa fa-circle-o"></i> List Stores</a></li>

            <li><a href="#"><i class="fa fa-circle-o"></i> Add Store</a></li>

			<li class="divider"></li>

			<li class="divider"></li>

            <li><a href="#"><i class="fa fa-circle-o"></i> List MF Units</a></li>

            <li><a href="#"><i class="fa fa-circle-o"></i> Add MF Unit</a></li>-->

		  </ul>

        </li>



		<!-- Reports -->

        <li class="treeview <?php if($page == 'reports_settle_sales' || $page == 'account_sheet' || $page == 'reports_item_wise'){ echo 'active menu-open'; } ?>">

          <a href="#"><i class="fa fa-bar-chart-o"></i> <span>Reports</span>

            <span class="pull-right-container">

                <i class="fa fa-angle-left pull-right"></i>

              </span>

          </a>

          <ul class="treeview-menu">

            <li class="<?php if($page == 'reports_settle_sales'){ echo "active"; } ?>"><a href="reports_settle_sales.php"><i class="fa fa-circle-o"></i>Settle Sales</a></li>
			<!--  <li class="<?php if($page == 'account_sheet'){ echo "active"; } ?>"><a href="account_sheet.php"><i class="fa fa-circle-o"></i>Account Sheet</a></li> -->

			

          </ul>
		  <!--<ul class="treeview-menu">-->

    <!--        <li class="<?php //if($page == 'reports_item_wise'){ echo "active"; } ?>"><a href="reports_item_wise.php"><i class="fa fa-circle-o"></i>Item Wise Sales</a></li>-->

			

    <!--      </ul>-->

        </li>
         <li class="treeview <?php  if($page == 'backup_db') { echo 'active menu-open'; } ?> ">
        <a href="#"><i class="fa fa-database"></i> <span>Database Backup</span>
        <span class="pull-right-container">
        <i class="fa fa-angle-left pull-right"></i>
        </span>
        </a>
        <ul class="treeview-menu">
          <li class="<?php if($page == 'backup_db'){ echo "active"; } ?>"><a href="backup_db.php"><i class="fa fa-circle-o"></i>Database Backup</a></li>
          <li class="divider"></li>
        </ul>
      </li>

			

		<!-- Logout -->

        <li class="<?php if($page == 'logout'){ echo "active"; } ?>"><a href="logout.php"><i class="fa fa-sign-out"></i> <span>Logout</span></a></li>

      </ul>

      <!-- /.sidebar-menu -->

    </section>

    <!-- /.sidebar -->

  </aside>