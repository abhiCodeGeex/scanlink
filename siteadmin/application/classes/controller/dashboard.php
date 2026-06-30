<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Dashboard extends Controller_Template_Master {
	
	public function before() {
		
		parent::before();
		
		// Default Head view file
		$this->template->head = View::factory('template/head');
		
		// Set the default Page Title
		$this->template->head->title = 'Welcome to ScanLink';
		
		/*// Set the default Meta Title
		$this->template->head->meta_title = 'Welcome to ScanLink';
		
		// Set the default Description
		$this->template->head->meta_description = 'ScanLink';
		
		// Set the default Keywords
		$this->template->head->meta_keywords = 'ScanLink';
		
		// Set the default ROBOTS
		$this->template->head->meta_robots = 'Index, Follow';
		
		// Meta Tags
		$this->template->head->meta_tags = array('name'=>'title','value'=>'test');*/
		
		// All CSS Stylesheets
		$this->template->head->stylesheets = array(array('href'=>'styles/site.css','media'=>'screen'), array('href'=>'styles/dropdown.css','media'=>'screen'), array('href'=>'styles/default.css','media'=>'screen'));
		
		// All JS Libraries and Scripts
		$this->template->head->javascripts = array();		
		
		// Default Header view file
		$this->template->headers = View::factory('template/headers');
		$this->template->headers->logoutlnk = View::factory('template/logoutlnk');
		
		// Default Header view file
		$this->template->menu = View::factory('template/menu');
		
		$this->template->menu->HeadLinksArray = $this->menu;
		
		// Default Content template
		$this->template->content = View::factory('dashboard');
		
		$this->template->content->menu = $this->menu;
		// Default Content template
		$this->template->footer = '';
		
		if($this->template->footer):
		// All JS Libraries and Scripts
		$this->template->footer->scripts = array();
		endif;
		
	}

	public function action_index()
	{
		$session = Session::instance();	
		if(!$session->get('adminuserid')):
			$this->request->redirect('welcome/index');
		endif;		
	}

} // End Welcome
