<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Welcome extends Controller_Template_Master {
	
	public function before() {
		
		parent::before();
		
		
		// Default Head view file
		$this->template->head = View::factory('template/head');
		
		// Set the default Page Title
		$this->template->head->title = 'Welcome to ScanLink';
		
		/*// Set the default Meta Title
		$this->template->head->meta_title = 'Welcome to ScanLink';
		
		// Set the default Description
		$this->template->head->meta_description = 'scanlink';
		
		// Set the default Keywords
		$this->template->head->meta_keywords = 'scanlink';
		
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
		//$this->template->headers->logoutlnk = View::factory('template/logoutlnk');
		
		// Default Content template
		$this->template->content = View::factory('index');
		
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
		if($session->get('adminuserid')):
			$this->request->redirect('dashboard/index');
		endif;
		$errors = '';
		$post = array();
		$postArr = array();
		if($_POST):		
		$username = trim($_POST['username']);
		$pass = trim($_POST['password']);
			if( $username == ''):
				$errors = "Username can't left blank..!";			
			elseif($pass == ''):
				$errors = "Password can't left blank..!";
			else:
				$postArr['username'] = $username;
				$postArr['password'] = ($pass);
				$data  = Model::factory('admin')->loadAdmin($postArr);
				if(empty($data)):
					$errors = "Invalid Username or Password...!";
				else:					
					$sdata = $session->set('adminuser',$username);	
					$sdata = $session->set('adminuserid',$data[0]['id']);					
					$this->request->redirect('dashboard/index');
				endif;
			endif;	
				
		endif;
		$this->template->content = View::factory('index')
		->bind('post', $_POST)
		->bind('errors', $errors);		
 
	}

} // End Welcome
