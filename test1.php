<?php //phpinfo();
ini_set('display_errors',1);
error_reporting(E_ALL);
//$arr= getimagesize('http://hiren.scanlink.com.au/images/logo.png');

//$arr= getimagesize('/home/hiren/web/scanlink/images/logo.png');
//print_r($arr);

$encoded_url = urlencode('https://scanlink.com.au');
 $QRURL = "https://chart.googleapis.com/chart?cht=qr&chs=250x250&chl=".$encoded_url."&chld=M|0";
 $QRCode = file_get_contents($QRURL);

$QRImage = "test1.png";
      
      $file = fopen($QRImage, "w");
      fwrite($file, $QRCode);
      fclose($file);

?>
