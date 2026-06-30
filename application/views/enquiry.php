<script type="text/javascript">
function checkAll(obj){
	if(obj.checked == true){
		document.getElementById("facilityAsset").checked = true;
		document.getElementById("plantEquipment").checked = true;
		document.getElementById("productProcedure").checked = true;
		document.getElementById("communicationTraining").checked = true;
		document.getElementById("videoProduction").checked = true;
		document.getElementById("QRCodeVisual").checked = true;
	}
	else{
			document.getElementById("facilityAsset").checked = false;
			document.getElementById("plantEquipment").checked = false;
			document.getElementById("productProcedure").checked = false;
			document.getElementById("communicationTraining").checked = false;
			document.getElementById("videoProduction").checked = false;
			document.getElementById("QRCodeVisual").checked = false;
	}
}
</script>
<div class="title">
  <p>These are just a few of the applications that ScanLink can be used for.
    If you want to know more, or have any suggestions, please fill out our
    enquiry form and we will be in contact to chat about how we can
    partner with you to achieve your Safety Culture Goals.</p>
</div>
<div class="content">
  <div class="simple-rounded-box">
    <div class="title"> <img class="imgLeft" alt="image" src="<?php echo URL::base();?>images/qrcode-left-img.png">
      <h1>ScanLink Solutions Enquiry Form</h1>
      <img class="imgRight" alt="image" src="<?php echo URL::base();?>images/qrcode-right-img.png"> </div>
      <?php if(isset($errors)){ ?>
       		<div class="error top"><?php echo $errors; ?></div>
            <?php } ?>
    <form style="padding-left:10px;" method="post" id="enquiryForm" action="">
      <ol class="forms">
        <li>
          <label for="companyName">Company Name:</label>
          <input type="text" value="" id="companyName" name="companyName">
        </li>
        <li>
          <label for="address">Address:</label>
          <textarea cols="70" rows="3" id="address" name="address"></textarea>
        </li>
        <div class="row">
          <li class="innerCol">
            <label for="tel">Tel:</label>
            <input type="text" value="" id="tel" name="tel">
          </li>
          <li class="innerCol">
            <label for="email">Email:</label>
            <input type="text" value="" id="email" name="email">
          </li>
        </div>
        <li>
          <label for="contactName">Contact Name:</label>
          <input type="text" value="" id="contactName" name="contactName">
        </li>
        <div class="row">
          <li class="innerCol">
            <label for="industryType">Industry Type:</label>
            <input type="text" value="" id="industryType" name="industryType">
          </li>
          <li class="innerCol">
            <label for="companySize">Company Size:</label>
            <input type="text" value="" id="companySize" name="companySize">
          </li>
        </div>
        <li>
          <label for="briefDescription">Brief Description of Company Focus:</label>
          <textarea cols="70" rows="3" id="briefDescription" name="briefDescription"></textarea>
        </li>
        <li class="inline"> <span>Tick the ScanLink Application you're interested in:</span> </li>
        <li class="inline"> <span id="span_text" for="sendCopy">Facility Asset Management</span>
          <input type="checkbox" class="nostyle" value="1" id="facilityAsset" name="facilityAsset">
        </li>
        <li class="inline"> <span id="span_text" for="sendCopy">Plant &amp; Equipment Document Management</span>
          <input type="checkbox" class="nostyle" value="1" id="plantEquipment" name="plantEquipment">
        </li>
        <li class="inline"> <span id="span_text" for="sendCopy">Product &amp; Procedure Management</span>
          <input type="checkbox" class="nostyle" value="1" id="productProcedure" name="productProcedure">
        </li>
        <li class="inline"> <span id="span_text" for="sendCopy">Communication Training</span>
          <input type="checkbox" class="nostyle" value="1" id="communicationTraining" name="communicationTraining">
        </li>
        <li class="inline"> <span id="span_text" for="sendCopy">Video Production, or Re-Editing for Smartphone use</span>
          <input type="checkbox" class="nostyle" value="1" id="videoProduction" name="videoProduction">
        </li>
        <li class="inline"> <span id="span_text" for="sendCopy">QR Code Visual Communication</span>
          <input type="checkbox" class="nostyle" value="1" id="QRCodeVisual" name="QRCodeVisual">
        </li>
        <li class="inline"> <span id="span_text" for="sendCopy">All of the above</span>
          <input type="checkbox" onclick="checkAll(this)" class="nostyle" value="1" id="allOfAbove" name="allOfAbove">
        </li>
        <li>
          <label for="comments">Comments</label>
          <textarea class="requiredField" cols="70" rows="3" id="comments" name="comments"></textarea>
        </li>
      </ol>
      <div class="clear"></div>
      <div>
        <input type="hidden" value="true" id="submitted" name="submitted">
      </div>
      <div class="form-button-left">
        <input type="submit" value="Send Enquiry" name="submit" class="save" id="save">
      </div>
      <div class="clear"></div>
    </form>
  </div>
</div>
