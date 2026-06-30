<style type="text/css">
td span {
	position:static !important;
	float:none !important;
	clear:both !important;
	margin-left:10px !important;
}
</style>
<div class="container">
  <div class="contant inner-box">
    <div class="penal">
      <h3 class="profile_head"> <span class="fleft">Plant & Equipment (<?php echo $uemail;?>)</span> <span class="fright">Profile No. <?php echo $post["edit_id"]; ?></span>
        <div class="clear"></div>
      </h3>
      <?php echo Form::open(URL::base().'plant/index', array('id'=>'frmplant','name'=>'frmplant','method'=>'post','enctype' => 'multipart/form-data')) ?>
      <div class="penal-tab">
        <div class="TabbedPanels">
          <div class="TabbedPanelsContentGroup">
            <div class="TabbedPanelsContent">
              <div class="fild-outer">
                <div class="main-row">
                  <div class="row">Upload Company logo</div>
                  <div class="row-r"> <?php echo Form::input('edit_id',$post["edit_id"], array('type'=>'hidden')) ?>
                    <iframe src="<?php echo url::base(); ?>test/logoUpload/<?php echo $post["edit_id"];?>" id="logoUpload" scrolling="no" frameborder="0" height="50" width="100%"></iframe>
                    <span class="orignalspan">File type JPG, JPEG, PNG, GIF</span>
                    <div class="clear"></div>
                    <?php if(isset($post["logo"]) && isset($post["logo"]["logo_name"]) && $post["logo"]["logo_name"]!=""){ ?>
                    <img src="<?php echo kohana::$base_site_url.$post["logo"]["logo_name"]; ?>" height="30" alt="<?php echo $post['txtname'] ?>" style="margin:5px" />
                    <?php } ?>
                  </div>
                </div>
                <div class="main-row">
                  <div class="row">Upload a Video</div>
                  <div class="row-r"> <a href="<?php echo url::base()."test/index/".$post["user_id"]; ?>" class="iframe" >Upload Video &nbsp;<img src="<?php echo url::base()."images/upload-video-ico.png"; ?>" width="20" height="20" /></a>
                    <input type="hidden" name="video" id="video" value="<?php echo isset($post['video']) ? $post['video']:'' ?>" />
                    <input type="hidden" name="videoTitle" id="videoTitle" value="" />
                  </div>
                </div>
                <div class="main-row">
                  <div class="row">Current Videos</div>
                  <div class="row-r">
                    <fieldset>
                      <table border="0" cellpadding="0" cellspacing="5" id="videos_display" >
                        <?php if(count($post["videos"])==0): ?>
                        <tr>
                          <td align="center">No Videos !</td>
                        </tr>
                        <?php endif; ?>
                        <?php $i=0; $num=0;foreach($post["videos"] as $video): $i++; ?>
                        <tr>
                          <td><?php echo $i; ?>) <a href="<?php echo "http://www.youtube.com/watch?v=".$video["video_name"]; ?>" target="_blank" ><?php echo $video['title']; ?></a><img src="<?php echo URL::base(); ?>images/delete2.png" height="15" width="15" alt="Remove Video" title="Remove Video" class="tooltip delete_ico" onClick="deleteVideo('<?php echo $video["id"]; ?>')" /></td>
                        </tr>
                        <?php endforeach; ?>
                      </table>
                    </fieldset>
                  </div>
                </div>
                <div class="main-row">
                  <div class="row">Make / Model</div>
                  <div class="row-r"> <?php echo Form::input('txtname',isset($post['txtname']) ? $post['txtname']:'', array('id'=>'txtname')) ?> <?php echo Form::label('txtname', isset($errors['txtname']) ? $errors['txtname']:'', array('class'=>'error','generated'=>'true')) ?> </div>
                </div>
                <div class="main-row">
                  <div class="row">Identification</div>
                  <div class="row-r"> <?php echo Form::input('txtidenty',isset($post['txtidenty']) ? $post['txtidenty']:'', array('id'=>'txtidenty')) ?> <?php echo Form::label('txtidenty', isset($errors['txtidenty']) ? $errors['txtidenty']:'', array('class'=>'error','generated'=>'true')) ?> </div>
                </div>
                <div class="main-row">
                  <div class="row">Serial No.</div>
                  <div class="row-r"> <?php echo Form::input('txtsrno',isset($post['txtsrno']) ? $post['txtsrno']:'', array('id'=>'txtsrno')) ?> <?php echo Form::label('txtsrno', isset($errors['txtsrno']) ? $errors['txtsrno']:'', array('class'=>'error','generated'=>'true')) ?> </div>
                </div>
                <div class="main-row">
                  <div class="row">Description</div>
                  <div class="row-r"> <?php echo Form::textarea('txtdesc', isset($post['txtdesc']) ? $post['txtdesc']:'', array("rows"=>1, "cols"=>40, "class"=>"input")); ?> <?php echo Form::label('txtdesc', isset($errors['txtdesc']) ? $errors['txtdesc']:'', array('class'=>'error','generated'=>'true')) ?> </div>
                </div>
                <div class="main-row">
                  <div class="row">Notes</div>
                  <div class="row-r"> <?php echo Form::textarea('txtnotes', isset($post['txtnotes']) ? $post['txtnotes']:'', array("rows"=>1, "cols"=>40,"class"=>"input")); ?> <?php echo Form::label('txtnotes', isset($errors['txtnotes']) ? $errors['txtnotes']:'', array('class'=>'error','generated'=>'true')) ?> </div>
                </div>
                <?php echo $contact_data; ?>
                <div id="picture">
                  <div class="main-row">
                    <div class="row">Upload a picture</div>
                    <div class="row-r">
                      <iframe src="<?php echo url::base(); ?>test/pictureUpload/<?php echo $post["edit_id"];?>" id="imageUpload" scrolling="no" frameborder="0" height="50" width="100%"></iframe>
                      <span class="orignalspan">File type JPG, JPEG, PNG, GIF</span> </div>
                    <div class="clear"></div>
                  </div>
                </div>
                <div class="main-row">
                  <div class="row">Current Image</div>
                  <div class="row-r">
                    <fieldset>
                      <table border="0" cellpadding="0" cellspacing="5" id="pictures_display" >
                        <tr>
                          <?php if(count($post["pictures"])==0): ?>
                          <td align="center">No Images !</td>
                          <?php endif; ?>
                          <?php $i=0;foreach($post["pictures"] as $picture): $i++; ?>
                          <td><img src="<?php echo kohana::$base_site_url.$picture["picture_name"]; ?>" height="50" width="50" /><img src="<?php echo URL::base(); ?>images/delete2.png" height="15" width="15" class="tooltip delete_ico" alt="Remove Picture"  title="Remove Picture" onClick="deletePicture('<?php echo $picture["id"]; ?>')" /></td>
                          <?php  if($i==5): $i=0; ?>
                        </tr>
                        <tr>
                          <?php endif; ?>
                          <?php endforeach; ?>
                        </tr>
                      </table>
                    </fieldset>
                  </div>
                </div>
                <div id="docu">
                  <div class="main-row">
                    <div class="row">Upload a Document</div>
                    <div class="row-r">
                      <iframe src="<?php echo url::base(); ?>test/docUpload/<?php echo $post["edit_id"];?>" id="docUpload" scrolling="no" frameborder="0" height="50" width="100%"></iframe>
                      <span class="orignalspan">File type DOC, DOCX, PDF, JPG, GIF, JPEG</span>
                      <div class="clear"></div>
                    </div>
                  </div>
                </div>
                <div class="main-row">
                  <div class="row">Current Documents</div>
                  <div class="row-r">
                    <fieldset>
                      <table border="0" cellpadding="0" cellspacing="5" id="documents_display" >
                        <?php if(count($post["documents"])==0): ?>
                        <tr>
                          <td align="center">No Documents !</td>
                        </tr>
                        <?php endif; ?>
                        <?php $i=0; $num=0;foreach($post["documents"] as $document): $i++; ?>
                        <tr>
                          <td><?php echo $i; ?>) <a href="<?php echo kohana::$base_site_url.$document["doc_name"]; ?>" ><?php echo $document["name"]; ?></a><img src="<?php echo URL::base(); ?>images/delete2.png" height="15" width="15" alt="Remove Document" title="Remove Document" class="tooltip delete_ico" onClick="deleteDocument('<?php echo $document["id"]; ?>')" /></td>
                        </tr>
                        <?php endforeach; ?>
                      </table>
                    </fieldset>
                  </div>
                </div>
                <?php echo $web_link_data; ?>
                <div class="main-row">
                  <div class="row padbot">Data Collection</div>
                  <div class="row-r">
                    <label class="lineheight extraWidth">Enabel data collection : </label>
                    <div class="radiwrap"> <?php echo Form::radio('enable_data_collection', '1', (($post["enable_data_collection"] == 1) ? true : false), array('class' => 'radi enable_data_collection')) ?> <?php echo Form::label('enable_data_collection', 'Yes', array('style' => 'display: inline;')) ?> <?php echo Form::radio('enable_data_collection', '0', (($post["enable_data_collection"] == 0) ? true : false), array('class' => 'radi enable_data_collection')) ?> <?php echo Form::label('enable_data_collection', 'No', array('style' => 'display: inline;')) ?> </div>
                  </div>
                </div>
                <div class="main-row">
                  <div class="row">&nbsp;</div>
                  <div class="row-r-2">
                    <?php
					  echo Form::checkbox(
							  'set_up_compulsory', '1', isset($post['set_up_compulsory']) ? $post['set_up_compulsory'] ? true : false  : false, array('class' => 'radi enable_data_collection'
							  )
					  );
					  ?>
                    <?php
					  echo Form::label(
							  'set_up_compulsory', 'Set as compulsory', array(
						  'style' => 'display: inline;'
							  )
					  );
					  ?>
                  </div>
                </div>
                <div class="main-row">
                  <div class="row">&nbsp;</div>
                  <div class="row-r-2">
                    <div class="dcChe"> <?php echo Form::checkbox('data_collection_mobile', '1', (bool) $post["data_collection_mobile"], array('id' => 'data_collection_mobile')) ?> <?php echo Form::label('data_collection_mobile', 'Mobile', array('style' => 'display: inline;')) ?> </div>
                    <div class="dcChe"> <?php echo Form::checkbox('data_collection_email', '1', (bool) $post["data_collection_email"], array('id' => 'data_collection_email')) ?> <?php echo Form::label('data_collection_email', 'Email', array('style' => 'display: inline;')) ?> </div>
                    <div class="dcChe"> <?php echo Form::checkbox('data_collection_name', '1', (bool) $post["data_collection_name"], array('id' => 'data_collection_name')) ?> <?php echo Form::label('data_collection_name', 'Name', array('style' => 'display: inline;')) ?> </div>
                  </div>
                </div>
                <div class="main-row padtop">
                  <div class="row padtop">&nbsp;</div>
                  <div class="row-r padtop"> <?php echo Form::label('data_collection_content', 'Content') ?> <?php echo Form::textarea('data_collection_content', $post["data_collection_content"], array('id' => 'data_collection_content','class'=>'input','rows'=>'1','cols'=>'40','style'=>'height:20px;')); ?> </div>
                </div>
                <div class="main-row">
                  <div class="row padbot">Set Code Type</div>
                  <div class="row-r">
                    <label class="lineheight extraWidth">Code Type:</label>
                    <div class="radiwrap"> <?php echo Form::radio('code_type', '0', (($post["code_type"] == 0) ? true : false), array('class' => 'radi')) ?> <?php echo Form::label('code_type_qr', 'QR Code', array('style' => 'display: inline;','class'=>'extraWidth')) ?> <?php echo Form::radio('code_type', '1', (($post["code_type"] == 1) ? true : false), array('class' => 'radi')) ?> <?php echo Form::label('code_type_dm', 'Data Matrix', array('style' => 'display: inline;','class'=>'extraWidth')) ?> </div>
                  </div>
                </div>
                <div class="main-row">
                  <div class="row">Header</div>
                  <div class="row-r-2"> <?php echo Form::checkbox('show_header', '1', (bool) $post['show_header'], array('id' => 'show_header')) ?> <?php echo Form::label('show_header', 'Show header in mobile', array('style' => 'display: inline;')) ?> </div>
                </div>
                <div class="main-row">
                  <div class="row">Password protect ?</div>
                  <div class="row-r">
                    <div class="radiwrap"> <?php echo Form::radio('protect','1', $yes,array('class'=>'radi')) ?><?php echo Form::label('protectyes', 'Yes') ?> <?php echo Form::radio('protect','0', $no,array('class'=>'radi')) ?><?php echo Form::label('protectno', 'No') ?> </div>
                  </div>
                </div>
                <div class="main-row">
                  <div class="row">password</div>
                  <div class="row-r"> <?php echo Form::input('password', isset($post['password']) ? $post['password'] : '', array('id' => 'password', 'class' => 'text-fi')) ?> <?php echo Form::label('password', isset($errors['password']) ? $errors['password'] : '', array('class' => 'error', 'generated' => 'true')) ?> </div>
                </div>
                <div class="main-row">
                  <div class="row">Share</div>
                  <div class="row-r-2">
                    <?php
					  echo Form::checkbox(
							  'display_share_link', '1', isset($post['display_share_link']) ? $post['display_share_link'] ? true : false  : false, array('class' => 'radi enable_data_collection'
							  )
					  );
					  ?>
                    <?php
					  echo Form::label(
							  'display_share_link', 'Display share links', array(
						  'style' => 'display: inline;'
							  )
					  );
					  ?>
                  </div>
                </div>
              </div>
              <div class="clear"></div>
            </div>
          </div>
        </div>
      </div>
      <div class="penal-qr"> <?php echo Form::submit(NULL, 'Save', array('id'=>'save','class'=>'save')) ?>
        <div class="qr-bg"> </div>
        <img src="<?php echo isset($qrimg) && kohana::$base_site_url.$qrimg ? kohana::$base_site_url.$qrimg['qrimg_name'] : '';?>" alt="" />
        <div class="btn"> <a href="<?php echo kohana::$base_site_url;?>dashboard/getQR/<?php echo $qrimg['id'];?>">DOWNLOAD PDF</a> </div>
      </div>
      <?php echo Form::close() ?> </div>
  </div>
</div>
<div id="progressbar"> <img src="<?php echo url::base(); ?>images/spinner_gray.gif" align="absmiddle" /> Please Wait. Saving Profile... </div>
<script type="text/javascript">
$(document).ready(function(){
	jQuery(".iframe").colorbox({
		iframe:true, 
		width:"750",
		height:"600", 
		onClosed:function(){
//			parent.location.reload()
		}
	});
});
</script>
<script type="text/javascript">
$(document).ready(function(){	
	$("#add_more_docu").unbind().bind('click',function(event){
		event.preventDefault();
		
		var new_main_row = document.createElement("tr");
		new_main_row.className ="main-row";
		
		var new_row = document.createElement("div");
		new_row.className ="row";
		new_row.innerHTML="Upload a Document";
		
		var new_row_r = document.createElement("div");
		new_row_r.className ="row-r";
		
		var new_row_table = document.createElement("table");
		new_row_table.cellPadding=0;
		new_row_table.cellSpacing=0;
		new_row_table.border=0;
		new_row_table.width="100%";
		
		var new_row_table_tr = document.createElement("tr");

		var new_input_name = document.createElement("input");
		new_input_name.id="docu_name_"+1;
		new_input_name.name="docu_name[]";
		new_input_name.type="text";
		
		var new_row_table_tr_td1 = document.createElement("td");		
		new_row_table_tr_td1.appendChild(new_input_name);
		
		var new_input_file = document.createElement("input");
		new_input_file.id="docu_file_"+1;
		new_input_file.name="docu_file[]";
		new_input_file.type="file";
		new_input_file.generated="true";
				
		var new_row_table_tr_td2 = document.createElement("td");
		new_row_table_tr_td2.appendChild(new_input_file);
		
		var new_rem_img = document.createElement("img");
		new_rem_img.src = "<?php echo URL::base(); ?>images/delete-icon.png";
		new_rem_img.className = "doc_input_delete";
		
		var new_row_table_tr_td3 = document.createElement("td");
		new_row_table_tr_td3.appendChild(new_rem_img);
		
		new_row_table_tr.appendChild(new_row_table_tr_td1);
		new_row_table_tr.appendChild(new_row_table_tr_td2);
		new_row_table_tr.appendChild(new_row_table_tr_td3);
		
		new_row_table.appendChild(new_row_table_tr);
		
		new_row_r.appendChild(new_row_table);
		
		new_main_row.appendChild(new_row);
		new_main_row.appendChild(new_row_r);
		
		document.getElementById("docu").appendChild(new_main_row);
	});
	
	$("#picture .main-row .row-r .picture_input_delete").live('click',function(){
		$(this).parent().parent().remove();
	});
	
	$("#docu .main-row .row-r .doc_input_delete").live('click',function(){
		$(this).parent().parent().parent().parent().parent().remove();
	});
	
	$("#frmplant").validate({
		rules: {
			logo_file: { accept: 'png|jpg|jpeg|gif' },
			txtname: "required"
		},
		/*errorPlacement: function(error, element) {			
		   	error.appendTo(element.parent().next());
    	},*/
		messages: {
			logo_file: { accept: "Only png jpg jpeg or gif file is allowed" },
			txtname: "Please provide name for Model"
		},
		submitHandler: function(form) {
			var fileArray = document.getElementsByName('docu_file[]');
			var txtArray = document.getElementsByName('docu_name[]');
			for(var i=0;i<fileArray.length;i++){
				if(fileArray[i].value.split(" ").join("") != "" && txtArray[i].value.split(" ").join("") == ""){
					alert("Please enter document name for uploaded document.");
					txtArray[i].focus();
					return false;
				}
			}			
			document.getElementById("progressbar").style.display = "block";
			form.submit();
			return true;
		}
	});
<?php if(!empty($checklist_items)){ ?>
		<?php foreach($checklist_items as $checklist_item){ ?>
		addNewChecklistItem('<?php echo $checklist_item['checklist_item'] ?>','<?php echo $checklist_item['id'] ?>');
		<?php } ?>
	<?php } ?>
});

function addNewChecklistItem(text,id){
	var cnt = $(".checklist_item").length;
	
	var new_input_checklist_item = document.createElement("input");
	new_input_checklist_item.id="checklist_item_"+(cnt+1);
	new_input_checklist_item.name="checklist_item[]";
	new_input_checklist_item.type="text";
	new_input_checklist_item.maxlength="50";
	new_input_checklist_item.size="50";
	new_input_checklist_item.value=text;
	new_input_checklist_item.className="checklist_item";
	if(id==0){
		$(new_input_checklist_item).attr("generated","true");
	}else{
		var new_input_checklist_item_id = document.createElement("input");
		new_input_checklist_item_id.type = "hidden";
		new_input_checklist_item_id.className = "hidden_checklist_item_id";
		new_input_checklist_item_id.name="checklist_item_id[]";
		new_input_checklist_item_id.value = id;
		document.getElementById("checklist_item_container").appendChild(new_input_checklist_item_id);	
		$(new_input_checklist_item).attr("generated",id);
	}
	
	var new_tr = document.createElement("tr");
	var new_td = document.createElement("td");
	new_td.appendChild(new_input_checklist_item);
	new_tr.appendChild(new_td);
	
	var new_delete_img = document.createElement("img");
	new_delete_img.src = "<?php echo URL::base(); ?>images/delete2.png";
	new_delete_img.height = "15";
	new_delete_img.width = "15";
	new_delete_img.alt = "Remove Checklist Item";
	new_delete_img.title = "Remove Checklist Item";
	new_delete_img.className = "tooltip delete_ico";
	
	if(new_delete_img.addEventListener) {
     	new_delete_img.addEventListener('click', function(){ removeChecklistItem(new_delete_img); }, false);
   	} else {
     	new_delete_img.attachEvent('onclick', function(){ removeChecklistItem(new_delete_img); });
   	}
	
	var new_td = document.createElement("td");
	new_td.appendChild(new_delete_img);
	new_tr.appendChild(new_td);
	
	document.getElementById("checklist_item_container").appendChild(new_tr);
	
}

function removeChecklistItem(ele){
	if($(ele).parent().prev().children().attr('generated')!="true"){
		if(confirm("Are you sure to remove this checklist Item ? ")){
			$.getJSON('<?php echo URL::base(); ?>plant/delete_checklist_item/'+$(ele).parent().prev().children().attr('generated'), function(data) {		

			alert("Checklist Item is Deleted...");
			$(".hidden_checklist_item_id").each(function(){
				if($(this).val()==data.id){
					$(this).remove();
				}
			});
				ele.parentNode.parentNode.parentNode.removeChild(ele.parentNode.parentNode);
				
			});
		}	
	}else{
	ele.parentNode.parentNode.parentNode.removeChild(ele.parentNode.parentNode);
	}	
}


$(function() {
	  $('textarea').autogrow();
});

function deletePicture(picture_id){
	if(confirm("Are you sure to remove this Picture ? ")){
		$.getJSON('<?php echo URL::base(); ?>plant/delete_picture/'+picture_id, function(data) { 
			var str="";
			str+="<tr>";
			var j=0;
			
			$('#pictures_display').empty();
			
			if(data.length==0){
				$('#pictures_display').append('<td align="center">No Images !</td>');
			}
			
			for(var i=0;i<data.length;i++){
				j++;
				str+='<td>'+
				'	  <img src="<?php echo  kohana::$base_site_url;?>'+data[i].picture_name+'" height="50" />'+
				'	  <img src="<?php echo URL::base(); ?>images/delete2.png" height="15" width="15" class="delete_ico" onclick="deletePicture(\''+data[i].id+'\')" />'+
				'</td>';
				
				if(j==5){
					j=0;
					str+="</tr><tr>";
				}
			}
			str+="</tr>";
			$('#pictures_display').append(str);
		});
	}
}

function deleteDocument(document_id){
	if(confirm("Are you sure to remove this Document ? ")){
		$.getJSON('<?php echo URL::base(); ?>plant/delete_document/'+document_id, function(data) { 
			var str="";
			var j=0;
			$('#documents_display').empty();
			
			if(data.length==0){
				$('#documents_display').append('<tr><td align="center">No Documents !</td><tr/>');
			}
			
			for(var i=0;i<data.length;i++){
				j++;
				str+='<tr><td>'+
				'	  '+j+') <a src="<?php echo  kohana::$base_site_url;?>'+data[i].doc_name+'" height="50" />'+data[i].name+'</a>'+
				'	  <img src="<?php echo URL::base(); ?>images/delete2.png" height="15" width="15" class="delete_ico" onclick="deleteDocument(\''+data[i].id+'\')" />'+
				'</td></tr>';
			}
			$('#documents_display').append(str);
		});
	}
}

function deleteVideo(video_id){
	if(confirm("Are you sure to remove this Video ? ")){
		var copyProgress = $("#progressbar").clone();
		copyProgress.attr('id','progressbar1');
		copyProgress.html('<img align="absmiddle" src="<?php echo URL::base();?>images/spinner_gray.gif"> Please Wait. Deleting Video...');
		$("#progressbar").after(copyProgress);
		$("#progressbar1").show();
		$.getJSON('<?php echo URL::base(); ?>plant/delete_video/'+video_id, function(data) {
			$("#progressbar1").remove();
			var str="";
			var j=0;
			$('#videos_display').empty();
			
			if(data.length==0){
				$('#videos_display').append('<tr><td align="center">No Video !</td><tr/>');
			}
			
			for(var i=0;i<data.length;i++){
				j++;
				str+='<tr><td>'+
				'	  '+j+') <a src="http://www.youtube.com/watch?v='+data[i].video_name+'" target="_blank" height="50" />'+data[i].title+'</a>'+
				'	  <img src="<?php echo URL::base(); ?>images/delete2.png" height="15" width="15" class="delete_ico" onclick="deleteVideo(\''+data[i].id+'\')" />'+
				'</td></tr>';
			}
			$('#videos_display').append(str);
		});
	}
}



</script>
