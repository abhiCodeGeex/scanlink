<?php $url = "https://apibeta.banggood.com/getAccessToken?apiTest=1&apiTest=app_id=bg5eb93ba54d103&app_secret=2222a888cc4637034c656fbe9e81d754";

//call api
$json = file_get_contents($url);
$json = json_decode($json);
//$lat = $json->results[0]->geometry->location->lat;
//$lng = $json->results[0]->geometry->location->lng;
//echo "Latitude: " . $lat . ", Longitude: " . $lng;
print_r($json);

?>