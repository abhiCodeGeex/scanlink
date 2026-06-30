<p><?php /* This is the Dashboard Manager Area. You can manage your profile(s) here. */ ?></p>

<?php echo $innermenu; ?>
<?php
if (!$expried_or_not) {
?>
<?php if (isset($successmsg) && $successmsg): ?>
  <div class="smsg"><?php echo $successmsg; ?></div>
<?php endif; ?>

<?php if (isset($errormsg) && $errormsg): ?>
  <div><label class="error"><?php echo $errormsg; ?></label><br/><br/></div>
<?php endif; ?>

<section class="qrcode-lable-order-left"><!-- QRCODE LABEL ORDER LEFT -->
  <h1 class="order-label">Order Label >> <?php echo ucfirst($eq_type[0]['name']); ?> >> <?php echo ucwords($profile['code_profile_name']); ?></h1>
  <h3>Industrial Grade Labels</h3>
  <p>
    Our industrial labels are water, UV, chemical and abrasive resistant with a 5 year warranty and rated to withstand temperatures up to 93°C. 
	<br/><br/>
    We supply single label orders and print your own equipment packages are also available.
  </p>
  <br/>
  <section class="small-label">
    <figure class="qr-bg-small">
      <img src="<?php echo url::base() . $qr['qrimg_name']; ?>" alt="" class="qr-label-small" />
    </figure>
    <label>50 X 40 mm Label</label>
    <input type="text" name="qty_45mm" class="text-fi" id="qty_45mm" />

    <p class="enter_quantity">Enter Quantity</p>
  </section>

  <section class="big-label">
    <figure class="qr-bg-big">
      <img src="<?php echo url::base() . $qr['qrimg_name']; ?>" alt="" class="qr-label-big" />
    </figure>
    <label>100 X 75 mm Label</label>
    <input type="text" name="qty_90mm" id="qty_90mm" class="text-fi" />
    <br/>
    <p class="enter_quantity">Enter Quantity</p>
    <div style="height:50px;">&nbsp;</div>
  </section>

</section><!-- /QRCODE LABEL ORDER LEFT -->

<?php /* ?> <form action="<?php echo url::base(); ?>dashboard/orderLabel/<?php echo $id; ?>" method="post" id="frm_order" name="frm_order">

  <input type="hidden" name="cmd" value="_xclick" />
  <input type="hidden" name="business" value="<?php echo $paypal_email;?>"> <?php */ ?>
<?php
$url = url::base() . "dashboard/orderLabel/" . $id;
echo Form::open(
        $url, array(
    'id' => 'frm_order',
    'name' => 'frm_order',
    'method' => 'post',
    'enctype' => 'multipart/form-data'
        )
);
?>
<?php
echo Form::input(
        'quantity_small', 0, array(
    'id' => 'quantity_small',
    'class' => 'text-fi',
    'type' => 'hidden')
);
?> 
<?php
echo Form::input(
        'quantity_big', 0, array(
    'id' => 'quantity_big',
    'class' => 'text-fi',
    'type' => 'hidden')
);
?>


<section class="add-form-left qr-code-order"><!--  FORM -->
  <?php /* ?> <h3>Delivery Address :</h3> <?php */ ?>
  <ul class="form-view clearfix">
    <?php /* ?>
      <li class="width-half">
      <label>First Name: *</label>
      <input type="text" name="first_name" id="first_name" value="" class="text-fi">
      </li>
      <li class="width-half">
      <label>Last Name: *</label>
      <input type="text" name="last_name" id="last_name" class="text-fi" value="">
      </li>
      <li>
      <label>Address1: *</label>
      <input type="text" name="address1" id="address1" class="text-fi" />
      </li>
      <li>
      <label>Address2:</label>
      <input type="text" name="address2" id="address2" class="text-fi" />
      </li>
      <li class="width-half">
      <label>City: *</label>
      <input type="text" name="city" id="city" class="text-fi" />
      </li>
      <li class="width-half">
      <label>State: *</label>
      <input type="text" name="state" id="state" class="text-fi" />
      </li>
      <li class="width-half">
      <label>Postal Code: *</label>
      <input type="text" name="zip" id="zip" class="text-fi" />
      </li>
      <li class="width-half">
      <label>Country: *</label>
      <input type="text" name="country"  id="country" class="text-fi" />
      </li>
      <li class="width-half">
      <label>Email: *</label>
      <input type="text" name="email" id="email" class="text-fi" />
      </li>
      <li class="width-half">
      <label>Contact: *</label>
      <input type="text" name="contact" id="contact" class="text-fi" />
      <input type="hidden" name="custom" id="custom" value="" class="text-fi" />
      </li>
      <?php */ ?>
    <li style="width: 100%">
      <h3>Order Summary :</h3>
      <table class="listing-table" border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr>
          <th>Name</th>
          <th>Qty</th>
          <th>Price</th>
          <th>Total</th>
        </tr>
        <tr>
          <td>1) 50 X 40 mm label</td>
          <td id="qty_small" align="center">0</td>
          <td id="price_small" align="center">3</td>
          <td id="tot_small" align="center">0</td>
        </tr>
        <tr>
          <td>2) 100 X 75 mm label</td>
          <td id="qty_big" align="center">0</td>
          <td id="price_big" align="center">5</td>
          <td id="tot_big" align="center">0</td>
        </tr>
        <tr>
          <td colspan="3"><strong>Total</strong></td>
          <td align="center" id="total"><strong>0</strong></td>
        </tr>
        <tr>
          <td colspan="3">
            Postage & Handling 
            <input type="hidden" name="postage" value="8.95" />
          </td>
          <td id="postage" align="center">0</td>
        </tr>
        <tr>
          <td colspan="2"><strong>Grand Total (inc GST)</strong></td>
          <td align="center">AUD<input type="hidden" name="currency_code" value="AUD" /></td>
          <td id="gtotal" align="center"><strong>0</strong></td>
        </tr>
      </table>
    </li>
    <?php /* ?>
      <li  style="width: 100%">
      <span class="fright">
      <a href="#"><img src="<?php echo url::base(); ?>images/payment-gateway1.png" alt="" /></a>&nbsp;
      <a href="#"><img src="<?php echo url::base(); ?>images/payment-gateway2.png" alt="" /></a>&nbsp;
      <a href="#"><img src="<?php echo url::base(); ?>images/payment-gateway3.png" alt="" /></a>
      </span>
      </li>
      <?php */ ?>
    <li  style="width: 100%">
      <input type="button" onclick="javascript:return saveOrder(this.form)" value="Next" class="save" id="save" name="save">
      <?php /* ?>  <input type="hidden" name="item_name" value="<?php echo "ScanLink QR Label"; ?>">
        <input type="hidden" name="item_number" value="<?php echo $profile['id']; ?>">
        <input type="hidden" name="amount" id="amount" value="">
        <input type="hidden" name="notify_url" value="<?php echo url::base(); ?>notify" />
        <input type="hidden" name="return" value="<?php echo url::base(); ?>dashboard/" /> <?php */ ?>

    </li>
  </ul>

	<div class="green_label">Customised Labels</div>
	<div>We also supply customised industrial labels featuring your logo/design<br> with up to 4 colours.</div>
	<div class="green_label1"><a  href="<?php echo URL::base();?>welcome/contact">Contact us to discuss your requirements</a></div>

</section><!-- /FORM -->
<?php /* ?> </form> <?php */ ?>
<?php echo Form::close() ?> 

<?php

function format_phone($phone) {
  $phone = preg_replace("/[^0-9]/", "", $phone);
  if (strlen($phone) == 7)
    return preg_replace("/([0-9]{3})([0-9]{4})/", "$1-$2", $phone);
  elseif (strlen($phone) == 10)
    return preg_replace("/([0-9]{4})([0-9]{3})([0-9]{3})/", "$1 $2 $3", $phone);
  else
    return $phone;
}
?>
<script type="text/javascript" language="javascript">
  $(document).ready(function (){
    $("#qty_90mm").keyup(function(){
      var qty = $("#qty_90mm").val();
      qty=parseInt(qty);
      if(isNaN(qty)){
        $("#qty_90mm").val("");
        qty=0;
      }else{
        $("#qty_90mm").val(qty);
      }
      $("#qty_big").html(qty);
			
      var tot_big = parseInt($("#qty_big").html())*parseInt($("#price_big").html());
      $("#tot_big").html(tot_big);
			
      $("#total").html(parseInt($("#tot_small").html())+parseInt($("#tot_big").html()));
			
      $("#gtotal").html(parseInt($("#total").html())+parseFloat($("#postage").html()));
			
      $("#amount").val($("#gtotal").html());
			
      $('#quantity_big').val($("#qty_90mm").val());
			
      setCustom($("#qty_45mm").val(),$("#qty_90mm").val());
			
    });
		
    $("#qty_45mm").keyup(function(){
      var qty = $("#qty_45mm").val();
      qty=parseInt(qty);
      if(isNaN(qty)){
        $("#qty_45mm").val("");
        qty=0;
      }else{
        $("#qty_45mm").val(qty);
      }
      $("#qty_small").html(qty);
			
      var small_tot = parseInt($("#qty_small").html())*parseInt($("#price_small").html());
      $("#tot_small").html(small_tot);
			
      $("#total").html(parseInt($("#tot_small").html())+parseInt($("#tot_big").html()));
			
      $("#gtotal").html(parseInt($("#total").html())+parseFloat($("#postage").html()));
			
      $("#amount").val($("#gtotal").html());
			
      $('#quantity_small').val($("#qty_45mm").val());
			
      setCustom($("#qty_45mm").val(),$("#qty_90mm").val());
			
    });
		
    setCustom($("#qty_45mm").val(),$("#qty_90mm").val());
		
    $("#total").html(parseInt($("#tot_small").html())+parseInt($("#tot_big").html()));
    $("#gtotal").html(parseInt($("#total").html())+parseFloat($("#postage").html()));
		
  });
	
  function setCustom(qty_small,qty_big){
    var qty = $("#qty_45mm").val();
    qty=parseInt(qty);
    if(isNaN(qty)){
      $("#qty_45mm").val("");
      qty=0;
    }else{
      $("#qty_45mm").val(qty);
    }
		
    var qty_small=qty;
    $("#qty_small").html(qty);
		
    var small_tot = parseInt($("#qty_small").html())*parseInt($("#price_small").html());
    $("#tot_small").html(small_tot);
		
    var qty = $("#qty_90mm").val();
    qty=parseInt(qty);
    if(isNaN(qty)){
      $("#qty_90mm").val("");
      qty=0;
    }else{
      $("#qty_90mm").val(qty);
    }
    var qty_big=qty;
    $("#qty_big").html(qty);
		
    var tot_big = parseInt($("#qty_big").html())*parseInt($("#price_big").html());
    $("#tot_big").html(tot_big);
		
    var custom = qty_small+"|"+qty_big;
    $("#custom").val(custom+"|<?php echo $user_id; ?>");
  }
	
<?php /* ?> 
  function saveOrder(frm){
  if(parseInt($("#total").html())!=0){
  var fname = document.getElementById("first_name").value;
  var lname = document.getElementById("last_name").value;
  var adr1 = document.getElementById("address1").value;
  var adr2 = document.getElementById("address2").value;
  var city = document.getElementById("city").value;
  var state = document.getElementById("state").value;
  var postcode = document.getElementById("zip").value;
  var country = document.getElementById("country").value;
  var email = document.getElementById("email").value;
  var contact = document.getElementById("contact").value;

  if(fname=="" || lname == "" || adr1 == "" || city == "" || state == "" || postcode == "" || country == "" || email == "" || contact == ""){
  alert("Please fill out all necessary fields for delivery address");
  return false;
  }
  else{
  $.post('<?php echo url::base(); ?>notify/tempOrder',$("#frm_order").serialize(),function(data){ $("#custom").val(data); frm.submit();  });
  return true;
  }
  }else{
  alert("Please provide quantity !");
  return false;
  }
  }
  <?php */ ?>
    function saveOrder(frm){
      if(parseInt($("#total").html())!=0){
        $('#frm_order').submit();
        return true;
      }else{
        $('#frm_order').submit();
        return true;
        alert("Please provide quantity !");
        return false;
      }
    }
</script>
<?php } else { ?>
  <p>You can not perform this action on expired profile.</p>
<?php } ?>