<?php
/*
//define('GOOGLE_API_KEY', 'AIzaSyDo3BUdSFoHB2EqVHmyPP5sXakAIUOzN-g');
//define('GOOGLE_ENDPOINT', 'https://www.googleapis.com/urlshortener/v1');
define('FIREBASE_ENDPOINT', 'https://firebasedynamiclinks.googleapis.com/v1');
// define('FIREBASE_API_KEY', ' AIzaSyAckp2z6LjPUsYZ2z0HF7G92Vr5DlFjUWY');
define('FIREBASE_API_KEY', 'AIzaSyC6_MiUP17U2wjVBWO_DnFe4ediBR3xjls');
function shortenUrl($longUrl)
{
$post = array('longDynamicLink' => "https://scanlink.page.link/?link=".$longUrl);
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://firebasedynamiclinks.googleapis.com/v1/shortLinks?key='.FIREBASE_API_KEY);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
$response = curl_exec($ch);
var_dump($response);
$error = curl_error($ch);
if ($error !== '') {
throw new \Exception($error);
}
//return json_decode($response,true);
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
echo CreateAnalytics("https://scanlink.com.au");*/
define('GOOGLE_API_KEY', 'AIzaSyCSDj4M5PjGxzLS7ZV2-9NDFuPmXTB-kVQ');
define('GOOGLE_ENDPOINT', 'https://www.googleapis.com/urlshortener/v1');

function create_short_url($longUrl)
{

    $url = 'https://firebasedynamiclinks.googleapis.com/v1/shortLinks?key=' . GOOGLE_API_KEY;
    $data = array(
        'dynamicLinkInfo' => array(
            'domainUriPrefix' => 'https://beta.scanlink.com.au',
            'link' => $longUrl,
        ),
        'suffix' => array('option' => 'SHORT'),
    );

    $headers = array('Content-Type: application/json');

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

    $data = curl_exec($ch);
    curl_close($ch);

    $short_url = json_decode($data);

    // echo '<pre>';
    // print_r($short_url);
    // echo '</pre>';
    // exit;
    if (isset($short_url->error)) {
        return $short_url->error->message;
    } else {
        return json_decode($short_url, true);
        // return $short_url->shortLink;
    }

/*    // initialize the cURL connection
$ch = curl_init(
sprintf('%s/url?key=%s', GOOGLE_ENDPOINT, GOOGLE_API_KEY)
);

// tell cURL to return the data rather than outputting it
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
// ... more code will go here

// create the data to be encoded into JSON
$requestData = array(
'longUrl' => $longUrl
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
return json_decode($result, true);*/
}

//View Analytics goo.gl URL
function CreateAnalytics($url)
{
    //create the URL
    $googl = 'https://www.googleapis.com/urlshortener/v1/url?shortUrl=' . urlencode($url) . '&projection=FULL';
    //could also use cURL here
    $response = file_get_contents($googl);
    $format = 'json';
    if (strtolower($format) == 'json') {
        $json = json_decode($response, true);
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

function shortenUrl($url, $api_token)
{
	$short_url = array();
    	$endpoint = 'https://api.tinyurl.com/create';
    	//$data = ['url' => $url]; // pass raw URL
	$data = array("url" => $url);	

    	$ch = curl_init($endpoint);


	$headers = array(
    		'Content-Type: application/json',
    		'Authorization: Bearer ' . $api_token
	);

        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,5);
        curl_setopt($ch,CURLOPT_HTTPHEADER,$headers);
        curl_setopt($ch,CURLOPT_POST,true);
        curl_setopt($ch,CURLOPT_POSTFIELDS,json_encode($data));
        curl_setopt($ch,CURLOPT_FORBID_REUSE,true);	

    	$result = curl_exec($ch);

    	if ($result === false) {
        	return null;
	}

   	$response = json_decode($result, true);
    	//$short_url['shortLink'] = $response['data']['tiny_url'];
	$short_url = array("shortLink" => $response['data']['tiny_url']);
    	return $short_url;
}
