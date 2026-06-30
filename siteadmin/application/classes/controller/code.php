<?php
defined('SYSPATH') or die('No direct script access.');

class Controller_Code extends Controller_Template_Master {

  public $types = '';
  public $userid = '';
  public $client_id = '';
  public $originalfile = "../images/logo/code/original/";
  public $thumbfile = "../images/logo/code/thumb/";
  public $mediumfile = "../images/logo/code/medium/";
  
  public $type_id = 9;

  public function before() {

   parent::before();
		
		$this->session = Session::instance();
		if(!$this->session->get('adminuserid')):
			$this->request->redirect('welcome/index');
		endif;

		$this->template->head = View::factory('template/head');
		
		// Set the default Page Title
		$this->template->head->title = 'Welcome to ScanLink';
		
		// All CSS Stylesheets
		$this->template->head->stylesheets = array(array('href'=>'styles/site.css','media'=>'screen'),array('href'=>'styles/style.css','media'=>'screen'), array('href'=>'styles/dropdown.css','media'=>'screen'), array('href'=>'styles/default.css','media'=>'screen'),array('href'=>'styles/colorbox.css','media'=>'screen'));
		
		// All JS Libraries and Scripts
		$this->template->head->javascripts = array("js/client/jquery.min.js", "js/paginator.js","js/addInput.js",'js/client/jquery-ui-1.js','js/client/jquery.colorbox.js','js/jquery.validate.min.js','js/jquery.autogrow-textarea.js');
		
		// Default Header view file
		$this->template->headers = View::factory('template/headers');
		$this->template->headers->logoutlnk = View::factory('template/logoutlnk');
		
		// Default Header view file
		$this->template->menu = View::factory('template/menu');
		
		$this->template->menu->HeadLinksArray = $this->menu;
		
		// Default Content template
		$this->template->content = View::factory('product/index');
		
		$this->template->content->menu = $this->menu;
		// Default Content template
		$this->template->footer = '';
		
		if($this->template->footer):
		// All JS Libraries and Scripts
		$this->template->footer->scripts = array();
		endif;
		
		$settings = Model::factory("settings")->getallsettings();
		if(isset($settings) && !empty($settings)){
			foreach($settings as $key=>$val){
				if($val['title'] == 'youtube_username'){
					$this->USERNAME = $val['values'];
				}
				if($val['title'] == 'youtube_password'){
					$this->PASSWORD = $val['values'];
				}
				if($val['title'] == 'youtube_developer_key'){
					$this->YT_DEVELOPER_KEY = $val['values'];
				}
				if($val['title'] == 'youtube_client_id'){
					$this->YT_CLIENT_ID = $val['values'];
				}
				if($val['title'] == 'youtube_application_id'){
					$this->YT_APPLICATION_ID = $val['values'];
				}
			}
		}
  }


  // add form code 
  public function action_index() {
	  
	 //if post request than insert into database
     if ($this->request->method() === Request::POST):
	 	// get post data

        $application = trim($_POST['application']);
        $txtdesc = trim($_POST['txtdesc']);
        $destination_url = trim($_POST['destination_url']);
        $data_collection_mobile = isset($_POST['data_collection_mobile']) ? $_POST['data_collection_mobile'] : '0';
        $data_collection_email = isset($_POST['data_collection_email']) ? $_POST['data_collection_email'] : '0';
        $data_collection_name = isset($_POST['data_collection_name']) ? $_POST['data_collection_name'] : '0';
        $enable_data_collection = $_POST['enable_data_collection'];
        $mode = $_POST['mode'];
        $activatite_bridge = isset($_POST['activate_bridge_graphic']) ? $_POST['activate_bridge_graphic'] : '';
        $set_up_compulsory = isset($_POST['set_up_compulsory']) ? ($enable_data_collection) ? $_POST['set_up_compulsory'] : ''  : '';
		
		$profile_id = $_POST['profile_id'];
		$user_id = $_POST['userid'];
		
		// validation start before update
        if ($application == '') {
          //$errors['application'] = "Please provide application for code";
        }
        if ($txtdesc == '') {
          //$errors['txtdesc'] = "Please provide popup message for code.";
        }
        if ($destination_url == '') {
          $errors['destination_url'] = "Please provide destination url for code.";
        } elseif (!filter_var($destination_url, FILTER_VALIDATE_URL)) {
          $errors['destination_url'] = "Please provide valid destination url for code.";
        }
		
		$images = $this->session->get("codelogo");
        
		// validation end before update
        // getting into a array to display form value filled up
        $feilds['application'] = $application;
        $feilds['txtdesc'] = $txtdesc;
        $feilds['destination_url'] = $destination_url;
        $feilds['enable_data_collection'] = $enable_data_collection;
        $feilds['data_collection_mobile'] = $data_collection_mobile;
        $feilds['data_collection_email'] = $data_collection_email;
        $feilds['data_collection_name'] = $data_collection_name;
        $feilds['images'] = $images;
       
       
        $feilds['set_up_compulsory'] = $set_up_compulsory;
		
		
		// if sucessfully update the code
        if (empty($errors)) {
			// insert code data
          if ($mode == 'add') {
            $profile_id = Model::factory('code')->insertCode($feilds, $user_id);
          } else {
            $profile_id = Model::factory('code')->updateCode($feilds, $profile_id);
           // $this->session->set("successmessage", "successfully edited code.");
          }
          $this->session->delete("codelogo");	
		  
		  
		  // QR CODE START 
          if ($mode == 'add') {
            $clienturl = Model::factory('client')->geturl($profile_id);
            $clienturl = trim($clienturl);
            if ($clienturl == '' || empty($clienturl) || strlen($clienturl) == 0) {
              $clienturl = $this->userid;
            }
            $qrurl = URL::base() . $clienturl . "/" . $profile_id;


            $wpArr = @file_get_contents(GALATECH_API.'item/addurl?url=' . $qrurl);
            $response = json_decode($wpArr);


            if ($response->response) {
              $data = array("analytic_key" => $response->item_key);
              Model::factory('profiles')->updateProfile($profile_id, $data);
            }

            require_once("../classes/shortURL.php");
            $api_token = 'KqXZgB6aPc7IkpDB1UlqALKp0v1yhCcjPI40dkprS8UOCypvfYu7KKNrn0QY';
      		$short_url = shortenUrl($qrurl, $api_token);
            //$short_url = shortenUrl($qrurl);

            $data = array("shorturl" => $short_url['shortLink']);
            Model::factory('profiles')->updateProfile($profile_id, $data);

            //$QRURL = "https://chart.googleapis.com/chart?cht=qr&chs=185x185&chl=" . $short_url['shortLink'] . "&chld=M|0";
	    $QRURL = "https://api.qrserver.com/v1/create-qr-code/?data=" . $short_url['shortLink'] . "&size=185x185&ecc=M";

            $QRCode = file_get_contents($QRURL);
            $QRImage = "/images/qrcode/CSQRIMG" . $profile_id . ".png";

            if ($mode == 'add') {
              $qrcodeid = Model::factory('qrcode')->saveqrcode($profile_id, $QRImage);
             // $this->session->set("SuccessMessage", "successfully added code.");
            } else {
              $qrcodeid = Model::factory('qrcode')->update_qrcode($profile_id, $QRImage);
              //$this->session->set("successmessage", "successfully edited code.");
            }

            $file = fopen(DOCROOT . $QRImage, "w");
            fwrite($file, $QRCode);
            fclose($file);
          }
          // QR CODE end 
          if ($mode == 'add') {
            $this->request->redirect('product/');
          } else {
            $this->request->redirect('product/');
          }
          exit;
		  
		}
		
	 endif;
	  
	  
    //intialize the variable 
    
	array_push($this->template->head->javascripts, "js/farbtastic.js");

    array_push($this->template->head->stylesheets, array('href' => '../styles/farbtastic.css', 'media' => 'screen'));
	
	$errors = array();
    $feilds = array();
    $param = "code/index";
    $add_form_display = false;
    $mode = "add";
    $profile_id = "";
    $profile_id = $this->request->param('id');
	
	
	$userid = $this->request->param('id');
	$uemail = Model::factory("user")->getUserEmailById($userid);
	if(!empty($uemail)){
		$uemail = $uemail[0]['email'];
	}else{
		$this->request->redirect('product/');
		exit;
	}
	
	$this->session->delete('tmp_files');
	
    // Default Content template
    $this->template->content = View::factory('code/index')
            ->bind("errors", $errors)
            ->bind("feilds", $feilds)
            ->bind("add_form_display", $add_form_display)
            ->bind("mode", $mode)
            ->bind("profile_id", $profile_id)
            ->bind('errormessage', $errormessage)
            ->bind('successmessage', $successmessage)
            ->bind('userid',$userid)
			->bind('uemail',$uemail);


  }
  
  
  // edit form code 
  public function action_edit() {

    //intialize the variable 
    
	array_push($this->template->head->javascripts, "js/farbtastic.js");

    array_push($this->template->head->stylesheets, array('href' => '../styles/farbtastic.css', 'media' => 'screen'));
	
	$errors = array();
    $feilds = array();
    $param = "code/index";
    $add_form_display = false;
    $mode = "edit";
    $profile_id = "";
    $profile_id = $this->request->param('id');
	
	
	if(isset($profile_id) && $profile_id){
		$profiledata = Model::factory('profiles')->getProfileById($profile_id);
			
		if($profiledata['type_id'] != $this->type_id){
			$this->request->redirect('product/');
			exit;
		}
		
		$uemail = Model::factory('user')->getUserEmailByClientId($profiledata['client_id']);
		if(!empty($uemail)){
			$uemail = $uemail[0]['email'];
		}
		
		 // getting into a array to display form value filled up while edit mode
		$feilds['application'] = $profiledata['name'];
		$feilds['txtdesc'] = $profiledata['description'];
		$feilds['destination_url'] = $profiledata['url'];
		$feilds['enable_data_collection'] = $profiledata['enable_data_collection'];
		$feilds['set_up_compulsory'] = $profiledata['set_up_compulsory'];
		$feilds['data_collection_mobile'] = $profiledata['data_collection_mobile'];
		$feilds['data_collection_email'] = $profiledata['data_collection_email'];
		$feilds['data_collection_name'] = $profiledata['data_collection_name'];
  
		$logodata = Model::factory('logo')->getLogoByProfileId($profile_id);
		$qrimg = Model::factory('qrcode')->getQRCodeByProfileId($profile_id);
		$feilds['qrimg'] = $qrimg['qrimg_name'];
		if (!empty($logodata)) {
		  if ($logodata['logo_name'] != '') {
			$feilds['activate_bridge_graphic'] = 1;
			$feilds['originalfile'] = $this->originalfile . $logodata['logo_name'];
			$feilds['thumbfile'] = $this->thumbfile . $logodata['logo_name'];
			$feilds['mediumfile'] = $this->mediumfile . $logodata['logo_name'];
		  }
		}
		
		
	  
	}
	else{
		$this->request->redirect('product/');
			exit;
	}
	
	$this->session->delete('tmp_files');
	
    // Default Content template
    $this->template->content = View::factory('code/index')
            ->bind("errors", $errors)
            ->bind("feilds", $feilds)
            ->bind("add_form_display", $add_form_display)
            ->bind("mode", $mode)
            ->bind("profile_id", $profile_id)
            ->bind('errormessage', $errormessage)
            ->bind('successmessage', $successmessage)
            ->bind('userid',$userid)
			->bind('uemail',$uemail)
			->bind('qrimg',$qrimg);


  }
  

  /****
   ***** upload graphic image 
   ****/

  public function action_getData() {
    $returndata = array();

    $type = $_FILES['upload_graphic']['type'];

    if ($type == 'image/jpeg' || $type == 'image/jpg' || $type == 'image/png' || $type == 'image/gif') {
      $size = getimagesize($_FILES['upload_graphic']['tmp_name']);
      if ($size[0] <= 320 && $size[1] <= 480) {

        //include generate thumb classs
        include_once("class_THUMB.php");
        // get image name 
        $filedata = explode(".", $_FILES['upload_graphic']['name']);

        //file name change
        $filename = $filedata[0] . rand() . "." . end($filedata);

        //filename save in session to genreate logo image
        $this->session->set("codelogo", $filename);

        //getting path of thumb and original image with file name
        $originalfile = $this->originalfile . $filename;
        $thumbfile = $this->thumbfile . $filename;
        $mediumfile = $this->mediumfile . $filename;

        // copy orignial image
        copy($_FILES['upload_graphic']['tmp_name'], $originalfile);

        // genrating thumb 
        //START midum size thumb 
        $obj_img = new thumbnail_images();
        $obj_img->PathImgOld = $originalfile;
        $obj_img->PathImgNew = $mediumfile;
        $obj_img->NewWidth = 320;
        $obj_img->NewHeight = 480;

        if (!$obj_img->create_thumbnail_images()) {
          // return data with errror
          $returndata['status'] = "0";
          $returndata['error'] = "Image could not be generated try again.";
        } else {
          //START thumbnail  size thumb 
          $obj_img = new thumbnail_images();
          $obj_img->PathImgOld = $originalfile;
          $obj_img->PathImgNew = $thumbfile;
          $obj_img->NewWidth = 52;
          $obj_img->NewHeight = 52;
          $obj_img->create_thumbnail_images();

          // return data with success
          $returndata['status'] = "1";
          $returndata['thumb'] = URL::base() . $mediumfile;
          $returndata['smallthumb'] = URL::base() . $thumbfile;
        }
      } else {
        // return data with errror
        $returndata['status'] = "0";
        $returndata['error'] = "File size is too high (maximum is 480 pixels, " . $size[1] . " given )";
      }
    } else {
      // return data with errror
      $returndata['status'] = "0";
      $returndata['error'] = "Invalid mime type(" . $type . ").";
    }
    echo json_encode($returndata);
    exit();
  }

  /*   * **
   * **** delete code 
   * ** */

  public function action_delete() {
    // set title tag 
    $this->template->head->title = "Code Delete";

    $id = $this->request->param('id');
    if (isset($id) && $id):

      $profiledata = Model::factory('code')->getProfilesById($id);

      if (sizeof($profiledata) == 0) {
        Session::instance()->set('errormessage', 'Oops! Access denied for requested profile.');
        $this->request->redirect(URL::base() . 'code/index');
      } else {
        if (strtotime($profiledata['expired_at']) < strtotime(date("Y-m-d H:i:s"))) {
          $this->session->set("errormessage", "Code is expired.You can not delete that code.");
          $this->request->redirect('code/index');
        } else {

          // delete analytics
          $wpArr = @file_get_contents(GALATECH_API.'item/clearItem?key=' . $profiledata['analytic_key']);
          $response = json_decode($wpArr);

          Model::factory('profiles')->deleteprofile($id);
          $this->session->set("SuccessMessage", "successfully deleted code.");
          $this->request->redirect('code/index');
        }
      }
    endif;

    exit;
  }

  /*   * **
   * **** Generate Qr code with color
   * ** */

  public function action_qrCodeColor() {
    // set title tag 
    $this->template->head->title = "Code QR Code Color Change";

    // gathring profile data 
    $profile_id = isset($_POST['profile_id']) ? $_POST['profile_id'] : 277;
    $profiledata = Model::factory('code')->getProfilesById($profile_id);
    $googleShorturl = isset($profiledata['shorturl']) && $profiledata['shorturl'] != "" ? $profiledata['shorturl'] : 'http://www.google.co.in';
//	echo $googleShorturl; exit;
    $color = isset($_POST['color']) ? $_POST['color'] : '#FFFFFF';
    $color = str_replace('#', '', $color);

    $qrimage = "images/qrcode/CSQRIMG" . $profile_id . ".png";

    // Start generating qr code with color 
    $googleapis = url::base() . "qrcode/code.php?type=URL&height=165&width=165&block_size=5&url=" . $googleShorturl . "&foreground_color=" . $color . "&border_size=4&background_color=FFFFFF&block_style=square_blocks";
    $datareturn['codeImage'] = URL::base() . $qrimage;
    //echo json_encode($datareturn);
    //exit();
    $qrApis = @file_get_contents($googleapis);

    $file = fopen($qrimage, "w");
    fwrite($file, $qrApis);
    fclose($file);
    // END  generating qr code with color

    $datareturn['codeImage'] = URL::base() . $qrimage;
    echo json_encode($datareturn);
    exit();
  }

  /*   * **
   * **** Remove loge image
   * ** */

  public function action_removelogo() {
    // set title tag 
    $this->template->head->title = "Code Logo Remove";

    // get profile id 
    $profile_id = isset($_POST['profile_id']) ? $_POST['profile_id'] : '';
    $returndata = array();
    if ($profile_id == '') {
      // return data with error message
      $returndata['status'] = "0";
      $returndata['error'] = "profile id could not found.try again";
    } else {
      // getting profile logo detail 
      $logodata = Model::factory('logo')->getLogoByProfileId($profile_id);
      if (!empty($logodata) && $logodata['logo_name'] != '') {
        // getting profile logo file  
        $originalfile = $this->originalfile . $logodata['logo_name'];
        $thumbfile = $this->thumbfile . $logodata['logo_name'];
        $mediumfile = $this->mediumfile . $logodata['logo_name'];

        //unlink or remove  image from the folder
        if (file_exists($originalfile))
          unlink($originalfile);
        if (file_exists($thumbfile))
          unlink($thumbfile);
        if (file_exists($mediumfile))
          unlink($mediumfile);

        // delete it from the logo table
        Model::factory('logo')->deleteLogoByProfileId($profile_id);

        // return data with success message
        $returndata['status'] = "1";
        $returndata['thumb'] = URL::base() . 'images/graphic.jpg';
        $returndata['smallthumb'] = URL::base() . 'images/graphic-small.jpg';
      }else {
        // return data with error message
        $returndata['status'] = "0";
        $returndata['error'] = "Logo image not found.";
      }
    }
    echo json_encode($returndata);
    exit();
  }

  /*   * **
   * **** Download QR code as pdf
   * ** */

  public function action_downloadQrpdf() {
    // set title tag 
    $this->template->head->title = "Code Download QR code as PDF";

    $profile_id = $this->request->param('id');
    if ($profile_id != '') {
      //profile data gathering
      $profiledata = Model::factory('code')->getProfilesById($profile_id);
      $qrimg = Model::factory('qrcode')->getQRCodeByProfileId($profile_id);

      //to generate pdf and download code start 
      require_once('pdf.php');

      $content1 = 'Profile No. : ' . $profiledata['id'];
      $content2 = 'Name : ' . $profiledata['name'];
      $content3 = 'URL :' . $profiledata['url'];

      $pdf = new PDF();
      $pdf->AddPage();
      $pdf->Image('images/logo.png', 90, 12, 40, 0, '', URL::base());
      $pdf->SetLeftMargin(45);
      $pdf->SetFont('Arial', '', 20);
      $pdf->SetFontSize(14);
      $pdf->Ln(25);
      $pdf->WriteHTML($content1);
      $pdf->Ln(10);
      $pdf->WriteHTML($content2);
      $pdf->Ln(10);
      $pdf->WriteHTML($content3);
      $pdf->Image($qrimg['qrimg_name'], 90, 80, 40, 0, '', URL::base());
      $pdf->Output('code.pdf', 'D');
      //$pdf->Output();
      //to generate pdf and download code end 
    } else {
      $this->request->redirect('dashboard/mastercode');
      exit;
    }
    exit();
  }

  /*   * **
   * **** Download QR code as png
   * ** */

  public function action_downloadQrImage() {
    // set title tag 
    $this->template->head->title = "Code Download QR code as PNG";

    $profile_id = $this->request->param('id');
    if ($profile_id != '') {
      //getting qr image
      $qrimg = Model::factory('qrcode')->getQRCodeByProfileId($profile_id);
      $_GET['filename'] = $qrimg['qrimg_name'];

      // to download the qr image
      include_once("download.php");
    } else {
      $this->request->redirect('dashboard/mastercode');
      exit;
    }
    exit();
  }

  /*   * **
   * **** Renew code summary
   * ** */

  public function action_renewCode() {
    // set title tag 
    $this->template->head->title = "Renew Code Order Summary";

    // get profile id to be renew
    $renew_profile = isset($_POST['selectprofile']) ? $_POST['selectprofile'] : array();

    // if post array of profile to be renew is not set than check from session data and set to form  
    if (empty($renew_profile)) {
      $renew_profile = $this->session->get('renewdata');
      if (isset($renew_profile['profile_id']))
        $renew_profile = $renew_profile['profile_id'];
      else
        $renew_profile = array();
    }

    // if profile id not get at that time redirect to dashbord/code with error message
    if (!empty($renew_profile)) {

      //calculate renew amount 
      $noorder = count($renew_profile);
      $codeamount = Model::factory('codeprising')->getAmountByQuantity($noorder);
      $permonthamount = number_format($codeamount[0]['amount'], 2);
      $totalsubscrption = number_format($codeamount[0]['amount'] * $noorder * 12, 2);

      // store rew data to seession 
      $sessiondata = array();
      $sessiondata['profile_id'] = $renew_profile;
      $sessiondata['noorder'] = $noorder;
      $sessiondata['amount'] = $permonthamount;
      $sessiondata['totalsubscrption'] = $totalsubscrption;
      $this->session->set('renewdata', $sessiondata);

      // Default Content template
      $this->template->content = View::factory('code/renewSummary')
              ->bind('nocode', $noorder)
              ->bind('codeamount', $permonthamount)
              ->bind('totalsubscription', $totalsubscrption);
    } else {
      $this->session->set("errormessage", "Please select code to be renew.");
      $this->request->redirect('dashboard/mastercode');
      exit;
    }
  }

  public function action_renewableCode() {
    // set title tag 
    $this->template->head->title = "Code Renewd";
    // get session data to renew profile 
    $renew_profile_data = $this->session->get('renewdata');

    // if not get the redirect to dashboard/mastercode to select the profile to be renew
    if (!empty($renew_profile_data)) {

      // get user billing information 
      $userdata = Model::factory('user')->getUserByID($this->session->get('clientuserid'));
      $data['first_name'] = $userdata['first_name'];
      $data['last_name'] = $userdata['last_name'];
      $data['company_name'] = $userdata['company_name'];
      $data['billing_address'] = $userdata['billing_address'];
      $data['email'] = $userdata['email'];
      $data['town'] = $userdata['town'];
      $data['phone'] = $userdata['phone'];
      $data['postal_code'] = $userdata['postal_code'];
      $data['profile_id'] = $renew_profile_data['profile_id'];
      $data['no_of_codes'] = $renew_profile_data['noorder'];
      $data['per_code_amount'] = $renew_profile_data['amount'];

      $returndata = Model::factory('codepurchase')->renewcodepurchase($userdata['client_id'], $userdata['id'], $data);

      $datafill = array();
      $datafill['pop_up_purchase'] = '1';
      Model::factory('user')->updateuser($datafill, $userdata['id']);

      // send mail  START
      include_once("mailsend.php");
      $adminemail = Model::factory('settings')->getValueByTitle('contact_email');
      $adminemail = "admin@scanlink.net.au";
      $baseurl = URL::base();
      //send mail to user START 
      renewCode_mail_client($adminemail, $baseurl, $data);
      //send mail to user END
      //send mail to admin START 
      renewCode_mail_admin($adminemail, $baseurl, $data);
      //send mail to admin END
      // send mail  END


      $this->session->delete('renewdata');
      //$this->session->set("SuccessMessage","Your ".$renew_profile_data['noorder']." code has been renewed successfully. Our team will send invoice in 14 days.");
      if ($returndata['same_type']) {
        switch ($returndata['type_id']) {
          case 1 :
            $this->request->redirect('dashboard/index');
            break;
          case 2 :
            $this->request->redirect('dashboard/location');
            break;
          case 3 :
            $this->request->redirect('dashboard/asset');
            break;
          case 4 :
            $this->request->redirect('dashboard/product');
            break;
          case 5 :
            $this->request->redirect('dashboard/procedure');
            break;
          case 6 :
            $this->request->redirect('dashboard/mastercode');
            break;
          case 7 :
            $this->request->redirect('dashboard/mastercode');
            break;
          case 8 :
            $this->request->redirect('dashboard/misc');
            break;
          case 9 :
            $this->request->redirect('dashboard/mastercode');
            break;
        }
      } else {
        $this->request->redirect('dashboard/mastercode');
      }
      exit;
    } else {
      $this->session->set("errormessage", "Please select code to be renew.");
      $this->request->redirect('dashboard/mastercode');
      exit;
    }
  }

}

// End Welcome
