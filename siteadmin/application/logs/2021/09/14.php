<?php defined('SYSPATH') or die('No direct script access.'); ?>

2021-09-14 18:47:38 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: codeorder/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1128 ]
2021-09-14 18:47:38 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: codeorder/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1128 ]
--
#0 /var/www/scanlink.com.au/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2021-09-14 18:47:38 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: codeorder/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1128 ]
2021-09-14 18:47:38 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: codeorder/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1128 ]
--
#0 /var/www/scanlink.com.au/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2021-09-14 18:47:53 --- ERROR: ErrorException [ 8 ]: Undefined offset: 0 ~ APPPATH/classes/controller/codeorder.php [ 99 ]
2021-09-14 18:47:53 --- STRACE: ErrorException [ 8 ]: Undefined offset: 0 ~ APPPATH/classes/controller/codeorder.php [ 99 ]
--
#0 /var/www/scanlink.com.au/siteadmin/application/classes/controller/codeorder.php(99): Kohana_Core::error_handler(8, 'Undefined offse...', '/var/www/scanli...', 99, Array)
#1 [internal function]: Controller_Codeorder->action_view()
#2 /var/www/scanlink.com.au/system/classes/kohana/request/client/internal.php(118): ReflectionMethod->invoke(Object(Controller_Codeorder))
#3 /var/www/scanlink.com.au/system/classes/kohana/request/client.php(64): Kohana_Request_Client_Internal->execute_request(Object(Request))
#4 /var/www/scanlink.com.au/system/classes/kohana/request.php(1140): Kohana_Request_Client->execute(Object(Request))
#5 /var/www/scanlink.com.au/siteadmin/index.php(132): Kohana_Request->execute()
#6 {main}