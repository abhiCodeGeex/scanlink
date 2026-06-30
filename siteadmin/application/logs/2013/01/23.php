<?php defined('SYSPATH') or die('No direct script access.'); ?>

2013-01-23 00:34:34 --- ERROR: Database_Exception [ 1054 ]: Unknown column 'clients.*,users.checklist_option' in 'field list' [ SELECT `clients`.`*,users`.`checklist_option` FROM `clients` JOIN `users` ON (`clients`.`id` = `users`.`client_id`) WHERE `users`.`role` = 5 ORDER BY `client_name` ASC ] ~ MODPATH/database/classes/kohana/database/mysql.php [ 194 ]
2013-01-23 00:34:34 --- STRACE: Database_Exception [ 1054 ]: Unknown column 'clients.*,users.checklist_option' in 'field list' [ SELECT `clients`.`*,users`.`checklist_option` FROM `clients` JOIN `users` ON (`clients`.`id` = `users`.`client_id`) WHERE `users`.`role` = 5 ORDER BY `client_name` ASC ] ~ MODPATH/database/classes/kohana/database/mysql.php [ 194 ]
--
#0 /home/codesafe/public_html/modules/database/classes/kohana/database/query.php(245): Kohana_Database_MySQL->query(1, 'SELECT `clients...', false, Array)
#1 /home/codesafe/public_html/siteadmin/application/classes/model/client.php(100): Kohana_Database_Query->execute()
#2 /home/codesafe/public_html/siteadmin/application/classes/controller/client.php(49): Model_Client->getallclient()
#3 [internal function]: Controller_Client->action_index()
#4 /home/codesafe/public_html/system/classes/kohana/request/client/internal.php(118): ReflectionMethod->invoke(Object(Controller_Client))
#5 /home/codesafe/public_html/system/classes/kohana/request/client.php(64): Kohana_Request_Client_Internal->execute_request(Object(Request))
#6 /home/codesafe/public_html/system/classes/kohana/request.php(1138): Kohana_Request_Client->execute(Object(Request))
#7 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#8 {main}
2013-01-23 00:36:09 --- ERROR: Database_Exception [ 1064 ]: You have an error in your SQL syntax; check the manual that corresponds to your MySQL server version for the right syntax to use near '``.``*`,`users``.``checklist_option`` FROM `clients` JOIN `users` ON (`clients`.' at line 1 [ SELECT ``clients``.``*`,`users``.``checklist_option`` FROM `clients` JOIN `users` ON (`clients`.`id` = `users`.`client_id`) WHERE `users`.`role` = 5 ORDER BY `client_name` ASC ] ~ MODPATH/database/classes/kohana/database/mysql.php [ 194 ]
2013-01-23 00:36:09 --- STRACE: Database_Exception [ 1064 ]: You have an error in your SQL syntax; check the manual that corresponds to your MySQL server version for the right syntax to use near '``.``*`,`users``.``checklist_option`` FROM `clients` JOIN `users` ON (`clients`.' at line 1 [ SELECT ``clients``.``*`,`users``.``checklist_option`` FROM `clients` JOIN `users` ON (`clients`.`id` = `users`.`client_id`) WHERE `users`.`role` = 5 ORDER BY `client_name` ASC ] ~ MODPATH/database/classes/kohana/database/mysql.php [ 194 ]
--
#0 /home/codesafe/public_html/modules/database/classes/kohana/database/query.php(245): Kohana_Database_MySQL->query(1, 'SELECT ``client...', false, Array)
#1 /home/codesafe/public_html/siteadmin/application/classes/model/client.php(100): Kohana_Database_Query->execute()
#2 /home/codesafe/public_html/siteadmin/application/classes/controller/client.php(49): Model_Client->getallclient()
#3 [internal function]: Controller_Client->action_index()
#4 /home/codesafe/public_html/system/classes/kohana/request/client/internal.php(118): ReflectionMethod->invoke(Object(Controller_Client))
#5 /home/codesafe/public_html/system/classes/kohana/request/client.php(64): Kohana_Request_Client_Internal->execute_request(Object(Request))
#6 /home/codesafe/public_html/system/classes/kohana/request.php(1138): Kohana_Request_Client->execute(Object(Request))
#7 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#8 {main}
2013-01-23 00:42:13 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 00:42:13 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 00:42:13 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 00:42:13 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 00:47:52 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 00:47:52 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 00:47:53 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 00:47:53 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 00:48:00 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 00:48:00 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 00:48:00 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 00:48:00 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 00:48:36 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 00:48:36 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 00:48:37 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 00:48:37 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 00:49:30 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 00:49:30 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 00:49:31 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 00:49:31 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 00:50:26 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 00:50:26 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 00:50:27 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 00:50:27 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 00:58:33 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 00:58:33 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 00:58:34 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 00:58:34 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 00:58:39 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 00:58:39 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 00:58:41 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 00:58:41 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 01:03:28 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 01:03:28 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 01:03:29 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 01:03:29 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 01:03:38 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 01:03:38 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 01:03:39 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 01:03:39 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 01:03:56 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 01:03:56 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 01:03:57 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 01:03:57 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 01:06:31 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 01:06:31 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 01:06:32 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 01:06:32 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 01:15:39 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 01:15:39 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 01:15:40 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 01:15:40 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 01:17:13 --- ERROR: ErrorException [ 8 ]: Undefined variable: 0 ~ APPPATH/classes/model/client.php [ 100 ]
2013-01-23 01:17:13 --- STRACE: ErrorException [ 8 ]: Undefined variable: 0 ~ APPPATH/classes/model/client.php [ 100 ]
--
#0 /home/codesafe/public_html/siteadmin/application/classes/model/client.php(100): Kohana_Core::error_handler('19')
#1 /home/codesafe/public_html/siteadmin/application/classes/controller/client.php(74): Model_Client->changeChecklistOption()
#2 [internal function]: Controller_Client->action_checklist_option(Object(Controller_Client))
#3 /home/codesafe/public_html/system/classes/kohana/request/client/internal.php(118): ReflectionMethod->invoke(Object(Request))
#4 /home/codesafe/public_html/system/classes/kohana/request/client.php(64): Kohana_Request_Client_Internal->execute_request(Object(Request))
#5 /home/codesafe/public_html/system/classes/kohana/request.php(1138): Kohana_Request_Client->execute()
#6 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#7 {main}
2013-01-23 01:19:17 --- ERROR: ErrorException [ 8 ]: Undefined variable: 0 ~ APPPATH/classes/model/client.php [ 101 ]
2013-01-23 01:19:17 --- STRACE: ErrorException [ 8 ]: Undefined variable: 0 ~ APPPATH/classes/model/client.php [ 101 ]
--
#0 /home/codesafe/public_html/siteadmin/application/classes/model/client.php(101): Kohana_Core::error_handler('19')
#1 /home/codesafe/public_html/siteadmin/application/classes/controller/client.php(74): Model_Client->changeChecklistOption()
#2 [internal function]: Controller_Client->action_checklist_option(Object(Controller_Client))
#3 /home/codesafe/public_html/system/classes/kohana/request/client/internal.php(118): ReflectionMethod->invoke(Object(Request))
#4 /home/codesafe/public_html/system/classes/kohana/request/client.php(64): Kohana_Request_Client_Internal->execute_request(Object(Request))
#5 /home/codesafe/public_html/system/classes/kohana/request.php(1138): Kohana_Request_Client->execute()
#6 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#7 {main}
2013-01-23 01:19:40 --- ERROR: ErrorException [ 8 ]: Array to string conversion ~ APPPATH/classes/model/client.php [ 100 ]
2013-01-23 01:19:40 --- STRACE: ErrorException [ 8 ]: Array to string conversion ~ APPPATH/classes/model/client.php [ 100 ]
--
#0 /home/codesafe/public_html/siteadmin/application/classes/model/client.php(100): Kohana_Core::error_handler('19')
#1 /home/codesafe/public_html/siteadmin/application/classes/controller/client.php(74): Model_Client->changeChecklistOption()
#2 [internal function]: Controller_Client->action_checklist_option(Object(Controller_Client))
#3 /home/codesafe/public_html/system/classes/kohana/request/client/internal.php(118): ReflectionMethod->invoke(Object(Request))
#4 /home/codesafe/public_html/system/classes/kohana/request/client.php(64): Kohana_Request_Client_Internal->execute_request(Object(Request))
#5 /home/codesafe/public_html/system/classes/kohana/request.php(1138): Kohana_Request_Client->execute()
#6 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#7 {main}
2013-01-23 01:24:48 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 01:24:48 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 01:24:49 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 01:24:49 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 01:24:51 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: images/checklist-allowed.gif.png ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 01:24:51 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: images/checklist-allowed.gif.png ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 01:25:11 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: images/checklist-allowed.gif.png ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 01:25:11 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: images/checklist-allowed.gif.png ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 01:25:45 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 01:25:45 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 01:25:47 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 01:25:47 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 01:26:00 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 01:26:00 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 01:26:00 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 01:26:00 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 01:28:19 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 01:28:19 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 01:28:23 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 01:28:23 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 01:28:23 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 01:28:23 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 01:28:33 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: order/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 01:28:33 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: order/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 01:28:35 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: order/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 01:28:35 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: order/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 01:28:35 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: order/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 01:28:35 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: order/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 01:28:47 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 01:28:47 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 01:28:47 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 01:28:47 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 01:29:27 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 01:29:27 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 01:29:28 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 01:29:28 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 01:33:53 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 01:33:53 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 01:33:54 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 01:33:54 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 01:34:14 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 01:34:14 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 01:34:14 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 01:34:14 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 01:34:42 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 01:34:42 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 01:34:42 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 01:34:42 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 03:33:32 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 03:33:32 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 03:33:38 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 03:33:38 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 03:33:38 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 03:33:38 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 04:05:20 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 04:05:20 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 04:05:21 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 04:05:21 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 04:05:25 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 04:05:25 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 04:05:25 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 04:05:25 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 04:05:55 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 04:05:55 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 04:05:56 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 04:05:56 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 04:15:39 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 04:15:39 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 04:15:39 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 04:15:39 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 04:39:50 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 04:39:50 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 04:39:50 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 04:39:50 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 04:40:08 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 04:40:08 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 04:40:08 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 04:40:08 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 04:59:04 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 04:59:04 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 04:59:04 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 04:59:04 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 04:59:10 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 04:59:10 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 04:59:10 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 04:59:10 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 04:59:20 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 04:59:20 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 04:59:21 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 04:59:21 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 04:59:41 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 04:59:41 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 04:59:42 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 04:59:42 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 05:00:02 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 05:00:02 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 05:00:05 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 05:00:05 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 05:02:03 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 05:02:03 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 05:02:03 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 05:02:03 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 05:02:26 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 05:02:26 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 05:02:26 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 05:02:26 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 05:03:58 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 05:03:58 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 05:03:58 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 05:03:58 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 05:04:14 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 05:04:14 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 05:04:15 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 05:04:15 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 05:04:28 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 05:04:28 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 05:04:29 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 05:04:29 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 05:12:42 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 05:12:42 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 05:12:42 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 05:12:42 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 05:13:16 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 05:13:16 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 05:13:16 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 05:13:16 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 05:13:47 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 05:13:47 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 05:13:47 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 05:13:47 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 05:14:11 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 05:14:11 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 05:14:11 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 05:14:11 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 05:15:59 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 05:15:59 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 05:15:59 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 05:15:59 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 05:16:28 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 05:16:28 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 05:16:28 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 05:16:28 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 05:24:33 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 05:24:33 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 05:24:34 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 05:24:34 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 05:28:56 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 05:28:56 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 05:28:56 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 05:28:56 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 05:30:05 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: styles/images/ui-bg_flat_75_ffffff_40x100.png ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 05:30:05 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: styles/images/ui-bg_flat_75_ffffff_40x100.png ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 05:30:05 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: styles/images/ui-bg_highlight-soft_75_cccccc_1x100.png ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 05:30:05 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: styles/images/ui-bg_highlight-soft_75_cccccc_1x100.png ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 05:30:05 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: styles/images/ui-bg_glass_75_e6e6e6_1x400.png ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 05:30:05 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: styles/images/ui-bg_glass_75_e6e6e6_1x400.png ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 05:30:05 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: styles/images/ui-bg_glass_55_fbf9ee_1x400.png ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 05:30:05 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: styles/images/ui-bg_glass_55_fbf9ee_1x400.png ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 05:30:05 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: styles/images/ui-icons_222222_256x240.png ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 05:30:05 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: styles/images/ui-icons_222222_256x240.png ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 05:52:06 --- ERROR: ErrorException [ 4 ]: syntax error, unexpected T_FOR ~ APPPATH/views/client/add.php [ 97 ]
2013-01-23 05:52:06 --- STRACE: ErrorException [ 4 ]: syntax error, unexpected T_FOR ~ APPPATH/views/client/add.php [ 97 ]
--
#0 [internal function]: Kohana_Core::shutdown_handler('/home/codesafe/...', Array)
#1 {main}
2013-01-23 05:57:15 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 05:57:15 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 05:57:15 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 05:57:15 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 05:57:29 --- ERROR: ErrorException [ 8 ]: Undefined index:  checklist_option ~ APPPATH/classes/controller/client.php [ 117 ]
2013-01-23 05:57:29 --- STRACE: ErrorException [ 8 ]: Undefined index:  checklist_option ~ APPPATH/classes/controller/client.php [ 117 ]
--
#0 /home/codesafe/public_html/siteadmin/application/classes/controller/client.php(117): Kohana_Core::error_handler()
#1 [internal function]: Controller_Client->action_edit(Object(Controller_Client))
#2 /home/codesafe/public_html/system/classes/kohana/request/client/internal.php(118): ReflectionMethod->invoke(Object(Request))
#3 /home/codesafe/public_html/system/classes/kohana/request/client.php(64): Kohana_Request_Client_Internal->execute_request(Object(Request))
#4 /home/codesafe/public_html/system/classes/kohana/request.php(1138): Kohana_Request_Client->execute()
#5 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#6 {main}
2013-01-23 05:57:33 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 05:57:33 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 05:57:33 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 05:57:33 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 06:05:01 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 06:05:01 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 06:05:03 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 06:05:03 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 06:05:06 --- ERROR: Database_Exception [ 1054 ]: Unknown column '20' in 'on clause' [ SELECT `clients`.*, `users`.`checklist_option` FROM `clients` JOIN `users` ON (`users`.`client_id` = `20`) WHERE `users`.`role` = 5 AND `clients`.`id` = '20' ] ~ MODPATH/database/classes/kohana/database/mysql.php [ 194 ]
2013-01-23 06:05:06 --- STRACE: Database_Exception [ 1054 ]: Unknown column '20' in 'on clause' [ SELECT `clients`.*, `users`.`checklist_option` FROM `clients` JOIN `users` ON (`users`.`client_id` = `20`) WHERE `users`.`role` = 5 AND `clients`.`id` = '20' ] ~ MODPATH/database/classes/kohana/database/mysql.php [ 194 ]
--
#0 /home/codesafe/public_html/modules/database/classes/kohana/database/query.php(245): Kohana_Database_MySQL->query(1, 'SELECT `clients...', false, Array)
#1 /home/codesafe/public_html/siteadmin/application/classes/model/client.php(152): Kohana_Database_Query->execute()
#2 /home/codesafe/public_html/siteadmin/application/classes/controller/client.php(108): Model_Client->getclientbyid('20')
#3 [internal function]: Controller_Client->action_edit()
#4 /home/codesafe/public_html/system/classes/kohana/request/client/internal.php(118): ReflectionMethod->invoke(Object(Controller_Client))
#5 /home/codesafe/public_html/system/classes/kohana/request/client.php(64): Kohana_Request_Client_Internal->execute_request(Object(Request))
#6 /home/codesafe/public_html/system/classes/kohana/request.php(1138): Kohana_Request_Client->execute(Object(Request))
#7 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#8 {main}
2013-01-23 06:06:31 --- ERROR: ErrorException [ 1 ]: Class 'Model_client' not found ~ SYSPATH/classes/kohana/model.php [ 26 ]
2013-01-23 06:06:31 --- STRACE: ErrorException [ 1 ]: Class 'Model_client' not found ~ SYSPATH/classes/kohana/model.php [ 26 ]
--
#0 [internal function]: Kohana_Core::shutdown_handler('client')
#1 {main}
2013-01-23 06:07:07 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 06:07:07 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 06:07:10 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 06:07:10 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 06:07:10 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 06:07:10 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 06:07:57 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 06:07:57 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 06:07:58 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 06:07:58 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 06:08:22 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 06:08:22 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 06:08:22 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 06:08:22 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 06:08:26 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 06:08:26 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 06:08:27 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 06:08:27 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 06:08:36 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 06:08:36 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 06:08:36 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 06:08:36 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 06:08:48 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 06:08:48 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 06:08:48 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 06:08:48 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 06:10:29 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 06:10:29 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 06:10:29 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 06:10:29 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 06:10:34 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 06:10:34 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 06:10:35 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 06:10:35 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 06:10:38 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 06:10:38 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 06:10:42 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 06:10:42 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 06:12:23 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 06:12:23 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 06:12:30 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 06:12:30 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 06:12:30 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 06:12:30 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 06:12:36 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 06:12:36 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 06:12:36 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 06:12:36 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 06:12:42 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 06:12:42 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 06:12:43 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 06:12:43 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 06:12:48 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 06:12:48 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 06:12:48 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 06:12:48 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 06:13:01 --- ERROR: Database_Exception [ 1054 ]: Unknown column 'checklist_option' in 'field list' [ UPDATE `clients` SET `client_name` = 'bhagirath chauhan', `address` = 'test address', `telephone` = '493', `contact_person` = 'bhagirath chauhan', `regi_date` = '2013-01-18', `email` = 'bhagirath@webplanex.com', `password` = 'bhagirath123', `checklist_option` = '1', `url` = '14YdfMR41', `approve` = 1, `created_at` = '2013-01-23 06:13:01' WHERE `id` = '26' ] ~ MODPATH/database/classes/kohana/database/mysql.php [ 194 ]
2013-01-23 06:13:01 --- STRACE: Database_Exception [ 1054 ]: Unknown column 'checklist_option' in 'field list' [ UPDATE `clients` SET `client_name` = 'bhagirath chauhan', `address` = 'test address', `telephone` = '493', `contact_person` = 'bhagirath chauhan', `regi_date` = '2013-01-18', `email` = 'bhagirath@webplanex.com', `password` = 'bhagirath123', `checklist_option` = '1', `url` = '14YdfMR41', `approve` = 1, `created_at` = '2013-01-23 06:13:01' WHERE `id` = '26' ] ~ MODPATH/database/classes/kohana/database/mysql.php [ 194 ]
--
#0 /home/codesafe/public_html/modules/database/classes/kohana/database/query.php(245): Kohana_Database_MySQL->query(3, 'UPDATE `clients...', false, Array)
#1 /home/codesafe/public_html/siteadmin/application/classes/model/client.php(156): Kohana_Database_Query->execute()
#2 /home/codesafe/public_html/siteadmin/application/classes/controller/client.php(179): Model_Client->editclient('26', Array, Array)
#3 [internal function]: Controller_Client->action_edit()
#4 /home/codesafe/public_html/system/classes/kohana/request/client/internal.php(118): ReflectionMethod->invoke(Object(Controller_Client))
#5 /home/codesafe/public_html/system/classes/kohana/request/client.php(64): Kohana_Request_Client_Internal->execute_request(Object(Request))
#6 /home/codesafe/public_html/system/classes/kohana/request.php(1138): Kohana_Request_Client->execute(Object(Request))
#7 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#8 {main}
2013-01-23 06:14:26 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 06:14:26 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 06:14:27 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 06:14:27 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 06:14:28 --- ERROR: Database_Exception [ 1054 ]: Unknown column 'checklist_option' in 'field list' [ UPDATE `clients` SET `client_name` = 'bhagirath chauhan', `address` = 'test address', `telephone` = '493', `contact_person` = 'bhagirath chauhan', `regi_date` = '2013-01-18', `email` = 'bhagirath@webplanex.com', `password` = 'bhagirath123', `checklist_option` = '1', `url` = '14YdfMR41', `approve` = 1, `created_at` = '2013-01-23 06:14:28' WHERE `id` = '26' ] ~ MODPATH/database/classes/kohana/database/mysql.php [ 194 ]
2013-01-23 06:14:28 --- STRACE: Database_Exception [ 1054 ]: Unknown column 'checklist_option' in 'field list' [ UPDATE `clients` SET `client_name` = 'bhagirath chauhan', `address` = 'test address', `telephone` = '493', `contact_person` = 'bhagirath chauhan', `regi_date` = '2013-01-18', `email` = 'bhagirath@webplanex.com', `password` = 'bhagirath123', `checklist_option` = '1', `url` = '14YdfMR41', `approve` = 1, `created_at` = '2013-01-23 06:14:28' WHERE `id` = '26' ] ~ MODPATH/database/classes/kohana/database/mysql.php [ 194 ]
--
#0 /home/codesafe/public_html/modules/database/classes/kohana/database/query.php(245): Kohana_Database_MySQL->query(3, 'UPDATE `clients...', false, Array)
#1 /home/codesafe/public_html/siteadmin/application/classes/model/client.php(156): Kohana_Database_Query->execute()
#2 /home/codesafe/public_html/siteadmin/application/classes/controller/client.php(179): Model_Client->editclient('26', Array, Array)
#3 [internal function]: Controller_Client->action_edit()
#4 /home/codesafe/public_html/system/classes/kohana/request/client/internal.php(118): ReflectionMethod->invoke(Object(Controller_Client))
#5 /home/codesafe/public_html/system/classes/kohana/request/client.php(64): Kohana_Request_Client_Internal->execute_request(Object(Request))
#6 /home/codesafe/public_html/system/classes/kohana/request.php(1138): Kohana_Request_Client->execute(Object(Request))
#7 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#8 {main}
2013-01-23 06:14:32 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 06:14:32 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 06:14:33 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 06:14:33 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 06:14:34 --- ERROR: Database_Exception [ 1054 ]: Unknown column 'checklist_option' in 'field list' [ UPDATE `clients` SET `client_name` = 'bhagirath chauhan', `address` = 'test address', `telephone` = '493', `contact_person` = 'bhagirath chauhan', `regi_date` = '2013-01-18', `email` = 'bhagirath@webplanex.com', `password` = 'bhagirath123', `checklist_option` = '1', `url` = '14YdfMR41', `approve` = 1, `created_at` = '2013-01-23 06:14:34' WHERE `id` = '26' ] ~ MODPATH/database/classes/kohana/database/mysql.php [ 194 ]
2013-01-23 06:14:34 --- STRACE: Database_Exception [ 1054 ]: Unknown column 'checklist_option' in 'field list' [ UPDATE `clients` SET `client_name` = 'bhagirath chauhan', `address` = 'test address', `telephone` = '493', `contact_person` = 'bhagirath chauhan', `regi_date` = '2013-01-18', `email` = 'bhagirath@webplanex.com', `password` = 'bhagirath123', `checklist_option` = '1', `url` = '14YdfMR41', `approve` = 1, `created_at` = '2013-01-23 06:14:34' WHERE `id` = '26' ] ~ MODPATH/database/classes/kohana/database/mysql.php [ 194 ]
--
#0 /home/codesafe/public_html/modules/database/classes/kohana/database/query.php(245): Kohana_Database_MySQL->query(3, 'UPDATE `clients...', false, Array)
#1 /home/codesafe/public_html/siteadmin/application/classes/model/client.php(156): Kohana_Database_Query->execute()
#2 /home/codesafe/public_html/siteadmin/application/classes/controller/client.php(179): Model_Client->editclient('26', Array, Array)
#3 [internal function]: Controller_Client->action_edit()
#4 /home/codesafe/public_html/system/classes/kohana/request/client/internal.php(118): ReflectionMethod->invoke(Object(Controller_Client))
#5 /home/codesafe/public_html/system/classes/kohana/request/client.php(64): Kohana_Request_Client_Internal->execute_request(Object(Request))
#6 /home/codesafe/public_html/system/classes/kohana/request.php(1138): Kohana_Request_Client->execute(Object(Request))
#7 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#8 {main}
2013-01-23 06:14:40 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 06:14:40 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 06:14:40 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 06:14:40 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 06:14:47 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 06:14:47 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 06:14:48 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 06:14:48 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 06:14:51 --- ERROR: Database_Exception [ 1054 ]: Unknown column 'checklist_option' in 'field list' [ UPDATE `clients` SET `client_name` = 'bhagirath chauhan', `address` = 'test address', `telephone` = '493', `contact_person` = 'bhagirath chauhan', `regi_date` = '2013-01-18', `email` = 'bhagirath@webplanex.com', `password` = 'bhagirath123', `checklist_option` = '1', `url` = '14YdfMR41', `approve` = 1, `created_at` = '2013-01-23 06:14:51' WHERE `id` = '26' ] ~ MODPATH/database/classes/kohana/database/mysql.php [ 194 ]
2013-01-23 06:14:51 --- STRACE: Database_Exception [ 1054 ]: Unknown column 'checklist_option' in 'field list' [ UPDATE `clients` SET `client_name` = 'bhagirath chauhan', `address` = 'test address', `telephone` = '493', `contact_person` = 'bhagirath chauhan', `regi_date` = '2013-01-18', `email` = 'bhagirath@webplanex.com', `password` = 'bhagirath123', `checklist_option` = '1', `url` = '14YdfMR41', `approve` = 1, `created_at` = '2013-01-23 06:14:51' WHERE `id` = '26' ] ~ MODPATH/database/classes/kohana/database/mysql.php [ 194 ]
--
#0 /home/codesafe/public_html/modules/database/classes/kohana/database/query.php(245): Kohana_Database_MySQL->query(3, 'UPDATE `clients...', false, Array)
#1 /home/codesafe/public_html/siteadmin/application/classes/model/client.php(156): Kohana_Database_Query->execute()
#2 /home/codesafe/public_html/siteadmin/application/classes/controller/client.php(179): Model_Client->editclient('26', Array, Array)
#3 [internal function]: Controller_Client->action_edit()
#4 /home/codesafe/public_html/system/classes/kohana/request/client/internal.php(118): ReflectionMethod->invoke(Object(Controller_Client))
#5 /home/codesafe/public_html/system/classes/kohana/request/client.php(64): Kohana_Request_Client_Internal->execute_request(Object(Request))
#6 /home/codesafe/public_html/system/classes/kohana/request.php(1138): Kohana_Request_Client->execute(Object(Request))
#7 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#8 {main}
2013-01-23 06:15:34 --- ERROR: ErrorException [ 4 ]: syntax error, unexpected $end ~ APPPATH/classes/controller/client.php [ 338 ]
2013-01-23 06:15:34 --- STRACE: ErrorException [ 4 ]: syntax error, unexpected $end ~ APPPATH/classes/controller/client.php [ 338 ]
--
#0 [internal function]: Kohana_Core::shutdown_handler('controller_clie...')
#1 {main}
2013-01-23 06:16:12 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 06:16:12 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 06:16:12 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 06:16:12 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 06:16:16 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 06:16:16 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 06:16:17 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 06:16:17 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 06:16:58 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: styles/images/ui-bg_glass_55_fbf9ee_1x400.png ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 06:16:58 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: styles/images/ui-bg_glass_55_fbf9ee_1x400.png ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 06:16:58 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: styles/images/ui-bg_flat_75_ffffff_40x100.png ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 06:16:58 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: styles/images/ui-bg_flat_75_ffffff_40x100.png ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 06:16:58 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: styles/images/ui-bg_highlight-soft_75_cccccc_1x100.png ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 06:16:58 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: styles/images/ui-bg_highlight-soft_75_cccccc_1x100.png ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 06:16:58 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: styles/images/ui-bg_glass_75_dadada_1x400.png ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 06:16:58 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: styles/images/ui-bg_glass_75_dadada_1x400.png ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 06:16:58 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: styles/images/ui-icons_222222_256x240.png ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 06:16:58 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: styles/images/ui-icons_222222_256x240.png ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 06:16:58 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: styles/images/ui-bg_glass_75_e6e6e6_1x400.png ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 06:16:58 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: styles/images/ui-bg_glass_75_e6e6e6_1x400.png ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 06:17:27 --- ERROR: Database_Exception [ 1054 ]: Unknown column 'checklist_option' in 'field list' [ INSERT INTO `clients` (`client_name`, `address`, `telephone`, `contact_person`, `regi_date`, `email`, `password`, `url`, `checklist_option`, `approve`, `created_at`) VALUES ('bhagirath chauhan', 'iscon mall, rajkot', '29489089', 'bhagirath chauhan', '2013-01-23', 'ankit.s.shhah@gmail.com', 'ankit123', 'bhagirath', '1', 1, '2013-01-23 06:17:27') ] ~ MODPATH/database/classes/kohana/database/mysql.php [ 194 ]
2013-01-23 06:17:27 --- STRACE: Database_Exception [ 1054 ]: Unknown column 'checklist_option' in 'field list' [ INSERT INTO `clients` (`client_name`, `address`, `telephone`, `contact_person`, `regi_date`, `email`, `password`, `url`, `checklist_option`, `approve`, `created_at`) VALUES ('bhagirath chauhan', 'iscon mall, rajkot', '29489089', 'bhagirath chauhan', '2013-01-23', 'ankit.s.shhah@gmail.com', 'ankit123', 'bhagirath', '1', 1, '2013-01-23 06:17:27') ] ~ MODPATH/database/classes/kohana/database/mysql.php [ 194 ]
--
#0 /home/codesafe/public_html/modules/database/classes/kohana/database/query.php(245): Kohana_Database_MySQL->query(2, 'INSERT INTO `cl...', false, Array)
#1 /home/codesafe/public_html/siteadmin/application/classes/model/client.php(15): Kohana_Database_Query->execute()
#2 /home/codesafe/public_html/siteadmin/application/classes/controller/client.php(287): Model_Client->addclient(Array)
#3 [internal function]: Controller_Client->action_add()
#4 /home/codesafe/public_html/system/classes/kohana/request/client/internal.php(118): ReflectionMethod->invoke(Object(Controller_Client))
#5 /home/codesafe/public_html/system/classes/kohana/request/client.php(64): Kohana_Request_Client_Internal->execute_request(Object(Request))
#6 /home/codesafe/public_html/system/classes/kohana/request.php(1138): Kohana_Request_Client->execute(Object(Request))
#7 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#8 {main}
2013-01-23 06:20:12 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 06:20:12 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 06:20:13 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 06:20:13 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 06:20:30 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 06:20:30 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 06:20:30 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 06:20:30 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 06:21:18 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 06:21:18 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2013-01-23 06:21:18 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2013-01-23 06:21:18 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}