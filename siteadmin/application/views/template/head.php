<title><?php echo $title;?></title>
<meta name='robots' content='noindex,nofollow' />
<?php
/*if($meta_tags):
	foreach ($meta_tags as $meta_tag):
		if (is_array($meta_tag)):
			echo html::meta($meta_tag);
		else:
			echo html::meta($meta_tag['name'], $meta_tag['value']);
		endif;
	endforeach;
endif;*/

if($stylesheets):
	foreach ($stylesheets as $stylesheet):
		echo (isset($stylesheet['ie_only'])) ? "<!--[if IE]>\n\t\t" : '';
		echo html::style($stylesheet['href'], array('media' => $stylesheet['media']), FALSE);
		echo (isset($stylesheet['ie_only'])) ? "<![endif]-->\n\t\t" : '';
	endforeach;
endif;

if($javascripts):
	foreach ($javascripts as $javascript):
		echo html::script($javascript, NULL, FALSE)."\t\t";
	endforeach;
endif;

?>
<!--[if lt IE 7]>
<script type="text/javascript" src="js/jquery.js"></script>
<script type="text/javascript" src="js/jquery.dropdown.js"></script>
<![endif]-->