<p><?php /* This is the Dashboard Manager Area. You can manage your profile(s) here. */ ?></p>

<?php echo $innermenu; ?>
<?php
if ($add_form_display) {
?>
<?php echo Form::open(URL::base() . 'plant/index', array('id' => 'frmplant', 'name' => 'frmplant', 'method' => 'post', 'enctype' => 'multipart/form-data')) ?> 

<section class="add-form-right"><!-- ADD FORM RIGHT -->
 
  <section class="iphone-preview" >
  </section>
  <div class="clear"></div>
  <div class="SectionTitleBox">
    <div class="SectionTitle">Share</div>
    <ul class="form-view clearfix">
      <li>
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
      </li>
      <div class="clear"></div>
    </ul>
	 <div class="shareNav">
	 	<a href="javascrit:void(0);" class="shareFB">Facebook</a>
		<a href="javascrit:void(0);" class="shareTWT">Twitter</a>
		<a href="javascrit:void(0);" class="shareEML">Email</a>
	 </div>
  </div>
  <?php /* <section class="button-section">
    <a class="green-btn">Download PDF</a>
    <a class="gray-btn">Order Label</a>
    </section> */ ?>

</section><!-- /ADD FORM RIGHT -->


<section class="add-form-left"><!-- ADD FORM LEFT -->


  <div class="SectionTitleBox">
    <div class="SectionTitle">Logo</div>
    <ul class="form-view clearfix">
      <li>
        <label>Upload Company Logo: <span class="hint-info">(File type JPG, JPEG, PNG, GIF)</span></label>
        <iframe src="<?php echo url::base(); ?>test/logoUpload" id="logoUpload" scrolling="no" frameborder="0" width="125%" height="50"></iframe>
        <input type="hidden" id="uploaded" name="uploaded" value="" />
      </li>
    </ul>
  </div>

  <div class="SectionTitleBox">
    <div class="SectionTitle">Videos</div>
    <ul class="form-view clearfix">
      <li <?php echo $video_per == 0 ? 'style="display:none;"' : ''; ?> >
        <label>Upload a Video:</label>
        <a href="<?php echo url::base() . "test"; ?>" class="iframe" >Upload Video &nbsp;<img src="<?php echo url::base() . "images/upload-video-ico.png"; ?>" width="20" height="20" /></a>
        <input type="hidden" name="video" id="video" value="<?php echo isset($post['video']) ? $post['video'] : '' ?>" />
        <input type="hidden" name="videoTitle" id="videoTitle" value="" />
      </li>
    </ul>
  </div>

  <div class="SectionTitleBox">
    <div class="SectionTitle">Words</div>
    <ul class="form-view clearfix">
      <li>
        <label>Make / Model:</label>
        <?php echo Form::input('txtname', isset($post['txtname']) ? $post['txtname'] : '', array('id' => 'txtname', 'class' => 'text-fi')) ?>	
        <?php echo Form::label('txtname', isset($errors['txtname']) ? $errors['txtname'] : '', array('class' => 'error', 'generated' => 'true')) ?>
      </li>
      <li>
        <label>ID:</label>
        <?php echo Form::input('txtidenty', isset($post['txtidenty']) ? $post['txtidenty'] : '', array('id' => 'txtidenty', 'class' => 'text-fi')) ?>
        <?php echo Form::label('txtidenty', isset($errors['txtidenty']) ? $errors['txtidenty'] : '', array('class' => 'error', 'generated' => 'true')) ?>	
      </li>
      <li>
        <label>Serial No.:</label>
        <?php echo Form::input('txtsrno', isset($post['txtsrno']) ? $post['txtsrno'] : '', array('id' => 'txtsrno', 'class' => 'text-fi')) ?>	
        <?php echo Form::label('txtsrno', isset($errors['txtsrno']) ? $errors['txtsrno'] : '', array('class' => 'error', 'generated' => 'true')) ?>
      </li>
      <li>
        <label>Description:</label>
        <?php echo Form::textarea('txtdesc', isset($post['txtdesc']) ? $post['txtdesc'] : '', array("rows" => 1, "cols" => 40, "class" => "input")); ?>
        <?php echo Form::label('txtdesc', isset($errors['txtdesc']) ? $errors['txtdesc'] : '', array('class' => 'error', 'generated' => 'true')) ?>	
      </li>
      <li>
        <label>Note:</label>
        <?php echo Form::textarea('txtnotes', isset($post['txtnotes']) ? $post['txtnotes'] : '', array("rows" => 1, "cols" => 40, "class" => "input")); ?>
        <?php echo Form::label('txtnotes', isset($errors['txtnotes']) ? $errors['txtnotes'] : '', array('class' => 'error', 'generated' => 'true')) ?>
      </li>
      <li class="width-half">
        <label><?php echo Form::label('comp', 'CONTACT:') ?></label>
        <?php echo Form::input('txtcompname', isset($post['txtcompname']) ? $post['txtcompname'] : '', array('id' => 'txtcompname', 'class' => 'text-fi')) ?>
        <?php echo Form::label('txtcompname', isset($errors['txtcompname']) ? $errors['txtcompname'] : '', array('id' => 'errcomp', 'class' => 'error', 'generated' => 'true')) ?>
      </li>
      <li class="width-half">
        <label><?php echo Form::label('tele', 'TELEPHONE:', array('class' => 'pad')) ?></label>
        <?php echo Form::input('txttelephone', isset($post['txttelephone']) ? $post['txttelephone'] : '', array('id' => 'txttelephone', 'class' => 'text-fi')) ?>	
        <?php echo Form::label('txttelephone', isset($errors['txttelephone']) ? $errors['txttelephone'] : '', array('id' => 'errtele', 'class' => 'error', 'generated' => 'true')) ?>                      	
      </li>

      <?php /*
        <li class="width-half">
        <label><?php echo Form::label('comp', 'Mobile button back color:') ?></label>
        <?php echo Form::input('txtbuttonbackcolor', '', array('id' => 'txtbuttonbackcolor', 'class' => 'text-fi color {required:false}')) ?>
        <?php echo Form::label('txtbuttonbackcolor', isset($errors['txtbuttonbackcolor']) ? $errors['txtbuttonbackcolor'] : '', array('id' => 'errcomp', 'class' => 'error', 'generated' => 'true')) ?>
        </li>
        <li class="width-half">
        <label><?php echo Form::label('tele', 'Mobile button font color:', array('class' => 'pad')) ?></label>
        <?php echo Form::input('txtbuttonfontcolor', '', array('id' => 'txtbuttonfontcolor', 'class' => 'text-fi color {required:false}')) ?>
        <?php echo Form::label('txtbuttonfontcolor', isset($errors['txtbuttonfontcolor']) ? $errors['txtbuttonfontcolor'] : '', array('id' => 'errtele', 'class' => 'error', 'generated' => 'true')) ?>
        </li>
       */ ?>
    </ul>
  </div>

  <div class="SectionTitleBox">
    <div class="SectionTitle">Pictures</div>
    <ul class="form-view clearfix">
      <li>
        <label>Upload a picture: <span class="hint-info">(File type JPG, JPEG, PNG, GIF)</span></label>
        <iframe src="<?php echo url::base(); ?>test/pictureUpload" id="imageUpload" scrolling="no" frameborder="0" height="50" width="125%"></iframe>
      </li>
    </ul>
  </div>

  <div class="SectionTitleBox">
    <div class="SectionTitle">Documents</div>
    <ul class="form-view clearfix">
      <li>
        <label>Upload a Document: <span class="hint-info">(File type DOC, DOCX, PDF, JPG, GIF, JPEG)</span></label>
        <iframe src="<?php echo url::base(); ?>test/docUpload" id="docUpload" scrolling="no" frameborder="0" height="50" width="125%"></iframe>
      </li>
    </ul>
  </div>



  <div class="SectionTitleBox">
    <div class="SectionTitle">Web Link</div>
    <ul class="form-view clearfix">

      <li>
        <div style="float: left; width: 350px;">
          <?php echo Form::checkbox('link_button', '1', false, array('id' => 'link_button')) ?>
          <?php echo Form::label('link_button', 'Add Button', array('style' => 'display: inline;')) ?>
        </div>
      </li>

      <li class="width-half">
        <label><?php echo Form::label('comp', 'Button Text:') ?></label>
        <?php echo Form::input('link_button_text', isset($post['link_button_text']) ? $post['link_button_text'] : '', array('id' => 'link_button_text', 'class' => 'text-fi')) ?>
        <?php echo Form::label('link_button_text', isset($errors['link_button_text']) ? $errors['link_button_text'] : '', array('id' => 'errcomp', 'class' => 'error', 'generated' => 'true')) ?>
      </li>
      <li class="width-half">
        <label><?php echo Form::label('comp', 'Button Link:') ?></label>
        <?php echo Form::input('link_button_url', isset($post['link_button_url']) ? $post['link_button_url'] : '', array('id' => 'link_button_url', 'class' => 'text-fi')) ?>
        <?php echo Form::label('link_button_url', isset($errors['link_button_url']) ? $errors['link_button_url'] : '', array('id' => 'errcomp', 'class' => 'error', 'generated' => 'true')) ?>
      </li>
      <li class="width-half">
        <label><?php echo Form::label('comp', 'Button color:') ?></label>
        <?php echo Form::input('link_button_color', '', array('id' => 'link_button_color', 'class' => 'text-fi color {required:false}')) ?>
        <?php echo Form::label('link_button_color', isset($errors['link_button_color']) ? $errors['link_button_color'] : '', array('id' => 'errcomp', 'class' => 'error', 'generated' => 'true')) ?>
      </li>

      <?php /*
        <li class="width-half">
        <label><?php echo Form::label('comp', 'Mobile button back color:') ?></label>
        <?php echo Form::input('txtbuttonbackcolor', '', array('id' => 'txtbuttonbackcolor', 'class' => 'text-fi color {required:false}')) ?>
        <?php echo Form::label('txtbuttonbackcolor', isset($errors['txtbuttonbackcolor']) ? $errors['txtbuttonbackcolor'] : '', array('id' => 'errcomp', 'class' => 'error', 'generated' => 'true')) ?>
        </li>
        <li class="width-half">
        <label><?php echo Form::label('tele', 'Mobile button font color:', array('class' => 'pad')) ?></label>
        <?php echo Form::input('txtbuttonfontcolor', '', array('id' => 'txtbuttonfontcolor', 'class' => 'text-fi color {required:false}')) ?>
        <?php echo Form::label('txtbuttonfontcolor', isset($errors['txtbuttonfontcolor']) ? $errors['txtbuttonfontcolor'] : '', array('id' => 'errtele', 'class' => 'error', 'generated' => 'true')) ?>
        </li>
       */ ?>
    </ul>
  </div>

  <div class="SectionTitleBox">
  	<div class="SectionTitle">Data Collection</div>
  	<ul class="form-view clearfix">
    	<li>
        <label style="float: left;">Enable Data Collection:</label>
        <div style="float: left; width: 350px;">
          <?php echo Form::radio('enable_data_collection', '1', false, array('class' => 'radi enable_data_collection')) ?>
          <?php echo Form::label('enable_data_collection', 'Yes', array('style' => 'display: inline;')) ?>
          <?php echo Form::radio('enable_data_collection', '0', true, array('class' => 'radi enable_data_collection')) ?>
          <?php echo Form::label('enable_data_collection', 'No', array('style' => 'display: inline;')) ?>
        </div>
      </li>
	
     <li>
      <div style="float: left; width: 350px;">
        <?php
        echo Form::checkbox(
                'set_up_compulsory', '1', isset($feilds['set_up_compulsory']) ? $feilds['set_up_compulsory'] ? true : false  : false, array('class' => 'radi enable_data_collection'
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
    </li>

      <div class="data_collection_div">
        <li>
          <div style="float: left; width: 350px;">
            <?php echo Form::checkbox('data_collection_mobile', '1', '', array('id' => 'data_collection_mobile')) ?>
            <?php echo Form::label('data_collection_mobile', 'Mobile', array('style' => 'display: inline;')) ?>

            <?php echo Form::checkbox('data_collection_email', '1', '', array('id' => 'data_collection_email')) ?>
            <?php echo Form::label('data_collection_email', 'Email', array('style' => 'display: inline;')) ?>

            <?php echo Form::checkbox('data_collection_name', '1', '', array('id' => 'data_collection_name')) ?>
            <?php echo Form::label('data_collection_name', 'Name', array('style' => 'display: inline;')) ?>
          </div>
        </li>

        <li>
          <div>
            <?php echo Form::label('data_collection_content', 'Content') ?>
            <?php echo Form::textarea('data_collection_content', '', array('id' => 'data_collection_content')); ?>
          </div>
        </li>
      </div>
    </ul>
  </div>

  <div class="SectionTitleBox">
  	<div class="SectionTitle">Set Code Type</div>
    <ul class="form-view clearfix">
    	<li>
        <label style="float: left;margin-top:3px;">Code Type:</label>
        <div style="float: left; width: 350px;">
          <?php echo Form::radio('code_type', '0', true, array('class' => 'radi')) ?>
          <?php echo Form::label('code_type_qr', 'QR Code', array('style' => 'display: inline;')) ?>
          <?php echo Form::radio('code_type', '1', false, array('class' => 'radi')) ?>
          <?php echo Form::label('code_type_dm', 'Data Matrix', array('style' => 'display: inline;')) ?>
        </div>
      </li>
    </ul>
  </div>
   
  <div class="SectionTitleBox">
  	<div class="SectionTitle">Header</div>
    <ul class="form-view clearfix">
    	<li>
        <div style="float: left; width: 350px;">
          <?php echo Form::checkbox('show_header', '1', false, array('id' => 'show_header')) ?>
          <?php echo Form::label('show_header', 'Show header in mobile', array('style' => 'display: inline;')) ?>
        </div>
      </li>
    </ul>
  </div>
  
  
  <div class="SectionTitleBox">
  	<div class="SectionTitle">User Access Security</div>
    <ul class="form-view clearfix">
      <li>
        <label style="float: left;margin-top:3px;">Password protect?:</label>
        <div style="float: left; width: 350px;">
          <?php echo Form::radio('protect', '1', FALSE, array('class' => 'radi')) ?>
          <?php echo Form::label('protectyes', 'Yes', array('style' => 'display: inline;')) ?>
          <?php echo Form::radio('protect', '0', TRUE, array('class' => 'radi')) ?>
          <?php echo Form::label('protectno', 'No', array('style' => 'display: inline;')) ?>
        </div>
      </li>

      <li>
        <label style="float: left;">Password:</label>
        <div>
          <?php echo Form::input('password', isset($post['password']) ? $post['password'] : '', array('id' => 'password', 'class' => 'text-fi')) ?>
          <?php echo Form::label('password', isset($errors['password']) ? $errors['password'] : '', array('class' => 'error', 'generated' => 'true')) ?>	
        </div>
      </li>

      
    </ul>
  </div>


  <ul class="form-view clearfix">







    <?php /* if($checklist_option==1){ ?>
      <li>
      <label>Checklist Items:</label>
      <table id="checklist_item_container"></table>
      <a href="javascript:addNewChecklistItem('',0)">Add new Checklist Item</a>
      </li>
      <?php }else{ ?>
      <input type="hidden" name="checklist_item[]" />
      <?php } */ ?>                  




    <li>
      <?php echo Form::submit(NULL, '&nbsp;&nbsp;&nbsp;&nbsp;Save&nbsp;&nbsp;&nbsp;&nbsp;', array('id' => 'save', 'class' => 'save', 'style' => 'width: 150px;')) ?>
    </li>
  </ul>
</section>

<?php echo Form::close() ?>


<div id="progressbar">
  <img src="<?php echo url::base(); ?>images/spinner_gray.gif" align="absmiddle" /> Please Wait. Saving Profile...
</div>
<script type="text/javascript">

  $(document).ready(function(){
    
    
    jQuery('#data_collection_content').maxlength( {slider: true, maxCharacters: 150} ); 
	 
    jQuery('.enable_data_collection').click(function(){
      if($(this).val() == "0"){
        $('.data_collection_div').hide();
        $('#data_collection_mobile').attr('checked',false);
        $('#data_collection_email').attr('checked',false);
        $('#data_collection_content').val('');
      }else{
        $('.data_collection_div').show();
      }
    });
    
    
    jQuery(".iframe").colorbox({
      iframe:true, 
      width:"750", 
      height:"600", 
      onClosed:function(){
        //			parent.location.reload()
      }
    });
	
    /*$("#addmore").click(function(){
    var copyclone = $('#docu').clone();
    $(copyclone).removeAttr("id").addClass("docminus").find("#addmore").removeAttr("id").addClass("thisminus").attr("src","images/minus.png");
    $("#docu").after(copyclone);
  });
  $(".thisminus").live('click',function(){
    $(this).parent().parent().remove();
  });*/
    /*$("#docminus").each(function(){
    $(this).bind("click", function(){
    alert($(this).html());
    //$(this).parent().parent().remove();
    });
  });*/
	
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
	
    $("#frmplant").validate({
      rules: {
        logo_file: { required: true, accept: 'png|jpg|jpeg|gif' },
        uploaded: "required",
        txtname: "required"
      },
      
      messages: {
        logo_file: { required: "Please specify logo ", accept: "Only png jpg jpeg or gif file is allowed" },
        uploaded: "Please specify logo ",
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

  $(function() {
    $('textarea').autogrow();
  });
</script>
<?php } else { ?>
  <p> You can not add misc.kindly purchase the code.</p>
<?php } ?>