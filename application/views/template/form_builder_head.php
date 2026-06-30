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

?>
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-167618191-1"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-167618191-1');
</script>

<script type="text/javascript">
	function url_base() { return "<?php echo url::base();?>"; }
</script>
