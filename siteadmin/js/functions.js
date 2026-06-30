jQuery(document).ready(function(){
	jQuery(".edt").colorbox({iframe:true, width:"30%", height:"55%", onClosed:function(){parent.location.reload()}
});
	jQuery(".edtplant").colorbox({iframe:true, width:"30%", height:"55%", onClosed:function(){parent.location.reload()}
});
});
jQuery(function() {
		jQuery( "#txtRegidate" ).datepicker({dateFormat: 'yy-mm-dd'}); //2012-03-22 04:39:32
	});