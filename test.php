<?php $wpArr = file_get_contents('https://www.scanlink.com.au/api/web/api.php/item/addurl?url=http://scanlink.com.au/company/443');

    $response = json_decode($wpArr);

   print_r($response->item_key);

$strURL = 'https://www.scanlink.com.au/api/web/api.php/item/addurl?url=http://scanlink.com.au/company/443';
$ch = curl_init();
 curl_setopt($ch, CURLOPT_URL, $strURL);

 curl_setopt($ch, CURLOPT_HEADER, 0);
 curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
 curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
 curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);


$strContent= curl_exec($ch);
$info = curl_getinfo($ch);
curl_close($ch);

echo $strContent;
echo "<br /><br />DEBUG\n<pre>";
var_dump($info);
echo "</pre>";


?>
<!DOCTYPE html>
<html>
<head>
<title>jQuery Ajax Example with JSON Response</title>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js" type="text/javascript"></script>
<script>
<!-- Write Javascript code here -->
$(document).ready(function(){
  $(':submit').on('click', function() { // This event fires when a button is clicked
      var button = $(this).val();
      $.ajax({ // ajax call starts
          url: 'https://www.scanlink.com.au/api/web/api.php/item/addurl?url=http://scanlink.com.au/company/443', // JQuery loads serverside.php
          dataType: 'json', // Choosing a JSON datatype
          success: function(data) // Variable data contains the data we get from serverside
          {
          	alert(data.item_key);
		  }
      });
      return false; // keeps the page from not refreshing 
  });
 });
 </script>
</head>

<body>
<form method="post" action="">
      <button value="all" type="submit">Get All </button>
      
  </form>
  
 </body>
</html>