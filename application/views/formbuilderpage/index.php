<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<!-- CONTENT CONTAINER -->
<div class="formbuilder-container">
		<div class="Creat-Mobile"></div>
		<div class="creat-Text">Create mobile interactive<br>
  		forms, surveys and checklists
		</div>
		<div class="Left-imges"><img src="<?php echo URL::base() ?>images/Civil-mobile.png"  height="310"></div>
		<div class="clear"></div>

		<div class="Blue-Belt"> <span>ScanLink Form Builder enables you to quickly and easily create<br>
		  mobile interactive forms that can be completed on a smartphone<br>
		  or tablet then submitted and recorded instantly</span><br>
		  <b>- no paperwork, no filling!</b>
		 </div>
		 
		<section class="Give-it">
		  <div class="Give-Left">
		    <div class="Give-Text">Give it a try...</div>
		    <div class="Qr-Code"><img src="<?php echo URL::base() ?>images/Qr-Code.png" width="120" height="120"></div>
		    <div class="Scan-thiscode"><img src="<?php echo URL::base() ?>images/scan-this-code.png" width="150" height="36"></div>
			<div class="errow"><img src="<?php echo URL::base() ?>images/errow.png"></div>
		  </div>
		  <div class="Give-Right">
		   	<iframe width="480" height="300" frameborder="0" allowfullscreen="" src="https://www.youtube.com/embed/MK6pIu_gf0A?rel=0&amp;autoplay=0&amp;loop=0&amp;wmode=opaque" marginwidth="0" marginheight="0"></iframe>
		  </div>
		  <div class="clear"></div>
		</section>
		
		<section class="Process">
		  <div class="blue-text">Mobile & tablet users scan<br>
		    the code to instantly<br>
		    access the form</div>
		  <div class="mobile-text">Create your mobile<br>
		    interactive form and <br>
		    print your code</div>
		  <div class="the-Form">The form is completed <br>
		    on the mobile device</div>
		  <div class="centar-Text">The process is simple</div>
		  <div class="Process-images"> &nbsp; </div>
		  <div class="Bottom"><img src="<?php echo URL::base() ?>images/4.png" width="200" height="54"></div>
		  <div class="Bottom-Text">The date, time, device type, location<br>
		    and response is recorded instantly</div>
		</section>
		<div class="clear"></div>
		
		
		<section class="Register_section">
		  <div class="Easy-Text"> Its easy and you can get started for FREE...</div>
		  <div class="Register-Part">
		  <div class="Register-Left"><img src="<?php echo URL::base() ?>images/300X258.png" width="300" height="258"></div>
		  <div class="Register-Right"><b>Register</b><br>
		    Open your ScanLink account with your first<br>
		    dynamic QR Code for free for one year!<br>
		    <div class="Show-Me-More">SHOW ME HOW <a href="https://www.youtube.com/embed/9aTjweHyAWw?rel=0" class="youtube_link" title="Create a ScanLink account"><img src="<?php echo URL::base() ?>images/paly.png" align="top" width="30" height="30"></a></div>
		  </div>
		</section>
		<div class="clear"></div>
		
		<section class="Build">
		  <div class="Build-left">
		    <h3>Build your forms</h3>
		    <div class="Register-Right2">Simple click & drag edit tools<br>
		      make it easy to create Audit<br>
		      forms, Pre-Start checklist, surveys<br>
		      and more</div>
		    <div class="Show-Me-More">SHOW ME HOW <a href="https://www.youtube.com/embed/cYQnzxkp528?rel=0" title="Create a form" class="youtube_link"><img src="<?php echo URL::base() ?>images/paly.png" align="top" width="30" height="30"></a></div>
		  </div>
		  <div class="Build-Right"><img src="<?php echo URL::base() ?>images/form_builder.png" width="300" height="341"></div>
		</section>
		<div class="clear"></div>
		
		<section class="Publish-Code">
		  <div class="Publish-Left"> <img src="<?php echo URL::base() ?>images/Qr-Code-Dawnload.png" width="200" height="301"> </div>
		  <div class="Publish-Right">
		    <div class="Register-Right3"><b>Publish your code</b><br>
		      Download and print your ScanLink<br>
		      QR or Data Matrix code<br>
		      <div class="Show-Me-More">SHOW ME HOW <a href="https://www.youtube.com/embed/MrsyIv-72YI?rel=0" title="How to download a code" class="youtube_link" ><img src="<?php echo URL::base() ?>images/paly.png" align="top" width="30" height="30"></a></div>
		    </div>
		  </div>
		  
		  <div class="clear">&nbsp;</div>
		  <div class="clear">&nbsp;</div>
		  <div class="clear">&nbsp;</div>
		  <div class="clear">&nbsp;</div>
		  <div class="Build-left">
		    <h3>Record the results</h3>
		    <div class="Register-Right2">Form responses are recorded in your <br>
		      ScanLink account where you can<br>
		      view or download them anytime.<br>
		      They can also be sent automatically<br>
		      to email.</div>
		    <div class="Show-Me-More">SHOW ME HOW <img src="<?php echo URL::base() ?>images/paly.png" align="top" width="30" height="30"></div>
		  </div>
		  <div class="Build-Right"><img src="<?php echo URL::base() ?>images/Screen-shot.png" width="300" height="231"></div>
		</section>
		<div class="clear"></div>
		
		
		<div class="Headding">There are so many ways ScanLink mobile interactive<br>
		  forms can save you time and money...</div>
		<section class="Last">
		  <div class="menu-3">Site inductions<br>
		    Petitions<br>
		    Education<br>
		    Order Forms</div>
		  <div class="menu-3">Customer Survey's<br>
		    Pre-Start Checklists<br>
		    Competition Entries<br>
			and more...</div>
		  <div class="menu-4">Quality Checks <br>
		    Enquiry forms<br>
		    Registration forms</div>
		  <div class="clear"></div>
		  <button class="Register-Account" type="button" onclick="location.href='<?php echo URL::base() ?>register/index'">Click here to get started with your first code FREE for a year!</button>
		</section>
</div>
<script>
		
	$('.youtube_link').click(function(e){
		
		e.preventDefault();
		var url = $(this).attr("href");
		
		if(url != ''){
		  var p = /^(?:https?:\/\/)?(?:www\.)?(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=))((\w|-){11})(?:\S+)?$/;
		  var result = (url.match(p)) ? RegExp.$1 : false;
		  
		  if(result){
			$('#how_to_pop_up').html('<iframe src="'+url+'" frameborder="0" allowfullscreen wmode="transparent" width="480" height="300" ></iframe>');
			$('.black-tint').show();
			$('.player-window').show(500);
		  }
		}else{
			return false;
		}
	});
</script>