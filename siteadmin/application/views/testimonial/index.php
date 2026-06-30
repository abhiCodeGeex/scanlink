<table align="center" width="90%" cellspacing="0" cellpadding="0" border="0">
  <tbody>
    <tr>
      <td class="H1" colspan="2">&nbsp;</td>
    </tr>
    <tr>
      <td width="50%" class="orange13"><img width="13" height="13" src="<?php echo URL::base();?>images/arrow2.jpg"> &nbsp;<strong>Manage Testimonial</strong></td>
      <td align="right" width="50%"><!--<p class="fltrt">&nbsp;&nbsp;&nbsp;</p>--> 
        <a class="fltrt iframe" title="Add Profile" href="<?php echo URL::base();?>testimonial/add" style="font-size:16px"><img src="<?php echo URL::base();?>images/add-client.png" />Add Testimonial</a></td>
    </tr>
    <tr>
      <td align="left" height="8px" colspan="2"></td>
    </tr>
    <tr>
      <td background="images/vdots.gif" colspan="2"><img width="1" height="1" border="0" src="images/spacer.gif"></td>
    </tr>
    <tr>
      <td colspan="2"><form method="post" action="#" name="form2">
          <table width="100%" height="89" cellspacing="1" cellpadding="3" border="0">
            <!--DWLayoutTable-->
            <thead>
              <tr>
                <td colspan="3" align="right"><span style="font-size:16px"> Filter: </span><input type="text" onkeyup="javascript:getResults(1,this.value)" value="" name="filter" id="filter" /></td>
              </tr>
              <tr class="th-a">
                <td align="center" width="65%" class="vgg"><strong>Title</strong></td>
                <td align="center" width="15%" class="vgg"><strong>Created At</strong></td>
                <td align="center" width="20%" height="27" class="vgg"><strong>Option</strong></td>
              </tr>
            </thead>
            <tbody id="records"></tbody>
            <tr align="center">
              <td colspan="3" id="paging"></td>
            </tr>
          </table>
        </form></td>
    </tr>
  </tbody>
</table>
<script type="text/javascript">        

$(document).ready(function(){
	getResults(1,'')
});

function getResults(page,filter){
	var request = $.ajax({
		url:'<?php echo URL::base();?>testimonial/getData',
		type: "POST",
		dataType: "json",
		data:{
			curpage : page,
			perpage : 20,
			entity : 'testimonial',
			filter : filter,
			filter_on : 'title'
		}
		
	});
	
	request.done(function(data) {

		var html="";
		for(var i=0;i<data.results.length;i++){
			
			var imgact = data.results[i].approve==1?"active":"blocked";
			var opp= (imgact=="active")?"blocked":"active";
			var tid = data.results[i].type_id;
			var cname = '';
			if(tid == 1)
				cname = 'plant';
			if(tid == 2)
				cname = 'location';
			if(tid == 3)
				cname = 'asset';
			if(tid == 4)
				cname = 'product';
			if(tid == 5)
				cname = 'procedure';
			
			
			html+='<tr bgcolor="#CCCCCC">'
			 		+'<td align="left" valign="middle" class="vgg1">'+data.results[i].title+'</td>'
					+'<td align="left" valign="middle" class="vgg1">'+data.results[i].created_at+'</td>'
					+'<td align="center" class="vgg1">'
					+'<input type="button" class="black-bt editdlt" onclick="location.href=\'<?php echo url::base(); ?>testimonial/view/'+data.results[i].id+'\'" value="View" name="view">&nbsp;';
					if(data.results[i].type_id != '7'){
						html+='<input type="button" class="black-bt editdlt" onclick="location.href=\'<?php echo url::base(); ?>testimonial/edit/'+data.results[i].id+'\'" value="Edit" name="edit">&nbsp;';
					}
      html+='<input type="button" class="black-bt editdlt" onclick="if(confirm(\'Are you Sure to delelte this ? \')){ location.href=\'<?php echo url::base();  ?>testimonial/delete/'+data.results[i].id+'\'; }" value="Delete" name="delete">'
				+'</td></tr>';
		}
		/*if(html == '' || html.length <= 0){
			html = 'No Result Found..!';
		}*/
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
