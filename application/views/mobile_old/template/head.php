<title><?php echo $title; ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, maximum-scale=1.0" />
<meta name='robots' content='noindex,nofollow' />
<?php
if ($stylesheets):
  foreach ($stylesheets as $stylesheet):
    echo (isset($stylesheet['ie_only'])) ? "<!--[if IE]>\n\t\t" : '';
    echo html::style($stylesheet['href'], array('media' => $stylesheet['media']), FALSE);
    echo (isset($stylesheet['ie_only'])) ? "<![endif]-->\n\t\t" : '';
  endforeach;
endif;

if ($javascripts):
  foreach ($javascripts as $javascript):
    echo html::script($javascript, NULL, FALSE) . "\t\t";
  endforeach;
endif;
?>

