<?php

error_reporting(E_ALL);
ini_set("display_errors", 1);
// Call set_include_path() as needed to point to your client library.

require_once 'config.php';

$key = file_get_contents('the_key.txt');

/*
 * You can acquire an OAuth 2.0 client ID and client secret from the
 * Google Developers Console <https://console.developers.google.com/>
 * For more information about using OAuth 2.0 to access Google APIs, please see:
 * <https://developers.google.com/youtube/v3/guides/authentication>
 * Please ensure that you have enabled the YouTube Data API for your project.
 */

// Define an object that will be used to make all API requests.
$youtube = new Google_Service_YouTube($client);

if (isset($_GET['code'])) {
  if (strval($_SESSION['state']) !== strval($_GET['state'])) {
    die('The session state did not match.');
  }

  $client->authenticate($_GET['code']);
  $_SESSION['token'] = $client->getAccessToken();
  
  
  header('Location: ' . $redirect);
}

$client->setAccessType('offline');
$client->setAccessToken($key);
/*if (isset($_SESSION['token'])) {
  $client->setAccessToken($_SESSION['token']);
}*/

// Check to ensure that the access token was successfully acquired.
if ($client->getAccessToken()) {
    
    if($client->isAccessTokenExpired()) {
        //$newToken = json_decode($client->getAccessToken());
        //$client->refreshToken($newToken->refresh_token);
        $client->refreshToken( $client->getRefreshToken() );
        file_put_contents('the_key.txt', $client->getAccessToken());
    }
    
  try {
    // Call the channels.list method to retrieve information about the
    // currently authenticated user's channel.
    $channelsResponse = $youtube->channels->listChannels('contentDetails', array(
      'mine' => 'true',
    ));

    $htmlBody = '';
    foreach ($channelsResponse['items'] as $channel) {
      // Extract the unique playlist ID that identifies the list of videos
      // uploaded to the channel, and then call the playlistItems.list method
      // to retrieve that list.
      $uploadsListId = $channel['contentDetails']['relatedPlaylists']['uploads'];

      $playlistItemsResponse = $youtube->playlistItems->listPlaylistItems('snippet', array(
        'playlistId' => $uploadsListId
        //'videoId' =>'iCvCPo5xTtg'
      ));

      $htmlBody .= "<h3>Videos in list $uploadsListId</h3><ul>";
      foreach ($playlistItemsResponse['items'] as $playlistItem) {
        $htmlBody .= sprintf('<li>%s (%s)</li>', $playlistItem['snippet']['title'],
          $playlistItem['snippet']['resourceId']['videoId']);
        $htmlBody .= "<img src='".$playlistItem['snippet']['modelData']['thumbnails']['default']['url']."'>";
        
      }
      
      $htmlBody .= '</ul>';
      
      // For thumbnail there are four size : default, medium, high, standard
      
      //echo "<pre>";
      //print_r($playlistItemsResponse['items']);
    }
  } catch (Google_Service_Exception $e) {
    $htmlBody .= sprintf('<p>A service error occurred: <code>%s</code></p>',
      htmlspecialchars($e->getMessage()));
  } catch (Google_Exception $e) {
    $htmlBody .= sprintf('<p>An client error occurred: <code>%s</code></p>',
      htmlspecialchars($e->getMessage()));
  }

  $_SESSION['token'] = $client->getAccessToken();
} else {
  $state = mt_rand();
  $client->setState($state);
  $_SESSION['state'] = $state;

  $authUrl = $client->createAuthUrl();
  $htmlBody = <<<END
  <h3>Authorization Required</h3>
  <p>You need to <a href="$authUrl">authorize access</a> before proceeding.<p>
END;
}
?>

<!doctype html>
<html>
  <head>
    <title>My Uploaded Videos</title>
  </head>
  <body>
    <?php echo $htmlBody?>
  </body>
</html>