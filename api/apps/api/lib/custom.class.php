<?php

/**
 * Custom Class.
 *
 * @package    property
 * @subpackage form
 * @author     Ajay Makwana <ajay@webplanex.net>
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class custom {

  public static function getRandomString($len = 30) {
    $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
    $r_str = "";
    for ($i = 0; $i < $len; $i++)
      $r_str .= substr($chars, rand(0, strlen($chars)), 1);
    return $r_str;
  }

  /**
   * * get browser
   * */
  public static function getBrowser() {
    $u_agent = $_SERVER['HTTP_USER_AGENT'];
    $bname = 'Unknown';
    $platform = 'Unknown';
    $version = "";

    //First get the platform?
    if (preg_match('/linux/i', $u_agent)) {
      $platform = 'linux';
    } elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
      $platform = 'mac';
    } elseif (preg_match('/windows|win32/i', $u_agent)) {
      $platform = 'windows';
    }

    // Next get the name of the useragent yes seperately and for good reason
    if (preg_match('/MSIE/i', $u_agent) && !preg_match('/Opera/i', $u_agent)) {
      $bname = 'Internet Explorer';
      $ub = "MSIE";
    } elseif (preg_match('/Firefox/i', $u_agent)) {
      $bname = 'Mozilla Firefox';
      $ub = "Firefox";
    } elseif (preg_match('/Chrome/i', $u_agent)) {
      $bname = 'Google Chrome';
      $ub = "Chrome";
    } elseif (preg_match('/Safari/i', $u_agent)) {
      $bname = 'Apple Safari';
      $ub = "Safari";
    } elseif (preg_match('/Opera/i', $u_agent)) {
      $bname = 'Opera';
      $ub = "Opera";
    } elseif (preg_match('/Netscape/i', $u_agent)) {
      $bname = 'Netscape';
      $ub = "Netscape";
    }

    // finally get the correct version number
    $known = array('Version', $ub, 'other');
    $pattern = '#(?<browser>' . join('|', $known) .
            ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
    if (!preg_match_all($pattern, $u_agent, $matches)) {
      // we have no matching number just continue
    }

    // see how many we have
    $i = count($matches['browser']);
    if ($i != 1) {
      //we will have two since we are not using 'other' argument yet
      //see if version is before or after the name
      if (strripos($u_agent, "Version") < strripos($u_agent, $ub)) {
        $version = $matches['version'][0];
      } else {
        $version = $matches['version'][1];
      }
    } else {
      $version = $matches['version'][0];
    }

    // check if we have a number
    if ($version == null || $version == "") {
      $version = "?";
    }


    // check for device
    $device_type = "";
    if (preg_match('/Android/i', $u_agent)) {
      $device_type = 'android';
    } elseif (preg_match('/iPod/i', $u_agent)) {
      $device_type = 'ipod';
    } elseif (preg_match('/iPad/i', $u_agent)) {
      $device_type = 'ipad';
    } elseif (preg_match('/iPhone/i', $u_agent)) {
      $device_type = 'iphone';
    }elseif (preg_match('/BlackBerry/i', $u_agent)) {
      $device_type = 'blackberry';
    }elseif (preg_match('/mini/i', $u_agent)) {
      $device_type = 'mini';
    }elseif (preg_match('/HTC/i', $u_agent)) {
      $device_type = 'htc';
    }else{
			$device_type = 'computer';
		}
		

    return array(
        'userAgent' => $u_agent,
        'name' => $bname,
        'version' => $version,
        'platform' => $platform,
        'pattern' => $pattern,
        'device' => $device_type
    );
  }

}

?>
