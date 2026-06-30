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
    
    <?php echo Form::open(URL::base().'asset/index', array('id'=>'frmasset','name'=>'frmasset','method'=>'post','enctype' => 'multipart/form-data')) ?>
    <div class="penal">
      <h3 class="profile_head"> <span class="fleft">Facility Asset (<?php echo $uemail;?>)</span> <span class="fright">Profile No. <?php echo $post["edit_id"]; ?></span>
        <div class="clear"></div>
      </h3>
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
                          <td><?php echo $i; ?>) <a href="<?php echo "http://www.youtube.com/watch?v=".$video["video_name"]; ?>" target="_blank" ><?php echo $video['title']; ?></a><img src="<?php echo URL::base(); ?>images/delete2.png" height="15" width="15" alt="Remove Video" title="Remove Video" class="tooltip delete_ico" onclick="deleteVideo('<?php echo $video["id"]; ?>')" /></td>
                        </tr>
                        <?php endforeach; ?>
                      </table>
                    </fieldset>
                  </div>
                </div>
                
                <div class="main-row">
                  <div class="row">Name</div>
                  <div class="row-r"> 
                  	<div class="row-r-2"> 
							<?php echo Form::checkbox('show_name', '1', isset($post['show_name']) ? $post['show_name'] ? true : false  : false, array('class' => 'radi enable_data_collection','style'=>'box-shadow:none;'));?>
                    </div>
				  	<?php echo Form::input('txtname',$post['txtname'], array('id'=>'txtname')) ?> <?php echo Form::label('txtname', isset($errors['txtname']) ? $errors['txtname']:'', array('class'=>'error','generated'=>'true')) ?> </div>
                </div>
                
                <div class="main-row">
                  <div class="row">Address</div>
                  <div class="row-r"> 
                  	<div class="row-r-2"> 
							<?php echo Form::checkbox('show_address', '1', isset($post['show_address']) ? $post['show_address'] ? true : false  : false, array('class' => 'radi enable_data_collection','style'=>'box-shadow:none;'));?>
                    </div>
				  	<?php echo Form::input('txtaddress',$post['txtaddress'], array('id'=>'txtaddress')) ?> <?php echo Form::label('txtaddress', isset($errors['txtaddress']) ? $errors['txtaddress']:'', array('class'=>'error','generated'=>'true')) ?>
                    <div>&nbsp;</div>
                    <a href="#" id="map-link" onclick="javascript:return getMapLink()" class="view-map">View Map</a> </div>
                </div>
                
                <div class="main-row">
                  <div class="row">Telephone</div>
                  <div class="row-r">
                    <div class="row-r-2"> 
                        <?php echo Form::checkbox('show_telephone', '1', isset($post['show_telephone']) ? $post['show_telephone'] ? true : false  : false, array('class' => 'radi enable_data_collection','style'=>'box-shadow:none;'));?>
                    </div>
                    <?php echo Form::input('profile_telephone',$post['profile_telephone'], array('id'=>'profile_telephone')) ?><?php echo Form::label('profile_telephone', isset($errors['profile_telephone']) ? $errors['profile_telephone']:'', array('class'=>'error','generated'=>'true')) ?>
                  </div>
                </div>
                
                <div class="main-row">
                  <div class="row">Mobile</div>
                  <div class="row-r">
                    <div class="row-r-2"> 
                        <?php echo Form::checkbox('show_mobile', '1', isset($post['show_mobile']) ? $post['show_mobile'] ? true : false  : false, array('class' => 'radi enable_data_collection','style'=>'box-shadow:none;'));?>
                    </div>
                    <?php echo Form::input('profile_mobile',$post['profile_mobile'], array('id'=>'profile_mobile')) ?><?php echo Form::label('profile_mobile', isset($errors['profile_mobile']) ? $errors['profile_mobile']:'', array('class'=>'error','generated'=>'true')) ?>
                  </div>
                </div>
                
                <div class="main-row">
                  <div class="row">Email</div>
                  <div class="row-r">
                    <div class="row-r-2"> 
                        <?php echo Form::checkbox('show_email', '1', isset($post['show_email']) ? $post['show_email'] ? true : false  : false, array('class' => 'radi enable_data_collection','style'=>'box-shadow:none;'));?>
                    </div>
                    <?php echo Form::input('profile_email',$post['profile_email'], array('id'=>'profile_email')) ?><?php echo Form::label('profile_email', isset($errors['profile_email']) ? $errors['profile_email']:'', array('class'=>'error','generated'=>'true')) ?>
                  </div>
                </div>
                
                <div class="main-row">
                  <div class="row">Website</div>
                  <div class="row-r"> 
                    <div class="row-r-2"> 
                        <?php echo Form::checkbox('show_destination_url', '1', isset($post['show_destination_url']) ? $post['show_destination_url'] ? true : false  : false, array('class' => 'radi enable_data_collection','style'=>'box-shadow:none;'));?>
                    </div>
                    <?php echo Form::input('destination_url',$post['destination_url'], array('id'=>'destination_url')) ?><?php echo Form::label('destination_url', isset($errors['destination_url']) ? $errors['destination_url']:'', array('class'=>'error','generated'=>'true')) ?>
                 </div>
                </div>
                
                <?php /*
                <div class="main-row">
                  <div class="row">ID</div>
                  <div class="row-r"> <?php echo Form::input('txtidenty',$post['txtidenty'], array('id'=>'txtidenty')) ?> <?php echo Form::label('txtidenty', isset($errors['txtidenty']) ? $errors['txtidenty']:'', array('class'=>'error','generated'=>'true')) ?> </div>
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
                */ ?>
                <div id="picture">
                  <div class="main-row">
                    <div class="row">Upload a picture</div>
                    <div class="row-r">
                      <iframe src="<?php echo url::base(); ?>test/pictureUpload/<?php echo $post["edit_id"];?>" id="imageUpload" scrolling="no" frameborder="0" height="50" width="100%"></iframe>
                      <span class="orignalspan">File type JPG, JPEG, PNG, GIF<br>
                      </span> </div>
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
                          <td><img src="<?php echo kohana::$base_site_url.$picture["picture_name"]; ?>" height="50" width="50" /><img src="<?php echo URL::base(); ?>images/delete2.png" height="15" width="15" class="tooltip delete_ico" alt="Remove Picture"  title="Remove Picture" onclick="deletePicture('<?php echo $picture["id"]; ?>')" /></td>
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
                
                <div class="main-row" >
                  <div class="row">Upload a Document</div>
                  <div class="row-r">
                    <iframe src="<?php echo url::base(); ?>test/docUpload/<?php echo $post["edit_id"];?>" id="docUpload" scrolling="no" frameborder="0" width="100%" height="50" ></iframe>
                    <span class="orignalspan">File type DOC, DOCX, PDF, JPG, GIF, JPEG</span> </div>
                </div>
                <div id="docu"></div>
                
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
                          <td><?php echo $i; ?>) <a href="<?php echo  kohana::$base_site_url.$document["doc_name"]; ?>" ><?php echo $document["name"]; ?></a><img src="<?php echo URL::base(); ?>images/delete2.png" height="15" width="15" alt="Remove Document" title="Remove Document" class="tooltip delete_ico" onclick="deleteDocument('<?php echo $document["id"]; ?>')" /></td>
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
      <div class="penal-qr">
        <input name="save" id="save" type="submit" class="save" value="Save">
        <div class="qr-bg">&nbsp;</div>
        <img src="<?php echo isset($qrimg) &&  kohana::$base_site_url.$qrimg ?  kohana::$base_site_url.$qrimg['qrimg_name'] : '';?>" alt="" />
        <div class="btn"> <a href="<?php echo  kohana::$base_site_url;?>dashboard/getQR/<?php echo $qrimg['id'];?>">DOWNLOAD PDF</a>
        </div>
      </div>
    </div>
    <?php echo Form::close() ?> </div>
</div>
<div id="progressbar"> <img src="<?php echo url::base(); ?>images/spinner_gray.gif" align="absmiddle" /> Please Wait. Saving Profile... </div>
<script type="text/javascript">
$(document).ready(function(){
	jQuery(".iframe").colorbox({
		iframe:true, 
		width:"750", 
		height:"400", 
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
	
	
	$("#add_more_picture").unbind().bind('click',function(event){
		event.preventDefault();
		
		var new_main_row = document.createElement("div");
		new_main_row.className ="main-row";
		
		var new_row = document.createElement("div");
		new_row.className ="row";
		new_row.innerHTML="Upload a Picture";
		var new_row_r = document.createElement("div");
		new_row_r.className ="row-r";
		
		var new_input_file = document.createElement("input");
		new_input_file.name="picture_file[]";
		new_input_file.type="file";
		
		
		var new_rem_img = document.createElement("img");
		new_rem_img.src = "<?php echo URL::base(); ?>images/delete-icon.png";
		new_rem_img.className = "picture_input_delete";
		
		
		new_row_r.appendChild(new_input_file);
		new_row_r.appendChild(new_rem_img);
		
		new_main_row.appendChild(new_row);
		new_main_row.appendChild(new_row_r);
		
		document.getElementById("picture").appendChild(new_main_row);
	});
	
	
	$("#picture .main-row .row-r .picture_input_delete").live('click',function(){
		$(this).parent().parent().remove();
	});
	
	$("#docu .main-row .row-r .doc_input_delete").live('click',function(){
		$(this).parent().parent().parent().parent().parent().remove();
	});
	
	
	
	$("#frmasset").validate({
		rules: {
			logo_file: { accept: 'png|jpg|jpeg|gif' },
			txtname: "required"
		},
		errorElement: "label",
		messages: {
			logo_file: { accept: "Only png jpg jpeg or gif file is allowed" },
			txtname: "Please provide name for Facility"
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
});


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

var mymap=null;
function getMapLink(){
	
	if(document.getElementById("txtaddress").value!=""){

		var maplink="http://maps.google.com?q="+document.getElementById("txtaddress").value;
		if(mymap==null || mymap.closed){
		mymap = window.open(''+maplink,'mywin', 'left=20,top=20,width=500,height=500,toolbar=1,resizable=0,location=0,status=0');
		}

		return true;
	}else{
		alert("please Provide Address...");
		return false;
	}
	return false;
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
