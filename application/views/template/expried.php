 <div style="display:none"><a class='expried' href="#expried_content" style="padding: 0;" id="expriedtag"><input class="expried" type="button" id="expriedbutton" value="expried div" /></a></div>
 <div style='display:none;'>
  <div id='expried_content' style='padding:10px; background:#fff;'>
    <p>
       You can not perform this action on an expired code profile.<br>
		Please renew this code to restore its Functions.
    </p>
    
  </div>
</div>
<script type="text/javascript">
$(document).ready(function(){
	$('.red').find('a').die('click').live('click', function(){
      if($(this).hasClass('Edit') || $(this).hasClass('Scanalytics') || $(this).hasClass('Download') || $(this).hasClass('OrderLabels') || $(this).hasClass('Delete')){
        //alert ('You can not perform this action on expired profile');
		$(".expried").colorbox({inline:true, width:"25%"});
  	    $("#expriedtag").click();
        return false;
      }
    });
});
</script>
