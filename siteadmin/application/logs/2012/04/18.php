<?php defined('SYSPATH') or die('No direct script access.'); ?>

2012-04-18 03:40:06 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: order/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2012-04-18 03:40:06 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: order/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 D:\Server\xampp\htdocs\codesafe\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2012-04-18 03:40:06 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: order/images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2012-04-18 03:40:06 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: order/images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 D:\Server\xampp\htdocs\codesafe\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2012-04-18 03:40:17 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: order/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2012-04-18 03:40:17 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: order/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 D:\Server\xampp\htdocs\codesafe\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2012-04-18 03:40:17 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: order/images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2012-04-18 03:40:17 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: order/images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 D:\Server\xampp\htdocs\codesafe\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2012-04-18 03:40:24 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: order/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2012-04-18 03:40:24 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: order/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 D:\Server\xampp\htdocs\codesafe\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2012-04-18 03:40:24 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: order/images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2012-04-18 03:40:24 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: order/images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 D:\Server\xampp\htdocs\codesafe\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2012-04-18 05:01:44 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: application/classes/controller/order.php ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2012-04-18 05:01:44 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: application/classes/controller/order.php ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 D:\Server\xampp\htdocs\codesafe\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2012-04-18 05:07:36 --- ERROR: Database_Exception [ 1054 ]: Unknown column 'profiles.delete' in 'where clause' [ SELECT * FROM `profiles` WHERE `profiles`.`delete` = 0 ] ~ MODPATH\database\classes\kohana\database\mysql.php [ 194 ]
2012-04-18 05:07:36 --- STRACE: Database_Exception [ 1054 ]: Unknown column 'profiles.delete' in 'where clause' [ SELECT * FROM `profiles` WHERE `profiles`.`delete` = 0 ] ~ MODPATH\database\classes\kohana\database\mysql.php [ 194 ]
--
#0 D:\Server\xampp\htdocs\codesafe\modules\database\classes\kohana\database\query.php(245): Kohana_Database_MySQL->query(1, 'SELECT * FROM `...', false, Array)
#1 D:\Server\xampp\htdocs\codesafe\siteadmin\application\classes\model\profiles.php(15): Kohana_Database_Query->execute()
#2 D:\Server\xampp\htdocs\codesafe\siteadmin\application\classes\controller\product.php(51): Model_Profiles->getAllProfiles()
#3 [internal function]: Controller_Product->action_index()
#4 D:\Server\xampp\htdocs\codesafe\system\classes\kohana\request\client\internal.php(118): ReflectionMethod->invoke(Object(Controller_Product))
#5 D:\Server\xampp\htdocs\codesafe\system\classes\kohana\request\client.php(64): Kohana_Request_Client_Internal->execute_request(Object(Request))
#6 D:\Server\xampp\htdocs\codesafe\system\classes\kohana\request.php(1138): Kohana_Request_Client->execute(Object(Request))
#7 D:\Server\xampp\htdocs\codesafe\siteadmin\index.php(132): Kohana_Request->execute()
#8 {main}
2012-04-18 05:08:03 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2012-04-18 05:08:03 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 D:\Server\xampp\htdocs\codesafe\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2012-04-18 05:08:03 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2012-04-18 05:08:03 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 D:\Server\xampp\htdocs\codesafe\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2012-04-18 05:08:06 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2012-04-18 05:08:06 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 D:\Server\xampp\htdocs\codesafe\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2012-04-18 05:08:06 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2012-04-18 05:08:06 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 D:\Server\xampp\htdocs\codesafe\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2012-04-18 05:08:09 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: order/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2012-04-18 05:08:09 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: order/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 D:\Server\xampp\htdocs\codesafe\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2012-04-18 05:08:09 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: order/images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2012-04-18 05:08:09 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: order/images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 D:\Server\xampp\htdocs\codesafe\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2012-04-18 05:08:11 --- ERROR: ErrorException [ 8 ]: Undefined index: name ~ APPPATH\views\order\view.php [ 74 ]
2012-04-18 05:08:11 --- STRACE: ErrorException [ 8 ]: Undefined index: name ~ APPPATH\views\order\view.php [ 74 ]
--
#0 D:\Server\xampp\htdocs\codesafe\siteadmin\application\views\order\view.php(74): Kohana_Core::error_handler(8, 'Undefined index...', 'D:\Server\xampp...', 74, Array)
#1 D:\Server\xampp\htdocs\codesafe\system\classes\kohana\view.php(61): include('D:\Server\xampp...')
#2 D:\Server\xampp\htdocs\codesafe\system\classes\kohana\view.php(343): Kohana_View::capture('D:\Server\xampp...', Array)
#3 D:\Server\xampp\htdocs\codesafe\system\classes\kohana\view.php(228): Kohana_View->render()
#4 D:\Server\xampp\htdocs\codesafe\siteadmin\application\views\template\template.php(9): Kohana_View->__toString()
#5 D:\Server\xampp\htdocs\codesafe\system\classes\kohana\view.php(61): include('D:\Server\xampp...')
#6 D:\Server\xampp\htdocs\codesafe\system\classes\kohana\view.php(343): Kohana_View::capture('D:\Server\xampp...', Array)
#7 D:\Server\xampp\htdocs\codesafe\system\classes\kohana\controller\template.php(44): Kohana_View->render()
#8 [internal function]: Kohana_Controller_Template->after()
#9 D:\Server\xampp\htdocs\codesafe\system\classes\kohana\request\client\internal.php(121): ReflectionMethod->invoke(Object(Controller_Order))
#10 D:\Server\xampp\htdocs\codesafe\system\classes\kohana\request\client.php(64): Kohana_Request_Client_Internal->execute_request(Object(Request))
#11 D:\Server\xampp\htdocs\codesafe\system\classes\kohana\request.php(1138): Kohana_Request_Client->execute(Object(Request))
#12 D:\Server\xampp\htdocs\codesafe\siteadmin\index.php(132): Kohana_Request->execute()
#13 {main}
2012-04-18 05:08:14 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: order/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2012-04-18 05:08:14 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: order/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 D:\Server\xampp\htdocs\codesafe\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2012-04-18 05:08:14 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: order/images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2012-04-18 05:08:14 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: order/images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 D:\Server\xampp\htdocs\codesafe\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2012-04-18 05:08:16 --- ERROR: ErrorException [ 8 ]: Undefined index: name ~ APPPATH\views\order\view.php [ 74 ]
2012-04-18 05:08:16 --- STRACE: ErrorException [ 8 ]: Undefined index: name ~ APPPATH\views\order\view.php [ 74 ]
--
#0 D:\Server\xampp\htdocs\codesafe\siteadmin\application\views\order\view.php(74): Kohana_Core::error_handler(8, 'Undefined index...', 'D:\Server\xampp...', 74, Array)
#1 D:\Server\xampp\htdocs\codesafe\system\classes\kohana\view.php(61): include('D:\Server\xampp...')
#2 D:\Server\xampp\htdocs\codesafe\system\classes\kohana\view.php(343): Kohana_View::capture('D:\Server\xampp...', Array)
#3 D:\Server\xampp\htdocs\codesafe\system\classes\kohana\view.php(228): Kohana_View->render()
#4 D:\Server\xampp\htdocs\codesafe\siteadmin\application\views\template\template.php(9): Kohana_View->__toString()
#5 D:\Server\xampp\htdocs\codesafe\system\classes\kohana\view.php(61): include('D:\Server\xampp...')
#6 D:\Server\xampp\htdocs\codesafe\system\classes\kohana\view.php(343): Kohana_View::capture('D:\Server\xampp...', Array)
#7 D:\Server\xampp\htdocs\codesafe\system\classes\kohana\controller\template.php(44): Kohana_View->render()
#8 [internal function]: Kohana_Controller_Template->after()
#9 D:\Server\xampp\htdocs\codesafe\system\classes\kohana\request\client\internal.php(121): ReflectionMethod->invoke(Object(Controller_Order))
#10 D:\Server\xampp\htdocs\codesafe\system\classes\kohana\request\client.php(64): Kohana_Request_Client_Internal->execute_request(Object(Request))
#11 D:\Server\xampp\htdocs\codesafe\system\classes\kohana\request.php(1138): Kohana_Request_Client->execute(Object(Request))
#12 D:\Server\xampp\htdocs\codesafe\siteadmin\index.php(132): Kohana_Request->execute()
#13 {main}
2012-04-18 05:08:18 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: order/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2012-04-18 05:08:18 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: order/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 D:\Server\xampp\htdocs\codesafe\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2012-04-18 05:08:18 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: order/images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2012-04-18 05:08:18 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: order/images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 D:\Server\xampp\htdocs\codesafe\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2012-04-18 05:08:58 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: order/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2012-04-18 05:08:58 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: order/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 D:\Server\xampp\htdocs\codesafe\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2012-04-18 05:08:58 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: order/images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2012-04-18 05:08:58 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: order/images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 D:\Server\xampp\htdocs\codesafe\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2012-04-18 05:09:03 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2012-04-18 05:09:03 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 D:\Server\xampp\htdocs\codesafe\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2012-04-18 05:09:03 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2012-04-18 05:09:03 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 D:\Server\xampp\htdocs\codesafe\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2012-04-18 05:09:06 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: order/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2012-04-18 05:09:06 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: order/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 D:\Server\xampp\htdocs\codesafe\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2012-04-18 05:09:06 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: order/images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2012-04-18 05:09:06 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: order/images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 D:\Server\xampp\htdocs\codesafe\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2012-04-18 05:09:23 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2012-04-18 05:09:23 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 D:\Server\xampp\htdocs\codesafe\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2012-04-18 05:09:23 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2012-04-18 05:09:23 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 D:\Server\xampp\htdocs\codesafe\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2012-04-18 05:09:41 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: user/index/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2012-04-18 05:09:41 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: user/index/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 D:\Server\xampp\htdocs\codesafe\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2012-04-18 05:09:42 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: user/index/images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2012-04-18 05:09:42 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: user/index/images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 D:\Server\xampp\htdocs\codesafe\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}