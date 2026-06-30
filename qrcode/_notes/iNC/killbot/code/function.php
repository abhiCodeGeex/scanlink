<?php

class Killbot {
    protected $server = 'https://killbot.org';
    protected $active;
    protected $apikey;
    protected $bot;

    function __construct($config = array()) {
        $this->active = $config['active'];
        $this->apikey = $config['apikey'];
        $this->bot = $config['bot_redirect'];
    }

    function get_client_ip() {
        if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
            $_SERVER['REMOTE_ADDR'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
            $_SERVER['HTTP_CLIENT_IP'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
        }
        $client  = @$_SERVER['HTTP_CLIENT_IP'];
        $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
        $remote  = $_SERVER['REMOTE_ADDR'];
        
        if(filter_var($client, FILTER_VALIDATE_IP)) {
            $ip = $client;
        } elseif(filter_var($forward, FILTER_VALIDATE_IP)) {
            $ip = $forward;
        } else {
            $ip = $remote;
        }
        
        return $ip;
    }
    
    function httpGet($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Killbot Blocker');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        return $response;
    }

    function logs($ip, $message, $type) {
        $click = fopen(__DIR__ . "/../logs/" . $type . ".txt", "a");
        fwrite($click, $ip . " - " . $message . " at " . date('r') . "\n");
        fclose($click);
    }

    function check() {
        $ip         = $this->get_client_ip();
        $respons    = $this->httpGet($this->server . "/api/v2/blocker?ip=".$ip."&apikey=".$this->apikey."&ua=".urlencode($_SERVER['HTTP_USER_AGENT'])."&url=".urldecode($_SERVER['REQUEST_URI']));
        $json       = json_decode($respons,true);
        if($json['meta']['code'] == 200) {
            if($json['data']['block_access'] == true) {
                $this->logs($ip, $json['data']['block_by'], 'blocked');
                return true;
            } else {
                $this->logs($ip, 'Granted by Killbot', 'granted');
                return false;
            }
        } else {
            if(!empty($json['meta']['message'])) {
                $this->logs($ip, 'Not blocked because ' . $json['meta']['message'], 'error');
                return false;
            } else {
                $this->logs($ip, 'Not blocked because can\'t connect to Killbot server', 'error');
                return false;
            }
        }
    }

    function run() {
        if($this->active == true) {
            if($this->check() == true){
                $this->error($this->bot);
            }
        }
    }
    
	function error($code){
		$tempale = file_get_contents(__DIR__ . '/../templates/main.html');
		switch ($code) {
			case '403':
				header('HTTP/1.0 403 Forbidden');
				$tempale = str_replace("{text}", "403 Forbidden", $tempale);
				$tempale = str_replace("{error_message}", "You dont have authorization to view this page.", $tempale);
				die($tempale);
			break;
			case '404':
				header("HTTP/1.0 404 Not Found");
				$tempale = str_replace("{text}", "404 Not Found", $tempale);
				$tempale = str_replace("{error_message}", "The requested was not found on this server.", $tempale);
				die($tempale);
			break;
			case 'suspend':
				header('HTTP/1.0 503 Service Unavailable');
				$tempale = str_replace("{text}", "Service Suspended", $tempale);
				$tempale = str_replace("{error_message}", "Please contact support to correct issues causing your service to be offline.", $tempale);
				die($tempale);
			break;
			default:
				die(header("Location: ".$code));
			break;
		}
	}
}