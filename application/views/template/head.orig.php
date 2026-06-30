<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title><?php echo $title; ?></title>
<?php
if ($stylesheets):
  foreach ($stylesheets as $stylesheet):
    echo (isset($stylesheet['ie_only'])) ? "<!--[if IE]>\n\t\t" : '';
    echo html::style($stylesheet['href'], array('media' => $stylesheet['media']), FALSE) . "\r\n";
    echo (isset($stylesheet['ie_only'])) ? "<![endif]-->\n\t\t" : '';
  endforeach;
endif;

if ($javascripts):
  foreach ($javascripts as $javascript):
    echo html::script($javascript, NULL, FALSE) . "\t\t\r\n";
  endforeach;
endif;
//if(isset($extrascripts) && $extrascripts=='slider'):
?>
<script type="text/javascript">
	function url_base() { return "<?php echo url::base();?>"; }
</script>


<script type="text/javascript" src="<?php echo URL::base(TRUE); ?>js/commonFunction.js"></script>


<script type="text/javascript">
  $(document).ready(function() {
    $('#coin-slider').coinslider({ width: 820, height:249, navigation: true, delay: 8000 ,sDelay: 30, opacity:1});
  });
</script>

<a href="https://plus.google.com/106453354449852537831" rel="publisher" />
<?php
//endif;
?>


<!--[if IE 7]>
  <link rel="stylesheet" type="text/css" href="<?php echo URL::base(); ?>styles/ie7.css">
<![endif]-->

