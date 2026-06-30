<script language="JavaScript">
var logoUploadHeight = 0;
var logoUploadTimer = null;
$(document).ready(function(){
	logoUploadHeight = $(document).height();
	logoUploadTimer = setInterval(chnagelogoUploadHeight,1000);
});

function chnagelogoUploadHeight(){
	if($(document).height()!=logoUploadHeight){
		$(window.parent.document.getElementById("logoUpload")).height($(document).height());
		logoUploadHeight=$(document).height();
	}
}

window.parent.$("#save").bind('click',function(){
	if($("form").length>2){
		alert("Logo is not uploaded. Please Upload");
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
html, body, .middle-outer2 {
	background-color:#F1F1F1 !important;
	background-image:none !important
}
.middle-outer2 {
	min-height:none!important;
}
.curve, .curve2, .footer-outer, .header {
	display:none!important;
}
.middle {
	padding:0!important;
}
.input-w {
	background: none repeat scroll 0 0 #FFFFFF;
	border: 1px solid #C6C6C6;
	border-radius: 5px 5px 5px 5px;
	box-shadow: 0 1px 3px -1px #666666 inset;
	font-size: 12px;
	margin: 0;
	padding: 3px 3px;
	width: 42%;
}
</style>
<div id="main_container" class="file-uploader">
  <form action="<?php echo url::base(); ?>dashboard/saveTempPictureFile" method="post" enctype="multipart/form-data">
    <input type="file" name="userfile" class="fileUpload">
    <input type="hidden" name="profile_id" id="profile_id" value="<?php echo $profile_id; ?>">
    <button id="px-submit" type="submit">Upload</button>
    <button id="px-clear" type="reset">Clear</button>
  </form>
  <script type="text/javascript">
    jQuery(function($){
      $('.fileUpload').fileUploader({
        autoUpload: false,
        selectFileLabel: 'Select',
        allowedExtension: 'jpg|jpeg|gif|png',
        limit: 1,
        afterEachUpload: function(data, status, formContainer) {
                  /*  if(status=="success"){
                      window.parent.document.getElementById("uploaded").value = 1;
                    }*/
			if(window.parent.document.getElementById("uploaded_file"))
			   window.parent.document.getElementById("uploaded_file").value = 1;
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
