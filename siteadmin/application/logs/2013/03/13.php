<?php defined('SYSPATH') or die('No direct script access.'); ?>

2013-03-13 01:26:38 --- ERROR: ErrorException [ 8 ]: Undefined variable: gallery ~ APPPATH\classes\controller\template\master.php [ 74 ]
2013-03-13 01:26:38 --- STRACE: ErrorException [ 8 ]: Undefined variable: gallery ~ APPPATH\classes\controller\template\master.php [ 74 ]
--
#0 G:\xampp\htdocs\scanlink\siteadmin\application\classes\controller\template\master.php(74): Kohana_Core::error_handler(8, 'Undefined varia...', 'G:\xampp\htdocs...', 74, Array)
#1 G:\xampp\htdocs\scanlink\siteadmin\application\classes\controller\dashboard.php(7): Controller_Template_Master->before()
#2 [internal function]: Controller_Dashboard->before()
#3 G:\xampp\htdocs\scanlink\system\classes\kohana\request\client\internal.php(103): ReflectionMethod->invoke(Object(Controller_Dashboard))
#4 G:\xampp\htdocs\scanlink\system\classes\kohana\request\client.php(64): Kohana_Request_Client_Internal->execute_request(Object(Request))
#5 G:\xampp\htdocs\scanlink\system\classes\kohana\request.php(1138): Kohana_Request_Client->execute(Object(Request))
#6 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#7 {main}
2013-03-13 01:26:48 --- ERROR: HTTP_Exception_404 [ 404 ]: The requested URL testimonial/index was not found on this server. ~ SYSPATH\classes\kohana\request\client\internal.php [ 87 ]
2013-03-13 01:26:48 --- STRACE: HTTP_Exception_404 [ 404 ]: The requested URL testimonial/index was not found on this server. ~ SYSPATH\classes\kohana\request\client\internal.php [ 87 ]
--
#0 G:\xampp\htdocs\scanlink\system\classes\kohana\request\client.php(64): Kohana_Request_Client_Internal->execute_request(Object(Request))
#1 G:\xampp\htdocs\scanlink\system\classes\kohana\request.php(1138): Kohana_Request_Client->execute(Object(Request))
#2 G:\xampp\htdocs\scanlink\siteadmin\index.php(132): Kohana_Request->execute()
#3 {main}