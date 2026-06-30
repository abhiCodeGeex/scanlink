<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Test extends Controller_Template_Master {
	public $YT_DEVELOPER_KEY,$YT_CLIENT_ID,$YT_APPLICATION_ID,$USERNAME,$PASSWORD;
	public function before() {
		
		parent::before();
		
		// Default Head view file
		$this->template->head = View::factory('template/head');
		
		// Set the default Page Title
		$this->template->head->title = 'Welcome to ScanLink';	
		
		// All CSS Stylesheets
		$this->template->head->stylesheets = array(array('href'=>'styles/style.css','media'=>'screen'), array('href'=>'styles/coin-slider-styles.css','media'=>'screen'), array('href'=>'fileuploader/css/ui-lightness/jquery-ui-1.8.14.custom.css','media'=>'screen'), array('href'=>'fileuploader/css/fileUploader.css','media'=>'screen'));
		
		// All JS Libraries and Scripts
		$this->template->head->javascripts = array('js/jquery.min-1-7-1.js', 'js/coin-slider.min.js', 'fileuploader/js/jquery-ui-1.8.14.custom.min.js', 'fileuploader/js/jquery.fileUploader.js');
		
		$this->template->head->extrascripts	= 'slider';
		
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
		$client_id = $this->request->param("id");
		//$client_id = Model::factory("profiles")->getUserIdByProfileId($pro_id);
		$msg="";
		if(isset($_GET) && !empty($_GET) && isset($_GET['id'])){ ?>
            <script type="text/javascript" language="javascript">
			
				parent.document.getElementById("video").value+=parent.document.getElementById("videoTitle").value+'='+'<?php echo $_GET['id'].","; ?>';
				parent.document.getElementById("videoTitle").value="";
			</script>
            <?php
			$msg = "<span style='color:green'>Video upload Success! Close window to return to profile</span>";
		}else if(isset($_GET['code'])){
			$msg = "<span style='color:red'>Video Upload Failed - Please Try again !</span>";
		}
		// Default Content template

/*		if ($path = Kohana::find_file('vendor', 'Zend/Loader'))
		{
			ini_set('include_path', ini_get('include_path').PATH_SEPARATOR.dirname(dirname($path)));
			require_once 'Zend/Loader/Autoloader.php';
			Zend_Loader_Autoloader::getInstance();
		}*/
		ini_set('include_path', ini_get('include_path').PATH_SEPARATOR.APPPATH.'vendor/');		
		require_once 'Zend/Loader.php';
		Zend_Loader::loadClass('Zend_Gdata_YouTube');
		Zend_Loader::loadClass('Zend_Gdata_ClientLogin');
	
		$developerKey=$this->YT_DEVELOPER_KEY;
		$clientId=$this->YT_CLIENT_ID;
		$applicationId=$this->YT_APPLICATION_ID;
		
		$authenticationURL= 'https://www.google.com/accounts/ClientLogin';
		$httpClient = Zend_Gdata_ClientLogin::getHttpClient(
			$username = $this->USERNAME,
			$password = $this->PASSWORD,
			$service = 'youtube',
			$client = null,
			$source = 'Youtube Video Upload', // a short string identifying your application
			$loginToken = null,
			$loginCaptcha = null,
			$authenticationURL
		);



		$yt = new Zend_Gdata_YouTube($httpClient, $applicationId, $clientId, $developerKey);

		$yt->setMajorProtocolVersion(2);
		
		///start the code for video upload
		
		// Note that this example creates an unversioned service object.
		// You do not need to specify a version number to upload content
		// since the upload behavior is the same for all API versions.
		//$yt = new Zend_Gdata_YouTube($httpClient);
		
		// create a new VideoEntry object
		$myVideoEntry = new Zend_Gdata_YouTube_VideoEntry();
		
		$myVideoEntry->setVideoTitle('My Test Movie');
		$myVideoEntry->setVideoDescription('My Test Movie');
		// The category must be a valid YouTube category!
		$myVideoEntry->setVideoCategory('Autos');
		
		// Set keywords. Please note that this must be a comma-separated string
		// and that individual keywords cannot contain whitespace
		$myVideoEntry->SetVideoTags('cars, funny');
		
		$tokenHandlerUrl = 'http://gdata.youtube.com/action/GetUploadToken';
		$tokenArray = $yt->getFormUploadToken($myVideoEntry, $tokenHandlerUrl);
		$tokenValue = $tokenArray['token'];
		$postUrl = $tokenArray['url'];

		// place to redirect user after upload
		$nextUrl = kohana::$base_url.'test';
		// build the form
		
		
		$ytAll = new Zend_Gdata_YouTube();
		$ytAll->setMajorProtocolVersion(2);
		/*start code for list previously uploaded video */
		$videoData=array();
		$videos = Model::factory("video")->getVideosByClient($client_id);//$this->session->get('clientuserid'));
		$notfound = 0;
		if($videos){
			foreach($videos as $video){
				try{
					$videoEntry = $ytAll->getVideoEntry($video['video_name']);
					array_push($videoData, $this->getVideoDetail($videoEntry,$video['title'],$video['id']));
				}catch(Exception $e){ $notfound++; }
				//$yt->delete($videoEntry);
				//Model::factory('video')->deleteVideoById($video_id);
			}
		}
		/*End code for list previously uploaded video */
		
		$this->template->content = View::factory('test/index')
		->bind('postUrl',$postUrl)
		->bind('nextUrl',$nextUrl)
		->bind('tokenValue',$tokenValue)
		->bind('videoData',$videoData)
		->bind('msg',$msg);
	}
	
	public function action_docUpload(){
		$doc_id = $this->request->param("id");
		$doc_id = Model::factory("profiles")->getUserIdByProfileId($doc_id);
		$this->template->content = View::factory('test/docUpload',array('uid'=>$doc_id));
	}
	
	public function action_pictureUpload(){
		$picture_id = $this->request->param("id");
		$picture_id = Model::factory("profiles")->getUserIdByProfileId($picture_id);
		$this->template->content = View::factory('test/pictureUpload',array('uid'=>$picture_id));
	}
	
	public function action_logoUpload(){
		$logo_id = $this->request->param("id");
		$logo_id = Model::factory("profiles")->getUserIdByProfileId($logo_id);
		$this->template->content = View::factory('test/logoUpload',array('uid'=>$logo_id));
	}
	
	public function action_docUploadAdd(){
		$doc_id = $this->request->param("id");
		//$doc_id = Model::factory("profiles")->getUserIdByProfileId($doc_id);
		$this->template->content = View::factory('test/docUpload',array('uid'=>$doc_id));
	}
	
	public function action_pictureUploadAdd(){
		$picture_id = $this->request->param("id");
		//$picture_id = Model::factory("profiles")->getUserIdByProfileId($picture_id);
		$this->template->content = View::factory('test/pictureUpload',array('uid'=>$picture_id));
	}
	
	public function action_logoUploadAdd(){
		$logo_id = $this->request->param("id");
		//$logo_id = Model::factory("profiles")->getUserIdByProfileId($logo_id);
		$this->template->content = View::factory('test/logoUpload',array('uid'=>$logo_id));
	}
	
	public function action_cron(){
		
		$tempPictures = Model::factory("picture")->getTempPictures();
		foreach($tempPictures as $picture){
			@unlink(realpath("images/tmp_files/".$picture["picture_name"]));
			Model::factory("picture")->deletePictureById($picture["id"]);
		}
		
		$tempDocuments = Model::factory("documents")->getTempDocuments();
		foreach($tempDocuments as $document){
			@unlink(realpath("images/tmp_files/".$document["doc_name"]));
			Model::factory("documents")->deleteDocumentById($document["id"]);
		}
//		 $videos = Model::factory('picture')->getDuplicateVideos("");
	}
	
	public function action_videos(){
		ini_set('include_path', ini_get('include_path').PATH_SEPARATOR.APPPATH.'vendor/');		
		require_once 'Zend/Loader.php';
		Zend_Loader::loadClass('Zend_Gdata_YouTube');
		Zend_Loader::loadClass('Zend_Gdata_ClientLogin');
			
		$developerKey=$this->YT_DEVELOPER_KEY;
		$clientId=$this->YT_CLIENT_ID;
		$applicationId=$this->YT_APPLICATION_ID;
		
		$authenticationURL= 'https://www.google.com/accounts/ClientLogin';
		$httpClient = Zend_Gdata_ClientLogin::getHttpClient(
			$username = $this->USERNAME,
			$password = $this->PASSWORD,
			$service = 'youtube',
			$client = null,
			$source = 'Youtube Video Upload', // a short string identifying your application
			$loginToken = null,
			$loginCaptcha = null,
			$authenticationURL
		);
		$yt = new Zend_Gdata_YouTube($httpClient, $applicationId, $clientId, $developerKey);
		$yt->setMajorProtocolVersion(2);
			
		$videos = Model::factory("video")->getVideosByClient(12);//$this->session->get('clientuserid'));
		
		
		foreach($videos as $video){
			$videoEntry = $yt->getVideoEntry($video['video_name'], NULL, true);
			$this->printVideoEntry($videoEntry);
			//$yt->delete($videoEntry);			
			//Model::factory('video')->deleteVideoById($video_id);
		}		
	}
	
	public function getVideoDetail($videoEntry,$title,$id) {
		$videoDeatil=array();
//		$videoDeatil["video_title"]= $videoEntry->getVideoTitle();
		$videoDeatil["video_id"]= $videoEntry->getVideoId();
		$videoDeatil["id"]= $id;
		$videoDeatil["title"]= $title;
		
		$videoThumbnails = $videoEntry->getVideoThumbnails();
		foreach($videoThumbnails as $videoThumbnail) {
			$videoDeatil['thumbnail'] = $videoThumbnail['url'];
			break;
		}		
		return $videoDeatil;
	}
	
	public function printVideoEntry($videoEntry) {
		// the videoEntry object contains many helper functions
		// that access the underlying mediaGroup object
		echo 'Video: ' . $videoEntry->getVideoTitle() . "\n<br />";
		echo 'Video ID: ' . $videoEntry->getVideoId() . "\n<br />";
		echo 'Updated: ' . $videoEntry->getUpdated() . "\n<br />";
		echo 'Description: ' . $videoEntry->getVideoDescription() . "\n<br />";
		echo 'Category: ' . $videoEntry->getVideoCategory() . "\n<br />";
		echo 'Tags: ' . implode(", ", $videoEntry->getVideoTags()) . "\n<br />";
		echo 'Watch page: ' . $videoEntry->getVideoWatchPageUrl() . "\n<br />";
		echo 'Flash Player Url: ' . $videoEntry->getFlashPlayerUrl() . "\n<br />";
		echo 'Duration: ' . $videoEntry->getVideoDuration() . "\n<br />";
		echo 'View count: ' . $videoEntry->getVideoViewCount() . "\n<br />";
		echo 'Rating: ' . $videoEntry->getVideoRatingInfo() . "\n<br />";
		echo 'Geo Location: ' . $videoEntry->getVideoGeoLocation() . "\n<br />";
		echo 'Recorded on: ' . $videoEntry->getVideoRecorded() . "\n<br />";
		
		// see the paragraph above this function for more information on the 
		// 'mediaGroup' object. in the following code, we use the mediaGroup
		// object directly to retrieve its 'Mobile RSTP link' child
		foreach ($videoEntry->mediaGroup->content as $content) {
			if ($content->type === "video/3gpp") {
				echo 'Mobile RTSP link: ' . $content->url . "\n<br />";
			}
		}
	
		echo "Thumbnails:\n<br />";
		$videoThumbnails = $videoEntry->getVideoThumbnails();
		
		foreach($videoThumbnails as $videoThumbnail) {
			echo $videoThumbnail['time'] . ' - ' . $videoThumbnail['url'];
			echo ' height=' . $videoThumbnail['height'];
			echo ' width=' . $videoThumbnail['width'] . "\n<br />";
		}
	}
	
} // End Welcome
