<?php
$ch = curl_init();

curl_setopt($ch,CURLOPT_URL,"https://www.scanlink.com.au/welcome/sendusernotification");
curl_setopt($ch,CURLOPT_POST,count($fields));
curl_setopt($ch,CURLOPT_POSTFIELDS,$fields_string);
echo $result = curl_exec($ch);
curl_close($ch);


?>
