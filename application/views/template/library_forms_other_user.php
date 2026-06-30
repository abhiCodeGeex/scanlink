	<div id="tabs">
	  <ul>
	    <li><a href="#tabs-4">Select From existing code profile</a></li>
	    <li><a href="#tabs-5">Select from library</a></li>
	    <li><a href="#tabs-6">Select from another account</a></li>
	  </ul>
	  <div id="tabs-4">
	    
	  </div>
	  <div id="tabs-5"> <!--The user can select a form saved in their Form Library-->
	     <?php $sr_no =1;
			
		
			foreach($form_library_arr as $form_library)
			{
			?>
				<div class="form-list" id="form_<?php echo $form_library["form_id"];?>">
					<div class="sr-no"><?php echo $sr_no;?></div>
					<div class="form-name" style="width: 64%;">
						 <?php if($form_library["form_title"]!="")
						 	echo $form_library["form_title"];
							else
								echo "&nbsp;";
							?>
					</div>
					
					<div class="form-select">
							  <img align="absmiddle" src="<?php echo url::base(); ?>images/select_icon.png" alt="Add Form" title="Add Form" onclick="add_form_from_library('<?php echo $profile_id;?>','<?php echo $form_library["form_id"];?>')"  />
						&nbsp;<img align="absmiddle" src="<?php echo url::base(); ?>images/preview_form.png" alt="Preview Form" title="Preview Form" onclick="show_preview_form('<?php echo $form_library["form_id"];?>','<?php echo $form_library["form_title"];?>')" />
						&nbsp;<img align="absmiddle" src="<?php echo url::base(); ?>images/delete_profile.png"   />
					</div>
				</div>
				<div class="clear">&nbsp;</div>
			<?php $sr_no++;
			}?>
	  </div>
	  <div id="tabs-6">
	 
	  </div>
	</div>
