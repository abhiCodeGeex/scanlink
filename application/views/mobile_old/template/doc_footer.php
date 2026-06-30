<?php
	$button_style = "style='";
	if (trim($profile["buttonbackcolor"])!="")
		$button_style .= "background: #".$profile["buttonbackcolor"].";";

	if (trim($profile["buttonfontcolor"])!="")
		$button_style .= "color: #".$profile["buttonfontcolor"].";";
	
	$button_style .= "'";
?>

<div class="DocumationVideoBTN">
	<button class="Documents" <?php print $button_style;?> onclick="javascript: window.location.href='<?php echo URL::base().$home."/videos"; ?>'"><img src="<?php echo URL::base(); ?>images/video-icon.png"> Videos</button>
	<button class="" <?php print $button_style;?> onclick="javascript: window.location.href='<?php echo URL::base().$home; ?>'"><img src="<?php echo URL::base(); ?>images/back-icon.png"> Back</button>
</div>