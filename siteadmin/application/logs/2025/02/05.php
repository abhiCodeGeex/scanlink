<?php defined('SYSPATH') or die('No direct script access.'); ?>

2025-02-05 11:06:03 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: wp-login.php ~ SYSPATH/classes/kohana/request.php [ 1128 ]
2025-02-05 11:06:03 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: wp-login.php ~ SYSPATH/classes/kohana/request.php [ 1128 ]
--
#0 /var/www/scanlink.com.au/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2025-02-05 11:06:03 --- ERROR: HTTP_Exception_404 [ 404 ]: The requested URL wp-admin was not found on this server. ~ SYSPATH/classes/kohana/request/client/internal.php [ 87 ]
2025-02-05 11:06:03 --- STRACE: HTTP_Exception_404 [ 404 ]: The requested URL wp-admin was not found on this server. ~ SYSPATH/classes/kohana/request/client/internal.php [ 87 ]
--
#0 /var/www/scanlink.com.au/system/classes/kohana/request/client.php(64): Kohana_Request_Client_Internal->execute_request(Object(Request))
#1 /var/www/scanlink.com.au/system/classes/kohana/request.php(1140): Kohana_Request_Client->execute(Object(Request))
#2 /var/www/scanlink.com.au/siteadmin/index.php(132): Kohana_Request->execute()
#3 {main}