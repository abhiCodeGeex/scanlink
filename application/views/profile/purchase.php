<p>
  <?php /* This is the second step Register page. You can select code  and calculate subscription from here */ ?>
</p>

<style>
.registerli{
	min-width:300px;
}
</style>
<section>
  <h2 class="page-title">Code Balance</h2>
  <div class="secondstep-title-purchase">
    <div class="SectionTitle-purchase-right"> <?php echo Form::input(
                                'exit', 
                                'EXIT', 
                                array(
                                        'id' => 'exit', 
                                        'class' => 'text-fi',
										'type'=>'button',
										'onClick'=>'window.location.href="'.URL::base().'dashboard/code"'
                                    )
                   ); ?> </div>
    <?php if (isset($sessionmsg) && $sessionmsg != '') { ?>
    <p><?php echo $sessionmsg; ?></p>
    <?php } ?>
    <div class="current-code-balance-text">
      <div class="current-code-balance-text-left">Your current code availability balance</div>
      <div class="current-code-balance-text-right"><?php echo $pending_codes ?> </div>
    </div>
  </div>
  <div class="clear"></div>
  <h3>Enter the amount of codes you would like to activate and click the calculate button to display the total order value</h3>
  <div class="clear"></div>
  
  
  
  <div class="SecondPurchaseBox">
    <div class="GreenTitlePurchaseCodes" id="purchase_code">Purchase Codes</div>
    <div class="BlackTitlePurchaseCodes" id="agency_reseller" >Agency/Reseller</div>
    <div class="clear"></div>
    
    
    <div class="content-register" id="purchase_code_content">
      <?php 
	   echo Form::open(
				URL::base() . 'profile/billing', array('id' => 'frmpurchase',
			'name' => 'frmpurchase',
			'method' => 'post',
			'enctype' => 'multipart/form-data',
				)
		) 
	  ?>
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
      <div class="clear"></div>
      <div class="secondstep-submit"> <?php echo Form::input(
                            'order-summary', 
                            'Purchase', 
                            array(
                                    'id' => 'order-summary', 
                                    'class' => 'text-fi',
                                    'type'=>'button',
                                )
                            ); ?> <?php echo Form::label(
                                'order-summary', 
                                isset($errors['order-summary']) ? $errors['order-summary'] : '', 
                                array(
                                        'class' => 'error', 
                                        'generated' => 'true'
                                    )
                            ); ?> 
     	</div>
                            
    	<?php echo Form::close() ?> 
    </div>
    
    <div class="content-register" id="agency_reseller_content">
      <?php 
	   echo Form::open(
				URL::base() . 'profile/billing', array('id' => 'frmpurchase',
			'name' => 'frmpurchase',
			'method' => 'post',
			'enctype' => 'multipart/form-data',
				)
		) 
	  ?>
      <div class="content-main-register">
      		<div class="content-label-register reseller">
              <label>Enter your reseller password:</label>
            </div>
      		<div class="content-textbox-register reseller">
            	<?php echo Form::input(
							  'reseller_code_client', 
							  isset($fields['reseller_code_client']) ? $fields['reseller_code_client'] : '', 
							  array(
									  'id' => 'reseller_code_client', 
									  'class' => 'text-fi',
								  )
							  ); ?> 
				<?php echo Form::label(
								'reseller_code_client', 
								isset($errors['resller_code_client']) ? $errors['reseller_code_client'] : '', 
								array(
										'class' => 'error', 
										'generated' => 'true'
									)
							); ?> 
            </div>
            <div class="clear"></div>
      		<div class="content-dispaly-amount-register">
          		<div class="code-amount"><sup>$</sup><span id="reseller_code_amount">0.00</span> <br /></div>
          		<div class="code-permonth">Per Code / Month <br /></div>
          		<div class="code-total">Total Annual subscription <b>$<span id="reseller_subscription_amount">0.00</span></b></div>
                <div class="code-total resellerMarginDiff">Reseller margin <b>$<span id="reseller_margin">0.00</span></b></div>
      		</div>
		   <div class="content-label-register">
          	<label>Enter a number of codes required:</label>
      	   </div>
      	   <div class="content-textbox-register"> 
			  <?php echo Form::input(
                              'reseller_code_qty', 
                              isset($fields['reseller_code_qty']) ? $fields['reseller_code_qty'] : '', 
                              array(
                                      'id' => 'reseller_code_qty', 
                                      'class' => 'text-fi',
                                      'maxlength'=>'4'
                                  )
                              ); ?> 
              <?php echo Form::label(
                              'reseller_code_qty', 
                              isset($errors['reseller_code_qty']) ? $errors['reseller_code_qty'] : '', 
                              array(
                                      'class' => 'error', 
                                      'generated' => 'true'
                                  )
                          ); ?> 
       		</div>
		    <div class="content-button-register"> 
				<?php echo Form::input(
                                'calculate', 
                                'Calculate', 
                                array(
                                        'id' => 'calculateRe', 
                                        'class' => 'text-fi',
                                        'type'=>'button',
                                    )
                                ); ?> 
                <?php echo Form::label(
                                'calculate', 
                                isset($errors['calculate']) ? $errors['calculate'] : '', 
                                array(
                                        'class' => 'error', 
                                        'generated' => 'true'
                                    )
                            ); ?> 
       		</div>
         
      </div>
      <div class="clear"></div>
      <div class="secondstep-submit"> 
	  	<?php echo Form::input(
					'order-summary', 
					'Purchase', 
					array(
							'id' => 'order-summaryRe', 
							'class' => 'text-fi',
							'type'=>'button',
						)
					); ?> 
		<?php echo Form::label(
					  'order-summary', 
					  isset($errors['order-summary']) ? $errors['order-summary'] : '', 
					  array(
							  'class' => 'error', 
							  'generated' => 'true'
						  )
				  ); ?> 
     	</div>
                            
    	<?php echo Form::close() ?> 
    </div>
    
    
  </div>
  

</section>



<script type="text/javascript">
	$(document).ready(function(){
		$('#purchase_code').click(function(){
			$(this).addClass('GreenTitlePurchaseCodes');
			$(this).removeClass('BlackTitlePurchaseCodes');
			$('#agency_reseller').addClass('BlackTitlePurchaseCodes');
			$('#agency_reseller').removeClass('GreenTitlePurchaseCodes');
			$('#agency_reseller_content').hide();
			$('#purchase_code_content').show();
		});
		$('#agency_reseller').click(function(){
			$(this).removeClass('BlackTitlePurchaseCodes');
			$(this).addClass('GreenTitlePurchaseCodes');
			$('#purchase_code').removeClass('GreenTitlePurchaseCodes');
			$('#purchase_code').addClass('BlackTitlePurchaseCodes');
			$('#purchase_code_content').hide();
			$('#agency_reseller_content').show();
		});
		
		
	});
</script>


<script type="text/javascript">


   $('#no_codes').bind('keypress',function(e){
	  	 return isNumberKey(e);
  });
  
  $('#reseller_code_qty').bind('keypress',function(e){
	  	 return isNumberKey(e);
  });
  
  $('#order-summaryRe').bind('click',function(e){
	  	 return checkResellervalidation(true);
  });
  
  $('#calculateRe').bind('click',function(e){
	  	 return checkResellervalidation(false);
  });
  
  $('#order-summary').bind('click',function(e){
	  	 return checkvalidation(true);
  });
  
  $('#calculate').bind('click',function(e){
	  	 return checkvalidation(false);
  });
  
  
  
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
      $('.loader-image').show();
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
          $('.loader-image').hide();
        }
        if(submitbuttonpress){
          window.location.href = "<?php echo URL::base(); ?>profile/billing/";
        }
      });
      request.fail(function(jqXHR, textStatus) {
        alert( "Request failed: " + textStatus );
      });
			
    }
  }
  
  function checkResellervalidation(submitbuttonpress){

	if($('#reseller_code_client').val() == ''){
      alert("Enter your reseller password.");
      $('#reseller_code_client').focus();
      return false;
    }else if($('#reseller_code_qty').val() == ''){
      alert("Enter a reseller of code.");
      $('#reseller_code_qty').focus();
      return false;
    }
    else if($('#reseller_code_qty').val() > 1000){
      alert("Enter a number of code less than 1000.");
      $('#reseller_code_qty').focus();
      return false; 
    }
    else{
      $('.loader-image').show();
      var request = $.ajax({
        url:'<?php echo URL::base(); ?>profile/getResellerdata/',
        type: "POST",
        dataType: "json",
        data:{
          no_codes 			: $('#reseller_code_qty').val(),
		  reseller_code     : $('#reseller_code_client').val(),
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
          $('#reseller_code_amount').html(data.amount);
          $('#reseller_subscription_amount').html(data.totalsubscrption);
		  $('#reseller_margin').html(data.reseller_margin);
          $('.loader-image').hide();
		  if(submitbuttonpress){
          	window.location.href = "<?php echo URL::base(); ?>profile/billing/";
          }
        }
        
      });
      request.fail(function(jqXHR, textStatus) {
        alert( "Request failed: " + textStatus );
      });
			
    }
    
  }
</script>