<?php
 
defined('SYSPATH') or die('No direct script access.');

class Controller_Otheruserform extends Controller_Template_Other {
    public $clientlogin = false;
    public function before() {

        parent::before();

        if ($this->session->get('clientuserid')):
            $this->clientlogin = true;
        endif;

        // Default Head view file
        $this->template->head = View::factory('template/other_user_form_head');
		
		// Set the default Page Title
        $this->template->head->title = '';

        // All CSS Stylesheets
        $this->template->head->stylesheets = array();

        // All JS Libraries and Scripts
        $this->template->head->javascripts = array();
      

    }
   
    public function action_index() {
        // Default Content template
		
		$user_id = $_REQUEST["user_id"];
		$profile_id = $_REQUEST["profile_id"];
		
		$form_library_arr = Model::factory('Formbuilder')->get_library_forms_other_user($user_id);
		
		
		$this->template->content = View::factory('template/library_forms_other_user')
                ->bind('form_library_arr', $form_library_arr)
                ->bind('profile_id', $profile_id)
				;
		
		
    }
	
	
	
}