<style type="text/css">
	td span{ position:static !important; float:none !important; clear:both !important; margin-left:10px !important; }
</style>
<div class="container">
  <div class="contant inner-box">
    <?php /*?><div class="inner-menu"> <?php echo $innermenu;?> </div><?php */?>
	<?php echo Form::open(URL::base().'people/index', array('id'=>'frmpeople','name'=>'frmpeople','method'=>'post','enctype' => 'multipart/form-data')) ?>
    <input type="hidden" id="userid" name="userid" value="<?php echo $userid;?>" />
    <div class="penal">
    	<h3>People (<?php echo $uemail;?>)</h3>
        <div class="penal-tab">
          <div class="TabbedPanels">
            <div class="TabbedPanelsContentGroup">
              <div class="TabbedPanelsContent">
                <div class="fild-outer">
                  <div class="main-row">
                    <div class="row">Upload Company logo</div>
                    <div class="row-r">
                      <?php /*?><?php echo Form::input('logo_file','', array('id'=>'logo_file','type'=>'file')) ?>	
                     <?php echo Form::label('logo_file', isset($errors['logo_file']) ? $errors['logo_file']:'', array('class'=>'error','generated'=>'true')) ?><?php */?>
                     <iframe src="<?php echo url::base(); ?>test/logoUploadAdd/<?php echo $userid;?>" id="logoUpload" scrolling="no" frameborder="0" width="100%" height="50"></iframe>
							<input type="hidden" id="uploaded" name="uploaded" value="" />
                    	<span>File type JPG, JPEG, PNG, GIF</span>
					</div>
                  </div>
                  <div class="main-row">
                    <div class="row">Position</div>
                    <div class="row-r">
                      <?php echo Form::input('txtposition','', array('id'=>'txtposition')) ?>
					<?php echo Form::label('txtposition', isset($errors['txtposition']) ? $errors['txtposition']:'', array('class'=>'error','generated'=>'true')) ?>
                    </div>
                  </div>
                  <div class="main-row">
                    <div class="row">Name</div>
                    <div class="row-r">
                      <?php echo Form::input('txtname','', array('id'=>'txtname')) ?>
					<?php echo Form::label('txtname', isset($errors['txtname']) ? $errors['txtname']:'', array('class'=>'error','generated'=>'true')) ?>
                    </div>
                  </div>
                  <div class="main-row">
                    <div class="row">Notes</div>
                    <div class="row-r">
   	                  <?php echo Form::textarea('txtnotes', isset($post['txtnotes']) ? $post['txtnotes']:'', array("rows"=>1, "cols"=>40,"class"=>"input")); ?>
                      <?php echo Form::label('txtnotes', isset($errors['txtnotes']) ? $errors['txtnotes']:'', array('class'=>'error','generated'=>'true')) ?>
                    </div>
                  </div>
                  <div class="main-row padbot">
                    <div class="row padbot padtop2">&nbsp;</div>
                    <div class="row-r padbot">
                       <?php echo Form::label('comp', 'CONTACT') ?>
                       <?php echo Form::label('tele', 'TELEPHONE',array('class'=>'pad')) ?>
                    </div>
                  </div>
                  <div class="main-row padtop">
                    <div class="row padtop">&nbsp;</div>
                    <div class="row-r padtop">
                       <?php echo Form::input('txtcompname','', array('id'=>'txtcompname','class'=>'input-w')) ?>
                      <?php echo Form::input('txttelephone','', array('id'=>'txttelephone','class'=>'input-w')) ?>	                      	
                       <?php echo Form::label('txtcompname', isset($errors['txtcompname']) ? $errors['txtcompname']:'', array('id'=>'errcomp','class'=>'error','generated'=>'true')) ?>
                      <?php echo Form::label('txttelephone', isset($errors['txttelephone']) ? $errors['txttelephone']:'', array('id'=>'errtele','class'=>'error','generated'=>'true')) ?>
                   </div>
                  </div>
                  <div id="picture">
                      <div class="main-row">
                        <div class="row">Upload a picture</div>
                        <div class="row-r">
                          <?php /*?><?php echo Form::input('picture_file[]','', array('type'=>'file')) ?>	
                          <?php echo Form::label('picture_file[]', isset($errors['picture_file']) ? $errors['picture_file']:'', array('class'=>'error','generated'=>'true')) ?><?php */?>
                          <?php  //echo Form::button('add_more_picture','Add More...', array('id'=>'add_more_picture','type'=>'button')) ?>
                          <iframe src="<?php echo url::base(); ?>test/pictureUploadAdd/<?php echo $userid;?>" id="imageUpload" scrolling="no" frameborder="0" height="50" width="100%"></iframe>
                            <span>File type JPG, JPEG, PNG, GIF</span>
						</div>
                      </div>
                  </div>
					<div class="main-row" >
                    <div class="row">Upload a Document</div>
                    <div class="row-r">
                        <?php /*?><table cellpadding="0" cellspacing="0" border="0">
                             <tr>
                                <td align="left"><span>Document Name</span></td><td align="left"><span>Document</span></td>
                            </tr>
                            <tr>
                                <td><?php echo Form::input('docu_name[]','', array('type'=>'text')) ?></td>
                                <td><?php echo Form::input('docu_file[]','', array('type'=>'file')) ?></td>
                             </tr></table>
                      <?php echo Form::label('docu_file[]', isset($errors['docu_file']) ? $errors['docu_file']:'', array('class'=>'error','generated'=>'true')) ?>
                      <?php echo Form::button('add_more_docu','Add More...', array('id'=>'add_more_docu','type'=>'button')) ?><?php */?>
                      <?php /*?><img src="<?php echo URL::base();?>images/addmore.png" id="addmore" style="cursor:pointer;" />	<?php */?>
                      <iframe src="<?php echo url::base(); ?>test/docUploadAdd/<?php echo $userid;?>" id="docUpload" scrolling="no" frameborder="0" height="50" width="100%"></iframe>
                    <span>File type DOC, DOCX, PDF, JPG, GIF, JPEG</span>
                    </div>
                      
                  </div>
					<div id="docu"></div>
                  <div class="main-row">
                    <div class="row">Upload a Video</div>
                    <div class="row-r">
                      <a href="<?php echo url::base()."test/index/".$userid; ?>" class="iframe" >Upload Video &nbsp;<img src="<?php echo url::base()."images/upload-video-ico.png"; ?>" width="20" height="20" /></a>
						<input type="hidden" name="video" id="video" value="<?php echo isset($post['video']) ? $post['video']:'' ?>" />
	                    <input type="hidden" name="videoTitle" id="videoTitle" value="" />
                       </div>
                  </div>
					<?php /*
                  <div class="main-row">
                    <div class="row">Checklist Items</div>
                    <div class="row-r">
                        	<table id="checklist_item_container"></table>
                   		<a href="javascript:addNewChecklistItem('',0)">Add new Checklist Item</a>
                    </div>
                  </div>  
									  */ ?>
                  <div class="main-row">
                    <div class="row">Password protect?</div>
                    <div class="row-r">
                    	<div class="radiwrap">
                      		<?php echo Form::radio('protect','1',FALSE,array('class'=>'radi')) ?><?php echo Form::label('protectyes', 'Yes') ?>
                      		<?php echo Form::radio('protect','0',TRUE,array('class'=>'radi')) ?><?php echo Form::label('protectno', 'No') ?>
                      	</div>                      
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
          <!--<div class="btn"> <a href="#">DOWNLOAD PDF</a> <!--<a href="#">ORDER LABELS</a>-- > </div>-->
        </div>
      </div>
	<?php echo Form::close() ?>
  </div>
</div>
<div id="progressbar">
<img src="<?php echo url::base(); ?>images/spinner_gray.gif" align="absmiddle" /> Please Wait. Saving Profile...
</div>
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
	
	$("#frmpeople").validate({
		rules: {
			logo_file: { required: true, accept: 'png|jpg|jpeg|gif' },
			uploaded: "required",
			txtname: "required"
		},
		errorElement: "label",
		messages: {
			logo_file: { required: "Please specify logo", accept: "Only png jpg jpeg or gif file is allowed" },
			uploaded: "Please specify logo ",
			txtname: "Please provide name for People"
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


</script>