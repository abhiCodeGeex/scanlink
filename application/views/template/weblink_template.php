<?php 
if (isset($post['weblink']) && is_array($post['weblink']) && !empty($post['weblink'])) {
    
} else {
    $post['weblink'] = array();
    $post['weblink'][0] = array();
    $post['weblink'][0]['link_button'] = false;
    $post['weblink'][0]['link_button_text'] = '';
    $post['weblink'][0]['link_button_url'] = '';
    $post['weblink'][0]['link_button_color'] = '007A01';
}

$totalweblinkcount = count($post['weblink']);
?>

<?php $web_link_count =0;
 foreach ($post['weblink'] as $key => $myweblink) { ?>
    <div id="weblinkdata<?php echo isset($myweblink['id']) ? $myweblink['id'] : ''; ?>">
        <div class="SectionTitleBox">
            <div class="SectionTitle">Web Link
					<img src="<?php echo url::base(); ?>images/help_icon.jpg" class="help-icon" style="vertical-align:text-bottom;" />
					<div class="help-div" style="margin-left: 87px;"><a href="https://www.youtube.com/embed/id0I8j8RTuY?rel=0">Video Tutorial</a>
						<div class="arrow-bg"></div>
					</div>
					<?php if($web_link_count==0)
					{
					?>
						<img src="<?php echo url::base(); ?>images/drag.png"  class="drag-icon" style="display: none;" />
					<?php
					}
					?>
			</div>
            <ul class="form-view clearfix">

                <li>
                    <div style="float: left; width: 350px;">
                        <?php echo Form::checkbox('link_button[]', '1', isset($myweblink['link_button']) ? (bool) $myweblink['link_button'] : false, array('id' => 'link_button' . ($key + 1),'class'=>'link_button_checkbox')) ?>
                        <?php echo Form::label('link_button', 'Add Button', array('style' => 'display: inline;')) ?>
                    </div>
                </li>

                <li class="width-half">
                    <label><?php echo Form::label('comp', 'Button Text:') ?></label>
                    <?php echo Form::input('link_button_text[]', isset($myweblink['link_button_text']) ? $myweblink['link_button_text'] : '', array('id' => 'txtcompname' . ($key + 1), 'class' => 'text-fi')) ?>
                    <?php echo Form::label('link_button_text', isset($errors['link_button_text']) ? $errors['link_button_text'] : '', array('id' => 'errcomp', 'class' => 'error', 'generated' => 'true')) ?>
                </li>
                <li class="width-half">
                    <label style="margin:0;padding: 0;float: left;width:100%;"><?php echo Form::label('comp', 'Button Link URL:',array('style'=>'float:left;')) ?> <p style="float: right;margin:0 20px 0 0px;padding: 0;">(Start with http://)</p></label>
                    <?php echo Form::input('link_button_url[]', isset($myweblink['link_button_url']) ? $myweblink['link_button_url'] : '', array('id' => 'txtcompname' . ($key + 1), 'class' => 'text-fi')) ?>
                    <?php echo Form::label('link_button_url', isset($errors['link_button_url']) ? $errors['link_button_url'] : '', array('id' => 'errcomp', 'class' => 'error', 'generated' => 'true')) ?>
                </li>
					
					<?php
						if(isset($myweblink['link_button_align']))
							$link_button_align = $myweblink['link_button_align'];
						else 
							$link_button_align = "left";
						 
					?>
					 <li class="clear" style="width:100%;">
                    <label style="margin:0;padding: 0;float: left;width:100%;"><?php echo Form::label('comp', 'Button Text Alignment:',array('style'=>'float:left;')) ?> </label>
                    <input type="radio" value="left" name="link_button_align_<?php echo ($key + 1);?>"  <?php if($link_button_align=='left') echo 'checked="checked"';?> />Left&nbsp;
                    <input type="radio" value="center" name="link_button_align_<?php echo ($key + 1);?>" <?php if($link_button_align=='center') echo 'checked="checked"';?> />Center&nbsp;
                    <input type="radio" value="right" name="link_button_align_<?php echo ($key + 1);?>" <?php if($link_button_align=='right') echo 'checked="checked"';?> />Right&nbsp;
               
						  <input type="hidden" class="link_button_active" name="link_button_active_<?php echo ($key + 1);?>" id="link_button_active_<?php echo ($key + 1);?>" value="<?php echo (isset($myweblink['link_button']) && $myweblink['link_button']=='1') ? 'yes' : 'no';?>">	               
                </li>

                <li class="width-half">
                    <label><?php echo Form::label('comp', 'Button color:') ?></label>
                    <?php echo Form::input('link_button_color[]', isset($myweblink['link_button_color']) ? $myweblink['link_button_color'] : '', array('id' => 'link_button_color' . ($key + 1), 'class' => 'text-fi color {required:false}')) ?>
                    <?php echo Form::label('link_button_color', isset($errors['link_button_color']) ? $errors['link_button_color'] : '', array('id' => 'errcomp', 'class' => 'error', 'generated' => 'true')) ?>
                </li>

					 
                <?php if ($key == 0) { ?>
                    <li class="width-half">
                        <label>&nbsp;</label>
                        <input name="another_web_link" id="another_web_link" type="button" class="inline" value="And another web link">  
                    </li>
                <?php } else { ?>
                    <li class="width-half button-section">
                        <label>&nbsp;</label>
                        <a class="gray-btn" href="javascript:void(0);" onclick="return deleteweblink('<?php echo isset($myweblink['id']) ? $myweblink['id'] : ''; ?>')" style=" height: 28px;line-height: 28px;">Delete this link</a>
                    </li>
                <?php } ?>

            </ul>
        </div>
    </div>
<?php $web_link_count++;
} ?>
<div id="another_weblink"></div>


<script type="text/javascript">
    var totalweblink = <?php echo $totalweblinkcount; ?>;
    
	$('body').on('click','.link_button_checkbox',function () {
			if($(this).prop('checked')==true)
				$(this).closest('ul').find('.link_button_active').val('yes');
			else
				$(this).closest('ul').find('.link_button_active').val('no');
				
			
	})    
    
    $('#another_web_link').live('click',function(){
	 
        //$('#progressbar_weblink').show();
        totalweblink = totalweblink + 1;
        var weblink_html = '';
        weblink_html += '<div class="SectionTitleBox new_weblink_'+totalweblink+'">';
        weblink_html +='<div class="SectionTitle">Web Link</div>';
        weblink_html +='<ul class="form-view clearfix">';
        weblink_html +='<li>';
        weblink_html +='<div style="float: left; width: 350px;">';
        weblink_html +='<input type="checkbox" value="1" name="link_button[]" class="link_button_checkbox" id="link_button'+totalweblink+'" >';
        weblink_html +='<label for="link_button" style="display: inline;">Add Button</label>';
        weblink_html +='</div>';
        weblink_html +='</li>';
        weblink_html +='<li class="width-half">';
        weblink_html +='<label><label for="comp">Button Text:</label></label>';
        weblink_html +='<input type="text" class="text-fi" value="" name="link_button_text[]" id="txtcompname'+totalweblink+'">';
        weblink_html +='</li>';
        weblink_html +='<li class="width-half">';
        weblink_html +='<label><label for="comp" style="float:left;">Button Link URL:</label><p style="float: right;margin:0 20px 0 0;padding: 0;">(Start with http://)</p></label>';
        weblink_html +='<input type="text" class="text-fi" value="" name="link_button_url[]" id="txtcompname'+totalweblink+'">';
        weblink_html +='</li>';
        
        weblink_html +='<li class="clear" style="width:100%;">';
        weblink_html +='<label><label for="comp">Button Text Alignment:</label></label>';
        weblink_html +='<input type="radio" value="left" name="link_button_align_'+totalweblink+'"  checked="checked" />Left &nbsp;';
        weblink_html +='<input type="radio" value="center" name="link_button_align_'+totalweblink+'" />Center &nbsp;';
        weblink_html +='<input type="radio" value="right" name="link_button_align_'+totalweblink+'" />Right &nbsp;';
		  weblink_html +='<input type="hidden" class="link_button_active" name="link_button_active_'+totalweblink+'" id="link_button_active_'+totalweblink+'" value="no">';       
        weblink_html +='</li>';
        
        weblink_html +='<li class="width-half">';
        weblink_html +='<label><label for="comp">Button color:</label></label>';
        weblink_html +='<input type="text" class="text-fi color {required:false}" value="#007A01" name="link_button_color[]" id="link_button_color'+totalweblink+'" autocomplete="off">';
        weblink_html +='</li>';
        weblink_html += '</ul>';
        weblink_html += '</div>';
		
        $('#another_weblink').append(weblink_html);
        $( $('#link_button'+totalweblink)).uniform();
        $(".new_weblink_"+totalweblink+ " :radio").uniform();
        var colinput = document.getElementById('link_button_color' + totalweblink);
        var col = new jscolor.color(colinput);
		
    });
  
    function deleteweblink(weblinkid){
	  
        if(confirm("Are you sure you want to delete this weblink permanently.")){
			var copyProgress = $("#progressbar").clone();
      		copyProgress.attr('id','progressbar1');
		    copyProgress.html('<img align="absmiddle" src="<?php echo URL::base(); ?>images/spinner_gray.gif"> Please Wait. Deleting Web Link...');
		    $("#progressbar").after(copyProgress);
		    $("#progressbar1").show();
			 
            var request = $.ajax({
                url:'<?php echo URL::base(); ?>dashboard/deleteweblink',
                type: "POST",
                dataType: "json",
                data:{
                    weblinkid : weblinkid,
                    client_id  : '<?php echo $client_id ?>',
                    profile_id  : '<?php echo $profile_id ?>'
                }
			
            });
            request.done(function(data) {
				$("#progressbar1").remove();
                if(data.error == 1){
                    alert(data.message);
                    return false;
                }else{
                    $("#weblinkdata"+weblinkid).empty();
                    alert(data.message);
                    return false;
                }
            });
        }
    }
  	
</script>