<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Customqr extends Controller_Template_Master {
	public $YT_DEVELOPER_KEY,$YT_CLIENT_ID,$YT_APPLICATION_ID,$USERNAME,$PASSWORD;
	public $userid = '';
	public $session='';
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

	public function action_index()
	{	
		$userid = $this->request->param('id');
		$uemail = Model::factory("user")->getUserEmailById($userid);
		if(!empty($uemail)){
			$uemail = $uemail[0]['email'];
		}
		if(!empty($_POST) && !isset($_POST["edit_id"])):
			
			$post = $_POST;
			$txturl = trim($_POST['txturl']);
			if($txturl == ''):
				$errors['txturl'] = "Please provide url for CustomQR";
			else:			
			
					$postArr['type'] = 'customqr';
					$postArr['txturl'] = $txturl;
					$postArr['created_at'] = date('Y-m-d H:i:s');
					
					$profileid  = Model::factory('customqr')->insertCustomqr($postArr, $_POST['userid']);
					
					require_once("../classes/shortURL.php");
					$api_token = 'KqXZgB6aPc7IkpDB1UlqALKp0v1yhCcjPI40dkprS8UOCypvfYu7KKNrn0QY';
      				$short_url = shortenUrl($txturl, $api_token);
					//$short_url = shortenUrl($txturl);
					$data = array("shorturl"=>$short_url['shortLink']);
					Model::factory('profiles')->updateProfile($profileid,$data);
					
					//$QRURL = "https://chart.googleapis.com/chart?cht=qr&chs=250x250&chl=".$short_url['shortLink']."&chld=M|0";
					$QRURL = "https://api.qrserver.com/v1/create-qr-code/?data=" . $short_url['shortLink'] . "&size=250x250&ecc=M";
					
					$QRCode = file_get_contents($QRURL);
					$QRImage = "images/qrcode/CSQRIMG".$profileid.".png";
					$qrcodeid  = Model::factory('qrcode')->saveqrcode($profileid,$QRImage);
					$file = fopen(DOCROOT.$QRImage,"w");
					fwrite($file,$QRCode);
					fclose($file);
					$this->request->redirect('product/');
					exit;
			endif;			
		endif;
		// Default Content template
		$this->template->content = View::factory('customqr/index')->bind('userid',$userid)->bind('uemail',$uemail);		
	}	
	
	public function action_realtimeqr(){
		$qrtext = $this->request->param('qrtext');
		//$QRURL = "https://chart.googleapis.com/chart?cht=qr&chs=250x250&chl=".$qrtext."&chld=M|0";
		$QRURL = "https://api.qrserver.com/v1/create-qr-code/?data=" . $qrtext . "&size=250x250&ecc=M";
		$QRCode = file_get_contents($QRURL);
		$QRImage = "images/qrcode/temp/CSQRIMG.png";
		$docroot = str_replace('\\','/',DOCROOT);
		unlink($docroot.$QRImage);
		$file = fopen($docroot.$QRImage,"w");
		fwrite($file,$QRCode);
		fclose($file);
		echo $QRImage;
		exit;
	}
	/*public function action_add()
	{
		$errors = '';
		$succmsg = '';
		$last_id = array();
		$postArr = array();
		if(isset($_POST) && $_POST):
			$logo_file = $_FILES['logo_file']['name'];					
			$txtname = trim($_POST['txtname']);
			$txtidenty = trim($_POST['txtidenty']);
			$txtsrno = trim($_POST['txtsrno']);
			$txtdesc = trim($_POST['txtdesc']);
			$txtnotes = trim($_POST['txtnotes']);
			$txtcompname = trim($_POST['txtcompname']);
			$txttelephone = trim($_POST['txttelephone']);
			$picture_file = $_FILES['picture_file']['name'];	
			$docu_file = $_FILES['docu_file']['name'];
			$video_file = $_FILES['video_file']['name'];			
			if( $logo_file == ''):
				$errors = "Client name can't left blank..!";
			elseif($txtname == ''):
				$errors = "Address can't left blank..!";
			elseif($txtidenty == ''):
				$errors = "Telephone can't left blank..!";
			elseif($txtsrno == ''):
				$errors = "Contact Person can't left blank..!";
			elseif($txtdesc == ''):
				$errors = "Registration Date can't left blank..!";
			elseif($txtnotes == ''):
				$errors = "Email Address can't left blank..!";
			elseif($txtcompname == ''):
				$errors = 'Please enter valid email address..!';
			elseif($txttelephone == ''):
				$errors = "Password can't left blank..!";
			elseif($picture_file == ''):
				$errors = "URL can't left blank..!";
			elseif($docu_file == ''):
				$errors = "URL can't left blank..!";
			elseif($video_file == ''):
				$errors = "URL can't left blank..!";
			else:			
					$postArr['logo_file'] = $logo_file;
					$postArr['type'] = 'plant';
					$postArr['txtname'] = $txtname;
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
					$last_id  = Model::factory('plant')->insertplant($postArr, $this->userid);
					$this->request->redirect('dashboard/profile');			
			endif;					
		endif;			
	}*/
	
	/*public function action_edit()
	{
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
		if(isset($id) && $id):
			$profiledata = Model::factory('profiles')->getProfileById($id);
			
			$post["edit_id"]=$id;
			$post["logo"]=Model::factory('logo')->getLogoById($id);
			$post["pictures"]=Model::factory('picture')->getPicturesByProfileId($id);
			$post["documents"]=Model::factory('documents')->getDocumentsByProfileId($id);
			$post["videos"]=Model::factory('video')->getVideoByProfileId($id);
			$uemail = Model::factory('user')->getUserEmailByClientId($profiledata['client_id']);
			if(!empty($uemail)){
				$uemail = $uemail[0]['email'];
			}	
			$post['user_id'] = $profiledata['user_id'];
			$post['txtname'] = $profiledata['name'];
			$post['txtdesc'] = $profiledata['description'];
			$post['txtnotes'] = $profiledata['notes'];
			$post['txtcompname'] = $profiledata['name_company'];
			$post['txttelephone'] = $profiledata['telephone'];
			$post['protect'] = $profiledata['protect'];
			if($post['protect'] == '1'):
				$yes = TRUE;
				$no = FALSE;
			else:
				$yes = FALSE;
				$no = TRUE;
			endif;
		endif;	
		$qrimg = Model::factory('qrcode')->getQRCodeByProfileId($id);
		$checklist_items = Model::factory('checklistitem')->getChecklist_itemsByProfileId($id);

		$this->template->content = View::factory('procedure/edit')
		->bind('yes',$yes)
		->bind('no',$no)
		->bind('checklist_items',$checklist_items)		
		->bind('post',$post)
		->bind('qrimg',$qrimg)
		->bind('uemail',$uemail);		
	}
	
	public function action_delete(){
		$id = $this->request->param('id');
		if(isset($id) && $id):
			Model::factory('profiles')->deleteprofile($id);
		endif;
		$this->request->redirect('dashboard/procedure');
	}*/
	
} // End Welcome
