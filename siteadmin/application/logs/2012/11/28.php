<?php defined('SYSPATH') or die('No direct script access.'); ?>

2012-11-28 18:59:12 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: styles/images/ui-bg_flat_75_ffffff_40x100.png ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2012-11-28 18:59:12 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: styles/images/ui-bg_flat_75_ffffff_40x100.png ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2012-11-28 19:00:16 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: styles/images/ui-bg_highlight-soft_75_cccccc_1x100.png ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2012-11-28 19:00:16 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: styles/images/ui-bg_highlight-soft_75_cccccc_1x100.png ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2012-11-28 19:00:16 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: styles/images/ui-icons_222222_256x240.png ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2012-11-28 19:00:16 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: styles/images/ui-icons_222222_256x240.png ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2012-11-28 19:00:16 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: styles/images/ui-bg_glass_55_fbf9ee_1x400.png ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2012-11-28 19:00:16 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: styles/images/ui-bg_glass_55_fbf9ee_1x400.png ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2012-11-28 19:00:16 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: styles/images/ui-bg_glass_75_e6e6e6_1x400.png ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2012-11-28 19:00:16 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: styles/images/ui-bg_glass_75_e6e6e6_1x400.png ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2012-11-28 19:00:17 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: styles/images/ui-bg_glass_75_dadada_1x400.png ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2012-11-28 19:00:17 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: styles/images/ui-bg_glass_75_dadada_1x400.png ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2012-11-28 19:00:51 --- ERROR: Database_Exception [ 1054 ]: Unknown column 'expire_at' in 'field list' [ INSERT INTO `clients` (`client_name`, `address`, `telephone`, `contact_person`, `regi_date`, `email`, `password`, `url`, `approve`, `created_at`, `expire_at`) VALUES ('Alumium Scaffolding & Active Mechanical', '26 Commercial Place, East Keilor', '03 9336 3166', 'Gavin Shaw', '2012-11-29', 'gavin@scaffactive.com.au', 'gavin', 'scaffactive', 1, '2012-11-28 19:00:51', '2013-11-28 19:00:51') ] ~ MODPATH/database/classes/kohana/database/mysql.php [ 194 ]
2012-11-28 19:00:51 --- STRACE: Database_Exception [ 1054 ]: Unknown column 'expire_at' in 'field list' [ INSERT INTO `clients` (`client_name`, `address`, `telephone`, `contact_person`, `regi_date`, `email`, `password`, `url`, `approve`, `created_at`, `expire_at`) VALUES ('Alumium Scaffolding & Active Mechanical', '26 Commercial Place, East Keilor', '03 9336 3166', 'Gavin Shaw', '2012-11-29', 'gavin@scaffactive.com.au', 'gavin', 'scaffactive', 1, '2012-11-28 19:00:51', '2013-11-28 19:00:51') ] ~ MODPATH/database/classes/kohana/database/mysql.php [ 194 ]
--
#0 /home/codesafe/public_html/modules/database/classes/kohana/database/query.php(245): Kohana_Database_MySQL->query(2, 'INSERT INTO `cl...', false, Array)
#1 /home/codesafe/public_html/siteadmin/application/classes/model/client.php(15): Kohana_Database_Query->execute()
#2 /home/codesafe/public_html/siteadmin/application/classes/controller/client.php(275): Model_Client->addclient(Array)
#3 [internal function]: Controller_Client->action_add()
#4 /home/codesafe/public_html/system/classes/kohana/request/client/internal.php(118): ReflectionMethod->invoke(Object(Controller_Client))
#5 /home/codesafe/public_html/system/classes/kohana/request/client.php(64): Kohana_Request_Client_Internal->execute_request(Object(Request))
#6 /home/codesafe/public_html/system/classes/kohana/request.php(1138): Kohana_Request_Client->execute(Object(Request))
#7 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#8 {main}
2012-11-28 19:01:24 --- ERROR: Database_Exception [ 1054 ]: Unknown column 'expire_at' in 'field list' [ INSERT INTO `clients` (`client_name`, `address`, `telephone`, `contact_person`, `regi_date`, `email`, `password`, `url`, `approve`, `created_at`, `expire_at`) VALUES ('Alumium Scaffolding & Active Mechanical', '26 Commercial Place, East Keilor', '03 9336 3166', 'Gavin Shaw', '2012-11-29', 'gavin@scaffactive.com.au', 'gavin', 'scaffactive', 1, '2012-11-28 19:01:24', '2013-11-28 19:01:24') ] ~ MODPATH/database/classes/kohana/database/mysql.php [ 194 ]
2012-11-28 19:01:24 --- STRACE: Database_Exception [ 1054 ]: Unknown column 'expire_at' in 'field list' [ INSERT INTO `clients` (`client_name`, `address`, `telephone`, `contact_person`, `regi_date`, `email`, `password`, `url`, `approve`, `created_at`, `expire_at`) VALUES ('Alumium Scaffolding & Active Mechanical', '26 Commercial Place, East Keilor', '03 9336 3166', 'Gavin Shaw', '2012-11-29', 'gavin@scaffactive.com.au', 'gavin', 'scaffactive', 1, '2012-11-28 19:01:24', '2013-11-28 19:01:24') ] ~ MODPATH/database/classes/kohana/database/mysql.php [ 194 ]
--
#0 /home/codesafe/public_html/modules/database/classes/kohana/database/query.php(245): Kohana_Database_MySQL->query(2, 'INSERT INTO `cl...', false, Array)
#1 /home/codesafe/public_html/siteadmin/application/classes/model/client.php(15): Kohana_Database_Query->execute()
#2 /home/codesafe/public_html/siteadmin/application/classes/controller/client.php(275): Model_Client->addclient(Array)
#3 [internal function]: Controller_Client->action_add()
#4 /home/codesafe/public_html/system/classes/kohana/request/client/internal.php(118): ReflectionMethod->invoke(Object(Controller_Client))
#5 /home/codesafe/public_html/system/classes/kohana/request/client.php(64): Kohana_Request_Client_Internal->execute_request(Object(Request))
#6 /home/codesafe/public_html/system/classes/kohana/request.php(1138): Kohana_Request_Client->execute(Object(Request))
#7 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#8 {main}
2012-11-28 19:01:25 --- ERROR: Database_Exception [ 1054 ]: Unknown column 'expire_at' in 'field list' [ INSERT INTO `clients` (`client_name`, `address`, `telephone`, `contact_person`, `regi_date`, `email`, `password`, `url`, `approve`, `created_at`, `expire_at`) VALUES ('Alumium Scaffolding & Active Mechanical', '26 Commercial Place, East Keilor', '03 9336 3166', 'Gavin Shaw', '2012-11-29', 'gavin@scaffactive.com.au', 'gavin', 'scaffactive', 1, '2012-11-28 19:01:25', '2013-11-28 19:01:25') ] ~ MODPATH/database/classes/kohana/database/mysql.php [ 194 ]
2012-11-28 19:01:25 --- STRACE: Database_Exception [ 1054 ]: Unknown column 'expire_at' in 'field list' [ INSERT INTO `clients` (`client_name`, `address`, `telephone`, `contact_person`, `regi_date`, `email`, `password`, `url`, `approve`, `created_at`, `expire_at`) VALUES ('Alumium Scaffolding & Active Mechanical', '26 Commercial Place, East Keilor', '03 9336 3166', 'Gavin Shaw', '2012-11-29', 'gavin@scaffactive.com.au', 'gavin', 'scaffactive', 1, '2012-11-28 19:01:25', '2013-11-28 19:01:25') ] ~ MODPATH/database/classes/kohana/database/mysql.php [ 194 ]
--
#0 /home/codesafe/public_html/modules/database/classes/kohana/database/query.php(245): Kohana_Database_MySQL->query(2, 'INSERT INTO `cl...', false, Array)
#1 /home/codesafe/public_html/siteadmin/application/classes/model/client.php(15): Kohana_Database_Query->execute()
#2 /home/codesafe/public_html/siteadmin/application/classes/controller/client.php(275): Model_Client->addclient(Array)
#3 [internal function]: Controller_Client->action_add()
#4 /home/codesafe/public_html/system/classes/kohana/request/client/internal.php(118): ReflectionMethod->invoke(Object(Controller_Client))
#5 /home/codesafe/public_html/system/classes/kohana/request/client.php(64): Kohana_Request_Client_Internal->execute_request(Object(Request))
#6 /home/codesafe/public_html/system/classes/kohana/request.php(1138): Kohana_Request_Client->execute(Object(Request))
#7 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#8 {main}
2012-11-28 19:01:26 --- ERROR: Database_Exception [ 1054 ]: Unknown column 'expire_at' in 'field list' [ INSERT INTO `clients` (`client_name`, `address`, `telephone`, `contact_person`, `regi_date`, `email`, `password`, `url`, `approve`, `created_at`, `expire_at`) VALUES ('Alumium Scaffolding & Active Mechanical', '26 Commercial Place, East Keilor', '03 9336 3166', 'Gavin Shaw', '2012-11-29', 'gavin@scaffactive.com.au', 'gavin', 'scaffactive', 1, '2012-11-28 19:01:26', '2013-11-28 19:01:26') ] ~ MODPATH/database/classes/kohana/database/mysql.php [ 194 ]
2012-11-28 19:01:26 --- STRACE: Database_Exception [ 1054 ]: Unknown column 'expire_at' in 'field list' [ INSERT INTO `clients` (`client_name`, `address`, `telephone`, `contact_person`, `regi_date`, `email`, `password`, `url`, `approve`, `created_at`, `expire_at`) VALUES ('Alumium Scaffolding & Active Mechanical', '26 Commercial Place, East Keilor', '03 9336 3166', 'Gavin Shaw', '2012-11-29', 'gavin@scaffactive.com.au', 'gavin', 'scaffactive', 1, '2012-11-28 19:01:26', '2013-11-28 19:01:26') ] ~ MODPATH/database/classes/kohana/database/mysql.php [ 194 ]
--
#0 /home/codesafe/public_html/modules/database/classes/kohana/database/query.php(245): Kohana_Database_MySQL->query(2, 'INSERT INTO `cl...', false, Array)
#1 /home/codesafe/public_html/siteadmin/application/classes/model/client.php(15): Kohana_Database_Query->execute()
#2 /home/codesafe/public_html/siteadmin/application/classes/controller/client.php(275): Model_Client->addclient(Array)
#3 [internal function]: Controller_Client->action_add()
#4 /home/codesafe/public_html/system/classes/kohana/request/client/internal.php(118): ReflectionMethod->invoke(Object(Controller_Client))
#5 /home/codesafe/public_html/system/classes/kohana/request/client.php(64): Kohana_Request_Client_Internal->execute_request(Object(Request))
#6 /home/codesafe/public_html/system/classes/kohana/request.php(1138): Kohana_Request_Client->execute(Object(Request))
#7 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#8 {main}
2012-11-28 23:12:35 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: styles/images/ui-bg_flat_75_ffffff_40x100.png ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2012-11-28 23:12:35 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: styles/images/ui-bg_flat_75_ffffff_40x100.png ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2012-11-28 23:12:35 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: styles/images/ui-bg_highlight-soft_75_cccccc_1x100.png ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2012-11-28 23:12:35 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: styles/images/ui-bg_highlight-soft_75_cccccc_1x100.png ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2012-11-28 23:12:35 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: styles/images/ui-bg_glass_75_dadada_1x400.png ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2012-11-28 23:12:35 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: styles/images/ui-bg_glass_75_dadada_1x400.png ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2012-11-28 23:12:35 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: styles/images/ui-bg_glass_75_e6e6e6_1x400.png ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2012-11-28 23:12:35 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: styles/images/ui-bg_glass_75_e6e6e6_1x400.png ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2012-11-28 23:12:35 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: styles/images/ui-bg_glass_55_fbf9ee_1x400.png ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2012-11-28 23:12:35 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: styles/images/ui-bg_glass_55_fbf9ee_1x400.png ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2012-11-28 23:12:38 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: styles/images/ui-icons_222222_256x240.png ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2012-11-28 23:12:38 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: styles/images/ui-icons_222222_256x240.png ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2012-11-28 23:13:15 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2012-11-28 23:13:15 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2012-11-28 23:13:15 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2012-11-28 23:13:15 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2012-11-28 23:13:24 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: user/index/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2012-11-28 23:13:24 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: user/index/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2012-11-28 23:13:26 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: user/index/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2012-11-28 23:13:26 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: user/index/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2012-11-28 23:13:27 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: user/index/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2012-11-28 23:13:27 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: user/index/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2012-11-28 23:14:21 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: user/index/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2012-11-28 23:14:21 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: user/index/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2012-11-28 23:14:22 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: user/index/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2012-11-28 23:14:22 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: user/index/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2012-11-28 23:14:26 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: user/index/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2012-11-28 23:14:26 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: user/index/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2012-11-28 23:14:26 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: user/index/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2012-11-28 23:14:26 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: user/index/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2012-11-28 23:14:56 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: user/index/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2012-11-28 23:14:56 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: user/index/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2012-11-28 23:14:59 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: user/index/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2012-11-28 23:14:59 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: user/index/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2012-11-28 23:15:00 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: user/index/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2012-11-28 23:15:00 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: user/index/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2012-11-28 23:15:07 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: user/index/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2012-11-28 23:15:07 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: user/index/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2012-11-28 23:17:32 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: styles/images/ui-bg_glass_55_fbf9ee_1x400.png ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2012-11-28 23:17:32 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: styles/images/ui-bg_glass_75_e6e6e6_1x400.png ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2012-11-28 23:17:32 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: styles/images/ui-bg_glass_55_fbf9ee_1x400.png ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2012-11-28 23:17:32 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: styles/images/ui-bg_glass_75_e6e6e6_1x400.png ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2012-11-28 23:17:32 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: styles/images/ui-bg_highlight-soft_75_cccccc_1x100.png ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2012-11-28 23:17:32 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: styles/images/ui-bg_highlight-soft_75_cccccc_1x100.png ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2012-11-28 23:17:32 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: styles/images/ui-bg_glass_75_dadada_1x400.png ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2012-11-28 23:17:32 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: styles/images/ui-bg_glass_75_dadada_1x400.png ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2012-11-28 23:17:32 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: styles/images/ui-bg_flat_75_ffffff_40x100.png ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2012-11-28 23:17:32 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: styles/images/ui-bg_flat_75_ffffff_40x100.png ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2012-11-28 23:17:32 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: styles/images/ui-icons_222222_256x240.png ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2012-11-28 23:17:32 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: styles/images/ui-icons_222222_256x240.png ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2012-11-28 23:17:36 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: styles/images/ui-bg_glass_65_ffffff_1x400.png ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2012-11-28 23:17:36 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: styles/images/ui-bg_glass_65_ffffff_1x400.png ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2012-11-28 23:17:56 --- ERROR: Database_Exception [ 1054 ]: Unknown column 'expire_at' in 'field list' [ INSERT INTO `clients` (`client_name`, `address`, `telephone`, `contact_person`, `regi_date`, `email`, `password`, `url`, `approve`, `created_at`, `expire_at`) VALUES ('ashish', 'rajkot', '1234567890', 'ashish', '2012-11-29', 'ashish@webplanex.net', '123456', 'ashish', 1, '2012-11-28 23:17:56', '2013-11-28 23:17:56') ] ~ MODPATH/database/classes/kohana/database/mysql.php [ 194 ]
2012-11-28 23:17:56 --- STRACE: Database_Exception [ 1054 ]: Unknown column 'expire_at' in 'field list' [ INSERT INTO `clients` (`client_name`, `address`, `telephone`, `contact_person`, `regi_date`, `email`, `password`, `url`, `approve`, `created_at`, `expire_at`) VALUES ('ashish', 'rajkot', '1234567890', 'ashish', '2012-11-29', 'ashish@webplanex.net', '123456', 'ashish', 1, '2012-11-28 23:17:56', '2013-11-28 23:17:56') ] ~ MODPATH/database/classes/kohana/database/mysql.php [ 194 ]
--
#0 /home/codesafe/public_html/modules/database/classes/kohana/database/query.php(245): Kohana_Database_MySQL->query(2, 'INSERT INTO `cl...', false, Array)
#1 /home/codesafe/public_html/siteadmin/application/classes/model/client.php(15): Kohana_Database_Query->execute()
#2 /home/codesafe/public_html/siteadmin/application/classes/controller/client.php(275): Model_Client->addclient(Array)
#3 [internal function]: Controller_Client->action_add()
#4 /home/codesafe/public_html/system/classes/kohana/request/client/internal.php(118): ReflectionMethod->invoke(Object(Controller_Client))
#5 /home/codesafe/public_html/system/classes/kohana/request/client.php(64): Kohana_Request_Client_Internal->execute_request(Object(Request))
#6 /home/codesafe/public_html/system/classes/kohana/request.php(1138): Kohana_Request_Client->execute(Object(Request))
#7 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#8 {main}
2012-11-28 23:23:19 --- ERROR: ErrorException [ 8 ]: Undefined variable: postArr ~ APPPATH/classes/model/client.php [ 17 ]
2012-11-28 23:23:19 --- STRACE: ErrorException [ 8 ]: Undefined variable: postArr ~ APPPATH/classes/model/client.php [ 17 ]
--
#0 /home/codesafe/public_html/siteadmin/application/classes/model/client.php(17): Kohana_Core::error_handler(Array)
#1 /home/codesafe/public_html/siteadmin/application/classes/controller/client.php(274): Model_Client->addclient()
#2 [internal function]: Controller_Client->action_add(Object(Controller_Client))
#3 /home/codesafe/public_html/system/classes/kohana/request/client/internal.php(118): ReflectionMethod->invoke(Object(Request))
#4 /home/codesafe/public_html/system/classes/kohana/request/client.php(64): Kohana_Request_Client_Internal->execute_request(Object(Request))
#5 /home/codesafe/public_html/system/classes/kohana/request.php(1138): Kohana_Request_Client->execute()
#6 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#7 {main}