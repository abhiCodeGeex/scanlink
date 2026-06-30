<table align="center" width="90%" cellspacing="0" cellpadding="0" border="0">
  <tbody>
  
  <tr>
    <td class="H1" colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td width="50%" class="orange13"><img width="13" height="13" src="<?php echo URL::base();?>images/arrow2.jpg"> &nbsp;<strong>Manage Client </strong></td>
    <td align="right" width="50%"><p class="fltrt">&nbsp;&nbsp;&nbsp;</p>
      <a class="fltrt" title="Add client" href="<?php echo URL::base();?>client/add"><img src="<?php echo URL::base();?>images/add-client.png" /></a></td>
  </tr>
  <tr>
    <td align="left" height="8px" colspan="2"></td>
  </tr>
  <tr>
    <td background="images/vdots.gif" colspan="2"><img width="1" height="1" border="0" src="images/spacer.gif"></td>
  </tr>
  <tr>
    <td colspan="2"><table class="client-list" width="100%" height="89" cellspacing="1" cellpadding="3" border="0">
        <!--DWLayoutTable-->
        <tbody>
          <tr>
            <td height="5" colspan="8"></td>
          </tr>
          <tr>
            <td colspan="8" align="right"><span style="font-size:16px"> Filter: </span><input type="text" onkeyup="javascript:getResults(1,this.value)" value="" name="filter" id="filter" /></td>
          </tr>
          <tr class="th-a">
            <td align="center" height="27" width="20%" class="vgg first"><strong>Client Name</strong></td>
            <td align="center" width="20%" class="vgg"><strong>Address</strong></td>
            <td align="center" width="10%" class="vgg"><strong>Telephone</strong></td>
            <td align="center" width="15%" class="vgg"><strong>Contact Person</strong></td>
            <td align="center" width="10%" class="vgg"><strong>Registration Date</strong></td>
            <td align="center" width="15%" class="vgg"><strong>Email</strong></td>
            <td align="center" width="10%" class="vgg"><strong>URL</strong></td>
            <td align="center" height="27" class="vgg last"><strong>Option</strong></td>
          </tr>
        <tbody id="records">
        </tbody>
        <tr align="center">
          <td colspan="8" id="paging"></td>
        </tr>
     	</tbody>
      </table></td>
  </tr>
  </tbody>
  
</table>
<script type="text/javascript">        

$(document).ready(function(){
	getResults(1,'')
});

function getResults(page,filter){
	var request = $.ajax({
		url:'<?php echo URL::base();?>client/getData',
		type: "POST",
		dataType: "json",
		data:{
			curpage : page,
			perpage : 20,
			entity : 'clients',
			filter : filter,
			filter_on : 'client_name'
		}
		
	});
	
	request.done(function(data) {
		var html="";
		for(var i=0;i<data.results.length;i++){
			//var checklist_option = (data.results[i].checklist_option==0)?'<a href="<?php echo URL::base();?>client/checklist_option/'+data.results[i].id+'" class="option mlft" title="Checklist Option Disallowed"> <img src="<?php echo URL::base();?>images/checklist-disallowed.gif" width="20" /> </a>':'<a href="<?php echo URL::base();?>client/checklist_option/'+data.results[i].id+'" class="option mlft" title="Checklist Option Allowed"> <img src="<?php echo URL::base();?>images/checklist-allowed.gif" width="20" /> </a>';
			//var customqr_option = (data.results[i].customqr_option==0)?'<a href="<?php echo URL::base();?>client/customqr_option/'+data.results[i].id+'" class="option mlft" title="CustomQR Option Disabled"> <img src="<?php echo URL::base();?>images/customqr_disabled.png" width="20" /> </a>':'<a href="<?php echo URL::base();?>client/customqr_option/'+data.results[i].id+'" class="option mlft" title="CustomQR Option Enabled"> <img src="<?php echo URL::base();?>images/customqr_enabled.png" width="20" /> </a>';
			var checklist_option = '';
			var customqr_option = '';
			var imgact = data.results[i].approve==1?"active":"blocked";
			var opp= (imgact=="active")?"blocked":"active";
			 html+='<tr bgcolor="#CCCCCC">';
			 
			 if(data.results[i].reseller_code!="")
			 	html+='<td align="left" valign="middle" class="vgg1" style="line-height:20px;"><div class="rounded-green-alphbet">R</div>'+data.results[i].client_name+'</td>'
			 else 
			 	html+='<td align="left" valign="middle" class="vgg1">'+data.results[i].client_name+'</td>'	
			
			
			 html+='<td align="left" valign="middle" class="vgg1">'+data.results[i].address+'</td>'
					+'<td align="left" valign="middle" class="vgg1">'+data.results[i].telephone+'</td>'
					+'<td align="left" valign="middle" class="vgg1">'+data.results[i].contact_person+'</td>'
					+'<td align="left" valign="middle" class="vgg1">'+data.results[i].regi_date+'</td>'
					+'<td align="left" valign="middle" class="vgg1">'+data.results[i].email+'</td>'
					+'<td align="left" valign="middle" class="vgg1">'+data.results[i].url+'</td>'
					+'<td align="center" class="vgg1">'
						+'<div class="optionwrap newoptionwrap"> <a href="<?php echo URL::base();?>user/index/'+data.results[i].id+'" class="option mlft" title="User\'s list"> <img src="<?php echo URL::base();?>images/users.png" width="20" /> </a> <a href="<?php echo URL::base();?>client/edit/'+data.results[i].id+'" class="option mlftrt" title="Edit client"> <img src="<?php echo URL::base();?>images/edit.png" width="20" /> </a> <a href="<?php echo URL::base();?>client/blocked/'+data.results[i].id+'" onclick="if(confirm(\'Are you sure to '+opp+' this client?\')){ return true; }else{ return false; }" class="option" title="click to '+opp+'"> <img src="<?php echo URL::base();?>images/'+imgact+'.png" width="20" /> </a> <a href="<?php echo URL::base();?>client/delete/'+data.results[i].id+'" onclick="if(confirm(\'Are you sure to delete this client?\')){ return true; }else{ return false; }" class="option mlft" title="Delete client"> <img src="<?php echo URL::base();?>images/delete.png" width="20" /> </a>'+checklist_option+customqr_option+'</div></td>'
				+'</tr>';
				
				
		}
		$("#records").html(html);
			
		var html =[];
		for(var i=0;i<data.links.length;i++){
			html.push('<a href="javascript:'+data.links[i].href+'" class="'+data.links[i].myClass+'">'+data.links[i].label+'</a>');
		}
		if(data.results.length>0){
			$("#paging").html(html.join(" | "));
		}else{
			$("#paging").html("<h3>No Results Found</h3>");
		}
		
	});
	
	request.fail(function(jqXHR, textStatus) {
		alert( "Request failed: " + textStatus );
	});
}
</script>