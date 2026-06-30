<?php
//define('GOOGLE_API_KEY', 'AIzaSyDo3BUdSFoHB2EqVHmyPP5sXakAIUOzN-g');
//define('GOOGLE_ENDPOINT', 'https://www.googleapis.com/urlshortener/v1');
define('FIREBASE_ENDPOINT', 'https://firebasedynamiclinks.googleapis.com/v1');
define('FIREBASE_API_KEY', 'AIzaSyCYbyf40xJwEFrVHcBaQS3FzvuiU-0MuJQ');
function shortenUrl($longUrl)
{
	// initialize the cURL connection
	$ch = curl_init(
		//sprintf('%s/url?key=%s', GOOGLE_ENDPOINT, GOOGLE_API_KEY)
		sprintf('%s/shortLinks?key=%s', FIREBASE_ENDPOINT, FIREBASE_API_KEY)
	);

	// tell cURL to return the data rather than outputting it
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	// ... more code will go here

	// create the data to be encoded into JSON
	// $requestData = array(
	// 	'longUrl' => $longUrl
	// );

	$requestData = array(
		'longDynamicLink' => "https://scanlink.page.link/?link=".$longUrl
	);

	// change the request type to POST
	curl_setopt($ch, CURLOPT_POST, true);

	// set the form content type for JSON data
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));

	// set the post body to encoded JSON data
	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestData));

	// perform the request
	$result = curl_exec($ch);
	curl_close($ch);

	// decode and return the JSON response
	return json_decode($result, true);
}

//View Analytics goo.gl URL
function CreateAnalytics($url)
{
	//create the URL
	$googl = 'https://www.googleapis.com/urlshortener/v1/url?shortUrl='.urlencode($url).'&projection=FULL';
	//could also use cURL here
	$response = file_get_contents($googl);
	$format='json';
	if(strtolower($format) == 'json')
	{
		$json = json_decode($response,true);		
		return $json;
	}
}
//End
/*$response = shortenUrl('http://www.webplanex.com/');
echo '<XMP>';
print_r($response);
echo '</XMP>';

$Analytics = CreateAnalytics($response['id']);
echo '<XMP>';
print_r($Analytics);
echo '</XMP>';*/
?>