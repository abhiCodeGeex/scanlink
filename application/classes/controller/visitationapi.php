<?php

defined('SYSPATH') or die('No direct script access.');

class Controller_Visitationapi extends Controller_Template_Master
{
    public function action_index()
    {
        $data = array('values' => 'This is the response');
        if (!empty($_REQUEST) || $data) {
            Model::factory("settings")->updateVisitationWebhookData2($data);
            //array("visitation_webhook_response" => json_encode($_REQUEST))
        }
        Response::factory(array('status' => 200));
        exit;
        //echo http_response_code('200');
        // mail('shaileshk@webplanex.com', 'Webhook Response', json_encode($_REQUEST));
    }
}
