<div class="container">
<div class="contant inner-box">
<form method="post" name="frm_subdivide_step2_client" id="frm_subdivide_step2_client"  class="subdivide">
<input type="hidden" id="client" name="client" value="<?php echo $client; ?>" />
<div class="penal">
<h3>
     <span class="fleft">*&nbsp;Please Select one or multiple profiles which you want to transfer to new client</span>
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
	<div class="row">Select Profiles</div>
     <div class="row-r">
     	<table width="100%">
          	<tr><th>Click on Profile which you want to transfer</th>
               	<th>Click to send it back to old client</th></tr>
     	    	<tr><td width="50%">
                    <select id="old_profiles" name="old_profiles[]" class="input multi" multiple="multiple" size="10">
	                    <option class="head" value="" disabled>Profiles in Old Client</option>
                         <?php foreach($profiles as $profile){ ?>
                         <option class="sub" value="<?php echo $profile['id']; ?>"><?php echo $profile['name']; ?></option>
                         <?php } ?>
                    </select>
	          </td>
               <td>
                    <select id="profiles" name="profiles[]" class="input multi" multiple="multiple" size="10">
	                    <option class="head" value="" disabled>Profiles in New Client</option>
                    </select>
               </td></tr>
          </table>
     </div>
</div>
<div class="main-row">
	<div class="row">&nbsp;</div>
     <div class="row-r">
     	<button type="submit" value="Go to next step" name="next" id="next">Go to next step</button>
     </div>
</div>


</div>
</form>
</div>
</div>
<script type="text/javascript">
$(document).ready(function(){
	

	
	$(document).on("change","#old_profiles",function(){

		var id=$(this).val();
		var text = $(this).children("option:selected").text();

		var length = document.getElementById("old_profiles").length;
		$(this).children("option:selected").remove();
		$("#profiles").append('<option class="sub" value="'+id+'">'+text+'</option>');
	});
	
	$("#frm_subdivide_step2_client").submit(function(){
		$("#profiles option").attr('selected','selected');
	});
	
	$(document).on("change","#profiles",function(){
		var id=$(this).val();
		var text = $(this).children("option:selected").text();
		var length = document.getElementById("profiles").length;
		$(this).children("option:selected").remove();
		$("#old_profiles").append('<option class="sub" value="'+id+'">'+text+'</option>');
	});
	
});
</script>