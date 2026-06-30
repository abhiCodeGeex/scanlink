<div class="scanlink-container">

<h2 class="page-title">Contact us</h2>

<?php if(isset($errors)){ ?>
<div>
    <label class="error"><?php echo $errors; ?></label>
		<br/><br/>
</div>
<?php } ?>

<?php echo Form::open(URL::base().'welcome/contact', array('class'=>'contact-form', 'id'=>'frmcontact','name'=>'frmcontact','method'=>'post','enctype' => 'multipart/form-data')) ?>
	<ul class="form-view clearfix">
		<li>
      <label for="yourName">Name:</label>
      <input type="text" name="name" id="yourName" value="" class="text-fi" />
		</li>
		<li>
      <label for="email">Email:</label>
      <input type="text" name="email" id="email" value="" class="text-fi" />
		</li>
		<li>
      <label for="commentsText">Message:</label>
      <textarea name="comments" id="commentsText"></textarea>
		</li>
    <li>
    	<label>Verification Code:</label>
      <br/>
			<?php echo $captcha->render(); ?>
      <img src="images/capcha-img.png" alt="" />
      <input type="text" class="text-fi" id="captcha" name="captcha" />
    </li>
		<li>
			<input id="save" type="submit" name="submit" value="Send">
			<input type="hidden" name="submitted" id="submitted" value="true"/>
		</li>
	</ul>
<?php echo Form::close() ?>

<section class="contact-box">
	<h2 class="title-box"><img src="<?php echo URL::base();?>images/img2.png" alt="" />&nbsp;ScanLink</h2>
	<section class="brd-btm">Submit your enquiry with the 'contact us' from or 
		alternatively call us during business hours from Monday to Friday on...</section>
	<section class="brd-btm">
		<span class="tel-icon">+61 0364314025</span>
	</section>
	<section class="last">
		<span class="email-icon">
			<h3>ScanLink</h3>
			5 Wattle Ave,<br/>
			Emu Heights, Tasmania, 7320
                        <!--<a href="maito:admin@scanlink.net.au">admin@scanlink.net.au</a>
		</span>
	</section>-->
					
</section>
</div>