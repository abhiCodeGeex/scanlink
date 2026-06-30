
<?php echo $innermenu; ?>
<?php if ($errormessage != ""): ?>
  <div class="row-r"><label  class="error"><?php echo $errormessage; ?></label></div>
  <div class="clear">&nbsp;</div>
<?php endif; ?>
<?php if ($successmessage != ""): ?>
  <div class="row-r"><label class="success" ><?php echo $successmessage; ?></label></div>
  <div class="clear">&nbsp;</div>
<?php endif; ?>
<?php /* <link  type="text/css" href="<?php echo url::base(); ?>styles/farbtastic.css"  rel="stylesheet" media="screen"/>
  <script  type="text/javascript" src="<?php echo url::base(); ?>js/farbtastic.js"></script>
  <link  type="text/css" href="<?php echo url::base(); ?>styles/jquery.tooltip.css"  rel="stylesheet" media="screen"/>
  <script type="text/javascript" src="<?php echo url::base(); ?>js/jquery.tooltip.js"></script> */ ?>

<?php 
if ($add_form_display) {
	if(!$expried_or_not || true){
		  $url = URL::base() . 'code/index';
		  if ($mode == 'edit') {
			$url .= '/' . $profile_id;
		  }
		  ?>
		  <?php
		  echo Form::open(
				  $url, array(
			  'id' => 'frmcode',
			  'name' => 'frmcode',
			  'method' => 'post',
			  'enctype' => 'multipart/form-data'
				  )
		  );
		  ?>
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
	<div class="scanlink-container">	
		  <section class="add-form-right-code"><!-- ADD FORM RIGHT START -->
			<div class="SectionTitleBox">
			  	
				<div class="form-view clearfix">
					
					<div align="center">
						<div class="graybar_preview">Code Preview</div>
						<div class="code_preview_qr">
						  <?php if (isset($feilds['qrimg']) && $feilds['qrimg'] != '') { ?>
							<img id="qrcode-preview" src="<?php echo URL::base() . $feilds['qrimg'].'?'.rand(11111111,99999999); ?>" width="185px"  height="185px"/>
			
						  <?php } ?>
						</div>
					</div>
					<div>&nbsp;</div>
					<div align="center" class="code_review">
						<section class="button-section" style="margin: 0px; width: 50%;text-align: left;">
					        <select name="download_option" id="download_option" class="noUniform">
							  	<option value="" selected="">Download As</option>
							  	<option value="pdf">PDF</option>
							  	<option value="tiff">TIFF</option>
							  	<option value="eps">Eps(vector)</option>
							  	<option value="png">PNG</option>
		  						<option value="jpg">JPG</option>
							</select>
						</section>
						
						<div class="download_div">
							<button type="button" onclick="download_qrcode('<?php echo $profile_id;?>');" class="download_code_as">DOWNLOAD</button>
						</div>
				
						
					</div>
				</div>
			</div>
						
			
		  </section><!-- /ADD FORM RIGHT END-->
		
		  <section class="add-form-left"><!-- ADD FORM LEFT START -->
			<div class="SectionTitleBox">
			  <div class="SectionTitleCode">Code no <?php echo $profile_id; ?></div>
			  <ul class="form-view clearfix">
				<li>
				  <label><span class="codelabel tooltip" title="This is for you to reference where you have applied you QR Code">Application: </span></label>
				  <?php
				  echo Form::input(
						  'application', isset($feilds['application']) ? $feilds['application'] : '', array(
					  'id' => 'application',
					  'class' => 'text-fi codeTextApplication'
						  )
				  );
				  ?> 
				  <?php
				  echo Form::label(
						  'application', isset($errors['application']) ? $errors['application'] : '', array(
					  'class' => 'error',
					  'generated' => 'true'
						  )
				  );
				  ?>
				</li>
				<li>
				  <label>Enter the Web page address(URL) here:<span class="codelabel tooltip" title="This is where you enter the URL (web address) To where you want to redirect mobile users when they they scan your QR code."></span></label>
				  <?php
				  echo Form::input(
						  'destination_url', isset($feilds['destination_url']) ? $feilds['destination_url'] : 'http://', array(
					  'id' => 'destination_url',
					  'class' => 'text-fi'
						  )
				  );
				  ?> 
				  <?php
				  echo Form::label(
						  'destination_url', isset($errors['destination_url']) ? $errors['destination_url'] : '', array(
					  'class' => 'error',
					  'generated' => 'true'
						  )
				  );
				  ?>
		
				</li>
				<li>
		
				</li>
			  </ul>
			  
			  <ul class="form-view clearfix">
				<li>
				  <label style="float: left;">Enable Data Collection Pop Up Window:<span class="codelabel tooltip"  title="The Data Collection option enables you to feature a pop up window with a customised message to collect mobile user information which is stored in your Visitor Log."></span></a></label>
				  <div style="float: left; width: 350px;">
					<?php
					echo Form::radio(
							'enable_data_collection', '1', isset($feilds['enable_data_collection']) ? $feilds['enable_data_collection'] ? true : false  : false, array('class' => 'radi enable_data_collection'
							)
					);
					?>
					<?php
					echo Form::label(
							'enable_data_collection', 'Yes', array(
						'style' => 'display: inline;'
							)
					);
					?>
					<?php
					echo Form::radio(
							'enable_data_collection', '0', isset($feilds['enable_data_collection']) ? $feilds['enable_data_collection'] ? false : true  : true, array(
						'class' => 'radi enable_data_collection'
							)
					);
					?>
					<?php
					echo Form::label(
							'enable_data_collection', 'No', array(
						'style' => 'display: inline;'
							)
					);
					?>
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
				<li>
				  <div style="float: left; width: 350px;">
					<?php
					echo Form::checkbox(
							'data_collection_mobile', '1', isset($feilds['data_collection_mobile']) ? $feilds['data_collection_mobile'] ? true : ''  : '', array(
						'id' => 'data_collection_mobile'
							)
					);
					?>
					<?php
					echo Form::label(
							'data_collection_mobile', 'Mobile', array(
						'style' => 'display: inline;'
							)
					);
					?>
		
					<?php
					echo Form::checkbox(
							'data_collection_email', '1', isset($feilds['data_collection_email']) ? $feilds['data_collection_email'] ? true : ''  : '', array(
						'id' => 'data_collection_email'
							)
					)
					?>
					<?php
					echo Form::label(
							'data_collection_email', 'Email', array(
						'style' => 'display: inline;'
							)
					);
					?>
		
					<?php
					echo Form::checkbox(
							'data_collection_name', '1', isset($feilds['data_collection_name']) ? $feilds['data_collection_name'] ? true : ''  : '', array(
						'id' => 'data_collection_name'
							)
					);
					?>
					<?php
					echo Form::label(
							'data_collection_name', 'Name', array(
						'style' => 'display: inline;'
							)
					);
					?>
					
					<?php
					echo Form::checkbox(
							'data_collection_surname', '1', isset($feilds['data_collection_surname']) ? $feilds['data_collection_surname'] ? true : ''  : '', array(
						'id' => 'data_collection_surname'
							)
					);
					?>
					<?php
					echo Form::label(
							'data_collection_surname', 'Surname', array(
						'style' => 'display: inline;'
							)
					);
					?>
				  </div>
				</li>
				<li>
				  <label >pop up message:</label>
				  <?php
				  echo Form::textarea(
						  'txtdesc', isset($feilds['txtdesc']) ? $feilds['txtdesc'] : '', array(
					  "rows" => 1,
					  "cols" => 40,
					  "class" => "input",
					  'id' => 'txtdesc'
						  )
				  );
				  ?> 
				  <?php
				  echo Form::label(
						  'txtdesc', isset($errors['txtdesc']) ? $errors['txtdesc'] : '', array(
					  'class' => 'error',
					  'generated' => 'true'
						  )
				  );
				  ?>
				</li>
				<li style="width: 100%;">
				  <div class="">
					
						<div style="float:left;">
							  <div class="color-picker">
								<div class="color-picker-label">Colour Selector</div>
								<input type="hidden" id="color" name="color" value="<?php if(isset($feilds["color_code"])) echo $feilds["color_code"];  else echo '#000000';?>" />
								<div id="picker">
								  <div class="farbtastic">
									<div class="color" style="background-color: rgb(255, 0, 0);"></div>
									<div class="wheel"></div>
									<div class="overlay"></div>
									<div class="h-marker marker" style="left: 97px; top: 13px;"></div>
									<div class="sl-marker marker" style="left: 147px; top: 147px;"></div>
								  </div>
								</div>
							  </div>
						</div>
						<div class="code_tp">
							Select Code Type
							<div class="clear"></div>
							<div style="clear: both;float: none;">
								<div class="rdo">
					            	<label>QR</label>
					              	<input type="radio" name="code_type" class="type_of_code" value="0" <?php if(!isset($feilds["code_type"]) || (isset($feilds["code_type"]) && $feilds["code_type"]=="0") ) echo 'checked="checked"' ;?>  />
					            </div>
					            <div class="rdo">
					            	<label>DM</label>
					              	<input type="radio" name="code_type" class="type_of_code" value="1" <?php if(isset($feilds["code_type"]) && $feilds["code_type"]=="1") echo 'checked="checked"' ;?>  />
					            </div>
							</div>
						</div> 
					</div>
				</li>
			  </ul>
			  
			  	<div style=" width: 150px;margin:auto;" align="center">
			  		<input name="save" id="save" type="submit" class="inline" value="Save" onclick='javascript: beforeSave();'>  
				</div>
			</div>
		
		  </section><!-- /ADD FORM RIGHT END -->
		
		
		  <?php echo Form::close() ?> 
		  <div id="progressbar"> <img src="<?php echo url::base(); ?>images/spinner_gray.gif" align="absmiddle" /> Please Wait. Saving Profile... </div>
		</div>
		 <!--  <script type="text/javascript" src="http://malsup.github.com/jquery.form.js"></script> -->
         <script type="text/javascript" src="<?php echo url::base(); ?>js/jquery.form.js"></script>
		
		  <script type="text/javascript">
			$(document).ready(function(){
						 
			  /**  length of textbox area **/  
			  jQuery('#data_collection_content').maxlength( {slider: true, maxCharacters: 150} ); 
			  jQuery('#txtdesc').maxlength( {slider: true, maxCharacters: 150} ); 
						  
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
			var request;
			
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
					 
			/** QR code color picker **/
			$('#picker').farbtastic('#color');
					
		  <?php if ($mode == 'edit') { ?>
			  /** QR code color picker color save **/
			  $('#picker').live('mouseup', function(){
				var width = $(window).width()/2-34;
				var height =$(window).height()/2-34;
				$('.loaderdiv').css("width",width);
				$('.loaderdiv').css("height",height);
				$('#loaderimg').css("padding-left","103%");
				$('.loaderdiv').show();
				var color = $('#color').val();
				
				if(typeof request !== 'undefined') //to abort previous ajax request
        			request.abort();
				
				request = $.ajax({
					  type:'post',
					  data:{
						"color":color,
						"profile_id":'<?php echo $profile_id; ?>'
					  },
					  url:'<?php echo URL::base(); ?>code/qrCodeColor',
					  success:function(data){
						$('.loaderdiv').hide();
						// console.log(data);return ;
						var data = $.parseJSON(data);
										  
						console.log(data);
						console.log(data.codeImage);
						if(data){
						  $("#qrcode-preview").attr("src",data.codeImage + '?rand='+Math.random());
						}
					  }
				});
				
				
			  });
		  <?php } ?>
		  </script>
		  <script type="text/javascript">
			  $(function(){
				$('.tooltip').tooltip({ 
				  track: true,
				  delay: 0,
				  showURL: false,
				  fixPNG: true,
				  showBody: " - ",
				  extraClass: "pretty fancy",
				  top: -15,
				  left: 5
				});
			  });
			</script>
    <?php }else{ ?>
    	<p>You can not perform this action on expired profile.</p>
    <?php } ?>
<?php } else { ?>
  <p> You can not add code.kindly purchase the code.</p>
<?php } ?>
<script>
	function download_qrcode(profile_id)	
	{	
		download_as = $("#download_option").val();
		
		
		if(!$('#qrcode-preview').length)
		{
			alert("Please create code first");
			return false;
		}
		
				
		if(download_as=="")
		{
			alert("Please select download as");
			return false;
		}
		
		if(download_as=="pdf")
			location.href='<?php echo URL::base(); ?>code/downloadQrpdf/'+profile_id;  
		else
			location.href='<?php echo URL::base(); ?>code/downloadQrImage/'+profile_id+'?download_as='+download_as;  
	}
</script>

