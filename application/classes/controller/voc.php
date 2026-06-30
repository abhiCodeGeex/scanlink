<?php

defined('SYSPATH') or die('No direct script access.');

class Controller_Voc extends Controller_Template_Master
{

  public $types = '';
  public $userid = '';
  public $video_permission = 0;
  public $client_id = '';

  public function before()
  {

    parent::before();



    //$session = Session::instance();	
    //var_dump($session->get('clientuserid'));exit;
    if (!$this->session->get('clientuserid') && !$this->session->get('voc_user_id')) :
      $this->request->redirect(URL::base() . 'welcome/index');
    endif;
    // Default Head view file
    $this->template->head = View::factory('template/head');

    // Set the default Page Title
    $this->template->head->title = 'Welcome to ScanLink';

    // All CSS Stylesheets
    $this->template->head->stylesheets = array(array('href' => 'styles/style.css', 'media' => 'screen'), array('href' => 'styles/colorbox.css', 'media' => 'screen'), array('href' => 'styles/coin-slider-styles.css', 'media' => 'screen'), array('href' => 'styles/ui/jquery.ui.tabs.css', 'media' => 'screen'));

    // All JS Libraries and Scripts
    //$this->template->head->javascripts = array('js/jquery.min.js', 'js/jquery.validate.min.js', 'js/jquery.colorbox.js', 'js/jquery.autogrow-textarea.js', 'js/coin-slider.min.js', 'js/dashboard/tooltip.js', 'js/dashboard/jquery-ui-1.js', 'js/dashboard/custom.js', 'js/jquery.uniform.js', 'js/custome.js', 'js/modernizr.custom.60079.js', 'js/jscolor/jscolor.js', 'js/jquery.maxlength-min.js', 'ckeditor/ckeditor.js', 'js/deleteLogo.js','js/profile_page_common.js');
    $this->template->head->javascripts = array('js/jquery.min.js', 'js/jquery.validate.min.js', 'js/jquery.colorbox.js', 'js/jquery.autogrow-textarea.js', 'js/coin-slider.min.js', 'js/dashboard/tooltip.js', 'form-builder/js/jquery-ui.js', 'js/dashboard/custom.js', 'js/jquery.uniform.js', 'js/custome.js', 'js/modernizr.custom.60079.js', 'js/jscolor/jscolor.js', 'js/jquery.maxlength-min.js', 'ckeditor/ckeditor.js', 'js/deleteLogo.js', 'js/profile_page_common.js');

    // Default Header view file
    $this->template->headers = View::factory('template/headers');

    // Default menu
    $parameter = $this->request->param('id');
    $action = $this->request->action();
    if (isset($action) && $action != 'index') {
      $msgPopup = "false";
    } else {
      $msgPopup = "true";
    }
    $this->template->headers->menu = View::factory('template/menu')
      ->bind('profile_id', $parameter)
      ->bind('msgPopup', $msgPopup);

    if ($this->session->get('clientuserid')) :
      $this->template->headers->menu->logoutlnk = $this->session->get('clientusername');
    endif;
    $this->template->headers->menu->menu = $this->menu;
    /* WP_MS START  */
    $this->template->headers->menu->menuSubmenu = $this->menuSubmenu;
    /* WP_MS END  */
    $this->template->headers->menu->currentactive = $this->request->controller() . "/" . $this->request->action();
    // Default Content template
    $this->template->footer = View::factory('template/footer');

    if ($this->template->footer) :
      // All JS Libraries and Scripts
      $this->template->footer->scripts = array();
    endif;
    $this->types = Model::factory('dashboard')->getTypes();
    $this->userid = $this->session->get('clientuserid');
    $this->client_id = $this->session->get('client_id');
    $this->video_permission = Model::factory('user')->getVideoPermissionByCid($this->userid);

    $this->type_id = 12;
  }

  public function action_index()
  {

    if ($this->session->get('voc_user_id') != "") {
      $this->request->redirect(URL::base() . 'dashboard/voc_user');
      exit;
    }

    // Set the Page Title
    $this->template->head->title = 'VOCC Add';
    $videos = array();
    $videos2 = array();
    $add_form_display = false;
    //get profile id exsist to add code 
    $profile_id_get = Model::factory('code')->getprofileidtoupdate($this->session->get('client_id'));
    if (!empty($profile_id_get)) {
      $add_form_display = true;
      $profile_id = $profile_id_get[0]['id'];
    }



    $user_data = Model::factory('user')->getUserByID($this->session->get('clientuserid'));

    if ($user_data["is_sub_user"] == 1 && $user_data["access_addcode"] == 0) {
      Session::instance()->set('errormessage', 'Oops! You have no access to add profile.');
      $this->request->redirect('dashboard/voc');
    }

    if ($this->session->get('form_builder_referer', FALSE) != FALSE) {
      $add_form_display = TRUE;
    }

    $id = $this->request->param('id');
    if ($id != '') {
      $profile_id = $id;
    }

    $current_url = URL::base() . $this->request->uri();

    $type_id = $this->type_id;

    //existing forms
    //$existing_forms_arr = Model::factory('Formbuilder')->get_existing_forms($profile_id, $this->userid);
    if ($_SESSION['is_sub_user'] == 1) {
      $show_account_code_profile_to_sub_user = Model::factory('User')->getUserByID($_SESSION['clientuserid']);
      $existing_forms_arr = Model::factory('Formbuilder')->get_existing_forms_client_id($profile_id, $_SESSION['client_id'], $show_account_code_profile_to_sub_user['show_code_profile_id_to_acc_user']);
      // $existing_forms_arr = Model::factory('Formbuilder')->get_existing_forms($profile_id, $this->userid);
    } else {
      $existing_forms_arr = Model::factory('Formbuilder')->get_existing_forms($profile_id, $this->userid);
    }

    $form_library_arr = Model::factory('Formbuilder')->get_library_forms($this->userid, $profile_id);
    $formbuilder_existing_popup_show = true;

    // whether to show popup for form builder activation
    if ($id == "" && isset($_POST['profile_id']))
      $profile_id = $_POST['profile_id'];

    $profile_arr = Model::factory('profiles')->getProfileById($profile_id);

    if (strtotime($profile_arr['expired_at']) < strtotime(date("Y-m-d H:i:s")) && ($profile_arr['free_code'] == 0 || ($profile_arr['free_code'] == 1 && $profile_arr['renewal_required'] == 1))) {
      $this->session->set("errormessage", "You can not perform this action on expired profile.");
      $this->request->redirect('dashboard/voc');
      exit;
    }

    $form_active = $profile_arr['form_active'];
    $form_is_enable = $profile_arr['form_is_enable'];
    $form_id = $profile_arr['form_id'];
    $form_name = $profile_arr['form_title'];
    $expired_at = $profile_arr['expired_at_formatted'];
    $free_code = $profile_arr['free_code'];
    $renewal_required = $profile_arr['renewal_required'];
    $form_email_tag     = !empty($_REQUEST["form_email_tag"]) ? $_REQUEST["form_email_tag"] : '';

    if (isset($profile_arr['pop_up_formbuilder']) && $profile_arr['pop_up_formbuilder'] == '1') {
      $datafill = array();
      $datafill['pop_up_formbuilder'] = '0';
      $datafill['form_email_tag'] = $form_email_tag;
      Model::factory('profiles')->updateProfile($profile_id, $datafill);
      $formbuilder_popup_show = true;
    } else {
      $formbuilder_popup_show = false;
    }

    if (!empty($_POST) && !isset($_POST["edit_id"])) : //add mode



      $profile_id = isset($_POST['profile_id']) ? $_POST['profile_id'] : '';
      if ($profile_id == '') {
        $this->request->redirect('dashboard/index');
        exit;
      }

      /// update is_temp field for logo, pictures, documents uploaded
      Model::factory('picture')->update_temp_field_voc($profile_id);
      Model::factory('logo')->update_temp_field($profile_id);

      //for form builder related entry start
      $form_name     = $_REQUEST["hdn_form_name"];
      $email_arr     = explode(",", $_REQUEST["hdn_email_arr"]);
      if (isset($_REQUEST["enable_form"]) && $_REQUEST["enable_form"] == 1)
        $enable_form     = 1;
      else
        $enable_form     = 0;

      if (isset($_REQUEST["enable_form_analytics"]) && $_REQUEST["enable_form_analytics"] == 1)
        $enable_form_analytics     = 1;
      else
        $enable_form_analytics    = 0;

      if (trim($form_name) != "") {
        $form_id       = Model::factory('Formbuilder')->get_form_id($profile_id);

        $form_id_current = $form_id[0]["form_id"];
        if ($form_id_current == 0) {
          //get maximum form id 
          $new_form_id       = Model::factory('Formbuilder')->get_form_id_max();

          $datafill = array();
          $datafill['form_id'] = $new_form_id;
          $datafill['form_title'] = $form_name;
          $datafill['form_email_tag'] = $form_email_tag;
          $datafill['form_is_enable'] = $enable_form;
          $datafill['enable_form_analytics'] = $enable_form_analytics;
          Model::factory('profiles')->updateProfile($profile_id, $datafill);

          $form_id_current = $new_form_id;
        } else {
          $datafill = array();
          $datafill['form_title'] = $form_name;
          $datafill['form_is_enable'] = $enable_form;
          $datafill['form_email_tag'] = $form_email_tag;
          $datafill['enable_form_analytics'] = $enable_form_analytics;
          Model::factory('profiles')->updateProfile($profile_id, $datafill);

          //update form_id in question if it is zero only happned when add existing form
          $datafill = array();
          $datafill['form_id'] = $form_id_current;
          Model::factory('Formbuilder')->form_builder_question_update_form_id($profile_id, $datafill);
        }

        //add entry in form builder library if no entry exists
        $form_builder_library_id  = Model::factory('Formbuilder')->get_form_builder_library_id($form_id_current);
        if (count($form_builder_library_id) > 0) //update form name
        {
          $library_id_current = $form_builder_library_id[0]["form_builder_library_id"];
          $datafill = array();
          $datafill['form_title'] = $form_name;
          Model::factory('Formbuilder')->form_builder_library_update($library_id_current, $datafill);
        } else //add entry in form builder library
        {
          Model::factory('Formbuilder')->form_builder_library_add($form_id_current, $this->userid, $form_name);
        }

        // remove old email recipient
        Model::factory('Formbuilder')->form_builder_recipient_delete($form_id_current);
        for ($i = 0; $i < count($email_arr); $i++) {
          Model::factory('Formbuilder')->form_builder_recipient_add($form_id_current, $email_arr[$i]);
        }
      } else {
        $datafill = array();
        $datafill['form_email_tag'] = $form_email_tag;
        $datafill['form_is_enable'] = $enable_form;
        Model::factory('profiles')->updateProfile($profile_id, $datafill);
      }
      //for form builder related entry end

      $post = $_POST;


      $tiles_order = trim($_POST['hdn_order']);

      $txt_code_profile_name = isset($_POST['txt_code_profile_name']) ? trim($_POST['txt_code_profile_name']) : '';
      $display_share_link = isset($_POST['display_share_link']) ? $_POST['display_share_link'] : '';



      $protect = $_POST['protect'];
      $password = $_POST['password'];
      $show_header = isset($_POST['show_header']) ? 1 : 0;
      $code_type = $_POST['code_type'];

      $form_submission_format = $_POST['form_submission_format'];



      $postArr['type'] = 'voc';

      $postArr['code_profile_name'] = $txt_code_profile_name;
      $postArr['tiles_order'] = $tiles_order;

      $postArr["form_submission_format"] = $form_submission_format;

      $postArr['voc_email_url'] = trim($_POST["voc_email_url"]);
      $postArr['voc_email_text'] = trim($_POST["voc_email_text"]);
      $postArr['voc_email_sign_line1'] = trim($_POST["voc_email_sign_line1"]);
      $postArr['voc_email_sign1_bold'] = $_POST["voc_email_sign1_bold"];
      $postArr['voc_email_sign1_italic'] = $_POST["voc_email_sign1_italic"];
      $postArr['voc_email_sign1_underline'] = $_POST["voc_email_sign1_underline"];

      $postArr['voc_email_sign_line2'] = trim($_POST["voc_email_sign_line2"]);
      $postArr['voc_email_sign2_bold'] = $_POST["voc_email_sign2_bold"];
      $postArr['voc_email_sign2_italic'] = $_POST["voc_email_sign2_italic"];
      $postArr['voc_email_sign2_underline'] = $_POST["voc_email_sign2_underline"];


      $postArr['voc_title_bar_enable'] = isset($_POST["voc_title_bar_enable"]) ? '1' : '0';

      $postArr['voc_title_bar_text'] = trim($_POST["voc_title_bar_text"]);
      $postArr['voc_title_bar_colour'] = trim($_POST["voc_title_bar_colour"]);
      $postArr['voc_first_name'] = trim($_POST["voc_first_name"]);
      $postArr['voc_last_name'] = trim($_POST["voc_last_name"]);
      $postArr['voc_address'] = trim($_POST["voc_address"]);
      $postArr['voc_town'] = trim($_POST["voc_town"]);
      $postArr['voc_state'] = trim($_POST["voc_state"]);
      $postArr['voc_phone'] = trim($_POST["voc_phone"]);


      if ($_POST["voc_dob"] != "") {
        $voc_dob_arr = explode("/", $_POST["voc_dob"]);
        $postArr['voc_dob'] = $voc_dob_arr[2] . "-" . $voc_dob_arr[1] . "-" . $voc_dob_arr[0];
      } else {
        $postArr['voc_dob'] = "";
      }

      $postArr['voc_known_allergies'] = trim($_POST["voc_known_allergies"]);
      $postArr['voc_blood_type'] = trim($_POST["voc_blood_type"]);
      $postArr['voc_next_of_kin'] = trim($_POST["voc_next_of_kin"]);
      $postArr['voc_contact_phone'] = trim($_POST["voc_contact_phone"]);
      $postArr['voc_employer'] = trim($_POST["voc_employer"]);
      $postArr['voc_emp_address'] = trim($_POST["voc_emp_address"]);
      $postArr['voc_emp_town'] = trim($_POST["voc_emp_town"]);
      $postArr['voc_emp_state'] = trim($_POST["voc_emp_state"]);
      $postArr['voc_emp_phone'] = trim($_POST["voc_emp_phone"]);

      $postArr['protect'] = $protect;
      $postArr['password'] = $password;
      $postArr['show_header'] = $show_header;

      $postArr['code_type'] = $code_type;
      $postArr['created_at'] = date('Y-m-d H:i:s');


      $postArr['display_share_link'] = $display_share_link;


      if ($_POST["activation_start_date"] != "") {
        $activation_start_date_arr = explode("/", $_POST["activation_start_date"]);
        $postArr['activation_start_date'] = $activation_start_date_arr[2] . "-" . $activation_start_date_arr[1] . "-" . $activation_start_date_arr[0];
      } else {
        $postArr['activation_start_date'] = "";
      }

      if ($_POST["activation_end_date"] != "") {
        $activation_end_date_arr = explode("/", $_POST["activation_end_date"]);
        $postArr['activation_end_date'] = $activation_end_date_arr[2] . "-" . $activation_end_date_arr[1] . "-" . $activation_end_date_arr[0];
      } else {
        $postArr['activation_end_date'] = "";
      }

      $profileid = Model::factory('voc')->insertVoc($postArr, $this->userid, $profile_id);

      for ($k = 0; $k < count($_POST["voc_user_email"]); $k++) {
        //check for user exists for this profile
        $arr = Model::factory('voc')->get_voc_user_by_user_details($profile_id, $_POST["voc_user_email"][$k]);

        if ($_POST["voc_user_email"][$k] != "" && count($arr) == 0)
          Model::factory('voc')->add_voc_users($profile_id, $_POST["voc_user_email"][$k], $_POST["voc_user_password"][$k]);
      }

      for ($k = 0; $k < count($_POST["voc_recipient_email"]); $k++) {
        if ($_POST["voc_recipient_email"][$k] != "") {
          //check for recipients exists for this profile
          $arr = Model::factory('voc')->get_voc_recipients_by_email($profile_id, $_POST["voc_recipient_email"][$k]);

          if (count($arr) == 0)
            Model::factory('voc')->add_voc_recipients($profile_id, $_POST["voc_recipient_email"][$k]);
        }
      }

      for ($k = 0; $k < count($_POST["doc_name"]); $k++) {
        if ($_POST["doc_name"][$k] != "") {
          $doc_expiry_date = "";
          if ($_POST["doc_expiry_date"][$k] != "") {
            $doc_expiry_date_arr = explode("/", $_POST["doc_expiry_date"][$k]);
            $doc_expiry_date = $doc_expiry_date_arr[2] . "-" . $doc_expiry_date_arr[1] . "-" . $doc_expiry_date_arr[0];
          }


          $voc_document_id = Model::factory('voc')->add_voc_documents($profile_id, $_POST["doc_name"][$k], $doc_expiry_date);

          $arr  = pathinfo($_FILES["document_file"]["name"][$k]);
          $ext = $arr["extension"];

          $filename = $profile_id . "_" . ($k + 1) . "_doc." . $ext;

          if (move_uploaded_file($_FILES["document_file"]["tmp_name"][$k], "images/voc_documents/" . $filename))
            Model::factory('voc')->update_voc_doc_image_name($voc_document_id, $filename);
        }
      }


      $tmp_files = $this->session->get('tmp_files', FALSE);
      if ($tmp_files) {
        $this->session->set('tmp_files', FALSE);
      }



      $clienturl = Model::factory('client')->geturl($profileid);
      $qrurl = URL::base() . $clienturl . "/" . $profileid;

      $wpArr = @file_get_contents(GALATECH_API . 'item/addurl?url=' . $qrurl);
      $response = json_decode($wpArr);

      if ($response->response) {
        $data = array("analytic_key" => $response->item_key);
        Model::factory('profiles')->updateProfile($profileid, $data);
      }

      require_once("classes/shortURL.php");
      $api_token = 'KqXZgB6aPc7IkpDB1UlqALKp0v1yhCcjPI40dkprS8UOCypvfYu7KKNrn0QY';
      $short_url = shortenUrl($qrurl, $api_token);
      //$short_url = shortenUrl($qrurl);
      $data = array("shorturl" => $short_url['shortLink']);
      Model::factory('profiles')->updateProfile($profileid, $data);

      if ($code_type == 0) { // QR Code
        //$QRURL = "https://chart.googleapis.com/chart?cht=qr&chs=250x250&chl=" . $short_url['shortLink'] . "&chld=M|0";
	$QRURL = "https://api.qrserver.com/v1/create-qr-code/?data=" . $short_url['shortLink'] . "&size=250x250&ecc=M";

        $QRCode = file_get_contents($QRURL);
        $QRImage = "images/qrcode/CSQRIMG" . $profileid . ".png";
        $qrcodeid = Model::factory('qrcode')->saveqrcode($profileid, $QRImage);
        $file = fopen(DOCROOT . $QRImage, "w");
        fwrite($file, $QRCode);
        fclose($file);
      } elseif ($code_type == 1) { // Data matrix
        $QRCode = file_get_contents(URL::base() . "dm_code/index.php?data_url=" . $short_url['shortLink'] . "&matrix_size=10&matrix_type=URL&fc=000000&bc=FFFFFF&matrix_block_style=square_blocks");
        //$QRCode = file_get_contents("http://localhost/datamatrix/dm_code/index.php?data_url=http://www,google.com&matrix_size=10&matrix_type=URL&fc=000000&bc=FFFFFF&matrix_block_style=square_blocks");
        $QRImage = "images/dmcode/DMIMG" . $profileid . ".png";
        $qrcodeid = Model::factory('qrcode')->saveqrcode($profileid, $QRImage);
        $file = fopen(DOCROOT . $QRImage, "w");
        fwrite($file, $QRCode);
        fclose($file);
      }

      $this->session->set("successmessage", "successfully added VOCC.");
      $this->request->redirect('dashboard/voc');
      exit;

    elseif (!empty($_POST) && isset($_POST["edit_id"])) :  //edit mode



      $profiledata = Model::factory('profiles')->getProfileById($_POST["edit_id"]);

      //if ($profiledata['user_id'] != $this->userid) {
      if ($profiledata['client_id'] != $this->session->get('client_id')) {
        $this->session->set("errormessage", "You can edit your own voc only.");
        $this->request->redirect('dashboard/voc');
        exit;
      }

      if ($profiledata['type_id'] != $type_id) {
        $this->session->set("errormessage", "You can edit only voc type.");
        $this->request->redirect('dashboard/voc');
        exit;
      }



      //for form builder related entry start
      $form_name     = $_REQUEST["hdn_form_name"];
      $email_arr     = explode(",", $_REQUEST["hdn_email_arr"]);
      if (isset($_REQUEST["enable_form"]) && $_REQUEST["enable_form"] == 1)
        $enable_form     = 1;
      else
        $enable_form     = 0;

      if (isset($_REQUEST["enable_form_analytics"]) && $_REQUEST["enable_form_analytics"] == 1)
        $enable_form_analytics     = 1;
      else
        $enable_form_analytics    = 0;

      $profile_id = $_POST["edit_id"];

      /// update is_temp field for logo, pictures, documents uploaded
      Model::factory('picture')->update_temp_field_voc($profile_id);
      Model::factory('logo')->update_temp_field($profile_id);

      if (trim($form_name) != "") {
        $form_id       = Model::factory('Formbuilder')->get_form_id($profile_id);

        $form_id_current = $form_id[0]["form_id"];
        if ($form_id_current == 0) {
          //get maximum form id 
          $new_form_id       = Model::factory('Formbuilder')->get_form_id_max();

          $datafill = array();
          $datafill['form_id'] = $new_form_id;
          $datafill['form_title'] = $form_name;
          $datafill['form_email_tag'] = $form_email_tag;
          $datafill['form_is_enable'] = $enable_form;
          $datafill['enable_form_analytics'] = $enable_form_analytics;
          Model::factory('profiles')->updateProfile($profile_id, $datafill);

          $form_id_current = $new_form_id;
        } else {
          $datafill = array();
          $datafill['form_title'] = $form_name;
          $datafill['form_is_enable'] = $enable_form;
          $datafill['form_email_tag'] = $form_email_tag;
          $datafill['enable_form_analytics'] = $enable_form_analytics;
          Model::factory('profiles')->updateProfile($profile_id, $datafill);

          //update form_id in question if it is zero only happned when add existing form
          $datafill = array();
          $datafill['form_id'] = $form_id_current;
          Model::factory('Formbuilder')->form_builder_question_update_form_id($profile_id, $datafill);
        }

        //add entry in form builder library if no entry exists
        $form_builder_library_id  = Model::factory('Formbuilder')->get_form_builder_library_id($form_id_current);
        if (count($form_builder_library_id) > 0) //update form name
        {
          $library_id_current = $form_builder_library_id[0]["form_builder_library_id"];
          $datafill = array();
          $datafill['form_title'] = $form_name;
          Model::factory('Formbuilder')->form_builder_library_update($library_id_current, $datafill);
        } else //add entry in form builder library
        {
          Model::factory('Formbuilder')->form_builder_library_add($form_id_current, $this->userid, $form_name);
        }


        // remove old email recipient
        Model::factory('Formbuilder')->form_builder_recipient_delete($form_id_current);
        for ($i = 0; $i < count($email_arr); $i++) {
          Model::factory('Formbuilder')->form_builder_recipient_add($form_id_current, $email_arr[$i]);
        }
      } else {
        $datafill = array();
        $datafill['form_email_tag'] = $form_email_tag;
        $datafill['form_is_enable'] = $enable_form;
        Model::factory('profiles')->updateProfile($profile_id, $datafill);
      }
      //for form builder related entry end

      $post = $_POST;

      $txt_code_profile_name = isset($_POST['txt_code_profile_name']) ? trim($_POST['txt_code_profile_name']) : '';

      $display_share_link = isset($_POST['display_share_link']) ? $_POST['display_share_link'] : '';

      $tiles_order = trim($_POST['hdn_order']);

      $protect = $_POST['protect'];
      $password = $_POST['password'];
      $show_header = isset($_POST['show_header']) ? $_POST['show_header'] : '0';

      $code_type = $_POST['code_type'];



      $postArr['type'] = 'voc';
      $postArr['code_profile_name'] = $txt_code_profile_name;
      $postArr['tiles_order'] = $tiles_order;

      $postArr['form_submission_format'] = $_POST['form_submission_format'];

      $postArr['voc_email_url'] = trim($_POST["voc_email_url"]);
      $postArr['voc_email_text'] = trim($_POST["voc_email_text"]);
      $postArr['voc_email_sign_line1'] = trim($_POST["voc_email_sign_line1"]);
      $postArr['voc_email_sign1_bold'] = $_POST["voc_email_sign1_bold"];
      $postArr['voc_email_sign1_italic'] = $_POST["voc_email_sign1_italic"];
      $postArr['voc_email_sign1_underline'] = $_POST["voc_email_sign1_underline"];

      $postArr['voc_email_sign_line2'] = trim($_POST["voc_email_sign_line2"]);
      $postArr['voc_email_sign2_bold'] = $_POST["voc_email_sign2_bold"];
      $postArr['voc_email_sign2_italic'] = $_POST["voc_email_sign2_italic"];
      $postArr['voc_email_sign2_underline'] = $_POST["voc_email_sign2_underline"];

      $postArr['voc_title_bar_enable'] = isset($_POST["voc_title_bar_enable"]) ? '1' : '0';

      $postArr['voc_title_bar_text'] = trim($_POST["voc_title_bar_text"]);
      $postArr['voc_title_bar_colour'] = trim($_POST["voc_title_bar_colour"]);
      $postArr['voc_first_name'] = trim($_POST["voc_first_name"]);
      $postArr['voc_last_name'] = trim($_POST["voc_last_name"]);
      $postArr['voc_address'] = trim($_POST["voc_address"]);
      $postArr['voc_town'] = trim($_POST["voc_town"]);
      $postArr['voc_state'] = trim($_POST["voc_state"]);
      $postArr['voc_phone'] = trim($_POST["voc_phone"]);


      if ($_POST["voc_dob"] != "") {
        $voc_dob_arr = explode("/", $_POST["voc_dob"]);
        $postArr['voc_dob'] = $voc_dob_arr[2] . "-" . $voc_dob_arr[1] . "-" . $voc_dob_arr[0];
      } else {
        $postArr['voc_dob'] = "";
      }

      $postArr['voc_known_allergies'] = trim($_POST["voc_known_allergies"]);
      $postArr['voc_blood_type'] = trim($_POST["voc_blood_type"]);
      $postArr['voc_next_of_kin'] = trim($_POST["voc_next_of_kin"]);
      $postArr['voc_contact_phone'] = trim($_POST["voc_contact_phone"]);
      $postArr['voc_employer'] = trim($_POST["voc_employer"]);
      $postArr['voc_emp_address'] = trim($_POST["voc_emp_address"]);
      $postArr['voc_emp_town'] = trim($_POST["voc_emp_town"]);
      $postArr['voc_emp_state'] = trim($_POST["voc_emp_state"]);
      $postArr['voc_emp_phone'] = trim($_POST["voc_emp_phone"]);

      $postArr['protect'] = $protect;
      $postArr['password'] = $password;
      $postArr['show_header'] = $show_header;

      $postArr['code_type'] = $code_type;
      $postArr['created_at'] = date('Y-m-d H:i:s');

      $postArr['display_share_link'] = $display_share_link;


      if ($_POST["activation_start_date"] != "") {
        $activation_start_date_arr = explode("/", $_POST["activation_start_date"]);
        $postArr['activation_start_date'] = $activation_start_date_arr[2] . "-" . $activation_start_date_arr[1] . "-" . $activation_start_date_arr[0];
      } else {
        $postArr['activation_start_date'] = "";
      }

      if ($_POST["activation_end_date"] != "") {
        $activation_end_date_arr = explode("/", $_POST["activation_end_date"]);
        $postArr['activation_end_date'] = $activation_end_date_arr[2] . "-" . $activation_end_date_arr[1] . "-" . $activation_end_date_arr[0];
      } else {
        $postArr['activation_end_date'] = "";
      }

      $profileid = Model::factory('voc')->updateVoc($postArr, $_POST["edit_id"]);


      //first remove old users and then add
      Model::factory('voc')->remove_voc_users($profile_id);
      for ($k = 0; $k < count($_POST["voc_user_email"]); $k++) {
        if ($_POST["voc_user_email"][$k] != "") {
          //check for user exists for this profile
          $arr = Model::factory('voc')->get_voc_user_by_user_details($profile_id, $_POST["voc_user_email"][$k]);

          if (count($arr) == 0)
            Model::factory('voc')->add_voc_users($profile_id, $_POST["voc_user_email"][$k], $_POST["voc_user_password"][$k]);
        }
      }

      //first remove old recipients and then add
      Model::factory('voc')->remove_voc_recipients($profile_id);
      for ($k = 0; $k < count($_POST["voc_recipient_email"]); $k++) {
        if ($_POST["voc_recipient_email"][$k] != "") {
          //check for recipients exists for this profile
          $arr = Model::factory('voc')->get_voc_recipients_by_email($profile_id, $_POST["voc_recipient_email"][$k]);

          if (count($arr) == 0)
            Model::factory('voc')->add_voc_recipients($profile_id, $_POST["voc_recipient_email"][$k]);
        }
      }

      for ($k = 0; $k < count($_POST["doc_name"]); $k++) {
        if ($_POST["doc_name"][$k] != "") {
          $doc_expiry_date = "";
          if ($_POST["doc_expiry_date"][$k] != "") {
            $doc_expiry_date_arr = explode("/", $_POST["doc_expiry_date"][$k]);
            $doc_expiry_date = $doc_expiry_date_arr[2] . "-" . $doc_expiry_date_arr[1] . "-" . $doc_expiry_date_arr[0];
          }

          if ($_POST["hdn_existing_doc_id"][$k] > 0) {
            $voc_document_id = $_POST["hdn_existing_doc_id"][$k];
            Model::factory('voc')->update_voc_documents($voc_document_id, $_POST["doc_name"][$k], $doc_expiry_date);
          } else {
            $voc_document_id = Model::factory('voc')->add_voc_documents($profile_id, $_POST["doc_name"][$k], $doc_expiry_date);
          }

          if ($_FILES["document_file"]["size"][$k] > 0) {
            if ($_POST["hdn_existing_doc_id"][$k] > 0) //remove old documents
            {
              $document_details = Model::factory('voc')->getDocumentsById($_POST["hdn_existing_doc_id"][$k]);
              if ($document_details["file_name"] != "" && file_exists("images/voc_documents/" . $document_details["file_name"])) {
                unlink("images/voc_documents/" . $document_details["file_name"]);
              }
            }


            $arr  = pathinfo($_FILES["document_file"]["name"][$k]);
            $ext = $arr["extension"];

            $filename = $profile_id . "_" . ($k + 1) . "_doc." . $ext;

            if (move_uploaded_file($_FILES["document_file"]["tmp_name"][$k], "images/voc_documents/" . $filename))
              Model::factory('voc')->update_voc_doc_image_name($voc_document_id, $filename);
          }
        }
      }

      $tmp_files = $this->session->get('tmp_files', FALSE);
      if ($tmp_files) {
        //Model::factory('documents')->saveDocFiles($tmp_files, $_POST["edit_id"]);
        //Model::factory('picture')->savePictureFiles($tmp_files, $_POST["edit_id"]);
        //Model::factory('logo')->saveLogoFile($tmp_files, $_POST["edit_id"]);
        $this->session->set('tmp_files', FALSE);
      }

      $this->session->delete('form_builder_referer');

      $clienturl = Model::factory('client')->geturl($_POST["edit_id"]);
      $qrurl = URL::base() . $clienturl . "/" . $_POST["edit_id"];

      require_once("classes/shortURL.php");
      $api_token = 'KqXZgB6aPc7IkpDB1UlqALKp0v1yhCcjPI40dkprS8UOCypvfYu7KKNrn0QY';
      $short_url = shortenUrl($qrurl, $api_token);
      //$short_url = shortenUrl($qrurl);
      $data = array("shorturl" => $short_url['shortLink']);
      Model::factory('profiles')->updateProfile($_POST["edit_id"], $data);

      if ($code_type == 0) { // QR Code
        //$QRURL = "https://chart.googleapis.com/chart?cht=qr&chs=250x250&chl=" . $short_url['shortLink'] . "&chld=M|0";
	$QRURL = "https://api.qrserver.com/v1/create-qr-code/?data=" . $short_url['shortLink'] . "&size=250x250&ecc=M";

        $QRCode = file_get_contents($QRURL);
        $QRImage = "images/qrcode/CSQRIMG" . $_POST["edit_id"] . ".png";
        $qrcodeid = Model::factory('qrcode')->update_qrcode($_POST["edit_id"], $QRImage);
        $file = fopen(DOCROOT . $QRImage, "w");
        fwrite($file, $QRCode);
        fclose($file);
      } elseif ($code_type == 1) { // Data matrix
        $QRCode = file_get_contents(URL::base() . "dm_code/index.php?data_url=" . $short_url['shortLink'] . "&matrix_size=10&matrix_type=URL&fc=000000&bc=FFFFFF&matrix_block_style=square_blocks");
        //$QRCode = file_get_contents("http://localhost/datamatrix/dm_code/index.php?data_url=http://www,google.com&matrix_size=10&matrix_type=URL&fc=000000&bc=FFFFFF&matrix_block_style=square_blocks");
        $QRImage = "images/dmcode/DMIMG" . $_POST["edit_id"] . ".png";
        $qrcodeid = Model::factory('qrcode')->update_qrcode($_POST["edit_id"], $QRImage);
        $file = fopen(DOCROOT . $QRImage, "w");
        fwrite($file, $QRCode);
        fclose($file);
      }
      $this->session->set("successmessage", "successfully edited VOCC.");
      $this->request->redirect('voc/edit/' . $_POST["edit_id"]);
      exit;

    endif;




    $param = 'voc/index';
    // Default Content template

    //participant list
    $add_edit_participant_list_arr = Model::factory('Formbuilder')->get_participant_list($profile_id);
    $participant_recipient_list_arr = Model::factory('Formbuilder')->get_participant_recipient_list($profile_id);
    $add_edit_participant_list_popup_show = true;

    $this->template->content = View::factory('voc/index')
      ->bind('post', $post)
      ->bind('video_per', $this->video_permission)
      ->bind('add_form_display', $add_form_display)
      ->bind('profile_id', $profile_id)
      ->bind('type_id', $type_id)
      ->bind('formbuilder_popup_show', $formbuilder_popup_show)
      ->bind('formbuilder_existing_popup_show', $formbuilder_existing_popup_show)
      ->bind('form_active', $form_active)
      ->bind('form_is_enable', $form_is_enable)
      ->bind('form_id', $form_id)
      ->bind('form_name', $form_name)
      ->bind('current_url', $current_url)
      ->bind('expired_at', $expired_at)
      ->bind('free_code', $free_code)
      ->bind('renewal_required', $renewal_required);

    $customqr_option = Model::factory('client')->isCustomqrEnabledToClient($this->client_id);
    $customqr = $customqr_option[0]['customqr_option'];
    $this->template->content->innermenu = View::factory('template/innermenu')
      ->bind('equiptypes', $this->types)
      ->bind('customqr', $customqr)
      ->bind('activemenu', $param);




    $this->template->content->formbuilder_popup = View::factory('template/formbuilder_order_success')
      ->bind('formbuilder_popup_show', $formbuilder_popup_show);

    $this->template->content->formbuilder_existing_popup = View::factory('template/formbuilder_exising_forms')
      ->bind('formbuilder_existing_popup_show', $formbuilder_existing_popup_show)
      ->bind('profile_id', $profile_id)
      ->bind('existing_forms_arr', $existing_forms_arr)
      ->bind('form_library_arr', $form_library_arr);

    $this->template->content->add_edit_participant_list_popup = View::factory('template/formbuilder_participant_list')
      ->bind('add_edit_participant_list_popup_show', $add_edit_participant_list_popup_show)
      ->bind('profile_id', $profile_id)
      ->bind('add_edit_participant_list_arr', $add_edit_participant_list_arr)
      ->bind('participant_recipient_list_arr', $participant_recipient_list_arr);
  }

  public function action_add()
  {
    // Set the Page Title
    $this->template->head->title = 'VOCC Add';

    $type_id = $this->type_id;

    $errors = '';
    $succmsg = '';
    $last_id = array();
    $postArr = array();
    if (isset($_POST) && $_POST) :
      $logo_file = $_FILES['logo_file']['name'];
      $txtname = trim($_POST['txtname']);
      $txtname2 = trim($_POST['txtname2']);
      $txt_code_profile_name = trim($_POST['txt_code_profile_name']);
      $txtidenty = trim($_POST['txtidenty']);

      $form_submission_format = $_POST['form_submission_format'];
      $txtsrno = trim($_POST['txtsrno']);
      $txtname = trim($_POST['txtname']);
      $txtdesc = trim($_POST['txtdesc']);
      $txtdesc2 = trim($_POST['txtdesc2']);
      $txtnotes = trim($_POST['txtnotes']);
      $txtcompname = trim($_POST['txtcompname']);
      $txttelephone = trim($_POST['txttelephone']);

      $txtbuttonbackcolor = $_POST['txtbuttonbackcolor'];
      $txtbuttonfontcolor = $_POST['txtbuttonfontcolor'];

      $link_button = isset($_POST['link_button']) ? '1' : '0';
      $link_button_text = trim($_POST['link_button_text']);
      $link_button_url = trim($_POST['link_button_url']);
      $link_button_color = trim($_POST['link_button_color']);

      $picture_file = $_FILES['picture_file']['name'];
      $docu_file = $_FILES['docu_file']['name'];
      $video_file = $_FILES['video_file']['name'];
      if ($logo_file == '') :
        $errors = "Client name can't left blank..!";
      elseif ($txtname == '') :
        $errors = "Address can't left blank..!";
      elseif ($txtidenty == '') :
        $errors = "Telephone can't left blank..!";
      elseif ($txtsrno == '') :
        $errors = "Contact Person can't left blank..!";
      elseif ($txtdesc == '') :
        $errors = "Registration Date can't left blank..!";
      elseif ($txtnotes == '') :
        $errors = "Email Address can't left blank..!";
      elseif ($txtcompname == '') :
        $errors = 'Please enter valid email address..!';
      elseif ($txttelephone == '') :
        $errors = "Password can't left blank..!";
      elseif ($picture_file == '') :
        $errors = "URL can't left blank..!";
      elseif ($docu_file == '') :
        $errors = "URL can't left blank..!";
      elseif ($video_file == '') :
        $errors = "URL can't left blank..!";
      else :
        $postArr['logo_file'] = $logo_file;
        $postArr['type'] = 'plant';
        $postArr['txtname'] = $txtname;
        $postArr['code_profile_name'] = $txt_code_profile_name;

        $postArr['form_submission_format'] = $form_submission_format;

        $postArr['txtidenty'] = $txtidenty;
        $postArr['txtsrno'] = $txtsrno;
        $postArr['txtdesc'] = $txtdesc;
        $postArr['txtnotes'] = $txtnotes;
        $postArr['txtcompname'] = $txtcompname;
        $postArr['txttelephone'] = $txttelephone;
        $postArr['picture_file'] = $picture_file;
        $postArr['docu_file'] = $docu_file;
        $postArr['video_file'] = $video_file;
        $postArr['created_at'] = date('Y-m-d H:i:s');
        $last_id = Model::factory('plant')->insertplant($postArr, $this->userid);
        $this->request->redirect('dashboard/profile');
      endif;
    endif;
  }

  public function action_edit()
  {

    if ($this->session->get('voc_user_id') != "") {
      $this->request->redirect(URL::base() . 'dashboard/voc_user');
      exit;
    }

    // Set the Page Title
    $this->template->head->title = 'VOCC Edit';



    $user_data = Model::factory('user')->getUserByID($this->session->get('clientuserid'));

    if ($user_data["is_sub_user"] == 1 && $user_data["access_edit"] == 0) {
      Session::instance()->set('errormessage', 'Oops! You have no access to edit profile.');
      $this->request->redirect('dashboard/voc');
    }

    $current_url = URL::base() . $this->request->uri();

    $type_id = $this->type_id;

    $successmessage = "";
    if ($this->session->get("successmessage") != "") {
      $successmessage = $this->session->get("successmessage");
      $this->session->delete("successmessage");
    }
    $this->session->delete('tmp_files');
    $id = $this->request->param('id');
    $profiledata = array();
    $errors = array();
    $succmsg = '';
    $last_id = array();
    $postArr = array();
    $post = array();
    $yes = '';
    $no = '';
    $expried_or_not = false;

    if (isset($id) && $id) :
      $profiledata = Model::factory('profiles')->getProfileById($id);

      if (sizeof($profiledata) == 0) {
        Session::instance()->set('errormessage', 'Oops! Access denied for requested profile.');
        $this->request->redirect(URL::base() . 'dashboard/voc');
      }

      //if ($profiledata['user_id'] != $this->userid) {
      if ($profiledata['client_id'] != $this->session->get('client_id')) {
        $this->session->set("errormessage", "You can edit your own voc only.");
        $this->request->redirect('dashboard/voc');
        exit;
      }

      if ($profiledata['type_id'] != $type_id) {
        $this->session->set("errormessage", "You can edit only voc type.");
        $this->request->redirect('dashboard/voc');
        exit;
      }

      if (strtotime($profiledata['expired_at']) < strtotime(date("Y-m-d H:i:s")) && ($profiledata['free_code'] == 0 || ($profiledata['free_code'] == 1 && $profiledata['renewal_required'] == 1))) {
        $expried_or_not = true;
      }

      $post["edit_id"] = $id;
      $post["logo"] = Model::factory('logo')->getLogoById($id);
      $post["documents"] = Model::factory('voc')->getDocumentsByProfileId($id);
      $post["pictures"] = Model::factory('picture')->getPicturesVocByProfileId($id);
      $post["voc_users"] = Model::factory('voc')->get_voc_user_by_profile_id($id);
      $post["voc_recipients"] = Model::factory('voc')->get_voc_recipients_by_profile_id($id);

      $post['txt_code_profile_name'] = $profiledata['code_profile_name'];
      $post['tiles_order'] = $profiledata['tiles_order'];
      $post['voc_first_name'] = $profiledata['voc_first_name'];
      $post['voc_last_name'] = $profiledata['voc_last_name'];

      $post['form_submission_format'] = $profiledata['form_submission_format'];

      $post['voc_email_url'] = $profiledata["voc_email_url"];
      $post['voc_email_text'] = $profiledata["voc_email_text"];
      $post['voc_email_sign_line1'] = $profiledata["voc_email_sign_line1"];
      $post['voc_email_sign1_bold'] = $profiledata["voc_email_sign1_bold"];
      $post['voc_email_sign1_italic'] = $profiledata["voc_email_sign1_italic"];
      $post['voc_email_sign1_underline'] = $profiledata["voc_email_sign1_underline"];

      $post['voc_email_sign_line2'] = $profiledata["voc_email_sign_line2"];
      $post['voc_email_sign2_bold'] = $profiledata["voc_email_sign2_bold"];
      $post['voc_email_sign2_italic'] = $profiledata["voc_email_sign2_italic"];
      $post['voc_email_sign2_underline'] = $profiledata["voc_email_sign2_underline"];

      $post['voc_title_bar_enable'] = $profiledata["voc_title_bar_enable"];

      $post['voc_title_bar_text'] = $profiledata["voc_title_bar_text"];
      $post['voc_title_bar_colour'] = $profiledata["voc_title_bar_colour"];
      $post['voc_first_name'] = $profiledata["voc_first_name"];
      $post['voc_last_name'] = $profiledata["voc_last_name"];
      $post['voc_address'] = $profiledata["voc_address"];
      $post['voc_town'] = $profiledata["voc_town"];
      $post['voc_state'] = $profiledata["voc_state"];
      $post['voc_phone'] = $profiledata["voc_phone"];

      $voc_dob = explode("-", $profiledata["voc_dob"]);

      if ($profiledata["voc_dob"] == "0000-00-00")
        $post['voc_dob'] = "";
      else
        $post['voc_dob'] = $voc_dob["2"] . "/" . $voc_dob[1] . "/" . $voc_dob[0];

      $post['voc_known_allergies'] = $profiledata["voc_known_allergies"];
      $post['voc_blood_type'] = $profiledata["voc_blood_type"];
      $post['voc_next_of_kin'] = $profiledata["voc_next_of_kin"];
      $post['voc_contact_phone'] = $profiledata["voc_contact_phone"];
      $post['voc_employer'] = $profiledata["voc_employer"];
      $post['voc_emp_address'] = $profiledata["voc_emp_address"];
      $post['voc_emp_town'] = $profiledata["voc_emp_town"];
      $post['voc_emp_state'] = $profiledata["voc_emp_state"];
      $post['voc_emp_phone'] = $profiledata["voc_emp_phone"];

      $post["display_share_link"] = $profiledata['display_share_link'];

      $post['protect'] = $profiledata['protect'];
      $post['password'] = $profiledata['password'];
      $post['show_header'] = $profiledata['show_header'];
      $post['code_type'] = $profiledata['code_type'];
      $post['enable_data_collection'] = $profiledata['enable_data_collection'];

      /* echo "<pre>";
        print_r ($post);
        exit; */


      if ($post['protect'] == '1') :
        $yes = TRUE;
        $no = FALSE;
      else :
        $yes = FALSE;
        $no = TRUE;
      endif;
    else :
      $this->session->set("errormessage", "To edit voc you need to get id");
      $this->request->redirect('dashboard/voc');
      exit;
    endif;
    $qrimg = Model::factory('qrcode')->getQRCodeByProfileId($id);

    // whether to show popup for form builder activation
    $profile_arr = Model::factory('profiles')->getProfileById($id);
    $form_active = $profile_arr['form_active'];
    $form_id = $profile_arr['form_id'];
    $form_name = $profile_arr['form_title'];
    $form_is_enable = $profile_arr['form_is_enable'];
    $enable_form_analytics = $profile_arr['enable_form_analytics'];
    $expired_at = $profile_arr['expired_at_formatted'];
    $activation_start_date = $profile_arr['activation_start_date_formatted'];
    $activation_end_date = $profile_arr['activation_end_date_formatted'];
    $free_code = $profile_arr['free_code'];
    $renewal_required = $profile_arr['renewal_required'];

    if ($activation_start_date == '00/00/0000')
      $activation_start_date = "";

    if ($activation_end_date == '00/00/0000')
      $activation_end_date = "";

    //existing forms
    // $existing_forms_arr = Model::factory('Formbuilder')->get_existing_forms($id, $this->userid);

    if ($_SESSION['is_sub_user'] == 1) {
      $show_account_code_profile_to_sub_user = Model::factory('User')->getUserByID($_SESSION['clientuserid']);
      $existing_forms_arr = Model::factory('Formbuilder')->get_existing_forms_client_id($id, $_SESSION['client_id'], $show_account_code_profile_to_sub_user['show_code_profile_id_to_acc_user']);
      // $existing_forms_arr = Model::factory('Formbuilder')->get_existing_forms($profile_id, $this->userid);
    } else {
      $existing_forms_arr = Model::factory('Formbuilder')->get_existing_forms($id, $this->userid);
      // $existing_forms_arr = Model::factory('Formbuilder')->get_existing_forms($profile_id, $this->userid);
    }

    $form_library_arr = Model::factory('Formbuilder')->get_library_forms($this->userid, $id);
    $formbuilder_existing_popup_show = TRUE;

    if (isset($profile_arr['pop_up_formbuilder']) && $profile_arr['pop_up_formbuilder'] == '1') {
      $datafill = array();
      $datafill['pop_up_formbuilder'] = '0';
      Model::factory('profiles')->updateProfile($id, $datafill);
      $formbuilder_popup_show = true;
    } else {
      $formbuilder_popup_show = false;
    }




    //get qr code url   
    $clienturl = Model::factory('client')->geturl($id);
    $qrurl = URL::base() . $clienturl . "/" . $id;

    //participant list
    $add_edit_participant_list_arr = Model::factory('Formbuilder')->get_participant_list($id);

    $participant_recipient_list_arr = Model::factory('Formbuilder')->get_participant_recipient_list($id);
    $add_edit_participant_list_popup_show = true;


    $this->template->content = View::factory('voc/edit')
      ->bind('yes', $yes)
      ->bind('no', $no)
      ->bind('post', $post)
      ->bind('qrimg', $qrimg)
      ->bind('video_per', $this->video_permission)
      ->bind('successmessage', $successmessage)
      ->bind('expried_or_not', $expried_or_not)
      ->bind('type_id', $type_id)
      ->bind('profile_id', $id)
      ->bind('formbuilder_popup_show', $formbuilder_popup_show)
      ->bind('formbuilder_existing_popup_show', $formbuilder_existing_popup_show)
      ->bind('form_active', $form_active)
      ->bind('form_id', $form_id)
      ->bind('form_name', $form_name)
      ->bind('form_is_enable', $form_is_enable)
      ->bind('enable_form_analytics', $enable_form_analytics)
      ->bind('current_url', $current_url)
      ->bind('qrurl', $qrurl)
      ->bind('expired_at', $expired_at)
      ->bind('activation_start_date', $activation_start_date)
      ->bind('activation_end_date', $activation_end_date)
      ->bind('free_code', $free_code)
      ->bind('renewal_required', $renewal_required);

    $param = 'voc/index';
    $customqr_option = Model::factory('client')->isCustomqrEnabledToClient($this->client_id);
    $customqr = $customqr_option[0]['customqr_option'];
    $this->template->content->innermenu = View::factory('template/innermenu')
      ->bind('equiptypes', $this->types)
      ->bind('customqr', $customqr)
      ->bind('activemenu', $param);

    $this->template->content->web_link_data = View::factory('template/weblink_template')
      ->bind('post', $post)
      ->bind('errors', $errors)
      ->bind('client_id', $this->client_id)
      ->bind('profile_id', $id);

    $this->template->content->formbuilder_popup = View::factory('template/formbuilder_order_success')
      ->bind('formbuilder_popup_show', $formbuilder_popup_show);

    $this->template->content->formbuilder_existing_popup = View::factory('template/formbuilder_exising_forms')
      ->bind('formbuilder_existing_popup_show', $formbuilder_existing_popup_show)
      ->bind('profile_id', $id)
      ->bind('existing_forms_arr', $existing_forms_arr)
      ->bind('form_library_arr', $form_library_arr);

    $this->template->content->add_edit_participant_list_popup = View::factory('template/formbuilder_participant_list')
      ->bind('add_edit_participant_list_popup_show', $add_edit_participant_list_popup_show)
      ->bind('profile_id', $id)
      ->bind('add_edit_participant_list_arr', $add_edit_participant_list_arr)
      ->bind('participant_recipient_list_arr', $participant_recipient_list_arr);
  }


  public function action_vocedit()
  {

    // Set the Page Title
    $this->template->head->title = 'VOCC Edit';

    $this->template->headers = View::factory('template/headers');

    // Default menu

    $this->template->headers->menu = View::factory('template/vocmenu');
    if ($this->session->get('voc_user_id')) :
      $this->template->headers->menu->logoutlnk = $this->session->get('clientusername');
    endif;
    $this->template->headers->menu->menu = '';
    $this->template->headers->menu->voc_user_logged_in = $this->voc_user_logged_in;

    array_push($this->template->head->stylesheets, array('href' => 'styles/ui/jquery.ui.theme.css', 'media' => 'screen'));


    if (!$this->session->get('voc_user_id')) {
      $this->request->redirect(URL::base() . 'welcome/index');
    }



    $current_url = URL::base() . $this->request->uri();

    $type_id = $this->type_id;

    $successmessage = "";
    if ($this->session->get("successmessage") != "") {
      $successmessage = $this->session->get("successmessage");
      $this->session->delete("successmessage");
    }
    $this->session->delete('tmp_files');
    $id = $this->request->param('id');

    $profiledata = array();
    $errors = array();
    $succmsg = '';
    $last_id = array();
    $postArr = array();
    $post = array();
    $yes = '';
    $no = '';
    $expried_or_not = false;

    if ($id == "" && isset($_POST['profile_id']))
      $id = $_POST['profile_id'];

    $profile_allowed_arr = $this->session->get('profile_id_array');



    if (!in_array($id, $profile_allowed_arr)) {
      $this->session->set("errormessage", "You can edit your own voc only.");
      $this->request->redirect('dashboard/voc_user');
      exit;
    }

    //form save - updated data
    if (isset($_POST["edit_id"])) {
      $profiledata = Model::factory('profiles')->getProfileByIdForMobile($_POST["edit_id"]);

      if ($profiledata['type_id'] != $type_id) {
        $this->session->set("errormessage", "You can edit only voc type.");
        $this->request->redirect('dashboard/voc');
        exit;
      }

      $profile_id = $_POST["edit_id"];
      $code_type = $profiledata["code_type"];

      $post = $_POST;


      $postArr['voc_first_name'] = trim($_POST["voc_first_name"]);
      $postArr['voc_last_name'] = trim($_POST["voc_last_name"]);
      $postArr['voc_address'] = trim($_POST["voc_address"]);
      $postArr['voc_town'] = trim($_POST["voc_town"]);
      $postArr['voc_state'] = trim($_POST["voc_state"]);
      $postArr['voc_phone'] = trim($_POST["voc_phone"]);
      $postArr['form_submission_format'] = $_POST['form_submission_format'];

      if ($_POST["voc_dob"] != "") {
        $voc_dob_arr = explode("/", $_POST["voc_dob"]);
        $postArr['voc_dob'] = $voc_dob_arr[2] . "-" . $voc_dob_arr[1] . "-" . $voc_dob_arr[0];
      } else {
        $postArr['voc_dob'] = "";
      }

      $postArr['voc_known_allergies'] = trim($_POST["voc_known_allergies"]);
      $postArr['voc_blood_type'] = trim($_POST["voc_blood_type"]);
      $postArr['voc_next_of_kin'] = trim($_POST["voc_next_of_kin"]);
      $postArr['voc_contact_phone'] = trim($_POST["voc_contact_phone"]);
      $postArr['voc_employer'] = trim($_POST["voc_employer"]);
      $postArr['voc_emp_address'] = trim($_POST["voc_emp_address"]);
      $postArr['voc_emp_town'] = trim($_POST["voc_emp_town"]);
      $postArr['voc_emp_state'] = trim($_POST["voc_emp_state"]);
      $postArr['voc_emp_phone'] = trim($_POST["voc_emp_phone"]);



      $profileid = Model::factory('voc')->updateVocByUser($postArr, $_POST["edit_id"]);

      for ($k = 0; $k < count($_POST["doc_name"]); $k++) {
        if ($_POST["doc_name"][$k] != "") {
          $doc_expiry_date = "";
          if ($_POST["doc_expiry_date"][$k] != "") {
            $doc_expiry_date_arr = explode("/", $_POST["doc_expiry_date"][$k]);
            $doc_expiry_date = $doc_expiry_date_arr[2] . "-" . $doc_expiry_date_arr[1] . "-" . $doc_expiry_date_arr[0];
          }

          if ($_POST["hdn_existing_doc_id"][$k] > 0) {
            $voc_document_id = $_POST["hdn_existing_doc_id"][$k];
            Model::factory('voc')->update_voc_documents($voc_document_id, $_POST["doc_name"][$k], $doc_expiry_date);
          } else {
            $voc_document_id = Model::factory('voc')->add_voc_documents($profile_id, $_POST["doc_name"][$k], $doc_expiry_date);
          }

          if ($_FILES["document_file"]["size"][$k] > 0) {
            if ($_POST["hdn_existing_doc_id"][$k] > 0) //remove old document
            {
              $document_details = Model::factory('voc')->getDocumentsById($_POST["hdn_existing_doc_id"][$k]);
              if ($document_details["file_name"] != "" && file_exists("images/voc_documents/" . $document_details["file_name"])) {
                unlink("images/voc_documents/" . $document_details["file_name"]);
              }
            }


            $arr  = pathinfo($_FILES["document_file"]["name"][$k]);
            $ext = $arr["extension"];

            $filename = $profile_id . "_" . ($k + 1) . "_doc." . $ext;

            if (move_uploaded_file($_FILES["document_file"]["tmp_name"][$k], "images/voc_documents/" . $filename))
              Model::factory('voc')->update_voc_doc_image_name($voc_document_id, $filename);
          }
        }
      }

      $tmp_files = $this->session->get('tmp_files', FALSE);
      if ($tmp_files) {
        //Model::factory('documents')->saveDocFiles($tmp_files, $_POST["edit_id"]);
        //Model::factory('picture')->savePictureFiles($tmp_files, $_POST["edit_id"]);
        //Model::factory('logo')->saveLogoFile($tmp_files, $_POST["edit_id"]);
        $this->session->set('tmp_files', FALSE);
      }



      $clienturl = Model::factory('client')->geturl($_POST["edit_id"]);
      $qrurl = URL::base() . $clienturl . "/" . $_POST["edit_id"];

      require_once("classes/shortURL.php");
      $api_token = 'KqXZgB6aPc7IkpDB1UlqALKp0v1yhCcjPI40dkprS8UOCypvfYu7KKNrn0QY';
      $short_url = shortenUrl($qrurl, $api_token);
      //$short_url = shortenUrl($qrurl);
      $data = array("shorturl" => $short_url['shortLink']);
      Model::factory('profiles')->updateProfile($_POST["edit_id"], $data);

      if ($code_type == 0) { // QR Code
        //$QRURL = "https://chart.googleapis.com/chart?cht=qr&chs=250x250&chl=" . $short_url['shortLink'] . "&chld=M|0";
	$QRURL = "https://api.qrserver.com/v1/create-qr-code/?data=" . $short_url['shortLink'] . "&size=250x250&ecc=M";

        $QRCode = file_get_contents($QRURL);
        $QRImage = "images/qrcode/CSQRIMG" . $_POST["edit_id"] . ".png";
        $qrcodeid = Model::factory('qrcode')->update_qrcode($_POST["edit_id"], $QRImage);
        $file = fopen(DOCROOT . $QRImage, "w");
        fwrite($file, $QRCode);
        fclose($file);
      } elseif ($code_type == 1) { // Data matrix
        $QRCode = file_get_contents(URL::base() . "dm_code/index.php?data_url=" . $short_url['shortLink'] . "&matrix_size=10&matrix_type=URL&fc=000000&bc=FFFFFF&matrix_block_style=square_blocks");
        //$QRCode = file_get_contents("http://localhost/datamatrix/dm_code/index.php?data_url=http://www,google.com&matrix_size=10&matrix_type=URL&fc=000000&bc=FFFFFF&matrix_block_style=square_blocks");
        $QRImage = "images/dmcode/DMIMG" . $_POST["edit_id"] . ".png";
        $qrcodeid = Model::factory('qrcode')->update_qrcode($_POST["edit_id"], $QRImage);
        $file = fopen(DOCROOT . $QRImage, "w");
        fwrite($file, $QRCode);
        fclose($file);
      }

      $successmessage = "successfully edited VOCC.";
    }
    //end of form save - updated data

    if (isset($id) && $id) :
      $profiledata = Model::factory('profiles')->getProfileByIdForMobile($id);


      if (sizeof($profiledata) == 0) {
        Session::instance()->set('errormessage', 'Oops! Access denied for requested profile.');
        $this->request->redirect(URL::base() . 'dashboard/voc');
      }



      if ($profiledata['type_id'] != $type_id) {
        $this->session->set("errormessage", "You can edit only voc type.");
        $this->request->redirect('dashboard/voc');
        exit;
      }

      if (strtotime($profiledata['expired_at']) < strtotime(date("Y-m-d H:i:s")) && ($profiledata['free_code'] == 0 || ($profiledata['free_code'] == 1 && $profiledata['renewal_required'] == 1))) {
        $expried_or_not = true;
      }

      $post["edit_id"] = $id;
      $post["logo"] = Model::factory('logo')->getLogoById($id);
      $post["documents"] = Model::factory('voc')->getDocumentsByProfileId($id);
      $post["pictures"] = Model::factory('picture')->getPicturesVocByProfileId($id);
      $post["voc_users"] = Model::factory('voc')->get_voc_user_by_profile_id($id);
      $post["voc_recipients"] = Model::factory('voc')->get_voc_recipients_by_profile_id($id);

      $post['txt_code_profile_name'] = $profiledata['code_profile_name'];
      $post['voc_first_name'] = $profiledata['voc_first_name'];
      $post['voc_last_name'] = $profiledata['voc_last_name'];

      $post['form_submission_format'] = $profiledata['form_submission_format'];

      $post['voc_email_url'] = $profiledata["voc_email_url"];
      $post['voc_email_text'] = $profiledata["voc_email_text"];
      $post['voc_title_bar_enable'] = $profiledata["voc_title_bar_enable"];

      $post['voc_title_bar_text'] = $profiledata["voc_title_bar_text"];
      $post['voc_title_bar_colour'] = $profiledata["voc_title_bar_colour"];
      $post['voc_first_name'] = $profiledata["voc_first_name"];
      $post['voc_last_name'] = $profiledata["voc_last_name"];
      $post['voc_address'] = $profiledata["voc_address"];
      $post['voc_town'] = $profiledata["voc_town"];
      $post['voc_state'] = $profiledata["voc_state"];
      $post['voc_phone'] = $profiledata["voc_phone"];

      $voc_dob = explode("-", $profiledata["voc_dob"]);

      if ($profiledata["voc_dob"] == "0000-00-00")
        $post['voc_dob'] = "";
      else
        $post['voc_dob'] = $voc_dob["2"] . "/" . $voc_dob[1] . "/" . $voc_dob[0];

      $post['voc_known_allergies'] = $profiledata["voc_known_allergies"];
      $post['voc_blood_type'] = $profiledata["voc_blood_type"];
      $post['voc_next_of_kin'] = $profiledata["voc_next_of_kin"];
      $post['voc_contact_phone'] = $profiledata["voc_contact_phone"];
      $post['voc_employer'] = $profiledata["voc_employer"];
      $post['voc_emp_address'] = $profiledata["voc_emp_address"];
      $post['voc_emp_town'] = $profiledata["voc_emp_town"];
      $post['voc_emp_state'] = $profiledata["voc_emp_state"];
      $post['voc_emp_phone'] = $profiledata["voc_emp_phone"];


      $post['code_type'] = $profiledata['code_type'];


    /* echo "<pre>";
        print_r ($post);
        exit; */



    else :
      $this->session->set("errormessage", "To edit voc you need to get id");
      $this->request->redirect('dashboard/voc');
      exit;
    endif;
    $qrimg = Model::factory('qrcode')->getQRCodeByProfileId($id);

    // whether to show popup for form builder activation
    $profile_arr = Model::factory('profiles')->getProfileByIdForMobile($id);


    $expired_at = $profile_arr['expired_at_formatted'];
    $activation_start_date = $profile_arr['activation_start_date_formatted'];
    $activation_end_date = $profile_arr['activation_end_date_formatted'];
    $free_code = $profile_arr['free_code'];
    $renewal_required = $profile_arr['renewal_required'];







    //get qr code url   
    $clienturl = Model::factory('client')->geturl($id);
    $qrurl = URL::base() . $clienturl . "/" . $id;




    $this->template->content = View::factory('voc/vocedit')
      ->bind('yes', $yes)
      ->bind('no', $no)
      ->bind('post', $post)
      ->bind('qrimg', $qrimg)
      ->bind('video_per', $this->video_permission)
      ->bind('successmessage', $successmessage)
      ->bind('expried_or_not', $expried_or_not)
      ->bind('type_id', $type_id)
      ->bind('profile_id', $id)
      ->bind('current_url', $current_url)
      ->bind('qrurl', $qrurl)
      ->bind('expired_at', $expired_at)
      ->bind('free_code', $free_code)
      ->bind('renewal_required', $renewal_required);

    $param = 'voc/index';
  }

  public function action_delete()
  {

    if ($this->session->get('voc_user_id') != "") {
      $this->request->redirect(URL::base() . 'dashboard/voc_user');
      exit;
    }
    // Set the Page Title
    $this->template->head->title = 'VOCC Delete';

    $user_data = Model::factory('user')->getUserByID($this->session->get('clientuserid'));

    if ($user_data["is_sub_user"] == 1 && $user_data["access_delete"] == 0) {
      Session::instance()->set('errormessage', 'Oops! You have no access to delete profile.');
      $this->request->redirect('dashboard/voc');
    }

    $id = $this->request->param('id');
    if (isset($id) && $id) :
      $profiledata = Model::factory('profiles')->getProfileById($id);
      if (sizeof($profiledata) == 0) {
        Session::instance()->set('errormessage', 'Oops! Access denied for requested profile.');
        $this->request->redirect(URL::base() . 'dashboard/voc');
      } else {
        if (strtotime($profiledata['expired_at']) < strtotime(date("Y-m-d H:i:s"))  && ($profiledata['free_code'] == 0 || ($profiledata['free_code'] == 1 && $profiledata['renewal_required'] == 1))) {
          $this->session->set("errormessage", "VOCC is expired.You can not delete that VOCC.");
          $this->request->redirect('dashboard/voc');
        } else {

          // delete analytics
          $wpArr = @file_get_contents(GALATECH_API . 'item/clearItem?key=' . $profiledata['analytic_key']);
          $response = json_decode($wpArr);

          Model::factory('profiles')->deleteprofile($id);
          $this->session->set("successmessage", "successfully deleted VOCC.");
          $this->request->redirect('dashboard/voc');
        }
      }
    endif;

    exit;
  }

  public function  action_delete_voc_document()
  {
    $voc_document_id = $_REQUEST["voc_document_id"];

    $document = Model::factory('voc')->getDocumentsById($voc_document_id);

    if ($document["file_name"] != "" && file_exists("images/voc_documents/" . $document["file_name"])) {
      unlink(realpath("images/voc_documents/" . $document["file_name"]));
    }

    Model::factory('voc')->remove_voc_document_by_id($voc_document_id);
  }

  public function action_export_voc_document()
  {
    // set title tag 
    $this->template->head->title = "Export";

    //get voc profiles of this user
    $profiles =  Model::factory('profiles')->get_all_profiles(" user_id='" . $this->session->get('clientuserid') . "' and type_id=12");


    ini_set('include_path', ini_get('include_path') . PATH_SEPARATOR . APPPATH . 'vendor/');
    require("PHPExcel/PHPExcel.php");

    $workbook = new PHPExcel();

    $sheet = $workbook->getActiveSheet();

    $workbook->setActiveSheetIndex(0);

    $workbook->getActiveSheet()->getRowDimension('1')->setRowHeight(30);
    $workbook->getActiveSheet()->getStyle('A1:D1')->getFont()->setBold(TRUE);
    $workbook->getActiveSheet()->getStyle('A1:D1')->getFont()->setSize(14);
    $workbook->getActiveSheet()->getColumnDimension('A')->setWidth(20);

    $workbook->getActiveSheet()->setCellValue('A1', 'Profile No.');
    $workbook->getActiveSheet()->setCellValue('B1', 'Name');
    $workbook->getActiveSheet()->setCellValue('C1', '30 Day Expiry');
    $workbook->getActiveSheet()->setCellValue('D1', 'Expired');

    $workbook->getActiveSheet()->getColumnDimension('A')->setAutoSize(TRUE);
    $workbook->getActiveSheet()->getColumnDimension('B')->setAutoSize(TRUE);
    $workbook->getActiveSheet()->getColumnDimension('C')->setAutoSize(TRUE);
    $workbook->getActiveSheet()->getColumnDimension('D')->setAutoSize(TRUE);


    $row_num = 3;
    foreach ($profiles as $profile) {
      $profile_id = $profile["id"];
      $voc_first_name = $profile["voc_first_name"];
      $voc_last_name = $profile["voc_last_name"];

      $document_arr = Model::factory('voc')->get_expiry_document_with_condition(" profile_id='" . $profile_id . "' and (DATEDIFF(expiry_date,curdate()))<=30");

      foreach ($document_arr as $document) {
        $workbook->getActiveSheet()->getStyle("A" . $row_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

        $workbook->getActiveSheet()->setCellValue("A" . $row_num, $profile_id);
        $workbook->getActiveSheet()->setCellValue("B" . $row_num, $voc_first_name . " " . $voc_last_name);

        if ($document["diff_date_expiry"] > 0 && $document["diff_date_expiry"] <= 30)
          $workbook->getActiveSheet()->setCellValue("C" . $row_num, 'Yes');
        else
          $workbook->getActiveSheet()->setCellValue("C" . $row_num, '');

        if ($document["diff_date_expiry"] <= 0)
          $workbook->getActiveSheet()->setCellValue("D" . $row_num, 'Yes');
        else
          $workbook->getActiveSheet()->setCellValue("D" . $row_num, '');
        $row_num++;
      }
    }
    //exit;



    //set background color 
    $workbook->getActiveSheet()->getStyle('A1:D1')->getFill()->applyFromArray(array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'startcolor' => array('rgb' => 'F7E1D2')));
    $workbook->getActiveSheet()->getStyle('A1:D1')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);







    $writer = PHPExcel_IOFactory::createWriter($workbook, 'Excel5');
    $writer->save('php://output');
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="document_list.xls"');
    header('Cache-Control: max-age=0');
    exit;
  }

  public function action_email_template_view()
  {
    // set title tag 
    $this->template->head = View::factory('template/form_builder_head');

    $this->template->head->javascripts = array();
    $this->template->head->stylesheets = array();

    $this->template->head->title = "Email Template";
    $this->template->head->stylesheets = array(array('href' => 'styles/form-details.css', 'media' => 'screen'));

    $this->template->headers = "";
    $this->template->footer = "";


    $profile_id = $this->request->query("profile_id");
    $section = $this->request->query("section");


    $logo = Model::factory('logo')->getLogoByProfileId($profile_id);

    $profile_logo = "";

    if (!empty($logo) && isset($logo["logo_name"]) && file_exists($logo["logo_name"])) {
      $profile_logo = $logo["logo_name"];
    }

    $profile =  Model::factory('profiles')->getProfileByIdForMobile($profile_id);

    // Default Content template
    $this->template->content = View::factory('voc/email_template_preview')
      ->bind('profile_id', $profile_id)
      ->bind('profile', $profile)
      ->bind('profile_logo', $profile_logo)
      ->bind('section', $section);
  }
}

// End Welcome
