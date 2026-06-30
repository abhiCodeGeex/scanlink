<script type="text/javascript">
  function checkForFile() {
    if (document.getElementById('file').value) {
      return true;
    }
    document.getElementById('errMsg').style.display = '';
    return false;
  }  
  
  function addVideo(title,video_id){
	  	parent.document.getElementById("video").value+=title+'='+video_id+",";
		alert("Video is Selected...");
		parent.$.colorbox.close();
  }
  function addYoutubeVideo(){
	  	var ytitle = document.getElementById("ytitle").value;
		var yurl = document.getElementById("yurl").value;
		if(ytitle !='' && yurl != ''){
			parent.document.getElementById("video").value+=ytitle+'='+yurl+",";
			alert("Video is Saved...");
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
#container_wrap{ background-color:#EEE !important; background-image:none !important; -webkit-border-radius: 5px;-moz-border-radius: 5px;border-radius: 5px; min-height:310px; }
.main-row .row { width:85px; }
.main-row .row-r { width:200px; }
.main-row .row-btn{ width:150px;float: left; margin: 0; padding: 3px; }
.main-row .row-btn .save{ font-size:14px; }
table td{ font-size:12px; }
.save{ font-size:12px; }
.select-btn{ width:50px; }
fieldset h3{ color:#0F63EB; font-size:16px; font-weight:700; padding-left:5px; }
.vid-sep-td{ line-height:5px; }
.vid-sep{ border-bottom:1px dotted #CCC; margin-bottom:5px; } 
fieldset{ border:1px solid #CCC; position:relative; padding:0px; -webkit-border-radius: 5px;-moz-border-radius: 5px;border-radius: 5px; margin:5px;}
legend{ margin-left:10px; }
#loader-wrap{float: left;padding-top: 6px;display:none;}
.fnt15{ font-size:15px;}
.save-you-btn{ width:84px; }
.remove-pad{ padding:0 5px !important;}
.row-r.inpt{ width:90px; }
.you-txt{float: left; line-height: 38px;}
</style>
<table cellpadding="2" cellspacing="0" width="100%">
	<tr>
    	<td width="49%" valign="top">
			<fieldset>
                <legend align="left" >&nbsp;<img src="<?php echo url::base(); ?>images/scanlink-logo.png" width="150" /> </legend>
                <?php if($msg!=""){ ?><div style="padding-left:15px;"><?php echo $msg; ?></div><?php } ?>
                <h3>Upload New</h3>
                <p>&nbsp;</p>
                <form action="<?php echo $postUrl.'?nexturl='.$nextUrl; ?>" method="post" enctype="multipart/form-data" onsubmit="return checkForm(this)">
                  <div class="main-row">
                    <div class="row">Video Title :</div>
                    <div class="row-r">
                      <input name="title" type="text" onblur="setVideoTitle(this.value)" maxlength="50" />
                    </div>
                  </div>
                  <div class="main-row">
                    <div class="row">Select File :</div>
                    <div class="row-r">
                      <input name="file" type="file"/>
                    </div>
                  </div>
                  <div class="main-row">
                    <div class="row">&nbsp;</div>
                    <div class="row-btn">
                      <input value="Upload Video File" type="submit" class="save" onclick="viewLoader();" />
                      <input name="token" type="hidden" value="<?php echo $tokenValue; ?>"/>
                    </div>
                    <div id="loader-wrap"><img src="<?php echo url::base(); ?>images/loading.gif" /></div>
                  </div>
                </form>
              </fieldset>
		</td>
        <td>&nbsp;</td>
    	<td width="49%" valign="top">
        	<fieldset>
                    <legend align="left" >&nbsp;<img src="<?php echo url::base(); ?>images/scanlink-logo.png" width="150" /> </legend>
                    <h3 class="fnt15">Provide Existing Youtube Video Detail</h3>
                    <div class="main-row">
                        <div class="row">Video Title :</div>
                        <div class="row-r">
                          <input type="text" maxlength="50" name="ytitle" id="ytitle">
                        </div>
                  	</div> 
                    <div class="main-row">
                        <div class="row remove-pad">Video Url :</div>
                        <div class="row-r"></div>
                  	</div>
                    <div class="main-row">
                        <div class="you-txt">http://www.youtube.com/watch?v=</div>
                        <div class="row-r inpt">
                          <input type="text" maxlength="50" name="yurl" id="yurl">
                        </div>
                  	</div>
                    <div class="main-row">
                        <div class="row"></div>
                        <div class="row-btn">
                          <input type="button" onclick="addYoutubeVideo()" name="add_video" value="Save Video" class="save save-you-btn" />
                        </div>
                  	</div>                                  
			</fieldset>
        	<fieldset>
                    <legend align="left" >&nbsp;<img src="<?php echo url::base(); ?>images/scanlink-logo.png" width="150" /> </legend>
                    <h3>Select From Existing</h3>                
                    <table cellpadding="5" cellspacing="0" width="100%">
                    	<?php $i=0; foreach($videoData as $video_item){ $i++; ?>
                        <tr>
                        	<td colspan="4" class="vid-sep-td" ><div class="vid-sep">&nbsp;</div></td>
                        </tr>
                        <tr style="border-bottom:1px solid #000">
                        	<td valign="top" width="5%">&nbsp;<?php echo $i; ?>.</td>
                            <td valign="top" width="50%">&nbsp;&nbsp;<?php echo $video_item["title"]; ?></td>
                            <td valign="top" width="25%"><img src="<?php echo $video_item["thumbnail"]; ?>" height="50" alt="" title="<?php echo $video_item["title"]; ?>" /></td>
                            <td valign="top" width="20%"><input type="button" onclick="addVideo('<?php echo addslashes($video_item["title"]); ?>','<?php echo $video_item["video_id"]; ?>')" name="add_video" value="Select" class="save select-btn" /></td>
                        </tr>
                        <?php } ?>
                    </table>
			</fieldset>
		</td>
	</tr>
</table>
<script type="text/javascript">
function setVideoTitle(val){
	parent.document.getElementById("videoTitle").value=val;
}

function checkForm(frm){
	if(frm.title.value==""){
		alert("Video Title is Empty !");
		return false;
	}else if((frm.title.value).length<3){
		alert("Video Title must have atleast 3 character long !");
		return false;
	}
}

</script>