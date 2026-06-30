$(document).ready(function(){
	$('.addP_field').click(function(){
		var input = $('#logo_name1');
		var clone = input.clone(true);
		clone.removeAttr ('id');
		clone.val('');
		clone.appendTo('.input_holder');
	});
	$('.removeP_field').click(function(){
		if($('.input_holder input:last-child').attr('id') != 'logo_name1'){
			$('.input_holder input:last-child').remove();
		}
	});
	$('.addD_field').click(function(){
		var input1 = $('#doc_title1');
		var clone1 = input1.clone(true);
		clone1.removeAttr ('id');
		clone1.val('');
		clone1.appendTo('.doc_holder');
		var input = $('#doc_name1');
		var clone = input.clone(true);
		clone.removeAttr ('id');
		clone.val('');
		clone.appendTo('.doc_holder');
		
	});
	$('.removeD_field').click(function(){
		if($('.doc_holder input[type=file]:last-child').attr('id') != 'doc_name1'){
			$('.doc_holder input[type=file]:last-child').remove();
			$('.doc_holder input[type=text]:last-child').remove();
		}
	});
	
	$('.addV_field').click(function(){
		var input1 = $('#vid_title1');
		var clone1 = input1.clone(true);
		clone1.removeAttr ('id');
		clone1.val('');
		clone1.appendTo('.doc_holder');
		var input = $('#vid_name1');
		var clone = input.clone(true);
		clone.removeAttr ('id');
		clone.val('');
		clone.appendTo('.vid_holder');
	});
	$('.removeV_field').click(function(){
		if($('.vid_holder input[type=file]:last-child').attr('id') != 'vid_name1'){
			$('.vid_holder input[type=file]:last-child').remove();
			$('.vid_holder input[type=text]:last-child').remove();
		}
	});
}); 