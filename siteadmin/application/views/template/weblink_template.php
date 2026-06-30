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

         
<?php foreach ($post['weblink'] as $key => $myweblink) { ?>
    <div id="weblinkdata<?php echo isset($myweblink['id']) ? $myweblink['id'] : ''; ?>">
    	
        <div class="main-row">
                  <div class="row"><?php if ($key == 0) { ?>WebLink<?php } else{ ?> &nbsp;<?php } ?></div>
                  <div class="row-r-2"> 
				  	<?php echo Form::checkbox('link_button[]', '1', isset($myweblink['link_button']) ? (bool) $myweblink['link_button'] : false, array('id' => 'link_button' . ($key + 1))) ?>
                    <?php echo Form::label('link_button', 'Add Button', array('style' => 'display: inline;')) ?>
                  </div>
         </div>
         
        <div class="main-row padbot">
            <div class="row padbot padtop2">&nbsp;</div>
            <div class="row-r padbot"> 
				<?php echo Form::label('comp', 'Button Text:',array('class'=>'pad')) ?><?php echo Form::label('comp', 'Button Link:',array('class'=>'pad')) ?>  
          	</div>
         </div>
          <div class="main-row padtop">
            <div class="row padtop">&nbsp;</div>
            <div class="row-r padtop"> 
            	<?php echo Form::input('link_button_text[]', isset($myweblink['link_button_text']) ? $myweblink['link_button_text'] : '', array('id' => 'txtcompname' . ($key + 1), 'class' => 'input-w')) ?>
                 <?php echo Form::input('link_button_url[]', isset($myweblink['link_button_url']) ? $myweblink['link_button_url'] : '', array('id' => 'txtcompname' . ($key + 1), 'class' => 'input-w')) ?>
                 <?php echo Form::label('link_button_text', isset($errors['link_button_text']) ? $errors['link_button_text'] : '', array('id' => 'errcomp', 'class' => 'error', 'generated' => 'true')) ?>
                 <?php echo Form::label('link_button_url', isset($errors['link_button_url']) ? $errors['link_button_url'] : '', array('id' => 'errcomp', 'class' => 'error', 'generated' => 'true')) ?>
            </div>
          </div>
          
          <div class="main-row padbot">
            <div class="row padbot padtop2">&nbsp;</div>
            <div class="row-r padbot"> 
				<?php echo Form::label('buttoncolor', 'Button color:',array('class'=>'pad')) ?>  
          	</div>
         </div>
         
          <div class="main-row padtop">
                  <div class="row padtop">&nbsp;</div>
                  <div class="row-r padtop"> 
				  	<?php echo Form::input('link_button_color[]', isset($myweblink['link_button_color']) ? $myweblink['link_button_color'] : '', array('id' => 'link_button_color' . ($key + 1), 'class' => 'input-w color {required:false}')) ?>
                    <?php echo Form::label('link_button_color', isset($errors['link_button_color']) ? $errors['link_button_color'] : '', array('id' => 'errcomp', 'class' => 'error', 'generated' => 'true')) ?> 
                  </div>
         </div>
                
          <?php if ($key == 0) { ?>
                <div class="main-row padbot">
                    <div class="row padbot padtop2">&nbsp;</div>
                    <div class="row-r-new padbot">
                        <input name="another_web_link" id="another_web_link" type="button" class="save" value="And another web link">  
                    </div>
                </div>
          <?php } else { ?>
                <div class="main-row padbot">
                    <div class="row padbot padtop2">&nbsp;</div>
                    <div class="row-r-new padbot">
                        <a class="view-map" href="javascript:void(0);" onclick="return deleteweblink('<?php echo isset($myweblink['id']) ? $myweblink['id'] : ''; ?>')" style=" height: 28px;line-height: 28px;">Delete this link</a>
                    </div>
                </div>
          <?php } ?>
    </div>
<?php } ?>
<div id="another_weblink"></div>


<script type="text/javascript">
    var totalweblink = <?php echo $totalweblinkcount; ?>;
    $('#another_web_link').live('click',function(){
	 
        //$('#progressbar_weblink').show();
        totalweblink = totalweblink + 1;
        var weblink_html = '';
		
		
		weblink_html += '<div class="main-row">';
		weblink_html += '<div class="row">&nbsp;</div>';
		weblink_html += '<div class="row-r-2">';
		weblink_html += '<input type="checkbox" value="1" name="link_button[]" id="link_button'+totalweblink+'" >';
		weblink_html += '<label for="link_button" style="display: inline;">Add Button</label>';
		weblink_html += '</div>';
		weblink_html += '</div>';
			 
		weblink_html += '<div class="main-row padbot">';
		weblink_html += '<div class="row padbot padtop2">&nbsp;</div>';
		weblink_html += '<div class="row-r padbot"> ';
		weblink_html += '<label for="comp" class="pad">Button Text:</label>';
		weblink_html += '<label for="comp" class="pad">Button Link:</label>';
		weblink_html += '</div>';
		weblink_html += '</div>';
		
		weblink_html += '<div class="main-row padtop">';
		weblink_html += '<div class="row padtop">&nbsp;</div>';
		weblink_html += '<div class="row-r padtop"> ';
		weblink_html += '<input type="text" class="input-w" value="" name="link_button_text[]" id="txtcompname'+totalweblink+'">';
		weblink_html += '<input type="text" class="input-w" value="" name="link_button_url[]" id="txtcompname'+totalweblink+'">';
		weblink_html += '</div>';
		weblink_html += '</div>';
		
		weblink_html += '<div class="main-row padbot">';
		weblink_html += '<div class="row padbot padtop2">&nbsp;</div>';
		weblink_html += '<div class="row-r padbot"> ';
		weblink_html += '<label for="comp" class="pad">Button color:</label>';
		weblink_html += '</div>';
		weblink_html += '</div>';
		
		weblink_html += '<div class="main-row padtop">';
		weblink_html += '<div class="row padtop">&nbsp;</div>';
		weblink_html += '<div class="row-r padtop"> ';
		weblink_html += '<input type="text" class="input-w color {required:false}" value="#007A01" name="link_button_color[]" id="link_button_color'+totalweblink+'" autocomplete="off">';
		weblink_html += '</div>';
		weblink_html += '</div>';
		
        $('#another_weblink').append(weblink_html);
        //$( $('#link_button'+totalweblink)).uniform();
        var colinput = document.getElementById('link_button_color' + totalweblink);
        var col = new jscolor.color(colinput);
		
    });
  
    function deleteweblink(weblinkid){
	  
        if(confirm("Are you sure you want to delete this weblink permanently.")){
			
			 
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