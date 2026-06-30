<?php if ($errormessage != ""): ?>
  <div class="row-r"><label  class="error"><?php echo $errormessage; ?></label></div>
  <div class="clear">&nbsp;</div>
<?php endif; ?>
<?php if ($successmessage != ""): ?>
  <div class="row-r"><label class="success" ><?php echo $successmessage; ?></label></div>
  <div class="clear">&nbsp;</div>
<?php endif; ?>

<div class="colorcode_define" style="width:38%">
  	<div class="mainbox">
    	<div class="colorcodebox red">&nbsp;</div>
        <div class="colorbox_text">Exipred</div>
    </div>
    <div class="mainbox">
    	<div class="colorcodebox orange">&nbsp;</div>
        <div class="colorbox_text">Expires within 30 days</div>
    </div>
    <div class="mainbox">
    	<div class="colorcodebox gray">&nbsp;</div>
        <div class="colorbox_text">Expires within 3 days</div>
    </div>
  </div>

<section class="search-right"><!-- PAGE RIGHT SEARCH BOX -->
  <ul class="form-view">
    <!--- WI_MS START ---->
    <li>
      <label>Sort By</label>
      <select id="sorton" name="sorton" onchange="javascript:getResults(1,document.getElementById('filter').value,this.value,document.getElementById('sortby').value)">
        <option value="">Select sort on option</option>
        <option value="id">Profile No.</option>
        <option value="code_profile_name">Code Profile Name</option>
        <option value="expired_at">Expiry Date</option>
      </select>
      <div class="AscDec">
        <select id="sortby" name="sortby" onchange="javascript:getResults(1,document.getElementById('filter').value,document.getElementById('sorton').value,this.value)">
          <option value="asc">Asc</option>
          <option value="desc">Desc</option>
        </select>
      </div>
    </li>
    <!--- WI_MS START ---->
    <li>
      <label>Search</label>
      <input type="text" onKeyUp="javascript:getResults(1,this.value,document.getElementById('sorton').value,document.getElementById('sortby').value)" value="" name="filter" id="filter" class="text-fi" />
    </li>
	
	<?php
	if ($is_sub_user=="0")
	{
	?>
    <li>
      <?php if (isset($profiles) && !empty($profiles)) { ?>
        <input type="button" id="renew_code" name="renew_code"   value="Renew Selected Codes" />
      <?php } else { ?>
        <input type="button" disabled="disabled"  value="Renew Selected Codes" />
      <?php } ?>
    </li>
	<?php
	}//end of if ($is_sub_user=="0")
	?>
    
	
  </ul>
</section><!-- /PAGE RIGHT SEARCH BOX -->


<?php if (isset($profiles) && !empty($profiles)) { ?>
  <?php
  echo Form::open(
          URL::base() . 'code/renewCode', array(
      'id' => 'frmdashcode',
      'name' => 'frmdashcode',
      'method' => 'post',
      'enctype' => 'multipart/form-data'
          )
  );
  ?>
  
  <input  type="hidden" name="is_sub_user" id="is_sub_user" value="<?php echo $is_sub_user;?>"/>
  <input  type="hidden" name="access_edit" id="access_edit" value="<?php echo $user["access_edit"];?>"/>
  <input  type="hidden" name="access_analytics" id="access_analytics" value="<?php echo $user["access_analytics"];?>"/>
  <input  type="hidden" name="access_form_submission" id="access_form_submission" value="<?php echo $user["access_form_submission"];?>"/>
  <input  type="hidden" name="access_download" id="access_download" value="<?php echo $user["access_download"];?>"/>
  <input  type="hidden" name="access_label" id="access_label" value="<?php echo $user["access_label"];?>"/>
  <input  type="hidden" name="access_log" id="access_log" value="<?php echo $user["access_log"];?>"/>
  <input  type="hidden" name="access_delete" id="access_delete" value="<?php echo $user["access_delete"];?>"/>
  <div class="clear"></div>
  <table width="100%" cellpadding="0" cellspacing="0" class="listing-table" id="table">
    <thead>
      <tr>
        <th width="10%" class="left-round" align="center"><strong>Profile No.</strong></th>
        <th align="left"><strong class="txt-left">Code Profile Name</strong></th>
        <th width="20%"  align="center"><strong class="txt-left">Code expiry date</strong></th>
        <th width="13%" align="center"><strong class="txt-left">Select All
				&nbsp;<input type="checkbox" id="checkall">        
        </strong></th>
      </tr>
    </thead>
    <tbody id="records">
	</tbody>
  </table>
  <?php echo Form::close() ?>
  <table width="100%" cellpadding="0" cellspacing="0" id="table">
    <tr>
      <td colspan="5" align="center" id="paging"></td>
    </tr>
  </table>

<?php
} else {
  echo '<div class="clear"></div><div style="text-align:center;color:#5286BE;">No Profile Found..!</div>';
}
?>



<script type="text/javascript">      

  
  <!--- WI_MS START ---->
<?php /* added two parameter in getresults() */ ?>
  $(document).ready(function(){
    getResults(1,'','','asc');
  });

  $('#renew_code').click(function(){
		
    var isSelected = false;
    $('.selectprofile').each(function(){
      if($(this).is(':checked')){
        isSelected = true;			
      }
    });
    if(isSelected){
      $('#frmdashcode').submit();
    }else{
      alert("Please select the code to be renewed.");
      return false;
    }
    return false;
  });
  
  function getResults(page,filter,sorton,sortby){
    if(sorton == ''){
      sorton = 'expired_at';
    }
   
    var request = $.ajax({
      url:'<?php echo URL::base(); ?>test/getExpiredProfile',
      type: "POST",
      dataType: "json",
      data:{
        curpage : page,
        perpage : 50,
        entity : 'expired_profiles',
        filter : filter,
        filter_on : sorton,
        filter_by : sortby,
        params : { deleted:0, user_id:<?php echo $user_id ?> }
      }
		
    });
	
    request.done(function(data) {
		
      var html="";
	  
	  is_sub_user = document.getElementById('is_sub_user').value;
	  access_edit = document.getElementById('access_edit').value;
	  access_analytics = document.getElementById('access_analytics').value;
	  access_form_submission = document.getElementById('access_form_submission').value;
	  access_download = document.getElementById('access_download').value;
	  access_label = document.getElementById('access_label').value;
	  access_log = document.getElementById('access_log').value;
	  access_delete = document.getElementById('access_delete').value;
	  
	  
      for(var i=0;i<data.results.length;i++){
			
        var imgact = data.results[i].approve==1?"active":"blocked";
        var opp= (imgact=="active")?"blocked":"active";
			
        		 
		if(data.results[i].free_code=="1" && data.results[i].renewal_required=="0") 
		{
			exp_date = "N/A";
			checkbox_disabled = 'disabled="disabled"';
		}
		else
		{
			exp_date = data.results[i].expired_at;
			checkbox_disabled = '';
		}
		
		my_class = "";
		if(data.results[i].date_diff<=30 && data.results[i].date_diff>3)
			my_class = "orange";
		if(data.results[i].date_diff<=0)
			my_class = "red";
			
		if(data.results[i].date_diff<=3 && data.results[i].date_diff>0)
			my_class = "gray";
		
		
				
      html+= '<tr class="'+my_class+'">'+
          '<td align="center" class="left-border">'+data.results[i].id+'</td>'+
          '<td class="product-title">'+data.results[i].code_profile_name+'</td>'+
          '<td align="center">'+exp_date+'</td>'+
          '<td align="center"><input type="checkbox" class="selectprofile" '+ checkbox_disabled + ' id="selectprofile'+data.results[i].id+'" name="selectprofile[]" value="'+data.results[i].id+'" /></td>'+
          '</tr>';
				
      }
      $("#records").html(html);
      $(".selectprofile").uniform();
		 
      var html =[];
      for(var i=0;i<data.links.length;i++){
        html.push('<a href="javascript:'+data.links[i].href+'" class="'+data.links[i].myClass+'">'+data.links[i].label+'</a>');
      }
      if(data.results.length>0){
        $("#paging").html(html.join(""));
      }else{
        $("#paging").html("<h3>No Results Found</h3>");
      }
		
    });
	
    request.fail(function(jqXHR, textStatus) {
      alert( "Request failed: " + textStatus );
    });
  }
</script>
<script type="text/javascript">
  $(document).ready(function(){
  	
	 $('#table').on('click','#checkall',function () {
	 	$('.selectprofile').attr('checked', $(this).is(':checked'));
	 	
	 	if($(this).is(':checked')==true)
			$('.selectprofile').closest('span').addClass('checked');	 	
		else
			$('.selectprofile').closest('span').removeClass('checked');	
		
	 	
	 	
	 });
  	
  	
    $(".inline").colorbox({inline:true, width:"25%"});
    $(".register").colorbox({inline:true, width:"25%"});
<?php if ((isset($orderlabel_popup_show) && $orderlabel_popup_show)) { ?>
        $("#registertag").click();
<?php } ?>
    });
</script>