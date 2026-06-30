<div class="content-register" id="agency_reseller_content">
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
            <div class="code-total">Reseller Total Annual subscription <b>$<span id="subscription_amount">0.00</span></b></div>
          </div>
          <div class="content-label-register reseller">
            <label>Enter a reseller code:</label>
          </div>
          <div class="content-textbox-register reseller"> 
              <?php echo Form::input(
                                      'resller_code', 
                                      isset($fields['resller_code']) ? $fields['resller_code'] : '', 
                                      array(
                                              'id' => 'resller_code', 
                                              'class' => 'text-fi',
                                          )
                                      ); ?> 
              <?php echo Form::label(
                                          'resller_code', 
                                          isset($errors['resller_code']) ? $errors['resller_code'] : '', 
                                          array(
                                                  'class' => 'error', 
                                                  'generated' => 'true'
                                              )
                                      ); ?> 
          </div>
          <div class="resller_code"> </div>
        </div>
        <div class="clear"></div>
        <div class="secondstep-submit"> 
          <?php echo Form::input(
                      'order-summary', 
                      'Purchase', 
                      array(
                              'id' => 'order-summary', 
                              'class' => 'text-fi',
                              'type'=>'button',
                              'onclick'=>'javascript:return checkvalidation(true);'
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
