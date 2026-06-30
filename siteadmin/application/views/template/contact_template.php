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
  <div class="main-row padbot">
    <div class="row padbot padtop2">&nbsp;</div>
    <div class="row-r padbot"> <?php echo Form::label('comp', 'CONTACT') ?> <?php echo Form::label('tele', 'TELEPHONE',array('class'=>'pad')) ?> </div>
  </div>
  <div class="main-row padtop">
    <div class="row padtop">&nbsp;</div>
    <div class="row-r padtop"> 
		<?php echo Form::input('txtcompname[]',isset($mycontact['name_company']) ? $mycontact['name_company'] : '', array('id' => 'txtcompname'.($key + 1),'class'=>'input-w')) ?>
		<?php echo Form::input('txttelephone[]',isset($mycontact['telephone']) ? $mycontact['telephone'] : '', array('id' => 'txttelephone'. ($key + 1),'class'=>'input-w')) ?> 
		<?php echo Form::label('txtcompname', isset($errors['txtcompname']) ? $errors['txtcompname']:'', array('id'=>'errcomp','class'=>'error','generated'=>'true')) ?> 
		<?php echo Form::label('txttelephone',isset($errors['txttelephone']) ? $errors['txttelephone'] : '', array('id'=>'errtele','class'=>'error','generated'=>'true')) ?> 
    </div>
  </div>
  <?php if ($key == 0) { ?>
  		<div class="main-row padbot">
  			<div class="row padbot padtop2">&nbsp;</div>
			<div class="row-r-new padbot">
			    <input name="another_contact" id="another_contact" type="button" class="save" value="And another">
	  		</div>
		</div>
  <?php } else { ?>
  		<div class="main-row padbot">
  			<div class="row padbot padtop2">&nbsp;</div>
			<div class="row-r-new padbot">
            	<a class="view-map delete_link" href="javascript:void(0);" onclick="return deletecontact('<?php echo isset($mycontact['id']) ? $mycontact['id'] : ''; ?>')" >Delete this contact</a> 
            </div>
		</div>
  <?php } ?>
</div>
<?php } ?>

<div id="another_contact_div"></div>
<script type="text/javascript">
    var totalcontact = <?php echo $totalcontactcount; ?>;
    $('#another_contact').live('click',function(){
	 	totalcontact = totalcontact + 1;
        var contact_html = '';
		contact_html += '<div class="main-row padbot">';
			contact_html += '<div class="row padbot padtop2">&nbsp;</div>';
			contact_html += '<div class="row-r padbot">';
				contact_html += '<label for="comp" class="pad">CONTACT:</label>';
				contact_html += '<label for="tele" class="pad">TELEPHONE:</label>';
			contact_html += '</div>';
		contact_html += '</div>';
		contact_html += '<div class="main-row padbot">';
			contact_html += '<div class="row padbot padtop2">&nbsp;</div>';
			contact_html += '<div class="row-r padbot">';
				contact_html += '<input type="text" class="input-w" value="" name="txtcompname[]" id="txtcompname'+totalcontact+'">';
				contact_html += '<label for="txtcompname" generated="true" class="error" id="errcomp"></label>';
				contact_html += '<input type="text" class="input-w" value="" name="txttelephone[]" id="txttelephone'+totalcontact+'">';
				contact_html += '<label for="txttelephone" generated="true" class="error" id="errtele"></label>';
			contact_html += '</div>';
		contact_html += '</div>';
		
        $('#another_contact_div').append(contact_html);
    });
  
    
  	function deletecontact(contactid){
	  
        if(confirm("Are you sure you want to delete this contact permanently.")){
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
