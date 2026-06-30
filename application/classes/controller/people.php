<?php

defined('SYSPATH') or die('No direct script access.');

class Controller_People extends Controller_Template_Master {

  public $types = '';
  public $userid = '';
  public $video_permission = 0;
  public $client_id = '';

  public function before() {

    parent::before();
    if (!$this->session->get('clientuserid')):
      $this->request->redirect(URL::base() . 'welcome/index');
    endif;
    // Default Head view file
    $this->template->head = View::factory('template/head');

    // Set the default Page Title
    $this->template->head->title = 'Welcome to ScanLink';

    // All CSS Stylesheets
    $this->template->head->stylesheets = array(array('href' => 'styles/style.css', 'media' => 'screen'), array('href' => 'styles/colorbox.css', 'media' => 'screen'), array('href' => 'styles/coin-slider-styles.css', 'media' => 'screen'));

    // All JS Libraries and Scripts
    $this->template->head->javascripts = array('js/jquery.min.js', 'js/jquery.validate.min.js', 'js/jquery.colorbox.js', 'js/jquery.autogrow-textarea.js', 'js/coin-slider.min.js', 'js/dashboard/tooltip.js', 'js/dashboard/jquery-ui-1.js', 'js/dashboard/custom.js', 'js/jquery.uniform.js', 'js/custome.js', 'js/modernizr.custom.60079.js', 'js/jscolor/jscolor.js', 'js/jquery.maxlength-min.js');

    // Default Header view file
    $this->template->headers = View::factory('template/headers');

    // Default menu
    $this->template->headers->menu = View::factory('template/menu');
    if ($this->session->get('clientuserid')):
      $this->template->headers->menu->logoutlnk = $this->session->get('clientusername');
    endif;
    $this->template->headers->menu->menu = $this->menu;
	/* WP_MS START  */
	$this->template->headers->menu->menuSubmenu = $this->menuSubmenu;
	/* WP_MS END  */
    $this->template->headers->menu->currentactive = $this->request->controller() . "/" . $this->request->action();
    // Default Content template
    $this->template->footer = View::factory('template/footer');

    if ($this->template->footer):
      // All JS Libraries and Scripts
      $this->template->footer->scripts = array();
    endif;
    $this->types = Model::factory('dashboard')->getTypes();
    $this->userid = $this->session->get('clientuserid');
    $this->client_id = $this->session->get('client_id');
    $this->video_permission = Model::factory('user')->getVideoPermissionByCid($this->userid);
  }

  public function action_index() {
    if (!empty($_POST) && !isset($_POST["edit_id"])):

      $post = $_POST;

      $txtvideos = trim($_POST['video']);

      $txtname = trim($_POST['txtname']);
      $txtposition = trim($_POST['txtposition']);
      $txtnotes = trim($_POST['txtnotes']);
      $txtcompname = trim($_POST['txtcompname']);
      $txttelephone = trim($_POST['txttelephone']);
      $txtbuttonbackcolor = trim($_POST['txtbuttonbackcolor']);
      $txtbuttonfontcolor = trim($_POST['txtbuttonfontcolor']);

      $checklist_items = (isset($_POST['checklist_item']) && !empty($_POST['checklist_item'])) ? $_POST['checklist_item'] : array();

      $videos = explode(",", $txtvideos);

      foreach ($videos as $key => $value) {
        $value = explode("=", $value);
        if (count($value) == 2) {
          $videos[$key] = array("title" => $value[0], "id" => $value[1]);
        }
      }
      array_pop($videos);

      $protect = $_POST['protect'];
      $code_type = $_POST['code_type'];
      $enable_data_collection = $_POST['enable_data_collection'];

      $data_collection_mobile = isset($_POST['data_collection_mobile']) ? $_POST['data_collection_mobile'] : '0';
      $data_collection_email = isset($_POST['data_collection_email']) ? $_POST['data_collection_email'] : '0';
      $data_collection_content = trim($_POST['data_collection_content']);


      if ($txtname == ''):
        $errors['txtname'] = "Please provide name for people";
      else:

        $postArr['type'] = 'people';
        $postArr['txtname'] = $txtname;

        $postArr['txtposition'] = $txtposition;
        $postArr['txtnotes'] = $txtnotes;
        $postArr['txtcompname'] = $txtcompname;
        $postArr['txttelephone'] = $txttelephone;

        $postArr['txtbuttonbackcolor'] = $txtbuttonbackcolor;
        $postArr['txtbuttonfontcolor'] = $txtbuttonfontcolor;

        $postArr['protect'] = $protect;
        $postArr['code_type'] = $code_type;
        $postArr['enable_data_collection'] = $enable_data_collection;

        $postArr['data_collection_mobile'] = $data_collection_mobile;
        $postArr['data_collection_email'] = $data_collection_email;
        $postArr['data_collection_content'] = $data_collection_content;

        $postArr['created_at'] = date('Y-m-d H:i:s');

        $postArr['videos'] = $videos;
        $postArr['checklist_items'] = $checklist_items;

        $profileid = Model::factory('people')->insertPeople($postArr, $this->userid);
        $tmp_files = $this->session->get('tmp_files', FALSE);
        if ($tmp_files) {
          Model::factory('documents')->saveDocFiles($tmp_files, $profileid);
          Model::factory('picture')->savePictureFiles($tmp_files, $profileid);
          Model::factory('logo')->saveLogoFile($tmp_files, $profileid);
          $this->session->set('tmp_files', FALSE);
        }

        $clienturl = Model::factory('client')->geturl($profileid);
        $qrurl = URL::base() . $clienturl . "/" . $profileid;


        $wpArr = @file_get_contents(GALATECH_API.'item/addurl?url=' . $qrurl);
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

        $this->request->redirect('dashboard/people');
        exit;
      endif; elseif (!empty($_POST) && isset($_POST["edit_id"])):


      $post = $_POST;
      $txtname = trim($_POST['txtname']);
      $txtvideos = trim($_POST['video']);

      $txtposition = trim($_POST['txtposition']);
      $txtnotes = trim($_POST['txtnotes']);
      $txtcompname = trim($_POST['txtcompname']);
      $txttelephone = trim($_POST['txttelephone']);

      $txtbuttonbackcolor = trim($_POST['txtbuttonbackcolor']);
      $txtbuttonfontcolor = trim($_POST['txtbuttonfontcolor']);

      $checklist_items = (isset($_POST['checklist_item']) && !empty($_POST['checklist_item'])) ? $_POST['checklist_item'] : array();
      $checklist_item_ids = (isset($_POST['checklist_item_id']) && !empty($_POST['checklist_item_id'])) ? $_POST['checklist_item_id'] : array();

      $videos = explode(",", $txtvideos);
      foreach ($videos as $key => $value) {

        $value = explode("=", $value);

        if (count($value) == 2) {
          $videos[$key] = array("title" => $value[0], "id" => $value[1]);
        }
      }
      array_pop($videos);

      $protect = $_POST['protect'];
      $code_type = $_POST['code_type'];
      $enable_data_collection = $_POST['enable_data_collection'];

      $data_collection_mobile = isset($_POST['data_collection_mobile']) ? $_POST['data_collection_mobile'] : '0';
      $data_collection_email = isset($_POST['data_collection_email']) ? $_POST['data_collection_email'] : '0';
      $data_collection_content = trim($_POST['data_collection_content']);

      if ($txtname == ''):
        $errors['txtname'] = "Please provide name for People";
      else:
        $postArr['type'] = 'people';
        $postArr['txtname'] = $txtname;


        $postArr['txtposition'] = $txtposition;
        $postArr['txtnotes'] = $txtnotes;
        $postArr['txtcompname'] = $txtcompname;
        $postArr['txttelephone'] = $txttelephone;

        $postArr['txtbuttonbackcolor'] = $txtbuttonbackcolor;
        $postArr['txtbuttonfontcolor'] = $txtbuttonfontcolor;

        $postArr['protect'] = $protect;
        $postArr['code_type'] = $code_type;
        $postArr['enable_data_collection'] = $enable_data_collection;
        $postArr['created_at'] = date('Y-m-d H:i:s');

        $postArr['data_collection_mobile'] = $data_collection_mobile;
        $postArr['data_collection_email'] = $data_collection_email;
        $postArr['data_collection_content'] = $data_collection_content;


        $postArr['videos'] = $videos;

        $postArr['checklist_items'] = $checklist_items;
        $postArr['checklist_item_ids'] = $checklist_item_ids;
        
        /*echo "<pre>";
          print_r ($postArr); 
        echo "</pre>";
        exit;*/

        $profileid = Model::factory('people')->updatePeople($postArr, $_POST["edit_id"]);
        $tmp_files = $this->session->get('tmp_files', FALSE);
        if ($tmp_files) {
          Model::factory('documents')->saveDocFiles($tmp_files, $_POST["edit_id"]);
          Model::factory('picture')->savePictureFiles($tmp_files, $_POST["edit_id"]);
          Model::factory('logo')->saveLogoFile($tmp_files, $_POST["edit_id"]);
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

          if (file_exists("images/dmcode/DMIMG" . $_POST["edit_id"] . ".png"))
            unlink("images/dmcode/DMIMG" . $_POST["edit_id"] . ".png");

          $QRCode = file_get_contents($QRURL);
          $QRImage = "images/qrcode/CSQRIMG" . $_POST["edit_id"] . ".png";
          $qrcodeid = Model::factory('qrcode')->update_qrcode($_POST["edit_id"], $QRImage);
          $file = fopen(DOCROOT . $QRImage, "w");
          fwrite($file, $QRCode);
          fclose($file);
        }
        elseif ($code_type == 1) { // Data matrix
          $QRCode = file_get_contents(URL::base() . "dm_code/index.php?data_url=" . $short_url['shortLink'] . "&matrix_size=10&matrix_type=URL&fc=000000&bc=FFFFFF&matrix_block_style=square_blocks");

          if (file_exists("images/qrcode/CSQRIMG" . $_POST["edit_id"] . ".png"))
            unlink("images/qrcode/CSQRIMG" . $_POST["edit_id"] . ".png");

          $QRImage = "images/dmcode/DMIMG" . $_POST["edit_id"] . ".png";
          $qrcodeid = Model::factory('qrcode')->update_qrcode($_POST["edit_id"], $QRImage);
          $file = fopen(DOCROOT . $QRImage, "w");
          fwrite($file, $QRCode);
          fclose($file);
        }

        $this->request->redirect('people/edit/' . $_POST["edit_id"]);
        exit;
      endif;
    endif;
    $checklist_option = Model::factory('user')->getChecklistOption($this->client_id);

    $this->session->delete('tmp_files');
    $param = 'people/index';
    // Default Content template
    $this->template->content = View::factory('people/index')
            ->bind('video_per', $this->video_permission)
            ->bind('checklist_option', $checklist_option[0]['checklist_option']);
    $customqr_option = Model::factory('client')->isCustomqrEnabledToClient($this->client_id);
    $customqr = $customqr_option[0]['customqr_option'];
    $this->template->content->innermenu = View::factory('template/innermenu')
            ->bind('equiptypes', $this->types)
            ->bind('customqr', $customqr)
            ->bind('activemenu', $param);
  }

  public function action_add() {
    $errors = '';
    $succmsg = '';
    $last_id = array();
    $postArr = array();
    if (isset($_POST) && $_POST):
      $logo_file = $_FILES['logo_file']['name'];
      $txtname = trim($_POST['txtname']);
      $txtidenty = trim($_POST['txtidenty']);
      $txtsrno = trim($_POST['txtsrno']);
      $txtposition = trim($_POST['txtposition']);
      $txtnotes = trim($_POST['txtnotes']);
      $txtcompname = trim($_POST['txtcompname']);
      $txttelephone = trim($_POST['txttelephone']);
      $txtbuttonbackcolor = $_POST['txtbuttonbackcolor'];
      $txtbuttonfontcolor = $_POST['txtbuttonfontcolor'];
      $picture_file = $_FILES['picture_file']['name'];
      $docu_file = $_FILES['docu_file']['name'];
      $video_file = $_FILES['video_file']['name'];
      if ($logo_file == ''):
        $errors = "Client name can't left blank..!";
      elseif ($txtname == ''):
        $errors = "Address can't left blank..!";
      elseif ($txtidenty == ''):
        $errors = "Telephone can't left blank..!";
      elseif ($txtsrno == ''):
        $errors = "Contact Person can't left blank..!";
      elseif ($txtposition == ''):
        $errors = "Please provide your position..!";
      elseif ($txtnotes == ''):
        $errors = "Email Address can't left blank..!";
      elseif ($txtcompname == ''):
        $errors = 'Please enter valid email address..!';
      elseif ($txttelephone == ''):
        $errors = "Password can't left blank..!";
      elseif ($picture_file == ''):
        $errors = "URL can't left blank..!";
      elseif ($docu_file == ''):
        $errors = "URL can't left blank..!";
      elseif ($video_file == ''):
        $errors = "URL can't left blank..!";
      else:
        $postArr['logo_file'] = $logo_file;
        $postArr['type'] = 'position';
        $postArr['txtname'] = $txtname;
        $postArr['txtposition'] = $txtposition;
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

  public function action_edit() {
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
    if (isset($id) && $id):
      $profiledata = Model::factory('profiles')->getProfileById($id);

      $post["edit_id"] = $id;
      $post["logo"] = Model::factory('logo')->getLogoById($id);
      $post["pictures"] = Model::factory('picture')->getPicturesByProfileId($id);
      $post["documents"] = Model::factory('documents')->getDocumentsByProfileId($id);
      $post["videos"] = Model::factory('video')->getVideoByProfileId($id);

      $post['txtname'] = $profiledata['name'];
      $post['txtposition'] = $profiledata['position'];
      $post['txtnotes'] = $profiledata['notes'];
      $post['txtcompname'] = $profiledata['name_company'];
      $post['txttelephone'] = $profiledata['telephone'];
      $post['txtbuttonbackcolor'] = $profiledata['buttonbackcolor'];
      $post['txtbuttonfontcolor'] = $profiledata['buttonfontcolor'];
      $post['protect'] = $profiledata['protect'];
      $post['code_type'] = $profiledata['code_type'];
      $post['enable_data_collection'] = $profiledata['enable_data_collection'];

      $post["data_collection_mobile"] = $profiledata['data_collection_mobile'];
      $post["data_collection_email"] = $profiledata['data_collection_email'];
      $post["data_collection_content"] = $profiledata['data_collection_content'];


      if ($post['protect'] == '1'):
        $yes = TRUE;
        $no = FALSE;
      else:
        $yes = FALSE;
        $no = TRUE;
      endif;
    endif;
    $qrimg = Model::factory('qrcode')->getQRCodeByProfileId($id);
    $checklist_items = Model::factory('checklistitem')->getChecklist_itemsByProfileId($id);
    $checklist_option = Model::factory('user')->getChecklistOption($this->client_id);

    $this->template->content = View::factory('people/edit')
            ->bind('yes', $yes)
            ->bind('no', $no)
            ->bind('post', $post)
            ->bind('qrimg', $qrimg)
            ->bind('checklist_items', $checklist_items)
            ->bind('checklist_option', $checklist_option[0]['checklist_option'])
            ->bind('video_per', $this->video_permission);
    $param = 'people/edit';
    $customqr_option = Model::factory('client')->isCustomqrEnabledToClient($this->client_id);
    $customqr = $customqr_option[0]['customqr_option'];
    $this->template->content->innermenu = View::factory('template/innermenu')
            ->bind('equiptypes', $this->types)
            ->bind('customqr', $customqr)
            ->bind('activemenu', $param);
  }

  public function action_delete() {
    $id = $this->request->param('id');
    if (isset($id) && $id):
      Model::factory('profiles')->deleteprofile($id);
    endif;
    $this->request->redirect('dashboard/people');
  }

}

// End Welcome
