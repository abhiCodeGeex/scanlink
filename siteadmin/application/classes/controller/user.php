<?php defined('SYSPATH') or die('No direct script access.');

class Controller_User extends Controller_Template_Master {
	
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
		$this->template->head->stylesheets = array(array('href'=>'styles/site.css','media'=>'screen'), array('href'=>'styles/dropdown.css','media'=>'screen'), array('href'=>'styles/default.css','media'=>'screen'), array('href'=>'styles/colorbox.css','media'=>'screen'));
		
		// All JS Libraries and Scripts
		$this->template->head->javascripts = array('js/jquery.min.js','js/jquery.colorbox.js','js/paginator.js');
		
		if($this->request->action() != 'edit'):
		// Default Header view file
		$this->template->headers = View::factory('template/headers');
		$this->template->headers->logoutlnk = View::factory('template/logoutlnk');
				
		// Default Header view file
		$this->template->menu = View::factory('template/menu');
		
		$this->template->menu->HeadLinksArray = $this->menu;	
		
		endif;
		// Default Content template
		$this->template->footer = '';
		
		if($this->template->footer):
		// All JS Libraries and Scripts
		$this->template->footer->scripts = array();
		endif;
		
	}

	public function action_index()
	{
		$id = $this->request->param('id');
		if(isset($id) && $id):
			$userlist = Model::factory('user')->getalluser($id);
		endif;

		// Default Content template
		$this->template->content = View::factory('user/index')	
		->bind('userlist',$userlist);
	}
	public function action_edit()
	{
		$id = $this->request->param('id');
		
		$user_data = Model::factory('user')->getuser($id);	
	
		$clientid = $this->request->param('clientid');
		
		$userpost = array();
		$errmsg = '';
		$successmsg = '';
		if($_POST):	
			//$userpost['videopermission'] = (isset($_POST['videopermission']) && $_POST['videopermission'] ==1) ? $_POST['videopermission']:0;
			$userpost['videopermission'] = 1;
			$userpost['txtUseremail'] = trim($_POST['txtUseremail']);	
			$userpost['txtUserpassword'] = trim($_POST['txtUserpassword']);
			$userArr = '';
			$userArr['email'] = $userpost['txtUseremail'];
			$userArr['video_upload'] = (isset($_POST['videopermission']) && $_POST['videopermission'] ==1) ? $_POST['videopermission']:0;
			if($userpost['txtUserpassword']):
					
				if($user_data[0]["password"]!=$userpost['txtUserpassword'])
					$userArr['password'] = $userpost['txtUserpassword'];
			
				
			endif;			
			if($userArr['email'] == ''):
				$errmsg = "Email Address can't left blank..!";
			elseif(!preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/i", $userArr['email'])):
				$errmsg = 'Please enter valid email address..!';
			else:
				$userid = Model::factory('user')->getdupemail($id,$userpost['txtUseremail']);
				if(count($userid) > 0):
					$errmsg = 'You entered duplicate email address..!';
				else:
					$updateuserid = Model::factory('user')->edituser($id,$userArr);	
					$successmsg = 'User updated successfully..!';							
				endif;
			endif;
		endif;

		if(isset($id) && $id):
			$userlist = Model::factory('user')->getuser($id);		
			//print_r($userlist);exit;
			$userpost['txtUseremail'] = $userlist[0]['email'];
			$userpost['txtUserpassword'] = $userlist[0]['password'];
			$userpost['videopermission'] = $userlist[0]['video_upload'];
		endif;
		// Default Content template
		$this->template->content = View::factory('user/edit')	
		->bind('post1',$userpost)
		->bind('userid',$id)
		->bind('successmsg',$successmsg)
		->bind('errmsg',$errmsg);
	}
	
	public function action_update(){
		$userpost = array();
		$expire_at = trim($_POST['expireDate']);	
		$id = $_POST['id'];
		Model::factory('user')->edituser($id,array('expire_at'=>$expire_at,'notice'=>0));	
		Model::factory('user')->insertRenewal(array('user_id'=>$id, 'renew_at'=>date('Y-m-d H:i:s')));	
		Model::factory('user')->insertRenewal(array('user_id'=>$id, 'renew_at'=>date('Y-m-d H:i:s')));	
		$user = Model::factory('user')->getuser($id);
		$user = $user[0];
		$mailTo = $user['email'];
		$fromemail = Model::factory('settings')->getValueByTitle('contact_email');
		$subject = 'Re-new account notification';
		$message = '<html>
		<head>
		<title>ScanLink account renew notification.</title>
		</head>
		<body>
		<p>Hi there!</p>
		<p>As usual, ScanLink is always happy to help!</p>
		<p>Your account has been re-newed by admin, your account will expire on '.$user['expire_at'].'.</p>
		<p>Best regards,</p>
		<p><img src="'.URL::base().'images/qr-img-regards.png" /></p>
		</body>
		</html>';
		// To send HTML mail, the Content-type header must be set
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		$headers .= 'From: ScanLink Solutions <'.$fromemail.'>' . "\r\n";
		$dataArr = array();
		@mail($mailTo, $subject, $message, $headers);
		print(1);
		exit;
		
	}
	
	public function action_renewaccount(){
		$id = $_POST['id'];
		if(!empty($id)){
			$user = Model::factory('user')->getuser($id);
			$user = $user[0];
			if($user['expire_at'] < date('Y-m-d H:i:s')){
				$expire_at = date('Y-m-d H:i:s',strtotime('+1 years', strtotime(date('Y-m-d H:i:s'))));
				Model::factory('user')->edituser($id,array('expire_at'=>$expire_at, 'notice'=>0));	
			}else{
				$expire_at = date('Y-m-d H:i:s',strtotime('+1 years', strtotime($user['expire_at'])));
				Model::factory('user')->edituser($id,array('expire_at'=>$expire_at, 'notice'=>0));	
			}
			Model::factory('user')->insertRenewal(array('user_id'=>$id, 'renew_at'=>date('Y-m-d H:i:s')));	
			$mailTo = $user['email'];
			$fromemail = Model::factory('settings')->getValueByTitle('contact_email');
			$subject = 'Re-new account notification';
			$message = '<html>
			<head>
			<title>ScanLink account renew notification.</title>
			</head>
			<body>
			<p>Hi there!</p>
			<p>As usual, ScanLink is always happy to help!</p>
			<p>Your account has been re-newed by admin, your account will expire on '.$user['expire_at'].'.</p>
			<p>Best regards,</p>
			<p><img src="'.URL::base().'images/qr-img-regards.png" /></p>
			</body>
			</html>';
			// To send HTML mail, the Content-type header must be set
			$headers  = 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
			$headers .= 'From: ScanLink Solutions <'.$fromemail.'>' . "\r\n";
			$dataArr = array();
			@mail($mailTo, $subject, $message, $headers);
			print(1);
			exit;
		}
	}
	
	public function action_blocked()
	{
		$id = $this->request->param('id');
		$clientid = $this->request->param('clientid');
		if(isset($id) && $id):
			Model::factory('user')->blockuser($id);
		endif;
		$flag = $this->request->param('mode');
		if(isset($flag) && $flag == 'cled'):
			$reurl = 'client/edit/'.$clientid;
		else:
			$reurl = 'user/index/'.$clientid;
		endif;
		$this->request->redirect($reurl);
	}
	public function action_delete()
	{
		$id = $this->request->param('id');
		$clientid = $this->request->param('clientid');
		if(isset($id) && $id):
			Model::factory('user')->deleteuser($id);		
		endif;
		$flag = $this->request->param('mode');
		if(isset($flag) && $flag == 'cled'):
			$reurl = 'client/edit/'.$clientid;
		else:
			$reurl = 'user/index/'.$clientid;
		endif;
		$this->request->redirect($reurl);
	}
	public function action_videop()
	{
		$id = $this->request->param('id');
		$clientid = $this->request->param('clientid');
		if(isset($id) && $id):
			Model::factory('user')->videopermission($id);
		endif;
		$flag = $this->request->param('mode');
		if(isset($flag) && $flag == 'cled'):
			$reurl = 'client/edit/'.$clientid;
		else:
			$reurl = 'user/index/'.$clientid;
		endif;
		$this->request->redirect($reurl);
	}

} // End Welcome
