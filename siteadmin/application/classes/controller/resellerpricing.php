<?php

defined('SYSPATH') or die('No direct script access.');

class Controller_Resellerpricing extends Controller_Template_Master {

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
      $editid = Model::factory('resellerpricing')->editallpricing($_POST);
      $succmsg = 'Reseller Pricing updated successfully..!';      
    }

    $codePrisingList = Model::factory('resellerpricing')->getallpricing();


    $this->template->content = View::factory('resellerpricing/index')
            ->bind('succmsg', $succmsg)
            ->bind('errors', $errors)
            ->bind('codePrisingList', $codePrisingList);
  }

  
}

// End Welcome