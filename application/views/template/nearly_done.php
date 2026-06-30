<?php if(isset($nearly_done_popup_show) && $nearly_done_popup_show){ ?>
 <div style="display:none"><a class='nearly_done' href="#register_content" style="padding: 0;" id="nearly_done_tag"><input class="nearly_done" type="button" id="registerbutton" value="Register div" /></a></div>
 <div style='display:none;'>
  <div id='register_content' style='padding:10px; background:#fff;'>
    <p align="center" style="vertical-align:middle;padding-top: 50px;">
        
		<span class="nearly-done-title">We're nearly done...</span>
        <br/><br>
        We've just sent you an email with a confirmation link.<br>
		Please click on the link to complete your account registration.
        <br/><br/>
        
    </p>
    
  </div>
</div>
<div style="clear: both;"></div>
<script type="text/javascript">
  $(document).ready(function(){
	$(".nearly_done").colorbox({inline:true, width:"45%"});
	<?php if((isset($nearly_done_popup_show) && $nearly_done_popup_show) || (isset($purchase_popup_show) && $purchase_popup_show) || (isset($orderlabel_popup_show) && $orderlabel_popup_show)){ ?>
		$("#nearly_done_tag").click();
	<?php } ?>
  });
</script>
<?php } ?>