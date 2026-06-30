<?php

defined('SYSPATH') or die('No direct script access.');

/* * **
 * **** User Profile edit
 * ** */

class Controller_profile extends Controller_Template_Master
{
	/*     * * 
     * *** Page header and footer set
     * ** */

	public function before()
	{
		/**  parent before	* */
		parent::before();

		if (!$this->session->get('clientuserid')) :
			$this->request->redirect(URL::base() . 'welcome/index');
		endif;

		// Default Head view file
		$this->template->head = View::factory('template/head');

		// Set the default Page Title
		$this->template->head->title = 'Edit your profile with ScanLink';

		// All CSS Stylesheets
		$this->template->head->stylesheets = array(array('href' => 'styles/style.css', 'media' => 'screen'), array('href' => 'styles/coin-slider-styles.css', 'media' => 'screen'), array('href' => 'styles/colorbox.css', 'media' => 'screen'), array('href' => 'styles/prettyPhoto.css', 'media' => 'screen'));

		// All JS Libraries and Scripts
		$this->template->head->javascripts = array('js/jquery.min.js', 'js/coin-slider.min.js', 'js/jquery.validate.min.js', 'js/jquery.prettyPhoto.js', 'js/jquery.colorbox.js', 'js/jquery.uniform.js', 'js/custome.js', 'js/modernizr.custom.60079.js');
		$this->template->head->extrascripts = 'slider';

		// Default Header view file
		$this->template->headers = View::factory('template/headers');

		// Default menu
		$this->template->headers->menu = View::factory('template/menu');

		if ($this->session->get('clientuserid')) :
			$this->template->headers->menu->logoutlnk = $this->session->get('clientusername');
		endif;
		$this->template->headers->menu->menu = $this->menu;
		$this->template->headers->menu->menuSubmenu = $this->menuSubmenu;

		$this->template->headers->menu->currentactive = $this->request->controller() . "/" . $this->request->action();
		// Default Content template
		$this->template->footer = View::factory('template/footer');
		//endif;
		if ($this->template->footer) :
			// All JS Libraries and Scripts
			$this->template->footer->scripts = array();
		endif;
	}

	/*     * ** 
     * **** edit user profile
     * ** */

	public function action_index()
	{
		// Set the Page Title
		$this->template->head->title = 'Profile Edit';

		// after validation value to be return 
		$fields = array();
		// after validation error to be  return 
		$errors = array();
		$clientfields = array();

		/**show profile code to the sub user */
		$profile_code_by_client_id = '';

		/*         * *** validation on submit  button *** */

		$userdata = Model::factory('user')->getUserByID($this->session->get('clientuserid'));


		$fields['first_name'] = $userdata['first_name'];
		$fields['last_name'] = $userdata['last_name'];
		$fields['company_name'] = $userdata['company_name'];
		$fields['billing_address'] = $userdata['billing_address'];
		$fields['email'] = $userdata['email'];
		$fields['town'] = $userdata['town'];
		$fields['phone'] = $userdata['phone'];

		$fields['postal_code'] = $userdata['postal_code'];
		$fields['is_sub_user'] = $userdata['is_sub_user'];
		$fields['footer_logo'] = $userdata['footer_logo'];

		$clientdata = Model::factory('client')->getClientById($this->session->get('client_id'));

		$clientfields['shortcut_title'] = $clientdata['shortcut_title'];
		$clientfields['shortcut_image1'] = $clientdata['shortcut_image1'];
		$clientfields['shortcut_image2'] = $clientdata['shortcut_image2'];


		if ($this->request->method() === Request::POST) {

			if (isset($_POST['editprofile']) && $_POST['editprofile'] == 'yes') {

				/*                 * *** get post variable *** */
				$first_name = trim($_POST['first_name']);
				$last_name = trim($_POST['last_name']);
				$company_name = trim($_POST['company_name']);
				$billing_address = trim($_POST['billing_address']);
				$email = trim($_POST['email']);
				$town = trim($_POST['town']);
				$phone = trim($_POST['phone']);
				$shortcut_title = trim($_POST['shortcut_title']);
				$shortcut_image1 = $_FILES['shortcut_image1']['name'];
				$shortcut_image2 = $_FILES['shortcut_image2']['name'];

				$postal_code = trim($_POST['postal_code']);

				/*                 * *** Validation START  *** */
				if ($first_name == '') {
					$errors['first_name'] = "Enter a first name.";
				}

				if ($last_name == '') {
					$errors['last_name'] = "Enter a last name.";
				}

				if ($company_name == '') {
					$errors['company_name'] = "Enter a company name.";
				}

				if ($billing_address == '') {
					$errors['billing_address'] = "Enter a billing address.";
				}

				if ($email == '') {
					$errors['email'] = 'Enter a email.';
				} else {
					$emailexist = Model::factory('user')->getIdByEmail($email);
					if (!empty($emailexist)) {
						foreach ($emailexist as $key => $data) {
							if ($emailexist[$key]['id'] != $this->session->get('clientuserid')) {
								$errors['email'] = 'This email is already exists.';
							}
						}
					}
				}

				if ($town == '') {
					$errors['town'] = "Enter a town.";
				}

				if ($phone == '') {
					$errors['phone'] = "Enter a phone.";
				}

				if ($postal_code == '') {
					$errors['postal_code'] = "Enter a postal code.";
				}

				/***** Validation END  *** */

				/***assign field variable ** */
				$fields['first_name'] = $first_name;
				$fields['last_name'] = $last_name;
				$fields['company_name'] = $company_name;
				$fields['billing_address'] = $billing_address;
				$fields['email'] = $email;
				$fields['town'] = $town;
				$fields['phone'] = $phone;
				$fields['is_password_change'] = 1;
				$fields['show_code_profile_id_to_acc_user'] =  $profile_code_by_client_id;
				$clientfields['shortcut_title'] = $shortcut_title;
				$clientfields['is_password_change'] = 1;

				if (isset($shortcut_image1) && $shortcut_image1 != "") {
					$temp1 = explode(".", $shortcut_image1);
					$newfilename1 = $this->session->get('client_id') . '_180x180' . '.' . end($temp1);
					$clientfields['shortcut_image1'] = $newfilename1;
				}
				if (isset($shortcut_image2) && $shortcut_image2 != "") {
					$temp2 = explode(".", $shortcut_image2);
					$newfilename2 = $this->session->get('client_id') . '_192x192' . '.' . end($temp2);
					$clientfields['shortcut_image2'] = $newfilename2;
				}

				$fields['postal_code'] = $postal_code;
				$fields['updated_at'] = date("Y-m-d H:i:s");
				/*                 * * if sucessfully complete the profile edit option update the profile information ** */
				if (empty($errors)) {

					if (isset($shortcut_image1) && $shortcut_image1 != "") {
						// Save Shortcut Image 1
						move_uploaded_file($_FILES["shortcut_image1"]["tmp_name"], "images/touch_icons/" . $newfilename1);
					}
					if (isset($shortcut_image2) && $shortcut_image2 != "") {
						// Save Shortcut Image 2
						move_uploaded_file($_FILES["shortcut_image2"]["tmp_name"], "images/touch_icons/" . $newfilename2);
					}
					Model::factory('client')->updateclient($clientfields, $this->session->get('client_id'));

					Model::factory('user')->updateuser($fields, $this->session->get('clientuserid'));
					if ($userdata['role'] == 5) {
						$newfeild = array();
						$newfeild['client_name'] = $first_name . " " . $last_name;
						$newfeild['address'] = $billing_address;
						$newfeild['telephone'] = $phone;
						$newfeild['contact_person'] = $first_name;
						$newfeild['email'] = $email;
						$newfeild['updated_at'] = date("Y-m-d H:i:s");
						Model::factory('client')->updateclient($newfeild, $userdata['client_id']);
					}

					if ($this->session->get('logo_for_footer_temp') != "") {
						if ($fields["footer_logo"] != "" && file_exists("images/logo/" . $fields["footer_logo"]))
							unlink("images/logo/" . $fields["footer_logo"]);

						$fields2['footer_logo']  = $this->session->get('logo_for_footer_temp');
						Model::factory('user')->updateuser($fields2, $this->session->get('clientuserid'));
						$this->session->delete('logo_for_footer_temp');
					}

					$this->session->set('successmessage', "Your profile has been updated successfully.");
					$this->session->set('clientusername', $email);
					$this->request->redirect('profile/index/');
					exit();
				}
			} else if (isset($_POST['editprofile']) && $_POST['editprofile'] == 'no') {

				$txtpass = addslashes(trim($_POST['txtpass']));

				$password_check = $txtpass;
				if (empty($userdata['is_password_change'])) {
					$password_check = md5($txtpass);
				}

				$txtnewpass = addslashes(trim($_POST['txtnewpass']));
				$txtconfirmpass = addslashes(trim($_POST['txtconfirmpass']));
				if ($txtpass == '') :
					$errors['txtpass'] = "Enter a old password.";
				elseif ($userdata['password'] != $password_check) :
					$errors['txtpass'] = "Old password does not match with current password.";
				elseif ($txtnewpass == '') :
					$errors['txtnewpass'] = "Enter a new password.";
				elseif ((strlen($txtnewpass) < 5) || (strlen($txtnewpass) > 12)) :
					$errors['txtnewpass'] = "Password must between 5-12 character..!";
				elseif ($txtconfirmpass == '') :
					$errors['txtconfirmpass'] = "Enter a confirm new password.";
				elseif ($txtnewpass !== $txtconfirmpass) :
					$errors['txtconfirmpass'] = "Confirm Password must be same as New Password..!";
				else :


					$changedpass = Model::factory('user')->editpass($this->session->get('clientuserid'), array('password' => $txtnewpass));


					if ($userdata['role'] == 5) {
						Model::factory('client')->editpass($userdata['client_id'], array('password' => ($txtnewpass)));
					}
					$this->session->set('pwdsuccessmessage', "Your Password has been changed successfully.");
				endif;
			}
			//edit profile message end

			//add new user
			if (isset($_POST['add_new_user']) && $_POST['add_new_user'] == 'yes') {
				$txtemail = addslashes(trim($_POST['txtemail']));
				$txtpass1 = addslashes(trim($_POST['txtpass1']));
				$txt_clientid = $this->session->get('client_id');

				$profile_code_by_client_id = "";


				if (!empty($_POST['enable_admin_access']) && $_POST['enable_admin_access'] == 1) {

					$profile_code_by_client_id = Model::factory('profiles')->getProfileByClientId($this->session->get('client_id'));

					$postArr['access_addcode'] = 1;
					$postArr['access_edit'] = 1;
					$postArr['access_analytics'] = 1;
					$postArr['access_form_submission'] = 1;
					$postArr['access_download'] = 1;
					$postArr['access_log'] = 1;
					$postArr['access_label'] = 1;
					$postArr['access_delete'] = 1;
					$postArr['show_code_profile_id_to_acc_user'] = $profile_code_by_client_id;
					$postArr['enable_admin_access'] = 1;

					$postArr['is_sub_user'] = 0;
					$postArr['is_password_change'] = 1;
				} else {
					$postArr['enable_admin_access'] = 0;
					$postArr['is_sub_user'] = 1;
					$postArr['is_password_change'] = 1;
				}


				if ($txtemail == '') :
					$errors['txtemail'] = "Email Address can't left blank..!";

				elseif ($txtpass1 == '') :
					$errors['txtpass1'] = "Password can't left blank..!";
				else :
					$useremail =  Model::factory('user')->getIdByEmail($txtemail);
					if (count($useremail) > 0) :
						$errors['txtemail'] = "This email is already exists..!";
						$fields['txtemail'] = $txtemail;
					else :
						$clientinfodata = Model::factory('client')->getClientById($txt_clientid);
						$postArr['reseller_email'] = $clientinfodata['reseller_email'];
						$postArr['reseller_code'] = $clientinfodata['reseller_code'];
						$postArr['client_id'] = $txt_clientid;
						$postArr['email'] = $txtemail;
						$postArr['password'] = $txtpass1;


						$postArr['created_at'] = date('Y-m-d H:i:s');
						$last_id = Model::factory('client')->adduser($postArr);

						$this->session->set('successmessage', "User added successfully");
					endif;
				endif;
			}
		}

		$sessionmsg = $this->session->get('successmessage');
		$this->session->delete('successmessage');

		//password  message		
		$pwdsessionmsg = $this->session->get('pwdsuccessmessage');
		$this->session->delete('pwdsuccessmessage');

		$this->session->delete('logo_for_footer_temp');

		$client_id = $this->session->get('clientuserid');
		$user_data = Model::factory('user')->getUserDataByClientId($client_id);

		$users = Model::factory('user')->getalluser_by_client_id($this->session->get('client_id'), $user_data);

		// Default Content template
		$this->template->content = View::factory('profile/index')
			->bind('errors', $errors)
			->bind('fields', $fields)
			->bind('clientfields', $clientfields)
			->bind('users', $users)
			->bind('sessionmsg', $sessionmsg)
			->bind('pwdsuccessmessage', $pwdsessionmsg);
	}

	public function action_delete_user()
	{
		$user_id = $this->request->param('id');


		if (isset($user_id) && $user_id) :
			Model::factory('user')->deleteuser($user_id);
			$this->session->set('successmessage', "User deleted successfully");
		endif;

		$this->request->redirect('profile');
	}

	public function action_block_user()
	{
		$user_id = $this->request->param('id');


		if (isset($user_id) && $user_id) :
			Model::factory('user')->blockuser($user_id);
			$this->session->set('successmessage', "User status updated successfully");
		endif;

		$this->request->redirect('profile');
	}

	public function action_edit_user()
	{
		$user_id = $this->request->param('id');

		$this->template->head = View::factory('template/form_builder_head');

		// Set the default Page Title
		$this->template->head->title = 'Edit User';

		$this->template->headers = "";
		$this->template->footer = "";

		// All CSS Stylesheets
		$this->template->head->stylesheets = array(array('href' => 'styles/style_user.css', 'media' => 'screen'), array('href' => 'form-builder/css/uniform.default.css', 'media' => 'screen'));

		// All JS Libraries and Scripts
		$this->template->head->javascripts = array('form-builder/js/jquery-1.9.1.js', 'form-builder/js/jquery-ui.js', 'form-builder/js/jquery.uniform.js');

		$user_data = Model::factory('user')->getUserByID($user_id);

		if (isset($_POST["txtUseremail"])) {
			$email = $_POST["txtUseremail"];
			$password = $_POST["txtUserpassword"];

			$access_addcode = isset($_POST["access_addcode"]) ? '1' : '0';
			$access_edit = isset($_POST["access_edit"]) ? '1' : '0';
			$access_analytics = isset($_POST["access_analytics"]) ? '1' : '0';
			$access_form_submission = isset($_POST["access_form_submission"]) ? '1' : '0';
			$access_download = isset($_POST["access_download"]) ? '1' : '0';
			$access_label = isset($_POST["access_label"]) ? '1' : '0';
			$access_log = isset($_POST["access_log"]) ? '1' : '0';
			$access_delete = isset($_POST["access_delete"]) ? '1' : '0';

			$user_arr['email'] = $email;
			if ($user_data["password"] != $password) {
				$user_arr['password'] = $password;
			}

			$profile_code_by_client_id = "";

			if (!empty($_POST['enable_admin_access']) && $_POST['enable_admin_access'] == 1) {
				$profile_code_by_client_id = Model::factory('profiles')->getProfileByClientId($this->session->get('client_id'));

				$user_arr['access_addcode'] = 1;
				$user_arr['access_edit'] = 1;
				$user_arr['access_analytics'] = 1;
				$user_arr['access_form_submission'] = 1;
				$user_arr['access_download'] = 1;
				$user_arr['access_log'] = 1;
				$user_arr['access_label'] = 1;
				$user_arr['access_delete'] = 1;
				$user_arr['show_code_profile_id_to_acc_user'] = $profile_code_by_client_id;
				$user_arr['enable_admin_access'] = 1;
				$user_arr['is_sub_user'] = 0;
			} else {

				$selected_code_profile_id = "";
				if (!empty($_POST["code_profile_id"])) {
					$selected_code_profile_id = implode(",", $_POST["code_profile_id"]);
				}

				$user_arr['access_addcode'] = $access_addcode;
				$user_arr['access_edit'] 	= $access_edit;
				$user_arr['access_analytics'] 	= $access_analytics;
				$user_arr['access_form_submission'] 	= $access_form_submission;
				$user_arr['access_download'] 	= $access_download;
				$user_arr['access_label'] 	= $access_label;
				$user_arr['access_log'] 	= $access_log;
				$user_arr['access_delete'] 	= 0;
				// $user_arr['access_delete'] 	= $access_delete;
				$user_arr['show_code_profile_id_to_acc_user'] 	= $selected_code_profile_id;
				$user_arr['enable_admin_access'] = 0;
				$user_arr['is_sub_user'] = 1;
			}

			$updateuserid = Model::factory('user')->updateuser($user_arr, $user_id);

			//get latest data of user
			$user_data = Model::factory('user')->getUserByID($user_id);

			$successmsg = 'User details updated successfully..!';
		}



		$this->template->content = View::factory('profile/edit_user')
			->bind('user_data', $user_data)
			->bind('user_id', $user_id)
			->bind('successmsg', $successmsg)
			->bind('errmsg', $errmsg);
	}

	/*
     * Action Name :        purchase
     * Date :               09-july-2013
     * Author :             Ajay Makwana <ajay@webplanex.net>
     * Usage :              To purchase new codes
     */
	public function action_purchase()
	{
		//check whether current user is subuser then redirect
		if ($this->session->get('is_sub_user') == "1") {
			$this->request->redirect('dashboard');
		}

		// Set the Page Title
		$this->template->head->title = 'Purchase new codes';

		// get code balance
		//echo $this->session->get('clientuserid');
		$pending_codes = Model::factory('code')->getPendingCodes($this->session->get('client_id'));

		//$resller_codes = Model::factory('resellerpricing')->getAllResellerCode();

		$this->template->content = View::factory('profile/purchase')
			->bind('pending_codes', $pending_codes['0']['pending_codes']);
	}


	/*
     * Action Name :        purchase
     * Date :               09-july-2013
     * Author :             megha shah 
     * Usage :              To purchase new codes
     */
	public function action_codeBalance()
	{

		//check whether current user is subuser then redirect
		if ($this->session->get('is_sub_user') == "1") {
			$this->request->redirect('dashboard');
		}

		// Set the Page Title
		$this->template->head->title = 'View Code Balance';
		// get code balance
		//echo $this->session->get('clientuserid');
		$pending_codes = Model::factory('code')->getPendingCodes($this->session->get('client_id'));

		$client = Model::factory('user')->getClientById($this->session->get('clientuserid'));

		$this->template->content = View::factory('profile/code_balance')
			->bind('pending_codes', $pending_codes['0']['pending_codes'])
			->bind('client_id', $client['id']);

		$this->template->content->expired_popup = View::factory('template/expried');
	}

	public function action_billing()
	{

		// check a card is empty or not 

		if ($this->session->get('code_amount') == '0' || $this->session->get('code_amount') == '') {
			$this->session->set('errormessage', "You can not put order without purchasing code.");
			//redirect to dashboard code page
			$this->request->redirect('dashboard/mastercode');
			exit();
		}
		// get code balance
		$this->template->head->title = 'Billing Information';
		$userdata = Model::factory('user')->getUserByID($this->session->get('clientuserid'));
		$fields['first_name'] = $userdata['first_name'];
		$fields['last_name'] = $userdata['last_name'];
		$fields['company_name'] = $userdata['company_name'];
		$fields['billing_address'] = $userdata['billing_address'];
		$fields['email'] = $userdata['email'];
		$fields['town'] = $userdata['town'];
		$fields['phone'] = $userdata['phone'];
		$fields['postal_code'] = $userdata['postal_code'];
		if ($this->request->method() === Request::POST) {
			/*** if sucessfully complete the profile edit option update the profile information ** */
			if (empty($errors) && ($this->session->get('code_amount') != '0' && $this->session->get('code_amount') != '')) {
				$client = Model::factory('user')->getUserByID($this->session->get('clientuserid'));

				$client_reseller_name = ""; //use in admin mail
				if ($client["client_reseller_code"] != "") {
					$client_reseller_name = Model::factory('client')->get_client_name_using_reseller_code($client["client_reseller_code"]);
				}


				$registerdata = $this->session->get('fieldsession');
				$registerdata['no_of_codes'] = $this->session->get('code_amount');
				$registerdata['per_code_amount'] = $this->session->get('per_code_amount');
				$reseller_code_client = $this->session->get('reseller_client_id', false);

				$reseller_session_set = $this->session->get('reseller_data', false);
				if ($reseller_session_set == 'true') {


					if ($reseller_code_client != '') {
						$registerdata['is_reseller_pricing_code'] = '1';
						$registerdata['reseller_client_id'] = $reseller_code_client;
					} else {
						$registerdata['is_reseller_pricing_code'] = '0';
						$registerdata['reseller_client_id'] = '';
					}
				} else {
					$registerdata['is_reseller_pricing_code'] = '0';
					$registerdata['reseller_client_id'] = '';
				}



				Model::factory('codepurchase')->insertcodepurchase($client['client_id'], $client['id'], $registerdata);

				$datafill = array();
				$datafill['pop_up_purchase'] = '1';
				Model::factory('user')->updateuser($datafill, $client['id']);

				// send mail  START
				include_once("mailsend.php");
				$adminemail = Model::factory('settings')->getValueByTitle('contact_email');
				$adminemail = "admin@scanlink.net.au";
				$baseurl = URL::base();

				if ($reseller_session_set == 'true') {
					$resellerData = Model::factory('client')->getClientById($reseller_code_client);
					$registerdata['user_email'] = $registerdata['email'];
					$registerdata['email'] = $resellerData['reseller_email'];

					$clientMailData = $registerdata;
					$clientMailData['name'] = $resellerData['client_name'];

					//send mail to reseller START 
					resellerCode_mail_client($adminemail, $baseurl, $clientMailData);
					//send mail to user END

					unset($clientMailData);
					$clientMailData = $registerdata;
					$clientMailData['name'] = $resellerData['client_name'];
					$namesplit = explode(" ", $resellerData['client_name']);
					$clientMailData['first_name'] = $namesplit[0];
					$clientMailData['last_name'] = isset($namesplit[1]) ? $namesplit[1] : $namesplit[0];

					//send mail to admin START 
					resellerCode_mail_admin($adminemail, $baseurl, $clientMailData);
					//send mail to admin END


				} else {
					//send mail to user START 
					purchaseCode_mail_client($adminemail, $baseurl, $registerdata);
					//send mail to user END

					//send mail to admin START 
					purchaseCode_mail_admin($adminemail, $baseurl, $registerdata, $client_reseller_name);
					//send mail to admin END
				}
				// send mail  END


				//$success = "" . $this->session->get('code_amount') . " code has been added to your profile, Our team will send you an invoice in 14 days";
				//$this->session->set('SuccessMessage', $success);
				$this->session->delete('code_amount');
				$this->session->delete('fieldsession');
				$this->session->delete('per_code_amount');
				$this->session->delete('reseller_data');
				$this->request->redirect('dashboard/mastercode');
			} else {
				$this->session->set('errormessage', "You can not put order without purchasing code.");
				//redirect to dashboard code page
				$this->request->redirect('dashboard/mastercode');
				exit();
			}
		}

		$sessionmsg = $this->session->get('successmessage');
		$this->session->delete('successmessage');
		// Default Content template
		$this->template->content = View::factory('profile/billing')
			->bind('errors', $errors)
			->bind('fields', $fields)
			->bind('sessionmsg', $sessionmsg);
	}

	public function action_billing_for_formbuilder()
	{
		$referer = $this->session->get('form_builder_referer');
		// check a card is empty or not 

		if ($this->session->get('code_amount') == '0' || $this->session->get('code_amount') == '') {
			$this->session->set('errormessage', "You can not put order without amount.");
			//redirect to dashboard code page
			$this->request->redirect($referer);
			exit();
		}
		// get code balance
		$this->template->head->title = 'Billing Information';
		$userdata = Model::factory('user')->getUserByID($this->session->get('clientuserid'));
		$fields['first_name'] = $userdata['first_name'];
		$fields['last_name'] = $userdata['last_name'];
		$fields['company_name'] = $userdata['company_name'];
		$fields['billing_address'] = $userdata['billing_address'];
		$fields['email'] = $userdata['email'];
		$fields['town'] = $userdata['town'];
		$fields['phone'] = $userdata['phone'];
		$fields['postal_code'] = $userdata['postal_code'];

		if ($this->request->method() === Request::POST) {

			if (empty($errors) && ($this->session->get('code_amount') != '0' && $this->session->get('code_amount') != '')) {

				$client = Model::factory('user')->getUserByID($this->session->get('clientuserid'));
				$registerdata = $this->session->get('fieldsession');
				$registerdata['no_of_codes'] = $this->session->get('code_amount');
				$registerdata['per_code_amount'] = $this->session->get('per_code_amount');
				$reseller_code_client = $this->session->get('reseller_client_id', false);

				$reseller_session_set = $this->session->get('reseller_data', false);
				if ($reseller_session_set == 'true') {


					if ($reseller_code_client != '') {
						$registerdata['is_reseller_pricing_code'] = '1';
						$registerdata['reseller_client_id'] = $reseller_code_client;
					} else {
						$registerdata['is_reseller_pricing_code'] = '0';
						$registerdata['reseller_client_id'] = '';
					}
				} else {
					$registerdata['is_reseller_pricing_code'] = '0';
					$registerdata['reseller_client_id'] = '';
				}


				$order_id = Model::factory('formbuilderorders')->insert_form_builder_orders($client['client_id'], $client['id'], $registerdata, $this->session->get('form_builder_profile_id'));



				// send mail  START
				include_once("mailsend.php");
				$adminemail = Model::factory('settings')->getValueByTitle('contact_email');
				$adminemail = "admin@scanlink.net.au";
				$baseurl = URL::base();

				if ($reseller_session_set == 'true') {
					$resellerData = Model::factory('client')->getClientById($reseller_code_client);
					$registerdata['user_email'] = $registerdata['email'];
					$registerdata['email'] = $resellerData['reseller_email'];

					$clientMailData = $registerdata;
					$clientMailData['name'] = $resellerData['client_name'];

					//send mail to reseller START 
					reseller_mail_client_for_formbuilder($adminemail, $baseurl, $clientMailData, $order_id);
					//send mail to user END

					unset($clientMailData);
					$clientMailData = $registerdata;
					$clientMailData['name'] = $resellerData['client_name'];
					$namesplit = explode(" ", $resellerData['client_name']);
					$clientMailData['first_name'] = $namesplit[0];
					$clientMailData['last_name'] = isset($namesplit[1]) ? $namesplit[1] : $namesplit[0];

					//send mail to admin START 
					reseller_mail_admin_for_formbuilder($adminemail, $baseurl, $clientMailData, $order_id);
					//send mail to admin END


				} else {
					//send mail to user START 
					client_mail_for_formbuilder($adminemail, $baseurl, $registerdata, $order_id);
					//send mail to user END

					//send mail to admin START 
					admin_mail_for_formbuilder($adminemail, $baseurl, $registerdata, $order_id);
					//send mail to admin END
				}




				$this->session->delete('code_amount');
				$this->session->delete('fieldsession');
				$this->session->delete('per_code_amount');
				$this->session->delete('reseller_data');
				$this->session->delete('form_builder_profile_id');

				$this->request->redirect($referer);
			} else {
				$this->session->set('errormessage', "You can not put order without amoount.");
				//redirect to dashboard code page
				$this->request->redirect($referer);
				exit();
			}
		}

		$sessionmsg = $this->session->get('successmessage');
		$this->session->delete('successmessage');
		// Default Content template
		$this->template->content = View::factory('profile/billing_for_formbuilder')
			->bind('errors', $errors)
			->bind('fields', $fields)
			->bind('sessionmsg', $sessionmsg);
	}

	public function action_ordersummary()
	{

		// check a card is empty or not 
		if ($this->session->get('code_amount') == '0' || $this->session->get('code_amount') == '') {
			$this->session->set('errormessage', "You can not put order without purchasing code.");
			//redirect to dashboard code page
			$this->request->redirect('dashboard/mastercode');
			exit();
		}

		// Set the Page Title
		$this->template->head->title = 'Purchase Code Order Summary';

		if ($this->request->method() === Request::POST) {
			/*             * *** get post variable *** */
			$first_name = trim($_POST['first_name']);
			$last_name = trim($_POST['last_name']);
			$company_name = trim($_POST['company_name']);
			$billing_address = trim($_POST['billing_address']);
			$email = trim($_POST['email']);
			$town = trim($_POST['town']);
			$phone = trim($_POST['phone']);
			$postal_code = trim($_POST['postal_code']);

			/*** assign field variable ***/
			$fields['first_name'] = $first_name;
			$fields['last_name'] = $last_name;
			$fields['company_name'] = $company_name;
			$fields['billing_address'] = $billing_address;
			$fields['email'] = $email;
			$fields['town'] = $town;
			$fields['phone'] = $phone;
			$fields['postal_code'] = $postal_code;
			$fields['updated_at'] = date("Y-m-d H:i:s");
			/*** if sucessfully complete the profile edit option update the profile information ***/
			if (empty($errors)) {
				$client = Model::factory('user')->getUserByID($this->session->get('clientuserid'));
				$this->session->set('fieldsession', $fields);
				$registerdata['no_of_codes'] = $this->session->get('code_amount');
				$registerdata['per_code_amount'] = $this->session->get('per_code_amount');
			}
		}
		$this->template->content = View::factory('profile/ordersummary')
			->bind('nocode', $registerdata['no_of_codes'])
			->bind('totalsubscription', $registerdata['per_code_amount']);
	}

	public function action_ordersummary_formbuilder()
	{

		// check a card is empty or not 
		if ($this->session->get('code_amount') == '0' || $this->session->get('code_amount') == '') {
			$this->session->set('errormessage', "You can not put order without purchasing code.");
			//redirect to dashboard code page
			$this->request->redirect('dashboard/mastercode');
			exit();
		}

		// Set the Page Title
		$this->template->head->title = 'Form Builder Order Summary';

		if ($this->request->method() === Request::POST) {
			/*             * *** get post variable *** */
			$first_name = trim($_POST['first_name']);
			$last_name = trim($_POST['last_name']);
			$company_name = trim($_POST['company_name']);
			$billing_address = trim($_POST['billing_address']);
			$email = trim($_POST['email']);
			$town = trim($_POST['town']);
			$phone = trim($_POST['phone']);
			$postal_code = trim($_POST['postal_code']);

			/*** assign field variable ***/
			$fields['first_name'] = $first_name;
			$fields['last_name'] = $last_name;
			$fields['company_name'] = $company_name;
			$fields['billing_address'] = $billing_address;
			$fields['email'] = $email;
			$fields['town'] = $town;
			$fields['phone'] = $phone;
			$fields['postal_code'] = $postal_code;
			$fields['updated_at'] = date("Y-m-d H:i:s");
			/*** if sucessfully complete the profile edit option update the profile information ***/
			if (empty($errors)) {
				$client = Model::factory('user')->getUserByID($this->session->get('clientuserid'));
				$this->session->set('fieldsession', $fields);
				$registerdata['no_of_codes'] = $this->session->get('code_amount');
				$registerdata['per_code_amount'] = $this->session->get('per_code_amount');
			}
		}
		$this->template->content = View::factory('profile/ordersummary_formbuilder')
			->bind('totalsubscription', $registerdata['per_code_amount']);
	}

	public function action_codepurchase()
	{
		// get code balance
		//echo $this->session->get('clientuserid');
		//echo $this->session->get('code_amount'); exit;    
	}

	public function action_getResellerdata()
	{

		$this->session->delete('reseller_data');
		$this->session->delete('code_amount');
		$this->session->delete('per_code_amount');

		$errors = array();
		$registerdata = $this->session->get('registerdata');

		$reseller_code = isset($_POST['reseller_code']) ? $_POST['reseller_code'] : '';
		$noorder = isset($_POST['no_codes']) ? $_POST['no_codes'] : '';
		$submit_button_press = isset($_POST['submit_button_press']) ? $_POST['submit_button_press'] : false;

		$permonthamount = 0.00;
		$totalsubscrption = 0.00;
		$reseller_margin = 0.00;

		$reseller_code_client = Model::factory('client')->isResellerCodeExit($reseller_code);



		if ($reseller_code == '') {
			$errors[] = 'Enter a reseller code.';
		} else if ($reseller_code_client == '') {
			$errors[] = 'Enter a valid reseller code.';
		} else if ($noorder == '') {
			$errors['no_codes'] = 'Enter a number of code required.';
		} else if ($noorder > 1000) {
			$errors[] = 'Enter a number of code less than 1000.';
		} else {

			$codeamount = Model::factory('codeprising')->getAmountByQuantity($noorder);
			$permonthamount = number_format($codeamount[0]['reseller_amount'], 2);
			$totalsubscrption = number_format($codeamount[0]['reseller_amount'] * $noorder * 12, 2);
			$reseller_margin = number_format((($codeamount[0]['amount'] - $codeamount[0]['reseller_amount']) * $noorder * 12), 2);

			if ($submit_button_press) {
				$registerdata['noorder'] = $noorder;
				$registerdata['amount'] = $permonthamount;
				$registerdata['totalsubscrption'] = $totalsubscrption;
				$registerdata['reseller_data'] = "true";
				$registerdata['reseller_client_id'] = $reseller_code_client;
				$this->session->set('registerdata', $registerdata);
				$this->session->set('reseller_data', "true");
				$this->session->set('reseller_client_id', $reseller_code_client);
			}
		}



		$returnarray = array();
		$returnarray['errors'] = $errors;
		$returnarray['amount'] = "$permonthamount";
		$returnarray['totalsubscrption'] = "$totalsubscrption";
		$returnarray['reseller_margin'] = "$reseller_margin";

		// store codes amount into session
		$this->session->set('code_amount', $_POST['no_codes']);
		$this->session->set('per_code_amount', $permonthamount);

		echo json_encode($returnarray);
		exit();
	}

	function action_delete_temp_files()
	{
		/// remove logo, documents, pictures which temp uploaded 

		if (isset($_REQUEST['profile_id'])) {
			$profile_id = $_REQUEST['profile_id'];
			$redirect_url = $_REQUEST['redirect_url'] . $profile_id;

			//delete logo file
			$logo = DB::select('*')->from('logo')
				->where('logo.profile_id', '=', $profile_id)
				->where('is_temp', '=', 1)
				->execute()
				->as_array();
			foreach ($logo as $profile_logo) {
				unlink($profile_logo['logo_name']);
			}


			//delete document file
			$documents = DB::select('*')->from('documents')
				->where('profile_id', '=', $profile_id)
				->where('is_temp', '=', 1)
				->execute()
				->as_array();
			foreach ($documents as $profile_documents) {
				unlink($profile_documents['doc_name']);
			}

			//delete picture file
			$picture = DB::select('*')->from('picture')
				->where('profile_id', '=', $profile_id)
				->where('is_temp', '=', 1)
				->execute()
				->as_array();
			foreach ($picture as $profile_picture) {
				unlink($profile_picture['picture_name']);
			}


			// Delete Logo
			DB::delete('logo')->where('profile_id', '=', $profile_id)->where('is_temp', '=', 1)->execute();

			// Delete picture
			DB::delete('picture')->where('profile_id', '=', $profile_id)->where('is_temp', '=', 1)->execute();

			// Delete documents
			DB::delete('documents')->where('profile_id', '=', $profile_id)->where('is_temp', '=', 1)->execute();

			$this->request->redirect($redirect_url);
			exit;
		}
	}
}
