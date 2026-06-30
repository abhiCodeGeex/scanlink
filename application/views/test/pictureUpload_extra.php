<script language="JavaScript">


  var imageUploadHeight = 0;
  var imageUploadTimer = null;
  $(document).ready(function(){
    imageUploadHeight = $(document).height();
    imageUploadTimer = setInterval(chnageimageUploadHeight,1000);
  });

  function chnageimageUploadHeight(){
    if($(document).height()!=imageUploadHeight){
      $(window.parent.document.getElementById("imageUpload1")).height($(document).height());
      imageUploadHeight=$(document).height();
    }
  }

  window.parent.$("#save").bind('click',function(){
    if($("form").length>2){
      alert("Some Pictures for the profile are not uploaded. Plase Upload them");
      return false;
    }
  });





</script>
<!--[if lte IE 7]>
<style type="text/css">
.file-uploader {
  margin-left: 0px; 
  margin-top:5px;
}
</script>
<![endif]-->  

<!--[if IE 9]>
<style type="text/css">
.file-uploader {
  margin-left: 0px; 
  margin-top:5px;
}
</script>
<![endif]-->

<!--[if IE 8]>
<style type="text/css">
.file-uploader {
  margin-left: 0px; 
  margin-top:5px;
}
</script>
<![endif]-->  
<style type="text/css">
  html,body,.middle-outer2{ background:#F1F1F1 !important; background-image:none !important  }
  .middle-outer2{ min-height:none!important;}
  .curve, .curve2,.footer-outer,.header{ display:none!important;}
  .middle{ padding:0!important;}
  .input-w{ background: none repeat scroll 0 0 #FFFFFF; border: 1px solid #C6C6C6; border-radius: 5px 5px 5px 5px; box-shadow: 0 1px 3px -1px #666666 inset; font-size: 12px; margin: 0; padding: 3px 3px; width: 42%; }
</style>
<div id="main_container" class="file-uploader">
  <form action="<?php echo url::base(); ?>dashboard/savePictureFile_extra" method="post" enctype="multipart/form-data">
    <span>Text Footer :</span><input type="text" class="input-w" name="txt_footer" maxlength="500">
	<input type="file" name="userfile" class="fileUpload">
    <input type="hidden" name="profile_id_picture" id="profile_id_picture" value="<?php echo $profile_id; ?>">
    <input type="hidden" name="type_id" id="type_id" value="<?php echo $type_id; ?>">
    <button id="px-submit" type="submit">Upload</button>
    <button id="px-clear" type="reset">Clear</button>
  </form>
  <script type="text/javascript">
    jQuery(function($){
      $('.fileUpload').fileUploader({
        autoUpload: false,
        selectFileLabel: 'Select',
        allowedExtension: 'jpg|jpeg|gif|png',
        limit: 100,
        afterEachUpload: function(data, status, formContainer) {
          //          if(status=="success"){
          //            window.parent.document.getElementById("uploaded").value = 1;
          //          }
		
          if(status.match(/success/g).length > 0){
		  	window.parent.saveornot = false;
            $('.status').html(data);
          }else
          {
            $('.status').html(data);
          }

        }
        
      });
    });
    $("#content").css("box-shadow","none");
    $("#content").css("background","white");
    $("html").addClass("remove-bg");
    $("body").addClass("remove-bg");
  </script>

</div>