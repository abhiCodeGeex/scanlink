<script >
 
 var docUploadHeight = 0;
 var docUploadTimer = null;
 
  
 
 
  $(document).ready(function(){
  	
	 docUploadHeight = $(document).height();
    docUploadTimer = setInterval(chnagedocUploadHeight,1000);
  });

  function chnagedocUploadHeight(){
    if($(document).height()!=docUploadHeight){
      $(window.parent.document.getElementById("docUpload")).height($(document).height());
      docUploadHeight=$(document).height();
    }
  }

  window.parent.$("#save").bind('click',function(){
    if($("form").length>2){
      alert("Some Documents for the profile are not uploaded. Plase Upload them");
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

  html,body,.middle-outer2{ background-color:#F1F1F1 !important; background-image:none !important  }
  .middle-outer2{ min-height:none!important;}
  .middle{ padding:0!important;}
  .curve, .curve2,.footer-outer,.header{ display:none!important;}
  .input-w{ background: none repeat scroll 0 0 #FFFFFF; border: 1px solid #C6C6C6; border-radius: 5px 5px 5px 5px; box-shadow: 0 1px 3px -1px #666666 inset; font-size: 12px; margin: 0; padding: 3px 3px; width: 42%; }
</style>
<div id="main_container" class="file-uploader">
  <form action="<?php echo url::base(); ?>dashboard/saveTempDocFile" method="post" enctype="multipart/form-data">
    
    <div style="height: 6px;">&nbsp;</div>
	 <input type="file" name="userfile" class="fileUpload">
    <input type="hidden" name="profile_id_document" id="profile_id_document" value="<?php echo $profile_id; ?>">
    
    <button id="px-submit" type="submit">Upload</button>
    <button id="px-clear" type="reset">Clear</button>
  </form>
  <script type="text/javascript">
    jQuery(function($){
      $('.fileUpload').fileUploader({
        autoUpload: false,
        selectFileLabel: 'Select',
        allowedExtension: 'jpg|jpeg|gif|png|doc|docx|pdf',
        limit: 1,
        afterEachUpload: function(data, status, formContainer) {
          
			if(window.parent.document.getElementById("uploaded_doc<?php echo $_REQUEST['random_id'];?>"))
			   window.parent.document.getElementById("uploaded_doc<?php echo $_REQUEST['random_id'];?>").value = 1;          
          
          if(status.match(/success/g)!=null && status.match(/success/g).length > 0){
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