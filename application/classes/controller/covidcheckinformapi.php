<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

class Controller_Covidcheckinformapi extends Controller_Template_Mobile
{

   public $defaultAuthScope = "webhook:read webhook:delete visitors:write query:read webhook:write";
   public $appProviderName = 'Scanlink';
   public $appProviderContact = '+61400014000';
   public $webhookId;
   public $visitationApiMode = 'testing';

   public function before()
   {
      $response = $this->action_getWebhookId();
      $this->visitationApiMode =  VISITAION_API_MODE;
      // $this->webhookId = GATEWAY_REQUEST_ID;
      // if ($this->visitationApiMode != 'testing') {
      $this->webhookId = $response->id;
      // }
   }

   public function action_getWebhookId()
   {
      $webhookResponse = Model::factory('settings')->getValueByTitle('visitation_webhook_response');
      return  json_decode($webhookResponse);
   }

   public function action_generateContractTracingRequest()
   {
      $authscope = 'query:read';
      $response = $this->action_generateAuthAccessToken($authscope);
      $curl = curl_init();
      curl_setopt_array($curl, array(
         CURLOPT_URL => CONTRACT_TRACING_REQUEST_URL . '?gateway_request_id=' . $this->webhookId,
         CURLOPT_RETURNTRANSFER => true,
         CURLOPT_ENCODING => '',
         CURLOPT_MAXREDIRS => 10,
         CURLOPT_TIMEOUT => 0,
         CURLOPT_FOLLOWLOCATION => true,
         CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
         CURLOPT_CUSTOMREQUEST => 'GET',
         CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'Accept: application/json',
            'Authorization:' . $response->token_type . ' ' . $response->access_token
         ),
      ));

      $response = curl_exec($curl);
      $searchableResponse = json_decode($response);

      $this->action_searchRequestResult($searchableResponse);
      curl_close($curl);
   }

   public function action_generateAuthAccessToken($authScope)
   {
      $postRequest = array(
         "client_id" => OAUTH_CLIENT_ID,
         "client_secret" => OAUTH_CLIENT_SECRET,
         "scope" => $authScope, //"webhook:write",
         "grant_type" => "client_credentials"
      );

      $curl = curl_init();

      curl_setopt_array($curl, array(
         CURLOPT_URL => AUTH_ACCESS_TOKEN_URL,
         CURLOPT_RETURNTRANSFER => true,
         CURLOPT_ENCODING => '',
         CURLOPT_MAXREDIRS => 10,
         CURLOPT_TIMEOUT => 0,
         CURLOPT_FOLLOWLOCATION => true,
         CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
         CURLOPT_CUSTOMREQUEST => 'POST',
         CURLOPT_POSTFIELDS => http_build_query($postRequest),
         CURLOPT_HTTPHEADER => array(
            'Content-Type: application/x-www-form-urlencoded',
         ),
      ));

      $response = curl_exec($curl);

      curl_close($curl);
      $response =  json_decode($response);

      return $response;
   }

   public function action_searchRequestResult($response)
   {
      $where = array();
      $searchType = trim($response->search_type);

      if ($searchType) {
         switch ($searchType) {
            case 'phone_restricted_response':
               $where = array(
                  'phone_number' => $response->phone_number,
                  'search_type' => "phone_restricted_response"
               );
               break;
            case 'address':
               $where = array(
                  'search_type' => "address",
                  'address' => array(
                     "address" => $response->address,
                     'short_address' => !empty($response->short_address) ? $response->short_address : '',
                     "building_number" => !empty($response->building_number) ? $response->building_number : '',
                     "unit_name" => !empty($response->unit_name) ? $response->unit_name : '',
                     "unit_type" => !empty($response->unit_type) ? $response->unit_type : '',
                     "street_name" => !empty($response->street_name) ? $response->street_name : '',
                     "street_type" => !empty($response->street_type) ? $response->street_type : '',
                     "city_suburb" => !empty($response->city_suburb) ? $response->city_suburb : '',
                     "postcode" => !empty($response->postcode) ? $response->postcode : '',
                     "state" => !empty($response->state) ? $response->state : '',
                     "latitude" => !empty($response->latitude) ? $response->latitude : '',
                     "longitude" => !empty($response->longitude) ? $response->longitude : ''
                  ),
               );
               break;
            case 'phone_period_range':
               $where = array(
                  'phone_number' => $response->phone_number,
                  'search_type' => "phone_period_range"
               );
               break;
            case 'vehicle_lookup':
               $where = array(
                  "search_type" => "vehicle_lookup",
                  "location" => array(
                     "name" => !empty($response->name) ? $response->name : '',
                     "address_line_1" => !empty($response->address_line_1) ? $response->address_line_1 : '',
                     "city_suburb" => !empty($response->city_suburb) ? $response->city_suburb : '',
                     "postcode" => !empty($response->postcode) ? $response->postcode : ''
                  )
               );
               break;
            case 'location_lookup':
               $where = array(
                  "search_type" => "location_lookup",
                  "location" => array(
                     "name" => !empty($response->name) ? $response->name : '',
                     "address_line_1" => !empty($response->address_line_1) ? $response->address_line_1 : '',
                     "city_suburb" => !empty($response->city_suburb) ? $response->city_suburb : '',
                     "postcode" => !empty($response->postcode) ? $response->postcode : ''
                  )
               );
               break;
            case 'location':
               $where = array(
                  "location_ids" => array($response->location_ids),
                  "application_id" => $response->application_id,
                  "search_type" => "location"
               );
               break;
            case 'vehicle':
               $where = array(
                  "location_ids" => array($response->location_ids),
                  "application_id" => $response->application_id,
                  "search_type" => "vehicle"
               );
               break;
            default:
            case 'phone_check_in_range':
               $where = array(
                  "phone_number" => $response->phone_number,
                  "checkin_minutes_prior" => $response->checkin_minutes_prior,
                  "checkin_minutes_after" => $response->checkin_minutes_after,
                  "search_type" => "phone_check_in_range"
               );
               break;
            case '':
               echo 'no request type provided';
               break;
         }

         //"2020-11-01T15:01:16+00:00" Date time format
         $where = $where + array(
            "gateway_request_id" => $response->gateway_request_id, //$this->webhookId,
            "period_start_datetime" => $response->period_start_datetime,
            "period_end_datetime" => $response->period_end_datetime
         );

         $queryResponse = Model::factory('covidapisearch')->getCovidApiSearchData($where);
         if ($searchType != 'location' &&  $searchType != 'vehicle') {

            if (!empty($queryResponse)) {

               $itemCount = count($queryResponse);
               $postVisitorJsonData = array();
               $authScope = "visitors:write";
               $accessToken =  $this->action_generateAuthAccessToken($authScope);
               $postVisitorJsonData = array();
               foreach ($queryResponse as $key => $response) {

                  $venueContactName = explode(" ", $response['client_name']);
                  $venueContactFname = !empty($venueContactName[0]) ? $venueContactName[0] : '';
                  $venueContactLname = !empty($venueContactName[1]) ? $venueContactName[1] : '';

                  $covidVisitorRecords = explode(':::', $response['question_answer']);

                  $visitorStartDate = '0000-00-00';
                  if (!empty($covidVisitorRecords[2])) {
                     $visitorStartDate = date('Y-m-d', strtotime(str_replace('/', '-', $covidVisitorRecords[2])));
                  }

                  $covidVisitorFname = "";
                  $covidVisitorLname = "";
                  $covidVisitorName = explode(" ", $covidVisitorRecords[0]);
                  $covidVisitorFname = $covidVisitorName[0];
                  $covidVisitorLname = !empty($covidVisitorName[1]) ? $covidVisitorName[1] : '';


                  $visitorStartTime = '00:00:00';
                  if (!empty($covidVisitorRecords[3])) {
                     $visitorStartTime = $covidVisitorRecords[3];
                  }

                  $postVisitorJsonData[] = array(
                     'gateway_request_id' => $this->webhookId,
                     'app_provider' => array(
                        'name' => $this->appProviderName,
                        'contact' => $this->appProviderContact
                     ),

                     "message" => "DATA_FOUND",
                     "request_complete" => (($itemCount == $key + 1 || $itemCount == 1) ? true : false),
                     "venues" => array(
                        "name" => $this->appProviderName,
                        "abn" => "",
                        "venue_contact_phone_number" => $covidVisitorRecords[1],
                        "venue_contact_first_name" =>  $covidVisitorFname,
                        "venue_contact_last_name" => $covidVisitorLname,
                        "venue_contact_email" => $response['email'],
                        "address_line_1" => !empty($covidVisitorRecords[5]) ? $covidVisitorRecords[5] : '',
                        "address_line_2" => "",
                        "city_suburb" => "",
                        "postcode" => "",
                        "location_contact_phone" => !empty($response['telephone']) ? $response['telephone'] : '',
                        "location_contact_first_name" => $venueContactFname,
                        "location_contact_last_name" => $venueContactLname,
                        "location_contact_email" => $response['email'],
                        "location_description" => !empty($covidVisitorRecords[6]) ? $covidVisitorRecords[6] : '',
                        "third_party_location_id" => "",
                        "visitors" => array(
                           "first_name" => $covidVisitorRecords[0],
                           "phone" => $covidVisitorRecords[1],
                           "start_date_time" => $visitorStartDate . "T" . $visitorStartTime . "+00:00"
                        ),
                     ),
                  );
               }

               //convert json data as per the api payload....
               $postVisitorJsonData = htmlspecialchars(json_encode($postVisitorJsonData), ENT_NOQUOTES);
               $visitorPostJson =  str_replace(array(
                  '[', ']'
               ), array('{', '}'), $postVisitorJsonData);

               $curl = curl_init();

               curl_setopt_array($curl, array(
                  CURLOPT_URL => VISITOR_POST_REQUEST_URL,
                  CURLOPT_RETURNTRANSFER => true,
                  CURLOPT_ENCODING => '',
                  CURLOPT_MAXREDIRS => 10,
                  CURLOPT_TIMEOUT => 0,
                  CURLOPT_FOLLOWLOCATION => true,
                  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                  CURLOPT_CUSTOMREQUEST => 'POST',
                  CURLOPT_POSTFIELDS => $visitorPostJson,
                  CURLOPT_HTTPHEADER => array(
                     'Authorization:' . $accessToken->token_type . ' ' . $accessToken->access_token,
                     'Content-Type: application/json'
                  ),
               ));

               $visitorApiResponse = curl_exec($curl);
               echo $visitorApiResponse;
               curl_close($curl);
               exit;
            } else {

               $postVisitorJsonData = array(
                  'gateway_request_id' => $this->webhookId,
                  'app_provider' => array(
                     'name' => $this->appProviderName,
                     'contact' => $this->appProviderContact
                  ),

                  "message" => "DATA_NOT_FOUND",
                  "request_complete" => true,
                  "venues" => array(),
               );

               echo json_encode($postVisitorJsonData);
            }
         } else {
            $postVisitorJsonData = array(
               'gateway_request_id' => $response->gateway_request_id, //GATEWAY_REQUEST_ID,
               'app_provider' => array(
                  'name' => $this->appProviderName,
                  'contact' => $this->appProviderContact
               ),

               "message" => "SEARCH_TYPE_NOT_IMPLEMENTED",
               "request_complete" => true,
               "venues" => array(),
            );
            echo json_encode($postVisitorJsonData);
         }
      } else {

         $postVisitorJsonData = array(
            'gateway_request_id' => $this->webhookId,
            'app_provider' => array(
               'name' => $this->appProviderName,
               'contact' => $this->appProviderContact
            ),

            "message" => "DATA_NOT_APPLICABLE",
            "request_complete" => true,
            "venues" => array(),
         );
         echo json_encode($postVisitorJsonData);
      }
      exit;
   }

   /**
    * Not run now 
    * Run while all code finish
    */
   public function action_createWebhookRequest()
   {

      $authScope = "webhook:write";
      // "id" => "4332ab39-211f-49b6-a6cb-2f3ce2384d8b",
      $postRequest = array(
         "id" => "c90ac149-6d6e-40fe-bea3-42b955968a65",
         // "id" => "4332ab39-211f-49b6-a6cb-2f3ce2384d8b",
         "name" => "Scanlink QR Codes",
         "webhook_url" => URL::base() . "/Visitationapi",
         "support_email" => "satish@galatech.com.au",
         "shared_key" => "scanlink-66721bd33352"
         // "shared_key" => "66721bd3-3352-4401-8f2c-9e0b9ab05fbb"
      );

      $curl = curl_init();
      $accessToken =  $this->action_generateAuthAccessToken($authScope);

      curl_setopt_array($curl, array(
         CURLOPT_URL => CREATE_WEBHOOK_POST_REQUEST,
         CURLOPT_RETURNTRANSFER => true,
         CURLOPT_ENCODING => '',
         CURLOPT_MAXREDIRS => 10,
         CURLOPT_TIMEOUT => 0,
         CURLOPT_FOLLOWLOCATION => true,
         CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
         CURLOPT_CUSTOMREQUEST => 'POST',
         CURLOPT_POSTFIELDS => $postRequest,
         CURLOPT_HTTPHEADER => array(
            'Authorization:' . $accessToken->token_type . ' ' . $accessToken->access_token,
            'Content-Type: application/json',
            'Accept:application/json'
         ),
      ));

      $curlExecuteResponse = curl_exec($curl);

      if (!$curlExecuteResponse) {
         die('Error: "' . curl_error($curl) . '" - Code: ' . curl_errno($curl));
      }

      $data = array('values' => $curlExecuteResponse);

      mail('shaileshk@webplanex.com', 'Webhook Response', json_encode($curlExecuteResponse));
      Model::factory("settings")->updateVisitationWebhookData($data);
      curl_close($curl);
      Response::factory(array('status' => 200));
      die('Webhook Request done');
   }

   /**
    * Url : /dhhs/visitations/v1/webhooks/{id}
    * Retive created webhook details
    */
   public function action_retriveWebhookDetails()
   {
      $authScope = "webhook:read";

      $curl = curl_init();
      $accessToken =  $this->action_generateAuthAccessToken($authScope);

      curl_setopt_array($curl, array(
         CURLOPT_URL => WEBHOOK_GET_REQUEST . $this->webhookId,
         CURLOPT_RETURNTRANSFER => true,
         CURLOPT_ENCODING => '',
         CURLOPT_MAXREDIRS => 10,
         CURLOPT_TIMEOUT => 0,
         CURLOPT_FOLLOWLOCATION => true,
         CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
         CURLOPT_CUSTOMREQUEST => 'GET',
         CURLOPT_HTTPHEADER => array(
            'Authorization:' . $accessToken->token_type . ' ' . $accessToken->access_token,
            'Content-Type: application/json'
         ),
      ));

      $curlExecuteResponse = curl_exec($curl);

      if (!$curlExecuteResponse) {
         die('Error: "' . curl_error($curl) . '" - Code: ' . curl_errno($curl));
      }

      echo $curlExecuteResponse;

      mail('shaileshk@webplanex.com', 'Webhook Response', json_encode($curlExecuteResponse));
      curl_close($curl);
      Response::factory(array('status' => 200));
      die('Webhook Retrive info  done');
   }

   public function action_generateTestingData()
   {
      $testing = Model::factory("covidapisearch")->generateTestingData();
   }


   public function action_customSearchTypeByRequest()
   {
      $where = array();
      $searchType = trim($_REQUEST['search_type']);

      if ($searchType) {
         switch ($searchType) {
            case 'phone_restricted_response':
               $where = array(
                  'phone_number' => $_REQUEST['phone_number'],
                  'search_type' => "phone_restricted_response"
               );
               break;
            case 'address':
               $where = array(
                  // 'phone_number' => $_REQUEST['phone_number'],
                  'search_type' => "address",
                  'address' => array(
                     "address" => $_REQUEST['address'],
                     'short_address' => !empty($_REQUEST['short_address']) ? $_REQUEST['short_address'] : '',
                     "building_number" => !empty($_REQUEST['building_number']) ? $_REQUEST['building_number'] : '',
                     "unit_name" => !empty($_REQUEST['unit_name']) ? $_REQUEST['unit_name'] : '',
                     "unit_type" => !empty($_REQUEST['unit_type']) ? $_REQUEST['unit_type'] : '',
                     "street_name" => !empty($_REQUEST['street_name']) ? $_REQUEST['street_name'] : '',
                     "street_type" => !empty($_REQUEST['street_type']) ? $_REQUEST['street_type'] : '',
                     "city_suburb" => !empty($_REQUEST['city_suburb']) ? $_REQUEST['city_suburb'] : '',
                     "postcode" => !empty($_REQUEST['postcode']) ? $_REQUEST['postcode'] : '',
                     "state" => !empty($_REQUEST['state']) ? $_REQUEST['state'] : '',
                     "latitude" => !empty($_REQUEST['latitude']) ? $_REQUEST['latitude'] : '',
                     "longitude" => !empty($_REQUEST['longitude']) ? $_REQUEST['longitude'] : ''
                  ),
               );
               break;
            case 'phone_period_range':
               $where = array(
                  'phone_number' => $_REQUEST['phone_number'],
                  'search_type' => "phone_period_range"
               );
               break;
            case 'vehicle_lookup':
               $where = array(
                  "search_type" => "vehicle_lookup",
                  "location" => array(
                     "name" => !empty($_REQUEST['name']) ? $_REQUEST['name'] : '',
                     "address_line_1" => !empty($_REQUEST['address_line_1']) ? $_REQUEST['address_line_1'] : '',
                     "city_suburb" => !empty($_REQUEST['city_suburb']) ? $_REQUEST['city_suburb'] : '',
                     "postcode" => !empty($_REQUEST['postcode']) ? $_REQUEST['postcode'] : ''
                  )
               );
               break;
            case 'location_lookup':
               $where = array(
                  "search_type" => "location_lookup",
                  "location" => array(
                     "name" => !empty($_REQUEST['name']) ? $_REQUEST['name'] : '',
                     "address_line_1" => !empty($_REQUEST['address_line_1']) ? $_REQUEST['address_line_1'] : '',
                     "city_suburb" => !empty($_REQUEST['city_suburb']) ? $_REQUEST['city_suburb'] : '',
                     "postcode" => !empty($_REQUEST['postcode']) ? $_REQUEST['postcode'] : ''
                  )
               );
               break;
            case 'location':

               $where = array(
                  "location_ids" => array($_REQUEST['location_ids']),
                  "application_id" => $_REQUEST['application_id'], //"c6b30240-81ec-46f2-b9c1-e2ac08014aa5",
                  "search_type" => "location"
               );
               break;
            case 'vehicle':
               // search type vehicle
               $where = array(
                  "location_ids" => array($_REQUEST['location_ids']),
                  "application_id" => $_REQUEST['application_id'],
                  "search_type" => "vehicle"
               );
               break;
            case 'phone_check_in_range':
               $where = array(
                  "phone_number" => $_REQUEST['phone_number'],
                  "checkin_minutes_prior" => $_REQUEST['checkin_minutes_prior'],
                  "checkin_minutes_after" => $_REQUEST['checkin_minutes_after'],
                  "search_type" => "phone_check_in_range"
               );
               break;
            case '':
               echo 'no request type provided';
               break;
         }

         //"2020-11-01T15:01:16+00:00" Date time format
         $where = $where + array(
            "gateway_request_id" => GATEWAY_REQUEST_ID,
            "period_start_datetime" => $_REQUEST['period_start_datetime'],
            "period_end_datetime" => $_REQUEST['period_end_datetime']
         );

         if ($searchType != "phone_check_in_range" && $searchType != "vehicle") {

            $queryResponse = Model::factory('covidapisearch')->getCovidApiSearchData($where);

            if (!empty($queryResponse)) {
               $itemCount = count($queryResponse);
               $postVisitorJsonData = array();
               $authScope = "visitors:write";
               $accessToken =  $this->action_generateAuthAccessToken($authScope);
               $postVisitorJsonData = array();
               foreach ($queryResponse as $key => $response) {

                  $venueContactName = explode(" ", $response['client_name']);
                  $venueContactFname = !empty($venueContactName[0]) ? $venueContactName[0] : '';
                  $venueContactLname = !empty($venueContactName[1]) ? $venueContactName[1] : '';

                  $covidVisitorRecords = explode(':::', $response['question_answer']);

                  $visitorStartDate = '0000-00-00';
                  if (!empty($covidVisitorRecords[2])) {
                     $visitorStartDate = date('Y-m-d', strtotime(str_replace('/', '-', $covidVisitorRecords[2])));
                  }

                  $covidVisitorFname = "";
                  $covidVisitorLname = "";
                  $covidVisitorName = explode(" ", $covidVisitorRecords[0]);
                  $covidVisitorFname = $covidVisitorName[0];
                  $covidVisitorLname = !empty($covidVisitorName[1]) ? $covidVisitorName[1] : '';


                  $visitorStartTime = '00:00:00';
                  if (!empty($covidVisitorRecords[3])) {
                     $visitorStartTime = $covidVisitorRecords[3];
                  }

                  $postVisitorJsonData = array(
                     'gateway_request_id' => GATEWAY_REQUEST_ID,
                     'app_provider' => array(
                        'name' => $this->appProviderName,
                        'contact' => $this->appProviderContact
                     ),

                     "message" => "DATA_FOUND",
                     "request_complete" => (($itemCount == $key + 1 || $itemCount == 1) ? true : false),
                     "venues" => array(
                        array(
                           "name" => $this->appProviderName,
                           "abn" => "",
                           "venue_contact_phone_number" => $covidVisitorRecords[1], //$response['telephone'],
                           "venue_contact_first_name" =>  $covidVisitorFname, //$venueContactFname,
                           "venue_contact_last_name" => $covidVisitorLname,
                           "venue_contact_email" => $response['email'],
                           "address_line_1" => !empty($covidVisitorRecords[5]) ? $covidVisitorRecords[5] : '', //$response['address'],
                           "address_line_2" => "",
                           "city_suburb" => "",
                           "postcode" => "",
                           "location_contact_phone" => !empty($response['telephone']) ? $response['telephone'] : '',  //"+61411111111",
                           "location_contact_first_name" => $venueContactFname, //"Location Contact First Name",
                           "location_contact_last_name" => $venueContactLname, //"location Contact Last Name",
                           "location_contact_email" => $response['email'], //"support-location@venue.com.au",
                           "location_description" => !empty($covidVisitorRecords[6]) ? $covidVisitorRecords[6] : '',
                           "third_party_location_id" => "",
                           "visitors" => array(
                              array(
                                 "first_name" => $covidVisitorRecords[0],
                                 "phone" => $covidVisitorRecords[1],
                                 "start_date_time" => $visitorStartDate . "T" . $visitorStartTime . "+00:00"
                              ),
                           ),
                        ),
                     ),
                  );
               }

               //convert json data as per the api payload....
               $postVisitorJsonData = htmlspecialchars(json_encode($postVisitorJsonData), ENT_NOQUOTES);
               //$visitorPostJson =  str_replace(array('[', ']'), array('{', '}'), $postVisitorJsonData);

               echo $postVisitorJsonData;
               exit;
               $curl = curl_init();

               curl_setopt_array($curl, array(
                  CURLOPT_URL => VISITOR_POST_REQUEST_URL,
                  CURLOPT_RETURNTRANSFER => true,
                  CURLOPT_ENCODING => '',
                  CURLOPT_MAXREDIRS => 10,
                  CURLOPT_TIMEOUT => 0,
                  CURLOPT_FOLLOWLOCATION => true,
                  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                  CURLOPT_CUSTOMREQUEST => 'POST',
                  CURLOPT_POSTFIELDS => $visitorPostJson,
                  CURLOPT_HTTPHEADER => array(
                     'Authorization:' . $accessToken->token_type . ' ' . $accessToken->access_token,
                     'Content-Type: application/json'
                  ),
               ));

               $visitorApiResponse = curl_exec($curl);
               echo $visitorApiResponse;
               curl_close($curl);
               exit;
            } else {

               $postVisitorJsonData = array(
                  'gateway_request_id' => GATEWAY_REQUEST_ID,
                  'app_provider' => array(
                     'name' => $this->appProviderName,
                     'contact' => $this->appProviderContact
                  ),

                  "message" => "DATA_NOT_FOUND",
                  "request_complete" => true,
                  "venues" => array(),
               );

               echo json_encode($postVisitorJsonData);
            }
         } else {
            $postVisitorJsonData = array(
               'gateway_request_id' => GATEWAY_REQUEST_ID,
               'app_provider' => array(
                  'name' => $this->appProviderName,
                  'contact' => $this->appProviderContact
               ),

               "message" => "SEARCH_TYPE_NOT_IMPLEMENTED",
               "request_complete" => 1,
               "venues" => array(),
            );
            echo json_encode($postVisitorJsonData);
         }
      } else {

         $postVisitorJsonData = array(
            'gateway_request_id' => GATEWAY_REQUEST_ID,
            'app_provider' => array(
               'name' => $this->appProviderName,
               'contact' => $this->appProviderContact
            ),

            "message" => "DATA_NOT_APPLICABLE",
            "request_complete" => 1,
            "venues" => array(),
         );
         echo json_encode($postVisitorJsonData);
      }
      exit;
   }
}
