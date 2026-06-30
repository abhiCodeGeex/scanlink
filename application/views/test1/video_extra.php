<script type="text/javascript">
 
  function checkForFile() {
    if (document.getElementById('file').value) {
      return true;
    }
    document.getElementById('errMsg').style.display = '';
    return false;
  }  
  
  function addVideo(title,video_id){
    parent.document.getElementById("video2").value+=title+'='+video_id+",";
    alert("Video is Selected...");
	window.parent.saveornot = false;
    parent.$.colorbox.close();
  }
  
  function removeVideo(video_id){
    if(confirm("Are you sure to remove this Video ? ")){
      $.getJSON('<?php echo URL::base(); ?>plant/delete_video/'+video_id, function(data) {
       	$('#video_row'+video_id).remove();
		$('#video_line'+video_id).remove();
      });
    }
  }
  
  function addYoutubeVideo(){
    var ytitle = document.getElementById("ytitle").value;
    var yurl = document.getElementById("yurl").value;
    if(ytitle !='' && yurl != ''){
      parent.document.getElementById("video2").value+=ytitle+'='+yurl+",";
      alert("Video is Saved...");
	  window.parent.saveornot = false;
      parent.$.colorbox.close();
    }
    else{
      alert("Please Enter Video title and url");
      return false;
    }
  }
  function viewLoader(){
    document.getElementById("loader-wrap").style.display = 'block';
  }
  
</script>
<style type="text/css">
  html,body,.middle-outer2{ background-color:#F1F1F1 !important; background-image:none !important  }
  .middle-outer2{ min-height:none!important;}
  .middle{ padding:0!important; width:auto!important;}
  .curve, .curve2,.footer-outer,.header{ display:none!important;}
  .main-row .row { width:85px; }
  .main-row .row-r { width:200px; }
  .main-row .row-btn{ width:150px;float: left; margin: 0; padding: 3px; }
  .main-row .row-btn .save{ font-size:14px; }
  table td{ font-size:12px;
  	word-wrap: break-word;
   }
  .save{ font-size:12px; }
  .select-btn{ width:50px; }
  fieldset h3{ font-size:16px; font-weight:700; padding-left:5px; margin-top:0px; }
  fieldset .form-items {
    padding: 10px;
  }
  .vid-sep-td{ line-height:5px; }
  .vid-sep{ border-bottom:1px dotted #CCC; margin-bottom:5px; } 
  fieldset{ border:1px solid #CCC; position:relative; padding:10px; -webkit-border-radius: 5px;-moz-border-radius: 5px;border-radius: 5px; margin:5px;}
  legend{ margin-left:10px; }
  #loader-wrap{float: left;padding-top: 6px;display:none;}
  .fnt15{ font-size:15px;}
  .save-you-btn{ width:84px; }
  .remove-pad{ padding:0 5px !important;}
  .row-r.inpt{ width:90px; }
  .you-txt{float: left; line-height: 38px;}
  .wrapper{
    width: auto !important;
  }

  .message_success, .message_error {
    position: absolute;
    width: 280px;
    padding-top: 15px;
    padding-bottom: 15px;
    text-align: center;
    font-size: 16px;

    left: 50%;
    margin-left: -140px;
    margin-top: -8px;
    z-index: 10;
    border-radius: 8px;
  }

  .message_success {
    color: #ffffff;
    background-color: #009900;
  }

  .message_error {
    color: #D8000C;
    background-color: #FFD2D2;
  }
</style>
<div id="progressbar" style="opacity: 0.9">
  <img src="<?php echo url::base(); ?>images/spinner_gray.gif" align="absmiddle" /> Please Wait. Video Uploading...
</div>
<table cellpadding="2" cellspacing="0" width="100%">
  <tr>
    <td width="49%" valign="top">
      <fieldset>
        <legend align="left" >&nbsp;<img src="<?php echo url::base(); ?>images/scanlink-logo.png" width="150" /> </legend>        
        <h3>Upload New</h3>

        <form id="video_upload_form" class="form-items" action="<?php echo url::base(); ?>test/video_extra_upload" method="post" enctype="multipart/form-data">
          <input type="hidden" name="updated" id="updated" value="0" />
          <input type="hidden" name="profile_id_video" id="profile_id_video" value="<?php echo $profile_id; ?>">

          <div class="main-row">
            <label>Video Title:</label>
            <div class="row-r">
              <input name="title" id="video_title" type="text" class="text-fi" onblur="setVideoTitle(this.value)" maxlength="50" />
            </div>
          </div>
          <div>&nbsp;</div>
          <div class="main-row">
            <div class="row">Select File :</div>
            <div class="row-r">
              <input name="file" id="video_file" type="file" />
            </div>
          </div>
          <div class="main-row">
            <div class="row">&nbsp;</div>
            <div class="row-btn">
              <input value="Upload Video File" type="button" class="save" onclick="checkForm()" />
              <input name="token" type="hidden" value="<?php echo $tokenValue; ?>"/>
            </div>
            <div id="loader-wrap"><img src="<?php echo url::base(); ?>images/loading.gif" /></div>
          </div>
        </form>
      </fieldset>
    </td>
    <td width="49%" valign="top">
      <fieldset>

        <legend align="left" >&nbsp;<img src="<?php echo url::base(); ?>images/scanlink-logo.png" width="150" /> </legend>
        <h3 class="fnt15">Provide Existing Youtube Video Detail</h3>
        <div class="form-items">
          <div class="main-row">
            <div class="row">Video Title :</div>
            <div class="row-r">
              <input type="text" maxlength="50" name="ytitle" id="ytitle" class="text-fi">
            </div>
          </div> 
          <div>&nbsp;</div>
          <div class="main-row">
            <div class="row remove-pad">Video Url :</div>
            <div class="row-r"></div>
          </div>
          <div class="main-row">
            <div class="you-txt"></div>
            <div class="row-r inpt" style="float: left;">
              <input type="text" maxlength="50" name="yurl" id="yurl" class="text-fi">
            </div>
          </div>
          <div class="main-row" style="clear: both;">
            <div class="row"></div>
            <div class="row-btn">
              <input type="button" onclick="addYoutubeVideo()" name="add_video" value="Save Video" class="save save-you-btn" />
            </div>
          </div>  
        </div>             
      </fieldset></td>
  </tr>
  <tr><td colspan="2">
      <?php if ($msg != "") { ?>
      <div class="<?php if ($err) echo 'message_error'; else echo 'message_success' ?>">
        <?php echo $msg; ?>
      </div>
      <?php } ?>

      <fieldset>
        <legend align="left" >&nbsp;<img src="<?php echo url::base(); ?>images/scanlink-logo.png" width="150" /> </legend>
        <h3>Select From Existing</h3>                
        <table cellpadding="5" cellspacing="0" width="100%">
          <?php
          $i = 0;
          foreach ($videoData as $video_item) {
            $i++;
            ?>
            <tr id="video_line<?php echo $video_item["id"]; ?>">
              <td colspan="5" class="vid-sep-td" ><div class="vid-sep">&nbsp;</div></td>
            </tr>
            <tr style="border-bottom:1px solid #000" id="video_row<?php echo $video_item["id"]; ?>">
              <td valign="top" width="5%">&nbsp;<?php echo $i; ?>.</td>
              <td valign="top" width="15%">&nbsp;&nbsp;<?php echo $video_item["title"]; ?></td>
              <td valign="top" width="15%">&nbsp;&nbsp;<?php echo "Profile # ".$video_item["profile_id"];?></td>
              <td valign="top" width="25%">
                  <?php
                  if(isset($video_item["thumbnail"]))
                  {
                  ?>
                  <img src="<?php echo $video_item["thumbnail"]; ?>" height="50" alt="" title="<?php echo $video_item["title"]; ?>" />
                  <?php } ?>
              </td>
              <td valign="top" width="20%"><div class="gray-btn"><input type="button" onclick="removeVideo('<?php echo addslashes($video_item["id"]); ?>')" name="add_video" value="Remove" class="save select-btn" /></div></td>
              <td valign="top" width="20%"><input type="button" onclick="addVideo('<?php echo addslashes($video_item["title"]); ?>','<?php echo $video_item["video_id"]; ?>')" name="add_video" value="Select" class="save select-btn" /></td>
            </tr>
          <?php } ?>
        </table>
      </fieldset></td></tr>
</table>
<script type="text/javascript">
  function setVideoTitle(val){
    parent.document.getElementById("videoTitle2").value=val;
  }

  
    
  $('#video_upload_form').submit(function(){
    if ($('#updated').val() == '0'){
      return false;
    }
  });
  

  function checkForm(){
    
    if ($('#video_title').val() == ""){
      alert("Video Title is Empty !");
      return false;
    }else if ($('#video_title').val().length < 3){
      alert("Video Title must have atleast 3 character long !");
      return false;
    }else if ($('#video_file').val().length <= 0){
      alert("Please select file");
      return false;
    }
    
    /*if($('#updated').val() == 0){
      $.ajax({
        url: '<?php echo url::base(); ?>test/getYoutubeUrl',
        data: {videoTitle: $('#video_title').val()},
        type: 'post',
        success: function (data){
          //alert (data);
          viewLoader();
          $('#video_upload_form').attr('action', data);
          $('#updated').val('1');
          //$('#video_upload_form').submit();
        },
        error: function(e){
          alert ('error :' + e);
        }
      });
      return false;
    }else{
      return true;
    }
     */
    $('#updated').val('1');
	document.getElementById("progressbar").style.display = "block";
    $('#video_upload_form').submit();
    
  }

</script>