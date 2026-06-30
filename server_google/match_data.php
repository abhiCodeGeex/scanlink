<?php

session_start();

$con = mysqli_connect('galadb1.croyirckzbgv.ap-southeast-2.rds.amazonaws.com', 'scanlink', 'RN4AS4a82h7P92uR', 'scanlink');
//$con = mysqli_onnect('localhost', 'root', '', 'scan_link');

// Check connection
if (mysqli_connect_errno()) {
    echo 'Failed to connect to MySQL: ' . mysqli_connect_error();
    exit();
}

$scaneventid = @$_REQUEST['scanhitid'];

if ($scaneventid == '') {
} else {
    $itemid = $_REQUEST['id_item'];
    $fetchdatafirst = mysqli_query($con, "select *  from ana_item_analytics where id_item='" . $itemid . "' and event_id='" . $scaneventid . "'");
    $nocount = mysqli_num_rows($fetchdatafirst);

    if ($nocount == 0) {
        //echo "diffrent session insert needs hits";
        $scaneventid1 = isset($_REQUEST['scanhitid']) ? $_REQUEST['scanhitid'] : 0;
        $id_item = isset($_REQUEST['id_item']) ? $_REQUEST['id_item'] : 0;
        $country_code = isset($_REQUEST['country_code']) ? $_REQUEST['country_code'] : 0;
        $country_name = isset($_REQUEST['country_name']) ? $_REQUEST['country_name'] : 0;
        $region_name = isset($_REQUEST['region_name']) ? $_REQUEST['region_name'] : 0;
        $city_name = isset($_REQUEST['city_name']) ? $_REQUEST['city_name'] : 0;
        $zipcode = '';
        $latitude = isset($_REQUEST['latitude']) ? $_REQUEST['latitude'] : 0;
        $longitude = isset($_REQUEST['longitude']) ? $_REQUEST['longitude'] : 0;
        $ip_add = isset($_REQUEST['ip_add']) ? $_REQUEST['ip_add'] : 0;
        // Change by Rohan
        $timezone = isset($_REQUEST['timezone']) ? $_REQUEST['timezone'] : 0;
        // $timezone = "Australia/Melbourne";
        $id_browsercat = isset($_REQUEST['id_browsercat']) ? $_REQUEST['id_browsercat'] : 0;
        $browser_version = isset($_REQUEST['browser_version']) ? $_REQUEST['browser_version'] : 0;
        $id_platformcat = isset($_REQUEST['id_platformcat']) ? $_REQUEST['id_platformcat'] : 0;
        $id_devicecat = isset($_REQUEST['id_devicecat']) ? $_REQUEST['id_devicecat'] : 0;
        $screensize = isset($_REQUEST['screensize']) ? $_REQUEST['screensize'] : 0;

        //echo $id_browsercat;
        if ($id_browsercat == 'Internet Explorer') {
            $id_browser = 3;
        } elseif ($id_browsercat == 'Safari') {
            $id_browser = 2;
        } elseif ($id_browsercat == 'Chrome') {
            $id_browser = 4;
        } elseif ($id_browsercat == 'Firefox') {
            $id_browser = 1;
        } elseif ($id_browsercat == 'Opera') {
            $id_browser = 5;
        } elseif ($id_browsercat == 'Netscape') {
            $id_browser = 6;
        }

        if ($id_devicecat == 'Desktop' && $id_platformcat == 'Windows') {
            $id_platform = 1;
            $scan_type = 'ip';
            $id_device = 4;
        } elseif ($id_devicecat == 'Desktop' && $id_platformcat == 'Mac') {
            $id_platform = 1;
            $scan_type = 'ip';
            $id_device = 12;
        } elseif ($id_devicecat == 'Desktop' && $id_platformcat == 'Linux') {
            $id_platform = 1;
            $id_device = 11;
            $scan_type = 'ip';
        }
        if ($id_devicecat == 'Mobile' && $id_platformcat == 'iOS') {
            $id_platform = 2;
            $id_device = 3;
            $scan_type = 'gps';
        } elseif ($id_devicecat == 'Mobile' && $id_platformcat == 'Android') {
            $id_platform = 2;
            $id_device = 9;
            $scan_type = 'gps';
        } elseif ($id_devicecat == 'Mobile' && $id_platformcat == 'Blackberry') {
            $id_platform = 2;
            $id_device = 7;
            $scan_type = 'gps';
        } elseif ($id_devicecat == 'tablet' && $id_platformcat == 'iOS') {
            $id_platform = 3;
            $id_device = 8;
            $scan_type = 'gps';
        } elseif ($id_devicecat == 'tablet' && $id_platformcat == 'Windows') {
            $id_platform = 3;
            $id_device = 4;
            $scan_type = 'gps';
        } elseif ($id_devicecat == 'tablet' && $id_platformcat == 'Android') {
            $id_platform = 3;
            $id_device = 4;
            $scan_type = 'gps';
        }

        if (empty($id_platform)) {
            $id_platform = 2;
        }
        if (empty($id_device)) {
            $id_device = 9;
        }
        if (empty($scan_type)) {
            $scan_type = 'gps';
        }
        // $id_device;
        // $scan_type;
        // $id_platform;

        $created_at = date('Y-m-d H:i:s');
        $updated_at = date('Y-m-d H:i:s');
        $listIdentifier =  \DateTimeZone::listIdentifiers();

        if (!empty($timezone) && in_array($timezone, $listIdentifier)) {
            $date = new DateTime('now', new DateTimeZone($timezone));
            $created_at = $date->format('Y-m-d H:i:s');
            $updated_at = $date->format('Y-m-d H:i:s');
        }

        // $date = new DateTime('now', new DateTimeZone($timezone));
        // $created_at = $date->format('Y-m-d H:i:s');
        // $updated_at = $date->format('Y-m-d H:i:s');

        $my_sql_query = "insert into ana_item_analytics(event_id,id_item,country_code,country_name,region_name,city_name,zipcode,latitude,longitude,ip_add,timezone,id_browser,browser_version,id_platform,id_device,scan_type,screen_size,created_at,updated_at) values ('" . $scaneventid1 . "','" . $id_item . "','" . $country_code . "','" . $country_name . "','" . $region_name . "','" . $city_name . "','" . $zipcode . "','" . $latitude . "','" . $longitude . "','" . $ip_add . "','" . $timezone . "','" . $id_browsercat . "','" . $browser_version . "'," . $id_platform . ',' . $id_device . ",'" . $scan_type . "','" . $screensize . "','" . $created_at . "','" . $updated_at . "')";

        $insert = mysqli_query($con, $my_sql_query);
	//Log::instance()->add(Log::INFO, 'analytics: '.$city_name);
        //echo "insert into ana_item_analytics(event_id,id_item,country_code,country_name,region_name,city_name,zipcode,latitude,longitude,ip_add,timezone,id_browser,browser_version,id_platform,id_device,scan_type,screen_size,created_at,updated_at) values ('".$scaneventid1."',".$id_item.",'".$country_code."','".$country_name."','".$region_name."','".$city_name."','".$zipcode."','".$latitude."','".$longitude."','".$ip_add."','".$timezone."',".$id_browser.",'".$browser_version."',".$id_platform.",".$id_device.",'".$scan_type."','".$screensize."','".$created_at."','".$updated_at."')";
        //echo "insert into ana_item_analytics(event_id,id_item,country_code,country_name,region_name,city_name,zipcode,latitude,longitude,ip_add,timezone,id_browser,browser_version,id_platform,id_device,scan_type,screen_size,created_at,updated_at) values ('".$scaneventid1."',".$id_item.",'".$country_code."','".$country_name."','".$region_name."','".$city_name."','".$zipcode."','".$latitude."','".$longitude."','".$ip_add."','".$timezone."',".$id_browser.",'".$browser_version."',".$id_platform.",".$id_device.",'".$scan_type."','".$screensize."','". $created_at."','".$updated_at."')";

        if ($insert > 0) {
            echo 'data insert successs';
            // echo $my_sql_query;
        } else {
            echo 'failed';
        }
    } else {
        echo 'same session no hits needed';
    }
}
