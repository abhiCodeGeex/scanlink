<?php

defined('SYSPATH') or die('No direct script access.');

class Controller_Code extends Controller_Template_Master {

  public $types = '';
  public $userid = '';
  public $client_id = '';
  public $originalfile = "images/logo/code/original/";
  public $thumbfile = "images/logo/code/thumb/";
  public $mediumfile = "images/logo/code/medium/";

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
    $this->template->head->javascripts = array('js/jquery.min.js', 'js/jquery.validate.min.js', 'js/jquery.colorbox.js', 'js/jquery.autogrow-textarea.js', 'js/coin-slider.min.js', 'js/dashboard/jquery-ui-1.js', 'js/jquery.uniform.js', 'js/custome_code.js', 'js/modernizr.custom.60079.js', 'js/jscolor/jscolor.js', 'js/jquery.maxlength-min.js','js/profile_page_common.js');

    // Default Header view file
    $this->template->headers = View::factory('template/headers');

    // Default menu
	
    $msgPopup = "false";	
    $this->template->headers->menu = View::factory('template/menu')->bind('msgPopup', $msgPopup);
    if ($this->session->get('clientuserid')):
      $this->template->headers->menu->logoutlnk = $this->session->get('clientusername');
    endif;
    $this->template->headers->menu->menu = $this->menu;


    $this->template->headers->menu->menuSubmenu = $this->menuSubmenu;

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
  }

  // add form code 
  public function action_index() {
    //intialize the variable 
    $errors = array();
    $feilds = array();
    $param = "code/index";
    $add_form_display = false;
    $mode = "add";
    $profile_id = "";
    $profile_id = $this->request->param('id');
    $expried_or_not = false;
	
	

    array_push($this->template->head->javascripts, "js/farbtastic.js");
    array_push($this->template->head->javascripts, "js/jquery.tooltip.js");

    array_push($this->template->head->stylesheets, array('href' => 'styles/farbtastic.css', 'media' => 'screen'));
    array_push($this->template->head->stylesheets, array('href' => 'styles/jquery.tooltip.css', 'media' => 'screen'));
    // set title tag 
    $this->template->head->title = "URL Link Add";

    $errormessage = "";
    if ($this->session->get("errormessage") != "") {
      $errormessage = $this->session->get("errormessage");
      $this->session->delete("errormessage");
    }
    $successmessage = "";
    if ($this->session->get("successmessage") != "") {
      $successmessage = $this->session->get("successmessage");
      $this->session->delete("successmessage");
    }

    //get profile id exsist to add code 
    $profile_id_get = Model::factory('code')->getprofileidtoupdate($this->session->get('client_id'));
	
	if ($profile_id != '') {
      $profiledata = Model::factory('code')->getProfilesById($profile_id);
	  if($profiledata['update_or_not'] == '1'){
		  $mode = 'edit';
	  }
	}
	
	
	$user_data = Model::factory('user')->getUserByID($this->session->get('clientuserid'));
	
	if($mode=="add" && $user_data["is_sub_user"]==1 && $user_data["access_addcode"]==0 )
	{
		Session::instance()->set('errormessage', 'Oops! You have no access to add profile.');
		$this->request->redirect('dashboard/code');
	}
	
	if($mode=="edit" && $user_data["is_sub_user"]==1 && $user_data["access_edit"]==0 )
	{
		Session::instance()->set('errormessage', 'Oops! You have no access to edit profile.');
		$this->request->redirect('dashboard/code');
	}
	
	
    //add form or edit 
    if ($mode == 'edit') {
		
      // set title tag 
      $this->template->head->title = "URL Link Edit";

      $mode = 'edit';
      $profiledata = Model::factory('code')->getProfilesById($profile_id);

      if (empty($profiledata)) {
        $this->session->set("errormessage", "You can not access.");
        $this->request->redirect('dashboard/mastercode');
        exit;
      }

      //if ($profiledata['user_id'] != $this->userid) {
	  if ($profiledata['client_id'] != $this->session->get('client_id')) {
        $this->session->set("errormessage", "You can edit your own code only.");
        $this->request->redirect('dashboard/mastercode');
        exit;
      }

      if ($profiledata['type_id'] != '9') {
        $this->session->set("errormessage", "You can edit only code type.");
        $this->request->redirect('dashboard/mastercode');
        exit;
      }

      if (strtotime($profiledata['expired_at']) < strtotime(date("Y-m-d H:i:s")) && ($profiledata['free_code']==0 || ($profiledata['free_code']==1 && $profiledata['renewal_required']==1) ) ) {
       $expried_or_not = true;
      }
      

      // getting into a array to display form value filled up while edit mode
      $feilds['application'] = $profiledata['name'];
      $feilds['txtdesc'] = $profiledata['description'];
      $feilds['code_type'] = $profiledata['code_type'];
      $feilds['color_code'] = $profiledata['color_code'];
      $feilds['destination_url'] = $profiledata['url'];
      $feilds['enable_data_collection'] = $profiledata['enable_data_collection'];
      $feilds['set_up_compulsory'] = $profiledata['set_up_compulsory'];
      $feilds['data_collection_mobile'] = $profiledata['data_collection_mobile'];
      $feilds['data_collection_email'] = $profiledata['data_collection_email'];
      $feilds['data_collection_name'] = $profiledata['data_collection_name'];
      $feilds['data_collection_surname'] = $profiledata['data_collection_surname'];

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
    else 
    { //add mode
    	$profiledata = Model::factory('code')->getProfilesById($profile_id);
    	if (strtotime($profiledata['expired_at']) < strtotime(date("Y-m-d H:i:s")) && ($profiledata['free_code']==0 || ($profiledata['free_code']==1 && $profiledata['renewal_required']==1) ) ) {
       $expried_or_not = true;
      }
    }

    //if exsit than only add the code 
    if ($mode == 'edit' || ($mode == 'add' && !empty($profile_id_get))) {

	  
      //get profile id to add the code
      if ($mode == 'add' && $profile_id == '') {
        $profile_id = $profile_id_get[0]['id'];
      }
	  
	 
	  
      $add_form_display = true;

      //if post request than insert into database
      if ($this->request->method() === Request::POST):
        // get post data

        $application = trim($_POST['application']);
        $txtdesc = trim($_POST['txtdesc']);
        $code_type = trim($_POST['code_type']);
        $color_code = trim($_POST['color']);
        $destination_url = trim($_POST['destination_url']);
        $data_collection_mobile = isset($_POST['data_collection_mobile']) ? $_POST['data_collection_mobile'] : '0';
        $data_collection_email = isset($_POST['data_collection_email']) ? $_POST['data_collection_email'] : '0';
        $data_collection_name = isset($_POST['data_collection_name']) ? $_POST['data_collection_name'] : '0';
        $data_collection_surname = isset($_POST['data_collection_surname']) ? $_POST['data_collection_surname'] : '0';
        $enable_data_collection = $_POST['enable_data_collection'];
        $mode = $_POST['mode'];
        $activatite_bridge = isset($_POST['activate_bridge_graphic']) ? $_POST['activate_bridge_graphic'] : '';
        $set_up_compulsory = isset($_POST['set_up_compulsory']) ? ($enable_data_collection) ? $_POST['set_up_compulsory'] : ''  : '';
		
		$profile_id = $_POST['profile_id'];
        
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
        if ($images == '' && $mode == 'add') {
          //$errors['activate_bridge_graphic'] = "Please provide activate bridge graphic.";
        }
        // validation end before update
        // getting into a array to display form value filled up
        $feilds['application'] = $application;
        $feilds['txtdesc'] = $txtdesc;
        $feilds['destination_url'] = $destination_url;
        $feilds['enable_data_collection'] = $enable_data_collection;
        $feilds['data_collection_mobile'] = $data_collection_mobile;
        $feilds['data_collection_email'] = $data_collection_email;
        $feilds['data_collection_name'] = $data_collection_name;
        $feilds['data_collection_surname'] = $data_collection_surname;
        $feilds['images'] = $images;
        $feilds['user_id'] = $this->userid;
        $userdata = Model::factory('user')->getUserByID($this->userid);
        $feilds['client_id'] = $userdata['client_id'];
        $feilds['set_up_compulsory'] = $set_up_compulsory;
        $feilds['code_type'] = $code_type;
        $feilds['color_code'] = $color_code;
		
		
        // if sucessfully update the code
        if (empty($errors)) {
          // insert code data
          if ($mode == 'add') {
            Model::factory('code')->insertCode($feilds, $profile_id);
          } else {
            Model::factory('code')->updateCode($feilds, $profile_id);
            $this->session->set("successmessage", "successfully edited URL Link.");
          }
          $this->session->delete("codelogo");
		 
		  $clienturl = Model::factory('client')->geturl($profile_id);
          $clienturl = trim($clienturl);
          if ($clienturl == '' || empty($clienturl) || strlen($clienturl) == 0) {
              $clienturl = $this->userid;
            }
          $qrurl = URL::base() . $clienturl . "/" . $profile_id;
			
          // QR CODE START 
          if ($mode == 'add') 
		  {
            


            $wpArr = @file_get_contents(GALATECH_API.'item/addurl?url=' . $qrurl);
            $response = json_decode($wpArr);


            if ($response->response) {
              $data = array("analytic_key" => $response->item_key);
              Model::factory('profiles')->updateProfile($profile_id, $data);
            }

            require_once("classes/shortURL.php");
            $api_token = 'KqXZgB6aPc7IkpDB1UlqALKp0v1yhCcjPI40dkprS8UOCypvfYu7KKNrn0QY';
      		$short_url = shortenUrl($qrurl, $api_token);
            //$short_url = shortenUrl($qrurl);

            $data = array("shorturl" => $short_url['shortLink']);
            Model::factory('profiles')->updateProfile($profile_id, $data);
			
			$color = isset($_POST['color']) ? $_POST['color'] : '#FFFFFF';
    		$color = str_replace('#', '', $color);
			
		
			if($code_type=="0")// QR Code
		    {
				    //$QRURL = "https://chart.googleapis.com/chart?cht=qr&chs=185x185&chl=" . $short_url['shortLink'] . "&chld=M|0";
				    $QRURL = "https://api.qrserver.com/v1/create-qr-code/?data=" . $short_url['shortLink'] . "&size=185x185&ecc=M";

		            $QRCode = file_get_contents($QRURL);
		            $QRImage = "images/qrcode/CSQRIMG" . $profile_id . ".png";

		            $qrcodeid = Model::factory('qrcode')->saveqrcode($profile_id, $QRImage);
		            $this->session->set("successmessage", "successfully added URL Link.");
		            
		            $file = fopen(DOCROOT . $QRImage, "w");
		            fwrite($file, $QRCode);
		            fclose($file);
					
					// Start generating qr code with color 
				    $googleapis = url::base() . "qrcode/code.php?type=URL&height=185&width=185&block_size=5&url=" . $short_url['shortLink'] . "&foreground_color=" . $color . "&border_size=4&background_color=FFFFFF&block_style=square_blocks";
				    
				    $qrApis = @file_get_contents($googleapis);

				    $file = fopen($QRImage, "w");
				    fwrite($file, $qrApis);
				    fclose($file);
				    // END  generating qr code with color
					
					
			}
			elseif ($code_type == 1) { // Data matrix
		      $QRCode = file_get_contents(URL::base() . "dm_code/index.php?data_url=" . $short_url['shortLink'] . "&matrix_size=10&matrix_type=URL&fc=".$color."&bc=FFFFFF&matrix_block_style=square_blocks");
		      
			  $QRImage = "images/dmcode/DMIMG" . $profile_id . ".png";
		      
			  
			  $qrcodeid = Model::factory('qrcode')->saveqrcode($profile_id, $QRImage);
              $this->session->set("successmessage", "successfully added URL Link.");
           	  
		      $file = fopen(DOCROOT . $QRImage, "w");
		      fwrite($file, $QRCode);
		      fclose($file);
		    }
			
          }
		  else //edit Mode
		  {
		  		$qrimg = Model::factory('qrcode')->getQRCodeByProfileId($profile_id);
				
				if($qrimg)
				{
						if($qrimg['qrimg_name']!="" && file_exists($qrimg['qrimg_name']))
							unlink($qrimg['qrimg_name']);
				}
			
		  		require_once("classes/shortURL.php");
		  		$api_token = 'KqXZgB6aPc7IkpDB1UlqALKp0v1yhCcjPI40dkprS8UOCypvfYu7KKNrn0QY';
      			$short_url = shortenUrl($qrurl, $api_token);
	            //$short_url = shortenUrl($qrurl);

	            $data = array("shorturl" => $short_url['shortLink']);
	            Model::factory('profiles')->updateProfile($profile_id, $data);
				
				$color = isset($_POST['color']) ? $_POST['color'] : '#FFFFFF';
    			$color = str_replace('#', '', $color);
			
				if($code_type=="0")// QR Code
			    {
					    //$QRURL = "https://chart.googleapis.com/chart?cht=qr&chs=185x185&chl=" . $short_url['shortLink'] . "&chld=M|0";
					    $QRURL = "https://api.qrserver.com/v1/create-qr-code/?data=" . $short_url['shortLink'] . "&size=185x185&ecc=M";

			            $QRCode = file_get_contents($QRURL);
						
			            $QRImage = "images/qrcode/CSQRIMG" . $profile_id . ".png";

			            $qrcodeid = Model::factory('qrcode')->update_qrcode($profile_id, $QRImage);
			            
			            
			            $file = fopen(DOCROOT . $QRImage, "w");
			            fwrite($file, $QRCode);
			            fclose($file);
						
						// Start generating qr code with color 
					    $googleapis = url::base() . "qrcode/code.php?type=URL&height=185&width=185&block_size=5&url=" . $short_url['shortLink'] . "&foreground_color=" . $color . "&border_size=4&background_color=FFFFFF&block_style=square_blocks";
					    
					    $qrApis = @file_get_contents($googleapis);

					    $file = fopen($QRImage, "w");
					    fwrite($file, $qrApis);
					    fclose($file);
					    // END  generating qr code with color
						
				}
				elseif ($code_type == 1) 
				{ // Data matrix
				  
				
				
			      $QRCode = file_get_contents(URL::base() . "dm_code/index.php?data_url=" . $short_url['shortLink'] . "&matrix_size=10&matrix_type=URL&fc=".$color."&bc=FFFFFF&matrix_block_style=square_blocks");
			      
				  $QRImage = "images/dmcode/DMIMG" . $profile_id . ".png";
			      
				  
				  $qrcodeid = Model::factory('qrcode')->update_qrcode($profile_id, $QRImage);
	             
	           	 
				  
			      $file = fopen(DOCROOT . $QRImage, "w");
			      fwrite($file, $QRCode);
			      fclose($file);
			    }
		  	
		  }
          // QR CODE end 
          if ($mode == 'add') {
            $this->request->redirect('code/index/' . $profile_id);
          } else {
            $this->request->redirect('code/index/' . $profile_id);
          }
          exit;
        }
      endif;
    }
	

    // Default Content template
    $this->template->content = View::factory('code/index')
            ->bind("errors", $errors)
            ->bind("feilds", $feilds)
            ->bind("add_form_display", $add_form_display)
            ->bind("mode", $mode)
            ->bind("profile_id", $profile_id)
            ->bind('errormessage', $errormessage)
            ->bind('successmessage', $successmessage)
            ->bind('expried_or_not', $expried_or_not);


    // Default inner menu template
    $this->template->content->innermenu = View::factory('template/innermenu')
            ->bind('equiptypes', $this->types)
            ->bind('activemenu', $param);
  }

  /*   * **
   * **** upload graphic image 
   * ** */

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
      
    if($this->session->get('voc_user_id')!="" )
    {
        $this->request->redirect(URL::base().'dashboard/voc_user');
        exit;
    }
    
    // set title tag 
    $this->template->head->title = "Code Delete";
	
	$user_data = Model::factory('user')->getUserByID($this->session->get('clientuserid'));
	
	if($user_data["is_sub_user"]==1 && $user_data["access_delete"]==0)
	{
		Session::instance()->set('errormessage', 'Oops! You have no access to delete profile.');
		$this->request->redirect('dashboard/code');
	}

    $id = $this->request->param('id');
    if (isset($id) && $id):

      $profiledata = Model::factory('code')->getProfilesById($id);

      if (sizeof($profiledata) == 0) {
        Session::instance()->set('errormessage', 'Oops! Access denied for requested profile.');
        $this->request->redirect(URL::base() . 'code/index');
      } else {
        if (strtotime($profiledata['expired_at']) < strtotime(date("Y-m-d H:i:s"))  && ($profiledata['free_code']==0 || ($profiledata['free_code']==1 && $profiledata['renewal_required']==1) ) ) {
          $this->session->set("errormessage", "Code is expired.You can not delete that code.");
          $this->request->redirect('code/index');
        } else {

          // delete analytics
          $wpArr = @file_get_contents(GALATECH_API.'item/clearItem?key=' . $profiledata['analytic_key']);
          $response = json_decode($wpArr);

          Model::factory('profiles')->deleteprofile($id);
          $this->session->set("SuccessMessage", "successfully deleted URL Link.");
          $this->request->redirect('dashboard/code');
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

    ///$qrimage = "images/qrcode/CSQRIMG" . $profile_id . ".png";
	
	$qrimg = Model::factory('qrcode')->getQRCodeByProfileId($profile_id);
	$qrimage = $qrimg['qrimg_name'];
	
	if($profiledata["code_type"]=="1") //dm code
	{
			$QRCode = file_get_contents(URL::base() . "dm_code/index.php?data_url=" . $googleShorturl . "&matrix_size=10&matrix_type=URL&fc=" . $color . "&bc=FFFFFF&matrix_block_style=square_blocks");
					      
			$file = fopen(DOCROOT . $qrimage, "w");
			fwrite($file, $QRCode);
			fclose($file);
	}
	else //qr code
	{
		    // Start generating qr code with color 
		    $googleapis = url::base() . "qrcode/code.php?type=URL&height=165&width=165&block_size=5&url=" . $googleShorturl . "&foreground_color=" . $color . "&border_size=4&background_color=FFFFFF&block_style=square_blocks";
		    $datareturn['codeImage'] = URL::base() . $qrimage;
		    //echo json_encode($datareturn);
		    //exit();
		    $qrApis = file_get_contents($googleapis);
			
		    $file = fopen($qrimage, "w");
		    fwrite($file, $qrApis);
		    fclose($file);
		    // END  generating qr code with color
	}
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
     // $_GET['filename'] = $qrimg['qrimg_name'];

      // to download the qr image
      //include_once("download.php");*/
	  
	  $download_as = $_REQUEST["download_as"];
	  $qr_path = $qrimg['qrimg_name'];
	  
	  
	  
	  switch ($download_as)
		{
			case "png":
						
						$download_filename = 'code_'.$profile_id.'.png';	
												
						$download_file_path = $qr_path;
						break;
						
			case "jpg":
						
						$download_filename = 'code_'.$profile_id.'.jpg';	
						$jpgfile = "images/qrcode/code_".$profile_id.".jpg";
						
						exec('convert '.$qr_path.' '.$jpgfile);
						$download_file_path = $jpgfile;
						break;
			
			case "tiff":
						
						$download_filename = 'code_'.$profile_id.'.tiff';	
						$tifffile = "images/qrcode/code_".$profile_id.".tiff";
						
						exec('convert '.$qr_path.' '.$tifffile);
						$download_file_path = $tifffile;
						
						break;
			case "eps":
			
						$download_filename = 'code_'.$profile_id.'.eps';							
						$espfile = "images/qrcode/code_".$profile_id.".eps";
						
						exec('convert '.$qr_path.' '.$espfile);
						$download_file_path = $espfile;
					
						break;
		}
		
		header('Content-Description: File Transfer');
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename='.$download_filename);
		header('Content-Transfer-Encoding: binary');
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');
		header('Content-Length: ' . filesize($download_file_path));
		ob_clean();
		flush();
		readfile($download_file_path);
		
		@unlink($tifffile);
		@unlink($epsfile);
	
		exit;
	  
	  
	  
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
	  
	 
	  //calculate renew amount START
      $noorder = count($renew_profile);
	  
	  //insert into is_reseller_code in code_purchase table
	  $mixing_code = 0;
	  
      $totalsubscrption = 0;
	  
	  $getProfileRenewData = Model::factory('profiles')->getCountnumberRenewProfile($renew_profile);
	 
	 
	  if(count($getProfileRenewData) > 1 ){
	  	$mixing_code = 2;
	  }
	  
	  foreach($renew_profile as $profile)
	  {
	  	$profile_id = $profile;	
		$profile = Model::factory('profiles')->get_expiry_date_by_profileid($profile_id);
		
		if($profile["date_diff"]>"0")
		{
			// Renew before expired - same amount charged when the code was first registered
			// find original price @time of register
			$code_purchase_detail = Model::factory('codepurchase')->get_purchase_detail_by_profile_id($profile_id);
			
			$code_purchase_id = $code_purchase_detail["code_purchase_id"];
			
			$code_purchase_price_arr = Model::factory('codepurchase')->get_per_code_amount_by_id($code_purchase_id);
			
			$per_code_amount_per_month = $code_purchase_price_arr["per_code_amount"];
			
			$permonthamount[] = $per_code_amount_per_month;
			
			$per_code_amount= number_format($per_code_amount_per_month * 12, 2);
			
			
			$totalsubscrption = $totalsubscrption + $per_code_amount ;
			
		}
		else
		{
			// Renew after expired - single code registration price
			
			$codeamount = Model::factory('codeprising')->getAmountByQuantity(1);
			
			if($getProfileRenewData[0]["is_reseller_code"]){
				$per_code_amount_per_month = number_format($codeamount[0]['reseller_amount'], 2);
				
				$mixing_code = ($mixing_code == 2)?$mixing_code:1;
			}else{
				$per_code_amount_per_month = number_format($codeamount[0]['amount'], 2);
			}
			
			$permonthamount[] = $per_code_amount_per_month;
			
			$per_code_amount= number_format($per_code_amount_per_month * 12, 2);
			
			
			$totalsubscrption = $totalsubscrption + $per_code_amount ;
			
			
		}
	  }
	  
	  $temp = array_unique($permonthamount);
	
	// if(count($temp)==1)
	 	//$permonthamount = number_format($temp[0], 2);
	  	
	  /*foreach($getProfileRenewData as $renewData){
	  		
	  		$codeamount = Model::factory('codeprising')->getAmountByQuantity($renewData['count']);
			
			if($renewData['is_reseller_code']){
				$codeamountpermonth = number_format($codeamount[0]['reseller_amount'], 2);
				
				$mixing_code = ($mixing_code == 2)?$mixing_code:1;
			}else{
				$codeamountpermonth = number_format($codeamount[0]['amount'], 2);
			}
      		
      		$mytotalsubscrption = number_format($codeamountpermonth * $renewData['count'] * 12, 2);
			$totalsubscrption = $totalsubscrption + $mytotalsubscrption ;
	  }*/
	  
	  
	  //$permonthamount = number_format($totalsubscrption/(12*$noorder),2);
	  
	  
	  
      //calculate renew amount END
     

      // store rew data to seession 
      $sessiondata = array();
      $sessiondata['profile_id'] = $renew_profile;
      $sessiondata['noorder'] = $noorder;
      $sessiondata['amount'] = $permonthamount;
      $sessiondata['totalsubscrption'] = $totalsubscrption;
	  $sessiondata['mixing_code'] = $mixing_code;
	  
	  //$sessiondata['simple_amount'] = number_format($codeamount[0]['amount'], 2);
	  //$sessiondata['reseller_amount'] = number_format($codeamount[0]['reseller_amount'], 2);
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
    $this->template->head->title = "Code Renewed";
    // get session data to renew profile 
    $renew_profile_data = $this->session->get('renewdata');
	
	
	
    // if not get the redirect to dashboard/mastercode to select the profile to be renew
    if (!empty($renew_profile_data)) {
	 
		// get user billing information 
      $userdata = Model::factory('user')->getUserByID($this->session->get('clientuserid'));
      
		$client_reseller_name = ""; //use in admin mail
	   if($userdata["client_reseller_code"]!="") 
	   {
	    	$client_reseller_name = Model::factory('client')->get_client_name_using_reseller_code($userdata["client_reseller_code"]);
   	} 
		
		     
      
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
      $data['total_amount'] = $renew_profile_data['totalsubscrption'];
	  	$data['is_reseller_pricing_code'] = (string)$renew_profile_data['mixing_code'];

      $returndata = Model::factory('codepurchase')->renewcodepurchase($userdata['client_id'], $userdata['id'], $data);
	   
	 
      $datafill = array();
      $datafill['pop_up_renew'] = '1';
      Model::factory('user')->updateuser($datafill, $userdata['id']);

      // send mail  START
      include_once("mailsend.php");
      $adminemail = Model::factory('settings')->getValueByTitle('contact_email');
      $adminemail = "admin@scanlink.net.au";
      $baseurl = URL::base();
     
	  if(count($renew_profile_data['amount'])>1)
	  {
			$amount_per_code = "(";
			for($j=0;$j<count($renew_profile_data['amount']);$j++)
			{
				if($j!=0)
					$amount_per_code .= " and ";
					
				$amount_per_code .= "one code with $".$renew_profile_data['amount'][$j];
				
			}
			
			$amount_per_code .= ")";
	  }
	  else
	  {
			$amount_per_code = "$".$renew_profile_data['amount'][0];
	  }
	 
	  $amount_arr = array();
	  $amount_per_code = "";
	  foreach($renew_profile_data['amount'] as $amount)
	  {
			if(!in_array($amount, $amount_arr))
			{
				array_push($amount_arr,$amount);
				$cnt = count(array_keys($renew_profile_data['amount'], $amount));
				$total_amt =number_format(12*$amount,2);
				
				$amount_per_code .= "$cnt code/s @ $$total_amt<br>";
			} 		
		}
		 
	  
	  if($returndata['email_own_array'] > 0)
	  {
	  	$data['no_of_codes'] = $returndata['email_own_array'];
		$data['amount_per_code'] = $amount_per_code;
		
		//send mail to user START 
	  	renewCode_mail_client($adminemail, $baseurl, $data);
		//send mail to user END
		
		//send mail to admin START 
		renewCode_mail_admin($adminemail, $baseurl, $data,$client_reseller_name);
      //send mail to admin END
	  
	  }
	  if(!empty($returndata['email_reseller_array'])){
	  	
	  	
	  	$data['amount_per_code'] = $amount_per_code;
		$clientEmail = $data['email'];
	  	foreach($returndata['email_reseller_array'] as $mykey=>$myvalue){
			$resellerData = Model::factory('client')->getClientById($mykey);
			
			$data['user_email'] = $clientEmail;
			
			$data['email'] = $resellerData['reseller_email'];
			$data['no_of_codes'] = $myvalue;
			$data['name'] = $resellerData['client_name'];
			
			 //send mail to reseller  START 
			reseller_renewCode_mail_client($adminemail, $baseurl, $data);
			//send mail to reseller  END
			
			$namesplit = explode(" ",$resellerData['client_name']);
			$data['first_name'] = $namesplit[0];
			$data['last_name'] = isset($namesplit[1])?$namesplit[1]:$namesplit[0];
			//send mail to admin START 
      	reseller_renewCode_mail_admin($adminemail, $baseurl, $data);
      	//send mail to admin END
	  
		}
	  }
      
     
	 
      
      // send mail  END
		


      $this->session->delete('renewdata');
      //$this->session->set("SuccessMessage","Your ".$renew_profile_data['noorder']." URL Link has been renewed successfully. Our team will send invoice in 14 days.");
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
          	$this->session->set("SuccessMessage","Code(s) has been renewed successfully. Our team will send invoice in 14 days.");
            $this->request->redirect('dashboard/mastercode');
            break;
		  case 10 :
            $this->request->redirect('dashboard/survey');
            break;
		  case 11 :
            $this->request->redirect('dashboard/exhibit');
            break;
        }
      } else {
      	
        $this->request->redirect('dashboard/mastercode');
      }
      exit;
    } else {
      $this->session->set("errormessage", "Please select code to be renewed.");
      $this->request->redirect('dashboard/mastercode');
      exit;
    }
  }

}

// End Welcome
