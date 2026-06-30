<?php if(isset($welcome_registration_popup_show) && $welcome_registration_popup_show){ ?>
 <div style="display:none"><a class='welcome_regis' href="#register_content" style="padding: 0;" id="welcome_regis_tag"><input class="welcome_regis" type="button" id="registerbutton" value="Register div" /></a></div>
 <div style='display:none;'>
  <div id='register_content' style='padding:10px; background:#fff;'>
    <div id="main-content-popup">
		<p align="center" style="vertical-align:middle;padding-top: 50px;">
	        
			<span class="nearly-done-title">Welcome!</span>
	        <br/><br>
	        Your first free ScanLink QR Code<br>
			is ready to go to work.<br><br>
			<a href="javascript:;" id="click_here" target="_self">Click here</a><br>
			to watch a brief video on how to get started
	        <br/><br/>
	        
	    </p>
	</div>
    <div id="iframe-content" style="display: none;text-align: center;">
	  	<iframe name="iframe1" id="iframe1" width="640" height="390" src="https://www.youtube.com/embed/o6NxTt0CmYI?rel=0&vq=large" ></iframe>
		
	</div>
  </div>
</div>
<div style="clear: both;"></div>
<script type="text/javascript">
  $(document).ready(function(){
	$(".welcome_regis").colorbox({inline:true, width:"65%",height:"500"});
	<?php if((isset($welcome_registration_popup_show) && $welcome_registration_popup_show) ){ ?>
		$("#welcome_regis_tag").click();
	<?php } ?>
	
	$('#click_here').click(function(){
			$('#main-content-popup').hide();
			$('#iframe-content').show();
		}	
	);
	
	
  });
</script>
<?php } ?>