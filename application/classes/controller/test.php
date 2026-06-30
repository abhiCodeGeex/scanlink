<?php
defined('SYSPATH') or die('No direct script access.');

class Controller_Test extends Controller_Template_Master
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

        // All JS Libraries and Scripts
        //$this->template->head->javascripts = array('js/jquery.min-1-7-1.js', 'js/coin-slider.min.js', 'fileuploader/js/jquery-ui-1.8.14.custom.min.js', 'fileuploader/js/jquery.fileUploader.js', 'js/jquery.uniform.js', 'js/custome.js', 'js/modernizr.custom.60079.js');
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

        $newVideoData = $this->get_yt_upload_video();

        $this->template->content = View::factory('test/index')
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

        // echo '<pre>' . gettype($key);
        // print_r(json_decode($key));
        // echo '</pre>';
        // exit;

        $client = new Google_Client();
        $client->setClientId($clientId);
        $client->setClientSecret(CLIENT_SECRET);
        $client->setScopes('https://www.googleapis.com/auth/youtube');
        $client->setRedirectUri($redirect);

        // Define an object that will be used to make all API requests.
        $youtube = new Google_Service_YouTube($client);

        $client->setAccessType('offline');
        $client->setAccessToken($key);

        if ($client->getAccessToken()) {

            if ($client->isAccessTokenExpired()) {
                $client->refreshToken($client->getRefreshToken());
                file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/the_key.txt', $client->getAccessToken());
            }

            try {

                $videos = Model::factory("video")->getVideosByClient($this->session->get('clientuserid'));

                $videoThumb = array();

                $videoidArr = array();
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
            } catch (Google_Exception $e) {
                // $htmlBody .= sprintf('<p>An client error occurred: <code>%s</code></p>',
                // htmlspecialchars($e->getMessage()));
            }
        }
    }

    public function action_video_extra()
    {

        $msg = "";
        $err = false;

        array_push($this->template->head->javascripts, 'js/custome.js'); //for apply uniform js

        $newVideoData = $this->get_yt_upload_video();

        /* End code for list previously uploaded video */

        $this->template->content = View::factory('test/video_extra')
            ->bind('postUrl', $postUrl)
            ->bind('nextUrl', $nextUrl)
            ->bind('tokenValue', $tokenValue)
            ->bind('videoData', $newVideoData)
            ->bind('msg', $msg)
            ->bind('profile_id', $profile_id)
            ->bind('err', $err);
    }

    public function action_youtubeUpload()
    {

        array_push($this->template->head->javascripts, 'js/custome.js'); //for apply uniform js

        $video_title = $_REQUEST['title'];
        $video_file = $_FILES['file']['name'];
        $newfilename = date('dmYHis') . '_' . str_replace(" ", "", basename($_FILES["file"]["name"]));
        move_uploaded_file($_FILES['file']['tmp_name'], 'images/video/' . $newfilename);
        // move_uploaded_file($_FILES['file']['tmp_name'], 'images/video/' . $video_file);

        // ini_set('include_path', ini_get('include_path') . PATH_SEPARATOR . APPPATH . 'vendor/');
        // require_once 'Zend/Loader.php';

        // require_once 'Youtube/src/Google/autoload.php';
        // require_once 'Youtube/src/Google/Client.php';
        // require_once 'Youtube/src/Google/Service/YouTube.php';

        // $clientId = $this->YT_CLIENT_ID;

        // $redirect = filter_var(
        //     'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'],
        //     FILTER_SANITIZE_URL
        // );

        // $key = file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/the_key.txt');

        // $client = new Google_Client();
        // $client->setClientId($clientId);
        // $client->setClientSecret(CLIENT_SECRET);
        // $client->setScopes('https://www.googleapis.com/auth/youtube');
        // $client->setRedirectUri($redirect);

        // // Define an object that will be used to make all API requests.
        // $youtube = new Google_Service_YouTube($client);

        // $client->setAccessType('offline');
        // //$client->setAccessToken(YOUTUBE_ACCESS_TOKEN);
        // $client->setAccessToken($key);

        if (true) {

            // if ($client->isAccessTokenExpired()) {
            //     $client->refreshToken($client->getRefreshToken());
            //     file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/the_key.txt', $client->getAccessToken());
            // }

            try {
                // REPLACE this value with the path to the file you are uploading.
                $videoPath = 'images/video/' . $newfilename;

                // Create a snippet with title, description, tags and category ID
                // Create an asset resource and set its snippet metadata and type.
                // This example sets the video's title, description, keyword tags, and
                // video category.
                // $snippet = new Google_Service_YouTube_VideoSnippet();
                // $snippet->setTitle($video_title);
                // $snippet->setDescription('Video Description');
                // $snippet->setTags(array("cars", "funny"));

                // Numeric video category. See
                // https://developers.google.com/youtube/v3/docs/videoCategories/list
                // $snippet->setCategoryId("22");

                // Set the video's status to "public". Valid statuses are "public",
                // "private" and "unlisted".
                // $status = new Google_Service_YouTube_VideoStatus();
                // $status->privacyStatus = "public";

                // // Associate the snippet and status objects with a new video resource.
                // $video = new Google_Service_YouTube_Video();
                // $video->setSnippet($snippet);
                // $video->setStatus($status);

                // Specify the size of each chunk of data, in bytes. Set a higher value for
                // reliable connection as fewer chunks lead to faster uploads. Set a lower
                // value for better recovery on less reliable connections.
                // $chunkSizeBytes = 1 * 1024 * 1024;

                // // Setting the defer flag to true tells the client to return a request which can be called
                // // with ->execute(); instead of making the API call immediately.
                // $client->setDefer(true);

                // // Create a request for the API's videos.insert method to create and upload the video.
                // $insertRequest = $youtube->videos->insert("status,snippet", $video);

                // // Create a MediaFileUpload object for resumable uploads.
                // $media = new Google_Http_MediaFileUpload(
                //     $client,
                //     $insertRequest,
                //     'video/*',
                //     null,
                //     true,
                //     $chunkSizeBytes
                // );
                // $media->setFileSize(filesize($videoPath));

                // Read the media file and upload it.
                // $status = false;
                // $handle = fopen($videoPath, "rb");
                // while (!$status && !feof($handle)) {
                //     $chunk = fread($handle, $chunkSizeBytes);
                //     $status = $media->nextChunk($chunk);
                // }
                // fclose($handle);

                // // If you want to make other calls after the file upload, set setDefer back to false
                // $client->setDefer(false);

                // delete video file from local folder
                // unlink($videoPath);

                $msg = "Video upload Success!<br>Close window to return to profile";
                $err = false;
            } catch (\Exception $e) {
                $msg = "Video Upload Failed<br>Please Try again!";
                $err = true;
            }
?>

            <script type="text/javascript" language="javascript">
                parent.document.getElementById("video").value += parent.document.getElementById("videoTitle").value + '=' + '<?php echo $newfilename . ","; ?>';
                parent.document.getElementById("videoTitle").value = "";
            </script>

        <?php

            $newVideoData = $this->get_yt_upload_video(); //$this->get_youtube_video();
            $this->template->content = View::factory('test/index')
                ->bind('videoData', $newVideoData)
                ->bind('msg', $msg)
                ->bind('err', $err);
        }
    }

    public function action_youtubeUpload_old()
    {
        ini_set('include_path', ini_get('include_path') . PATH_SEPARATOR . APPPATH . 'vendor/');
        require_once 'Zend/Loader.php';

        require_once 'Youtube/src/Google/autoload.php';
        require_once 'Youtube/src/Google/Client.php';
        require_once 'Youtube/src/Google/Service/YouTube.php';

        $video_title = $_REQUEST['title'];
        $video_file = $_FILES['file']['name'];
        move_uploaded_file($_FILES['file']['tmp_name'], 'images/video/' . $video_file);

        $key = file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/the_key.txt');
        file_put_contents('key.txt', json_encode($key));
        $key = file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/the_key.txt');


        $application_name = 'Scanlink';
        $client_secret    = 'SvGNL1f-weuS5644V-D2vHfc'; //CLIENT_SECRET;
        $client_id        = '191009218231-urgp65ts45bnvkq1r4hsr6mijucldch4.apps.googleusercontent.com'; //$this->YT_CLIENT_ID;
        $scope            = array('https://www.googleapis.com/auth/youtube.upload', 'https://www.googleapis.com/auth/youtube', 'https://www.googleapis.com/auth/youtubepartner');

        $videoPath        = 'images/video/' . $video_file; //"Test.f4v";
        $videoTitle       = $_REQUEST['title'];
        $videoDescription = 'Video Description';
        $videoCategory    = "22";
        $videoTags        = array("youtube", "tutorial");

        try {
            // Client init
            $client = new Google_Client();
            $client->setApplicationName($application_name);
            $client->setClientId($client_id);
            $client->setAccessType('offline');
            $client->setAccessToken($key);
            $client->setScopes($scope);
            $client->setClientSecret($client_secret);

            if ($client->getAccessToken()) {

                /**
                 * Check to see if access token has expired. If so, get a new one and save it to file for future use.
                 */
                if ($client->isAccessTokenExpired()) {
                    $newToken = json_decode($client->getAccessToken());
                    $client->refreshToken($newToken->refresh_token);
                    file_put_contents('key.txt', $client->getAccessToken());
                }

                $youtube = new Google_Service_YouTube($client);

                // Create a snipet with title, description, tags and category id
                $snippet = new Google_Service_YouTube_VideoSnippet();
                $snippet->setTitle($videoTitle);
                $snippet->setDescription($videoDescription);
                $snippet->setCategoryId($videoCategory);
                $snippet->setTags($videoTags);

                // Create a video status with privacy status. Options are "public", "private" and "unlisted".
                $status = new Google_Service_YouTube_VideoStatus();
                $status->setPrivacyStatus('public');

                // Create a YouTube video with snippet and status
                $video = new Google_Service_YouTube_Video();
                $video->setSnippet($snippet);
                $video->setStatus($status);

                // Size of each chunk of data in bytes. Setting it higher leads faster upload (less chunks,
                // for reliable connections). Setting it lower leads better recovery (fine-grained chunks)
                $chunkSizeBytes = 1 * 1024 * 1024;

                // Setting the defer flag to true tells the client to return a request which can be called
                // with ->execute(); instead of making the API call immediately.
                $client->setDefer(true);
                $client->refreshToken($client->getRefreshToken());

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

                // Read the media file and upload it chunk by chunk.
                $status = false;
                $handle = fopen($videoPath, "rb");
                while (!$status && !feof($handle)) {
                    $chunk = fread($handle, $chunkSizeBytes);
                    $status = $media->nextChunk($chunk);
                }

                fclose($handle);

                /**
                 * Video has successfully been upload
                 */
                if ($status->status['uploadStatus'] == 'uploaded') {
                    // Actions to perform for a successful upload
                    // ......
                }

                // If want to make other calls after the file upload, set setDefer back to false
                $client->setDefer(true);
            } else {
                // @TODO Log error
                echo 'Problems creating the client';
            }
        } catch (Google_Service_Exception $e) {
            print "Google_Service_Exception " . $e->getCode() . " message is " . $e->getMessage();
            print "Stack trace is " . $e->getTraceAsString();
        } catch (Exception $e) {
            print "Exception " . $e->getCode() . " message is " . $e->getMessage();
            print "Stack trace is " . $e->getTraceAsString();
        }
    }
    public function action_youtubeUpload1()
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

        $redirect = filter_var(
            'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'],
            FILTER_SANITIZE_URL
        );

        $key = file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/the_key.txt');

        $client = new Google_Client();
        $client->setClientId($clientId);
        $client->setClientSecret(CLIENT_SECRET);
        $client->setScopes('https://www.googleapis.com/auth/youtube');
        $client->setRedirectUri($redirect);

        // Define an object that will be used to make all API requests.
        $youtube = new Google_Service_YouTube($client);

        $client->setAccessType('offline');
        //$client->setAccessToken(YOUTUBE_ACCESS_TOKEN);
        $client->setAccessToken($key);

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

                // $insertRequest = new Google_Http_Request($insertRequest);
                //Google_Client
                //Google_Http_Request

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
                $client->setDefer(true);

                // delete video file from local folder
                unlink($videoPath);

                $msg = "Video upload Success!<br>Close window to return to profile";
                $err = false;
            } catch (Google_ServiceException $e) {
                $msg = "Video Upload Failed<br>Please Try again!";
                $err = true;
            } catch (Google_Exception $e) {
                echo '<pre>';
                print_r($e->getMessage());
                echo '</pre>';
                exit;
                $msg = "Video Upload Failed<br>Please Try again!";
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

    public function action_video_extra_upload()
    {

        array_push($this->template->head->javascripts, 'js/custome.js'); //for apply uniform js

        $video_title = $_REQUEST['title'];
        $video_file = $_FILES['file']['name'];
        $newfilename = date('dmYHis') . '_' . str_replace(" ", "", basename($_FILES["file"]["name"]));
        move_uploaded_file($_FILES['file']['tmp_name'], 'images/video/' . $newfilename);

        // move_uploaded_file($_FILES['file']['tmp_name'], 'images/video/' . $video_file);/

        // ini_set('include_path', ini_get('include_path') . PATH_SEPARATOR . APPPATH . 'vendor/');
        // require_once 'Zend/Loader.php';

        // require_once 'Youtube/src/Google/autoload.php';
        // require_once 'Youtube/src/Google/Client.php';
        // require_once 'Youtube/src/Google/Service/YouTube.php';

        // $clientId = $this->YT_CLIENT_ID;

        // $redirect = filter_var(
        //     'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'],
        //     FILTER_SANITIZE_URL
        // );

        // $key = file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/the_key.txt');

        // $client = new Google_Client();
        // $client->setClientId($clientId);
        // $client->setClientSecret(CLIENT_SECRET);
        // $client->setScopes('https://www.googleapis.com/auth/youtube');
        // $client->setRedirectUri($redirect);

        // // Define an object that will be used to make all API requests.
        // $youtube = new Google_Service_YouTube($client);

        // $client->setAccessType('offline');
        // $client->setAccessToken($key);

        if (true) {

            // if ($client->isAccessTokenExpired()) {
            //     $client->refreshToken($client->getRefreshToken());
            //     file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/the_key.txt', $client->getAccessToken());
            // }

            try {
                // REPLACE this value with the path to the file you are uploading.
                $videoPath = 'images/video/' . $video_file;

                // Create a snippet with title, description, tags and category ID
                // Create an asset resource and set its snippet metadata and type.
                // This example sets the video's title, description, keyword tags, and
                // video category.
                // $snippet = new Google_Service_YouTube_VideoSnippet();
                // $snippet->setTitle($video_title);
                // $snippet->setDescription('Video Description');
                // $snippet->setTags(array("cars", "funny"));

                // // Numeric video category. See
                // // https://developers.google.com/youtube/v3/docs/videoCategories/list
                // $snippet->setCategoryId("22");

                // // Set the video's status to "public". Valid statuses are "public",
                // // "private" and "unlisted".
                // $status = new Google_Service_YouTube_VideoStatus();
                // $status->privacyStatus = "public";

                // // Associate the snippet and status objects with a new video resource.
                // $video = new Google_Service_YouTube_Video();
                // $video->setSnippet($snippet);
                // $video->setStatus($status);

                // // Specify the size of each chunk of data, in bytes. Set a higher value for
                // // reliable connection as fewer chunks lead to faster uploads. Set a lower
                // // value for better recovery on less reliable connections.
                // $chunkSizeBytes = 1 * 1024 * 1024;

                // // Setting the defer flag to true tells the client to return a request which can be called
                // // with ->execute(); instead of making the API call immediately.
                // $client->setDefer(true);

                // // Create a request for the API's videos.insert method to create and upload the video.
                // $insertRequest = $youtube->videos->insert("status,snippet", $video);

                // // Create a MediaFileUpload object for resumable uploads.
                // $media = new Google_Http_MediaFileUpload(
                //     $client,
                //     $insertRequest,
                //     'video/*',
                //     null,
                //     true,
                //     $chunkSizeBytes
                // );
                // $media->setFileSize(filesize($videoPath));

                // // Read the media file and upload it.
                // $status = false;
                // $handle = fopen($videoPath, "rb");
                // while (!$status && !feof($handle)) {
                //     $chunk = fread($handle, $chunkSizeBytes);
                //     $status = $media->nextChunk($chunk);
                // }
                // fclose($handle);

                // // If you want to make other calls after the file upload, set setDefer back to false
                // $client->setDefer(false);

                // // delete video file from local folder
                // unlink($videoPath);

                $msg = "Video upload Success!<br>Close window to return to profile";
                $err = false;
            } catch (\Exception $e) {
                $msg = "Video Upload Failed<br>Please Try again!";
                $err = true;
            }
        ?>
            <script type="text/javascript" language="javascript">
                parent.document.getElementById("video2").value += parent.document.getElementById("videoTitle2").value + '=' + '<?php echo $newfilename . ","; ?>';
                parent.document.getElementById("videoTitle2").value = "";
            </script>
<?php

            $newVideoData = $this->get_yt_upload_video();
            $this->template->content = View::factory('test/video_extra')
                ->bind('videoData', $newVideoData)
                ->bind('msg', $msg)
                ->bind('err', $err);
        }
    }

    public function get_yt_upload_video()
    {

        try {

            $videos = Model::factory("video")->getVideosByClient($this->session->get('clientuserid'));
            $videoThumb = array();

            foreach ($videos as $videoid) {
                $videoidArr[] = $videoid['video_name'];
            }
            // $videoids = implode(",", $videoidArr);
            $i = 0;
            foreach ($videos as $video) {

                $videoThumb[$i]['video_id'] = $video['video_name'];
                $videoThumb[$i]['id'] = $video['id'];
                $videoThumb[$i]['title'] = $video['title'];
                $videoThumb[$i]['profile_id'] = $video['profile_id'];
                $i++;
            }

            return $videoThumb;
        } catch (\Exception $e) {
            echo '<pre>';
            print_r($e->getMessage());
            echo '</pre>';
            exit;
        }
    }

    public function action_docUpload()
    {
        array_push($this->template->head->javascripts, "js/jscolor/jscolor.js");

        if (isset($_REQUEST['profile_id']) && $_REQUEST['profile_id'] != '' && isset($_REQUEST['type_id']) && $_REQUEST['type_id'] != '') {
            $this->template->content = View::factory('test/docUpload')
                ->bind("profile_id", $_REQUEST['profile_id'])
                ->bind('type_id', $_REQUEST['type_id']);
        } else {
            exit;
        }
    }

    public function action_pictureUpload()
    {
        if (isset($_REQUEST['profile_id']) && $_REQUEST['profile_id'] != '' && isset($_REQUEST['type_id']) && $_REQUEST['type_id'] != '') {
            $this->template->content = View::factory('test/pictureUpload')
                ->bind("profile_id", $_REQUEST['profile_id'])
                ->bind('type_id', $_REQUEST['type_id']);
        } else {
            exit;
        }
    }

    //below is for picture 2 (extra picture tile in exhibit profile)
    public function action_pictureUpload_extra()
    {
        if (isset($_REQUEST['profile_id']) && $_REQUEST['profile_id'] != '' && isset($_REQUEST['type_id']) && $_REQUEST['type_id'] != '') {
            $this->template->content = View::factory('test/pictureUpload_extra')
                ->bind("profile_id", $_REQUEST['profile_id'])
                ->bind('type_id', $_REQUEST['type_id']);
        } else {
            exit;
        }
    }

    public function action_pictureUpload_voc()
    {
        if (isset($_REQUEST['profile_id']) && $_REQUEST['profile_id'] != '' && isset($_REQUEST['type_id']) && $_REQUEST['type_id'] != '') {
            $this->template->content = View::factory('test/pictureUpload_voc')
                ->bind("profile_id", $_REQUEST['profile_id'])
                ->bind('type_id', $_REQUEST['type_id']);
        } else {
            exit;
        }
    }

    public function action_temp_image_upload()
    {
        $profile_id = $_REQUEST["profile_id"];

        $this->template->content = View::factory('test/temp_image_upload')
            ->bind("profile_id", $_REQUEST['profile_id']);
    }

    public function action_logoUpload()
    {
        if (isset($_REQUEST['profile_id']) && $_REQUEST['profile_id'] != '' && isset($_REQUEST['type_id']) && $_REQUEST['type_id'] != '') {
            $this->template->content = View::factory('test/logoUpload')
                ->bind("profile_id", $_REQUEST['profile_id'])
                ->bind('type_id', $_REQUEST['type_id']);
        } else {
            exit;
        }
    }

    public function action_logoUpload_extra()
    { //used in logo 2 in exhibit profile
        if (isset($_REQUEST['profile_id']) && $_REQUEST['profile_id'] != '' && isset($_REQUEST['type_id']) && $_REQUEST['type_id'] != '') {
            $this->template->content = View::factory('test/logoUpload_extra')
                ->bind("profile_id", $_REQUEST['profile_id'])
                ->bind('type_id', $_REQUEST['type_id']);
        } else {
            exit;
        }
    }

    public function action_logoUpload_footer()
    {
        $this->template->content = View::factory('test/logoUpload_footer');
    }

    public function action_cron()
    {

        $tempPictures = Model::factory("picture")->getTempPictures();
        foreach ($tempPictures as $picture) {
            @unlink(realpath("images/tmp_files/" . $picture["picture_name"]));
            Model::factory("picture")->deletePictureById($picture["id"]);
        }

        $tempDocuments = Model::factory("documents")->getTempDocuments();
        foreach ($tempDocuments as $document) {
            @unlink(realpath("images/tmp_files/" . $document["doc_name"]));
            Model::factory("documents")->deleteDocumentById($document["id"]);
        }
        //         $videos = Model::factory('picture')->getDuplicateVideos("");
    }

    public function action_videos()
    {
        ini_set('include_path', ini_get('include_path') . PATH_SEPARATOR . APPPATH . 'vendor/');
        require_once 'Zend/Loader.php';
        Zend_Loader::loadClass('Zend_Gdata_YouTube');
        Zend_Loader::loadClass('Zend_Gdata_ClientLogin');

        $developerKey = $this->YT_DEVELOPER_KEY;
        $clientId = $this->YT_CLIENT_ID;
        $applicationId = $this->YT_APPLICATION_ID;

        $authenticationURL = 'https://www.google.com/accounts/ClientLogin';
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

        $videos = Model::factory("video")->getVideosByClient($this->session->get('clientuserid'));

        foreach ($videos as $video) {
            $videoEntry = $yt->getVideoEntry($video['video_name'], null, true);
            $this->printVideoEntry($videoEntry);
            //$yt->delete($videoEntry);
            //Model::factory('video')->deleteVideoById($video_id);
        }
    }

    public function getVideoDetail($videoEntry, $title, $videoid, $id, $profile_id)
    {
        $videoDeatil = array();
        //        $videoDeatil["video_title"]= $videoEntry->getVideoTitle();
        $videoDeatil["video_id"] = $videoid;
        $videoDeatil["id"] = $id;
        $videoDeatil["title"] = $title;
        $videoDeatil["profile_id"] = $profile_id;

        $videoThumbnails = $this->get_youtube_video();

        foreach ($videoThumbnails as $videoThumbnail) {
            $videoDeatil['thumbnail'] = $videoThumbnail;
            break;
        }

        return $videoDeatil;
    }

    public function printVideoEntry($videoEntry)
    {
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

        foreach ($videoThumbnails as $videoThumbnail) {
            echo $videoThumbnail['time'] . ' - ' . $videoThumbnail['url'];
            echo ' height=' . $videoThumbnail['height'];
            echo ' width=' . $videoThumbnail['width'] . "\n<br />";
        }
    }

    public function action_getData()
    {

        $curpage = $_POST['curpage'];
        $perpage = $_POST['perpage'];
        $entity = $_POST['entity'];
        $filter = $_POST['filter'];
        $filter_on = $_POST['filter_on'];
        $show_profiles_code = isset($_POST['show_profiles_code']) ? $_POST['show_profiles_code'] : ''; //$_POST['show_profiles_code'];
        $params = isset($_POST['params']) ? $_POST['params'] : array();
        if ($filter_on == 'code') {
            $filter_on = 'id';
        } else if ($filter_on == 'exiprydate') {
            $filter_on = 'expired_at';
        } else {
            $filter_on = 'code_profile_name';
        }
        $filterby = $_POST['filter_by'];
        header('Cache-Control: no-cache, must-revalidate');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Content-type: application/json');

        // if (isset($params['voc_user_id']) && $params['voc_user_id'] != "") {
        //     $clientlist = Model::factory('paging')->getFilteredResultVoc($entity, $filter, $filter_on, $curpage, $perpage, $params, $filterby);
        // } else if (isset($params['type_id']) && $params['type_id'] == 'code_balance') {
        //     $clientlist = Model::factory('paging')->getFilteredResultCodeBalance($entity, $filter, $filter_on, $curpage, $perpage, $params, $filterby);
        // } else {
        //     $clientlist = Model::factory('paging')->getFilteredResult($entity, $filter, $filter_on, $curpage, $perpage, $params, $filterby, $show_profiles_code);
        // }
        if (isset($params['voc_user_id']) && $params['voc_user_id'] != "") {
            $clientlist = Model::factory('paging')->getFilteredResultVoc($entity, $filter, $filter_on, $curpage, $perpage, $params, $filterby);
        } else if (isset($params['type_id']) && $params['type_id'] == 'code_balance') {
            $clientlist = Model::factory('paging')->getFilteredResultCodeBalance($entity, $filter, $filter_on, $curpage, $perpage, $params, $filterby);
        } else {
            $show_profiles_code = Model::factory('profiles')->getProfileByClientIdSub($this->session->get('clientuserid'));
            if (!empty($show_profiles_code['show_code_profile_id_to_acc_user'])) {
                $clientlist = Model::factory('paging')->getFilteredResult($entity, $filter, $filter_on, $curpage, $perpage, $params, $filterby, $show_profiles_code['show_code_profile_id_to_acc_user']);
            } else if ($show_profiles_code['is_sub_user'] == 0) {
                $clientlist = Model::factory('paging')->getFilteredResult($entity, $filter, $filter_on, $curpage, $perpage, $params, $filterby, '');
            } else {
                $clientlist = array();
            }
            // code_profile_id
        }

        print(json_encode($clientlist));
        exit;
    }

    public function action_getFormData()
    {
        ini_set('memory_limit', '1024M');
        ini_set('max_execution_time', 0);
        set_time_limit(0);
        $curpage = $_POST['curpage'];
        $perpage = $_POST['perpage'];
        $entity = $_POST['entity'];
        $filter = urldecode($_POST['filter']);
        $filter_on = $_POST['filter_on'];
        $group_by = $_POST['group_by'];
        $new_arr = "";

        $new_filter = explode("&", $filter);

        if ($filter != "" && count($new_filter) > 0) {
            for ($j = 0; $j < count($new_filter); $j++) {
                $part = explode("=", $new_filter[$j]);

                $new_arr[$part[0]] = $part[1];
            }
        }

        if (count($new_arr) > 0) {
            $filter = $new_arr;
        }

        $params = isset($_POST['params']) ? $_POST['params'] : array();

        $filterby = $_POST['filter_by'];
        header('Cache-Control: no-cache, must-revalidate');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Content-type: application/json');

        $result_list = Model::factory('paging')->get_form_result($entity, $filter, $filter_on, $curpage, $perpage, $params, $filterby, $group_by);
        //$result_list = Model::factory('paging')->get_form_result_for_export($entity, $filter, $filter_on, $curpage, $perpage, $params, $filterby, $group_by);

        print(json_encode($result_list));
        exit;
    }

    public function action_getExpiredProfile()
    {
        $curpage = $_POST['curpage'];
        $perpage = $_POST['perpage'];
        $entity = $_POST['entity'];
        $filter = urldecode($_POST['filter']);
        $filter_on = $_POST['filter_on'];

        $params = isset($_POST['params']) ? $_POST['params'] : array();

        $filterby = $_POST['filter_by'];
        header('Cache-Control: no-cache, must-revalidate');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Content-type: application/json');

        $result_list = Model::factory('paging')->get_expired_profile_result($entity, $filter, $filter_on, $curpage, $perpage, $params, $filterby);

        print(json_encode($result_list));
        exit;
    }

    public function action_getYoutubeUrl()
    {

        ini_set('include_path', ini_get('include_path') . PATH_SEPARATOR . APPPATH . 'vendor/');
        require_once 'Zend/Loader.php';
        Zend_Loader::loadClass('Zend_Gdata_YouTube');
        Zend_Loader::loadClass('Zend_Gdata_ClientLogin');

        $developerKey = $this->YT_DEVELOPER_KEY;
        $clientId = $this->YT_CLIENT_ID;
        $applicationId = $this->YT_APPLICATION_ID;

        $authenticationURL = 'https://www.google.com/accounts/ClientLogin';
        $httpClient = Zend_Gdata_ClientLogin::getHttpClient(
            $username = $this->USERNAME,
            $password = $this->PASSWORD,
            $service = 'youtube',
            $client = null,
            $source = 'youtube video upload', // a short string identifying your application
            $loginToken = null,
            $loginCaptcha = null,
            $authenticationURL
        );

        //echo "<pre>";
        ///print_r($_POST);
        //exit;

        $yt = new Zend_Gdata_YouTube($httpClient, $applicationId, $clientId, $developerKey);

        $yt->setMajorProtocolVersion(2);

        $myVideoEntry = new Zend_Gdata_YouTube_VideoEntry();

        $myVideoEntry->setVideoTitle($_POST['videoTitle']);
        $myVideoEntry->setVideoDescription($_POST['videoTitle']);
        // The category must be a valid YouTube category!

        $myVideoEntry->setVideoCategory('Autos');

        // Set keywords. Please note that this must be a comma-separated string
        // and that individual keywords cannot contain whitespace
        $myVideoEntry->SetVideoTags('cars, funny');

        $tokenHandlerUrl = 'http://gdata.youtube.com/action/GetUploadToken';
        $tokenArray = $yt->getFormUploadToken($myVideoEntry, $tokenHandlerUrl);
        $tokenValue = $tokenArray['token'];
        $postUrl = $tokenArray['url'];
        $nextUrl = URL::base() . 'test/';

        echo $postUrl . '?nexturl=' . $nextUrl;
        exit;
    }

    public function action_temp_doc_upload()
    {

        if (isset($_REQUEST['profile_id']) && $_REQUEST['profile_id'] != '') {
            $this->template->content = View::factory('test/temp_doc_upload')
                ->bind("profile_id", $_REQUEST['profile_id']);
        } else {
            exit;
        }
    }

    public function action_temp_doc_multi_upload()
    {

        if (isset($_REQUEST['profile_id']) && $_REQUEST['profile_id'] != '') {
            $this->template->content = View::factory('test/temp_doc_multi_upload')
                ->bind("profile_id", $_REQUEST['profile_id']);
        } else {
            exit;
        }
    }
}

// End Welcome
