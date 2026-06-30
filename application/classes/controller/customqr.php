<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Customqr extends Controller_Template_Master {
	public $types='';
	public $userid = '';
	public $video_permission = 0;
	public $client_id = '';	
	public function before() {
		
		parent::before();
		
		if(!$this->session->get('clientuserid')):
			$this->request->redirect(URL::base().'welcome/index');
		endif;
		// Default Head view file
		$this->template->head = View::factory('template/head');
		
		// Set the default Page Title
		$this->template->head->title = 'Welcome to ScanLink';	
		
		// All CSS Stylesheets
		$this->template->head->stylesheets = array(array('href'=>'styles/style.css','media'=>'screen'),array('href'=>'styles/colorbox.css','media'=>'screen'),array('href'=>'styles/coin-slider-styles.css','media'=>'screen'));
		
		// All JS Libraries and Scripts
		$this->template->head->javascripts = array('js/jquery.min.js','js/jquery.validate.min.js','js/jquery.colorbox.js','js/jquery.autogrow-textarea.js','js/coin-slider.min.js', 'js/dashboard/tooltip.js', 'js/dashboard/jquery-ui-1.js','js/dashboard/custom.js', 'js/jquery.uniform.js' ,'js/custome.js' ,'js/modernizr.custom.60079.js');
		
		// Default Header view file
		$this->template->headers = View::factory('template/headers');
		
		// Default menu
		$this->template->headers->menu = View::factory('template/menu');
		if($this->session->get('clientuserid')):
			$this->template->headers->menu->logoutlnk = $this->session->get('clientusername');
		endif;
		$this->template->headers->menu->menu = $this->menu;
		/* WP_MS START  */
		$this->template->headers->menu->menuSubmenu = $this->menuSubmenu;
		/* WP_MS END  */
		$this->template->headers->menu->currentactive = $this->request->controller()."/".$this->request->action();
		// Default Content template
		$this->template->footer = View::factory('template/footer');
		
		if($this->template->footer):
		// All JS Libraries and Scripts
		$this->template->footer->scripts = array();
		endif;
		$this->types  = Model::factory('dashboard')->getTypes();
		$this->userid = $this->session->get('clientuserid');
		$this->client_id = $this->session->get('client_id');		
		//$this->video_permission  = Model::factory('user')->getVideoPermissionByCid($this->userid);
	}

	public function action_index()
	{	
		$client = Model::factory('user')->getClientById($this->userid);
		$customqr_option = Model::factory('client')->isCustomqrEnabledToClient($client['id']);
		$customqr = $customqr_option[0]['customqr_option'];
		if($customqr == '0'){
			$this->request->redirect('dashboard/index');
					exit;
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

					$profileid  = Model::factory('customqr')->insertCustomqr($postArr, $this->userid);

					require_once("classes/shortURL.php");
					$api_token = 'KqXZgB6aPc7IkpDB1UlqALKp0v1yhCcjPI40dkprS8UOCypvfYu7KKNrn0QY';
      				$short_url = shortenUrl($txturl, $api_token);
					//$short_url = shortenUrl($txturl);
					$data = array("shorturl"=>$short_url['shortLink']);
					Model::factory('profiles')->updateProfile($profileid,$data);
					
					//$QRURL = "https://chart.googleapis.com/chart?cht=qr&chs=250x250&chl=".$short_url['shortLink']."&chld=M|0";
					$QRURL = "https://api.qrserver.com/v1/create-qr-code/?data=" . $short_url['shortLink'] . "&size=250x250&ecc=M";
					
					file_put_contents(DOCROOT. "debug/test.txt", $short_url);
					$QRCode = file_get_contents($QRURL);
					$QRImage = "images/qrcode/CSQRIMG".$profileid."_test.png";
					$qrcodeid  = Model::factory('qrcode')->saveqrcode($profileid,$QRImage);
					$file = fopen(DOCROOT.$QRImage,"w");
					fwrite($file,$QRCode);
					fclose($file);
					$this->request->redirect('dashboard/customqr');
					exit;
			endif;			
		endif;
		
		$param = 'customqr/index';
		// Default Content template
		$this->template->content = View::factory('customqr/index');
		
		$this->template->content->innermenu = View::factory('template/innermenu')
		->bind('equiptypes',$this->types)
		->bind('customqr',$customqr)
		->bind('activemenu', $param);
		
	}
	public function action_realtimeqr(){
		$qrtext = $this->request->param('qrtext');
		//$QRURL = "https://chart.googleapis.com/chart?cht=qr&chs=250x250&chl=".$qrtext."&chld=M|0";
		$QRURL = "https://api.qrserver.com/v1/create-qr-code/?data=" . $qrtext . "&size=250x250&ecc=M";
		$QRCode = file_get_contents($QRURL);
		$QRImage = "images/qrcode/temp/CSQRIMG.png";
		unlink(DOCROOT.$QRImage);
		$file = fopen(DOCROOT.$QRImage,"w");
		fwrite($file,$QRCode);
		fclose($file);
		echo $QRImage;
		exit;
	}
	public function action_delete(){
		$customqr_option = Model::factory('client')->isCustomqrEnabledToClient($this->client_id);
		$customqr = $customqr_option[0]['customqr_option'];
		if($customqr == '0'){
			$this->request->redirect('dashboard/index');
			exit;
		}
		$id = $this->request->param('id');
		if(isset($id) && $id):			
			Model::factory('profiles')->deleteprofile($id);
		endif;
		$this->request->redirect('dashboard/customqr');
	}
} // End Welcome
