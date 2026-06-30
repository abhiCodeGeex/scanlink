<script>
	
	
	function add_another_file(max_upload_size)
	{
                date_curr = new Date();
					
                hours = date_curr.getHours();
                mins = date_curr.getMinutes();
                seconds = date_curr.getSeconds();

                random_id = hours+""+mins+""+seconds;
                cntrl_name= "txt"+random_id;
                
                
		html  = '<div class="voc_user_details" style="width:50%;float:left;">'
		html += '	<label>Document Name</label>';
		html += '	<input type="text" id="'+cntrl_name+'" name="doc_name[]" style="width:200px" class="doc_name text-fi" value="" maxlength="200">';
		html += '</div>';
		html += '<div class="voc_user_details" style="width:50%;float:left;">';
		html += '	<label>Expiry Date</label>';
		html += '	<input type="text" name="doc_expiry_date[]" style="width:216px" class="doc_expiry text-fi" value="" maxlength="10">';
		html += '	<input type="hidden" name="hdn_existing_doc_id[]" class="hdn_existing_doc" value="0">';
		html += '</div>';
		html += '<div class="clear"></div>';
		html += '<div>';
		html += '	<label>Upload a Document: <span class="hint-info">(File type DOC, DOCX, PDF, GIF, JPEG, PNG) (Max file size '+max_upload_size+')</span></label>';
		html += '</div>';	
		html += '<div class="voc_doc_upload"><input type="file" name="document_file[]"  /></div>';
		
	 	$('.add_another_voc_file').before(html);
                $('#'+cntrl_name).maxlength( {slider: true, maxCharacters: 50} );
		$( "input:file").uniform();
		
		$('.doc_expiry').datepicker({
				dateFormat: 'dd/mm/yy',
                                changeYear: true,
                                changeMonth:true
		});
        }
	
	function download_qrcode(qrimg_id)	
	{	
		download_as = $("#download_option").val();
		if(download_as=="")
		{
			alert("Please select download as");
			return false;
		}
		location.href='<?php echo URL::base() ?>dashboard/getQR/'+ qrimg_id + '?download_as='+download_as;
		
	}
	
	
	$(document).ready(function(){
                $( "input:file").uniform();
                
                $('.doc_name').maxlength( {slider: true, maxCharacters: 50} );
            
                $('#voc_dob').datepicker({
				dateFormat: 'dd/mm/yy',
				changeYear: true,
                                changeMonth:true,
                                 yearRange: "1915:<?php echo date('Y');?>"
		});
		
		$('.doc_expiry').datepicker({
				dateFormat: 'dd/mm/yy',
                                changeYear: true,
                                changeMonth:true
		});
		
		
		
		
		//hide help tooltip when mouseleave
		$('.help-div').on('mouseleave',function(){
			$(this).hide();
		});
		
		// show help tooltip
		$('.help-icon').on('mouseenter',function(){
			$(this).next().show();
		});
		
		//open help icon
		$('.help-div a').click(function(e){
			$(this).parent().hide();
			
			e.preventDefault();
			var url = $(this).attr("href");
			if(url != ''){
			  var p = /^(?:https?:\/\/)?(?:www\.)?(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=))((\w|-){11})(?:\S+)?$/;
			  var result = (url.match(p)) ? RegExp.$1 : false;
			  if(result){
				$('#how_to_pop_up').html('<iframe src="'+url+'" frameborder="0" allowfullscreen wmode="transparent" width="480" height="300" ></iframe>');
				$('.black-tint').show();
				$('.player-window').show(500);
			  }else{
				  
				  var substr = 'image#';
				  if(url.indexOf(substr) !== -1){
					  url = url.replace(substr,"");
					  
					  
					  $('.black-tint').show();
			  		  $('.player-window-image').show(500);3
				  }else{
				  	return false;
				  }
			  }
			}else{
				return false;
			}
		});
		
	});
	
</script>

<?php
if (!$expried_or_not) {
?>
<?php if (isset($errormessage) && $errormessage != ""): ?>
  <div class="row-r"><label  class="error"><?php echo $errormessage; ?></label></div>
  <div class="clear">&nbsp;</div>
<?php endif; ?>
<?php if (!empty($successmessage)): ?>
  <div class="row-r"><label  class="success"><?php echo $successmessage; ?></label></div>
  <div class="clear">&nbsp;</div>
<?php endif; ?>

<?php echo Form::open(URL::base() . 'voc/vocedit/'. $profile_id, array('id' => 'frmvoc', 'name' => 'frmvoc', 'method' => 'post', 'enctype' => 'multipart/form-data')) ?>
<input type="hidden" id="hdn_email_arr" name="hdn_email_arr" value="" />
  <input type="hidden" id="profile_id" name="profile_id" value="<?php echo $profile_id; ?>" />
  <input type="hidden" id="hdn_exp_date" name="hdn_exp_date" value="<?php echo $expired_at;?>" />
  <input type="hidden" id="free_code" name="free_code" value="<?php echo $free_code;?>" />
  <input type="hidden" id="renewal_required" name="renewal_required" value="<?php echo $renewal_required;?>" />
  
<?php echo Form::input('edit_id', $post["edit_id"], array('type' => 'hidden')) ?>

  <div class="form-view" style="float:right;"><input type="button" onclick="location.href='<?php echo URL::base();?>dashboard/voc_user';" value="Back to Listing"></div>
  <div class="clear"></div>
  
<?php echo "<h2>Profile ".$post["edit_id"]." ".$post['txt_code_profile_name']."</h2>";?>
  <hr>  
<section class="add-form-left "><!-- ADD FORM LEFT -->
	
 
        			<div class="SectionTitleBox" >
				    <div class="SectionTitle">Profile Information
						<img src="<?php echo url::base(); ?>images/help_icon.jpg" class="help-icon" style="vertical-align:text-bottom;" />
						
						
                                    </div>
                                    <div class="form-view clearfix">
				   		<div class="voc_user_details" style="width:50%;float:left;">
					        <label>First Name</label>
					        <input type="text" name="voc_first_name" id="voc_first_name" style="width:200px" class="text-fi" value="<?php echo $post["voc_first_name"];?>" maxlength="200">
					      </div>
					      <div class="voc_user_details" style="width:50%;float:left;">
					        <label>Last Name</label>
					        <input type="text" name="voc_last_name" id="voc_last_name" style="width:216px" class="text-fi" value="<?php echo $post["voc_last_name"];?>" maxlength="200">
					      </div>
					      <div class="clear"></div>
					      
					      <div class="voc_user_details" >
					        <label>Address</label>
					        <input type="text" name="voc_address" id="voc_address"  class="text-fi" value="<?php echo $post["voc_address"];?>" maxlength="200">
					      </div>
					      
					      <div class="voc_user_details" style="width:50%;float:left;">
					        <label>Town</label>
					        <input type="text" name="voc_town" id="voc_town" style="width:200px" class="text-fi" value="<?php echo $post["voc_town"];?>" maxlength="50">
					      </div>
					      <div class="voc_user_details" style="width:50%;float:left;">
					        <label>State/Territory</label>
					        <input type="text" name="voc_state" id="voc_state" style="width:216px" class="text-fi" value="<?php echo $post["voc_state"];?>" maxlength="50">
					      </div>
					      <div class="clear"></div>
					      
					      <div class="voc_user_details" style="width:50%;float:left;">
					        <label>Telephone No.</label>
					        <input type="text" name="voc_phone" id="voc_phone" style="width:200px" class="text-fi" value="<?php echo $post["voc_phone"];?>" maxlength="30">
					      </div>
					      <div class="voc_user_details" style="width:50%;float:left;">
					        <label>Date of Birth</label>
					        <input type="text" name="voc_dob" id="voc_dob" style="width:216px" class="text-fi" value="<?php echo $post["voc_dob"];?>" maxlength="10">
					      </div>
					      <div class="clear"></div>
					      
					      <div class="voc_user_details" >
					        <label>Known Allergies/Medical Conditions</label>
					        <textarea  rows="3" name="voc_known_allergies" id="voc_known_allergies"><?php echo $post["voc_known_allergies"];?></textarea>
					      </div>
					      
					       <div class="voc_user_details" style="width:50%;">
					        <label>Blood Type</label>
					        <input type="text" name="voc_blood_type" id="voc_blood_type" style="width:200px" class="text-fi" value="<?php echo $post["voc_blood_type"];?>" maxlength="5">
					      </div>
					      
					      <div class="voc_user_details" style="width:50%;float:left;">
					        <label>Next of Kin</label>
					        <input type="text" name="voc_next_of_kin" id="voc_next_of_kin" style="width:200px" class="text-fi" value="<?php echo $post["voc_next_of_kin"];?>" maxlength="50">
					      </div>
					      <div class="voc_user_details" style="width:50%;float:left;">
					        <label>Contact Telephone No.</label>
					        <input type="text" name="voc_contact_phone" id="voc_contact_phone" style="width:216px" class="text-fi" value="<?php echo $post["voc_contact_phone"];?>" maxlength="30">
					      </div>
					      <div class="clear"></div>
					      
					      <div class="voc_user_details" >
					        <label>Employer</label>
					        <input type="text" name="voc_employer" id="voc_employer" class="text-fi" value="<?php echo $post["voc_employer"];?>" maxlength="200">
					      </div>
					      
					      <div class="voc_user_details" >
					        <label>Address</label>
					        <input type="text" name="voc_emp_address" id="voc_emp_address"  class="text-fi" value="<?php echo $post["voc_emp_address"];?>" >
					      </div>
					      
					      <div class="voc_user_details" style="width:50%;float:left;">
					        <label>Town</label>
					        <input type="text" name="voc_emp_town" id="voc_emp_town" style="width:200px" class="text-fi" value="<?php echo $post["voc_emp_town"];?>" maxlength="50">
					      </div>
					      <div class="voc_user_details" style="width:50%;float:left;">
					        <label>State/Territory</label>
					        <input type="text" name="voc_emp_state" id="voc_emp_state" style="width:216px" class="text-fi" value="<?php echo $post["voc_emp_state"];?>" maxlength="50">
					      </div>
					      <div class="clear"></div>
					      
					      <div class="voc_user_details" style="width:50%;float:left;">
					        <label>Telephone No.</label>
					        <input type="text" name="voc_emp_phone" id="voc_emp_phone" style="width:200px" class="text-fi" value="<?php echo $post["voc_emp_phone"];?>" maxlength="30">
					      </div>
					      <div class="clear"></div>
				   </div>
				</div>
			
				<div class="SectionTitleBox" >
				    <div class="SectionTitle">Document Upload
						<img src="<?php echo url::base(); ?>images/help_icon.jpg" class="help-icon" style="vertical-align:text-bottom;" />
						
						
					</div>
				    <div class="form-view clearfix">
                                        <?php 
                                        foreach ($post["documents"] as $voc_documents)
                                        {
                                            $voc_expiry_date_arr = explode("-",$voc_documents["expiry_date"]);
      
                                            if($voc_documents["expiry_date"]=="0000-00-00")
                                                $voc_document_exp_date = "";
                                            else
                                                $voc_document_exp_date = $voc_expiry_date_arr["2"]."/".$voc_expiry_date_arr[1]."/".$voc_expiry_date_arr[0];
                                            ?>
                                            <div class="div_voc_doc_<?php echo $voc_documents["voc_document_id"]; ?>">
                                                <div class="voc_user_details" style="width:50%;float:left;">
                                                    <label>Document Name</label>
                                                    <input type="text" name="doc_name[]" style="width:200px" class="doc_name text-fi" value="<?php echo $voc_documents["name"] ?>" maxlength="200">
                                                </div>
                                                <div class="voc_user_details" style="width:50%;float:left;">
                                                    <label>Expiry Date</label>
                                                    <input type="text" name="doc_expiry_date[]" style="width:216px" class="doc_expiry text-fi" value="<?php echo $voc_document_exp_date;?>" maxlength="10">
                                                    <input type="hidden" name="hdn_existing_doc_id[]" class="hdn_existing_doc" value="<?php echo $voc_documents["voc_document_id"]; ?>">
                                                </div>
                                                <div class="clear"></div>

                                                <div>
                                                    <label>Upload a Document: <span class="hint-info">(File type DOC, DOCX, PDF, GIF, JPEG, PNG) (Max file size <?php echo UPLOAD_MAX_FILESIZE;?>)</span></label>
                                                </div>	
                                                <div class="voc_doc_upload">
                                                    <input type="file" name="document_file[]">	
                                                </div>

                                                <div class="view-document">
                                                   <a href="<?php echo URL::base() .'images/voc_documents/'.$voc_documents["file_name"];?>" target="_blank">View Document</a> 
                                                </div>
                                                <div class="remove_voc_recipient"><a onclick="remove_voc_document('<?php echo $voc_documents["voc_document_id"]; ?>')" href="javascript:;">Remove</a></div>
                                            </div>
                                        <?php
                                        }
                                        
                                        if(count($post["documents"])==0)
                                        {
                                        ?>
                                        
                                            <div class="voc_user_details" style="width:50%;float:left;">
                                                <label>Document Name</label>
                                                <input type="text" name="doc_name[]" style="width:200px" class="doc_name text-fi" value="" maxlength="200">
                                            </div>
                                            <div class="voc_user_details" style="width:50%;float:left;">
                                                <label>Expiry Date</label>
                                                <input type="text" name="doc_expiry_date[]" style="width:216px" class="doc_expiry text-fi" value="" maxlength="10">
                                                <input type="hidden" name="hdn_existing_doc_id[]" class="hdn_existing_doc" value="">
                                            </div>
                                            <div class="clear"></div>

                                            <div>
                                                <label>Upload a Document: <span class="hint-info">(File type DOC, DOCX, PDF, GIF, JPEG, PNG (Max file size <?php echo UPLOAD_MAX_FILESIZE;?>)</span></label>
                                            </div>	
                                            <div class="voc_doc_upload">
                                                <input type="file" name="document_file[]">	
                                            </div>

                                           
                                    <?php
                                        }?>
                                            <div class="add_another_voc_file">
                                                <a onclick="add_another_file('<?php echo UPLOAD_MAX_FILESIZE;?>')" href="javascript:;">
                                                    Add Another<img src="<?php echo URL::base() ?>images/expand_window.png" id="expand_reduce_img" width="18" align="absmiddle" alt="">
                                                </a>
                                            </div>			      	
                                            <div class="clear"></div>
				    </div>
				</div>
	
  

  <ul class="form-view clearfix">
     <li>
      <input name="save" id="save" type="submit" class="save" value="&nbsp;&nbsp;&nbsp;&nbsp;Save&nbsp;&nbsp;&nbsp;&nbsp;" onclick='javascript: beforeSave();'>
    </li>

  </ul>
</section>

<section class="add-form-right"><!-- ADD FORM RIGHT -->

  <section class="iphone-preview" >
  	 <div class="iphone-preview-container">
    	<iframe width="320" frameborder="0"  height="882" src="<?php echo URL::base() . $post['edit_id']; ?>/<?php echo $post['edit_id']; ?>?ask_for_location=no"></iframe>
    </div>
  </section>

  <div  style="margin: 0 auto; width: 100%;">
    
    <div class="clear"></div>
    <div style="margin: 10px auto 0; width: 87%; " class="form-view1">
      <img style="float: left" src="<?php echo isset($qrimg['qrimg_name']) && URL::base() . $qrimg['qrimg_name'] ? URL::base() . $qrimg['qrimg_name'] : ''; ?>" alt="" width="175" />
      
    </div>
  </div>
  
  <div class="clear">&nbsp;</div>
  <div>
        URL<br>	
        <div class="qr-url-div"><?php echo $qrurl;?></div>
  </div>   
  <div class="clear">&nbsp;</div>
  
  <div class="clear"></div>


</section><!-- /ADD FORM RIGHT -->

<?php echo Form::close() ?>

<div id="progressbar">
  <img src="<?php echo url::base(); ?>images/spinner_gray.gif" align="absmiddle" /> Please Wait. Saving Profile...
</div>
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
  	
		
    $("#frmvoc").validate({
      rules: {
       
       	
      },
      errorElement: "label",
      messages: {
       
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
		
		free_code = $("#free_code").val();
		renewal_required = $("#renewal_required").val();
                
                voc_first_name = $.trim($('#voc_first_name').val());
		voc_last_name = $.trim($('#voc_last_name').val());
		
		if(voc_first_name=="")
		{
			alert("Please enter first name.");
			$('#voc_first_name').focus();
			return false;		
		}
		
		if(voc_last_name=="")
		{
			alert("Please enter last name.");
			$('#voc_last_name').focus();
			return false;		
		}
		
		doc_name_arr = document.getElementsByName('doc_name[]');
		document_file_arr = document.getElementsByName('document_file[]');
                document_exist_arr = document.getElementsByName('hdn_existing_doc_id[]');
		
		for(i=0;i<(doc_name_arr.length);i++)
		{
			if(document_exist_arr[i].value.split(" ").join("")==0 && doc_name_arr[i].value.split(" ").join("")!="" && document_file_arr[i].value.split(" ").join("")=="")
			{
				alert('Please upload file.');
				return false;
			}
			
			if(doc_name_arr[i].value.split(" ").join("")=="" && document_file_arr[i].value.split(" ").join("")!="")
			{
				alert('Please enter document name.');
				return false;
			}
			
			if(document_file_arr[i].value.split(" ").join("")!="")
			{
					file_name_arr = document_file_arr[i].value.split(".");
					ext = file_name_arr[file_name_arr.length-1];
					
					if(ext.toLowerCase()!="doc" && ext.toLowerCase()!="docx" && ext.toLowerCase()!="pdf" && ext.toLowerCase()!="jpeg" && ext.toLowerCase()!="jpg" && ext.toLowerCase()!="gif" && ext.toLowerCase()!="png")
					{
						alert("Upload .doc, .docx, .pdf, .jpeg, .png or .gif file only");
						return false;
					}				
				
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

  
  
    
  function remove_voc_document(voc_document_id)
  {
      if(confirm("Are you sure to remove this Document ? ")){
          
        $.ajax({
		type: "GET",
                url: "<?php echo URL::base() ;?>voc/delete_voc_document/",
                data: { "voc_document_id":voc_document_id}, 
                success: function(data)
                {
                        $('.div_voc_doc_'+voc_document_id).remove();
                }
                
        });
      }
  }



</script>
<?php } else { ?>
  <p>You can not perform this action on expired profile.</p>
<?php } ?>