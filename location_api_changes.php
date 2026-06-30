
<?php

function getRealIpAddr()
{
   if (!empty($_SERVER['HTTP_CLIENT_IP'])) { //check ip from share internet
      $ip = $_SERVER['HTTP_CLIENT_IP'];
   } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) { //to check ip is pass from proxy
      $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
   } else {
      $ip = $_SERVER['REMOTE_ADDR'];
   }
   return $ip;
}

for ($i = 0; $i < 10000; $i++) {
   $xml = file_get_contents('http://www.geoplugin.net/json.gp?ip=' . getRealIpAddr());
   echo $i . '<br>';
}
