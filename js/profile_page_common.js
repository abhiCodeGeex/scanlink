// JavaScript Document
var saveornot = true;
function logoutFunction(msgPopup){
	if(msgPopup == "true") {
		if(saveornot == true){
			location.href = url_base()+'logout/index';
		}else{
			//$("#popup_logout_tag").click();
		}
	} else {
		location.href = url_base()+'logout/index';
	}
}
// before logout this function is call.

window.onunload = window.onbeforeunload = (function() {
  return function() {
  	
   
   return "Have you saved any changes made ? ";
  }
}());

$(document).ready(function(){
	$( "input[type=text],input[type=checkbox],input[type=radio],input[type=file],textarea" ).change(function() {
    	saveornot = false;
	});
	
});

function beforeSave(){
	
	
	saveornot = true;
	window.onbeforeunload = null;
}

// check for valid date format dd/mm/yyyy
function is_valid_date(date)
{
		date_arr = date.split('/');
		
		if(date_arr.length<3)
		{
			return false;
		}
		else 
		{
			date_day = date_arr[0];
			date_month = date_arr[1];
			date_year = date_arr[2];
			
			if(date_year.length!=4)
				  return false;		
			
			if (date_month < 1 || date_month > 12) 
		        return false;
		   else if (date_day < 1 || date_day> 31) 
		        return false;
		   else if ((date_month==4 || date_month==6 || date_month==9 || date_month==11) && date_day ==31) 
		        return false;
		   else if (date_month == 2) 
		   {
		        var isleap = (date_year % 4 == 0 && (date_year % 100 != 0 || date_year % 400 == 0));
		        if (date_day> 29 || (date_day ==29 && !isleap)) 
		                return false;
		   }
		   return true;			
					
		}
}

// check for date range format dd/mm/yyyy
function validate_date_range(from_date,to_date)
{
		
		from_date_arr = from_date.split('/');
		
		from_date_day = from_date_arr[0];
		from_date_month = from_date_arr[1];
		from_date_year = from_date_arr[2];
		
		to_date_arr = to_date.split('/');
		
		to_date_day = to_date_arr[0];
		to_date_month = to_date_arr[1];
		to_date_year = to_date_arr[2];
				
		if(from_date_year>to_date_year)
		{
			return false;
		}
		else
		{
			if(from_date_month>to_date_month && from_date_year==to_date_year)
			{
				return false;
			}
			if(from_date_day>to_date_day && from_date_month==to_date_month && from_date_year==to_date_year)
			{
				return false;
			}
		}	
		return true;
		
}