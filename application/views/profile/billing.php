<p><?php /* This is the edit profile page. You can edit user profile detail from here */ ?></p>
<?php 
	echo Form::open(
              URL::base() . 'profile/ordersummary', array('id' => 'frmeditprofile',
          'name' => 'frmeditprofile',
          'method' => 'post',
          'enctype' => 'multipart/form-data',
          'onsubmit' => 'javascript:return editprofilevalidation();'
              )
      )
?>
<style>
.registerli{
	min-width:450px;
}
</style>

<section id="content-ordersummary"><!-- ADD FORM content -->
	<section class="add-form-right-register"><!-- ADD FORM RIGHT START -->
    	<div class="SectionTitleBox-orderSummary">
        	<div class="SectionTitleRegister">&nbsp;</div>
            <?php if ($sessionmsg != '') { ?>
              <p><?php echo $sessionmsg; ?></p>
            <?php } ?>
            <ul class="form-view clearfix">
              <li class="registerli">
                <label>Last name:</label>
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
                <label>Billing address:</label>
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
                <label>Town:</label>
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
                <label>Postal code:</label>
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
              
          </ul>
        </div>
    </section><!-- ADD FORM RIGHT END -->
    <section class="add-form-left-register"><!-- ADD FORM LEFT START -->
    	<div class="SectionTitleBox-orderSummary">
        	<div class="SectionTitleRegister">Billing Details</div>
            <ul class="form-view clearfix">
              <li class="registerli">
                <label>First name:</label>
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
                <label>Company name:</label>
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
                <label>Email(this will also be your username):</label>
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
                <label>Telephone number:</label>
                <?php echo Form::input(
                            'phone', 
                            isset($fields['phone']) && $fields['phone'] > 0 ? $fields['phone'] : '', 
                            array('id' => 'phone', 'class' => 'text-fi registertextwidth','onkeypress'=>"return isNumberKey(event)",'tabindex'=>7)
                        ); ?> 
                <?php echo Form::label(
                            'phone', 
                            isset($errors['phone']) && $errors['phone'] > 0  ? $errors['phone'] : '', 
                            array('class' => 'error', 'generated' => 'true')
                        ); ?>
              </li>
              <li class="registerli">
              	<div class="purchase-div-submit">
                <input name="save" id="save" type="submit" class="save" value="NEXT">
                </div>
              </li>
          </ul>
        </div>
    </section><!-- ADD FORM LEFT END -->
</section>
<?php echo Form::close() ?>


<script type="application/javascript">

  
function editprofilevalidation(){
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
		alert("Enter a billing address.");
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
		alert("Enter a postal_code.");
		document.getElementById('postal_code').focus();
		return false;
	}
	
}
  
  
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
		alert("Enter a billing address.");
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