<?php if(isset($orderlabel_popup_show) && $orderlabel_popup_show){ ?>
 <div style="display:none"><a class='register' href="#register_content" style="padding: 0;" id="registertag"><input class="register" type="button" id="registerbutton" value="Register div" /></a></div>
 <div style='display:none;'>
  <div id='register_content' style='padding:10px; background:#fff;'>
    <p>
        Your order has been successful and your 
        <br/>
        ScanLink label/s will be dispatched within 
        <br/>
        the next 48 hours
    </p>
    
  </div>
</div>
<script type="text/javascript">
  $(document).ready(function(){
	$(".register").colorbox({inline:true, width:"25%"});
	<?php if((isset($register_popup_show) && $register_popup_show) || (isset($purchase_popup_show) && $purchase_popup_show) || (isset($orderlabel_popup_show) && $orderlabel_popup_show)){ ?>
		$("#registertag").click();
	<?php } ?>
  });
</script>
<?php } ?>