$('#bizzpro_login').click(function () {
	$('#loader2').html('<img src="img/ajax-loader_small.gif"/>');
	var login_user = '1';
	var email = 'raf.rafees@gmail.com'; //'raf.rafees@gmail.com';
	var password = 'bizzpro'; // 'bizzpro';
	$.ajax({
		  type: "post",
		  url: 'http://connectivelinkstechnology.com/demo/bizzpro/index.php?controller=pjAdmin&action=pjActionLogin',
		  data: "login_user="+login_user+"&login_email="+email+"&login_password="+password,
		  success: function(data) {
			  $('#loader2').hide();
			  window.open('http://connectivelinkstechnology.com/demo/bizzpro/index.php?controller=pjAdmin&action=pjActionIndex', '_blank');
		  }
	});	
});
