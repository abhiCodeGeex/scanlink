<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class Controller_Demo extends Controller_Template_Master {

  public function action_index() {
    ini_set('include_path', ini_get('include_path') . PATH_SEPARATOR . APPPATH . 'vendor/');
    require_once 'Zend/Loader.php';
    Zend_Loader::loadClass('Zend_Gdata_YouTube');
    Zend_Loader::loadClass('Zend_Gdata_ClientLogin');


    $username_new = 'webplanex.test@gmail.com';
    $password_new = 'WebPlanex123';

    $authenticationURL = 'https://www.google.com/accounts/ClientLogin';
    $httpClient = Zend_Gdata_ClientLogin::getHttpClient(
                    $username = $username_new, $password = $password_new, $service = 'youtube', $client = null, $source = 'youtube video upload', // a short string identifying your application
                    $loginToken = null, $loginCaptcha = null, $authenticationURL
    );
    
    echo "connected";
    
    exit;
  }

}

?>
