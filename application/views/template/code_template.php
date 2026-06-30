<div class="content-register" id="purchase_code_content">
      <?php 
	   echo Form::open(
				URL::base() . 'profile/billing', array('id' => 'frmpurchase',
			'name' => 'frmpurchase',
			'method' => 'post',
			'enctype' => 'multipart/form-data',
			'onsubmit' => 'javascript:return checkvalidation(false);'
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
      <div class="clear"></div>
      <div class="secondstep-submit"> <?php echo Form::input(
                            'order-summary', 
                            'Purchase', 
                            array(
                                    'id' => 'order-summary', 
                                    'class' => 'text-fi',
                                    'type'=>'button',
                                    'onclick'=>'javascript:return checkvalidation(true);'
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