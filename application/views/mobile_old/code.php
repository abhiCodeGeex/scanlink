<style type="text/css">
  body{
    background:none !important;
  }
  #content{
    background:none !important;
  }
  .BannerImage{
    display:none;
  }
  #footer{
    display: none;
  }
  footer {
    display: none;
  }
  .popup_content{
	  font-size:18px;
  }

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
</style>

<script type="text/javascript">

function redirect(){
   window.location.href="<?php echo $profile['url']; ?>";
}

$(document).ready(function() {
	// Handler for .ready() called.
  <?php if($profile['enable_data_collection'] == 0  && !(isset($profile['mediumfile']) && $profile['mediumfile'] != '' )){ ?>
  		redirect();
  <?php }else if ((isset($profile['mediumfile']) && $profile['mediumfile'] != '' ) && !(!Session::instance()->get('user_info') || trim(Session::instance()->get('user_info')) != $profile["id"])){ ?>
  			<?php if(!(isset($profile['mediumfile']) && $profile['mediumfile'] != '' )){?>
		  		redirect();
			<?php }else{ ?>
				setTimeout("redirect()",5000);
			<?php } ?>
  <?php }else if((isset($profile['mediumfile']) && $profile['mediumfile'] != '' ) && $profile['enable_data_collection'] == 0){ ?>
  		setTimeout("redirect()",5000);
  <?php } ?>
});
    
	
</script>
<?php
if (isset($profile['mediumfile']) && $profile['mediumfile'] != '') {
  ?>
  <div align="center"><img src="<?php echo url::base() . $profile['mediumfile']; ?>"  align="absmiddle" /></div>
  <?php
} else {
  ?>
 
  <?php
}
?>

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
  
  
  
  
<?php //if ($profile["enable_data_collection"] == "1" && (!Session::instance()->get('user_info') || trim(Session::instance()->get('user_info')) != $profile["id"])) {                               ?>
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
                                                                
                                                                
                                                                      					
                                                          

                                                              
          $.ajax({
            type: 'post',
            url: '<?php echo url::site('mobile/addUserInfo'); ?>',
            data: {
              profile_id: $('#profile_id').val(),
              mobile_number: $('#mobile_number').val(),
              email_address: $('#email_address').val(),
              name: $('#data_collection_name').val()
                                                                        
            },
            success: function(data){
              console.log(data);
			   <?php if(!(isset($profile['mediumfile']) && $profile['mediumfile'] != '' )){ ?>
  					redirect();
			  <?php }else { ?>
				  setTimeout("redirect()",5000);
			  <?php } ?>
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
  }
</script>



<div id="dialog-box" style="display: none;" >
  <form name="frm_save_contact" id="frm_save_contact">

    <input type="hidden" name="cid" value="<?php //echo $cid;                                 ?>" />
    <input type="hidden" name="mid" value="<?php //echo $mid;                                 ?>" />
    <input type="hidden" name="profile_id" id="profile_id" value="<?php echo $profile['id']; ?>" />

    <div id="dialog" title="Basic dialog">
      <?php if ($profile['data_collection_content'] != ""): ?>
        <p class="popup_content"><?php echo $profile['data_collection_content']; ?></p>
        <?php else: ?>
        <p class="popup_content">
          <?php if (isset($profile['description']) && $profile['description'] != '') { ?>
            <?php echo $profile['description']; ?>
          <?php } else { ?>
            Register your mobile number to receive exclusive weekly offers.
        <?php } ?>
        </p>
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

