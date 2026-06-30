<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Testimonial extends Controller_Template_Master {
	
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
		$clientlist = Model::factory('paging')->getFilteredResult('testimonial','','title',1,2);
		$clientlist = $clientlist['results'];
		$this->template->content = View::factory('testimonial/index')
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
	
	public function action_checklist_option(){
		$id = $this->request->param('id');
		Model::factory('client')->changeChecklistOption($id);
		$this->request->redirect('client/index');
	}
	public function action_customqr_option(){
		$id = $this->request->param('id');
		Model::factory('client')->changeCustomqrOption($id);
		$this->request->redirect('client/index');
	}
	
	public function action_delete()
	{
		$id = $this->request->param('id');
		if(isset($id) && $id):
			Model::factory('testimonial')->deleteTestimonial($id);
		endif;
		$this->request->redirect('testimonial/index');
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
		$last_id = array();
		$postArr = array();
		$errors = '';
		$succmsg = '';
		$last_id = $id;
		//if(!isset($_POST)){ exit;}
		if(isset($id) && $id && !(isset($_POST['btnEdittestimonial']))):
			$testimonial = Model::factory('testimonial')->gettestimonialbyid($id);
			$post['txttitle'] = $testimonial[0]['title'];
			$post['txtvideo'] = $testimonial[0]['video'];
		endif;
		
		if(isset($id) && $id && isset($_POST) && isset($_POST['btnEdittestimonial']) && $_POST['btnEdittestimonial'] != ''):
			$post = $_POST;
		endif;
		
		if(isset($_POST['btnEdittestimonial']) && $_POST['btnEdittestimonial']):		
			$txttitle = trim($_POST['txttitle']);
			$txtvideo = trim($_POST['txtvideo']);
			
			if( $txttitle == ''):
				$errors = "Title can't left blank..!";
			elseif($txtvideo == ''):
				$errors = "Video can't left blank..!";
			else:
					$postArr['txttitle'] = $txttitle;
					$postArr['txtvideo'] = $txtvideo;
					
					$update_id  = Model::factory('testimonial')->updateTestimonial($last_id,$postArr);
					$succmsg = 'Testimonial updated successfully..!';
			endif;
		endif;
		
		// Default Content template
		$this->template->content = View::factory('testimonial/edit')
		->bind('post',$post)
		->bind('last_id',$last_id)
		->bind('succmsg',$succmsg)
		->bind('errors',$errors);
	}
	public function action_add()
	{
		//print_r($_POST);
		$post = array();
		if(isset($_POST['btnAddtestimonial'])):
			$post = $_POST;
		endif;
		$errors = '';
		$succmsg = '';
		$last_id = array();
		$postArr = array();
		if(isset($_POST['btnAddtestimonial']) && $_POST['btnAddtestimonial'])
		{
			$txttitle = trim($_POST['txttitle']);
			$txtvideo = trim($_POST['txtvideo']);
			
			if( $txttitle == '')
				$errors = "Title can't left blank..!";
			elseif($txtvideo == '')
				$errors = "Video embed code can't left blank..!";
			else
			{
				$postArr['txttitle'] = $txttitle;
				$postArr['txtvideo'] = $txtvideo;

				$last_id  = Model::factory('testimonial')->insertTestimonial($postArr);
				$succmsg = 'Testimonial added successfully...!';
			}
		}

		$this->template->content = View::factory('testimonial/add')
		->bind('post', $post)
		->bind('last_id', $last_id)
		->bind('errors', $errors)
		->bind('succmsg', $succmsg);
	}
	
	public function action_subdivide(){
		
		$client = $this->request->post('client');
		$profiles = $this->request->post('profiles');
		$new_client = $this->request->post('new_client');

		$users = $this->request->post('users');
		$from_users = $this->request->post('from_users');
		$errors = "";

		if(!empty($profiles) && !empty($client) && !empty($new_client) && !empty($from_users)){

		   $checklist_option = Model::factory('client')->isChecklistAllowedToClient($client);
		   $checklist = $checklist_option[0]['checklist_option'];
		   $new_client['checklist_option'] = $checklist;
		   
		   $customqr_option = Model::factory('client')->isCustomqrEnabledToClient($client);
		   $customqr = $customqr_option[0]['customqr_option'];
		   $new_client['customqr_option'] = $customqr;
	 
			$check_email_duplicate = Model::factory('client')->checkDuplicateEmail($new_client['email']);
			if(!$check_email_duplicate){
				$users=array();
				$post = $new_client;
				$errors = "Oops... Email you provided is already used. Please try another...";
				$this->template->content = View::factory('client/subdivide_step3')
				->bind('client', $client)
				->bind('client_details', $client_details[0])
				->bind('profiles', $profiles)
				->bind('post', $post)
				->bind('errors', $errors)
				->bind('succmsg', $succmsg);
			}else{
				$new_client['created_at'] = date("Y-m-d H:i:s");
				
				$old_clients_conflict_profile_ids =array();
				
				if(!empty($users)){
					$old_clients_conflict_profile_ids = Model::factory('profiles')->getConfictProfiles($users,$profiles,$client);
				}
				
				$user_id_of_old_client = Model::factory('user')->getUserIdOfClientByClientId($client);

				if(!empty($old_clients_conflict_profile_ids)){
					Model::factory('profiles')->updateUserIdsOfConflictProfiles($old_clients_conflict_profile_ids,$user_id_of_old_client[0]['id']);
				}
				
				$new_client_id  = Model::factory('client')->addclient($new_client);
				$user_id_of_new_client = Model::factory('user')->getUserIdOfClientByClientId($new_client_id[0]);
				if(!empty($users)){
					$new_clients_conflict_profile_ids = Model::factory('profiles')->getConfictProfilesOfNewClient($users,$profiles,$client);
				
					Model::factory('user')->updateClientIdOfTransferredUsers($users,$new_client_id[0]);
				}
				
				if(!empty($profiles)){
					Model::factory('profiles')->updateClientIdsOfTransferredProfiles($profiles,$new_client_id[0]);
				}
				if(!empty($new_clients_conflict_profile_ids)){
					Model::factory('profiles')->updateUserIdsOfConflictProfilesOfNewClient($new_clients_conflict_profile_ids,$user_id_of_new_client[0]['id']);
				}
				
				// Default Content template
				$succmsg = "New Client has been created...";
				$_POST = array();
				$post = array();
				$clients  = Model::factory('client')->getallclient();
				$this->template->content = View::factory('client/subdivide_step1')
				->bind('clients', $clients)
				->bind('errors', $errors)
				->bind('post', $post)
				->bind('succmsg', $succmsg);
			}

		}else if(!empty($profiles) && !empty($client) && !empty($new_client)){
			foreach($new_client as $key => $value){
				$new_client[$key] = addslashes(trim($value));
			}
			
			$check_email_format = preg_match("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$^", $new_client['email']);
			$post = array();
			$check_email_duplicate = Model::factory('client')->checkDuplicateEmail($new_client['email']);
			$client_details =  Model::factory('client')->getclientbyid($client);
			
			if(empty($new_client['client_name']) || empty($new_client['address']) || empty($new_client['telephone']) || empty($new_client['contact_person']) || empty($new_client['regi_date']) || empty($new_client['email']) || empty($new_client['password']) || empty($new_client['url'])){
				$errors = "All fields are required...";
				$post = $new_client;
			}
			
			if(!$check_email_format){
				$errors = "Email must be in proper format...";
				$post = $new_client;
			}
				
			if(!$check_email_duplicate){
				$errors = "Oops... Email you provided is already used. Please try another...";
				$post = $new_client;
			}
			
			if(!empty($errors)){
				$this->template->content = View::factory('client/subdivide_step3')
				->bind('client', $client)
				->bind('client_details', $client_details[0])
				->bind('profiles', $profiles)
				->bind('post', $post)
				->bind('errors', $errors)
				->bind('succmsg', $succmsg);
			}else{
				
				$users =  Model::factory('client')->getUsersByClientId($client);
				$this->template->content = View::factory('client/subdivide_step4')
				->bind('client', $client)
				->bind('client_details', $client_details[0])
				->bind('profiles', $profiles)
				->bind('new_client', $new_client)
				->bind('post', $post)
				->bind('users', $users)
				->bind('errors', $errors)
				->bind('succmsg', $succmsg);
			}

		}else  if(!empty($profiles) && !empty($client)){
			$client_details =  Model::factory('client')->getclientbyid($client);
			$post = array();

			$succmsg = "<strong><i>".count($profiles)."</i></strong> profiles in Client <strong><i>".$client_details[0]['client_name']."</i></strong> has been selected...";
			
			$this->template->content = View::factory('client/subdivide_step3')
			->bind('client', $client)
			->bind('client_details', $client_details[0])
			->bind('profiles', $profiles)
			->bind('post', $post)
			->bind('errors', $errors)
			->bind('succmsg', $succmsg);
			
		}else if(!empty($client)){
			$client_details =  Model::factory('client')->getclientbyid($client);
			$profiles  = Model::factory('profiles')->getProfilesByClientId($client);
			$succmsg = "Client <strong><i>".$client_details[0]['client_name']."</i></strong> is selected...";
			
			$this->template->content = View::factory('client/subdivide_step2')
			->bind('client', $client)
			->bind('profiles', $profiles)
			->bind('errors', $errors)
			->bind('succmsg', $succmsg);
		}else{
			$clients  = Model::factory('client')->getallclient();
			$this->template->content = View::factory('client/subdivide_step1')
			->bind('clients', $clients)
			->bind('errors', $errors)
			->bind('succmsg', $succmsg);
		}
	}
	
	public function action_view()
	{
		$id = $this->request->param('id');
		$testimonial = array();
		
		if(isset($id) && $id):
			$testimonial = Model::factory('testimonial')->gettestimonialbyid($id);
		endif;	
		
		$this->template->content = View::factory('testimonial/view')
		->bind('post',$testimonial);
	}
} // End Welcome
