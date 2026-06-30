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