<p>
  <?php /* This is the third step Register page. Display order summary */ ?>
</p>
<?php 
	echo Form::open(
		URL::base() . 'code/renewableCode', 
		array('id' => 'frmregister', 
			'name' => 'frmregister', 
			'method' => 'post', 
			'enctype' => 'multipart/form-data',
			'onsubmit'=>'javascript:return checkvalidation();'
			)
		) 
?>
<style>
.registerli{
	min-width:450px;
	width:100%;
}
.registertitle{
	 background: none repeat scroll 0 0 #B1F6F9;
    clear: both;
    display: inline-block;
    margin-top: 3px;
    min-height: 20px;
    min-width: 900px;
}

</style> 
<section id="content-ordersummary"><!-- ADD FORM content -->
  <h2 class="page-title">Order Summary</h2>
  <div class="SectionTitleBox-orderSummary">
    <ul class="OrderSummaryListing">
      <li class="Title">Item</li>
      <li>
        <div class="subscription-ordersummary">$ <?php echo number_format($totalsubscription,2); ?> AUD </div>
        <div style="width:70%;">12 months code subscription<br>  <?php 
		
		$amount_arr = array();
		
		foreach($codeamount as $amount)
		{
			if(!in_array($amount, $amount_arr))
			{
				array_push($amount_arr,$amount);
				$cnt = count(array_keys($codeamount, $amount));
				$total_amt =number_format(12*$amount,2);
				
				echo "$cnt code/s @ $$total_amt<br>";
			} 		
		}
		
		
		/*if(count($codeamount)>1)
		{
			echo "(";
			for($j=0;$j<count($codeamount);$j++)
			{
				if($j!=0)
					echo " and ";
					
				echo "one code with $".$codeamount[$j];
				
			}
			
			echo ")";
		}
		else
		{
			echo "for each $".$codeamount;
		}*/
		?> </div>
      </li>
      <li>
        <div class="total-subscription-ordersummary">$ <?php echo number_format($totalsubscription,2); ?> (includes GST)</div>
        <div><b>Total</b></div>
      </li>
      <li class="Title">Secure Payment Option</li>
      <li>
      <div class="RadioInvoice">
        <div class="AgreeRadio"> <?php echo Form::input(
                                    'paymentoption', 
                                    '', 
                                    array(
                                            'id' => 'paymentoption', 
                                            'class' => 'text-fi',
                                            'type'=>'radio',
                                            'checked'=>'checked'
                                        )
                                    ); ?> <?php echo Form::label(
										'paymentoption', 
										isset($errors['paymentoption']) ? $errors['paymentoption'] : '', 
										array(
												'class' => 'error', 
												'generated' => 'true'
											)
									); ?> </div>
        <div class="AgreeRadioText"> By Invoice <br/>
          ScanLink will mail an invoice to you. Terms are 14 days. </div>
          <div class="clear"></div>
          </div>
          <div class="AgreeWithCondition"><?php echo Form::input(
                                'termscondition', 
                                '', 
                                array(
                                        'id' => 'termscondition', 
                                        'class' => 'text-fi',
                                        'type'=>'checkbox',						)
                                ); ?> <?php echo Form::label(
                                    'termscondition', 
                                    isset($errors['termscondition']) ? $errors['termscondition'] : '', 
                                    array(
                                            'class' => 'error', 
                                            'generated' => 'true'
                                        )
                                ); ?> I agree with <a href="<?php echo URL::base(); ?>termsnndcondition/index/" target="_blank">terms & condition.</a>
                               <br>
<br>
                                <?php echo Form::input(
                            'proceed', 
                            'proceed', 
                            array(
                                    'id' => 'proceed', 
                                    'class' => 'text-fi',
                                    'type'=>'submit',
                                    'onclick'=>'javascript:return checkvalidation(true);'
                                )
                            ); ?> <?php echo Form::label(
                                'proceed', 
                                isset($errors['proceed']) ? $errors['proceed'] : '', 
                                array(
                                        'class' => 'error', 
                                        'generated' => 'true'
                                    )
                            ); ?> 
                                </div>
      </li>
      
    </ul>
  </div>
</section>
<div id="progressbar_renew">
  <img src="<?php echo url::base(); ?>images/spinner_gray.gif" align="absmiddle" /> Processing order...
</div>
<?php echo Form::close() ?> 
<script type="application/javascript">
function checkvalidation(){
	if(!$('#termscondition').is(':checked')){
		alert("Please check the terms and condtion.");
		$('#termscondition').focus();
		return false;
	}else{
		document.getElementById("progressbar_renew").style.display = "block";
		window.onbeforeunload = null;
		return true;
	}
}
</script>