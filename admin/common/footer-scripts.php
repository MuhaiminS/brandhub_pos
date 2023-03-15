<!-- jQuery 3 -->
<script src="bower_components/jquery/dist/jquery.min.js"></script>

<!-- Bootstrap 3.3.7 -->
<script src="bower_components/bootstrap/dist/js/bootstrap.min.js"></script>

<!-- Bootstrap Validator -->
<script src="dist/js/bootstrapValidator.min.js"></script>

<!-- Select2 Products add page only-->
<script src="bower_components/select2/dist/js/select2.full.min.js"></script>

<!-- bootstrap datepicker pages (add purcahses) only-->
<script src="bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>

<!-- DataTables products page only -->
<script src="bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
<!-- SlimScroll -->
<script src="bower_components/jquery-slimscroll/jquery.slimscroll.min.js"></script>

<!-- FastClick -->
<script src="bower_components/fastclick/lib/fastclick.js"></script>

<!-- AdminLTE App -->
<script src="dist/js/adminlte.min.js"></script>

<!-- validation -->
<script src="js/validation.js"></script>

<!-- List products page script -->
<script>
  $(function () {
    /*$('#example1').DataTable()*/
    $('#example2').DataTable({
      'paging'      : true,
      'lengthChange': true,
      'searching'   : true,
      'ordering'    : false,
      'info'        : true,
      'autoWidth'   : true
    })
  })
</script>

<!-- List products page script -->
<script>
  $(function () {
    //Initialize Select2 Elements
    $('.select2').select2();
  })
</script>

<!-- Add purchase page script -->
<script>
  $(function () {
	//Date picker
	$('.datepicker').datepicker({
      format: 'yyyy-mm-dd',
	  autoclose: true
	})
  })
</script>

<script>
var site_url = "<?php echo getServerURL(); ?>";
</script>