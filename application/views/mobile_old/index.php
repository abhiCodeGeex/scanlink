
<style>
  #dialog{
    z-index:99999 !important;
    padding: 10px 5px;
  }
  #dialog-box{
    text-align:center !important;
    opacity:.6 !important;
    background-color: #000 !important;
    width:100% !important;
    height:100% !important;
    left: 0 !important;
    opacity: 0.6 !important;
    position: fixed !important;
    top: 0 !important;
    z-index: 999 !important;

  }
  .btnClass, .btnClass a {
    background:#5CCD00;
    background:-moz-linear-gradient(top,#5CCD00 0%,#4AA400 100%);
    background:-webkit-gradient(linear,left top,left bottom,color-stop(0%,#5CCD00),color-stop(100%,#4AA400));
    background:-webkit-linear-gradient(top,#5CCD00 0%,#4AA400 100%);
    background:-o-linear-gradient(top,#5CCD00 0%,#4AA400 100%);
    background:-ms-linear-gradient(top,#5CCD00 0%,#4AA400 100%);
    background:linear-gradient(top,#5CCD00 0%,#4AA400 100%);
    filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#5CCD00',endColorstr='#4AA400',GradientType=0);
    padding:10px 0px;
    color:#fff;
    font-family:'Helvetica Neue',sans-serif;
    font-size:20px !important;
    border-radius:5px;
    -moz-border-radius:5px;
    -webkit-border-radius:5px;
    border:1px solid #459A00;
    text-decoration:none;
    min-width:200px!important;
    cursor:pointer;
    display:inline-block;
  }
  .powerby a{
    color:#57C200 !important;
    text-decoration:none;
  }

  .clear{
    clear:both;
  }

  #uniform-proceed_to_coupon{
    font-size: 20px;
  }

  .videoWrapper {
    position: relative;
    padding-bottom: 56.25%; /* 16:9 */
    padding-top: 25px;
    height: 0;
  }
  .videoWrapper iframe {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
  }
  .popup_content{
    font-size:18px;
  }
</style>

<?php if (count($videos) != 0) { ?>
  <?php
  $i = 0;
  foreach ($videos as $key => $video): $i++;
    ?>
    <p align="center">
      <?php // ?rel=0     for disable related videos ?>
    <div class="videoWrapper">
      <!-- Copy & Pasted from YouTube -->
      <iframe src="http://www.youtube.com/embed/<?php echo $video['video_name']; ?>?rel=0" frameborder="0" allowfullscreen wmode="transparent"></iframe>
    </div>
    <?php /* <a href="<?php echo 'http://www.youtube.com/watch?v=' . $video['video_name']; ?>" target="_blank"><?php echo $video['title']; ?></a> */ ?>
    </p>
  <?php endforeach; ?>
<?php } ?>


<?php
foreach ($profile as $key => $value) {
  $profile[$key] = trim(strip_tags($value));
}
/* echo "<pre>";
  print_r ($profile);exit; */
?>
<div class="MobileBottomText">
  <h3>
    <?php
    if ($profile['type_id'] == "1") {
      echo "Make / Model";
    } else if ($profile['type_id'] == "2") {
      echo "Location name";
    } else if ($profile['type_id'] == "3") {
      echo "Facility name";
    } else if ($profile['type_id'] == "4") {
      echo "Product name";
    } else if ($profile['type_id'] == "5") {
      echo "Title";
    } else if ($profile['type_id'] == "6") {
      echo "Name";
    }
    ?>		
  </h3>
  <p><?php echo $profile["name"]; ?></p>

  <?php if ($profile["position"] != "") { ?>	
    <h3>Position</h3>
    <p><?php echo $profile["position"]; ?></p>
  <?php } ?>

  <?php if ($profile["identification"] != "") { ?>
    <h3>ID</h3>
    <p><?php echo $profile["identification"]; ?></p>
  <?php } ?>

  <?php if ($profile["serial_no"] != "") { ?>
    <h3>Serial No.</h3>
    <p><?php echo $profile["serial_no"]; ?></p>
  <?php } ?>

  <?php if ($profile["address"] != "") { ?>
    <h3>Address</h3>
    <p><?php echo $profile["address"]; ?></p>
    <h3>&nbsp;</h3>
    <p>&nbsp;<a href="#" id="map-link" onClick="javascript:return getMapLink('<?php echo $profile["address"]; ?>')" class="view-map">View Map</a></p>
  <?php } ?>

  <?php if ($profile["description"] != "") { ?>
    <?php if ($profile['type_id'] != '8'): ?><h3>Description</h3> <?php endif; ?>
    <p><?php echo $profile["description"]; ?></p>
  <?php } ?>

  <?php if ($profile["notes"] != "") { ?>
    <h3>Notes</h3>
    <p><?php echo $profile["notes"]; ?></p>
  <?php } ?>

  <?php if ($profile["name_company"] != "") { ?>
    <h3>Contact</h3>
    <p><?php echo $profile["name_company"]; ?></p>
  <?php } ?>

  <?php /* if(!empty($checklist_items) && $checklist_option == 1){ ?>
    <div class="cal"><div><strong>Please Tick box once scanned :</strong></div>
    <?php foreach($checklist_items as $checklist_item){ ?>
    <input type="checkbox" class="prettyCheckable" <?php echo (in_array($checklist_item['id'],$checklist_items_checked))?'checked="checked"':""; ?> id="checklist_item_<?php echo $checklist_item['id']; ?>" name="checklist_item[]" value="<?php echo $checklist_item['id']; ?>" data-label="<?php echo $checklist_item['checklist_item']; ?>" /><br />
    <?php } ?>
    </div>
    <?php } */ ?>

  <?php //if ($profile["telephone"] != "" && trim($profile["telephone"]) != 0) { ?>
  <?php if ($profile["telephone"] != "") { ?>
    <h3>Telephone</h3>
    <p><a href="tel:<?php echo $profile["telephone"]; ?>"><?php echo $profile["telephone"]; ?></a></p>
  <?php } ?>
</div>

<?php if (is_array($pictures) && count($pictures) > 0): ?>
  <div style="width: 100%">
    <div class="swiper-main">
      <div class="swiper-container swiper1">
        <div class="swiper-wrapper">
          <?php foreach ($pictures as $key => $val): ?>
            <div class="swiper-slide" style="text-align: center"> 
              <img src="<?php echo URL::base() . $val['picture_name'] ?>" style="max-height: 200px;max-width:250px;" />
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
    <div class="pagination pagination1"></div>
  </div>
<?php endif; ?>

<?php
/* echo "<pre>";
  print_r($documents);
  echo "</pre>"; */
?>

<div class="MobileInnerList">
  <ul class="documentList">
    <?php if (isset($documents) && is_array($documents) && count($documents) > 0) { ?>
      <?php
      $i = 0;
      foreach ($documents as $key => $document): $i++;
        ?>
        <li><a href="<?php echo URL::base() . $document['doc_name']; ?>" target="_blank"><?php echo $document['name'] != "" ? $document['name'] : $document['doc_name']; ?></a></li>
      <?php endforeach; ?>
    <?php }else { ?>
    <?php } ?>

    <?php if (isset($weblink) && is_array($weblink) && !empty($weblink)) { ?>
      <?php foreach ($weblink as $key => $myweblink) { ?>
        <?php if ($myweblink['link_button'] == "1"): ?>
          <li <?php echo $myweblink['link_button_color'] != "" ? 'style="background: #' . $myweblink['link_button_color'] . '"' : '' ?> ><a href="<?php echo $myweblink['link_button_url']; ?>" target="_blank"><?php echo $myweblink['link_button_text']; ?></a></li>
        <?php endif; ?>
      <?php } ?>
    <?php } ?>

  </ul>
</div>

<?php if (isset($profile['display_share_link']) && $profile['display_share_link'] == '1') { ?>
  <div class="shareNav-mob">
    <a href="http://www.facebook.com/share.php?u=<?php echo $profile['shorturl']; ?>" target="_blank" class="shareFB">Facebook</a>
    <a href="http://twitter.com/share?text=<?php echo $profile["name"] . "  " . $profile['shorturl']; ?>" target="_blank" class="shareTWT">Twitter</a>
    <a href="mailto:?subject=Visit this link&amp;body=Hi, I found this information for you! Have a nice day :) : <?php echo $profile['shorturl']; ?>"  target="_blank" class="shareEML">Email</a>
  </div>
<?php } ?>



<script type="text/javascript">
  
  $(function(){
	
    //Main Swiper
    var swiper = new Swiper('.swiper1', {
      loop:true,
      grabCursor: true
    });
    
    
    
    
    
  });
  
  $(window).bind("load", function() {
    
    //$('.swiper-slide').css('width','320px');
    $('.swiper-container').css('width','320px');
    $(window).trigger('resize');
    //console.log($('.swiper-slide').css('width'));
  });
</script>



<script type="text/javascript" language="javascript">
  
  
  
  
  function setCookie(pLabel, pVal, pMinutes)
  {
    var tExpDate=new Date();
    tExpDate.setTime( tExpDate.getTime()+(pMinutes*60*1000) );
    document.cookie= pLabel + "=" +escape(pVal)+
      ( (pMinutes==null) ? "" : ";expires="+ tExpDate.toGMTString() );
  }	

  function getCookie(c_name)
  {
    var i,x,y,ARRcookies=document.cookie.split(";");
    for (i=0;i<ARRcookies.length;i++)
    {
      x=ARRcookies[i].substr(0,ARRcookies[i].indexOf("="));
      y=ARRcookies[i].substr(ARRcookies[i].indexOf("=")+1);
      x=x.replace(/^\s+|\s+$/g,"");
      if (x==c_name) {
        return unescape(y);
      }
    }
  }
  function numbersonly(e){
    var unicode=e.charCode? e.charCode : e.keyCode
    if (unicode!=8){ //if the key isn't the backspace key (which we should allow)
      if (unicode<48 || unicode>57) //if not a number
        return false //disable key press
    }
  }
      
  function checkEmail(email) {
    var filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,6})+$/;
    if (!filter.test(email)) {
      return false;
    }else{
      return true;
    }
  }
  
  
  $(function(){
    $(this).wpanalytics("<?php echo $profile['analytic_key']; ?>");
  
    var currentdate = new Date();
    var dateTime = currentdate.getFullYear() + "-" +currentdate.getMonth() + "-" + currentdate.getDate() + " " + currentdate.getHours() + ":" + currentdate.getMinutes() + ":" + currentdate.getSeconds();
    alert (dateTime);
  
<?php //if ($profile["enable_data_collection"] == "1" && (!Session::instance()->get('user_info') || trim(Session::instance()->get('user_info')) != $profile["id"])) {                                   ?>
<?php if ($profile["enable_data_collection"] == "1" && (!Session::instance()->get('user_info') || trim(Session::instance()->get('user_info')) != $profile["id"])) { ?>
                                                                             

      setTimeout(function(){
        $('.ui-dialog-titlebar-close').hide();
        //$('#dialog-box').css('width',$(document).width()+'%');
        //$('#dialog-box').css('height',$(document).height()+'%');
      },500);
                                                                              														
      if(!getCookie("isContactDialog")){
        //setCookie("isContactDialog", true, 10)
        $(function() {
          $('#dialog-box').show();
          $( "#dialog" ).dialog({
            autoOpen: true,
            show: "blind",
            draggable: false,
            position:'center',
            //title:'Exclusive Offers'
          }).dialog("widget").find(".ui-dialog-title").hide();
        });
      }
      $('.ui-dialog-close').live('click',function(){
              		  
  <?php
  if ($profile["set_up_compulsory"] == 1) {
    ?>
    <?php if ($profile['data_collection_name']) { ?>
                if($('#data_collection_name').val() == "" ){
                  alert("Please Enter a name.");
                  $('#data_collection_name').focus();
                  return false;
                }
    <?php } ?>
    <?php if ($profile['data_collection_mobile']) { ?>
                if($('#mobile_number').val() == "" ){
                  alert("Please Enter a mobile number.");
                  $('#mobile_number').focus();
                  return false;
                }
    <?php } ?>
    <?php if ($profile['data_collection_email']) { ?>
                if($('#email_address').val() == "" ){
                  alert("Please Enter a email address.");
                  $('#email_address').focus();
                  return false;
                }
    <?php } ?>
    <?php
  }
  ?> 
                                                                              					  
                                                                              					  
          if ($('#mobile_number').length > 0){
            if($('#mobile_number').val() !== "" ){
              if($('#mobile_number').val().length != 10){
                alert("Invalid contact number. Please enter a valid 10 digit mobile phone number.");
                $('#mobile_number').focus();
                return false;
              }
            }  
          }
          //alert (typeof($('#email_address').length));
          if ($('#email_address').length > 0){
            if($('#email_address').val() !== "" ){
              if(!checkEmail($('#email_address').val())){
                alert("Invalid email address");
                $('#email_address').focus();
                return false;
              }
            }  
          }
                                                                        
                                                                        
                                                                              					
                                                                  
          var currentdate = new Date();
          var dateTime = currentdate.getFullYear() + "-" +currentdate.getMonth() + "-" + currentdate.getDate() + " " + currentdate.getHours() + ":" + currentdate.getMinutes() + ":" + currentdate.getSeconds();
          alert (dateTime);
          
                    
                                                                      
          $.ajax({
            type: 'post',
            url: '<?php echo url::site('mobile/addUserInfo'); ?>',
            data: {
              profile_id: $('#profile_id').val(),
              mobile_number: $('#mobile_number').val(),
              email_address: $('#email_address').val(),
              name: $('#data_collection_name').val(),
              datetime: dateTime
                                                                                
            },
            success: function(data){
              console.log(data);
            },
            error: function(e){
                                                                                
            }
          })
                                                                      

                                                                   
          $('#dialog-box').hide();
          $( "#dialog" ).dialog( "close" );
          return false;
        });
<?php } ?>
  
  
  });
 
  var mymap=null;
  function getMapLink(adr){
    var maplink="http://maps.google.com?q="+adr;
    if(mymap==null || mymap.closed){
      mymap = window.open(''+maplink,'mywin', 'left=20,top=20,width=500,height=500,toolbar=1,resizable=0,location=0,status=0');
    }
    return true;
  }</script>
<script type="text/javascript">
  /*var myphotoswipe = null;
$(document).ready(function(){
	
  $('input.prettyCheckable').prettyCheckable({
    color: 'yellow'
  });
	
  myphotoswipe = $(".gallery a").photoSwipe({ enableMouseWheel: false , enableKeyboard: false, allowUserZoom:false });
  $(".gallery li").hide();	
  $(".gallery li:first").show();
	
  $("#photoswipe").click(function(){
    myphotoswipe.show(0);
  });
	
	
});

function showGallery(){
  myphotoswipe.show(0);
}

$('.prettyCheckable').live('change', function(){
  if($(this).is(':checked')){
    $.getJSON('<?php echo URL::base(); ?>checklistitem/checklistCheck/'+$(this).val(),function(data){});
  }else{
    $.getJSON('<?php echo URL::base(); ?>checklistitem/checklistUncheck/'+$(this).val(),function(data){});
  }
});*/


</script>



<div id="dialog-box" style="display: none;" >
  <form name="frm_save_contact" id="frm_save_contact">

    <input type="hidden" name="cid" value="<?php //echo $cid;                                     ?>" />
    <input type="hidden" name="mid" value="<?php //echo $mid;                                     ?>" />
    <input type="hidden" name="profile_id" id="profile_id" value="<?php echo $profile['id']; ?>" />

    <div id="dialog" title="Basic dialog">
      <?php if ($profile['data_collection_content'] != ""): ?>
        <p class="popup_content"><?php echo $profile['data_collection_content']; ?></p>
      <?php else: ?>
        <p class="popup_content">Register your mobile number to receive exclusive weekly offers.</p>
      <?php endif; ?>
      <?php if ($profile['data_collection_name']): ?>
        <p align="center" >

          <?php echo Form::input('name', '', array('type' => 'text', 'placeholder' => 'Name', 'id' => 'data_collection_name')) ?>

          <?php /* <input type="tel" placeholder="Mobile Number"  id="mobile_number" onKeyPress="return numbersonly(event);" name="mobile" width="100%"  maxlength="10" > */ ?>
        </p>
      <?php endif; ?>
      <?php if ($profile['data_collection_mobile']): ?>
        <p align="center" >

          <?php echo Form::input('mobile', '', array('type' => 'tel', 'placeholder' => 'Mobile Number', 'id' => 'mobile_number', 'onKeyPress' => 'return numbersonly(event);', 'maxlength' => 10)) ?>

          <?php /* <input type="tel" placeholder="Mobile Number"  id="mobile_number" onKeyPress="return numbersonly(event);" name="mobile" width="100%"  maxlength="10" > */ ?>
        </p>
      <?php endif; ?>
      <?php if ($profile['data_collection_email']): ?>
        <p align="center" >
          <input type="text" placeholder="Email Address"  id="email_address" name="email_address" width="100%"  maxlength="255" > 
        </p>
      <?php endif; ?>
      <p align="center">
        <input type="button" class="ui-button ui-dialog-close" id="proceed_to_coupon" value="PROCEED">
      </p>
    </div>
  </form>
</div>