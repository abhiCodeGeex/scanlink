<?php if(isset($formbuilder_popup_show) && $formbuilder_popup_show){ ?>
 <div style="display:none"><a class='nearly_done' href="#register_content" style="padding: 0;" id="nearly_done_tag"><input class="nearly_done" type="button" id="registerbutton" value="Register div" /></a></div>
 <div style='display:none;'>
  <div id='register_content' style='padding:10px; background:#fff;'>
    <p align="center" style="vertical-align:middle;padding-top: 50px;">
        
		<span class="nearly-done-title">Thank you for your order.</span>
        <br/><br>
        The form builder function is now activated <br>
		for this code profile.
        <br/><br/>
        
    </p>
    
  </div>
</div>
<div style="clear: both;"></div>
<script type="text/javascript">
  $(document).ready(function(){
	$(".nearly_done").colorbox({inline:true, width:"45%"});
	<?php if((isset($formbuilder_popup_show) && $formbuilder_popup_show) ){ ?>
		$("#nearly_done_tag").click();
	<?php } ?>
  });
</script>
<?php } ?>