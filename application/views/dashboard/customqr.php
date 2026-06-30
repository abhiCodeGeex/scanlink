<p><?php /* This is the Dashboard Manager Area. You can manage your profile(s) here. */ ?></p>

<?php echo $innermenu; ?>

<section class="search-right"><!-- PAGE RIGHT SEARCH BOX -->
  <ul class="form-view">
    <li>
      <label>Search</label>
      <input type="text" onKeyUp="javascript:getResults(1,this.value)" value="" name="filter" id="filter" class="text-fi" />
    </li>
    <li><input type="submit" onclick="javascript: window.location.href='<?php echo URL::base(); ?>customqr/index'" value="Add CustomQR" /></li>
  </ul>
</section><!-- /PAGE RIGHT SEARCH BOX -->


<?php if (isset($profile) && !empty($profile)) { ?>
  <table width="100%" cellpadding="0" cellspacing="0" class="listing-table" id="table">
    <thead>
      <tr>
        <th width="10%" class="left-round" align="center"><strong>Profile No.</strong></th>
        <th width="32%"><strong class="txt-left">Url</strong></th>
        <!--<th width="16%"><strong class="txt-left">Telephone</strong></th>-->
        <th width="12%" align="center"><strong class="txt-left">Date</strong></th>
        <th width="25%" class="right-round"><strong class="txt-left">Tools</strong></th>
      </tr>
    </thead>
    <tbody id="records">
    </tbody>
  </table>
  <table width="100%" cellpadding="0" cellspacing="0" id="table">
    <tr>
      <td colspan="6" align="center" id="paging"></td>
    </tr>
  </table>
<?php
} else {
  echo '<div style="text-align:center;color:#5286BE;">No Custom QR Found..!</div>';
}
?>
<script type="text/javascript">        

  $(document).ready(function(){
    getResults(1,'')
  });

  function getResults(page,filter){
    var request = $.ajax({
      url:'<?php echo URL::base(); ?>test/getData',
      type: "POST",
      dataType: "json",
      data:{
        curpage : page,
        perpage : 50,
        entity : 'profiles',
        filter : filter,
        filter_on : 'name',
        params : { type_id:<?php echo $type_id ?>, deleted:0, client_id:<?php echo $client_id ?> }
      }
		
    });
	
    request.done(function(data) {
		
      var html="";
      for(var i=0;i<data.results.length;i++){
			
        var imgact = data.results[i].approve==1?"active":"blocked";
        var opp= (imgact=="active")?"blocked":"active";
			
        html+= '<tr>'+
          '<td align="center" class="left-border">'+data.results[i].id+'</td>'+
          '<td class="product-title">'+data.results[i].name+'</td>'+
          /*'<td>'+data.results[i].telephone+'</td>'+*/
        '<td align="center">'+data.results[i].created_at+'</td>'+
          '<td class="right-border"><table width="100%" cellspacing="0" cellpadding="0" class="table-n">'+
          '<tbody><tr>'+
          '<td align="center" class="border-none">&nbsp;</td>'+
          '<td align="center" class="border-none"><a class="Box_recycle tooltip" href="<?php echo URL::base(); ?>dashboard/scanalytics/'+data.results[i].id+'" title="Scanalytics"><img src="<?php echo URL::base(); ?>images/scanalytics-icon.png" /></a></td>'+
          '<td align="center" class="border-none"><a class="Box_recycle tooltip" href="<?php echo URL::base(); ?>dashboard/getQR/'+data.results[i].qr_id+'" title="Download QR Code"><img src="<?php echo URL::base(); ?>images/download-QR-code-icon.png" /></a></td>'+
          '<td align="center" class="border-none"><a class="Box_recycle tooltip" href="<?php echo URL::base(); ?>customqr/delete/'+data.results[i].id+'" title="Delete" onclick="return confirm(\'Are your Sure ?\')" ><img src="<?php echo URL::base(); ?>images/delete-icon.png" /></a></td>'+
          '<td align="center" class="border-none">&nbsp;</td>'+
          '</tr></tbody>'+
          '</table></td>'+
          '</tr>';
      }
      $("#records").html(html);
			
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