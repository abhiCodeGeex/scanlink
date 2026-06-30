// JavaScript Document
function deleteLogo(profileid,base_URL){
    if(confirm("Are you sure to remove this Logo ? ")){
      var copyProgress = $("#progressbar").clone();
      copyProgress.attr('id','progressbar1');
      copyProgress.html('<img align="absmiddle" src="'+base_URL+'images/spinner_gray.gif"> Please Wait. Deleting Logo...');
	  
	  $("#progressbar").after(copyProgress);
      $("#progressbar1").show();
	   
	   var request = $.ajax({
                url:base_URL+'dashboard/deletelogo',
                type: "POST",
                dataType: "json",
                data:{
                    profile_id  : profileid
                }
			
         });
		request.done(function(data) {
			$("#progressbar1").remove();
			if(data.error == 1){
				alert(data.message);
				return false;
			}else{
				$("#logo_data").empty();
				alert(data.message);
				return false;
			}
		});
			
      
    }
  }
  
  
  function deleteLogo2(profileid,base_URL){
    if(confirm("Are you sure to remove this Logo ? ")){
      var copyProgress = $("#progressbar").clone();
      copyProgress.attr('id','progressbar1');
      copyProgress.html('<img align="absmiddle" src="'+base_URL+'images/spinner_gray.gif"> Please Wait. Deleting Logo...');
	  
	  $("#progressbar").after(copyProgress);
      $("#progressbar1").show();
	   
	   var request = $.ajax({
                url:base_URL+'dashboard/deletelogo_extra',
                type: "POST",
                dataType: "json",
                data:{
                    profile_id  : profileid
                }
			
         });
		request.done(function(data) {
			$("#progressbar1").remove();
			if(data.error == 1){
				alert(data.message);
				return false;
			}else{
				$("#logo_data_extra").empty();
				alert(data.message);
				return false;
			}
		});
			
      
    }
  }