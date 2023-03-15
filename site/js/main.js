 $(document).ready(function(){
   $('ul.nav li').hover(function() {
  $(this).find('.dropdown-menu').stop(true, true).delay(200).slideDown(500);
}, function() {
  $(this).find('.dropdown-menu').stop(true, true).delay(200).slideUp(500);
});

 $("a[rel^='prettyPhoto']").prettyPhoto({
		theme: 'light_square',
		social_tools: false,
		horizontal_padding: 10,
		overlay_gallery: false
		});


 
    


$('#carousel-example-generic1').carousel({
        interval: 500
    });

$('#carousel-example-generic3').carousel({
        interval: 1000
    });
  });