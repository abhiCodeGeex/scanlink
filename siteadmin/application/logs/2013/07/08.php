<?php defined('SYSPATH') or die('No direct script access.'); ?>

2013-07-08 00:16:01 --- ERROR: ErrorException [ 8 ]: Undefined index: title ~ APPPATH\views\codeprising\index.php [ 35 ]
2013-07-08 00:16:01 --- STRACE: ErrorException [ 8 ]: Undefined index: title ~ APPPATH\views\codeprising\index.php [ 35 ]
--
#0 G:\xampp\htdocs\scanlink\web\siteadmin\application\views\codeprising\index.php(35): Kohana_Core::error_handler(8, 'Undefined index...', 'G:\xampp\htdocs...', 35, Array)
#1 G:\xampp\htdocs\scanlink\web\system\classes\kohana\view.php(61): include('G:\xampp\htdocs...')
#2 G:\xampp\htdocs\scanlink\web\system\classes\kohana\view.php(343): Kohana_View::capture('G:\xampp\htdocs...', Array)
#3 G:\xampp\htdocs\scanlink\web\system\classes\kohana\view.php(228): Kohana_View->render()
#4 G:\xampp\htdocs\scanlink\web\siteadmin\application\views\template\template.php(9): Kohana_View->__toString()
#5 G:\xampp\htdocs\scanlink\web\system\classes\kohana\view.php(61): include('G:\xampp\htdocs...')
#6 G:\xampp\htdocs\scanlink\web\system\classes\kohana\view.php(343): Kohana_View::capture('G:\xampp\htdocs...', Array)
#7 G:\xampp\htdocs\scanlink\web\system\classes\kohana\controller\template.php(44): Kohana_View->render()
#8 [internal function]: Kohana_Controller_Template->after()
#9 G:\xampp\htdocs\scanlink\web\system\classes\kohana\request\client\internal.php(121): ReflectionMethod->invoke(Object(Controller_Codeprising))
#10 G:\xampp\htdocs\scanlink\web\system\classes\kohana\request\client.php(64): Kohana_Request_Client_Internal->execute_request(Object(Request))
#11 G:\xampp\htdocs\scanlink\web\system\classes\kohana\request.php(1138): Kohana_Request_Client->execute(Object(Request))
#12 G:\xampp\htdocs\scanlink\web\siteadmin\index.php(132): Kohana_Request->execute()
#13 {main}
2013-07-08 00:16:46 --- ERROR: ErrorException [ 8 ]: Undefined index: title ~ APPPATH\views\codeprising\index.php [ 35 ]
2013-07-08 00:16:46 --- STRACE: ErrorException [ 8 ]: Undefined index: title ~ APPPATH\views\codeprising\index.php [ 35 ]
--
#0 G:\xampp\htdocs\scanlink\web\siteadmin\application\views\codeprising\index.php(35): Kohana_Core::error_handler(8, 'Undefined index...', 'G:\xampp\htdocs...', 35, Array)
#1 G:\xampp\htdocs\scanlink\web\system\classes\kohana\view.php(61): include('G:\xampp\htdocs...')
#2 G:\xampp\htdocs\scanlink\web\system\classes\kohana\view.php(343): Kohana_View::capture('G:\xampp\htdocs...', Array)
#3 G:\xampp\htdocs\scanlink\web\system\classes\kohana\view.php(228): Kohana_View->render()
#4 G:\xampp\htdocs\scanlink\web\siteadmin\application\views\template\template.php(9): Kohana_View->__toString()
#5 G:\xampp\htdocs\scanlink\web\system\classes\kohana\view.php(61): include('G:\xampp\htdocs...')
#6 G:\xampp\htdocs\scanlink\web\system\classes\kohana\view.php(343): Kohana_View::capture('G:\xampp\htdocs...', Array)
#7 G:\xampp\htdocs\scanlink\web\system\classes\kohana\controller\template.php(44): Kohana_View->render()
#8 [internal function]: Kohana_Controller_Template->after()
#9 G:\xampp\htdocs\scanlink\web\system\classes\kohana\request\client\internal.php(121): ReflectionMethod->invoke(Object(Controller_Codeprising))
#10 G:\xampp\htdocs\scanlink\web\system\classes\kohana\request\client.php(64): Kohana_Request_Client_Internal->execute_request(Object(Request))
#11 G:\xampp\htdocs\scanlink\web\system\classes\kohana\request.php(1138): Kohana_Request_Client->execute(Object(Request))
#12 G:\xampp\htdocs\scanlink\web\siteadmin\index.php(132): Kohana_Request->execute()
#13 {main}