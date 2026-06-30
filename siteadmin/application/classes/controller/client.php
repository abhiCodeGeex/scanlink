<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Client extends Controller_Template_Master
{

	public function before()
	{

		parent::before();

		$session = Session::instance();
		if (!$session->get('adminuserid')) :
			$this->request->redirect('welcome/index');
		endif;

		// Default Head view file
		$this->template->head = View::factory('template/head');

		// Set the default Page Title
		$this->template->head->title = 'Welcome to ScanLink';

		// All CSS Stylesheets
		$this->template->head->stylesheets = array(array('href' => 'styles/site.css', 'media' => 'screen'), array('href' => 'styles/style.css', 'media' => 'screen'), array('href' => 'styles/dropdown.css', 'media' => 'screen'), array('href' => 'styles/default.css', 'media' => 'screen'), array('href' => 'styles/colorbox.css', 'media' => 'screen'), array('href' => 'styles/jquery-ui.css', 'media' => 'screen'));

		// All JS Libraries and Scripts
		$this->template->head->javascripts = array('js/jquery.min.js', 'js/jquery.colorbox.js', 'js/jquery-ui.min.js', 'js/functions.js', "js/paginator.js");

		// Default Header view file
		$this->template->headers = View::factory('template/headers');
		$this->template->headers->logoutlnk = View::factory('template/logoutlnk');

		// Default Header view file
		$this->template->menu = View::factory('template/menu');

		$this->template->menu->HeadLinksArray = $this->menu;

		$this->template->footer = '';

		if ($this->template->footer) :
			// All JS Libraries and Scripts
			$this->template->footer->scripts = array();
		endif;
	}

	public function action_index()
	{
		$clientlist = Model::factory('client')->getallclient();
		// Default Content template
		$clientlist = Model::factory('paging')->getFilteredResult('clients', '', 'client_name', 1, 2);
		$clientlist = $clientlist['results'];
		$this->template->content = View::factory('client/index')
			->bind('clients', $clientlist);
	}

	public function action_getData()
	{
		$curpage = $_POST['curpage'];
		$perpage = $_POST['perpage'];
		$entity = $_POST['entity'];
		$filter = $_POST['filter'];
		$filter_on = $_POST['filter_on'];

		header('Cache-Control: no-cache, must-revalidate');
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		header('Content-type: application/json');
		$clientlist = Model::factory('paging')->getFilteredResult($entity, $filter, $filter_on, $curpage, $perpage);
		print(json_encode($clientlist));
		exit;
	}

	public function action_checklist_option()
	{
		$id = $this->request->param('id');
		Model::factory('client')->changeChecklistOption($id);
		$this->request->redirect('client/index');
	}
	public function action_customqr_option()
	{
		$id = $this->request->param('id');
		Model::factory('client')->changeCustomqrOption($id);
		$this->request->redirect('client/index');
	}

	public function action_delete()
	{
		$id = $this->request->param('id');
		if (isset($id) && $id) :
			Model::factory('client')->deleteclient($id);
		endif;
		$this->request->redirect('client/index');
	}
	public function action_blocked()
	{
		$id = $this->request->param('id');
		if (isset($id) && $id) :
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
		$reseller_succmsg = '';
		$reseller_errors = '';
		$add_free_code_succmsg = '';
		$add_free_code_errors = '';
		$last_id = $id;

		$resellerCode = Model::factory('client')->getResllerCodeByClientId($id);

		//if(!isset($_POST)){ exit;}
		if (isset($id) && $id && !(isset($_POST['btnEditclient']))) :
			$clientinfo = Model::factory('client')->getclientbyid($id);
			$post['txtClientname'] = $clientinfo[0]['client_name'];
			$post['txtAddress'] = $clientinfo[0]['address'];
			$post['txtTelephone'] = $clientinfo[0]['telephone'];
			$post['txtContactperson'] = $clientinfo[0]['contact_person'];
			$post['txtRegidate'] = $clientinfo[0]['regi_date'];
			$post['txtEmail'] = $clientinfo[0]['email'];
			$post['txtResellerEmail'] = $clientinfo[0]['reseller_email'];
			$post['txtPassword'] = $clientinfo[0]['password'];
			$post['txtUrl'] = $clientinfo[0]['url'];

			$client_reseller_code = $clientinfo[0]['client_reseller_code']; //this reseller code is code of client who created this user

			$post['checklist_option'] = (isset($clientinfo[0]['checklist_option'])) ? $clientinfo[0]['checklist_option'] : 0;
			$post['customqr_option'] = (isset($clientinfo[0]['customqr_option'])) ? $clientinfo[0]['customqr_option'] : 0;
		endif;

		if (isset($id) && $id && isset($_POST) && isset($_POST['btnEditclient']) && $_POST['btnEditclient'] != '') :
			$post = $_POST;
		endif;

		if (isset($_POST['btnAdduser'])) :
			$post1 = $_POST;
			$last_id = $_POST['clientid'];
		endif;

		/*** Edit user code start*/

		if (isset($_POST['btnEditclient']) && $_POST['btnEditclient']) {

			$txtClientname = trim($_POST['txtClientname']);
			$txtAddress = trim($_POST['txtAddress']);
			$txtTelephone = trim($_POST['txtTelephone']);
			$txtContactperson = trim($_POST['txtContactperson']);
			$txtRegidate = trim($_POST['txtRegidate']);
			$txtEmail = trim($_POST['txtEmail']);
			$txtResellerEmail = trim($_POST['txtResellerEmail']);
			$txtPassword = trim($_POST['txtPassword']);

			$checklist_option = (isset($_POST['checklist_option'])) ? trim($_POST['checklist_option']) : 0;
			$customqr_option = (isset($_POST['customqr_option'])) ? trim($_POST['customqr_option']) : 0;

			$txtUrl = trim($_POST['txtUrl']);
			if ($txtClientname == '') :
				$errors = "Client name can't left blank..!";
			elseif ($txtAddress == '') :
				$errors = "Address can't left blank..!";
			elseif ($txtTelephone == '') :
				$errors = "Telephone can't left blank..!";
			elseif ($txtContactperson == '') :
				$errors = "Contact Person can't left blank..!";
			elseif ($txtRegidate == '') :
				$errors = "Registration Date can't left blank..!";
			elseif ($txtEmail == '') :
				$errors = "Email Address can't left blank..!";
			elseif (!preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,6})$/i", $txtEmail)) :
				$errors = 'Please enter valid email address..!';
			elseif ($txtResellerEmail != '' && !preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,6})$/i", $txtResellerEmail)) :
				$errors = 'Please enter valid reseller email address..!';
			elseif ($txtUrl == '') :
				$errors = "URL can't left blank..!";
			else :
				$email  = Model::factory('client')->getdupemail(array('email' => $txtEmail, 'mode' => 'edit', 'id' => $last_id));


				if (count($email) > 0) :
					$errors = "You entered duplicate email..!";
				else :
					$postArr['client_name'] = $txtClientname;
					$postArr['address'] = $txtAddress;
					$postArr['telephone'] = $txtTelephone;
					$postArr['contact_person'] = $txtContactperson;
					$postArr['regi_date'] = $txtRegidate;
					$postArr['email'] = $txtEmail;

					$postArr['reseller_email'] = $txtResellerEmail;
					$postuserArr = array();
					$postuserArr['email'] = $txtEmail;
					if ($txtPassword) :
						$postArr['password'] = $txtPassword;
						$postuserArr['password'] = $txtPassword;
					endif;

					$postuserArr['reseller_email'] = $txtResellerEmail;
					$postuserArr['checklist_option'] = $checklist_option;
					$postuserArr['customqr_option'] = $customqr_option;

					$postArr['url'] = $txtUrl;
					$postArr['approve'] = 1;
					$postArr['created_at'] = date('Y-m-d H:i:s');
					$postArr['is_password_change'] = 1;
					$update_id  = Model::factory('client')->editclient($last_id, $postArr, $postuserArr);

					$newpostarr['reseller_email'] = $txtResellerEmail;
					$update_id  = Model::factory('client')->editclientResellerData($last_id, $postArr, $postuserArr);
					$succmsg = 'Client updated successfully..!';
				endif;
			endif;
		}

		/**Edit User code end */


		if (isset($_POST['btnAdduser']) && $_POST['btnAdduser']) :
			//$videopermission = (isset($_POST['videopermission']) && $_POST['videopermission'] ==1) ? $_POST['videopermission']:0;
			$videopermission =  1;
			$txtClientid = $_POST['clientid'];
			$txtUseremail = trim($_POST['txtUseremail']);
			$txtUserpassword = trim($_POST['txtUserpassword']);
			if ($txtUseremail == '') :
				$errors = "Email Address can't left blank..!";
			elseif (!preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/i", $txtUseremail)) :
				$errors = 'Please enter valid email address..!';
			elseif ($txtUserpassword == '') :
				$errors = "Password can't left blank..!";
			elseif ($txtClientid == '') :
				$errors = "You must add client first..!";
			else :
				$useremail = Model::factory('client')->getuseremail($txtUseremail);
				if (count($useremail) > 0) :
					$errors = "You entered duplicate email..!";
				else :

					$clientinfodata = Model::factory('client')->getclientbyid($txtClientid);
					$clientinfodata = $clientinfodata[0];

					$postArr['reseller_email'] = $clientinfodata['reseller_email'];
					$postArr['reseller_code'] = $clientinfodata['reseller_code'];
					$postArr['client_id'] = $txtClientid;
					$postArr['email'] = $txtUseremail;
					$postArr['password'] = $txtUserpassword;
					$postArr['video_upload'] = $videopermission;
					$postArr['created_at'] = date('Y-m-d H:i:s');
					$postArr['is_sub_user'] = 1;
					$last_id1 = Model::factory('client')->adduser($postArr);
					$succmsg = 'User added successfully..!';
				endif;
			endif;
		endif;

		if (isset($_POST['btnAddResellerCode']) && $_POST['btnAddResellerCode']) :
			$reseller_errors = '';
			$client_id = trim($_POST['clientid']);
			$oldResellerCode = Model::factory('client')->getResllerCodeByClientId($client_id);
			$resellercode = trim($_POST['txtResellerCode']);
			if ($oldResellerCode == '' && $resellercode == '') :
				$reseller_errors = "Reseller code can not be blank..!";
			else :
				if ($resellercode != '') {
					if (Model::factory('client')->is_reseller_code_exist($client_id, $resellercode) == true) {
						$reseller_errors = "You entered duplicate reseller code..!";
					}
				}
			endif;

			if ($reseller_errors == '') {
				$postArr['reseller_code'] = $resellercode;
				$update_id  = Model::factory('client')->editclientResellerData($client_id, $postArr, $postArr);
				$reseller_succmsg = 'Reseller Code updated successfully..!';
			}

			$resellerCode = $resellercode;
		endif;

		//for add free codes to client start
		if (isset($_POST['btnAddFreeCode'])) :
			$add_free_code_errors = '';
			$client_id = trim($_POST['clientid']);
			$txtFreeCode = trim($_POST['txtFreeCode']);

			if ($txtFreeCode == '')
				$add_free_code_errors = "Quantity of code can not be blank..!";
			else if (!is_numeric($txtFreeCode))
				$add_free_code_errors = "Quantity of code must be a valid number..!";
			else if (is_numeric($txtFreeCode)) {
				if ($txtFreeCode == 0)
					$add_free_code_errors = "Quantity of code must be a valid number..!";
				else {
					$tmp = explode(".", $txtFreeCode);

					if (count($tmp) > 1)
						$add_free_code_errors = "Quantity of code must be a valid number..!";
				}
			}


			if ($add_free_code_errors == '') {
				$user_data = Model::factory('user')->getUserAllInfoByClientId($client_id);

				$data['no_of_codes'] = $txtFreeCode;
				$data['per_code_amount'] = 0;
				$data['email'] = $user_data[0]['email'];
				$data['town'] = $user_data[0]['town'];
				$data['first_name'] = $user_data[0]['first_name'];
				$data['last_name'] = $user_data[0]['last_name'];
				$data['company_name'] = $user_data[0]['company_name'];
				$data['billing_address'] = $user_data[0]['billing_address'];
				$data['phone'] = $user_data[0]['phone'];
				$data['postal_code'] = $user_data[0]['postal_code'];


				$user_id =  $user_data[0]['id'];

				Model::factory('codeorder')->add_free_code($client_id, $user_id, $data);

				$add_free_code_succmsg = 'Free Code(s) added successfully..!';
			}

		endif;
		//end for add free codes to client

		if (!empty($client_reseller_code)) {
			$post['client_reseller_name'] = Model::factory('client')->get_client_name_using_reseller_code($client_reseller_code);
		}


		$users = Model::factory('user')->getalluser($last_id);
		// Default Content template
		$this->template->content = View::factory('client/edit')
			->bind('post', $post)
			->bind('post1', $post1)
			->bind('last_id', $last_id)
			->bind('users', $users)
			->bind('succmsg', $succmsg)
			->bind('errors', $errors)
			->bind('resellerCode', $resellerCode)
			->bind('reseller_succmsg', $reseller_succmsg)
			->bind('reseller_errors', $reseller_errors)
			->bind('add_free_code_succmsg', $add_free_code_succmsg)
			->bind('add_free_code_errors', $add_free_code_errors);
	}


	public function action_add()
	{
		//print_r($_POST);
		$post = array();
		$post1 = array();
		if (isset($_POST['btnAddclient'])) :
			$post = $_POST;
		endif;
		if (isset($_POST['btnAdduser'])) :
			$post1 = $_POST;
		endif;
		$errors = '';
		$succmsg = '';
		$last_id = array();
		$postArr = array();
		if (isset($_POST['btnAddclient']) && $_POST['btnAddclient']) :
			$txtClientname = trim($_POST['txtClientname']);
			$txtAddress = trim($_POST['txtAddress']);
			$txtTelephone = trim($_POST['txtTelephone']);
			$txtContactperson = trim($_POST['txtContactperson']);
			$txtRegidate = trim($_POST['txtRegidate']);

			$checklist_option = (isset($_POST['checklist_option'])) ? trim($_POST['checklist_option']) : 0;
			$customqr_option = (isset($_POST['customqr_option'])) ? trim($_POST['customqr_option']) : 0;

			$txtEmail = trim($_POST['txtEmail']);
			$txtPassword = trim($_POST['txtPassword']);
			$txtUrl = trim($_POST['txtUrl']);
			if ($txtClientname == '') :
				$errors = "Client name can't left blank..!";
			elseif ($txtAddress == '') :
				$errors = "Address can't left blank..!";
			elseif ($txtTelephone == '') :
				$errors = "Telephone can't left blank..!";
			elseif ($txtContactperson == '') :
				$errors = "Contact Person can't left blank..!";
			elseif ($txtRegidate == '') :
				$errors = "Registration Date can't left blank..!";
			elseif ($txtEmail == '') :
				$errors = "Email Address can't left blank..!";
			elseif (!preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/i", $txtEmail)) :
				$errors = 'Please enter valid email address..!';
			elseif ($txtPassword == '') :
				$errors = "Password can't left blank..!";
			elseif ($txtUrl == '') :
				$errors = "URL can't left blank..!";
			else :
				$url  = Model::factory('client')->getdupurl(array('url' => $txtUrl));
				$email  = Model::factory('client')->getdupemail(array('email' => $txtEmail));
				if (count($url) > 0) :
					$errors = "You entered duplicate url..!";
				elseif (count($email) > 0) :
					$errors = "You entered duplicate email..!";
				else :
					$postArr['client_name'] = $txtClientname;
					$postArr['address'] = $txtAddress;
					$postArr['telephone'] = $txtTelephone;
					$postArr['contact_person'] = $txtContactperson;
					$postArr['regi_date'] = $txtRegidate;
					$postArr['email'] = $txtEmail;
					$postArr['password'] = ($txtPassword);
					$postArr['url'] = $txtUrl;
					$postArr['checklist_option'] = $checklist_option;
					$postArr['customqr_option'] = $customqr_option;
					$postArr['approve'] = 1;
					$postArr['created_at'] = date('Y-m-d H:i:s');
					$last_id  = Model::factory('client')->addclient($postArr);
					$succmsg = 'Client added successfully...!';
				endif;
			endif;
		endif;
		if (isset($_POST['btnAdduser']) && $_POST['btnAdduser']) :
			$videopermission = (isset($_POST['videopermission']) && $_POST['videopermission'] == 1) ? $_POST['videopermission'] : 0;
			$txtClientid = $_POST['clientid'];
			$txtUseremail = trim($_POST['txtUseremail']);
			$txtUserpassword = trim($_POST['txtUserpassword']);
			if ($txtUseremail == '') :
				$errors = "Email Address can't left blank..!";
			elseif (!preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/i", $txtUseremail)) :
				$errors = 'Please enter valid email address..!';
			elseif ($txtUserpassword == '') :
				$errors = "Password can't left blank..!";
			elseif ($txtClientid == '') :
				$errors = "You must add client first..!";
			else :
				$useremail = Model::factory('client')->getuseremail($txtUseremail);
				if (count($useremail) > 0) :
					$errors = "You entered duplicate email..!";
				else :

					$clientinfodata = Model::factory('client')->getclientbyid($txtClientid);
					$clientinfodata = $clientinfodata[0];

					$postArr['reseller_email'] = $clientinfodata['reseller_email'];
					$postArr['reseller_code'] = $clientinfodata['reseller_code'];

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

	public function action_subdivide()
	{

		$client = $this->request->post('client');
		$profiles = $this->request->post('profiles');
		$new_client = $this->request->post('new_client');

		$users = $this->request->post('users');
		$from_users = $this->request->post('from_users');
		$errors = "";

		if (!empty($profiles) && !empty($client) && !empty($new_client) && !empty($from_users)) {

			$checklist_option = Model::factory('client')->isChecklistAllowedToClient($client);
			$checklist = $checklist_option[0]['checklist_option'];
			$new_client['checklist_option'] = $checklist;

			$customqr_option = Model::factory('client')->isCustomqrEnabledToClient($client);
			$customqr = $customqr_option[0]['customqr_option'];
			$new_client['customqr_option'] = $customqr;

			$check_email_duplicate = Model::factory('client')->checkDuplicateEmail($new_client['email']);
			if (!$check_email_duplicate) {
				$users = array();
				$post = $new_client;
				$errors = "Oops... Email you provided is already used. Please try another...";
				$this->template->content = View::factory('client/subdivide_step3')
					->bind('client', $client)
					->bind('client_details', $client_details[0])
					->bind('profiles', $profiles)
					->bind('post', $post)
					->bind('errors', $errors)
					->bind('succmsg', $succmsg);
			} else {
				$new_client['created_at'] = date("Y-m-d H:i:s");

				$old_clients_conflict_profile_ids = array();

				if (!empty($users)) {
					$old_clients_conflict_profile_ids = Model::factory('profiles')->getConfictProfiles($users, $profiles, $client);
				}

				$user_id_of_old_client = Model::factory('user')->getUserIdOfClientByClientId($client);

				if (!empty($old_clients_conflict_profile_ids)) {
					Model::factory('profiles')->updateUserIdsOfConflictProfiles($old_clients_conflict_profile_ids, $user_id_of_old_client[0]['id']);
				}

				$new_client_id  = Model::factory('client')->addclient($new_client);
				$user_id_of_new_client = Model::factory('user')->getUserIdOfClientByClientId($new_client_id[0]);
				if (!empty($users)) {
					$new_clients_conflict_profile_ids = Model::factory('profiles')->getConfictProfilesOfNewClient($users, $profiles, $client);

					Model::factory('user')->updateClientIdOfTransferredUsers($users, $new_client_id[0]);
				}

				if (!empty($profiles)) {
					Model::factory('profiles')->updateClientIdsOfTransferredProfiles($profiles, $new_client_id[0]);
				}
				if (!empty($new_clients_conflict_profile_ids)) {
					Model::factory('profiles')->updateUserIdsOfConflictProfilesOfNewClient($new_clients_conflict_profile_ids, $user_id_of_new_client[0]['id']);
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
		} else if (!empty($profiles) && !empty($client) && !empty($new_client)) {
			foreach ($new_client as $key => $value) {
				$new_client[$key] = addslashes(trim($value));
			}

			$check_email_format = preg_match("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$^", $new_client['email']);
			$post = array();
			$check_email_duplicate = Model::factory('client')->checkDuplicateEmail($new_client['email']);
			$client_details =  Model::factory('client')->getclientbyid($client);

			if (empty($new_client['client_name']) || empty($new_client['address']) || empty($new_client['telephone']) || empty($new_client['contact_person']) || empty($new_client['regi_date']) || empty($new_client['email']) || empty($new_client['password']) || empty($new_client['url'])) {
				$errors = "All fields are required...";
				$post = $new_client;
			}

			if (!$check_email_format) {
				$errors = "Email must be in proper format...";
				$post = $new_client;
			}

			if (!$check_email_duplicate) {
				$errors = "Oops... Email you provided is already used. Please try another...";
				$post = $new_client;
			}

			if (!empty($errors)) {
				$this->template->content = View::factory('client/subdivide_step3')
					->bind('client', $client)
					->bind('client_details', $client_details[0])
					->bind('profiles', $profiles)
					->bind('post', $post)
					->bind('errors', $errors)
					->bind('succmsg', $succmsg);
			} else {

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
		} else  if (!empty($profiles) && !empty($client)) {
			$client_details =  Model::factory('client')->getclientbyid($client);
			$post = array();

			$succmsg = "<strong><i>" . count($profiles) . "</i></strong> profiles in Client <strong><i>" . $client_details[0]['client_name'] . "</i></strong> has been selected...";

			$this->template->content = View::factory('client/subdivide_step3')
				->bind('client', $client)
				->bind('client_details', $client_details[0])
				->bind('profiles', $profiles)
				->bind('post', $post)
				->bind('errors', $errors)
				->bind('succmsg', $succmsg);
		} else if (!empty($client)) {
			$client_details =  Model::factory('client')->getclientbyid($client);
			$profiles  = Model::factory('profiles')->getProfilesByClientId($client);
			$succmsg = "Client <strong><i>" . $client_details[0]['client_name'] . "</i></strong> is selected...";

			$this->template->content = View::factory('client/subdivide_step2')
				->bind('client', $client)
				->bind('profiles', $profiles)
				->bind('errors', $errors)
				->bind('succmsg', $succmsg);
		} else {
			$clients  = Model::factory('client')->getallclient();
			$this->template->content = View::factory('client/subdivide_step1')
				->bind('clients', $clients)
				->bind('errors', $errors)
				->bind('succmsg', $succmsg);
		}
	}
} // End Welcome
