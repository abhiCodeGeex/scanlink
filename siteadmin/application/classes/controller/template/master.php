<?php

defined('SYSPATH') or die('No direct script access.');

abstract class Controller_Template_Master extends Controller_Template {

  // Define the template to use
  public $template = 'template/template';
  public $menu = array();

  public function before() {
    parent::before();


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

    // menu
    $this->template->menu = '';

    // No content by default
    $this->template->content = '';

    // footer
    $this->template->footer = '';

    /**
      Third Level Menu
     * */
    $client = array(
        array("Add Client", 0, "client/add"),
        array("Manage Client", 0, "client/index"),
        array("Sub Divide Client", 0, "client/subdivide")
    );

    /* $user = array (
      array("Add User",0,"user/add"),
      array("Manage User",0,"user/index")
      ); */

    $product = array(
        array("Manage Product", 0, "product/index")
    );

    $order = array(
        array("Manage Order", 0, "order/index"),
        array("Manage Code Order", 0, "codeorder/index"),
        array("Manage Form Builder Order", 0, "formbuilderorder/index")
    );
    $setting = array(
        array("Global Settings", 0, "settings/index"),
        array("Code Pricing", 0, "codepricing/index")
    );

    $testimonial = array(
        array("Manage Testimonial", 0, "testimonial/index")
    );
    /**
      Second Level Menu
     * */
    $website = array(
        array("Client", count($client), $client, "client/index"),
        /* array("User",count($user),$user,"user/index"), */
        array("Product", count($product), $product, "product/index"),
        array("Order", count($order), $order, "order/index"),
        array("Settings", count($setting), $setting, "settings/index"),
        array("Testimonial", count($testimonial), $testimonial, "testimonial/index"),
    );
    /**
      First Level Menu
     * */
    $HeadLinksArray = array(
        array("Admin Home", 0, "dashboard/index"), //First Array Only For Top Javascript Menu
        array("Website", count($website), $website),
        array("Change Password", 0, "password/index"),
    );
    $this->menu = $HeadLinksArray;
  }

}

?>