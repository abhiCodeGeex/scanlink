<p><?php /* This is the third step Register page. Display order summary */ ?></p>
<?php 
	echo Form::open(
		URL::base() . 'register/thirdstep', 
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

  <ul class="breadcrumbsMain">
		<li >
			<span>1</span>
			<h4>Enter</h4>
			<p>your registration<br />details</p>
			<label>&nbsp;</label>
		</li>
		<li >
			<span class="txtI-10">2</span>
			<h4>Select</h4>
			<p>the quantity of codes<br />your require</p>
			<label>&nbsp;</label>
		</li>
		<li class="current">
			<span class="txtI-10">3</span>
			<h4>Upload</h4>
			<p>web links, videos,<br />pictures, documents</p>
			<label>&nbsp;</label>
		</li>
	</ul>
    
    <?php /*
  <section class="center-step-display"><!-- step display -->
           <div class="stepdisplay ">
        	<div class="labelstep">
            	Step 3 of 3
            </div>
            <div class="stepdisplay-step">
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
            </div>
        </div>
    </section>
	*/ ?>
  <h2 class="page-title">Order Summary</h2>
  <div class="SectionTitleBox-orderSummary">
    <ul class="OrderSummaryListing">
      <li class="Title">Item</li>
      <li>
        <div class="subscription-ordersummary">$ <?php echo $totalsubscription; ?> AUD </div>
        <div >12 months subscription <?php echo $nocode; ?> ScanLink codes </div>
      </li>
      <li>
        <div class="total-subscription-ordersummary">$ <?php echo $totalsubscription; ?> (includes GST)</div>
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
                                ); ?> I agree with <a href="<?php echo URL::base(); ?>termsnndcondition/index/" target="_blank">terms & condition</a>.
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
<?php echo Form::close() ?>
<script type="application/javascript">
function checkvalidation(){
	if(!$('#termscondition').is(':checked')){
		alert("Please check the terms and condtion.");
		$('#termscondition').focus();
		return false;
	}else{
		return true;
	}
}
</script>