<?php

defined('SYSPATH') or die('No direct script access.');

abstract class Controller_Template_Master extends Controller_Template
{

  // Define the template to use
  public $template = 'template/template';
  public $menu;
  public $menuSubmenu;
  public $session;

  public function before()
  {
    parent::before();

    $this->session = Session::instance();
    // Page Title
    $this->template->title = '';

    // Meta Tags
    $this->template->meta_tags = array();

    // Stylesheets
    $this->template->stylesheets = array();

    // Javascripts
    $this->template->javascripts = array();

    // header
    $this->template->headers = '';

    // No content by default
    $this->template->content = '';

    // footer
    $this->template->footer = '';

    /**
     * 	First Level Menu		
     */
    $HeadLinksArray = array(
      //array("What is ScanLink?", 0, "welcome/index", "home"), //First Array Only For Top Javascript Menu
      //array("Testimonial",0,"welcome/testimonial", "testimonial"),
      array("VOCC Login", 0, "voclogin", "voclogin"),
      // array("Mobile Video Production", 0, "mobilevideo/index", "mobileVideo")


      //array("Home",0,"welcome/index", ""), //First Array Only For Top Javascript Menu
      //array("Enquiry",0,"welcome/enquiry"),
      //array("Photo Gallery",0,"welcome/gallery"),
      /* array("Contact",0,"welcome/contact"), */
    );
    if ($this->session->get('clientuserid')) :
      array_push($HeadLinksArray, array("Contact us", 0, "welcome/contact", "contact"));
      array_push($HeadLinksArray, array("Dashboard", 0, "dashboard/index", "dash-board"));
      array_push($HeadLinksArray, array("My Account", 0, "", "arrow"));

    else :
      array_push($HeadLinksArray, array("Pricing", 0, "pricing/index", "pricing"));
      array_push($HeadLinksArray, array("Contact us", 0, "welcome/contact", "contact"));
      array_push($HeadLinksArray, array("Register", 0, "register/index", "register"));


    endif;

    if ($this->session->get('voc_user_id') != "")
      $this->voc_user_logged_in = 1;
    else
      $this->voc_user_logged_in = 0;

    array_push($HeadLinksArray, array("How to", 0, "", "arrow"));


    $client_id = $this->session->get('clientuserid');
    $user_data = Model::factory('user')->getUserDataByClientId($client_id);

    if (!empty($user_data['is_password_change'])) {
      $this->session->set('is_password_change', $user_data['is_password_change']);
    }

    if (!empty($user_data) && $user_data['is_sub_user'] == 1) {
      $this->session->set('is_sub_user', $user_data['is_sub_user']);
    } else if (empty($user_data['is_sub_user'])) {
      $this->session->set('is_sub_user', 0);
    }

    if ($this->session->get('is_sub_user')) {
      $is_sub_user = $this->session->get('is_sub_user');
    } else {
      $is_sub_user = 0;
    }

    $this->menu = $HeadLinksArray;

    if ($is_sub_user == 0) :
      $HeadSubLinksArray = array(
        array("Edit user profile", 0, "profile/index", "editprofile"),
        array("Master Code List", 0, "dashboard/mastercode", "Master Code List"),
	array("Code Balance", 0, "profile/codeBalance", "View Code List"),
        array("Purchase codes", 0, "profile/purchase", "Purchase codes"),
        array("Multiple Code Renewal", 0, "dashboard/multiple_code_renewal", "Purchase codes")
      );
    else :
      $HeadSubLinksArray = array(
        array("Edit user profile", 0, "profile/index", "editprofile"),
	array("Master Code List", 0, "dashboard/mastercode", "Master Code List")
      );
    endif;

    $this->menuSubmenu = $HeadSubLinksArray;
  }
}
