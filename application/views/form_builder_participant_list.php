<table cellpadding="0" cellspacing="0" border="0" width="100%">
	
	<?php 
	if(count($add_edit_participant_list_arr)>0)
	{
		
		foreach($add_edit_participant_list_arr as $participant)
		{
                    $bg_color= "";
                    if($participant["is_participated"]==1)
                    {
                        $bg_color= "#C9FFC9"; //Form submitted
                                                        
                        /*if($participant["diff_date"]>0)
                           $bg_color= "#C9FFC9"; //Form submitted
                        else //form submitted but after dew date
                           $bg_color= "#ffc0cb"; //Overdue
                        */
                    }
                    else
                    {
                        if($participant["diff_date_created"]>0)
                            $bg_color= "#ffc0cb"; //Overdue
                    }
	?>
	<tr <?php if($bg_color!="") echo 'style="background-color:'.$bg_color.'"';?> >
		<td width="55%" <?php if($participant["is_participated"]==1) echo 'style="border-bottom: 2px solid #ffffff;"';?>><span class="username_span"><?php echo $participant["name"];?></span></td>
		<td class="border-column"></td>
		<td <?php if($participant["is_participated"]==1) echo 'style="border-bottom: 2px solid #ffffff;"';?>><span class="due_date_span"><?php echo $participant["due_date_formated"];
                ?></span></td>
		<td class="border-column"></td>
		<td  width="15%" <?php if($participant["is_participated"]==1) echo 'style="border-bottom: 2px solid #ffffff;"';?>>
			<span class="edit_user" style="display: inline;" ><img src="<?php echo URL::base();?>images/edit.png" width="19" title="Edit Participant" alt="Edit Participant"  /></span>&nbsp;
	  		<span onclick="if(confirm('Are you sure you want to delete this Participant?')){ remove_participant_user('<?php echo $participant["participant_id"] ?>','<?php echo $profile_id;?>'); }else{ return false; }"  > <img src="<?php echo URL::base();?>images/delete.png" width="18" border="0" title="Delete Participant" alt="Delete Participant"  /></span>
		</td>
	</tr>
	<tr style="display: none;"> <!-- for edit items inline -->
		<td><input id="name"  name="name" class="username_txt inline-textbox" type="text" maxlength="255" style="width: 200px;" value="<?php echo $participant["name"];?>" /></td>
		<td class="border-column"></td>
		<td><input id="due_date<?php echo $participant["participant_id"] ?>" class="due_date inline-textbox" name="due_date" type="text" style="width: 100px;" value="<?php echo $participant["due_date_formated"];?>"/></td>
		<td class="border-column"></td>
		<td><span class="save_user" style="display: inline;" ><img src="<?php echo URL::base();?>images/save.png" width="16" title="Save Participant" alt="Save Participant" /></span>
			&nbsp;<span class="cancel_save" style="display: inline;" ><img src="<?php echo URL::base();?>images/cancel.png" width="16"  title="Cancel" alt="Cancel" /></span>
			<input id="participant_id"  name="participant_id" type="hidden" value="<?php echo $participant["participant_id"];?>" />
		</td>
	</tr>
	<?php
		} //end foreach
	}
	else
	{
	?>
		<tr>
			<td colspan="5" style="text-align: center;">No Participant found</td>
			
		</tr>
	<?php	
	}
	?>
	
</table>