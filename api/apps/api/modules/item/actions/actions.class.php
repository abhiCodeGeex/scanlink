<?php

/**
 * item actions.
 *
 * @package    analytics
 * @subpackage item
 * @author     Ajay Makwana <ajay@webplanex.net>
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class itemActions extends sfActions {
  /*   * *
   * generate anlytics entry. 
   * * */
	public function executeDatabase(sfWebRequest $request) {
    $table = $request->getParameter("table");
    $result = Doctrine::getTable('AnaItemAnalytics')->createQuery('a')->select('a.*')
							->orderBy('a.id DESC')->execute();	
    echo "<pre>";
    print_r($result->toArray());
    exit;
  }

  public function executeIndex(sfWebRequest $request) {
		$locationArr = $addressArr = array();
    $ip = $request->getRemoteAddress();
    $googlemap = new googlemap();
    //$refererUrl = $request->getReferer();
    //echo $request->getReferer(); exit;
    $refererUrlArr = parse_url($request->getReferer());
		$refererUrl = $refererUrlArr['scheme'] . '://' . $refererUrlArr['host'];

		/******/
		$parseUrl = parse_url($request->getReferer());
		$www = substr($parseUrl['host'],0,4);
		if($www == "www."){
			$parseUrl['host'] = substr($parseUrl['host'],4,strlen($parseUrl['host']));
		}
    $url = $parseUrl['scheme'] . '://' . $parseUrl['host'];
    $refererUrl = $url; 
    /******/
		
    if ($request->hasParameter("key")) {

			$itemKey = $request->getParameter("key");
      $itemArr = AnaItemsTable::getItem($itemKey, $refererUrl);
      //echo "Hello".$refererUrl; exit;

      if (sizeof($itemArr) > 0) {

        //:request var.
        $latitude = $request->getParameter("lat");
        $longitude = $request->getParameter("lng");
        $item_key = $request->getParameter("key");
        //:from current location GPS
        if (!empty($latitude) && !empty($longitude) && !empty($item_key)) {
          $addressArr = $googlemap->getAddress($latitude, $longitude);
          $scanType = "gps";

          //:from IP
        } else { 
          $locationArr = $googlemap->getLocation($ip);
          //echo "<pre>"; print_R ($locationArr); exit;

					$scanType = "ip";
          if ($locationArr['statusCode'] == "OK") {
            $latitude = $locationArr['latitude'];
            $longitude = $locationArr['longitude'];
            $addressArr = $googlemap->getAddress($latitude, $longitude);
          }
        }

        if ($addressArr !== false && sizeof($addressArr) > 0) {
          $itemArr = $itemArr[0];
          $analyticsObj = new AnaItemAnalytics();
          $analyticsObj->setIdItem($itemArr->getId());
					
					//:array from google map address (GPS).
					if (array_key_exists("locality", $addressArr))
            $analyticsObj->setCityName($addressArr['locality']['long_name']);

          if (array_key_exists("country", $addressArr))
            $analyticsObj->setCountryName($addressArr['country']['long_name']);

          if (array_key_exists("postal_code", $addressArr))
            $analyticsObj->setZipcode($addressArr['postal_code']['long_name']);

          if (array_key_exists("administrative_area_level_1", $addressArr))
            $analyticsObj->setRegionName($addressArr['administrative_area_level_1']['long_name']);
					
					if (array_key_exists("timeZone", $addressArr)){
            $analyticsObj->setTimezone($addressArr['timeZone']['long_name']);	
						@date_default_timezone_set($addressArr['timeZone']['long_name']);
						$analyticsObj->setCreatedAt(date('Y-m-d H:i:s',time()));
					}	
						
					// (IP)
          if (array_key_exists("countryName", $locationArr))
            $analyticsObj->setCountryCode($locationArr['countryCode']);

          $useragentArr = custom::getBrowser();

          $platformArr = Doctrine::getTable('AnaPlatforms')->findByPlatformName($useragentArr['platform']);
          if (sizeof($platformArr) > 0)
            $analyticsObj->setIdPlatform($platformArr[0]->getId());

          $deviceArr = Doctrine::getTable('AnaDevices')->findByDeviceName($useragentArr['device']);
          if (sizeof($deviceArr) > 0)
            $analyticsObj->setIdDevice($deviceArr[0]->getId());

          $browserArr = Doctrine::getTable('AnaBrowsers')->findByBrowserName($useragentArr['name']);
          if (sizeof($browserArr) > 0)
            $analyticsObj->setIdBrowser($browserArr[0]->getId());

					
          $analyticsObj->setBrowserVersion($useragentArr['version']);
          $analyticsObj->setLatitude($latitude);
          $analyticsObj->setLongitude($longitude);
					$analyticsObj->setScanType($scanType);
          $analyticsObj->setIpAdd($ip);
          $analyticsObj->save();
        }
        $response['response'] = TRUE;
        $response['message'] = "Your analytics has been generated";
      }else {
        $response['response'] = FALSE;
        $response['message'] = "Error: Either your key is invalid or your are trying to generate analytics with other domain";
      }
    }
		//exit;
    //echo "(".json_encode($response).")";
    echo json_encode($response);
    exit;
    return sfView::NONE;
  }

  /**
   * * generate url/item key for analytics under created project.
   * */
  public function executeAddurl(sfWebRequest $request) {
    $response = array();
    //: check for project.
    $url = $request->getParameter("url");
    $parseUrl = parse_url($url);
	
		$www = substr($parseUrl['host'],0,4);
		if($www == "www."){
			$parseUrl['host'] = substr($parseUrl['host'],4,strlen($parseUrl['host']));
		}
	
    $url = $parseUrl['scheme'] . '://' . $parseUrl['host'];
    $projectArr = Doctrine::getTable('AnaProjects')->createQuery('p')->where('p.url = ?', $url)->orWhere('p.url = ?', $url . '/')->execute();
    if (sizeof($projectArr) == 0) {
      //:bad request.
      $response['response'] = FALSE;
      //$response['project_url'] = $url;
      $response['item_key'] = null;
      $response['message'] = "This project is not available.";
    } else {
      $itemArr = Doctrine::getTable('AnaItems')->createQuery('i')->where('i.url = ?', $request->getParameter("url"))->execute();
      //:success
      if (sizeof($itemArr) == 0) {
        $item_key = custom::getRandomString();
        $itemsObj = new AnaItems();
        $itemsObj->setIdProject($projectArr[0]->getId());
        $itemsObj->setUrl($request->getParameter("url"));
        $itemsObj->setItemKey($item_key);
        $itemsObj->setStatus(1);
        $itemsObj->save();
        $response['response'] = TRUE;
        $response['message'] = "Url key generated successfully.";
        $response['item_key'] = $item_key;
        //$response['project_url'] = $url;
        //:failed	
      } else {
        $response['response'] = TRUE;
        $response['message'] = "Url key generated successfully.";
        $response['item_key'] = $itemArr[0]->getItemKey();
        //$response['project_url'] = $url;
      }
    }
    print_r(json_encode($response));
    exit;
  }

	/* clear analytics of item [start] */
	public function executeClearItem(sfWebRequest $request) {
	    $this->forward404Unless($request->hasParameter('key'));
	    $key = $request->getParameter('key');

	    $item = Doctrine::getTable('AnaItems')->findByItemKey($key);
	    $id_item = $item[0]->getId();

	    Doctrine::getTable('AnaItemAnalytics')
       	     ->createQuery()
	            ->delete()
	            ->where('id_item = ?', $id_item)
	            ->execute();

	    exit;
	}
	/* clear analytics of item [end] */


}
