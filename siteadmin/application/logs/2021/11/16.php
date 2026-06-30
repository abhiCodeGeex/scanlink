<?php defined('SYSPATH') or die('No direct script access.'); ?>

2021-11-16 20:22:14 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1128 ]
2021-11-16 20:22:14 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1128 ]
--
#0 /var/www/scanlink.com.au/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2021-11-16 20:22:14 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1128 ]
2021-11-16 20:22:14 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1128 ]
--
#0 /var/www/scanlink.com.au/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2021-11-16 20:22:29 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1128 ]
2021-11-16 20:22:29 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1128 ]
--
#0 /var/www/scanlink.com.au/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2021-11-16 20:22:29 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1128 ]
2021-11-16 20:22:29 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1128 ]
--
#0 /var/www/scanlink.com.au/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2021-11-16 20:23:50 --- ERROR: Database_Exception [ 1242 ]: Subquery returns more than 1 row [ SELECT `id` FROM `users` WHERE `email` = 'shaileshk@webplanex.com' AND `id` != (SELECT `id` FROM `users` WHERE `client_id` = '272' AND `role` = 5) ] ~ MODPATH/database/classes/kohana/database/mysql.php [ 194 ]
2021-11-16 20:23:50 --- STRACE: Database_Exception [ 1242 ]: Subquery returns more than 1 row [ SELECT `id` FROM `users` WHERE `email` = 'shaileshk@webplanex.com' AND `id` != (SELECT `id` FROM `users` WHERE `client_id` = '272' AND `role` = 5) ] ~ MODPATH/database/classes/kohana/database/mysql.php [ 194 ]
--
#0 /var/www/scanlink.com.au/modules/database/classes/kohana/database/query.php(245): Kohana_Database_MySQL->query(1, 'SELECT `id` FRO...', false, Array)
#1 /var/www/scanlink.com.au/siteadmin/application/classes/model/client.php(109): Kohana_Database_Query->execute()
#2 /var/www/scanlink.com.au/siteadmin/application/classes/controller/client.php(183): Model_Client->getdupemail(Array)
#3 [internal function]: Controller_Client->action_edit()
#4 /var/www/scanlink.com.au/system/classes/kohana/request/client/internal.php(118): ReflectionMethod->invoke(Object(Controller_Client))
#5 /var/www/scanlink.com.au/system/classes/kohana/request/client.php(64): Kohana_Request_Client_Internal->execute_request(Object(Request))
#6 /var/www/scanlink.com.au/system/classes/kohana/request.php(1140): Kohana_Request_Client->execute(Object(Request))
#7 /var/www/scanlink.com.au/siteadmin/index.php(132): Kohana_Request->execute()
#8 {main}
2021-11-16 20:24:14 --- ERROR: Database_Exception [ 1242 ]: Subquery returns more than 1 row [ SELECT `id` FROM `users` WHERE `email` = 'shaileshk@webplanex.com' AND `id` != (SELECT `id` FROM `users` WHERE `client_id` = '272' AND `role` = 5) ] ~ MODPATH/database/classes/kohana/database/mysql.php [ 194 ]
2021-11-16 20:24:14 --- STRACE: Database_Exception [ 1242 ]: Subquery returns more than 1 row [ SELECT `id` FROM `users` WHERE `email` = 'shaileshk@webplanex.com' AND `id` != (SELECT `id` FROM `users` WHERE `client_id` = '272' AND `role` = 5) ] ~ MODPATH/database/classes/kohana/database/mysql.php [ 194 ]
--
#0 /var/www/scanlink.com.au/modules/database/classes/kohana/database/query.php(245): Kohana_Database_MySQL->query(1, 'SELECT `id` FRO...', false, Array)
#1 /var/www/scanlink.com.au/siteadmin/application/classes/model/client.php(109): Kohana_Database_Query->execute()
#2 /var/www/scanlink.com.au/siteadmin/application/classes/controller/client.php(183): Model_Client->getdupemail(Array)
#3 [internal function]: Controller_Client->action_edit()
#4 /var/www/scanlink.com.au/system/classes/kohana/request/client/internal.php(118): ReflectionMethod->invoke(Object(Controller_Client))
#5 /var/www/scanlink.com.au/system/classes/kohana/request/client.php(64): Kohana_Request_Client_Internal->execute_request(Object(Request))
#6 /var/www/scanlink.com.au/system/classes/kohana/request.php(1140): Kohana_Request_Client->execute(Object(Request))
#7 /var/www/scanlink.com.au/siteadmin/index.php(132): Kohana_Request->execute()
#8 {main}
2021-11-16 20:37:15 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1128 ]
2021-11-16 20:37:15 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1128 ]
--
#0 /var/www/scanlink.com.au/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2021-11-16 20:37:15 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1128 ]
2021-11-16 20:37:15 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1128 ]
--
#0 /var/www/scanlink.com.au/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2021-11-16 20:37:23 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1128 ]
2021-11-16 20:37:23 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1128 ]
--
#0 /var/www/scanlink.com.au/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2021-11-16 20:37:23 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1128 ]
2021-11-16 20:37:23 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1128 ]
--
#0 /var/www/scanlink.com.au/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2021-11-16 20:37:25 --- ERROR: Database_Exception [ 1242 ]: Subquery returns more than 1 row [ SELECT `id` FROM `users` WHERE `email` = 'shaileshk@webplanex.com' AND `id` != (SELECT `id` FROM `users` WHERE `client_id` = '272' AND `role` = 5) ] ~ MODPATH/database/classes/kohana/database/mysql.php [ 194 ]
2021-11-16 20:37:25 --- STRACE: Database_Exception [ 1242 ]: Subquery returns more than 1 row [ SELECT `id` FROM `users` WHERE `email` = 'shaileshk@webplanex.com' AND `id` != (SELECT `id` FROM `users` WHERE `client_id` = '272' AND `role` = 5) ] ~ MODPATH/database/classes/kohana/database/mysql.php [ 194 ]
--
#0 /var/www/scanlink.com.au/modules/database/classes/kohana/database/query.php(245): Kohana_Database_MySQL->query(1, 'SELECT `id` FRO...', false, Array)
#1 /var/www/scanlink.com.au/siteadmin/application/classes/model/client.php(109): Kohana_Database_Query->execute()
#2 /var/www/scanlink.com.au/siteadmin/application/classes/controller/client.php(183): Model_Client->getdupemail(Array)
#3 [internal function]: Controller_Client->action_edit()
#4 /var/www/scanlink.com.au/system/classes/kohana/request/client/internal.php(118): ReflectionMethod->invoke(Object(Controller_Client))
#5 /var/www/scanlink.com.au/system/classes/kohana/request/client.php(64): Kohana_Request_Client_Internal->execute_request(Object(Request))
#6 /var/www/scanlink.com.au/system/classes/kohana/request.php(1140): Kohana_Request_Client->execute(Object(Request))
#7 /var/www/scanlink.com.au/siteadmin/index.php(132): Kohana_Request->execute()
#8 {main}
2021-11-16 20:37:30 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1128 ]
2021-11-16 20:37:30 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1128 ]
--
#0 /var/www/scanlink.com.au/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2021-11-16 20:37:30 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1128 ]
2021-11-16 20:37:30 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1128 ]
--
#0 /var/www/scanlink.com.au/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2021-11-16 20:37:36 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1128 ]
2021-11-16 20:37:36 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1128 ]
--
#0 /var/www/scanlink.com.au/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2021-11-16 20:37:37 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1128 ]
2021-11-16 20:37:37 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1128 ]
--
#0 /var/www/scanlink.com.au/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2021-11-16 20:37:45 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1128 ]
2021-11-16 20:37:45 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1128 ]
--
#0 /var/www/scanlink.com.au/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2021-11-16 20:37:45 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1128 ]
2021-11-16 20:37:45 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1128 ]
--
#0 /var/www/scanlink.com.au/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2021-11-16 20:39:05 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: styles/images/ui-bg_flat_75_ffffff_40x100.png ~ SYSPATH/classes/kohana/request.php [ 1128 ]
2021-11-16 20:39:05 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: styles/images/ui-bg_flat_75_ffffff_40x100.png ~ SYSPATH/classes/kohana/request.php [ 1128 ]
--
#0 /var/www/scanlink.com.au/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2021-11-16 20:39:05 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: styles/images/ui-bg_highlight-soft_75_cccccc_1x100.png ~ SYSPATH/classes/kohana/request.php [ 1128 ]
2021-11-16 20:39:05 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: styles/images/ui-bg_highlight-soft_75_cccccc_1x100.png ~ SYSPATH/classes/kohana/request.php [ 1128 ]
--
#0 /var/www/scanlink.com.au/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2021-11-16 20:39:05 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: styles/images/ui-bg_glass_55_fbf9ee_1x400.png ~ SYSPATH/classes/kohana/request.php [ 1128 ]
2021-11-16 20:39:05 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: styles/images/ui-bg_glass_55_fbf9ee_1x400.png ~ SYSPATH/classes/kohana/request.php [ 1128 ]
--
#0 /var/www/scanlink.com.au/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2021-11-16 20:39:05 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: styles/images/ui-bg_glass_75_e6e6e6_1x400.png ~ SYSPATH/classes/kohana/request.php [ 1128 ]
2021-11-16 20:39:05 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: styles/images/ui-bg_glass_75_e6e6e6_1x400.png ~ SYSPATH/classes/kohana/request.php [ 1128 ]
--
#0 /var/www/scanlink.com.au/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2021-11-16 20:39:05 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: styles/images/ui-icons_222222_256x240.png ~ SYSPATH/classes/kohana/request.php [ 1128 ]
2021-11-16 20:39:05 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: styles/images/ui-icons_222222_256x240.png ~ SYSPATH/classes/kohana/request.php [ 1128 ]
--
#0 /var/www/scanlink.com.au/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2021-11-16 20:39:27 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1128 ]
2021-11-16 20:39:27 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1128 ]
--
#0 /var/www/scanlink.com.au/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2021-11-16 20:39:27 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1128 ]
2021-11-16 20:39:27 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1128 ]
--
#0 /var/www/scanlink.com.au/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2021-11-16 20:39:55 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: styles/images/ui-bg_glass_55_fbf9ee_1x400.png ~ SYSPATH/classes/kohana/request.php [ 1128 ]
2021-11-16 20:39:55 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: styles/images/ui-bg_glass_55_fbf9ee_1x400.png ~ SYSPATH/classes/kohana/request.php [ 1128 ]
--
#0 /var/www/scanlink.com.au/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2021-11-16 20:39:55 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: styles/images/ui-icons_222222_256x240.png ~ SYSPATH/classes/kohana/request.php [ 1128 ]
2021-11-16 20:39:55 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: styles/images/ui-icons_222222_256x240.png ~ SYSPATH/classes/kohana/request.php [ 1128 ]
--
#0 /var/www/scanlink.com.au/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2021-11-16 20:39:55 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: styles/images/ui-bg_flat_75_ffffff_40x100.png ~ SYSPATH/classes/kohana/request.php [ 1128 ]
2021-11-16 20:39:55 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: styles/images/ui-bg_flat_75_ffffff_40x100.png ~ SYSPATH/classes/kohana/request.php [ 1128 ]
--
#0 /var/www/scanlink.com.au/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2021-11-16 20:39:55 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: styles/images/ui-bg_glass_75_e6e6e6_1x400.png ~ SYSPATH/classes/kohana/request.php [ 1128 ]
2021-11-16 20:39:55 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: styles/images/ui-bg_glass_75_e6e6e6_1x400.png ~ SYSPATH/classes/kohana/request.php [ 1128 ]
--
#0 /var/www/scanlink.com.au/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2021-11-16 20:39:55 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: styles/images/ui-bg_highlight-soft_75_cccccc_1x100.png ~ SYSPATH/classes/kohana/request.php [ 1128 ]
2021-11-16 20:39:55 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: styles/images/ui-bg_highlight-soft_75_cccccc_1x100.png ~ SYSPATH/classes/kohana/request.php [ 1128 ]
--
#0 /var/www/scanlink.com.au/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2021-11-16 20:40:44 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1128 ]
2021-11-16 20:40:44 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1128 ]
--
#0 /var/www/scanlink.com.au/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2021-11-16 20:40:44 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1128 ]
2021-11-16 20:40:44 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1128 ]
--
#0 /var/www/scanlink.com.au/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2021-11-16 20:40:56 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1128 ]
2021-11-16 20:40:56 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1128 ]
--
#0 /var/www/scanlink.com.au/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2021-11-16 20:40:56 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1128 ]
2021-11-16 20:40:56 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1128 ]
--
#0 /var/www/scanlink.com.au/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2021-11-16 20:41:03 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1128 ]
2021-11-16 20:41:03 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1128 ]
--
#0 /var/www/scanlink.com.au/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2021-11-16 20:41:05 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1128 ]
2021-11-16 20:41:05 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1128 ]
--
#0 /var/www/scanlink.com.au/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2021-11-16 20:41:28 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1128 ]
2021-11-16 20:41:28 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1128 ]
--
#0 /var/www/scanlink.com.au/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2021-11-16 20:41:28 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1128 ]
2021-11-16 20:41:28 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1128 ]
--
#0 /var/www/scanlink.com.au/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2021-11-16 20:41:31 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1128 ]
2021-11-16 20:41:31 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1128 ]
--
#0 /var/www/scanlink.com.au/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2021-11-16 20:41:32 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1128 ]
2021-11-16 20:41:32 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1128 ]
--
#0 /var/www/scanlink.com.au/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2021-11-16 20:41:41 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1128 ]
2021-11-16 20:41:41 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1128 ]
--
#0 /var/www/scanlink.com.au/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2021-11-16 20:41:42 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1128 ]
2021-11-16 20:41:42 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1128 ]
--
#0 /var/www/scanlink.com.au/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2021-11-16 20:41:46 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1128 ]
2021-11-16 20:41:46 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1128 ]
--
#0 /var/www/scanlink.com.au/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2021-11-16 20:41:47 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1128 ]
2021-11-16 20:41:47 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1128 ]
--
#0 /var/www/scanlink.com.au/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}