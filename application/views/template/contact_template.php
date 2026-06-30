<?php
if (isset($post['contact']) && is_array($post['contact']) && !empty($post['contact'])) {
    
} else {
    $post['contact'] = array();
    $post['contact'][0] = array();
    $post['contact'][0]['txtcompname'] = '';
    $post['contact'][0]['txttelephone'] = '';
}

$totalcontactcount = count($post['contact']);
?>

<?php foreach ($post['contact'] as $key => $mycontact) { ?>
    <div id="contactdata<?php echo isset($mycontact['id']) ? $mycontact['id'] : ''; ?>">
      <li class="width-half">
        <label><?php echo Form::label('comp', 'CONTACT:') ?></label>
        <?php echo Form::input('txtcompname[]', isset($mycontact['name_company']) ? $mycontact['name_company'] : '', array('id' => 'txtcompname'.($key + 1), 'class' => 'text-fi')) ?>
        <?php echo Form::label('txtcompname', isset($errors['txtcompname']) ? $errors['txtcompname'] : '', array('id' => 'errcomp', 'class' => 'error', 'generated' => 'true')) ?>
      </li>
      <li class="width-half">
        <label><?php echo Form::label('tele', 'TELEPHONE:', array('class' => 'pad')) ?></label>
        <?php echo Form::input('txttelephone[]', isset($mycontact['telephone']) ? $mycontact['telephone'] : '', array('id' => 'txttelephone'. ($key + 1), 'class' => 'text-fi')) ?>	
        <?php echo Form::label('txttelephone', isset($errors['txttelephone']) ? $errors['txttelephone'] : '', array('id' => 'errtele', 'class' => 'error', 'generated' => 'true')) ?>                      	
      </li>
      <?php if ($key == 0) { ?>
            <li class="width-half fright_contact">
                <label>&nbsp;</label>
                <div class="fright">
                	<input name="another_contact" id="another_contact" type="button" class="inline" value="And another">  
                </div>
            </li>
      <?php } else { ?>
            <li class="width-half button-section fright_contact">
                <label>&nbsp;</label>
                <div class="fright">
	                <a class="gray-btn delete_link" href="javascript:void(0);" onclick="return deletecontact('<?php echo isset($mycontact['id']) ? $mycontact['id'] : ''; ?>')" >Delete this contact</a>
                </div>
            </li>
      <?php } ?>
    </div>
<?php } ?>
<div id="another_contact_div"></div>


<script type="text/javascript">
    var totalcontact = <?php echo $totalcontactcount; ?>;
    $('#another_contact').live('click',function(){
	 
        totalcontact = totalcontact + 1;
        var contact_html = '';
		contact_html += '<li class="width-half">';
			contact_html += '<label><label for="comp">CONTACT:</label></label>';
			contact_html += '<input type="text" class="text-fi" value="" name="txtcompname[]" id="txtcompname'+totalcontact+'">';
			contact_html += '<label for="txtcompname" generated="true" class="error" id="errcomp"></label>';
        contact_html += '</li>';
		contact_html += '<li class="width-half">';
			contact_html += '<label><label for="tele" class="pad">TELEPHONE:</label></label>';
			contact_html += '<input type="text" class="text-fi" value="" name="txttelephone[]" id="txttelephone'+totalcontact+'">';
			contact_html += '<label for="txttelephone" generated="true" class="error" id="errtele"></label>';
        contact_html += '</li>';
		
        $('#another_contact_div').append(contact_html);
		
    });
  
    function deletecontact(contactid){
	  
        if(confirm("Are you sure you want to delete this contact permanently.")){
			
			 var copyProgress = $("#progressbar").clone();
      		 copyProgress.attr('id','progressbar1');
		     copyProgress.html('<img align="absmiddle" src="<?php echo URL::base(); ?>images/spinner_gray.gif"> Please Wait. Deleting Contact...');
		     $("#progressbar").after(copyProgress);
		     $("#progressbar1").show();
	  
            var request = $.ajax({
                url:'<?php echo URL::base(); ?>dashboard/deletecontact',
                type: "POST",
                dataType: "json",
                data:{
                    contactid : contactid,
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
                    $("#contactdata"+contactid).empty();
                    alert(data.message);
                    return false;
                }
            });
        }
    }
  	
</script>