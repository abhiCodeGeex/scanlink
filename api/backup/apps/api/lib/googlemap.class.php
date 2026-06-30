<?php
	class googlemap{
		
		protected $googleMapapi = "AIzaSyCYmJYoqijNKnyd1Y4e_53t3mdc-rhH4xY";
		protected $ipinfodbIpapi = "50b85784a64f027778df4f88f7016b247c7a3d413d09b6465bec167b6a83eecf";
				
		public  function getAddress($lat,$long){
			$response = json_decode(file_get_contents("http://maps.googleapis.com/maps/api/geocode/json?latlng=$lat,$long&sensor=false"));
			if ( $response->status === 'OK') {
					$timezoneResponse = file_get_contents("https://maps.googleapis.com/maps/api/timezone/json?location=$lat,$long&timestamp=".time()."&sensor=true");
					$timezoneResponse = json_decode($timezoneResponse);
					$geocodeResponse = array();		
					foreach($response->results[0]->address_components as $geoItem){
						$geocodeResponse[$geoItem->types[0]]['long_name'] = $geoItem->long_name;
						$geocodeResponse[$geoItem->types[0]]['short_name'] = $geoItem->short_name;
					}
					if($timezoneResponse->status == "OK"){
						$geocodeResponse['timeZone']['long_name'] = $timezoneResponse->timeZoneId;
						$geocodeResponse['timeZone']['short_name'] = $timezoneResponse->timeZoneId;
					}
					return $geocodeResponse;
			}
			return false;
		}
		
		
		
			
		
		
		
		
		
		
		
		
		/**
		** get lat / lng from ip address.
		**/
		public function getLocation($ip){
			$location = json_decode(file_get_contents('http://api.ipinfodb.com/v3/ip-city/?key=' . $this->ipinfodbIpapi . '&ip=' . $ip . '&format=json'), TRUE);
			return $location;
		}	
		
		
		
	}	
?>