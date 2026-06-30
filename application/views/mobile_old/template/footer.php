<?php
	$button_style = "style='";
	if (trim($profile["buttonbackcolor"])!="")
		$button_style .= "background: #".$profile["buttonbackcolor"].";";

	if (trim($profile["buttonfontcolor"])!="")
		$button_style .= "color: #".$profile["buttonfontcolor"].";";
	
	$button_style .= "'";
?>
<?php /*
<div class="DocumationVideoBTN">
	<button class="Documents" <?php print $button_style;?> onclick="javascript: window.location.href='<?php echo URL::base().$home."/videos"; ?>'"><img src="<?php echo URL::base(); ?>images/video-icon.png"> Video</button>
	<button class="" <?php print $button_style;?> onclick="javascript: window.location.href='<?php echo URL::base().$home."/documents"; ?>'"><img src="<?php echo URL::base(); ?>images/docu-icon.png"> Documents</button>
</div>

 */
?>