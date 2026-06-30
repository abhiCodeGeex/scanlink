<?php defined('SYSPATH') or die('No direct script access.'); ?>

2012-08-30 00:27:34 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: order/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2012-08-30 00:27:34 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: order/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2012-08-30 00:27:35 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: order/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2012-08-30 00:27:35 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: order/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2012-08-30 00:27:35 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: order/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2012-08-30 00:27:35 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: order/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2012-08-30 00:28:05 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: order/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2012-08-30 00:28:05 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: order/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2012-08-30 00:28:05 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: order/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2012-08-30 00:28:05 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: order/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2012-08-30 00:44:20 --- ERROR: HTTP_Exception_404 [ 404 ]: The requested URL settings/index was not found on this server. ~ SYSPATH/classes/kohana/request/client/internal.php [ 87 ]
2012-08-30 00:44:20 --- STRACE: HTTP_Exception_404 [ 404 ]: The requested URL settings/index was not found on this server. ~ SYSPATH/classes/kohana/request/client/internal.php [ 87 ]
--
#0 /home/codesafe/public_html/system/classes/kohana/request/client.php(64): Kohana_Request_Client_Internal->execute_request(Object(Request))
#1 /home/codesafe/public_html/system/classes/kohana/request.php(1138): Kohana_Request_Client->execute(Object(Request))
#2 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#3 {main}
2012-08-30 01:11:22 --- ERROR: HTTP_Exception_404 [ 404 ]: The requested URL settings/index was not found on this server. ~ SYSPATH/classes/kohana/request/client/internal.php [ 87 ]
2012-08-30 01:11:22 --- STRACE: HTTP_Exception_404 [ 404 ]: The requested URL settings/index was not found on this server. ~ SYSPATH/classes/kohana/request/client/internal.php [ 87 ]
--
#0 /home/codesafe/public_html/system/classes/kohana/request/client.php(64): Kohana_Request_Client_Internal->execute_request(Object(Request))
#1 /home/codesafe/public_html/system/classes/kohana/request.php(1138): Kohana_Request_Client->execute(Object(Request))
#2 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#3 {main}
2012-08-30 01:22:25 --- ERROR: HTTP_Exception_404 [ 404 ]: The requested URL settings/index was not found on this server. ~ SYSPATH/classes/kohana/request/client/internal.php [ 87 ]
2012-08-30 01:22:25 --- STRACE: HTTP_Exception_404 [ 404 ]: The requested URL settings/index was not found on this server. ~ SYSPATH/classes/kohana/request/client/internal.php [ 87 ]
--
#0 /home/codesafe/public_html/system/classes/kohana/request/client.php(64): Kohana_Request_Client_Internal->execute_request(Object(Request))
#1 /home/codesafe/public_html/system/classes/kohana/request.php(1138): Kohana_Request_Client->execute(Object(Request))
#2 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#3 {main}
2012-08-30 01:23:34 --- ERROR: ErrorException [ 1 ]: Class 'Model_settings' not found ~ SYSPATH/classes/kohana/model.php [ 26 ]
2012-08-30 01:23:34 --- STRACE: ErrorException [ 1 ]: Class 'Model_settings' not found ~ SYSPATH/classes/kohana/model.php [ 26 ]
--
#0 [internal function]: Kohana_Core::shutdown_handler('settings')
#1 {main}
2012-08-30 01:26:59 --- ERROR: ErrorException [ 8 ]: Undefined variable: succmsg ~ APPPATH/views/settings/index.php [ 12 ]
2012-08-30 01:26:59 --- STRACE: ErrorException [ 8 ]: Undefined variable: succmsg ~ APPPATH/views/settings/index.php [ 12 ]
--
#0 /home/codesafe/public_html/siteadmin/application/views/settings/index.php(12): Kohana_Core::error_handler('/home/codesafe/...', Array)
#1 /home/codesafe/public_html/system/classes/kohana/view.php(61): include('/home/codesafe/...')
#2 /home/codesafe/public_html/system/classes/kohana/view.php(343): Kohana_View::capture()
#3 /home/codesafe/public_html/system/classes/kohana/view.php(228): Kohana_View->render()
#4 /home/codesafe/public_html/siteadmin/application/views/template/template.php(9): Kohana_View->__toString('/home/codesafe/...', Array)
#5 /home/codesafe/public_html/system/classes/kohana/view.php(61): include('/home/codesafe/...')
#6 /home/codesafe/public_html/system/classes/kohana/view.php(343): Kohana_View::capture()
#7 /home/codesafe/public_html/system/classes/kohana/controller/template.php(44): Kohana_View->render()
#8 [internal function]: Kohana_Controller_Template->after(Object(Controller_Settings))
#9 /home/codesafe/public_html/system/classes/kohana/request/client/internal.php(121): ReflectionMethod->invoke(Object(Request))
#10 /home/codesafe/public_html/system/classes/kohana/request/client.php(64): Kohana_Request_Client_Internal->execute_request(Object(Request))
#11 /home/codesafe/public_html/system/classes/kohana/request.php(1138): Kohana_Request_Client->execute()
#12 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#13 {main}
2012-08-30 01:30:13 --- ERROR: ErrorException [ 1 ]: Cannot pass parameter 2 by reference ~ APPPATH/classes/controller/settings.php [ 50 ]
2012-08-30 01:30:13 --- STRACE: ErrorException [ 1 ]: Cannot pass parameter 2 by reference ~ APPPATH/classes/controller/settings.php [ 50 ]
--
#0 [internal function]: Kohana_Core::shutdown_handler()
#1 {main}
2012-08-30 01:30:40 --- ERROR: ErrorException [ 1 ]: Cannot pass parameter 2 by reference ~ APPPATH/classes/controller/settings.php [ 50 ]
2012-08-30 01:30:40 --- STRACE: ErrorException [ 1 ]: Cannot pass parameter 2 by reference ~ APPPATH/classes/controller/settings.php [ 50 ]
--
#0 [internal function]: Kohana_Core::shutdown_handler()
#1 {main}
2012-08-30 01:31:07 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2012-08-30 01:31:07 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2012-08-30 01:31:07 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2012-08-30 01:31:07 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2012-08-30 01:31:15 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2012-08-30 01:31:15 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2012-08-30 01:31:16 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2012-08-30 01:31:16 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2012-08-30 01:31:22 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2012-08-30 01:31:22 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2012-08-30 02:37:35 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2012-08-30 02:37:35 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2012-08-30 02:37:35 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2012-08-30 02:37:35 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2012-08-30 02:37:35 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2012-08-30 02:37:35 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2012-08-30 04:02:18 --- ERROR: ErrorException [ 1 ]: Cannot pass parameter 2 by reference ~ APPPATH/classes/controller/settings.php [ 50 ]
2012-08-30 04:02:18 --- STRACE: ErrorException [ 1 ]: Cannot pass parameter 2 by reference ~ APPPATH/classes/controller/settings.php [ 50 ]
--
#0 [internal function]: Kohana_Core::shutdown_handler()
#1 {main}
2012-08-30 04:15:37 --- ERROR: ErrorException [ 1 ]: Cannot pass parameter 2 by reference ~ APPPATH/classes/controller/settings.php [ 50 ]
2012-08-30 04:15:37 --- STRACE: ErrorException [ 1 ]: Cannot pass parameter 2 by reference ~ APPPATH/classes/controller/settings.php [ 50 ]
--
#0 [internal function]: Kohana_Core::shutdown_handler()
#1 {main}
2012-08-30 04:24:04 --- ERROR: ErrorException [ 1 ]: Cannot pass parameter 2 by reference ~ APPPATH/classes/controller/settings.php [ 49 ]
2012-08-30 04:24:04 --- STRACE: ErrorException [ 1 ]: Cannot pass parameter 2 by reference ~ APPPATH/classes/controller/settings.php [ 49 ]
--
#0 [internal function]: Kohana_Core::shutdown_handler()
#1 {main}
2012-08-30 04:26:01 --- ERROR: ErrorException [ 8 ]: Undefined variable: last_id ~ APPPATH/views/settings/index.php [ 17 ]
2012-08-30 04:26:01 --- STRACE: ErrorException [ 8 ]: Undefined variable: last_id ~ APPPATH/views/settings/index.php [ 17 ]
--
#0 /home/codesafe/public_html/siteadmin/application/views/settings/index.php(17): Kohana_Core::error_handler('/home/codesafe/...', Array)
#1 /home/codesafe/public_html/system/classes/kohana/view.php(61): include('/home/codesafe/...')
#2 /home/codesafe/public_html/system/classes/kohana/view.php(343): Kohana_View::capture()
#3 /home/codesafe/public_html/system/classes/kohana/view.php(228): Kohana_View->render()
#4 /home/codesafe/public_html/siteadmin/application/views/template/template.php(9): Kohana_View->__toString('/home/codesafe/...', Array)
#5 /home/codesafe/public_html/system/classes/kohana/view.php(61): include('/home/codesafe/...')
#6 /home/codesafe/public_html/system/classes/kohana/view.php(343): Kohana_View::capture()
#7 /home/codesafe/public_html/system/classes/kohana/controller/template.php(44): Kohana_View->render()
#8 [internal function]: Kohana_Controller_Template->after(Object(Controller_Settings))
#9 /home/codesafe/public_html/system/classes/kohana/request/client/internal.php(121): ReflectionMethod->invoke(Object(Request))
#10 /home/codesafe/public_html/system/classes/kohana/request/client.php(64): Kohana_Request_Client_Internal->execute_request(Object(Request))
#11 /home/codesafe/public_html/system/classes/kohana/request.php(1138): Kohana_Request_Client->execute()
#12 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#13 {main}
2012-08-30 04:26:47 --- ERROR: ErrorException [ 8 ]: Undefined index:  txtClientname ~ APPPATH/views/settings/index.php [ 28 ]
2012-08-30 04:26:47 --- STRACE: ErrorException [ 8 ]: Undefined index:  txtClientname ~ APPPATH/views/settings/index.php [ 28 ]
--
#0 /home/codesafe/public_html/siteadmin/application/views/settings/index.php(28): Kohana_Core::error_handler('/home/codesafe/...', Array)
#1 /home/codesafe/public_html/system/classes/kohana/view.php(61): include('/home/codesafe/...')
#2 /home/codesafe/public_html/system/classes/kohana/view.php(343): Kohana_View::capture()
#3 /home/codesafe/public_html/system/classes/kohana/view.php(228): Kohana_View->render()
#4 /home/codesafe/public_html/siteadmin/application/views/template/template.php(9): Kohana_View->__toString('/home/codesafe/...', Array)
#5 /home/codesafe/public_html/system/classes/kohana/view.php(61): include('/home/codesafe/...')
#6 /home/codesafe/public_html/system/classes/kohana/view.php(343): Kohana_View::capture()
#7 /home/codesafe/public_html/system/classes/kohana/controller/template.php(44): Kohana_View->render()
#8 [internal function]: Kohana_Controller_Template->after(Object(Controller_Settings))
#9 /home/codesafe/public_html/system/classes/kohana/request/client/internal.php(121): ReflectionMethod->invoke(Object(Request))
#10 /home/codesafe/public_html/system/classes/kohana/request/client.php(64): Kohana_Request_Client_Internal->execute_request(Object(Request))
#11 /home/codesafe/public_html/system/classes/kohana/request.php(1138): Kohana_Request_Client->execute()
#12 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#13 {main}
2012-08-30 06:54:19 --- ERROR: ErrorException [ 1 ]: Call to undefined function  arra_pop() ~ APPPATH/classes/controller/settings.php [ 50 ]
2012-08-30 06:54:19 --- STRACE: ErrorException [ 1 ]: Call to undefined function  arra_pop() ~ APPPATH/classes/controller/settings.php [ 50 ]
--
#0 [internal function]: Kohana_Core::shutdown_handler()
#1 {main}
2012-08-30 07:30:58 --- ERROR: ErrorException [ 8 ]: Undefined variable: client ~ APPPATH/classes/model/settings.php [ 73 ]
2012-08-30 07:30:58 --- STRACE: ErrorException [ 8 ]: Undefined variable: client ~ APPPATH/classes/model/settings.php [ 73 ]
--
#0 /home/codesafe/public_html/siteadmin/application/classes/model/settings.php(73): Kohana_Core::error_handler(Array)
#1 /home/codesafe/public_html/siteadmin/application/classes/controller/settings.php(60): Model_Settings->editallsettings()
#2 [internal function]: Controller_Settings->action_index(Object(Controller_Settings))
#3 /home/codesafe/public_html/system/classes/kohana/request/client/internal.php(118): ReflectionMethod->invoke(Object(Request))
#4 /home/codesafe/public_html/system/classes/kohana/request/client.php(64): Kohana_Request_Client_Internal->execute_request(Object(Request))
#5 /home/codesafe/public_html/system/classes/kohana/request.php(1138): Kohana_Request_Client->execute()
#6 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#7 {main}
2012-08-30 07:31:44 --- ERROR: ErrorException [ 8 ]: Undefined variable: client ~ APPPATH/classes/model/settings.php [ 73 ]
2012-08-30 07:31:44 --- STRACE: ErrorException [ 8 ]: Undefined variable: client ~ APPPATH/classes/model/settings.php [ 73 ]
--
#0 /home/codesafe/public_html/siteadmin/application/classes/model/settings.php(73): Kohana_Core::error_handler(Array)
#1 /home/codesafe/public_html/siteadmin/application/classes/controller/settings.php(60): Model_Settings->editallsettings()
#2 [internal function]: Controller_Settings->action_index(Object(Controller_Settings))
#3 /home/codesafe/public_html/system/classes/kohana/request/client/internal.php(118): ReflectionMethod->invoke(Object(Request))
#4 /home/codesafe/public_html/system/classes/kohana/request/client.php(64): Kohana_Request_Client_Internal->execute_request(Object(Request))
#5 /home/codesafe/public_html/system/classes/kohana/request.php(1138): Kohana_Request_Client->execute()
#6 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#7 {main}
2012-08-30 07:40:44 --- ERROR: ErrorException [ 8 ]: Undefined variable: client ~ APPPATH/classes/model/settings.php [ 73 ]
2012-08-30 07:40:44 --- STRACE: ErrorException [ 8 ]: Undefined variable: client ~ APPPATH/classes/model/settings.php [ 73 ]
--
#0 /home/codesafe/public_html/siteadmin/application/classes/model/settings.php(73): Kohana_Core::error_handler(Array)
#1 /home/codesafe/public_html/siteadmin/application/classes/controller/settings.php(60): Model_Settings->editallsettings()
#2 [internal function]: Controller_Settings->action_index(Object(Controller_Settings))
#3 /home/codesafe/public_html/system/classes/kohana/request/client/internal.php(118): ReflectionMethod->invoke(Object(Request))
#4 /home/codesafe/public_html/system/classes/kohana/request/client.php(64): Kohana_Request_Client_Internal->execute_request(Object(Request))
#5 /home/codesafe/public_html/system/classes/kohana/request.php(1138): Kohana_Request_Client->execute()
#6 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#7 {main}
2012-08-30 07:44:57 --- ERROR: ErrorException [ 4096 ]: Argument 1 passed to Kohana_Database_Query_Builder_Update::set() must be an array, string given, called in /home/codesafe/public_html/siteadmin/application/classes/model/settings.php on line 74 and defined ~ MODPATH/database/classes/kohana/database/query/builder/update.php [ 56 ]
2012-08-30 07:44:57 --- STRACE: ErrorException [ 4096 ]: Argument 1 passed to Kohana_Database_Query_Builder_Update::set() must be an array, string given, called in /home/codesafe/public_html/siteadmin/application/classes/model/settings.php on line 74 and defined ~ MODPATH/database/classes/kohana/database/query/builder/update.php [ 56 ]
--
#0 /home/codesafe/public_html/modules/database/classes/kohana/database/query/builder/update.php(56): Kohana_Core::error_handler('orders@codesafe...')
#1 /home/codesafe/public_html/siteadmin/application/classes/model/settings.php(74): Kohana_Database_Query_Builder_Update->set(Array)
#2 /home/codesafe/public_html/siteadmin/application/classes/controller/settings.php(61): Model_Settings->editallsettings()
#3 [internal function]: Controller_Settings->action_index(Object(Controller_Settings))
#4 /home/codesafe/public_html/system/classes/kohana/request/client/internal.php(118): ReflectionMethod->invoke(Object(Request))
#5 /home/codesafe/public_html/system/classes/kohana/request/client.php(64): Kohana_Request_Client_Internal->execute_request(Object(Request))
#6 /home/codesafe/public_html/system/classes/kohana/request.php(1138): Kohana_Request_Client->execute()
#7 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#8 {main}
2012-08-30 09:05:39 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2012-08-30 09:05:39 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2012-08-30 09:05:39 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2012-08-30 09:05:39 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2012-08-30 09:18:41 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2012-08-30 09:18:41 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2012-08-30 09:18:41 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2012-08-30 09:18:41 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2012-08-30 09:18:52 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2012-08-30 09:18:52 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2012-08-30 09:18:53 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2012-08-30 09:18:53 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2012-08-30 09:21:04 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2012-08-30 09:21:04 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2012-08-30 09:21:04 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2012-08-30 09:21:04 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2012-08-30 09:21:09 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2012-08-30 09:21:09 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2012-08-30 09:21:09 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2012-08-30 09:21:09 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2012-08-30 09:30:57 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2012-08-30 09:30:57 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2012-08-30 09:32:07 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2012-08-30 09:32:07 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2012-08-30 09:32:07 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2012-08-30 09:32:07 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2012-08-30 09:32:24 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2012-08-30 09:32:24 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2012-08-30 09:33:35 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2012-08-30 09:33:35 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2012-08-30 09:33:36 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2012-08-30 09:33:36 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2012-08-30 09:51:46 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2012-08-30 09:51:46 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2012-08-30 09:51:46 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2012-08-30 09:51:46 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2012-08-30 10:01:19 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2012-08-30 10:01:19 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}
2012-08-30 10:01:20 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
2012-08-30 10:01:20 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH/classes/kohana/request.php [ 1126 ]
--
#0 /home/codesafe/public_html/siteadmin/index.php(132): Kohana_Request->execute()
#1 {main}