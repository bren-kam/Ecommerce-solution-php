jQuery(document).ready( function() {
	
  $('.search').hide();
  
  $('#search-toggle').on('click', function() {
    var hidden = $('.search').is(':visible');
    
    $('.search').slideToggle('fast');
    
    if(!hidden) {
      $('input').focus();
    }
  });
  
  $('.nav li a').click(function(){
    $(this).find('i').toggleClass('fa-close')
  });

	
	$('.slide-btn').on('click', function(e) {
		$('.sidenav').toggleClass('active' ); //you can list several class names 
		e.preventDefault();
	});
	
	$('.slide-btn2').on('click', function(e) {
		$('.sidenav').toggleClass('active' ); //you can list several class names 
		e.preventDefault();
	});
	
	$('.open').on('click',function(e){
	  e.preventDefault();
	  $(this).addClass('hidden');
	  $('.close').removeClass('hidden');
	  $('nav').addClass('slide');
	});

	$('.close').on('click',function(e){
	  e.preventDefault();
	  $(this).addClass('hidden');
	  $('.open').removeClass('hidden');
	  $('nav').removeClass('slide');
	});

	$("nav a").click(function() {
	  
	  var s = $(this).attr('href');
		$('html,body').animate({scrollTop: $(s).offset().top}, 200, "swing");
	  return false;
	  });
	  
	 
	
	
	
});