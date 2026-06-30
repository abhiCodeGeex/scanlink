<?php defined('SYSPATH') or die('No direct script access.'); ?>

2013-03-16 00:11:18 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 00:11:18 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 00:11:18 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 00:11:18 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 00:11:37 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 00:11:37 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 00:11:37 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 00:11:37 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 00:11:37 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 00:11:37 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 00:11:40 --- ERROR: ErrorException [ 8 ]: Undefined index: checklist_option ~ APPPATH\views\client\edit.php [ 66 ]
2013-03-16 00:11:40 --- STRACE: ErrorException [ 8 ]: Undefined index: checklist_option ~ APPPATH\views\client\edit.php [ 66 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\application\views\client\edit.php(66): Kohana_Core::error_handler(8, 'Undefined index...', 'G:\xampp\htdocs...', 66, Array)
#1 G:\xampp\htdocs\scanlink\system\classes\kohana\view.php(61): include('G:\xampp\htdocs...')
#2 G:\xampp\htdocs\scanlink\system\classes\kohana\view.php(343): Kohana_View::capture('G:\xampp\htdocs...', Array)
#3 G:\xampp\htdocs\scanlink\system\classes\kohana\view.php(228): Kohana_View->render()
#4 G:\xampp\htdocs\scanlink\siteadmin\application\views\template\template.php(9): Kohana_View->__toString()
#5 G:\xampp\htdocs\scanlink\system\classes\kohana\view.php(61): include('G:\xampp\htdocs...')
#6 G:\xampp\htdocs\scanlink\system\classes\kohana\view.php(343): Kohana_View::capture('G:\xampp\htdocs...', Array)
#7 G:\xampp\htdocs\scanlink\system\classes\kohana\controller\template.php(44): Kohana_View->render()
#8 [internal function]: Kohana_Controller_Template->after()
#9 G:\xampp\htdocs\scanlink\system\classes\kohana\request\client\internal.php(121): ReflectionMethod->invoke(Object(Controller_Client))
#10 G:\xampp\htdocs\scanlink\system\classes\kohana\request\client.php(64): Kohana_Request_Client_Internal->execute_request(Object(Request))
#11 G:\xampp\htdocs\scanlink\system\classes\kohana\request.php(1138): Kohana_Request_Client->execute(Object(Request))
#12 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#13 {main}
2013-03-16 00:15:14 --- ERROR: ErrorException [ 8 ]: Undefined index: checklist_option ~ APPPATH\views\client\edit.php [ 66 ]
2013-03-16 00:15:14 --- STRACE: ErrorException [ 8 ]: Undefined index: checklist_option ~ APPPATH\views\client\edit.php [ 66 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\application\views\client\edit.php(66): Kohana_Core::error_handler(8, 'Undefined index...', 'G:\xampp\htdocs...', 66, Array)
#1 G:\xampp\htdocs\scanlink\system\classes\kohana\view.php(61): include('G:\xampp\htdocs...')
#2 G:\xampp\htdocs\scanlink\system\classes\kohana\view.php(343): Kohana_View::capture('G:\xampp\htdocs...', Array)
#3 G:\xampp\htdocs\scanlink\system\classes\kohana\view.php(228): Kohana_View->render()
#4 G:\xampp\htdocs\scanlink\siteadmin\application\views\template\template.php(9): Kohana_View->__toString()
#5 G:\xampp\htdocs\scanlink\system\classes\kohana\view.php(61): include('G:\xampp\htdocs...')
#6 G:\xampp\htdocs\scanlink\system\classes\kohana\view.php(343): Kohana_View::capture('G:\xampp\htdocs...', Array)
#7 G:\xampp\htdocs\scanlink\system\classes\kohana\controller\template.php(44): Kohana_View->render()
#8 [internal function]: Kohana_Controller_Template->after()
#9 G:\xampp\htdocs\scanlink\system\classes\kohana\request\client\internal.php(121): ReflectionMethod->invoke(Object(Controller_Client))
#10 G:\xampp\htdocs\scanlink\system\classes\kohana\request\client.php(64): Kohana_Request_Client_Internal->execute_request(Object(Request))
#11 G:\xampp\htdocs\scanlink\system\classes\kohana\request.php(1138): Kohana_Request_Client->execute(Object(Request))
#12 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#13 {main}
2013-03-16 00:18:16 --- ERROR: ErrorException [ 8 ]: Undefined index: checklist_option ~ APPPATH\views\client\edit.php [ 66 ]
2013-03-16 00:18:16 --- STRACE: ErrorException [ 8 ]: Undefined index: checklist_option ~ APPPATH\views\client\edit.php [ 66 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\application\views\client\edit.php(66): Kohana_Core::error_handler(8, 'Undefined index...', 'G:\xampp\htdocs...', 66, Array)
#1 G:\xampp\htdocs\scanlink\system\classes\kohana\view.php(61): include('G:\xampp\htdocs...')
#2 G:\xampp\htdocs\scanlink\system\classes\kohana\view.php(343): Kohana_View::capture('G:\xampp\htdocs...', Array)
#3 G:\xampp\htdocs\scanlink\system\classes\kohana\view.php(228): Kohana_View->render()
#4 G:\xampp\htdocs\scanlink\siteadmin\application\views\template\template.php(9): Kohana_View->__toString()
#5 G:\xampp\htdocs\scanlink\system\classes\kohana\view.php(61): include('G:\xampp\htdocs...')
#6 G:\xampp\htdocs\scanlink\system\classes\kohana\view.php(343): Kohana_View::capture('G:\xampp\htdocs...', Array)
#7 G:\xampp\htdocs\scanlink\system\classes\kohana\controller\template.php(44): Kohana_View->render()
#8 [internal function]: Kohana_Controller_Template->after()
#9 G:\xampp\htdocs\scanlink\system\classes\kohana\request\client\internal.php(121): ReflectionMethod->invoke(Object(Controller_Client))
#10 G:\xampp\htdocs\scanlink\system\classes\kohana\request\client.php(64): Kohana_Request_Client_Internal->execute_request(Object(Request))
#11 G:\xampp\htdocs\scanlink\system\classes\kohana\request.php(1138): Kohana_Request_Client->execute(Object(Request))
#12 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#13 {main}
2013-03-16 00:18:20 --- ERROR: ErrorException [ 8 ]: Undefined index: checklist_option ~ APPPATH\views\client\edit.php [ 66 ]
2013-03-16 00:18:20 --- STRACE: ErrorException [ 8 ]: Undefined index: checklist_option ~ APPPATH\views\client\edit.php [ 66 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\application\views\client\edit.php(66): Kohana_Core::error_handler(8, 'Undefined index...', 'G:\xampp\htdocs...', 66, Array)
#1 G:\xampp\htdocs\scanlink\system\classes\kohana\view.php(61): include('G:\xampp\htdocs...')
#2 G:\xampp\htdocs\scanlink\system\classes\kohana\view.php(343): Kohana_View::capture('G:\xampp\htdocs...', Array)
#3 G:\xampp\htdocs\scanlink\system\classes\kohana\view.php(228): Kohana_View->render()
#4 G:\xampp\htdocs\scanlink\siteadmin\application\views\template\template.php(9): Kohana_View->__toString()
#5 G:\xampp\htdocs\scanlink\system\classes\kohana\view.php(61): include('G:\xampp\htdocs...')
#6 G:\xampp\htdocs\scanlink\system\classes\kohana\view.php(343): Kohana_View::capture('G:\xampp\htdocs...', Array)
#7 G:\xampp\htdocs\scanlink\system\classes\kohana\controller\template.php(44): Kohana_View->render()
#8 [internal function]: Kohana_Controller_Template->after()
#9 G:\xampp\htdocs\scanlink\system\classes\kohana\request\client\internal.php(121): ReflectionMethod->invoke(Object(Controller_Client))
#10 G:\xampp\htdocs\scanlink\system\classes\kohana\request\client.php(64): Kohana_Request_Client_Internal->execute_request(Object(Request))
#11 G:\xampp\htdocs\scanlink\system\classes\kohana\request.php(1138): Kohana_Request_Client->execute(Object(Request))
#12 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#13 {main}
2013-03-16 00:18:36 --- ERROR: ErrorException [ 8 ]: Undefined index: checklist_option ~ APPPATH\views\client\edit.php [ 66 ]
2013-03-16 00:18:36 --- STRACE: ErrorException [ 8 ]: Undefined index: checklist_option ~ APPPATH\views\client\edit.php [ 66 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\application\views\client\edit.php(66): Kohana_Core::error_handler(8, 'Undefined index...', 'G:\xampp\htdocs...', 66, Array)
#1 G:\xampp\htdocs\scanlink\system\classes\kohana\view.php(61): include('G:\xampp\htdocs...')
#2 G:\xampp\htdocs\scanlink\system\classes\kohana\view.php(343): Kohana_View::capture('G:\xampp\htdocs...', Array)
#3 G:\xampp\htdocs\scanlink\system\classes\kohana\view.php(228): Kohana_View->render()
#4 G:\xampp\htdocs\scanlink\siteadmin\application\views\template\template.php(9): Kohana_View->__toString()
#5 G:\xampp\htdocs\scanlink\system\classes\kohana\view.php(61): include('G:\xampp\htdocs...')
#6 G:\xampp\htdocs\scanlink\system\classes\kohana\view.php(343): Kohana_View::capture('G:\xampp\htdocs...', Array)
#7 G:\xampp\htdocs\scanlink\system\classes\kohana\controller\template.php(44): Kohana_View->render()
#8 [internal function]: Kohana_Controller_Template->after()
#9 G:\xampp\htdocs\scanlink\system\classes\kohana\request\client\internal.php(121): ReflectionMethod->invoke(Object(Controller_Client))
#10 G:\xampp\htdocs\scanlink\system\classes\kohana\request\client.php(64): Kohana_Request_Client_Internal->execute_request(Object(Request))
#11 G:\xampp\htdocs\scanlink\system\classes\kohana\request.php(1138): Kohana_Request_Client->execute(Object(Request))
#12 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#13 {main}
2013-03-16 00:18:40 --- ERROR: ErrorException [ 8 ]: Undefined index: checklist_option ~ APPPATH\views\client\edit.php [ 66 ]
2013-03-16 00:18:40 --- STRACE: ErrorException [ 8 ]: Undefined index: checklist_option ~ APPPATH\views\client\edit.php [ 66 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\application\views\client\edit.php(66): Kohana_Core::error_handler(8, 'Undefined index...', 'G:\xampp\htdocs...', 66, Array)
#1 G:\xampp\htdocs\scanlink\system\classes\kohana\view.php(61): include('G:\xampp\htdocs...')
#2 G:\xampp\htdocs\scanlink\system\classes\kohana\view.php(343): Kohana_View::capture('G:\xampp\htdocs...', Array)
#3 G:\xampp\htdocs\scanlink\system\classes\kohana\view.php(228): Kohana_View->render()
#4 G:\xampp\htdocs\scanlink\siteadmin\application\views\template\template.php(9): Kohana_View->__toString()
#5 G:\xampp\htdocs\scanlink\system\classes\kohana\view.php(61): include('G:\xampp\htdocs...')
#6 G:\xampp\htdocs\scanlink\system\classes\kohana\view.php(343): Kohana_View::capture('G:\xampp\htdocs...', Array)
#7 G:\xampp\htdocs\scanlink\system\classes\kohana\controller\template.php(44): Kohana_View->render()
#8 [internal function]: Kohana_Controller_Template->after()
#9 G:\xampp\htdocs\scanlink\system\classes\kohana\request\client\internal.php(121): ReflectionMethod->invoke(Object(Controller_Client))
#10 G:\xampp\htdocs\scanlink\system\classes\kohana\request\client.php(64): Kohana_Request_Client_Internal->execute_request(Object(Request))
#11 G:\xampp\htdocs\scanlink\system\classes\kohana\request.php(1138): Kohana_Request_Client->execute(Object(Request))
#12 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#13 {main}
2013-03-16 00:18:59 --- ERROR: ErrorException [ 8 ]: Undefined index: checklist_option ~ APPPATH\views\client\edit.php [ 66 ]
2013-03-16 00:18:59 --- STRACE: ErrorException [ 8 ]: Undefined index: checklist_option ~ APPPATH\views\client\edit.php [ 66 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\application\views\client\edit.php(66): Kohana_Core::error_handler(8, 'Undefined index...', 'G:\xampp\htdocs...', 66, Array)
#1 G:\xampp\htdocs\scanlink\system\classes\kohana\view.php(61): include('G:\xampp\htdocs...')
#2 G:\xampp\htdocs\scanlink\system\classes\kohana\view.php(343): Kohana_View::capture('G:\xampp\htdocs...', Array)
#3 G:\xampp\htdocs\scanlink\system\classes\kohana\view.php(228): Kohana_View->render()
#4 G:\xampp\htdocs\scanlink\siteadmin\application\views\template\template.php(9): Kohana_View->__toString()
#5 G:\xampp\htdocs\scanlink\system\classes\kohana\view.php(61): include('G:\xampp\htdocs...')
#6 G:\xampp\htdocs\scanlink\system\classes\kohana\view.php(343): Kohana_View::capture('G:\xampp\htdocs...', Array)
#7 G:\xampp\htdocs\scanlink\system\classes\kohana\controller\template.php(44): Kohana_View->render()
#8 [internal function]: Kohana_Controller_Template->after()
#9 G:\xampp\htdocs\scanlink\system\classes\kohana\request\client\internal.php(121): ReflectionMethod->invoke(Object(Controller_Client))
#10 G:\xampp\htdocs\scanlink\system\classes\kohana\request\client.php(64): Kohana_Request_Client_Internal->execute_request(Object(Request))
#11 G:\xampp\htdocs\scanlink\system\classes\kohana\request.php(1138): Kohana_Request_Client->execute(Object(Request))
#12 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#13 {main}
2013-03-16 00:19:02 --- ERROR: ErrorException [ 8 ]: Undefined index: checklist_option ~ APPPATH\views\client\edit.php [ 66 ]
2013-03-16 00:19:02 --- STRACE: ErrorException [ 8 ]: Undefined index: checklist_option ~ APPPATH\views\client\edit.php [ 66 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\application\views\client\edit.php(66): Kohana_Core::error_handler(8, 'Undefined index...', 'G:\xampp\htdocs...', 66, Array)
#1 G:\xampp\htdocs\scanlink\system\classes\kohana\view.php(61): include('G:\xampp\htdocs...')
#2 G:\xampp\htdocs\scanlink\system\classes\kohana\view.php(343): Kohana_View::capture('G:\xampp\htdocs...', Array)
#3 G:\xampp\htdocs\scanlink\system\classes\kohana\view.php(228): Kohana_View->render()
#4 G:\xampp\htdocs\scanlink\siteadmin\application\views\template\template.php(9): Kohana_View->__toString()
#5 G:\xampp\htdocs\scanlink\system\classes\kohana\view.php(61): include('G:\xampp\htdocs...')
#6 G:\xampp\htdocs\scanlink\system\classes\kohana\view.php(343): Kohana_View::capture('G:\xampp\htdocs...', Array)
#7 G:\xampp\htdocs\scanlink\system\classes\kohana\controller\template.php(44): Kohana_View->render()
#8 [internal function]: Kohana_Controller_Template->after()
#9 G:\xampp\htdocs\scanlink\system\classes\kohana\request\client\internal.php(121): ReflectionMethod->invoke(Object(Controller_Client))
#10 G:\xampp\htdocs\scanlink\system\classes\kohana\request\client.php(64): Kohana_Request_Client_Internal->execute_request(Object(Request))
#11 G:\xampp\htdocs\scanlink\system\classes\kohana\request.php(1138): Kohana_Request_Client->execute(Object(Request))
#12 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#13 {main}
2013-03-16 00:19:07 --- ERROR: ErrorException [ 8 ]: Undefined index: checklist_option ~ APPPATH\views\client\edit.php [ 66 ]
2013-03-16 00:19:07 --- STRACE: ErrorException [ 8 ]: Undefined index: checklist_option ~ APPPATH\views\client\edit.php [ 66 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\application\views\client\edit.php(66): Kohana_Core::error_handler(8, 'Undefined index...', 'G:\xampp\htdocs...', 66, Array)
#1 G:\xampp\htdocs\scanlink\system\classes\kohana\view.php(61): include('G:\xampp\htdocs...')
#2 G:\xampp\htdocs\scanlink\system\classes\kohana\view.php(343): Kohana_View::capture('G:\xampp\htdocs...', Array)
#3 G:\xampp\htdocs\scanlink\system\classes\kohana\view.php(228): Kohana_View->render()
#4 G:\xampp\htdocs\scanlink\siteadmin\application\views\template\template.php(9): Kohana_View->__toString()
#5 G:\xampp\htdocs\scanlink\system\classes\kohana\view.php(61): include('G:\xampp\htdocs...')
#6 G:\xampp\htdocs\scanlink\system\classes\kohana\view.php(343): Kohana_View::capture('G:\xampp\htdocs...', Array)
#7 G:\xampp\htdocs\scanlink\system\classes\kohana\controller\template.php(44): Kohana_View->render()
#8 [internal function]: Kohana_Controller_Template->after()
#9 G:\xampp\htdocs\scanlink\system\classes\kohana\request\client\internal.php(121): ReflectionMethod->invoke(Object(Controller_Client))
#10 G:\xampp\htdocs\scanlink\system\classes\kohana\request\client.php(64): Kohana_Request_Client_Internal->execute_request(Object(Request))
#11 G:\xampp\htdocs\scanlink\system\classes\kohana\request.php(1138): Kohana_Request_Client->execute(Object(Request))
#12 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#13 {main}
2013-03-16 00:19:29 --- ERROR: ErrorException [ 8 ]: Undefined index: checklist_option ~ APPPATH\views\client\edit.php [ 66 ]
2013-03-16 00:19:29 --- STRACE: ErrorException [ 8 ]: Undefined index: checklist_option ~ APPPATH\views\client\edit.php [ 66 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\application\views\client\edit.php(66): Kohana_Core::error_handler(8, 'Undefined index...', 'G:\xampp\htdocs...', 66, Array)
#1 G:\xampp\htdocs\scanlink\system\classes\kohana\view.php(61): include('G:\xampp\htdocs...')
#2 G:\xampp\htdocs\scanlink\system\classes\kohana\view.php(343): Kohana_View::capture('G:\xampp\htdocs...', Array)
#3 G:\xampp\htdocs\scanlink\system\classes\kohana\view.php(228): Kohana_View->render()
#4 G:\xampp\htdocs\scanlink\siteadmin\application\views\template\template.php(9): Kohana_View->__toString()
#5 G:\xampp\htdocs\scanlink\system\classes\kohana\view.php(61): include('G:\xampp\htdocs...')
#6 G:\xampp\htdocs\scanlink\system\classes\kohana\view.php(343): Kohana_View::capture('G:\xampp\htdocs...', Array)
#7 G:\xampp\htdocs\scanlink\system\classes\kohana\controller\template.php(44): Kohana_View->render()
#8 [internal function]: Kohana_Controller_Template->after()
#9 G:\xampp\htdocs\scanlink\system\classes\kohana\request\client\internal.php(121): ReflectionMethod->invoke(Object(Controller_Client))
#10 G:\xampp\htdocs\scanlink\system\classes\kohana\request\client.php(64): Kohana_Request_Client_Internal->execute_request(Object(Request))
#11 G:\xampp\htdocs\scanlink\system\classes\kohana\request.php(1138): Kohana_Request_Client->execute(Object(Request))
#12 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#13 {main}
2013-03-16 00:19:36 --- ERROR: ErrorException [ 8 ]: Undefined index: checklist_option ~ APPPATH\views\client\edit.php [ 66 ]
2013-03-16 00:19:36 --- STRACE: ErrorException [ 8 ]: Undefined index: checklist_option ~ APPPATH\views\client\edit.php [ 66 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\application\views\client\edit.php(66): Kohana_Core::error_handler(8, 'Undefined index...', 'G:\xampp\htdocs...', 66, Array)
#1 G:\xampp\htdocs\scanlink\system\classes\kohana\view.php(61): include('G:\xampp\htdocs...')
#2 G:\xampp\htdocs\scanlink\system\classes\kohana\view.php(343): Kohana_View::capture('G:\xampp\htdocs...', Array)
#3 G:\xampp\htdocs\scanlink\system\classes\kohana\view.php(228): Kohana_View->render()
#4 G:\xampp\htdocs\scanlink\siteadmin\application\views\template\template.php(9): Kohana_View->__toString()
#5 G:\xampp\htdocs\scanlink\system\classes\kohana\view.php(61): include('G:\xampp\htdocs...')
#6 G:\xampp\htdocs\scanlink\system\classes\kohana\view.php(343): Kohana_View::capture('G:\xampp\htdocs...', Array)
#7 G:\xampp\htdocs\scanlink\system\classes\kohana\controller\template.php(44): Kohana_View->render()
#8 [internal function]: Kohana_Controller_Template->after()
#9 G:\xampp\htdocs\scanlink\system\classes\kohana\request\client\internal.php(121): ReflectionMethod->invoke(Object(Controller_Client))
#10 G:\xampp\htdocs\scanlink\system\classes\kohana\request\client.php(64): Kohana_Request_Client_Internal->execute_request(Object(Request))
#11 G:\xampp\htdocs\scanlink\system\classes\kohana\request.php(1138): Kohana_Request_Client->execute(Object(Request))
#12 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#13 {main}
2013-03-16 00:21:22 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 00:21:22 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 00:21:22 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 00:21:22 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 00:21:23 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 00:21:23 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 00:21:23 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 00:21:23 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 00:23:00 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 00:23:00 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 00:23:00 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 00:23:00 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 00:23:00 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 00:23:00 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 00:23:01 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 00:23:01 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 00:23:01 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 00:23:01 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 00:35:52 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: styles/images/ui-bg_flat_75_ffffff_40x100.png ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 00:35:52 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: styles/images/ui-bg_flat_75_ffffff_40x100.png ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 00:35:52 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: styles/images/ui-bg_glass_75_e6e6e6_1x400.png ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 00:35:52 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: styles/images/ui-bg_glass_75_e6e6e6_1x400.png ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 00:35:52 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: styles/images/ui-icons_222222_256x240.png ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 00:35:52 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: styles/images/ui-icons_222222_256x240.png ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 00:35:52 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: styles/images/ui-bg_highlight-soft_75_cccccc_1x100.png ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 00:35:52 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: styles/images/ui-bg_highlight-soft_75_cccccc_1x100.png ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 00:35:52 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: styles/images/ui-bg_glass_55_fbf9ee_1x400.png ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 00:35:52 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: styles/images/ui-bg_glass_55_fbf9ee_1x400.png ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 00:37:14 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: styles/images/ui-icons_222222_256x240.png ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 00:37:14 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: styles/images/ui-icons_222222_256x240.png ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 00:37:14 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: styles/images/ui-bg_glass_55_fbf9ee_1x400.png ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 00:37:14 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: styles/images/ui-bg_glass_55_fbf9ee_1x400.png ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 00:37:14 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: styles/images/ui-bg_highlight-soft_75_cccccc_1x100.png ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 00:37:14 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: styles/images/ui-bg_highlight-soft_75_cccccc_1x100.png ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 00:37:14 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: styles/images/ui-bg_glass_75_e6e6e6_1x400.png ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 00:37:14 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: styles/images/ui-bg_glass_75_e6e6e6_1x400.png ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 00:37:14 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: styles/images/ui-bg_flat_75_ffffff_40x100.png ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 00:37:14 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: styles/images/ui-bg_flat_75_ffffff_40x100.png ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 00:37:15 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: styles/images/ui-bg_glass_75_dadada_1x400.png ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 00:37:15 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: styles/images/ui-bg_glass_75_dadada_1x400.png ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 00:37:15 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: styles/images/ui-icons_454545_256x240.png ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 00:37:15 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: styles/images/ui-icons_454545_256x240.png ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 00:40:10 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 00:40:10 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 00:40:10 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 00:40:10 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 00:40:16 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 00:40:16 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 00:40:16 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 00:40:16 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 00:40:19 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 00:40:19 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 00:40:19 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 00:40:19 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 00:40:30 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 00:40:30 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 00:40:30 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 00:40:30 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 01:00:18 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 01:00:18 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 01:00:18 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 01:00:18 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 01:00:18 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 01:00:18 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 01:01:11 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 01:01:11 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 01:01:11 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 01:01:11 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 01:01:41 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 01:01:41 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 01:01:41 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 01:01:41 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 01:03:13 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 01:03:13 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 01:03:13 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 01:03:13 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 01:03:14 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 01:03:14 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 01:03:14 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 01:03:14 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 01:03:15 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 01:03:15 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 01:03:15 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 01:03:15 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 01:03:15 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 01:03:15 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 01:03:15 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 01:03:15 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 01:03:15 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 01:03:15 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 01:05:29 --- ERROR: ErrorException [ 4 ]: syntax error, unexpected T_OBJECT_OPERATOR ~ APPPATH\classes\controller\product.php [ 140 ]
2013-03-16 01:05:29 --- STRACE: ErrorException [ 4 ]: syntax error, unexpected T_OBJECT_OPERATOR ~ APPPATH\classes\controller\product.php [ 140 ]
--
#0 [internal function]: Kohana_Core::shutdown_handler()
#1 {main}
2013-03-16 01:05:41 --- ERROR: ErrorException [ 4 ]: syntax error, unexpected T_OBJECT_OPERATOR ~ APPPATH\classes\controller\product.php [ 140 ]
2013-03-16 01:05:41 --- STRACE: ErrorException [ 4 ]: syntax error, unexpected T_OBJECT_OPERATOR ~ APPPATH\classes\controller\product.php [ 140 ]
--
#0 [internal function]: Kohana_Core::shutdown_handler()
#1 {main}
2013-03-16 01:06:24 --- ERROR: ErrorException [ 4 ]: syntax error, unexpected T_OBJECT_OPERATOR ~ APPPATH\classes\controller\product.php [ 140 ]
2013-03-16 01:06:24 --- STRACE: ErrorException [ 4 ]: syntax error, unexpected T_OBJECT_OPERATOR ~ APPPATH\classes\controller\product.php [ 140 ]
--
#0 [internal function]: Kohana_Core::shutdown_handler()
#1 {main}
2013-03-16 01:06:28 --- ERROR: ErrorException [ 4 ]: syntax error, unexpected T_OBJECT_OPERATOR ~ APPPATH\classes\controller\product.php [ 140 ]
2013-03-16 01:06:28 --- STRACE: ErrorException [ 4 ]: syntax error, unexpected T_OBJECT_OPERATOR ~ APPPATH\classes\controller\product.php [ 140 ]
--
#0 [internal function]: Kohana_Core::shutdown_handler()
#1 {main}
2013-03-16 01:06:35 --- ERROR: ErrorException [ 4 ]: syntax error, unexpected T_OBJECT_OPERATOR ~ APPPATH\classes\controller\product.php [ 140 ]
2013-03-16 01:06:35 --- STRACE: ErrorException [ 4 ]: syntax error, unexpected T_OBJECT_OPERATOR ~ APPPATH\classes\controller\product.php [ 140 ]
--
#0 [internal function]: Kohana_Core::shutdown_handler()
#1 {main}
2013-03-16 01:06:36 --- ERROR: ErrorException [ 4 ]: syntax error, unexpected T_OBJECT_OPERATOR ~ APPPATH\classes\controller\product.php [ 140 ]
2013-03-16 01:06:36 --- STRACE: ErrorException [ 4 ]: syntax error, unexpected T_OBJECT_OPERATOR ~ APPPATH\classes\controller\product.php [ 140 ]
--
#0 [internal function]: Kohana_Core::shutdown_handler()
#1 {main}
2013-03-16 01:06:40 --- ERROR: ErrorException [ 4 ]: syntax error, unexpected T_OBJECT_OPERATOR ~ APPPATH\classes\controller\product.php [ 140 ]
2013-03-16 01:06:40 --- STRACE: ErrorException [ 4 ]: syntax error, unexpected T_OBJECT_OPERATOR ~ APPPATH\classes\controller\product.php [ 140 ]
--
#0 [internal function]: Kohana_Core::shutdown_handler()
#1 {main}
2013-03-16 01:06:53 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 01:06:53 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 01:06:53 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 01:06:53 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 01:06:54 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 01:06:54 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 01:06:54 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 01:06:54 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 01:07:10 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 01:07:10 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 01:07:10 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 01:07:10 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 01:07:10 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 01:07:10 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 01:07:11 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 01:07:11 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 01:07:11 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 01:07:11 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 01:07:29 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 01:07:29 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 01:07:29 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 01:07:29 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 01:07:41 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 01:07:41 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 01:07:41 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 01:07:41 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 01:07:41 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 01:07:41 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 01:07:46 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 01:07:46 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 01:07:47 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 01:07:47 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 01:07:47 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 01:07:47 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 01:08:05 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 01:08:05 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 01:08:06 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 01:08:06 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 01:08:09 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 01:08:09 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 01:08:10 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 01:08:10 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 01:08:10 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 01:08:10 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 01:08:50 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 01:08:50 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 01:08:50 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 01:08:50 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 01:09:10 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 01:09:10 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 01:09:10 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 01:09:10 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 01:09:35 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 01:09:35 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 01:09:35 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 01:09:35 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 01:10:01 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 01:10:01 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 01:10:02 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 01:10:02 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 01:10:11 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 01:10:11 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 01:10:11 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 01:10:11 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 01:10:28 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 01:10:28 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 01:10:29 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 01:10:29 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 01:10:33 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: user/index/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 01:10:33 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: user/index/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 01:10:36 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: user/index/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 01:10:36 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: user/index/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 01:10:36 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: user/index/images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 01:10:36 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: user/index/images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 01:10:43 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 01:10:43 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 01:10:43 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 01:10:43 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 01:10:52 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 01:10:52 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 01:10:52 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 01:10:52 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 01:11:47 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 01:11:47 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 01:11:47 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 01:11:47 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 01:11:47 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 01:11:47 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 01:15:59 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 01:15:59 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 01:15:59 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 01:15:59 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 01:17:06 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: images/scanlink-logo.png ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 01:17:06 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: images/scanlink-logo.png ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 01:20:17 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: images/scanlink-logo.png ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 01:20:17 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: images/scanlink-logo.png ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 01:22:22 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: images/scanlink-logo.png ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 01:22:22 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: images/scanlink-logo.png ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 01:25:29 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 01:25:29 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 01:25:29 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 01:25:29 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 01:25:29 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 01:25:29 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 01:26:02 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 01:26:02 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 01:26:03 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 01:26:03 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 01:27:21 --- ERROR: ErrorException [ 8 ]: Undefined index: video ~ APPPATH\classes\controller\product.php [ 77 ]
2013-03-16 01:27:21 --- STRACE: ErrorException [ 8 ]: Undefined index: video ~ APPPATH\classes\controller\product.php [ 77 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\application\classes\controller\product.php(77): Kohana_Core::error_handler(8, 'Undefined index...', 'G:\xampp\htdocs...', 77, Array)
#1 [internal function]: Controller_Product->action_index()
#2 G:\xampp\htdocs\scanlink\system\classes\kohana\request\client\internal.php(118): ReflectionMethod->invoke(Object(Controller_Product))
#3 G:\xampp\htdocs\scanlink\system\classes\kohana\request\client.php(64): Kohana_Request_Client_Internal->execute_request(Object(Request))
#4 G:\xampp\htdocs\scanlink\system\classes\kohana\request.php(1138): Kohana_Request_Client->execute(Object(Request))
#5 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#6 {main}
2013-03-16 01:29:57 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 01:29:57 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 01:29:58 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 01:29:58 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 01:31:15 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 01:31:15 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 01:31:15 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 01:31:15 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 01:32:13 --- ERROR: ErrorException [ 8 ]: Undefined index: video ~ APPPATH\classes\controller\plant.php [ 85 ]
2013-03-16 01:32:13 --- STRACE: ErrorException [ 8 ]: Undefined index: video ~ APPPATH\classes\controller\plant.php [ 85 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\application\classes\controller\plant.php(85): Kohana_Core::error_handler(8, 'Undefined index...', 'G:\xampp\htdocs...', 85, Array)
#1 [internal function]: Controller_Plant->action_index()
#2 G:\xampp\htdocs\scanlink\system\classes\kohana\request\client\internal.php(118): ReflectionMethod->invoke(Object(Controller_Plant))
#3 G:\xampp\htdocs\scanlink\system\classes\kohana\request\client.php(64): Kohana_Request_Client_Internal->execute_request(Object(Request))
#4 G:\xampp\htdocs\scanlink\system\classes\kohana\request.php(1138): Kohana_Request_Client->execute(Object(Request))
#5 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#6 {main}
2013-03-16 01:33:03 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 01:33:03 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 01:33:03 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 01:33:03 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 01:33:38 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 01:33:38 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 01:33:38 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 01:33:38 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 01:35:51 --- ERROR: ErrorException [ 8 ]: Undefined index: video ~ APPPATH\classes\controller\asset.php [ 83 ]
2013-03-16 01:35:51 --- STRACE: ErrorException [ 8 ]: Undefined index: video ~ APPPATH\classes\controller\asset.php [ 83 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\application\classes\controller\asset.php(83): Kohana_Core::error_handler(8, 'Undefined index...', 'G:\xampp\htdocs...', 83, Array)
#1 [internal function]: Controller_Asset->action_index()
#2 G:\xampp\htdocs\scanlink\system\classes\kohana\request\client\internal.php(118): ReflectionMethod->invoke(Object(Controller_Asset))
#3 G:\xampp\htdocs\scanlink\system\classes\kohana\request\client.php(64): Kohana_Request_Client_Internal->execute_request(Object(Request))
#4 G:\xampp\htdocs\scanlink\system\classes\kohana\request.php(1138): Kohana_Request_Client->execute(Object(Request))
#5 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#6 {main}
2013-03-16 01:36:27 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 01:36:27 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 01:36:27 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 01:36:27 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 01:37:22 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 01:37:22 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 01:37:22 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 01:37:22 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 01:38:55 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 01:38:55 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 01:38:55 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 01:38:55 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 01:40:50 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 01:40:50 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 01:40:50 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 01:40:50 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 01:42:12 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 01:42:12 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 01:42:12 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 01:42:12 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 01:42:36 --- ERROR: ErrorException [ 8 ]: Undefined index: video ~ APPPATH\classes\controller\people.php [ 109 ]
2013-03-16 01:42:36 --- STRACE: ErrorException [ 8 ]: Undefined index: video ~ APPPATH\classes\controller\people.php [ 109 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\application\classes\controller\people.php(109): Kohana_Core::error_handler(8, 'Undefined index...', 'G:\xampp\htdocs...', 109, Array)
#1 [internal function]: Controller_People->action_index()
#2 G:\xampp\htdocs\scanlink\system\classes\kohana\request\client\internal.php(118): ReflectionMethod->invoke(Object(Controller_People))
#3 G:\xampp\htdocs\scanlink\system\classes\kohana\request\client.php(64): Kohana_Request_Client_Internal->execute_request(Object(Request))
#4 G:\xampp\htdocs\scanlink\system\classes\kohana\request.php(1138): Kohana_Request_Client->execute(Object(Request))
#5 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#6 {main}
2013-03-16 01:43:04 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 01:43:04 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 01:43:04 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 01:43:04 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 01:43:35 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 01:43:35 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 01:43:35 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 01:43:35 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 01:43:39 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: user/index/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 01:43:39 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: user/index/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 01:43:40 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: user/index/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 01:43:40 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: user/index/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 01:43:40 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: user/index/images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 01:43:40 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: user/index/images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 01:43:45 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 01:43:45 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 01:43:45 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 01:43:45 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 01:43:52 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 01:43:52 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 01:43:52 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 01:43:52 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 02:08:50 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 02:08:50 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 02:08:50 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 02:08:50 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 02:08:50 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 02:08:50 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 02:09:02 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 02:09:02 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 02:09:02 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 02:09:02 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 02:09:09 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 02:09:09 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 02:09:09 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 02:09:09 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/edit/images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 02:16:24 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 02:16:24 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 02:16:24 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 02:16:24 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 02:16:24 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 02:16:24 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 02:35:48 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: images/scanlink-logo.png ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 02:35:48 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: images/scanlink-logo.png ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 02:51:03 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 02:51:03 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 02:51:04 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 02:51:04 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 02:51:04 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 02:51:04 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: client/images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 02:51:17 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: styles/images/ui-bg_flat_75_ffffff_40x100.png ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 02:51:17 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: styles/images/ui-bg_flat_75_ffffff_40x100.png ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 02:51:17 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: styles/images/ui-bg_highlight-soft_75_cccccc_1x100.png ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 02:51:17 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: styles/images/ui-bg_highlight-soft_75_cccccc_1x100.png ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 02:51:17 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: styles/images/ui-icons_222222_256x240.png ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 02:51:17 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: styles/images/ui-icons_222222_256x240.png ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 02:51:17 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: styles/images/ui-bg_glass_75_e6e6e6_1x400.png ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 02:51:17 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: styles/images/ui-bg_glass_75_e6e6e6_1x400.png ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 02:51:17 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: styles/images/ui-bg_glass_55_fbf9ee_1x400.png ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 02:51:17 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: styles/images/ui-bg_glass_55_fbf9ee_1x400.png ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 02:51:21 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: styles/images/ui-bg_glass_75_dadada_1x400.png ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 02:51:21 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: styles/images/ui-bg_glass_75_dadada_1x400.png ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 04:46:41 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 04:46:41 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/spacer.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 04:46:41 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 04:46:41 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: product/images/vdots.gif ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 04:46:48 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: images/scanlink-logo.png ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 04:46:48 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: images/scanlink-logo.png ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 04:49:02 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: images/scanlink-logo.png ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 04:49:02 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: images/scanlink-logo.png ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}
2013-03-16 04:49:02 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: images/scanlink-logo.png ~ SYSPATH\classes\kohana\request.php [ 1126 ]
2013-03-16 04:49:02 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: images/scanlink-logo.png ~ SYSPATH\classes\kohana\request.php [ 1126 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#1 {main}