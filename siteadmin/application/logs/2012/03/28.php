<?php defined('SYSPATH') or die('No direct script access.'); ?>

2012-03-28 01:03:11 --- ERROR: Database_Exception [ 2 ]: mysql_connect() [function.mysql-connect]: Access denied for user 'ODBC'@'localhost' (using password: NO) ~ MODPATH\database\classes\kohana\database\mysql.php [ 67 ]
2012-03-28 01:03:11 --- STRACE: Database_Exception [ 2 ]: mysql_connect() [function.mysql-connect]: Access denied for user 'ODBC'@'localhost' (using password: NO) ~ MODPATH\database\classes\kohana\database\mysql.php [ 67 ]
--
#0 D:\Server\xampp\htdocs\codesafe\modules\database\classes\kohana\database\mysql.php(432): Kohana_Database_MySQL->connect()
#1 D:\Server\xampp\htdocs\codesafe\modules\database\classes\kohana\database.php(473): Kohana_Database_MySQL->escape('admin')
#2 D:\Server\xampp\htdocs\codesafe\modules\database\classes\kohana\database\query\builder.php(116): Kohana_Database->quote('admin')
#3 D:\Server\xampp\htdocs\codesafe\modules\database\classes\kohana\database\query\builder\select.php(370): Kohana_Database_Query_Builder->_compile_conditions(Object(Database_MySQL), Array)
#4 D:\Server\xampp\htdocs\codesafe\modules\database\classes\kohana\database\query.php(228): Kohana_Database_Query_Builder_Select->compile(Object(Database_MySQL))
#5 D:\Server\xampp\htdocs\codesafe\siteadmin\application\classes\model\admin.php(16): Kohana_Database_Query->execute()
#6 D:\Server\xampp\htdocs\codesafe\siteadmin\application\classes\controller\welcome.php(73): Model_Admin->loadAdmin(Array)
#7 [internal function]: Controller_Welcome->action_index()
#8 D:\Server\xampp\htdocs\codesafe\system\classes\kohana\request\client\internal.php(118): ReflectionMethod->invoke(Object(Controller_Welcome))
#9 D:\Server\xampp\htdocs\codesafe\system\classes\kohana\request\client.php(64): Kohana_Request_Client_Internal->execute_request(Object(Request))
#10 D:\Server\xampp\htdocs\codesafe\system\classes\kohana\request.php(1138): Kohana_Request_Client->execute(Object(Request))
#11 D:\Server\xampp\htdocs\codesafe\siteadmin\index.php(114): Kohana_Request->execute()
#12 {main}
2012-03-28 01:26:19 --- ERROR: ErrorException [ 8 ]: Undefined variable: succmsg ~ APPPATH\views\password\index.php [ 12 ]
2012-03-28 01:26:19 --- STRACE: ErrorException [ 8 ]: Undefined variable: succmsg ~ APPPATH\views\password\index.php [ 12 ]
--
#0 D:\Server\xampp\htdocs\codesafe\siteadmin\application\views\password\index.php(12): Kohana_Core::error_handler(8, 'Undefined varia...', 'D:\Server\xampp...', 12, Array)
#1 D:\Server\xampp\htdocs\codesafe\system\classes\kohana\view.php(61): include('D:\Server\xampp...')
#2 D:\Server\xampp\htdocs\codesafe\system\classes\kohana\view.php(343): Kohana_View::capture('D:\Server\xampp...', Array)
#3 D:\Server\xampp\htdocs\codesafe\system\classes\kohana\view.php(228): Kohana_View->render()
#4 D:\Server\xampp\htdocs\codesafe\siteadmin\application\views\template\template.php(9): Kohana_View->__toString()
#5 D:\Server\xampp\htdocs\codesafe\system\classes\kohana\view.php(61): include('D:\Server\xampp...')
#6 D:\Server\xampp\htdocs\codesafe\system\classes\kohana\view.php(343): Kohana_View::capture('D:\Server\xampp...', Array)
#7 D:\Server\xampp\htdocs\codesafe\system\classes\kohana\controller\template.php(44): Kohana_View->render()
#8 [internal function]: Kohana_Controller_Template->after()
#9 D:\Server\xampp\htdocs\codesafe\system\classes\kohana\request\client\internal.php(121): ReflectionMethod->invoke(Object(Controller_Password))
#10 D:\Server\xampp\htdocs\codesafe\system\classes\kohana\request\client.php(64): Kohana_Request_Client_Internal->execute_request(Object(Request))
#11 D:\Server\xampp\htdocs\codesafe\system\classes\kohana\request.php(1138): Kohana_Request_Client->execute(Object(Request))
#12 D:\Server\xampp\htdocs\codesafe\siteadmin\index.php(114): Kohana_Request->execute()
#13 {main}
2012-03-28 01:27:29 --- ERROR: ErrorException [ 1 ]: Cannot pass parameter 2 by reference ~ APPPATH\classes\controller\password.php [ 50 ]
2012-03-28 01:27:29 --- STRACE: ErrorException [ 1 ]: Cannot pass parameter 2 by reference ~ APPPATH\classes\controller\password.php [ 50 ]
--
#0 [internal function]: Kohana_Core::shutdown_handler()
#1 {main}
2012-03-28 01:28:29 --- ERROR: ErrorException [ 8 ]: Undefined variable: post ~ APPPATH\views\password\index.php [ 12 ]
2012-03-28 01:28:29 --- STRACE: ErrorException [ 8 ]: Undefined variable: post ~ APPPATH\views\password\index.php [ 12 ]
--
#0 D:\Server\xampp\htdocs\codesafe\siteadmin\application\views\password\index.php(12): Kohana_Core::error_handler(8, 'Undefined varia...', 'D:\Server\xampp...', 12, Array)
#1 D:\Server\xampp\htdocs\codesafe\system\classes\kohana\view.php(61): include('D:\Server\xampp...')
#2 D:\Server\xampp\htdocs\codesafe\system\classes\kohana\view.php(343): Kohana_View::capture('D:\Server\xampp...', Array)
#3 D:\Server\xampp\htdocs\codesafe\system\classes\kohana\view.php(228): Kohana_View->render()
#4 D:\Server\xampp\htdocs\codesafe\siteadmin\application\views\template\template.php(9): Kohana_View->__toString()
#5 D:\Server\xampp\htdocs\codesafe\system\classes\kohana\view.php(61): include('D:\Server\xampp...')
#6 D:\Server\xampp\htdocs\codesafe\system\classes\kohana\view.php(343): Kohana_View::capture('D:\Server\xampp...', Array)
#7 D:\Server\xampp\htdocs\codesafe\system\classes\kohana\controller\template.php(44): Kohana_View->render()
#8 [internal function]: Kohana_Controller_Template->after()
#9 D:\Server\xampp\htdocs\codesafe\system\classes\kohana\request\client\internal.php(121): ReflectionMethod->invoke(Object(Controller_Password))
#10 D:\Server\xampp\htdocs\codesafe\system\classes\kohana\request\client.php(64): Kohana_Request_Client_Internal->execute_request(Object(Request))
#11 D:\Server\xampp\htdocs\codesafe\system\classes\kohana\request.php(1138): Kohana_Request_Client->execute(Object(Request))
#12 D:\Server\xampp\htdocs\codesafe\siteadmin\index.php(114): Kohana_Request->execute()
#13 {main}
2012-03-28 01:29:12 --- ERROR: ErrorException [ 8 ]: Undefined variable: post1 ~ APPPATH\views\password\index.php [ 14 ]
2012-03-28 01:29:12 --- STRACE: ErrorException [ 8 ]: Undefined variable: post1 ~ APPPATH\views\password\index.php [ 14 ]
--
#0 D:\Server\xampp\htdocs\codesafe\siteadmin\application\views\password\index.php(14): Kohana_Core::error_handler(8, 'Undefined varia...', 'D:\Server\xampp...', 14, Array)
#1 D:\Server\xampp\htdocs\codesafe\system\classes\kohana\view.php(61): include('D:\Server\xampp...')
#2 D:\Server\xampp\htdocs\codesafe\system\classes\kohana\view.php(343): Kohana_View::capture('D:\Server\xampp...', Array)
#3 D:\Server\xampp\htdocs\codesafe\system\classes\kohana\view.php(228): Kohana_View->render()
#4 D:\Server\xampp\htdocs\codesafe\siteadmin\application\views\template\template.php(9): Kohana_View->__toString()
#5 D:\Server\xampp\htdocs\codesafe\system\classes\kohana\view.php(61): include('D:\Server\xampp...')
#6 D:\Server\xampp\htdocs\codesafe\system\classes\kohana\view.php(343): Kohana_View::capture('D:\Server\xampp...', Array)
#7 D:\Server\xampp\htdocs\codesafe\system\classes\kohana\controller\template.php(44): Kohana_View->render()
#8 [internal function]: Kohana_Controller_Template->after()
#9 D:\Server\xampp\htdocs\codesafe\system\classes\kohana\request\client\internal.php(121): ReflectionMethod->invoke(Object(Controller_Password))
#10 D:\Server\xampp\htdocs\codesafe\system\classes\kohana\request\client.php(64): Kohana_Request_Client_Internal->execute_request(Object(Request))
#11 D:\Server\xampp\htdocs\codesafe\system\classes\kohana\request.php(1138): Kohana_Request_Client->execute(Object(Request))
#12 D:\Server\xampp\htdocs\codesafe\siteadmin\index.php(114): Kohana_Request->execute()
#13 {main}
2012-03-28 02:29:39 --- ERROR: ErrorException [ 8 ]: Undefined variable: post ~ APPPATH\classes\model\admin.php [ 23 ]
2012-03-28 02:29:39 --- STRACE: ErrorException [ 8 ]: Undefined variable: post ~ APPPATH\classes\model\admin.php [ 23 ]
--
#0 D:\Server\xampp\htdocs\codesafe\siteadmin\application\classes\model\admin.php(23): Kohana_Core::error_handler(8, 'Undefined varia...', 'D:\Server\xampp...', 23, Array)
#1 D:\Server\xampp\htdocs\codesafe\siteadmin\application\classes\controller\password.php(55): Model_Admin->getpass('1')
#2 [internal function]: Controller_Password->action_index()
#3 D:\Server\xampp\htdocs\codesafe\system\classes\kohana\request\client\internal.php(118): ReflectionMethod->invoke(Object(Controller_Password))
#4 D:\Server\xampp\htdocs\codesafe\system\classes\kohana\request\client.php(64): Kohana_Request_Client_Internal->execute_request(Object(Request))
#5 D:\Server\xampp\htdocs\codesafe\system\classes\kohana\request.php(1138): Kohana_Request_Client->execute(Object(Request))
#6 D:\Server\xampp\htdocs\codesafe\siteadmin\index.php(114): Kohana_Request->execute()
#7 {main}
2012-03-28 03:02:53 --- ERROR: ErrorException [ 4096 ]: Argument 1 passed to Kohana_Database_Query_Builder_Update::set() must be an array, string given, called in D:\Server\xampp\htdocs\codesafe\siteadmin\application\classes\model\admin.php on line 28 and defined ~ MODPATH\database\classes\kohana\database\query\builder\update.php [ 56 ]
2012-03-28 03:02:53 --- STRACE: ErrorException [ 4096 ]: Argument 1 passed to Kohana_Database_Query_Builder_Update::set() must be an array, string given, called in D:\Server\xampp\htdocs\codesafe\siteadmin\application\classes\model\admin.php on line 28 and defined ~ MODPATH\database\classes\kohana\database\query\builder\update.php [ 56 ]
--
#0 D:\Server\xampp\htdocs\codesafe\modules\database\classes\kohana\database\query\builder\update.php(56): Kohana_Core::error_handler(4096, 'Argument 1 pass...', 'D:\Server\xampp...', 56, Array)
#1 D:\Server\xampp\htdocs\codesafe\siteadmin\application\classes\model\admin.php(28): Kohana_Database_Query_Builder_Update->set('147538da338b770...')
#2 D:\Server\xampp\htdocs\codesafe\siteadmin\application\classes\controller\password.php(71): Model_Admin->editpass('1', '147538da338b770...')
#3 [internal function]: Controller_Password->action_index()
#4 D:\Server\xampp\htdocs\codesafe\system\classes\kohana\request\client\internal.php(118): ReflectionMethod->invoke(Object(Controller_Password))
#5 D:\Server\xampp\htdocs\codesafe\system\classes\kohana\request\client.php(64): Kohana_Request_Client_Internal->execute_request(Object(Request))
#6 D:\Server\xampp\htdocs\codesafe\system\classes\kohana\request.php(1138): Kohana_Request_Client->execute(Object(Request))
#7 D:\Server\xampp\htdocs\codesafe\siteadmin\index.php(114): Kohana_Request->execute()
#8 {main}
2012-03-28 03:03:30 --- ERROR: Database_Exception [ 1054 ]: Unknown column '0' in 'field list' [ UPDATE `admin` SET `0` = '147538da338b770b61e592afc92b1ee6' WHERE `id` = '1' ] ~ MODPATH\database\classes\kohana\database\mysql.php [ 194 ]
2012-03-28 03:03:30 --- STRACE: Database_Exception [ 1054 ]: Unknown column '0' in 'field list' [ UPDATE `admin` SET `0` = '147538da338b770b61e592afc92b1ee6' WHERE `id` = '1' ] ~ MODPATH\database\classes\kohana\database\mysql.php [ 194 ]
--
#0 D:\Server\xampp\htdocs\codesafe\modules\database\classes\kohana\database\query.php(245): Kohana_Database_MySQL->query(3, 'UPDATE `admin` ...', false, Array)
#1 D:\Server\xampp\htdocs\codesafe\siteadmin\application\classes\model\admin.php(28): Kohana_Database_Query->execute()
#2 D:\Server\xampp\htdocs\codesafe\siteadmin\application\classes\controller\password.php(71): Model_Admin->editpass('1', Array)
#3 [internal function]: Controller_Password->action_index()
#4 D:\Server\xampp\htdocs\codesafe\system\classes\kohana\request\client\internal.php(118): ReflectionMethod->invoke(Object(Controller_Password))
#5 D:\Server\xampp\htdocs\codesafe\system\classes\kohana\request\client.php(64): Kohana_Request_Client_Internal->execute_request(Object(Request))
#6 D:\Server\xampp\htdocs\codesafe\system\classes\kohana\request.php(1138): Kohana_Request_Client->execute(Object(Request))
#7 D:\Server\xampp\htdocs\codesafe\siteadmin\index.php(114): Kohana_Request->execute()
#8 {main}
2012-03-28 03:04:13 --- ERROR: Database_Exception [ 1054 ]: Unknown column '0' in 'field list' [ UPDATE `admin` SET `0` = 'password', `1` = '147538da338b770b61e592afc92b1ee6' WHERE `id` = '1' ] ~ MODPATH\database\classes\kohana\database\mysql.php [ 194 ]
2012-03-28 03:04:13 --- STRACE: Database_Exception [ 1054 ]: Unknown column '0' in 'field list' [ UPDATE `admin` SET `0` = 'password', `1` = '147538da338b770b61e592afc92b1ee6' WHERE `id` = '1' ] ~ MODPATH\database\classes\kohana\database\mysql.php [ 194 ]
--
#0 D:\Server\xampp\htdocs\codesafe\modules\database\classes\kohana\database\query.php(245): Kohana_Database_MySQL->query(3, 'UPDATE `admin` ...', false, Array)
#1 D:\Server\xampp\htdocs\codesafe\siteadmin\application\classes\model\admin.php(28): Kohana_Database_Query->execute()
#2 D:\Server\xampp\htdocs\codesafe\siteadmin\application\classes\controller\password.php(71): Model_Admin->editpass('1', Array)
#3 [internal function]: Controller_Password->action_index()
#4 D:\Server\xampp\htdocs\codesafe\system\classes\kohana\request\client\internal.php(118): ReflectionMethod->invoke(Object(Controller_Password))
#5 D:\Server\xampp\htdocs\codesafe\system\classes\kohana\request\client.php(64): Kohana_Request_Client_Internal->execute_request(Object(Request))
#6 D:\Server\xampp\htdocs\codesafe\system\classes\kohana\request.php(1138): Kohana_Request_Client->execute(Object(Request))
#7 D:\Server\xampp\htdocs\codesafe\siteadmin\index.php(114): Kohana_Request->execute()
#8 {main}
2012-03-28 03:05:25 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: welcome/images/error-icon.png ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2012-03-28 03:05:25 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: welcome/images/error-icon.png ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 D:\Server\xampp\htdocs\codesafe\siteadmin\index.php(114): Kohana_Request->execute()
#1 {main}
2012-03-28 03:05:36 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: welcome/images/error-icon.png ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2012-03-28 03:05:36 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: welcome/images/error-icon.png ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 D:\Server\xampp\htdocs\codesafe\siteadmin\index.php(114): Kohana_Request->execute()
#1 {main}
2012-03-28 03:33:45 --- ERROR: ErrorException [ 8 ]: Undefined variable: post ~ APPPATH\views\password\index.php [ 10 ]
2012-03-28 03:33:45 --- STRACE: ErrorException [ 8 ]: Undefined variable: post ~ APPPATH\views\password\index.php [ 10 ]
--
#0 D:\Server\xampp\htdocs\codesafe\siteadmin\application\views\password\index.php(10): Kohana_Core::error_handler(8, 'Undefined varia...', 'D:\Server\xampp...', 10, Array)
#1 D:\Server\xampp\htdocs\codesafe\system\classes\kohana\view.php(61): include('D:\Server\xampp...')
#2 D:\Server\xampp\htdocs\codesafe\system\classes\kohana\view.php(343): Kohana_View::capture('D:\Server\xampp...', Array)
#3 D:\Server\xampp\htdocs\codesafe\system\classes\kohana\view.php(228): Kohana_View->render()
#4 D:\Server\xampp\htdocs\codesafe\siteadmin\application\views\template\template.php(9): Kohana_View->__toString()
#5 D:\Server\xampp\htdocs\codesafe\system\classes\kohana\view.php(61): include('D:\Server\xampp...')
#6 D:\Server\xampp\htdocs\codesafe\system\classes\kohana\view.php(343): Kohana_View::capture('D:\Server\xampp...', Array)
#7 D:\Server\xampp\htdocs\codesafe\system\classes\kohana\controller\template.php(44): Kohana_View->render()
#8 [internal function]: Kohana_Controller_Template->after()
#9 D:\Server\xampp\htdocs\codesafe\system\classes\kohana\request\client\internal.php(121): ReflectionMethod->invoke(Object(Controller_Password))
#10 D:\Server\xampp\htdocs\codesafe\system\classes\kohana\request\client.php(64): Kohana_Request_Client_Internal->execute_request(Object(Request))
#11 D:\Server\xampp\htdocs\codesafe\system\classes\kohana\request.php(1138): Kohana_Request_Client->execute(Object(Request))
#12 D:\Server\xampp\htdocs\codesafe\siteadmin\index.php(114): Kohana_Request->execute()
#13 {main}
2012-03-28 03:38:24 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2012-03-28 03:38:24 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 D:\Server\xampp\htdocs\codesafe\siteadmin\index.php(114): Kohana_Request->execute()
#1 {main}
2012-03-28 03:38:24 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2012-03-28 03:38:24 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 D:\Server\xampp\htdocs\codesafe\siteadmin\index.php(114): Kohana_Request->execute()
#1 {main}
2012-03-28 03:38:28 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2012-03-28 03:38:28 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 D:\Server\xampp\htdocs\codesafe\siteadmin\index.php(114): Kohana_Request->execute()
#1 {main}
2012-03-28 03:38:28 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2012-03-28 03:38:28 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 D:\Server\xampp\htdocs\codesafe\siteadmin\index.php(114): Kohana_Request->execute()
#1 {main}
2012-03-28 03:38:28 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2012-03-28 03:38:28 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 D:\Server\xampp\htdocs\codesafe\siteadmin\index.php(114): Kohana_Request->execute()
#1 {main}
2012-03-28 03:44:07 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2012-03-28 03:44:07 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 D:\Server\xampp\htdocs\codesafe\siteadmin\index.php(114): Kohana_Request->execute()
#1 {main}
2012-03-28 03:45:30 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2012-03-28 03:45:30 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 D:\Server\xampp\htdocs\codesafe\siteadmin\index.php(114): Kohana_Request->execute()
#1 {main}
2012-03-28 03:45:30 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2012-03-28 03:45:30 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 D:\Server\xampp\htdocs\codesafe\siteadmin\index.php(114): Kohana_Request->execute()
#1 {main}
2012-03-28 03:45:30 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2012-03-28 03:45:30 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 D:\Server\xampp\htdocs\codesafe\siteadmin\index.php(114): Kohana_Request->execute()
#1 {main}