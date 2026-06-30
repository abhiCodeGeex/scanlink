<div class="container">
<div class="contant inner-box">

<?php echo Form::open('', array('id'=>'frm_subdivide_step4_client','name'=>'frm_subdivide_step4_client','method'=>'post','class'=>'subdivide')) ?>


<input type="hidden" id="client" name="client" value="<?php echo $client; ?>" />
<?php foreach($profiles as $key => $profile_id){ ?>
<input type="hidden" id="profiles_<?php echo $key; ?>" name="profiles[]" value="<?php echo $profile_id; ?>" />
<?php } ?>
<?php foreach($new_client as $key => $value){ ?>
<input type="hidden" id="new_client_<?php echo $key; ?>" name="new_client[<?php echo $key; ?>]" value="<?php echo $value; ?>" />
<?php } ?>
<input type="hidden" id="from_users" name="from_users" value="temp_user" />
<div class="penal">
<h3>
     <span class="fleft">*&nbsp;Please select one or multiple users whom you want to be under new client</span>
     <span class="fright">&nbsp;</span>
	<div class="clear"></div>
</h3>

<?php if( ($errors || $succmsg)){ ?>
<div class="main-row">
	<div class="row"></div>
	<div class="row-r">
		<div class="mymsg" style=" <?php echo $succmsg ? 'color:#35D10A' : 'color:#F00';?>; <?php echo $succmsg ? 'border-color:#35D10A' : 'border-color:#F00';?>;"><?php if( ($errors || $succmsg)){ echo $errors ? $errors: ($succmsg ? $succmsg: 'k'); }?></div>
	</div>
</div>
<?php } ?>

<div class="main-row">
	<div class="row"></div>
     <div class="row-r">
     	<table width="100%">
          	<tr><th>Click on User which you want to transfer</th>
               	<th>Click to send it back to old client</th></tr>
     	    	<tr><td width="50%">
                    <select id="old_users" name="old_users[]" class="input multi" multiple="multiple" size="<?php  echo(count($users)>5)?count($users):5; ?>">
	                    <option class="head" value="" disabled>Users in Old Client</option>
                         <?php foreach($users as $user){ if($user['role']==5) continue; ?>
                         <option class="sub" value="<?php echo $user['id']; ?>"><?php echo $user['email']; ?></option>
                         <?php } ?>
                    </select>
	          </td>
               <td>
                    <select id="users" name="users[]" class="input multi" multiple="multiple" size="<?php  echo(count($users)>5)?count($users):5; ?>">
	                    <option class="head" value="" disabled>Users in New Client</option>
                    </select>
               </td></tr>
          </table>
     </div>
</div>
<div class="main-row">
	<div class="row">&nbsp;</div>
     <div class="row-r">
     	<button type="submit" value="Create new client and transfer profiles and users" name="next" id="next">Create new client and transfer profiles and users</button>
     </div>
</div>
</div>
<?php echo Form::close() ?>

</div>
</div>
<script type="text/javascript">
$(document).ready(function(){
	$(document).on("click","#old_users option",function(){
		var id=$(this).val();
		var text = $(this).html();
		var length = document.getElementById("old_users").length;
		$(this).remove();
		$("#users").append('<option class="sub" value="'+id+'">'+text+'</option>');
	});
	
	$("#frm_subdivide_step4_client").submit(function(){
		$("#users option").attr('selected','selected');
	});
	
	$(document).on("click","#users option",function(){
		var id=$(this).val();
		var text = $(this).html();
		var length = document.getElementById("users").length;
		$(this).remove();
		$("#old_users").append('<option class="sub" value="'+id+'">'+text+'</option>');
	});
	
});
</script>