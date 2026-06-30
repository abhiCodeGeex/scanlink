<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Client extends Controller_Template_Master {
	
	public function before() {
		
		parent::before();
		
		$session = Session::instance();	
		if(!$session->get('adminuserid')):
			$this->request->redirect('welcome/index');
		endif;
		
		// Default Head view file
		$this->template->head = View::factory('template/head');
		
		// Set the default Page Title
		$this->template->head->title = 'Welcome to ScanLink';
		
		// All CSS Stylesheets
		$this->template->head->stylesheets = array(array('href'=>'styles/site.css','media'=>'screen'),array('href'=>'styles/style.css','media'=>'screen'), array('href'=>'styles/dropdown.css','media'=>'screen'), array('href'=>'styles/default.css','media'=>'screen'), array('href'=>'styles/colorbox.css','media'=>'screen'), array('href'=>'styles/jquery-ui.css','media'=>'screen'));
		
		// All JS Libraries and Scripts
		$this->template->head->javascripts = array('js/jquery.min.js','js/jquery.colorbox.js','js/jquery-ui.min.js','js/functions.js', "js/paginator.js");
		
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
		$clientlist = Model::factory('client')->getallclient();
		// Default Content template
		$clientlist = Model::factory('paging')->getFilteredResult('clients','','client_name',1,2);
		$clientlist = $clientlist['results'];
		
		$this->template->content = View::factory('client/index')
		->bind('clients',$clientlist);
		
	}
	
	public function action_getData(){
		$curpage= $_POST['curpage'];
		$perpage= $_POST['perpage'];
		$entity= $_POST['entity'];
		$filter= $_POST['filter'];
		$filter_on= $_POST['filter_on'];

		header('Cache-Control: no-cache, must-revalidate');
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		header('Content-type: application/json');
		$clientlist = Model::factory('paging')->getFilteredResult($entity,$filter,$filter_on,$curpage,$perpage);
		print(json_encode($clientlist));
		exit;
	}
	
	public function action_delete()
	{
		$id = $this->request->param('id');
		if(isset($id) && $id):
			Model::factory('client')->deleteclient($id);
		endif;
		$this->request->redirect('client/index');
	}
	public function action_blocked()
	{
		$id = $this->request->param('id');
		if(isset($id) && $id):
			Model::factory('client')->blockclient($id);
		endif;
		$this->request->redirect('client/index');
	}
	
	
	public function action_edit()
	{
		$id = $this->request->param('id');
		$post = array();
		$post1 = array();
		$last_id = array();
		$postArr = array();
		$errors = '';
		$succmsg = '';
		$last_id = $id;
		//if(!isset($_POST)){ exit;}
		if(isset($id) && $id && !(isset($_POST['btnEditclient']))):
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
		
		if(isset($id) && $id && isset($_POST) && isset($_POST['btnEditclient']) && $_POST['btnEditclient'] != ''):
			$post = $_POST;
		endif;
		if(isset($_POST['btnAdduser'])):
			$post1 = $_POST;
			$last_id = $_POST['clientid'] ;
		endif;
		if(isset($_POST['btnEditclient']) && $_POST['btnEditclient']):		
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
			elseif($txtUrl == ''):
				$errors = "URL can't left blank..!";
			else:
				$url  = Model::factory('client')->getdupurl(array('url'=>$txtUrl,'mode'=>'edit','id'=>$last_id));
				$email  = Model::factory('client')->getdupemail(array('email'=>$txtEmail,'mode'=>'edit','id'=>$last_id));
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
					$postuserArr = array();
					$postuserArr['email'] = $txtEmail;
					if($txtPassword):
					$postArr['password'] = ($txtPassword);
					$postuserArr['password'] = ($txtPassword);
					endif;
					$postArr['url'] = $txtUrl;
					$postArr['approve'] = 1;
					$postArr['created_at'] = date('Y-m-d H:i:s');					
					$update_id  = Model::factory('client')->editclient($last_id,$postArr,$postuserArr);
					$succmsg = 'Client updated successfully..!';
				endif;
			endif;					
		endif;
		if(isset($_POST['btnAdduser']) && $_POST['btnAdduser']):
			$videopermission = (isset($_POST['videopermission']) && $_POST['videopermission'] ==1) ? $_POST['videopermission']:0;
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
					$postArr['video_upload'] = $videopermission;				
					$postArr['created_at'] = date('Y-m-d H:i:s');					
					$last_id1 = Model::factory('client')->adduser($postArr);
					$succmsg = 'User added successfully..!';
				endif;
			endif;					
		endif;
		//var_dump($last_id);
		$users = Model::factory('user')->getalluser($last_id);
		// Default Content template
		$this->template->content = View::factory('client/edit')
		->bind('post',$post)
		->bind('post1',$post1)
		->bind('last_id',$last_id)
		->bind('users',$users)
		->bind('succmsg',$succmsg)
		->bind('errors',$errors);
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
			$videopermission = (isset($_POST['videopermission']) && $_POST['videopermission'] ==1) ? $_POST['videopermission']:0;	
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
					$postArr['video_upload'] = $videopermission;				
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
