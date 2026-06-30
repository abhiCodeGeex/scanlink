<script language="JavaScript">


var imageUploadHeight = 0;
var imageUploadTimer = null;
$(document).ready(function(){
	imageUploadHeight = $(document).height();
	imageUploadTimer = setInterval(chnageimageUploadHeight,1000);
});

function chnageimageUploadHeight(){
	if($(document).height()!=imageUploadHeight){
		$(window.parent.document.getElementById("imageUpload")).height($(document).height());
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
<style type="text/css">
body,#main_container,#container_wrap{ background-color:#F1F1F1 !important; background-image:none !important  }
.input-w{ background: none repeat scroll 0 0 #FFFFFF; border: 1px solid #C6C6C6; border-radius: 5px 5px 5px 5px; box-shadow: 0 1px 3px -1px #666666 inset; font-size: 12px; margin: 0; padding: 3px 3px; width: 42%; }
</style>
<div id="main_container">
	<form action="<?php echo url::base(); ?>plant/savePictureFile" method="post" enctype="multipart/form-data">
    	<input type="hidden" name="uid" id="uid" value="<?php echo $uid?>" />
		<input type="file" name="userfile" class="fileUpload">
		<button id="px-submit" type="submit">Upload</button>
		<button id="px-clear" type="reset">Clear</button>
	</form>
	<script type="text/javascript">
		jQuery(function($){
			$('.fileUpload').fileUploader({
				autoUpload: false,
				selectFileLabel: 'Select',
				allowedExtension: 'jpg|jpeg|gif|png'
			});
		});
	</script>
</div>