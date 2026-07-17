$(document).ready(function(){
	
	$(function(){
		$("input[type='submit'], input[type='button'], input[type='radio'], input[type='checkbox'], select ").uniform();
	});
	
	$('.login-menu').click(function(e){
		e.preventDefault();
		$('.login-menu a').toggleClass('active');
		$('.login-box').slideToggle(500);
	});
	
	$('.top-navigation .menu li a.home, #footer .footer-left ul li a.scan-link').click(function(e){
		e.preventDefault();
		$('.black-tint').show();
		$('.player-window').show(500);
		
	});
	
	$('.top-navigation .menu li#how_to a').click(function(e){
		
		e.preventDefault();
		var url = $(this).attr("href");
		
		if(url != ''){
		  var p = /^(?:https?:\/\/)?(?:www\.)?(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=))((\w|-){11})(?:\S+)?$/;
		  var result = (url.match(p)) ? RegExp.$1 : false;
		  
		  if(result){
			$('#how_to_pop_up').html('<iframe src="'+url+'" frameborder="0" allowfullscreen wmode="transparent" width="480" height="300" ></iframe>');
			$('.black-tint').show();
			$('.player-window').show(500);
		  }else{
			  
			  var substr = 'image#';
			  if(url.indexOf(substr) !== -1){
				  url = url.replace(substr,"");
				  /*$('#how_to_pop_up_image').html('<img src="'+url+'" ></img>');*/
				  
				  $('.black-tint').show();
		  		  $('.player-window-image').show(500);3
			  }else{
			  	return false;
			  }
		  }
		}else{
			return false;
		}
	});
	
	$('.close-btn').click(function(e){
		e.preventDefault();
		$('.black-tint').hide();
		$('.player-window').hide(500);
		$('.player-window-image').hide(500);
		$('.player-window-new').hide(500);
	});
	
	
	
	$('.listing-table').on('click','a.view',function(e){
			e.preventDefault();
			var url = $(this).attr("href");
			
			$('#details_pop_up').html('<iframe src="'+ url +'" frameborder="0" allowfullscreen wmode="transparent" width="700" height="500" ></iframe>');
			
			$('.black-tint').show();
			$('.player-window-new').show(500);
			
		});
	
	
	
});
