<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<!-- WORKPLACE BLOCK -->

<section class="pricing-block clearfix">
  <div class="scanlink-container">
	<h2 class="greenHead">Your first ScanLink code is FREE!</h2>
	<p>ScanLink provides scalable and affordable access to mobile QR code and content management services for any size business.<br />
		The ScanLink platform enables users to activate as few or as many mobile codes as required at anytime.<br />
		Pricing for each code activation is calculated on a 12 month subscription basis which can be renewed indefinitely.<br />
		Use the code calculator below to calculate for your code requirements. Remember your first ScanLink code is FREE and you can add new codes to your account at anytime.
	</p>
	
	<div class="content-register coRound">
      <div class="content-main-register">
        <div class="content-dispaly-amount-register">
          <div class="code-amount"><sup>$</sup><span id="code_amount">0.00</span> <br />
          </div>
          <div class="code-permonth">Per Code / Month <br />
          </div>
          <div class="code-total">Total Annual subscription <b>$<span id="subscription_amount">0.00</span></b></div>
        </div>
        <div class="content-label-register">
          <label>Enter a number of codes required:</label>
        </div>
        <div class="content-textbox-register"> <?php echo Form::input(
                                'no_codes', 
                                isset($fields['no_codes']) ? $fields['no_codes'] : '', 
                                array(
                                        'id' => 'no_codes', 
                                        'class' => 'text-fi',
                                        'onkeypress'=>'javascript:return isNumberKey(event);',
                                        'maxlength'=>'4'
                                    )
                                ); ?> <?php echo Form::label(
                                    'no_codes', 
                                    isset($errors['no_codes']) ? $errors['no_codes'] : '', 
                                    array(
                                            'class' => 'error', 
                                            'generated' => 'true'
                                        )
                                ); ?> </div>
        <div class="content-button-register"> <?php echo Form::input(
                                    'calculate', 
                                    'Calculate', 
                                    array(
                                            'id' => 'calculate', 
                                            'class' => 'text-fi',
                                            'type'=>'button',
                                            'onclick'=>'javascript:return checkvalidation(false);'
                                        )
                                    ); ?> <?php echo Form::label(
                                        'calculate', 
                                        isset($errors['calculate']) ? $errors['calculate'] : '', 
                                        array(
                                                'class' => 'error', 
                                                'generated' => 'true'
                                            )
                                    ); ?> </div>
      </div>
    </div>
	
	<p class="marketing-info text-20 margin-B40">
    	Ready to get started? Register your ScanLink account in 3 easy steps 
		<?php if(!$client_login){ ?> 
        	<a href="<?php echo URL::base(); ?>register/index" class="regblue bGreen">REGISTER</a>
		<?php } ?>
    </p>

	<h2 class="greenHead">Workplace industrial labels and tags from just $3</h2>
	<p class="margin-B40">We provide high quality industrial labels and tag solutions to help deploy your mobile initiative in the workplace.<br/> Available to order in any quantity directly from your ScanLink account. We also provide customised labels and tags to suit individual requirements.
	</p>
	
	<h2 class="greenHead">Mobile video production</h2>
	<p>Our experienced creative and production team offers a wide-range of affordable mobile optimised video and animation production services. We can provide a range of production techniques and solutions to accommodate your budget.<br/> Contact us to discuss your requirements.</p>
	<p><a href="<?php echo URL::base(); ?>welcome/contact/" class="regblue bGreen fright"><strong>Enquire</strong></a></p>
	
	</div>
</section>
<!-- /WORKPLACE BLOCK -->
<script type="text/javascript">
function isNumberKey(evt)
  {
    var charCode = (evt.which) ? evt.which : event.keyCode
    if (charCode > 31 && (charCode < 48 || charCode > 57))
      return false;

    return true;
  }
function checkvalidation(submitbuttonpress){
    if($('#no_codes').val() == ''){
      alert("Enter a number of code required.");
      $('#no_codes').focus();
      return false;
    }else if($('#no_codes').length > 4){
      alert("Enter a number of code less than 4 character.");
      $('#no_codes').focus();
      return false;
    }else if($('#no_codes').val() > 1000){
      alert("Enter a number of code less than 1000.");
      $('#no_codes').focus();
      return false; 
    }else{
      var request = $.ajax({
        url:'<?php echo URL::base(); ?>register/getData/',
        type: "POST",
        dataType: "json",
        data:{
          no_codes 			: $('#no_codes').val(),
          submit_button_press 	: submitbuttonpress
        }
		
      });
      request.done(function(data) {
			 
        var error = data.errors.length;
        if(error > 0){
          for(var i=0;i<data.errors.length;i++){
            alert(data.errors[i]);
          }
        }else{
          $('#code_amount').html(data.amount);
          $('#subscription_amount').html(data.totalsubscrption);
        }
        if(submitbuttonpress){
          window.location.href = "<?php echo URL::base(); ?>register/thirdstep/";
        }
      });
      request.fail(function(jqXHR, textStatus) {
        alert( "Request failed: " + textStatus );
      });
			
    }
  }
</script>

