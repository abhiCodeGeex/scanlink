<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

defined('SYSPATH') or die('No direct script access.');

class Controller_Youtube extends Controller_Template_Master
{

   public $YT_DEVELOPER_KEY, $YT_CLIENT_ID, $YT_APPLICATION_ID, $USERNAME, $PASSWORD;

   public function before()
   {

      parent::before();

      // Default Head view file
      $this->template->head = View::factory('template/head');

      // Set the default Page Title
      $this->template->head->title = 'Welcome to ScanLink';

      // All CSS Stylesheets
      $this->template->head->stylesheets = array(array('href' => 'styles/style.css', 'media' => 'screen'), array('href' => 'styles/coin-slider-styles.css', 'media' => 'screen'), array('href' => 'fileuploader/css/ui-lightness/jquery-ui-1.8.14.custom.css', 'media' => 'screen'), array('href' => 'fileuploader/css/fileUploader.css', 'media' => 'screen'));

      $this->template->head->javascripts = array('js/jquery.min-1-7-1.js', 'js/coin-slider.min.js', 'fileuploader/js/jquery-ui-1.8.14.custom.min.js', 'fileuploader/js/jquery.fileUploader.js', 'js/jquery.uniform.js', 'js/modernizr.custom.60079.js');

      $this->template->head->extrascripts = 'slider';
      $this->template->currentPage = 'test';
      $settings = Model::factory("settings")->getallsettings();
      if (isset($settings) && !empty($settings)) {
         foreach ($settings as $key => $val) {
            if ($val['title'] == 'youtube_username') {
               $this->USERNAME = $val['values'];
            }
            if ($val['title'] == 'youtube_password') {
               $this->PASSWORD = $val['values'];
            }
            if ($val['title'] == 'youtube_developer_key') {
               $this->YT_DEVELOPER_KEY = $val['values'];
            }
            if ($val['title'] == 'youtube_client_id') {
               $this->YT_CLIENT_ID = $val['values'];
            }
            if ($val['title'] == 'youtube_application_id') {
               $this->YT_APPLICATION_ID = $val['values'];
            }
         }
      }
   }

   public function action_index()
   {
      $msg = "";
      $err = false;

      array_push($this->template->head->javascripts, 'js/custome.js'); //for apply uniform js

      $newVideoData = $this->get_youtube_video();

      $this->template->content = View::factory('test1/index')
         ->bind('postUrl', $postUrl)
         ->bind('nextUrl', $nextUrl)
         ->bind('tokenValue', $tokenValue)
         ->bind('videoData', $newVideoData)
         ->bind('msg', $msg)
         ->bind('profile_id', $profile_id)
         ->bind('err', $err);
   }

   public function get_youtube_video()
   {
      ini_set('include_path', ini_get('include_path') . PATH_SEPARATOR . APPPATH . 'vendor/');
      require_once 'Zend/Loader.php';

      require_once 'Youtube/src/Google/autoload.php';
      require_once 'Youtube/src/Google/Client.php';
      require_once 'Youtube/src/Google/Service/YouTube.php';

      $clientId = $this->YT_CLIENT_ID;

      $redirect = filter_var(
         'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'],
         FILTER_SANITIZE_URL
      );

      $key = file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/the_key.txt');

      $client = new Google_Client();
      // $client->setClientId($clientId);
      // $client->setClientSecret(CLIENT_SECRET);
      $client->setClientId('191009218231-urgp65ts45bnvkq1r4hsr6mijucldch4.apps.googleusercontent.com');
      $client->setClientSecret('SvGNL1f-weuS5644V-D2vHfc');
      $client->setScopes('https://www.googleapis.com/auth/youtube');
      $client->setRedirectUri($redirect);

      // Define an object that will be used to make all API requests.
      $youtube = new Google_Service_YouTube($client);

      $client->setAccessType('offline');
      $token =  array(
         'access_token' => 'ya29.a0AfH6SMAbtvsbRreCJ_LHzQhdmSZJN3ZjsPa62Dt3gQ1L4RY-sZTPWf2n_QUc_g0Hpd-W362inDwBrKZwurb0RTDSc6z52w_dLlkF2iAPaf-kKbhQAhUwlhDa3zrDseE8Isui8Tf0sN9Rd19tmbUPwsTOxbfJKfsZjmmyg9CNiCE',
         'refresh_token' => '1//04EKA2ntbVHRoCgYIARAAGAQSNwF-L9IriVuzG9_dwZooxoBOegyzn5nvE3Nn9o3wIeTbPNOL7X9t3weIvc6oc3Ml9P756GWX3zs'
      );
      $json_token = json_encode($token);

      $client->setAccessToken($json_token);
      // $client->setAccessToken($key);

      if ($client->getAccessToken()) {

         if ($client->isAccessTokenExpired()) {
            $client->refreshToken($client->getRefreshToken());
            file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/the_key.txt', $client->getAccessToken());
         }

         try {

            $videos = Model::factory("video")->getVideosByClient($this->session->get('clientuserid'));

            $videoThumb = array();

            foreach ($videos as $videoid) {
               $videoidArr[] = $videoid['video_name'];
            }
            $videoids = implode(",", $videoidArr);
            $i = 0;
            foreach ($videos as $video) {
               $listResponse = $youtube->videos->listVideos(
                  "snippet",
                  array('id' => $videoids)
               );

               $videoThumb[$i]['video_id'] = $video['video_name'];
               $videoThumb[$i]['id'] = $video['id'];
               $videoThumb[$i]['title'] = $video['title'];
               $videoThumb[$i]['profile_id'] = $video['profile_id'];

               foreach ($listResponse['items'] as $playlistItem) {
                  if ($video['video_name'] == $playlistItem['id']) {
                     $videoThumb[$i]['thumbnail'] = $playlistItem['snippet']['modelData']['thumbnails']['default']['url'];
                  }
               }
               $i++;
            }

            return $videoThumb;
         } catch (Google_Service_Exception $e) {
            //$htmlBody .= sprintf('<p>A service error occurred: <code>%s</code></p>',
            // htmlspecialchars($e->getMessage()));
            echo '<pre>';
            print_r($e->getMessage());
         } catch (Google_Exception $e) {
            echo '<pre>';
            print_r($e->getMessage());
            // $htmlBody .= sprintf('<p>An client error occurred: <code>%s</code></p>',
            // htmlspecialchars($e->getMessage()));
         }
      }
   }

   public function action_youtubeUpload()
   {
      array_push($this->template->head->javascripts, 'js/custome.js'); //for apply uniform js
      $video_title = $_REQUEST['title'];
      $video_file = $_FILES['file']['name'];
      move_uploaded_file($_FILES['file']['tmp_name'], 'images/video/' . $video_file);

      ini_set('include_path', ini_get('include_path') . PATH_SEPARATOR . APPPATH . 'vendor/');
      require_once 'Zend/Loader.php';

      require_once 'Youtube/src/Google/autoload.php';
      require_once 'Youtube/src/Google/Client.php';
      require_once 'Youtube/src/Google/Service/YouTube.php';

      $clientId = $this->YT_CLIENT_ID;

      $redirect = filter_var('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'], FILTER_SANITIZE_URL);

      $key = file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/the_key.txt');

      $client = new Google_Client();
      // $client->setClientId($clientId);
      // $client->setClientSecret(CLIENT_SECRET);
      $client->setClientId('191009218231-urgp65ts45bnvkq1r4hsr6mijucldch4.apps.googleusercontent.com');
      $client->setClientSecret('SvGNL1f-weuS5644V-D2vHfc');
      $client->setScopes('https://www.googleapis.com/auth/youtube');
      $client->setRedirectUri($redirect);

      // Define an object that will be used to make all API requests.
      $youtube = new Google_Service_YouTube($client);

      $client->setAccessType('offline');
      //$client->setAccessToken(YOUTUBE_ACCESS_TOKEN);
     $token =  array(
         'access_token' => 'ya29.a0AfH6SMAbtvsbRreCJ_LHzQhdmSZJN3ZjsPa62Dt3gQ1L4RY-sZTPWf2n_QUc_g0Hpd-W362inDwBrKZwurb0RTDSc6z52w_dLlkF2iAPaf-kKbhQAhUwlhDa3zrDseE8Isui8Tf0sN9Rd19tmbUPwsTOxbfJKfsZjmmyg9CNiCE',
         'refresh_token' => '1//04EKA2ntbVHRoCgYIARAAGAQSNwF-L9IriVuzG9_dwZooxoBOegyzn5nvE3Nn9o3wIeTbPNOL7X9t3weIvc6oc3Ml9P756GWX3zs'
      );
      $json_token = json_encode($token);

      $client->setAccessToken($json_token);

      if ($client->getAccessToken()) {

         if ($client->isAccessTokenExpired()) {
            $client->refreshToken($client->getRefreshToken());
            file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/the_key.txt', $client->getAccessToken());
         }

         try {
            // REPLACE this value with the path to the file you are uploading.
            $videoPath = 'images/video/' . $video_file;

            // Create a snippet with title, description, tags and category ID
            // Create an asset resource and set its snippet metadata and type.
            // This example sets the video's title, description, keyword tags, and
            // video category.
            $snippet = new Google_Service_YouTube_VideoSnippet();
            $snippet->setTitle($video_title);
            $snippet->setDescription('Video Description');
            $snippet->setTags(array("cars", "funny"));

            // Numeric video category. See
            // https://developers.google.com/youtube/v3/docs/videoCategories/list
            $snippet->setCategoryId("22");

            // Set the video's status to "public". Valid statuses are "public",
            // "private" and "unlisted".
            $status = new Google_Service_YouTube_VideoStatus();
            $status->privacyStatus = "public";

            // Associate the snippet and status objects with a new video resource.
            $video = new Google_Service_YouTube_Video();
            $video->setSnippet($snippet);
            $video->setStatus($status);

            // Specify the size of each chunk of data, in bytes. Set a higher value for
            // reliable connection as fewer chunks lead to faster uploads. Set a lower
            // value for better recovery on less reliable connections.
            $chunkSizeBytes = 1 * 1024 * 1024;

            // Setting the defer flag to true tells the client to return a request which can be called
            // with ->execute(); instead of making the API call immediately.
            $client->setDefer(true);

            // Create a request for the API's videos.insert method to create and upload the video.
            $insertRequest = $youtube->videos->insert("status,snippet", $video);

            // Create a MediaFileUpload object for resumable uploads.
            $media = new Google_Http_MediaFileUpload(
               $client,
               $insertRequest,
               'video/*',
               null,
               true,
               $chunkSizeBytes
            );
            $media->setFileSize(filesize($videoPath));

            // Read the media file and upload it.
            $status = false;
            $handle = fopen($videoPath, "rb");
            while (!$status && !feof($handle)) {
               $chunk = fread($handle, $chunkSizeBytes);
               $status = $media->nextChunk($chunk);
            }
            fclose($handle);

            // If you want to make other calls after the file upload, set setDefer back to false
            $client->setDefer(false);

            // delete video file from local folder
            unlink($videoPath);

            $msg = "Video upload Success!<br>Close window to return to profile";
            $err = false;
         } catch (Google_ServiceException $e) {
            $msg = "Video Upload Failed<br>Please Try again!";
            $err = true;
            print_r($e);
         } catch (Google_Exception $e) {
            $msg = "Video Upload Failed<br>Please Try again!";
            print_r($e);
            $err = true;
         }
?>
         <script type="text/javascript" language="javascript">
            parent.document.getElementById("video").value += parent.document.getElementById("videoTitle").value + '=' + '<?php echo $status['id'] . ","; ?>';
            parent.document.getElementById("videoTitle").value = "";
         </script>
      <?php

         $newVideoData = $this->get_youtube_video();
         $this->template->content = View::factory('test/index')
            ->bind('videoData', $newVideoData)
            ->bind('msg', $msg)
            ->bind('err', $err);
      }
   }

}

// End Welcome
