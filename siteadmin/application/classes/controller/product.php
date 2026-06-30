<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Product extends Controller_Template_Master {
	public $YT_DEVELOPER_KEY,$YT_CLIENT_ID,$YT_APPLICATION_ID,$USERNAME,$PASSWORD;
	public $session='';
	protected $type_id = 4;
	
	public function before() {
		
		parent::before();
		
		$this->session = Session::instance();
		if(!$this->session->get('adminuserid')):
			$this->request->redirect('welcome/index');
		endif;
		
		// Default Head view file
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
	
		$this->session->delete('tmp_files');
		$profiles = Model::factory('profiles')->getAllProfiles();
		$qr=array();
		if(!empty($profile)){
			$qr  = Model::factory('qrcode')->getQRCodeByProfileId($profile[0]['id']);
		}
		

		
		$this->template->content = View::factory('product/index')
		->bind('profiles',$profiles);
		
	}
	
	public function action_add()
	{
		if(isset($_POST['btnsubmit']) && $_POST['btnsubmit'] != ''){
			$clientid = $_POST['clientid'];
			$typeid = $_POST['etypes'];
			$cname = '';
			if($typeid == 1)
					$cname = 'plant/index/';
				if($typeid == 2)
					$cname = 'location/index/';
				if($typeid == 3)
					$cname = 'asset/index/';
				if($typeid == 4)
					$cname = 'product/addpro/';
				if($typeid == 5)
					$cname = 'procedure/index/';
				if($typeid == 6)
					$cname = 'people/index/';
				if($typeid == 7)
					$cname = 'customqr/index/';	
				if($typeid == 8)
					$cname = 'misc/index/';	
				if($typeid == 9)
					$cname = 'code/index/';	
				$this->request->redirect($cname.$clientid);
				exit;
		}
		$clients = Model::factory('user')->getAllClientsFromUsers();
		$types = Model::factory('dashboard')->getTypes();
		$this->template->head = '';
		$this->template->headers = '';
		$this->template->menu = '';
		$this->template->content = View::factory('product/add')
		->bind('clients',$clients)
		->bind('types',$types);
		
	}
	public function action_addpro()
	{
		if(!empty($_POST) && !isset($_POST["edit_id"])):
		
			$post = $_POST;
			$this->saveProduct('add',$post);
			$this->request->redirect('product/');
			exit;
		elseif(!empty($_POST) && isset($_POST["edit_id"])):
			$post = $_POST;
			$this->saveProduct('edit',$post);
			$this->request->redirect('product/');
			exit;
		endif;
		
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
		$this->template->content = View::factory('product/addpro')
									->bind('userid',$userid)
									->bind('uemail',$uemail);
									
		$this->template->content->web_link_data = View::factory('template/weblink_template')
            ->bind('post', $post)
            ->bind('errors', $errors)
            ->bind('client_id', $userid)
            ->bind('profile_id', $id);
			
		$this->template->content->contact_data = View::factory('template/contact_template')
			->bind('post', $post)
            ->bind('errors', $errors)
            ->bind('client_id', $userid)
			->bind('profile_id', $id);
	
	}
	
	public function action_delete()
	{
		$id = $this->request->param('id');
		if(isset($id) && $id):
			Model::factory('profiles')->deleteprofile($id);
		endif;
		$this->request->redirect('product/index');
	}
	
	public function action_edit()
	{
		$id = $this->request->param('id');

		$profiledata = array();
		$errors = '';
		$succmsg = '';
		$last_id = array();
		$postArr = array();
		$post = array();
		$yes = '';
		$no = '';
		$last_id = $id;
		if(isset($id) && $id && !(isset($_POST['btnEditprofile']) && $_POST['btnEditprofile'] !='')){

			$profiledata = Model::factory('profiles')->getProfileById($id);
			
			if($profiledata['type_id'] == '7'){
				$this->request->redirect('product/index');
				exit;
			}
			
			if($profiledata['type_id'] != $this->type_id){
				$this->request->redirect('product/');
				exit;
			}
			
			$post['eq_type'] = Model::factory('profiles')->getEquipmentTypeById($profiledata['type_id']);
			$post["edit_id"]=$id;		
			$post["logo"]=Model::factory('logo')->getLogoById($id);
			$post["pictures"]=Model::factory('picture')->getPicturesByProfileId($id);
			$post["documents"]=Model::factory('documents')->getDocumentsByProfileId($id);
			$post["videos"]=Model::factory('video')->getVideoByProfileId($id);
			$post['txtname'] = $profiledata['name'];
			$uemail = Model::factory('user')->getUserEmailByClientId($profiledata['client_id']);
			if(!empty($uemail)){
				$uemail = $uemail[0]['email'];
			}
			$post['user_id'] = $profiledata['user_id'];
			$post['txtidenty'] = $profiledata['identification'];
			$post['txtsrno'] = $profiledata['serial_no'];
			$post['txtdesc'] = $profiledata['description'];
			$post['txtnotes'] = $profiledata['notes'];
			$post['txtcompname'] = $profiledata['name_company'];
			$post['txttelephone'] = $profiledata['telephone'];
			$post['txtaddress'] = $profiledata['address'];
			$post['protect'] = $profiledata['protect'];
			if($post['protect'] == '1'):
				$yes = TRUE;
				$no = FALSE;
			else:
				$yes = FALSE;
				$no = TRUE;
				
			endif;
			
			$post['enable_data_collection'] = $profiledata['enable_data_collection'];
			$post['set_up_compulsory'] = $profiledata['set_up_compulsory'];
			$post['data_collection_mobile'] = $profiledata['data_collection_mobile'];
			$post['data_collection_email'] = $profiledata['data_collection_email'];
			$post['data_collection_name'] = $profiledata['data_collection_name'];
			$post['data_collection_content'] = $profiledata['data_collection_content'];
			
			$post['code_type'] = $profiledata['code_type'];
			$post['show_header'] = $profiledata['show_header'];
			
			$post['password'] = $profiledata['password'];
			$post['display_share_link'] = $profiledata['display_share_link'];
			
			
			$post["weblink"] = Model::factory('weblink')->getWeblinkByProfileId($id);

      		$post["contact"] = Model::factory('profilecontact')->getContactByProfileId($id);
			
		}else{
			$this->request->redirect('product/');
			exit;
		}
		
		$qrimg = Model::factory('qrcode')->getQRCodeByProfileId($id);
		$checklist_items = Model::factory('checklistitem')->getChecklist_itemsByProfileId($id);		

		
		$this->template->content = View::factory('product/edit')
										->bind('yes',$yes)
										->bind('no',$no)
										->bind('checklist_items',$checklist_items)		
										->bind('post',$post)
										->bind('qrimg',$qrimg)
										->bind('succmsg',$succmsg)
										->bind('errors',$errors)
										->bind('uemail',$uemail);
		
		$this->template->content->web_link_data = View::factory('template/weblink_template')
										            ->bind('post', $post)
										            ->bind('errors', $errors)
										            ->bind('client_id', $userid)
										            ->bind('profile_id', $id);
			
		$this->template->content->contact_data = View::factory('template/contact_template')
													->bind('post', $post)
										            ->bind('errors', $errors)
										            ->bind('client_id', $userid)
													->bind('profile_id', $id);

	}
	
	public function action_view()
	{
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
			
			$eq_type=Model::factory('dashboard')->getTypeById($profiledata['type_id']);
			
			$profiledata["edit_id"]=$id;
			$profiledata["logo"]=Model::factory('logo')->getLogoById($id);
			$profiledata["pictures"]=Model::factory('picture')->getPicturesByProfileId($id);
			$profiledata["documents"]=Model::factory('documents')->getDocumentsByProfileId($id);
			$profiledata["videos"]=Model::factory('video')->getVideoByProfileId($id);
			if($profiledata['protect'] == '1'):
				$yes = TRUE;
				$no = FALSE;
			else:
				$yes = FALSE;
				$no = TRUE;
			endif;
			
			$profiledata["weblink"] = Model::factory('weblink')->getWeblinkByProfileId($id);

      		$profiledata["contact"] = Model::factory('profilecontact')->getContactByProfileId($id);
		endif;	
		
		$qrimg = Model::factory('qrcode')->getQRCodeByProfileId($id);
		$this->template->content = View::factory('product/view')
										->bind('yes',$yes)
										->bind('no',$no)
										->bind('post',$profiledata)
										->bind('qrimg',$qrimg)
										->bind('eq_type',$eq_type);
		
		
	}
	
	
	public function action_delete_picture(){
		header('content-type: application/json');
		
		$picture_id = $this->request->param("id");
		
		$picture = Model::factory('picture')->getPictureById($picture_id);
		
		if(unlink(realpath($picture["picture_name"]))){
			Model::factory('picture')->deletePictureById($picture["id"]);
		}
		
		$pictures = Model::factory('picture')->getPicturesByProfileId($picture["profile_id"]);
		echo json_encode($pictures);
		exit;
//		$this->template->content = View::factory('plant/json/pictures')->bind('pictures',$pictures);
	}
	
	public function action_delete_document(){
		header('content-type: application/json');
		$document_id = $this->request->param("id");
		
		$document = Model::factory('documents')->getDocumentById($document_id);
		
		if(unlink(realpath($document["doc_name"]))){
			Model::factory('documents')->deleteDocumentById($document["id"]);
		}
		
		$documents = Model::factory('documents')->getDocumentsByProfileId($document["profile_id"]);
		echo json_encode($documents);
		exit;
	}
	
	public function saveProduct($action, $post){
		$txtname = isset($post['txtname']) ? trim($post['txtname']) : '';
		$txtvideos = isset($post['video']) ? trim($post['video']) : '';
		
		$txtaddress = trim($_POST['txtaddress']);
		$txtidenty = trim($_POST['txtidenty']);
		$txtsrno = isset($post['txtsrno']) ? trim($post['txtsrno']) : '';
		
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
		
		$enable_data_collection = $post['enable_data_collection'];
		$set_up_compulsory = isset($post['set_up_compulsory']) ? $post['set_up_compulsory'] : '';
		$data_collection_mobile = isset($post['data_collection_mobile']) ? $post['data_collection_mobile'] : '0';
		$data_collection_email = isset($post['data_collection_email']) ? $post['data_collection_email'] : '0';
		$data_collection_name = isset($post['data_collection_name']) ? $post['data_collection_name'] : '0';
	
		$data_collection_content = isset($post['data_collection_content']) ? trim($post['data_collection_content']) : '';
		
		$protect = $post['protect'];
		$password = $post['password'];
		$show_header = isset($post['show_header']) ? 1 : 0;
		$code_type = $post['code_type'];
		
	
		$checklist_items = (isset($post['checklist_item']) && !empty($post['checklist_item'])) ? $post['checklist_item'] : array();
	
		$videos = array();
		$videos1 = explode(",", $txtvideos);
	
		foreach ($videos1 as $key => $value) {
		  if ($value != '') {
			$value = explode("=", $value);
			if (count($value) <= 2) {
			  if ($value[1] != '')
				$videos[$key] = array("title" => $value[0], "id" => $value[1]);
			}else {
			  if ($value[2] != '')
				$videos[$key] = array("title" => $value[0], "id" => $value[2]);
			}
		  }
		}
		//array_pop($videos);
	
	
		$postArr['type'] = 'product';
		$postArr['txtname'] = $txtname;
		
		$postArr['txtaddress'] = $txtaddress;
		$postArr['txtidenty'] = $txtidenty;
		$postArr['txtsrno'] = $txtsrno;
		
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
	
		$postArr['display_share_link'] = $display_share_link;
	
		$postArr['set_up_compulsory'] = $set_up_compulsory;
		$postArr['data_collection_mobile'] = $data_collection_mobile;
		$postArr['data_collection_email'] = $data_collection_email;
		$postArr['data_collection_name'] = $data_collection_name;
	
		$postArr['data_collection_content'] = $data_collection_content;
	
		$postArr['checklist_items'] = $checklist_items;
	
		$postArr['videos'] = $videos;
	
	
		if ($action == "add") {
		  $profileid  = Model::factory('product')->insertProduct($postArr, $post['userid']);
		} elseif ($action == "edit") {
		  $profileid  = Model::factory('product')->updateProduct($postArr,$post["edit_id"]);
		}
	
	
	
		$tmp_files = $this->session->get('tmp_files', FALSE);
	
		if ($tmp_files) {
		  Model::factory('documents')->saveDocFiles($tmp_files, $profileid);
		  Model::factory('picture')->savePictureFiles($tmp_files, $profileid);				
		  Model::factory('logo')->saveLogoFile($tmp_files, $profileid);
		  $this->session->set('tmp_files', FALSE);
		}
	
	
		if ($action == "add") {
		  $clienturl = Model::factory('client')->geturl($profileid);
		  $qrurl = URL::base() . $clienturl . "/" . $profileid;
		} elseif ($action == "edit") {
		  $clienturl = Model::factory('client')->geturl($post["edit_id"]);
		  $qrurl = URL::base() . $clienturl . "/" . $post["edit_id"];
		}
	
		$wpArr = @file_get_contents(GALATECH_API.'item/addurl?url=' . $qrurl);
		$response = json_decode($wpArr);
		
		if ($response->response) {
		  $data = array("analytic_key" => $response->item_key);
		  Model::factory('profiles')->updateProfile($profileid, $data);
		}
	
		require_once("../classes/shortURL.php");
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
	}
	
} // End Welcome
