<p><?php /* This is the third step Register page. Display order summary */ ?></p>
<?php
echo Form::open(
        URL::base() . 'dashboard/ordersummary', array('id' => 'frmeditprofile',
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
      <li class="NoBorder">
        <div class="subscription-ordersummary">$<?php echo number_format($quantity_small * $small_price, 2); ?> AUD </div>
        <div>
          QTY <?php echo $quantity_small; ?> ScanLink Label/s 50 x 40 mm
          <?php /*45 X 60 mm label <?php echo $quantity_small; ?> for each $<?php echo $small_price; ?>  */ ?>
        </div>
      </li>
      <li class="NoBorder">
        <div class="subscription-ordersummary">$<?php echo number_format($quantity_big * $big_price, 2); ?> AUD </div>
        <div>
          QTY <?php echo $quantity_big; ?> ScanLink Label/s 100 x 75 mm
          <?php /*90 X 120 mm label <?php echo $quantity_big; ?> for each $<?php echo $big_price; ?> */ ?>
        </div>
      </li>
      <li>
        <div class="subscription-ordersummary">$<?php echo number_format($postage, 2); ?> AUD</div>
        <div><b>Postage</b></div>
      </li>
      <li>
        <div class="total-subscription-ordersummary">$<?php echo number_format((($quantity_small * $small_price) + ($quantity_big * $big_price)) + $postage, 2); ?> (includes GST)</div>
        <div><b>Total</b></div>
      </li>
      <li class="Title">Secure Payment Option</li>
      <li>
        <div class="RadioInvoice">
          <div class="AgreeRadio"> <?php
echo Form::input(
        'paymentoption', '', array(
    'id' => 'paymentoption',
    'class' => 'text-fi',
    'type' => 'radio',
    'checked' => 'checked'
        )
);
?> <?php
            echo Form::label(
                    'paymentoption', isset($errors['paymentoption']) ? $errors['paymentoption'] : '', array(
                'class' => 'error',
                'generated' => 'true'
                    )
            );
?> </div>
          <div class="AgreeRadioText"> By Invoice <br/>
            ScanLink will mail an invoice to you. Terms are 14 days. </div>
          <div class="clear"></div>
        </div>
        <div class="AgreeWithCondition"><?php
            echo Form::input(
                    'termscondition', '', array(
                'id' => 'termscondition',
                'class' => 'text-fi',
                'type' => 'checkbox',)
            );
?> <?php
          echo Form::label(
                  'termscondition', isset($errors['termscondition']) ? $errors['termscondition'] : '', array(
              'class' => 'error',
              'generated' => 'true'
                  )
          );
?> I agree with terms & condition.
          <br>
          <br>
          <?php
          echo Form::input(
                  'proceed', 'proceed', array(
              'id' => 'proceed',
              'class' => 'text-fi',
              'type' => 'submit',
              'onclick' => 'javascript:return checkvalidation(true);'
                  )
          );
          ?> <?php
          echo Form::label(
                  'proceed', isset($errors['proceed']) ? $errors['proceed'] : '', array(
              'class' => 'error',
              'generated' => 'true'
                  )
          );
          ?> 
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