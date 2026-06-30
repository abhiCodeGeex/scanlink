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
      <h3>Code (<?php echo $uemail;?>)</h3>
      <?php echo Form::open(URL::base().'code/index', array('id'=>'frmcode','name'=>'frmcode','method'=>'post','enctype' => 'multipart/form-data')) ?>
      <input type="hidden" id="userid" name="userid" value="<?php echo $userid;?>" />
      	<?php
		  echo Form::input(
				  'mode', $mode, array(
				  'id' => 'mode',
				  'class' => 'text-fi',
				  'type' => 'hidden')
		  );
		  ?> 
		  <?php
		  echo Form::input(
				  'profile_id', $profile_id, array(
				  'id' => 'profile_id',
				  'class' => 'text-fi',
				  'type' => 'hidden')
		  );
		  ?> 
          
      <div class="penal-tab">
        <div class="TabbedPanels">
          <div class="TabbedPanelsContentGroup">
            <div class="TabbedPanelsContent">
              <div class="fild-outer">
                
               
                <div class="main-row">
                  <div class="row">Application</div>
                  <div class="row-r"> 
				  	 <?php echo Form::input('application', isset($feilds['application']) ? $feilds['application'] : '', array('id' => 'application','class' => 'text-fi codeTextApplication'));
				  ?> 
				  <?php echo Form::label('application', isset($errors['application']) ? $errors['application'] : '', array('class' => 'error','generated' => 'true'));?>
                  </div>
                </div>
                
                <div class="main-row">
                  <div class="row">Destination URL</div>
                  <div class="row-r"> 
				  	<?php echo Form::input('destination_url', isset($feilds['destination_url']) ? $feilds['destination_url'] : '', array('id' => 'destination_url','class' => 'text-fi')); ?> 
				  	<?php echo Form::label('destination_url', isset($errors['destination_url']) ? $errors['destination_url'] : '', array('class' => 'error','generated' => 'true'));?>
                  </div>
                </div>
                
              	<div class="main-row">
                  <div class="row">&nbsp;</div>
                  <div class="row-r-2"> 
                  	<?php echo Form::checkbox('activate_bridge_graphic', '1', isset($feilds['activate_bridge_graphic']) ? $feilds['activate_bridge_graphic'] ? true : ''  : '', array('id' => 'activate_bridge_graphic')); ?>
				  <?php echo Form::label('activate_bridge_graphic', 'Activate Bridge Graphic<span class="codelabel tooltip" title="This bridge graphic option enables you to feature an image momentarily on the mobile screen before your URL destenation appears"></span>', array('style' => 'display: inline;'));?>
				  <?php
				  		if (isset($errors['activate_bridge_graphic'])) {
							echo "<label class='error'>" . $errors['activate_bridge_graphic'] . "</label>";
				  		}
				  	?>
                    
                    
                  
                  
				  </div>
         		</div>
                
                <div class="main-row">
                  <div class="row ">&nbsp;</div>
                  <div class="row-r"> 
                  	<label class="lineheight extraWidth">Upload Graphic </label>
				  	
                    	<?php
					  if (isset($errors['activate_bridge_graphic'])) {
						echo "<label class='error'>" . $errors['activate_bridge_graphic'] . "</label>";
					  }
					  ?>
					  <?php
					  if ($mode == 'edit') {
						if (isset($feilds['activate_bridge_graphic']) && $feilds['activate_bridge_graphic'] == true) {
						  $buttonproperty = array(
							  'id' => 'upload_graphic',
							  'class' => 'radi bridge_graph_uploader',
							  'type' => 'file',
							  'accept' => "image/*"
						  );
						} else {
						  $buttonproperty = array(
							  'id' => 'upload_graphic',
							  'class' => ' radi bridge_graph_uploader',
							  'disabled' => true,
							  'type' => 'file',
							  'accept' => "image/*|picture/*"
						  );
						}
					  } else {
						$buttonproperty = array(
							'id' => 'upload_graphic',
							'class' => 'radi bridge_graph_uploader',
							'disabled' => true,
							'type' => 'file'
						);
					  }
					?>
                    
					<?php echo Form::input('upload_graphic', 'Upload Graphics', $buttonproperty); ?>
                    
                     <div class="clear"></div>
                     <span class="codespan">File Type jpeg png gif<br/>Max Size 2 MB <br/>Dimension <br/>320px(8.46cm)Wide <br/> 480px(12.7cm)long</span>
                     <div class="clear"></div>
                     <br/>
                     <?php
				  		if ($mode == 'edit' && isset($feilds['thumbfile']) && $feilds['thumbfile'] != '') {
					 ?>
							<img src="<?php echo URL::base() . $feilds['thumbfile']; ?>" id="bridge_image" name="bridge_image" class=""  />
					 <?php
				  		} else {
					  ?>
							<img src="<?php echo URL::base(); ?>../images/graphic.jpg" id="bridge_image" name="bridge_image" class=""  />
					  <?php
				  		}
				  	  ?>
                  </div>
         		</div>
                
               
                <div class="main-row">
                  <div class="row padbot">Data Collection</div>
                  <div class="row-r"> 
                  	<label class="lineheight extraWidth">Enabel data collection : </label>
				  	<div class="radiwrap">
					<?php echo Form::radio('enable_data_collection', '1', false, array('class' => 'radi enable_data_collection')) ?>
          			<?php echo Form::label('enable_data_collection', 'Yes', array('style' => 'display: inline;')) ?>
			        <?php echo Form::radio('enable_data_collection', '0', true, array('class' => 'radi enable_data_collection')) ?>
          			<?php echo Form::label('enable_data_collection', 'No', array('style' => 'display: inline;')) ?></div>
                  </div>
         		</div>
                
                
                <div class="main-row">
                  <div class="row">&nbsp;</div>
                  <div class="row-r-2"> 
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
         		</div>
                
                <div class="main-row">
                  <div class="row">&nbsp;</div>
                  <div class="row-r-2"> 
                  	<div class="dcChe"> 
						<?php echo Form::checkbox('data_collection_mobile', '1', (bool) $feilds["data_collection_mobile"], array('id' => 'data_collection_mobile')) ?> 
						<?php echo Form::label('data_collection_mobile', 'Mobile', array('style' => 'display: inline;')) ?> 
                    </div>
                    <div class="dcChe"> 
						<?php echo Form::checkbox('data_collection_email', '1', (bool) $feilds["data_collection_email"], array('id' => 'data_collection_email')) ?> 
						<?php echo Form::label('data_collection_email', 'Email', array('style' => 'display: inline;')) ?> 
                    </div>
                    <div class="dcChe"> 
						<?php echo Form::checkbox('data_collection_name', '1', (bool) $feilds["data_collection_name"], array('id' => 'data_collection_name')) ?> 
						<?php echo Form::label('data_collection_name', 'Name', array('style' => 'display: inline;')) ?> 
                    </div>
				  </div>
         		</div>
         
               <div class="main-row padtop">
                <div class="row padtop">&nbsp;</div>
                <div class="row-r padtop"> 
                    <?php echo Form::label('data_collection_content', 'pop up message') ?>
            		<?php echo Form::textarea('data_collection_content', $feilds["txtdesc"], array('id' => 'data_collection_content','class'=>'input','rows'=>'1','cols'=>'40','style'=>'height:20px;')); ?>
                </div>
              </div>
              
              </div>
              <div class="clear"></div>
            </div>
          </div>
          <div class="clear"></div>
        </div>
      </div>
      <div class="penal-qr"> <?php echo Form::submit(NULL, 'Save', array('id'=>'save','class'=>'save')) ?>
        <div class="qr-bg">&nbsp;</div>
        <?php if($mode == 'edit') { ?>
          <img src="<?php echo isset($qrimg) &&  kohana::$base_site_url.$qrimg ?  kohana::$base_site_url.$qrimg['qrimg_name'] : '';?>" alt="" />
          <div class="btn"> <a href="<?php echo  kohana::$base_site_url;?>code/downloadQrImage/<?php echo $profile_id;?>">DOWNLOAD PDF</a>
        <?php } ?>
         </div>
    <?php echo Form::close() ?> </div>
</div>
</div>
<div id="progressbar"> <img src="<?php echo url::base(); ?>images/spinner_gray.gif" align="absmiddle" /> Please Wait. Saving Profile... </div>

<script type="text/javascript" src="<?php echo url::base(); ?>js/jquery.form.js"></script>
		
<script type="text/javascript">
  $(document).ready(function(){
				
	/** show hide enable data collection  **/
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
			  
  });
</script> 
<script type="text/javascript">
  /** Text area auto grow **/
  $(function() {
	$('textarea').autogrow();
  });
			
  /** bridge graphics file button enavle or disable  **/
  $("#activate_bridge_graphic").click( function(){
	if( $(this).is(':checked') ){
	  $('#upload_graphic').attr("disabled",false);
	  $('#button-graphics').removeClass('UploadBlackBtn');
	  $('#button-graphics').addClass('myui-button');
	}else{
	  $('#upload_graphic').attr("disabled",true);
	  $('#button-graphics').removeClass('myui-button');
	  $('#button-graphics').addClass('UploadBlackBtn');
	}
  });
			
  /** bridge graphics logo remove  **/
  $('#removelogo').click( function(){
	var width = $(window).width()/2-34;
	var height =$(window).height()/2-34;
	$('.loaderdiv').css("width",width);
	$('.loaderdiv').css("height",height);
	$('#loaderimg').css("padding-left","103%");
	$('.loaderdiv').show();
	var options = { 
	  url:'<?php echo URL::base(); ?>code/removelogo',
	  success: function(data){
					  
		$('.loaderdiv').hide();
		var data = $.parseJSON(data);
					  
		if(data.status){
		  $('#bridge_image').attr('src','');
		  $('#bridge_image').attr('src', data.thumb + '?rnd='+ Math.random());
		  $('#small_bridge').attr('src', data.smallthumb+ '?rnd='+ Math.random());
		  $('#removelogo').hide();
		}else{
		  alert (data.error);
		}
					 
	  }
	}; 
	$('#frmcode').ajaxSubmit(options); 
	return false;
  });
		   
  /** bridge graphics logo upload  **/
  $('#upload_graphic').live('change', function(){
	var width = $(window).width()/2-34;
	var height =$(window).height()/2-34;
				
	$('.loaderdiv').css("width",width);
	$('.loaderdiv').css("height",height);
	$('#loaderimg').css("padding-left","103%");
	$('.loaderdiv').show();
	var options = { 
	  url:'<?php echo URL::base(); ?>code/getData',
	  success: function(data){
		$('.loaderdiv').hide();
		var data = $.parseJSON(data);
		if(data.status == '1'){
		  $('#bridge_image').attr('src','');
		  $('#bridge_image').attr('src', data.thumb + '?rnd='+ Math.random());
		  $('#small_bridge').attr('src', data.smallthumb+ '?rnd='+ Math.random());
		}else{
		  alert (data.error);
		}
					 
	  }
	}; 
	$('#frmcode').ajaxSubmit(options); 
	return false;
  });
		   
 
</script>
