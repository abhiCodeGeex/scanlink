<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Password extends Controller_Template_Master {
	public $session;
	public function before() {
		
		parent::before();
		
		$this->session = Session::instance();	
		if(!$this->session->get('adminuserid')):
			$this->request->redirect('welcome/index');
		endif;
		
		// Default Head view file
		$this->template->head = View::factory('template/head');
		
		// Set the default Page Title
		$this->template->head->title = 'Welcome to ScanLink';
		
		// All CSS Stylesheets
		$this->template->head->stylesheets = array(array('href'=>'styles/site.css','media'=>'screen'), array('href'=>'styles/dropdown.css','media'=>'screen'), array('href'=>'styles/default.css','media'=>'screen'), array('href'=>'styles/colorbox.css','media'=>'screen'), array('href'=>'styles/jquery-ui.css','media'=>'screen'));
		
		// All JS Libraries and Scripts
		$this->template->head->javascripts = array('js/jquery.min.js','js/jquery.colorbox.js','js/jquery-ui.min.js','js/functions.js');		
		
		// Default Header view file
		$this->template->headers = View::factory('template/headers');
		$this->template->headers->logoutlnk = View::factory('template/logoutlnk');
		
		// Default Header view file
		$this->template->menu = View::factory('template/menu');
		
		$this->template->menu->HeadLinksArray = $this->menu;
		
		
		$this->template->footer = '';
		
		if($this->template->footer):
		// All JS Libraries and Scripts
		$this->template->footer->scripts = array();
		endif;
		
	}

	public function action_index()
	{
		$succmsg='';
		$errors ='';
		$post = array();
		if(isset($_POST['btnChangePass']) && $_POST['btnChangePass'] != ''):
			$post = $_POST;
			$txtoldpass = addslashes(trim($_POST['txtoldpass']));
			$txtnewpass = addslashes(trim($_POST['txtnewpass']));
			$txtconfirmpass = addslashes(trim($_POST['txtconfirmpass']));
			if( $txtoldpass == ''):
				$errors = "Old Password can't left blank..!";
			else:
				$adminpass = Model::factory('admin')->getpass($this->session->get('adminuserid'),($txtoldpass));
				//print_r($adminpass);exit;
				if(empty($adminpass)):
					$errors = "you enter wrong Old Password..!";
				elseif($txtnewpass == ''):
					$errors = "New Password can't left blank..!";
				elseif((strlen($txtnewpass) < 5) || (strlen($txtnewpass) > 12)):
					$errors = "Password must between 5-12 character..!";				
				elseif($txtconfirmpass == ''):
					$errors = "Confirm Password can't left blank..!";
				elseif($txtnewpass !== $txtconfirmpass):
					$errors = "Confirm Password must be same as New Password..!";
				else:
					$changedpass = Model::factory('admin')->editpass($this->session->get('adminuserid'),array('password'=>($txtnewpass)));
					$post = array();
					$succmsg='Your Password has been changed..!';
				endif;
			endif;
		endif;
		//$clientlist = Model::factory('client')->getallclient();
		// Default Content template
		$this->template->content = View::factory('password/index')
		->bind('succmsg',$succmsg)
		->bind('errors',$errors)
		->bind('post',$post);
		
	}	
	public function action_add()
	{
		//print_r($_POST);
		$post = array();
		$post1 = array();
		if(isset($_POST['btnAddclient'])):
			$post = $_POST;
		endif;
		if(isset($_POST['btnAdduser'])):
			$post1 = $_POST;
		endif;
		$errors = '';
		$succmsg = '';
		$last_id = array();
		$postArr = array();
		if(isset($_POST['btnAddclient']) && $_POST['btnAddclient']):		
			$txtClientname = trim($_POST['txtClientname']);
			$txtAddress = trim($_POST['txtAddress']);
			$txtTelephone = trim($_POST['txtTelephone']);
			$txtContactperson = trim($_POST['txtContactperson']);
			$txtRegidate = trim($_POST['txtRegidate']);
			$txtEmail = trim($_POST['txtEmail']);
			$txtPassword = trim($_POST['txtPassword']);
			$txtUrl = trim($_POST['txtUrl']);		
			if( $txtClientname == ''):
				$errors = "Client name can't left blank..!";
			elseif($txtAddress == ''):
				$errors = "Address can't left blank..!";
			elseif($txtTelephone == ''):
				$errors = "Telephone can't left blank..!";
			elseif($txtContactperson == ''):
				$errors = "Contact Person can't left blank..!";
			elseif($txtRegidate == ''):
				$errors = "Registration Date can't left blank..!";
			elseif($txtEmail == ''):
				$errors = "Email Address can't left blank..!";
			elseif(!preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/i", $txtEmail)):
				$errors = 'Please enter valid email address..!';
			elseif($txtPassword == ''):
				$errors = "Password can't left blank..!";
			elseif($txtUrl == ''):
				$errors = "URL can't left blank..!";
			else:
				$url  = Model::factory('client')->getdupurl(array('url'=>$txtUrl));
				$email  = Model::factory('client')->getdupemail(array('email'=>$txtEmail));
				if(count($url) > 0):
					$errors = "You entered duplicate url..!";
				elseif(count($email) > 0):
					$errors = "You entered duplicate email..!";
				else:
					$postArr['client_name'] = $txtClientname;
					$postArr['address'] = $txtAddress;
					$postArr['telephone'] = $txtTelephone;
					$postArr['contact_person'] = $txtContactperson;
					$postArr['regi_date'] = $txtRegidate;
					$postArr['email'] = $txtEmail;
					$postArr['password'] = ($txtPassword);
					$postArr['url'] = $txtUrl;
					$postArr['approve'] = 1;
					$postArr['created_at'] = date('Y-m-d H:i:s');					
					$last_id  = Model::factory('client')->addclient($postArr);
					$succmsg = 'Client added successfully...!';
				endif;
			endif;					
		endif;
		if(isset($_POST['btnAdduser']) && $_POST['btnAdduser']):		
			$txtClientid = $_POST['clientid'];
			$txtUseremail = trim($_POST['txtUseremail']);
			$txtUserpassword = trim($_POST['txtUserpassword']);			
			if( $txtUseremail == ''):
				$errors = "Email Address can't left blank..!";
			elseif(!preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/i", $txtUseremail)):
				$errors = 'Please enter valid email address..!';
			elseif($txtUserpassword == ''):
				$errors = "Password can't left blank..!";
			elseif($txtClientid == ''):
				$errors = "You must add client first..!";			
			else:
				$useremail = Model::factory('client')->getuseremail($txtUseremail);
				if(count($useremail) > 0):
					$errors = "You entered duplicate email..!";
				else:
					$postArr['client_id'] = $txtClientid;
					$postArr['email'] = $txtUseremail;
					$postArr['password'] = ($txtUserpassword);				
					$postArr['created_at'] = date('Y-m-d H:i:s');					
					$last_id1  = Model::factory('client')->adduser($postArr);
					$succmsg = 'User added successfully..!';
				endif;
			endif;					
		endif;
		$this->template->content = View::factory('client/add')
		->bind('post', $post)
		->bind('post1', $post1)
		->bind('last_id', $last_id)
		->bind('errors', $errors)
		->bind('succmsg', $succmsg);
	}
} // End Welcome
