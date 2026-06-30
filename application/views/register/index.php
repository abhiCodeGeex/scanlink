<p><?php /* This is the Register page. You can register from here */ ?></p>
<?php 
	echo Form::open(
		URL::base() . 'register/index', 
		array('id' => 'frmregister', 
			'name' => 'frmregister', 
			'method' => 'post', 
			'enctype' => 'multipart/form-data',
			'onsubmit'=>'javascript:return registervalidation();'
			)
		) 
?>
<style>
.registerli{
	min-width:450px;
}
</style>

<div class="scanlink-container">
<section id="content-ordersummary">
	
	<ul class="breadcrumbsMain">
		<li class="current">
			<span>1</span>
			<h4>Enter</h4>
			<p>your registration<br />details</p>
			<label>&nbsp;</label>
		</li>
		<li>
			<span class="txtI-10">2</span>
			<h4>Select</h4>
			<p>the quantity of codes<br />your require</p>
			<label>&nbsp;</label>
		</li>
		<li>
			<span class="txtI-10">3</span>
			<h4>Upload</h4>
			<p>web links, videos,<br />pictures, documents</p>
			<label>&nbsp;</label>
		</li>
	</ul>
	
	<?php /*
	<!-- ADD FORM content -->
	<section class="center-step-display"><!-- step display -->
           <div class="stepdisplay ">
        	<div class="labelstep">
            	Step 1 of 3
            </div>
            <div class="stepdisplay-step">
            	<div class="step-radio">
				 <?php echo Form::radio(
                                'step_register', 
                                '0', 
                                true,
                                array(
                                    'class' => 'radi enable_data_collection',
                                    'disabled' => 'disabled'
                                )
                        ); 
                ?>
                </div>
                <div class="step-radio">
                <?php echo Form::radio(
                                'step_register', 
                                '0', 
                                false,
                                array(
                                    'class' => 'radi enable_data_collection',
                                    'disabled' => 'disabled'
                                )
                        ); 
                ?>
                </div>
                <div class="step-radio">
                <?php echo Form::radio(
                                'step_register', 
                                '0', 
                                false,
                                array(
                                    'class' => 'radi enable_data_collection',
                                    'disabled' => 'disabled'
                                )
                        ); 
                ?>
                </div>
            </div>
        </div>
    </section>
    */
    ?>
	
	<div class="clear"></div>
	<div>
		<div class="SectionTitleRegister" style="float:left;">Account registration includes your first Scanlink code is free for a year </div>
		<div style="float:right;margin-right:66px;">
				<div style="float:left;">			
					Reseller Code<br><small>(For Resellers Only)</small>&nbsp;&nbsp;
				</div>
				<div style="float:left;">
					<input type="text" name="client_reseller_code" id="client_reseller_code" class="reg-reseller-code" value="<?php echo isset($fields['client_reseller_code']) ? $fields['client_reseller_code'] : '';?>" maxlength="255"	/>
				</div>
				<div class="clear"></div>
				<?php echo Form::label(
                            'client_reseller_code', 
                            isset($errors['client_reseller_code']) ? $errors['client_reseller_code'] : '', 
                            array('class' => 'error', 'generated' => 'true')
                        ); ?>  
		</div>
	</div>
	<div class="clear"></div>
	
	<section class="add-form-right-register"><!-- ADD FORM RIGHT START -->
    	<div class="SectionTitleBox-orderSummary">
        	<!--<div class="SectionTitleRegister">&nbsp;</div>-->
            <ul class="form-view clearfix">
              <li class="registerli">
                <label for="last_name">Last name:</label>
                <?php echo Form::input(
                            'last_name', 
                            isset($fields['last_name']) ? $fields['last_name'] : '', 
                            array('id' => 'last_name', 'class' => 'text-fi registertextwidth','tabindex'=>2)
                        ); ?> 
                <?php echo Form::label(
                            'last_name', 
                            isset($errors['last_name']) ? $errors['last_name'] : '', 
                            array('class' => 'error', 'generated' => 'true')
                        ) ?>
              </li>
              <li class="registerli">
                <label for="billing_address">Address: </label>
                <?php echo Form::input(
                            'billing_address', 
                            isset($fields['billing_address']) ? $fields['billing_address'] : '', 
                            array('id' => 'billing_address', 'class' => 'text-fi registertextwidth','tabindex'=>4)
                        ); ?> 
                <?php echo Form::label(
                            'billing_address', 
                            isset($errors['billing_address']) ? $errors['billing_address'] : '', 
                            array('class' => 'error', 'generated' => 'true')
                        ); ?>
              </li>
              <li class="registerli">
                <label for="town">Town:</label>
                <?php echo Form::input(
                            'town', 
                            isset($fields['town']) ? $fields['town'] : '', 
                            array('id' => 'town', 'class' => 'text-fi registertextwidth','tabindex'=>6)
                        ); ?> 
                <?php echo Form::label(
                            'town', 
                            isset($errors['town']) ? $errors['town'] : '', 
                            array('class' => 'error', 'generated' => 'true')
                        ); ?>
              </li>
              <li class="registerli">
                <label for="postal_code">Postal code:</label>
                <?php echo Form::input(
                            'postal_code',
                            isset($fields['postal_code']) ? $fields['postal_code'] : '', 
                            array('id' => 'postal_code', 'class' => 'text-fi registertextwidth','onkeypress'=>"return isNumberKey(event)",'tabindex'=>8)
                        ); ?> 
                <?php echo Form::label(
                            'postal_code', 
                            isset($errors['postal_code']) ? $errors['postal_code'] : '', 
                            array('class' => 'error', 'generated' => 'true')
                        ); ?>
              </li>
              <li class="registerli">
                <label for="cpassword">Confirm Password:</label>
                <?php echo Form::input(
                            'cpassword',
                            '', 
                            array('id' => 'cpassword', 'class' => 'text-fi registertextwidth','type'=>'password','tabindex'=>10)
                        ); ?> 
                <?php echo Form::label(
                            '',
                            isset($errors['cpassword']) ? $errors['cpassword'] : '', 
                            array('class' => 'error', 'generated' => 'true')
                        ); ?>
              </li>
              <li class="registerli">
              	<div class="rgister-div-submit">
                <input name="save" id="save" type="submit" class="save" value="NEXT">
                </div>
              </li>
          </ul>
        </div>
    </section><!-- ADD FORM RIGHT END -->
    <section class="add-form-left-register"><!-- ADD FORM LEFT START -->
		
    	<div class="SectionTitleBox-orderSummary">
        	
            <ul class="form-view clearfix">
              <li class="registerli">
                <label for="first_name">First name:</label>
                <?php echo Form::input(
                        'first_name', 
                        isset($fields['first_name']) ? $fields['first_name'] : '', 
                        array('id' => 'first_name', 'class' => 'text-fi registertextwidth','tabindex'=>1)
                        ); ?> 
                <?php echo Form::label(
                            'first_name', 
                            isset($errors['first_name']) ? $errors['first_name'] : '', 
                            array('class' => 'error', 'generated' => 'true')
                        ); ?>
              </li>
              <li class="registerli">
                <label for="company_name">Company name/Business name:</label>
                <?php echo Form::input(
                            'company_name', 
                            isset($fields['company_name']) ? $fields['company_name'] : '', 
                            array('id' => 'company_name', 'class' => 'text-fi registertextwidth','tabindex'=>3)
                    ); ?> 
                <?php echo Form::label(
                            'company_name', 
                            isset($errors['company_name']) ? $errors['company_name'] : '', 
                            array('class' => 'error', 'generated' => 'true')
                        ) ?>
              </li>
              <li class="registerli">
                <label for="email">Email: <font size="-2">(This will also be your username)</font></label>
                <?php echo Form::input(
                            'email', 
                            isset($fields['email']) ? $fields['email'] : '', 
                            array('id' => 'email', 'class' => 'text-fi registertextwidth','tabindex'=>5)
                        ); ?> 
                <?php echo Form::label(
                            'email', 
                            isset($errors['email']) ? $errors['email'] : '', 
                            array('class' => 'error', 'generated' => 'true')
                        ); ?>
              </li>
              <li class="registerli">
                <label for="phone">Telephone number:</label>
                <?php echo Form::input(
                            'phone', 
                            isset($fields['phone']) ? $fields['phone'] : '', 
                            array('id' => 'phone', 'class' => 'text-fi registertextwidth','onkeypress'=>"return isNumberKey(event)",'tabindex'=>7)
                        ); ?> 
                <?php echo Form::label(
                            'phone', 
                            isset($errors['phone']) ? $errors['phone'] : '', 
                            array('class' => 'error', 'generated' => 'true')
                        ); ?>
              </li>
              <li class="registerli">
                <label for="password		">Password:</label>
                <?php echo Form::input(
                            'password',
                            '', 
                            array('id' => 'password', 'class' => 'text-fi registertextwidth','type'=>'password','tabindex'=>9)
                            ); ?> 
                <?php echo Form::label(
                            'password', 
                            isset($errors['password']) ? $errors['password'] : '', 
                            array('class' => 'error', 'generated' => 'true')
                        ); ?>
              </li>
              <li class="registerli">
                <label for="captcha">Verification code:</label>
                <?=$captchacode?><br />
                <?php echo Form::input(
                            'captcha',
                            '', 
                            array('id' => 'captcha', 'class' => 'text-fi registertextwidth','tabindex'=>11)
                        ); ?> 
                <?php echo Form::label(
                            '', 
                            isset($errors['captcha']) ? $errors['captcha'] : '', 
                            array('class' => 'error', 'generated' => 'true')
                        ); ?>
                <span class="help-inline"></span>
              </li>
          </ul>
        </div>
    </section><!-- ADD FORM LEFT END -->
</section>
</div>
<?php echo Form::close() ?>


<script type="application/javascript">

function registervalidation(){
	if(document.getElementById('first_name').value == ''){
		alert("Enter a first name.");
		document.getElementById('first_name').focus();
		return false;
	}
	if(document.getElementById('last_name').value == ''){
		alert("Enter a last name.");
		document.getElementById('last_name').focus();
		return false;
	}
	if(document.getElementById('company_name').value == ''){
		alert("Enter a company name.");
		document.getElementById('company_name').focus();
		return false;
	}
	if(document.getElementById('billing_address').value == ''){
		alert("Enter a address.");
		document.getElementById('billing_address').focus();
		return false;
	}
	if(document.getElementById('email').value == ''){
		alert("Enter a email.");
		document.getElementById('email').focus();
		return false;
	}else{
		 var emailRegexStr = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
		 if (!emailRegexStr.test(document.getElementById('email').value)) {
 			alert('Enter a valid email.');
			document.getElementById('email').focus();
			return false;
 		}
	}
	if(document.getElementById('town').value == ''){
		alert("Enter a town.");
		document.getElementById('town').focus();
		return false;
	}
	if(document.getElementById('phone').value == ''){
		alert("Enter a phone.");
		document.getElementById('phone').focus();
		return false;
	}
	if(document.getElementById('postal_code').value == ''){
		alert("Enter a postal code.");
		document.getElementById('postal_code').focus();
		return false;
	}
	if(document.getElementById('password').value == ''){
		alert("Enter a password.");
		document.getElementById('password').focus();
		return false;
	}else{
		if(document.getElementById('password').value.length > 12 || document.getElementById('password').value.length < 5){
			alert("Password must between 5-12 character.");
			document.getElementById('password').focus();
			return false;
		}
	}
	if(document.getElementById('cpassword').value == ''){
		alert("Enter a confirm password.");
		document.getElementById('cpassword').focus();
		return false;
	}
	if(document.getElementById('password').value != document.getElementById('cpassword').value){
		alert("Password and confirm password does not match.");
		document.getElementById('password').focus();
		return false;
	}
	if(document.getElementById('captcha').value == ''){
		alert("Enter a verification code.");
		document.getElementById('captcha').focus();
		return false;
	}
}

 function isNumberKey(evt)
  {
	 var charCode = (evt.which) ? evt.which : event.keyCode
	 if (charCode > 31 && (charCode < 48 || charCode > 57))
		return false;

	 return true;
  }
</script>