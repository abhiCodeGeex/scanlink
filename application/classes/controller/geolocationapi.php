<?php

defined('SYSPATH') or die('No direct script access.');

class Controller_Geolocationapi extends Controller_Template_Master
{

    // public function action_geo_location_api1()
    // {
    //     ini_set('allow_url_fopen', 'ON');
    //     $xml_api_response = array();
    //     $url = "http://api.geonames.org/findNearbyPostalCodes?lat=" . $_GET['lat'] . "&lng=" . $_GET['long'] . "&username=" . GEOLOCATIONUSERNAME;

    //     $api_response = simplexml_load_file($url);

    //     if (!empty($api_response->code[0])) {

    //         $xml_api_response['postalcode'] = (string) $api_response->code[0]->postalcode;
    //         $xml_api_response['address'] = (string) $api_response->code[0]->name;
    //         $xml_api_response['countryCode'] = (string) $api_response->code[0]->countryCode;
    //         $xml_api_response['lat'] = (string) $api_response->code[0]->lat;
    //         $xml_api_response['lng'] = (string) $api_response->code[0]->lng;
    //         $xml_api_response['short_code'] = (string) $api_response->code[0]->adminCode1;
    //         $xml_api_response['state'] = (string) !empty($api_response->code[0]->adminName1) ? $api_response->code[0]->adminName1 : '';
    //         $xml_api_response['city'] = (string) !empty($api_response->code[0]->adminName2) ? $api_response->code[0]->adminName2 : '';
    //         $xml_api_response['lat_long'] = json_encode($_GET);
    //         echo json_encode($xml_api_response);
    //         exit;
    //     }
    // }

    public function action_geo_location_api()
    {
        ini_set('allow_url_fopen', 'ON');
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "http://api.geonames.org/findNearbyPostalCodes?lat=" . $_GET['lat'] . "&lng=" . $_GET['long'] . "&username=" . GEOLOCATIONUSERNAME,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER =>
            array(
                'Content-Type:application/json',
                "Accept: application/json"
            )
        ));

        $api_response = curl_exec($curl);

        if (curl_exec($curl) != false) {
		$temp_api_response = '';
            if (!empty($api_response)) {
		$api_response = json_decode($api_response);
		$temp_api_response = $api_response;
		if (!empty($api_response->postalCodes[0])) {
                    $api_response = $api_response->postalCodes[0];
                    if (!empty($api_response)) {
                        $xml_api_response['postalcode'] = !empty($api_response->postalCode) ? $api_response->postalCode : '';
                        $xml_api_response['address'] =  !empty($api_response->name) ? $api_response->name : '';
                        $xml_api_response['countryCode'] = !empty($api_response->countryCode) ? $api_response->countryCode : '';
                        $xml_api_response['lat'] = !empty($api_response->lat) ? $api_response->lat : '';
                        $xml_api_response['lng'] =  !empty($api_response->lng) ? $api_response->lng : '';
                        $xml_api_response['short_code'] =  !empty($api_response->adminCode1) ? $api_response->adminCode1 : '';
                        $xml_api_response['state'] =  !empty($api_response->adminName1) ? $api_response->adminName1 : '';
                        $xml_api_response['city'] =  !empty($api_response->adminName2) ? $api_response->adminName2 : '';
                        $xml_api_response['lat_long'] = $_GET;
                        echo json_encode($xml_api_response);
			//Log::instance()->add(Log::INFO, 'api_good_response_Good: '.print_r($temp_api_response,true));
                        exit;
                    }
                } else {
		    //Log::instance()->add(Log::INFO, 'api_good_response_Bad: '.print_r($temp_api_response,true));
		    Log::instance()->add(Log::INFO, 'api lat: '.$_GET['lat']);
 		    Log::instance()->add(Log::INFO, 'api long: '.$_GET['long']);
		    //Log::instance()->add(Log::INFO, 'api_response: '.print_r($temp_api_response,true));
		    mail("support@scanlink.net.au", "Scanlink Data Not found", 'No postal code avaliable');
                }
            }
        } else {
            mail("support@scanlink.net.au", "Scanlink Curl Geo location error", json_encode(curl_error($curl)));
        }
        curl_close($curl);
    }


    public function action_geo_location_api_old()
    {
        ini_set('allow_url_fopen', 'ON');
        $xml_api_response = array();
        $url = "http://api.geonames.org/findNearbyPostalCodes?lat=" . $_GET['lat'] . "&lng=" . $_GET['long'] . "&username=" . GEOLOCATIONUSERNAME;

        $api_response = simplexml_load_file($url);

        if (!empty($api_response->code[0])) {

            $xml_api_response['postalcode'] = (string) $api_response->code[0]->postalcode;
            $xml_api_response['address'] = (string) $api_response->code[0]->name;
            $xml_api_response['countryCode'] = (string) $api_response->code[0]->countryCode;
            $xml_api_response['lat'] = (string) $api_response->code[0]->lat;
            $xml_api_response['lng'] = (string) $api_response->code[0]->lng;
            $xml_api_response['short_code'] = (string) $api_response->code[0]->adminCode1;
            $xml_api_response['state'] = (string) !empty($api_response->code[0]->adminName1) ? $api_response->code[0]->adminName1 : '';
            $xml_api_response['city'] = (string) !empty($api_response->code[0]->adminName2) ? $api_response->code[0]->adminName2 : '';
            $xml_api_response['lat_long'] = json_encode($_GET);
            echo json_encode($xml_api_response);
            exit;
        }
    }
    public function action_sampleWebhookRequest()
    {
        // $samplePostRequest = array(
        //     "name" => "Scanlink",
        //     "webhook_url" => URL::base() . "/Visitationapi",
        //     "support_email" => "satish@galatech.com.au",
        //     "shared_key" => "scanlink"
        // );

        $samplePostRequest = array(
            "name" => "Scanlink",
            "webhook_url" => URL::base() . "/Visitationapi",
            "support_email" => "support@scanlink.net.au",
            "shared_key" => "66721bd3-3352-4401-8f2c-9e0b9ab05fbb"
            // "shared_key" => "scanlink"
        );

        $ch = curl_init(VISITATION_API);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $samplePostRequest);
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);

        $curlExecuteResponse = curl_exec($ch);
        if (!$curlExecuteResponse) {
            die('Error: "' . curl_error($ch) . '" - Code: ' . curl_errno($ch));
        }
        $data = array('values' => json_encode($curlExecuteResponse));
        mail('support@scanlink.net.au', 'Webhook Response', json_encode($curlExecuteResponse));
        Model::factory("settings")->updateVisitationWebhookData($data);
        curl_close($ch);
    }

    public function action_generateAccessToken()
    {
        $samplePostRequest = array(
            "client_id" => "9b86c26d-ee37-4194-a6ce-ed6857f86411",
            "client_secret" => "b101e8f9-9ba2-4a98-81d2-c42e72307bd2",
            "scope" => "webhook:read webhook:delete visitors:write query:read webhook:write",
            "grant_type" => "client_credentials"
        );

        $ch = curl_init(AUTH_ACCESS_TOKEN_URL);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept:application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($samplePostRequest));

        $curlExecuteResponse = curl_exec($ch);
        if (!$curlExecuteResponse) {
            die('Error: "' . curl_error($ch) . '" - Code: ' . curl_errno($ch));
        }
        echo '<pre>';
        print_r($curlExecuteResponse);
        echo '</pre>';
        exit;
        $data = array('values' => json_encode($curlExecuteResponse));

        mail('support@scanlink.net.au', 'Webhook Response', json_encode($curlExecuteResponse));
        Model::factory("settings")->updateVisitationWebhookData($data);
        curl_close($ch);
    }
}
