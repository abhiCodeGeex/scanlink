<title><?php echo $title; ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!--<meta name="viewport" content="width=device-width, maximum-scale=1.0" />-->
<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, user-scalable=no" />
<meta name='robots' content='noindex,nofollow' />

<meta name="apple-mobile-web-app-capable" content="yes" />
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black">
<?php 
if(isset($shortcut_image1) && file_exists('images/touch_icons/'.$shortcut_image1))
{
?>
<link rel="apple-touch-startup-image" href="<?php echo url::base(); ?>images/touch_icons/<?php echo $shortcut_image1."?".rand(111111,9999999); ?>">
<link href="<?php echo url::base(); ?>images/touch_icons/<?php echo $shortcut_image1; ?>" rel="apple-touch-icon" sizes="180x180" />
<?php } ?>
<?php 
if(isset($shortcut_image2) && file_exists('images/touch_icons/'.$shortcut_image2))
{
?>
<link href="<?php echo url::base(); ?>images/touch_icons/<?php echo $shortcut_image2."?".rand(111111,9999999); ?>" rel="icon" sizes="192x192" />
<?php } ?>
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
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-167618191-1"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-167618191-1');
</script>
	

