<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<!-- MARKETING BLOCK -->
<section class="marketing-block padding-Top80 clearfix">
	<p class="marketing-info">Add value to products and services and make your advertising, packaging and signage mobile interactive with ScanLink.</p>
	<p class="marketing-info-dtl">ScanLink enables you to quickly and easily generate intelligent QR codes and Data matrix codes that provide an instant connection between mobile consumers and your products and services.
In addition to intelligent code generation ScanLink also provides a user-friendly
platform create and launch your own branded mobile content.</p>
<p class="marketing-info">
	Start your own mobile interactive initiative today. <br>
	Your first code is FREE for a year
    <?php if(!$client_login){ ?>
    	<a href="<?php echo URL::base(); ?>register/index" class="regblue">REGISTER</a>
    <?php } ?>
</p>
</p>
	<div class="clearfix"></div>
	<div class="marketing-raw dashtop">
		<div class="marketing-dtl">
			<h2 class="mTop50">Powerful Analytics</h2>
			<p>Marketing initiatives become 100%<br />
			measurable as each time a code is<br />
			scanned the ScanLink analytics function<br />
			records the time, date, type of device,<br />
			browser and even the location.
			</p>
		</div>
		<img src="<?php echo URL::base() ?>images/marketing-img1.png" alt="" class="img1" />
	</div>
	
	<div class="marketing-raw">
		<img src="<?php echo URL::base() ?>images/marketing-img2.png" alt="" class="img2" />
		<div class="marketing-dtl">
			<h2 class="mTop100">Customised data collection</h2>
			<p>Instantly create and launch your own<br />customised pop up message to collect<br />names, mobile numbers and email addresses.</p>
		</div>
	</div>
	
	<div class="marketing-raw backnone">
		<div class="marketing-dtl">
			<h2 class="mTop180">Create, edit and launch instantly</h2>
			<p>User-friendly templates and edit tools enable you<br />
			to quickly and easily create your own branded<br />
			mobile optimised content including video’s, text,<br />
			pictures, documents, web links and more.
			</p>
		</div>
		<img src="<?php echo URL::base() ?>images/marketing-img3.png" alt="" class="img3" />
	</div>
</section>
<!-- MARKETING BLOCK -->
