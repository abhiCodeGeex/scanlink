<?php defined('SYSPATH') or die('No direct script access.'); ?>

2012-09-12 00:09:33 --- ERROR: HTTP_Exception_404 [ 404 ]: The requested URL ] was not found on this server. ~ SYSPATH/classes/kohana/request/client/internal.php [ 87 ]
2012-09-12 00:09:33 --- STRACE: HTTP_Exception_404 [ 404 ]: The requested URL ] was not found on this server. ~ SYSPATH/classes/kohana/request/client/internal.php [ 87 ]
--
#0 /home/codesafe/public_html/system/classes/kohana/request/client.php(64): Kohana_Request_Client_Internal->execute_request(Object(Request))
#1 /home/codesafe/public_html/system/classes/kohana/request.php(1138): Kohana_Request_Client->execute(Object(Request))
#2 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#3 {main}
2012-09-12 00:09:58 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2012-09-12 00:09:58 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2012-09-12 00:09:58 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2012-09-12 00:09:58 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2012-09-12 22:09:05 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2012-09-12 22:09:05 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2012-09-12 22:09:05 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2012-09-12 22:09:05 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}