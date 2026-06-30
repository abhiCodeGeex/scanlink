<p><?php /* This is the second step Register page. You can select code  and calculate subscription from here */ ?></p>
<?php
echo Form::open(
        URL::base() . 'register/secondstep', array('id' => 'frmregister',
    'name' => 'frmregister',
    'method' => 'post',
    'enctype' => 'multipart/form-data',
    'onsubmit' => 'javascript:return checkvalidation(false);'
        )
)
?>
<style>
  .registerli{
    min-width:300px;
  }
</style>
<section>

 <ul class="breadcrumbsMain">
		<li >
			<span>1</span>
			<h4>Enter</h4>
			<p>your registration<br />details</p>
			<label>&nbsp;</label>
		</li>
		<li class="current">
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
  <section class="center-step-display"><!-- step display -->
    <div class="stepdisplay ">
      <div class="labelstep">
        Step 2 of 3
      </div>
      <div class="stepdisplay-step">
        <div class="step-radio">
          <?php
          echo Form::radio(
                  'step_register', '0', false, array(
              'class' => 'radi enable_data_collection',
              'disabled' => 'disabled'
                  )
          );
          ?>
        </div>
        <div class="step-radio">
          <?php
          echo Form::radio(
                  'step_register', '0', true, array(
              'class' => 'radi enable_data_collection',
              'disabled' => 'disabled'
                  )
          );
          ?>
        </div>
        <div class="step-radio">
          <?php
          echo Form::radio(
                  'step_register', '0', false, array(
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
  <div class="clear"></div>
  <h3 class="page-title">Enter the amount of codes you would like to activate and click the calculate button to display the total order value</h3>
  <div class="secondstep-title-purchase">
<?php if (isset($sessionmsg) && $sessionmsg != '') { ?>
      <p><?php echo $sessionmsg; ?></p>
<?php } ?>
  </div>
  <div class="SecondPurchaseBox">
    <div class="GreenTitlePurchaseCodes">Purchase Codes</div>
    <div class="content-register">
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
        <div class="content-textbox-register"> <?php
echo Form::input(
        'no_codes', isset($fields['no_codes']) ? $fields['no_codes'] : '', array(
    'id' => 'no_codes',
    'class' => 'text-fi',
    'onkeypress' => 'javascript:return isNumberKey(event);',
    'maxlength' => '4'
        )
);
?> <?php
          echo Form::label(
                  'no_codes', isset($errors['no_codes']) ? $errors['no_codes'] : '', array(
              'class' => 'error',
              'generated' => 'true'
                  )
          );
?> </div>
        <div class="content-button-register"> <?php
          echo Form::input(
                  'calculate', 'Calculate', array(
              'id' => 'calculate',
              'class' => 'text-fi',
              'type' => 'button',
              'onclick' => 'javascript:return checkvalidation(false);'
                  )
          );
?> <?php
          echo Form::label(
                  'calculate', isset($errors['calculate']) ? $errors['calculate'] : '', array(
              'class' => 'error',
              'generated' => 'true'
                  )
          );
?> </div>
      </div>
      <div class="clear"></div>
      <div class="secondstep-submit"> <?php
          echo Form::input(
                  'order-summary', 'Purchase', array(
              'id' => 'order-summary',
              'class' => 'text-fi',
              'type' => 'button',
              'onclick' => 'javascript:return checkvalidation(true);'
                  )
          );
?> <?php
        echo Form::label(
                'order-summary', isset($errors['order-summary']) ? $errors['order-summary'] : '', array(
            'class' => 'error',
            'generated' => 'true'
                )
        );
?> </div>
    </div>
  </div>
</section>
        <?php echo Form::close() ?>
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