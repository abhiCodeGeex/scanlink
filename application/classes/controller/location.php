<?php
error_reporting(0);

defined('SYSPATH') or die('No direct script access.');

class Controller_Location extends Controller_Template_Master
{

  public $types = '';
  public $userid = '';
  public $video_permission = 0;
  public $client_id = '';

  public function before()
  {

    parent::before();

    if (!$this->session->get('clientuserid')) :
      $this->request->redirect(URL::base() . 'welcome/index');
    endif;
    // Default Head view file
    $this->template->head = View::factory('template/head');

    // Set the default Page Title
    $this->template->head->title = 'Welcome to ScanLink';

    // All CSS Stylesheets
    $this->template->head->stylesheets = array(array('href' => 'styles/style.css', 'media' => 'screen'), array('href' => 'styles/colorbox.css', 'media' => 'screen'), array('href' => 'styles/coin-slider-styles.css', 'media' => 'screen'), array('href' => 'styles/ui/jquery.ui.tabs.css', 'media' => 'screen'));

    // All JS Libraries and Scripts
    $this->template->head->javascripts = array('js/jquery.min.js', 'js/jquery.validate.min.js', 'js/jquery.colorbox.js', 'js/jquery.autogrow-textarea.js', 'js/coin-slider.min.js', 'js/dashboard/tooltip.js', 'js/dashboard/jquery-ui-1.js', 'js/dashboard/custom.js', 'js/jquery.uniform.js', 'js/custome.js', 'js/modernizr.custom.60079.js', 'js/jscolor/jscolor.js', 'js/jquery.maxlength-min.js', 'js/deleteLogo.js', 'js/profile_page_common.js');

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

    $this->type_id = 2;
  }

  public function action_index()
  {
    $videos = array();
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
      $this->request->redirect('dashboard/location');
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
    // $existing_forms_arr = Model::factory('Formbuilder')->get_existing_forms($profile_id, $this->userid);
    //existing forms
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
      $this->request->redirect('dashboard/location');
      exit;
    }

    $form_active = $profile_arr['form_active'];
    $form_is_enable = $profile_arr['form_is_enable'];
    $form_id = $profile_arr['form_id'];
    $form_name = $profile_arr['form_title'];
    $expired_at = $profile_arr['expired_at_formatted'];
    $free_code = $profile_arr['free_code'];
    $renewal_required = $profile_arr['renewal_required'];
    $form_email_tag = !empty($_REQUEST["form_email_tag"]) ? $_REQUEST["form_email_tag"] : '';

    if (isset($profile_arr['pop_up_formbuilder']) && $profile_arr['pop_up_formbuilder'] == '1') {
      $datafill = array();
      $datafill['pop_up_formbuilder'] = '0';
      $datafill['form_email_tag'] = $form_email_tag;
      Model::factory('profiles')->updateProfile($profile_id, $datafill);
      $formbuilder_popup_show = true;
    } else {
      $formbuilder_popup_show = false;
    }


    // Set the Page Title
    $this->template->head->title = 'Location  Add';

    if (!empty($_POST) && !isset($_POST["edit_id"])) : //form submitted

      /* if(empty($profile_id_get)){
        $this->request->redirect('dashboard/location');
        exit;
        }
       */



      $profile_id = isset($_POST['profile_id']) ? $_POST['profile_id'] : '';
      if ($profile_id == '') {
        $this->request->redirect('dashboard/index');
        exit;
      }

      /// update is_temp field for logo, pictures, documents uploaded
      Model::factory('documents')->update_temp_field($profile_id);
      Model::factory('picture')->update_temp_field($profile_id);
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
          $datafill['form_is_enable'] = $enable_form;
          $datafill['form_email_tag'] = $form_email_tag;
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
      $txtname = isset($_POST['txtname']) ? trim($_POST['txtname']) : '';
      $txtvideos = isset($_POST['video']) ? trim($_POST['video']) : '';

      $txtaddress = isset($_POST['txtaddress']) ? trim($_POST['txtaddress']) : '';
      $txtdesc = isset($_POST['txtdesc']) ? trim($_POST['txtdesc']) : '';
      $txtnotes = isset($_POST['txtnotes']) ? trim($_POST['txtnotes']) : '';

      $form_submission_format = isset($_POST['form_submission_format']) ? trim($_POST['form_submission_format']) : '';

      //$txtcompname = trim($_POST['txtcompname']);
      //$txttelephone = trim($_POST['txttelephone']);
      $txtcompname = isset($_POST['txtcompname']) ? $_POST['txtcompname'] : array();
      $txttelephone = isset($_POST['txttelephone']) ? $_POST['txttelephone'] : array();

      $txtbuttonbackcolor = isset($_POST['txtbuttonbackcolor']) ? trim($_POST['txtbuttonbackcolor']) : '';
      $txtbuttonfontcolor = isset($_POST['txtbuttonfontcolor']) ? trim($_POST['txtbuttonfontcolor']) : '';

      $link_button = isset($_POST['link_button']) ? $_POST['link_button'] : array();
      $link_button_text = isset($_POST['link_button_text']) ? $_POST['link_button_text'] : array();
      $link_button_url = isset($_POST['link_button_url']) ? $_POST['link_button_url'] : array();
      $link_button_color = isset($_POST['link_button_color']) ? $_POST['link_button_color'] : array();

      $link_button_align = array();

      for ($k = 0; $k < count($_POST['link_button_text']); $k++) {
        array_push($link_button_align, $_POST['link_button_align_' . ($k + 1)]);
      }

      $link_button_active = array();

      for ($k = 0; $k < count($_POST['link_button_text']); $k++) {
        array_push($link_button_active, $_POST['link_button_active_' . ($k + 1)]);
      }


      /*
        $link_button = isset($_POST['link_button']) ? '1' : '0';
        $link_button_text = trim($_POST['link_button_text']);
        $link_button_url = trim($_POST['link_button_url']);
        $link_button_color = trim($_POST['link_button_color']);
       */

      $display_share_link = isset($_POST['display_share_link']) ? $_POST['display_share_link'] : '';

      $set_up_compulsory = isset($_POST['set_up_compulsory']) ? $_POST['set_up_compulsory'] : '';
      $data_collection_mobile = isset($_POST['data_collection_mobile']) ? $_POST['data_collection_mobile'] : '0';
      $data_collection_email = isset($_POST['data_collection_email']) ? $_POST['data_collection_email'] : '0';
      $data_collection_name = isset($_POST['data_collection_name']) ? $_POST['data_collection_name'] : '0';
      $data_collection_surname = isset($_POST['data_collection_surname']) ? $_POST['data_collection_surname'] : '0';


      $data_collection_content = trim($_POST['data_collection_content']);

      $videos1 = explode(",", $txtvideos);
      foreach ($videos1 as $key => $value) {
        if ($value != '') {
          $value = explode("=", $value);
          if (count($value) <= 2) {
            if ($value[1] != '')
              $videos[$key] = array("title" => $value[0], "id" => $value[1]);
          } else {
            if ($value[2] != '')
              $videos[$key] = array("title" => $value[0], "id" => $value[2]);
          }
        }
      }
      //array_pop($videos);


      $protect = $_POST['protect'];
      $password = $_POST['password'];
      $show_header = isset($_POST['show_header']) ? 1 : 0;
      $code_type = $_POST['code_type'];
      $enable_data_collection = $_POST['enable_data_collection'];

      /* if ($txtname == ''):
        $errors['txtname'] = "Please provide name for Location";
      else:*/
      $postArr['type'] = 'location';
      $postArr['txtname'] = $txtname;
      $postArr['code_profile_name'] = $txt_code_profile_name;

      $postArr['txtaddress'] = $txtaddress;
      $postArr['txtdesc'] = $txtdesc;
      $postArr['txtnotes'] = $txtnotes;
      $postArr['txtcompname'] = $txtcompname;
      $postArr['txttelephone'] = $txttelephone;

      $postArr['form_submission_format'] = $form_submission_format;

      $postArr['txtbuttonbackcolor'] = $txtbuttonbackcolor;
      $postArr['txtbuttonfontcolor'] = $txtbuttonfontcolor;

      $postArr['link_button'] = $link_button;
      $postArr['link_button_text'] = $link_button_text;
      $postArr['link_button_url'] = $link_button_url;
      $postArr['link_button_color'] = $link_button_color;
      $postArr['link_button_align'] = $link_button_align;
      $postArr['link_button_active'] = $link_button_active;

      $postArr['protect'] = $protect;
      $postArr['password'] = $password;
      $postArr['show_header'] = $show_header;
      $postArr['code_type'] = $code_type;
      $postArr['enable_data_collection'] = $enable_data_collection;
      $postArr['created_at'] = date('Y-m-d H:i:s');

      $postArr['videos'] = $videos;
      $postArr['checklist_items'] = $checklist_items;

      $postArr['display_share_link'] = $display_share_link;

      $postArr['set_up_compulsory'] = $set_up_compulsory;
      $postArr['data_collection_mobile'] = $data_collection_mobile;
      $postArr['data_collection_email'] = $data_collection_email;
      $postArr['data_collection_name'] = $data_collection_name;
      $postArr['data_collection_surname'] = $data_collection_surname;

      $postArr['data_collection_content'] = $data_collection_content;

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



      $profileid = Model::factory('location')->insertLocation($postArr, $this->userid, $profile_id);
      $tmp_files = $this->session->get('tmp_files', FALSE);
      if ($tmp_files) {
        //Model::factory('documents')->saveDocFiles($tmp_files, $profileid);
        //Model::factory('picture')->savePictureFiles($tmp_files, $profileid);
        //Model::factory('logo')->saveLogoFile($tmp_files, $profileid);
        $this->session->set('tmp_files', FALSE);
      }

      $this->session->delete('form_builder_referer');

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
      $this->session->set("successmessage", "successfully added location.");
      $this->request->redirect('dashboard/location');
      exit;
    /* endif;*/
    elseif (!empty($_POST) && isset($_POST["edit_id"])) : ///edit mode
      $profiledata = Model::factory('profiles')->getProfileById($_POST["edit_id"]);

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
      Model::factory('documents')->update_temp_field($profile_id);
      Model::factory('picture')->update_temp_field($profile_id);
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
          $datafill['form_is_enable'] = $enable_form;
          $datafill['enable_form_analytics'] = $enable_form_analytics;
          Model::factory('profiles')->updateProfile($profile_id, $datafill);

          $form_id_current = $new_form_id;
        } else {
          $datafill = array();
          $datafill['form_title'] = $form_name;
          $datafill['form_is_enable'] = $enable_form;
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
        $datafill['form_is_enable'] = $enable_form;
        Model::factory('profiles')->updateProfile($profile_id, $datafill);
      }
      //for form builder related entry end


      //if ($profiledata['user_id'] != $this->userid) {
      if ($profiledata['client_id'] != $this->session->get('client_id')) {
        $this->session->set("errormessage", "You can edit your own location  only.");
        $this->request->redirect('dashboard/location');
        exit;
      }

      if ($profiledata['type_id'] != $type_id) {
        $this->session->set("errormessage", "You can edit only location  type.");
        $this->request->redirect('dashboard/location');
        exit;
      }

      $post = $_POST;


      $txt_code_profile_name = isset($_POST['txt_code_profile_name']) ? trim($_POST['txt_code_profile_name']) : '';
      $txtname = isset($_POST['txtname']) ? trim($_POST['txtname']) : '';
      $txtvideos = isset($_POST['video']) ? trim($_POST['video']) : '';

      $txtaddress = isset($_POST['txtaddress']) ? trim($_POST['txtaddress']) : '';
      $txtdesc = isset($_POST['txtdesc']) ? trim($_POST['txtdesc']) : '';
      $txtnotes = isset($_POST['txtnotes']) ? trim($_POST['txtnotes']) : '';
      $txtcompname = isset($_POST['txtcompname']) ? $_POST['txtcompname'] : array();
      $txttelephone = isset($_POST['txttelephone']) ? $_POST['txttelephone'] : array();

      $txtbuttonbackcolor = isset($_POST['txtbuttonbackcolor']) ? trim($_POST['txtbuttonbackcolor']) : '';
      $txtbuttonfontcolor = isset($_POST['txtbuttonfontcolor']) ? trim($_POST['txtbuttonfontcolor']) : '';

      $form_submission_format = isset($_POST['form_submission_format']) ? trim($_POST['form_submission_format']) : '';


      $link_button = isset($_POST['link_button']) ? $_POST['link_button'] : array();
      $link_button_text = isset($_POST['link_button_text']) ? $_POST['link_button_text'] : array();
      $link_button_url = isset($_POST['link_button_url']) ? $_POST['link_button_url'] : array();
      $link_button_color = isset($_POST['link_button_color']) ? $_POST['link_button_color'] : array();
      /*
        $link_button = isset($_POST['link_button']) ? '1' : '0';
        $link_button_text = trim($_POST['link_button_text']);
        $link_button_url = trim($_POST['link_button_url']);
        $link_button_color = trim($_POST['link_button_color']);
       */

      $link_button_align = array();

      for ($k = 0; $k < count($_POST['link_button_text']); $k++) {
        array_push($link_button_align, $_POST['link_button_align_' . ($k + 1)]);
      }

      $link_button_active = array();

      for ($k = 0; $k < count($_POST['link_button_text']); $k++) {
        array_push($link_button_active, $_POST['link_button_active_' . ($k + 1)]);
      }

      $display_share_link = isset($_POST['display_share_link']) ? $_POST['display_share_link'] : '';

      $set_up_compulsory = isset($_POST['set_up_compulsory']) ? $_POST['set_up_compulsory'] : '';
      $data_collection_mobile = isset($_POST['data_collection_mobile']) ? $_POST['data_collection_mobile'] : '0';
      $data_collection_email = isset($_POST['data_collection_email']) ? $_POST['data_collection_email'] : '0';
      $data_collection_name = isset($_POST['data_collection_name']) ? $_POST['data_collection_name'] : '0';
      $data_collection_surname = isset($_POST['data_collection_surname']) ? $_POST['data_collection_surname'] : '0';

      $data_collection_content = isset($_POST['data_collection_content']) ? trim($_POST['data_collection_content']) : '';

      $checklist_items = (isset($_POST['checklist_item']) && !empty($_POST['checklist_item'])) ? $_POST['checklist_item'] : array();
      $checklist_item_ids = (isset($_POST['checklist_item_id']) && !empty($_POST['checklist_item_id'])) ? $_POST['checklist_item_id'] : array();

      $videos1 = explode(",", $txtvideos);
      foreach ($videos1 as $key => $value) {
        if ($value != '') {
          $value = explode("=", $value);
          if ((count($value)) <= 2) {
            if ($value[1] != '') {
              $videos[$key] = array("title" => $value[0], "id" => $value[1]);
            }
          } else {
            if ($value[2] != '') {
              $videos[$key] = array("title" => $value[0], "id" => $value[2]);
            }
          }
        }
      }

      //array_pop($videos);


      $protect = $_POST['protect'];
      $password = $_POST['password'];
      $show_header = $_POST['show_header'];
      $code_type = $_POST['code_type'];
      $enable_data_collection = $_POST['enable_data_collection'];

      /*if ($txtname == ''):
        $errors['txtname'] = "Please provide name for Location";
      else:*/
      $postArr['type'] = 'asset';
      $postArr['txtname'] = $txtname;
      $postArr['code_profile_name'] = $txt_code_profile_name;

      $postArr['txtaddress'] = $txtaddress;
      $postArr['txtdesc'] = $txtdesc;

      $postArr['videos'] = $videos;

      $postArr['txtnotes'] = $txtnotes;
      $postArr['txtcompname'] = $txtcompname;
      $postArr['txttelephone'] = $txttelephone;

      $postArr['form_submission_format'] = $form_submission_format;

      $postArr['txtbuttonbackcolor'] = $txtbuttonbackcolor;
      $postArr['txtbuttonfontcolor'] = $txtbuttonfontcolor;

      $postArr['link_button'] = $link_button;
      $postArr['link_button_text'] = $link_button_text;
      $postArr['link_button_url'] = $link_button_url;
      $postArr['link_button_color'] = $link_button_color;
      $postArr['link_button_align'] = $link_button_align;
      $postArr['link_button_active'] = $link_button_active;

      $postArr['protect'] = $protect;
      $postArr['password'] = $password;
      $postArr['show_header'] = $show_header;
      $postArr['code_type'] = $code_type;
      $postArr['enable_data_collection'] = $enable_data_collection;
      $postArr['created_at'] = date('Y-m-d H:i:s');

      $postArr['checklist_items'] = $checklist_items;
      $postArr['checklist_item_ids'] = $checklist_item_ids;

      $postArr['display_share_link'] = $display_share_link;

      $postArr['set_up_compulsory'] = $set_up_compulsory;
      $postArr['data_collection_mobile'] = $data_collection_mobile;
      $postArr['data_collection_email'] = $data_collection_email;
      $postArr['data_collection_name'] = $data_collection_name;
      $postArr['data_collection_surname'] = $data_collection_surname;
      $postArr['data_collection_content'] = $data_collection_content;

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

      $profileid = Model::factory('location')->updateLocation($postArr, $_POST["edit_id"]);
      $tmp_files = $this->session->get('tmp_files', FALSE);
      if ($tmp_files) {
        //Model::factory('documents')->saveDocFiles($tmp_files, $_POST["edit_id"]);
        // Model::factory('picture')->savePictureFiles($tmp_files, $_POST["edit_id"]);
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
      $this->session->set("successmessage", "successfully edited location.");
      $this->request->redirect('location/edit/' . $_POST["edit_id"]);
      exit;
    /* endif;*/
    endif;

    $checklist_option = Model::factory('user')->getChecklistOption($this->client_id);

    if ($this->session->get('form_builder_referer', FALSE) != FALSE) //load values when auto save after activate form builder
    {
      $profiledata = Model::factory('profiles')->getProfileById($profile_id);


      $post["logo"] = Model::factory('logo')->get_logo_by_id_for_autosave($profile_id);
      $post["pictures"] = Model::factory('picture')->get_pictures_by_id_for_autosave($profile_id);
      $post["documents"] = Model::factory('documents')->get_documents_by_id_for_autosave($profile_id);
      $post["videos"] = Model::factory('video')->getVideoByProfileId($profile_id);
      $post["weblink"] = Model::factory('weblink')->getWeblinkByProfileId($profile_id);
      $post["contact"] = Model::factory('profilecontact')->getContactByProfileId($profile_id);

      $post['txtname'] = $profiledata['name'];
      $post['txt_code_profile_name'] = $profiledata['code_profile_name'];
      $post['txtaddress'] = $profiledata['address'];
      $post['txtdesc'] = $profiledata['description'];
      $post['txtnotes'] = $profiledata['notes'];
      $post['txtcompname'] = $profiledata['name_company'];
      $post['txttelephone'] = $profiledata['telephone'];

      $post['txtbuttonbackcolor'] = $profiledata['buttonbackcolor'];
      $post['txtbuttonfontcolor'] = $profiledata['buttonfontcolor'];

      $post['link_button'] = $profiledata['link_button'];
      $post['link_button_text'] = $profiledata['link_button_text'];
      $post['link_button_url'] = $profiledata['link_button_url'];
      $post['link_button_color'] = $profiledata['link_button_color'];

      $post['protect'] = $profiledata['protect'];
      $post['password'] = $profiledata['password'];
      $post['show_header'] = $profiledata['show_header'];
      $post['code_type'] = $profiledata['code_type'];

      $post["display_share_link"] = $profiledata['display_share_link'];

      $post['enable_data_collection'] = $profiledata['enable_data_collection'];
      $post["set_up_compulsory"] = $profiledata['set_up_compulsory'];
      $post["data_collection_mobile"] = $profiledata['data_collection_mobile'];
      $post["data_collection_email"] = $profiledata['data_collection_email'];
      $post["data_collection_name"] = $profiledata['data_collection_name'];
      $post["data_collection_surname"] = $profiledata['data_collection_surname'];

      $post["data_collection_content"] = $profiledata['data_collection_content'];


      if ($post['protect'] == '1') :
        $yes = TRUE;
        $no = FALSE;
      else :
        $yes = FALSE;
        $no = TRUE;
      endif;
    }

    $this->session->delete('tmp_files');
    $param = 'location/index';

    //participant list
    $add_edit_participant_list_arr = Model::factory('Formbuilder')->get_participant_list($profile_id);
    $participant_recipient_list_arr = Model::factory('Formbuilder')->get_participant_recipient_list($profile_id);
    $add_edit_participant_list_popup_show = true;

    // Default Content template
    $this->template->content = View::factory('location/index')
      ->bind('post', $post)
      ->bind('video_per', $this->video_permission)
      ->bind('checklist_option', $checklist_option[0]['checklist_option'])
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

    $this->template->content->web_link_data = View::factory('template/weblink_template')
      ->bind('post', $post)
      ->bind('errors', $errors)
      ->bind('client_id', $this->client_id)
      ->bind('profile_id', $id);

    $this->template->content->contact_data = View::factory('template/contact_template')
      ->bind('post', $post)
      ->bind('errors', $errors)
      ->bind('client_id', $this->client_id)
      ->bind('profile_id', $id);

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
    $this->template->head->title = 'Location  Add';

    $errors = '';
    $succmsg = '';
    $last_id = array();
    $postArr = array();
    if (isset($_POST) && $_POST) :
      $logo_file = $_FILES['logo_file']['name'];
      $txtname = trim($_POST['txtname']);
      $txt_code_profile_name = trim($_POST['txt_code_profile_name']);
      $txtidenty = trim($_POST['txtidenty']);
      $txtsrno = trim($_POST['txtsrno']);
      $txtdesc = trim($_POST['txtdesc']);
      $txtnotes = trim($_POST['txtnotes']);
      $txtcompname = trim($_POST['txtcompname']);
      $txttelephone = trim($_POST['txttelephone']);

      $txtbuttonbackcolor = $_POST['txtbuttonbackcolor'];
      $txtbuttonfontcolor = $_POST['txtbuttonfontcolor'];

      $form_submission_format = $_POST['form_submission_format'];

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

    // Set the Page Title
    $this->template->head->title = 'Location  Edit';

    $user_data = Model::factory('user')->getUserByID($this->session->get('clientuserid'));

    if ($user_data["is_sub_user"] == 1 && $user_data["access_edit"] == 0) {
      Session::instance()->set('errormessage', 'Oops! You have no access to edit profile.');
      $this->request->redirect('dashboard/location');
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
        $this->request->redirect(URL::base() . 'dashboard/location');
      }

      //if ($profiledata['user_id'] != $this->userid) {
      if ($profiledata['client_id'] != $this->session->get('client_id')) {
        $this->session->set("errormessage", "You can edit your own location  only.");
        $this->request->redirect('dashboard/location');
        exit;
      }

      if ($profiledata['type_id'] != $type_id) {
        $this->session->set("errormessage", "You can edit only location type.");
        $this->request->redirect('dashboard/location');
        exit;
      }

      if (strtotime($profiledata['expired_at']) < strtotime(date("Y-m-d H:i:s")) && ($profiledata['free_code'] == 0 || ($profiledata['free_code'] == 1 && $profiledata['renewal_required'] == 1))) {
        $expried_or_not = true;
      }

      $post["edit_id"] = $id;
      $post["logo"] = Model::factory('logo')->getLogoById($id);
      $post["pictures"] = Model::factory('picture')->getPicturesByProfileId($id);
      $post["documents"] = Model::factory('documents')->getDocumentsByProfileId($id);
      $post["videos"] = Model::factory('video')->getVideoByProfileId($id);
      $post["weblink"] = Model::factory('weblink')->getWeblinkByProfileId($id);
      $post["contact"] = Model::factory('profilecontact')->getContactByProfileId($id);

      $post['txtname'] = $profiledata['name'];
      $post['txt_code_profile_name'] = $profiledata['code_profile_name'];
      $post['txtaddress'] = $profiledata['address'];
      $post['txtdesc'] = $profiledata['description'];
      $post['txtnotes'] = $profiledata['notes'];
      $post['txtcompname'] = $profiledata['name_company'];
      $post['txttelephone'] = $profiledata['telephone'];

      $post['form_submission_format'] = $profiledata['form_submission_format'];

      $post['txtbuttonbackcolor'] = $profiledata['buttonbackcolor'];
      $post['txtbuttonfontcolor'] = $profiledata['buttonfontcolor'];

      $post['link_button'] = $profiledata['link_button'];
      $post['link_button_text'] = $profiledata['link_button_text'];
      $post['link_button_url'] = $profiledata['link_button_url'];
      $post['link_button_color'] = $profiledata['link_button_color'];

      $post['protect'] = $profiledata['protect'];
      $post['password'] = $profiledata['password'];
      $post['show_header'] = $profiledata['show_header'];
      $post['code_type'] = $profiledata['code_type'];

      $post["display_share_link"] = $profiledata['display_share_link'];

      $post['enable_data_collection'] = $profiledata['enable_data_collection'];
      $post["set_up_compulsory"] = $profiledata['set_up_compulsory'];
      $post["data_collection_mobile"] = $profiledata['data_collection_mobile'];
      $post["data_collection_email"] = $profiledata['data_collection_email'];
      $post["data_collection_name"] = $profiledata['data_collection_name'];
      $post["data_collection_surname"] = $profiledata['data_collection_surname'];

      $post["data_collection_content"] = $profiledata['data_collection_content'];


      if ($post['protect'] == '1') :
        $yes = TRUE;
        $no = FALSE;
      else :
        $yes = FALSE;
        $no = TRUE;
      endif;
    else :
      $this->session->set("errormessage", "To edit location you need to get id");
      $this->request->redirect('dashboard/location');
      exit;
    endif;


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
    //$existing_forms_arr = Model::factory('Formbuilder')->get_existing_forms($id, $this->userid);
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

    $checklist_items = Model::factory('checklistitem')->getChecklist_itemsByProfileId($id);

    $qrimg = Model::factory('qrcode')->getQRCodeByProfileId($id);
    $checklist_option = Model::factory('user')->getChecklistOption($this->client_id);

    //get qr code url   
    $clienturl = Model::factory('client')->geturl($id);
    $qrurl = URL::base() . $clienturl . "/" . $id;

    //participant list
    $add_edit_participant_list_arr = Model::factory('Formbuilder')->get_participant_list($id);

    $participant_recipient_list_arr = Model::factory('Formbuilder')->get_participant_recipient_list($id);
    $add_edit_participant_list_popup_show = true;

    $this->template->content = View::factory('location/edit')
      ->bind('yes', $yes)
      ->bind('no', $no)
      ->bind('post', $post)
      ->bind('checklist_items', $checklist_items)
      ->bind('checklist_option', $checklist_option[0]['checklist_option'])
      ->bind('video_per', $this->video_permission)
      ->bind('qrimg', $qrimg)
      ->bind('successmessage', $successmessage)
      ->bind('expried_or_not', $expried_or_not)
      ->bind('profile_id', $id)
      ->bind('type_id', $type_id)
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
    $param = 'location/edit';

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

    $this->template->content->contact_data = View::factory('template/contact_template')
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

  public function action_delete()
  {

    if ($this->session->get('voc_user_id') != "") {
      $this->request->redirect(URL::base() . 'dashboard/voc_user');
      exit;
    }

    // Set the Page Title
    $this->template->head->title = 'Location  Delete';

    $user_data = Model::factory('user')->getUserByID($this->session->get('clientuserid'));

    if ($user_data["is_sub_user"] == 1 && $user_data["access_delete"] == 0) {
      Session::instance()->set('errormessage', 'Oops! You have no access to delete profile.');
      $this->request->redirect('dashboard/location');
    }



    $id = $this->request->param('id');
    if (isset($id) && $id) :
      $profiledata = Model::factory('profiles')->getProfileById($id);
      if (sizeof($profiledata) == 0) {
        Session::instance()->set('errormessage', 'Oops! Access denied for requested profile.');
        $this->request->redirect(URL::base() . 'dashboard/location');
      } else {
        if (strtotime($profiledata['expired_at']) < strtotime(date("Y-m-d H:i:s")) && ($profiledata['free_code'] == 0 || ($profiledata['free_code'] == 1 && $profiledata['renewal_required'] == 1))) {
          $this->session->set("errormessage", "Location is expired.You can not delete that location.");
          $this->request->redirect('dashboard/location');
        } else {

          // delete analytics
          $wpArr = @file_get_contents(GALATECH_API . 'item/clearItem?key=' . $profiledata['analytic_key']);
          $response = json_decode($wpArr);

          Model::factory('profiles')->deleteprofile($id);
          $this->session->set("successmessage", "successfully deleted location.");
          $this->request->redirect('dashboard/location');
        }
      }

    endif;

    exit;
  }

  public function action_auto_save_location()
  {
    $post = $_REQUEST;
    $action = $_REQUEST["action"];
    $profile_id = $_REQUEST["profile_id"];
    $current_url = $_REQUEST["current_url"];


    $txt_code_profile_name = isset($post['txt_code_profile_name']) ? trim($post['txt_code_profile_name']) : '';
    $txtname = isset($post['txtname']) ? trim($post['txtname']) : '';
    $txtvideos = isset($post['video']) ? trim($post['video']) : '';

    $txtaddress = isset($post['txtaddress']) ? trim($post['txtaddress']) : '';
    $txtdesc = isset($post['txtdesc']) ? trim($post['txtdesc']) : '';
    $txtnotes = isset($post['txtnotes']) ? trim($post['txtnotes']) : '';

    $txtcompname = isset($post['txtcompname']) ? $post['txtcompname'] : array();
    $txttelephone = isset($post['txttelephone']) ? $post['txttelephone'] : array();

    $txtbuttonbackcolor = isset($post['txtbuttonbackcolor']) ? trim($post['txtbuttonbackcolor']) : '';
    $txtbuttonfontcolor = isset($post['txtbuttonfontcolor']) ? trim($post['txtbuttonfontcolor']) : '';

    $link_button = isset($post['link_button']) ? $post['link_button'] : array();
    $link_button_text = isset($post['link_button_text']) ? $post['link_button_text'] : array();
    $link_button_url = isset($post['link_button_url']) ? $post['link_button_url'] : array();
    $link_button_color = isset($post['link_button_color']) ? $post['link_button_color'] : array();


    $display_share_link = isset($post['display_share_link']) ? $post['display_share_link'] : '';

    $set_up_compulsory = isset($post['set_up_compulsory']) ? $post['set_up_compulsory'] : '';
    $data_collection_mobile = isset($post['data_collection_mobile']) ? $post['data_collection_mobile'] : '0';
    $data_collection_email = isset($post['data_collection_email']) ? $post['data_collection_email'] : '0';
    $data_collection_name = isset($post['data_collection_name']) ? $post['data_collection_name'] : '0';
    $data_collection_surname = isset($post['data_collection_surname']) ? $post['data_collection_surname'] : '0';


    $data_collection_content = trim($post['data_collection_content']);

    $videos1 = explode(",", $txtvideos);
    foreach ($videos1 as $key => $value) {

      if ($value != '') {
        $value = explode("=", $value);
        if (count($value) <= 2) {
          if ($value[1] != '')
            $videos[$key] = array("title" => $value[0], "id" => $value[1]);
        } else {
          if ($value[2] != '')
            $videos[$key] = array("title" => $value[0], "id" => $value[2]);
        }
      }
    }


    $protect = $post['protect'];
    $password = $post['password'];
    $show_header = isset($post['show_header']) ? 1 : 0;
    $code_type = $post['code_type'];
    $enable_data_collection = $post['enable_data_collection'];


    $postArr['type'] = 'location';
    $postArr['txtname'] = $txtname;
    $postArr['code_profile_name'] = $txt_code_profile_name;
    $postArr['txtaddress'] = $txtaddress;
    $postArr['txtdesc'] = $txtdesc;
    $postArr['txtnotes'] = $txtnotes;
    $postArr['txtcompname'] = $txtcompname;
    $postArr['txttelephone'] = $txttelephone;

    $postArr['txtbuttonbackcolor'] = $txtbuttonbackcolor;
    $postArr['txtbuttonfontcolor'] = $txtbuttonfontcolor;

    $postArr['link_button'] = $link_button;
    $postArr['link_button_text'] = $link_button_text;
    $postArr['link_button_url'] = $link_button_url;
    $postArr['link_button_color'] = $link_button_color;

    $postArr['protect'] = $protect;
    $postArr['password'] = $password;
    $postArr['show_header'] = $show_header;
    $postArr['code_type'] = $code_type;
    $postArr['enable_data_collection'] = $enable_data_collection;
    $postArr['created_at'] = date('Y-m-d H:i:s');

    $postArr['videos'] = $videos;
    $postArr['checklist_items'] = (isset($post['checklist_item']) && !empty($post['checklist_item'])) ? $post['checklist_item'] : array();

    $postArr['display_share_link'] = $display_share_link;

    $postArr['set_up_compulsory'] = $set_up_compulsory;
    $postArr['data_collection_mobile'] = $data_collection_mobile;
    $postArr['data_collection_email'] = $data_collection_email;
    $postArr['data_collection_name'] = $data_collection_name;
    $postArr['data_collection_surname'] = $data_collection_surname;

    $postArr['data_collection_content'] = $data_collection_content;



    if ($action == "add") {
      $profileid = Model::factory('location')->insertLocation($postArr, $this->userid, $profile_id, "0");
    } elseif ($action == "edit") {
      $profileid = Model::factory('location')->updateLocation($postArr, $profile_id);
    }



    $tmp_files = $this->session->get('tmp_files', FALSE);

    if ($tmp_files) {
      $this->session->set('tmp_files', FALSE);
    }

    $this->request->redirect(URL::base() . '/formbuilder/active_formbuilder/?current_url=' . $current_url . '&profile_id=' . $profile_id);
  } //end of function action_auto_save_location

}

// End Welcome
