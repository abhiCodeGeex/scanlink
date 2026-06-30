<?php

defined('SYSPATH') or die('No direct script access.');

class Controller_Codeorder extends Controller_Template_Master {

  public function before() {

    parent::before();


    // Default Head view file
    $this->template->head = View::factory('template/head');

    // Set the default Page Title
    $this->template->head->title = 'Welcome to ScanLink';

    // All CSS Stylesheets
    $this->template->head->stylesheets = array(array('href' => 'styles/site.css', 'media' => 'screen'), array('href' => 'styles/dropdown.css', 'media' => 'screen'), array('href' => 'styles/default.css', 'media' => 'screen'));

    // All JS Libraries and Scripts
    $this->template->head->javascripts = array("js/jquery.js", "js/paginator.js");

    // Default Header view file
    $this->template->headers = View::factory('template/headers');
    $this->template->headers->logoutlnk = View::factory('template/logoutlnk');

    // Default Header view file
    $this->template->menu = View::factory('template/menu');

    $this->template->menu->HeadLinksArray = $this->menu;

    // Default Content template
    $this->template->content = View::factory('order/index');

    $this->template->content->menu = $this->menu;
    // Default Content template
    $this->template->footer = '';

    if ($this->template->footer):
      // All JS Libraries and Scripts
      $this->template->footer->scripts = array();
    endif;
  }

  public function action_index() {
    $session = Session::instance();
    if (!$session->get('adminuserid')):
      $this->request->redirect('welcome/index');
    endif;


    if (isset($_POST["status"])) {
      $status = $_POST['status'];
    } else {
      $status = 'All';
    }

    $session->set('status', $status);

    $status = $session->get('status');

    if ($status == "All") {
      $orders = Model::factory("codeorder")->getAllOrders();
    } else {
      $orders = Model::factory("codeorder")->getOrdersByStatus($status);
    }

    $this->template->content = View::factory('codeorder/index')
            ->bind("orders", $orders)
            ->bind('status', $status);
  }

  public function action_view() {
  	
    $session = Session::instance();
    if (!$session->get('adminuserid')):
      $this->request->redirect('welcome/index');
    endif;

    $id = NULL;
    if (isset($_POST['id']) && isset($_POST['status'])) {
      $id = $_POST['id'];
      $status = $_POST['status'];
      Model::factory("codeorder")->updateOrder($id, array('status' => $status));
    } else {
      $id = $this->request->param('id');
    }

    $order = Model::factory("codeorder")->getOrder($id);
    $order_details = Model::factory("codeorder")->getOrderDetails($id);
	
    $client_id = $order['client_id'];
    
    $user = Model::factory("client")->getUsersByClientId($client_id);
    
    $reseller_details = "";
    
    if($user[0]["client_reseller_code"]!="") //get reseller details if client_reseller_code is not empty
	 {
    		$client_reseller_id = Model::factory('client')->get_client_id_using_reseller_code($user[0]["client_reseller_code"]);
    		
    		$reseller_details_arr = Model::factory("client")->get_user_by_clientid_and_role($client_reseller_id);
    		
    		if(count($reseller_details_arr)>0)
    		{
				$reseller_details =  $reseller_details_arr[0];
    		}
    }
    
    //$profile = Model::factory("profiles")->getProfileById($order['profile_id']);


    $this->template->content = View::factory('codeorder/view')
            ->bind("order", $order)
            ->bind("order_details", $order_details)
            ->bind('profile', $profile)
            ->bind('reseller_details', $reseller_details)
            ;
  }

}

// End Welcome
