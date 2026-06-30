<?php

defined('SYSPATH') or die('No direct script access.');

class Controller_Settings extends Controller_Template_Master {

  public function before() {

    parent::before();

    $session = Session::instance();
    if (!$session->get('adminuserid')):
      $this->request->redirect('welcome/index');
    endif;

    // Default Head view file
    $this->template->head = View::factory('template/head');

    // Set the default Page Title
    $this->template->head->title = 'Welcome to ScanLink';

    // All CSS Stylesheets
    $this->template->head->stylesheets = array(array('href' => 'styles/site.css', 'media' => 'screen'), array('href' => 'styles/dropdown.css', 'media' => 'screen'), array('href' => 'styles/default.css', 'media' => 'screen'), array('href' => 'styles/colorbox.css', 'media' => 'screen'), array('href' => 'styles/jquery-ui.css', 'media' => 'screen'));

    // All JS Libraries and Scripts
    $this->template->head->javascripts = array('js/jquery.min.js', 'js/jquery.colorbox.js', 'js/jquery-ui.min.js', 'js/functions.js', "js/paginator.js");

    // Default Header view file
    $this->template->headers = View::factory('template/headers');
    $this->template->headers->logoutlnk = View::factory('template/logoutlnk');

    // Default Header view file
    $this->template->menu = View::factory('template/menu');

    $this->template->menu->HeadLinksArray = $this->menu;


    $this->template->footer = '';

    if ($this->template->footer):
      // All JS Libraries and Scripts
      $this->template->footer->scripts = array();
    endif;
  }

  public function action_index() {
    $errors = '';
    $succmsg = '';
    if (isset($_POST['btnsettings']) && $_POST['btnsettings']) {
      array_pop($_POST);
      $paypal_email = $_POST['paypal_email'];
      $youtube_username = $_POST['youtube_username'];
      $youtube_password = $_POST['youtube_password'];
      $contact_email = $_POST['contact_email'];
      $youtube_developer_key = $_POST['youtube_developer_key'];
      $youtube_client_id = $_POST['youtube_client_id'];
      $youtube_application_id = $_POST['youtube_application_id'];
      if ($paypal_email && $youtube_username && $youtube_password && $contact_email && $youtube_developer_key && $youtube_client_id && $youtube_application_id) {
        $editid = Model::factory('settings')->editallsettings($_POST);
        $succmsg = 'Settings updated successfully..!';
      } else {
        $errors = 'Please Enter All Fields Value';
      }
    }
    $settingslist = Model::factory('settings')->getallsettings();
    $this->template->content = View::factory('settings/index')
            ->bind('succmsg', $succmsg)
            ->bind('errors', $errors)
            ->bind('settings', $settingslist);
  }

  public function action_edit() {
    $id = $this->request->param('id');
    $post = array();
    $post1 = array();
    $last_id = array();
    $postArr = array();
    $errors = '';
    $succmsg = '';
    $last_id = $id;
    //if(!isset($_POST)){ exit;}
    if (isset($id) && $id && !(isset($_POST['btnEditclient']))):
      $clientinfo = Model::factory('client')->getclientbyid($id);
      $post['txtClientname'] = $clientinfo[0]['client_name'];
      $post['txtAddress'] = $clientinfo[0]['address'];
      $post['txtTelephone'] = $clientinfo[0]['telephone'];
      $post['txtContactperson'] = $clientinfo[0]['contact_person'];
      $post['txtRegidate'] = $clientinfo[0]['regi_date'];
      $post['txtEmail'] = $clientinfo[0]['email'];
      $post['txtPassword'] = $clientinfo[0]['password'];
      $post['txtUrl'] = $clientinfo[0]['url'];
    endif;

    if (isset($id) && $id && isset($_POST) && isset($_POST['btnEditclient']) && $_POST['btnEditclient'] != ''):
      $post = $_POST;
    endif;
    if (isset($_POST['btnEditclient']) && $_POST['btnEditclient']):
      $txtClientname = trim($_POST['txtClientname']);
      $txtAddress = trim($_POST['txtAddress']);
      $txtTelephone = trim($_POST['txtTelephone']);
      $txtContactperson = trim($_POST['txtContactperson']);
      $txtRegidate = trim($_POST['txtRegidate']);
      $txtEmail = trim($_POST['txtEmail']);
      $txtPassword = trim($_POST['txtPassword']);
      $txtUrl = trim($_POST['txtUrl']);
      if ($txtClientname == ''):
        $errors = "Client name can't left blank..!";
      elseif ($txtAddress == ''):
        $errors = "Address can't left blank..!";
      elseif ($txtTelephone == ''):
        $errors = "Telephone can't left blank..!";
      elseif ($txtContactperson == ''):
        $errors = "Contact Person can't left blank..!";
      elseif ($txtRegidate == ''):
        $errors = "Registration Date can't left blank..!";
      elseif ($txtEmail == ''):
        $errors = "Email Address can't left blank..!";
      elseif (!preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/i", $txtEmail)):
        $errors = 'Please enter valid email address..!';
      elseif ($txtUrl == ''):
        $errors = "URL can't left blank..!";
      else:
        $url = Model::factory('client')->getdupurl(array('url' => $txtUrl, 'mode' => 'edit', 'id' => $last_id));
        $email = Model::factory('client')->getdupemail(array('email' => $txtEmail, 'mode' => 'edit', 'id' => $last_id));
        if (count($url) > 0):
          $errors = "You entered duplicate url..!";
        elseif (count($email) > 0):
          $errors = "You entered duplicate email..!";
        else:
          $postArr['client_name'] = $txtClientname;
          $postArr['address'] = $txtAddress;
          $postArr['telephone'] = $txtTelephone;
          $postArr['contact_person'] = $txtContactperson;
          $postArr['regi_date'] = $txtRegidate;
          $postArr['email'] = $txtEmail;
          $postuserArr = array();
          $postuserArr['email'] = $txtEmail;
          if ($txtPassword):
            $postArr['password'] = ($txtPassword);
            $postuserArr['password'] = ($txtPassword);
          endif;
          $postArr['url'] = $txtUrl;
          $postArr['approve'] = 1;
          $postArr['created_at'] = date('Y-m-d H:i:s');
          $update_id = Model::factory('client')->editclient($last_id, $postArr, $postuserArr);
          $succmsg = 'Client updated successfully..!';
        endif;
      endif;
    endif;
    //var_dump($last_id);
    $users = Model::factory('user')->getalluser($last_id);
    // Default Content template
    $this->template->content = View::factory('client/edit')
            ->bind('post', $post)
            ->bind('post1', $post1)
            ->bind('last_id', $last_id)
            ->bind('users', $users)
            ->bind('succmsg', $succmsg)
            ->bind('errors', $errors);
  }

}

// End Welcome