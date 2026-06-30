<?php

/**
 * data actions.
 *
 * @package    analytics
 * @subpackage data
 * @author     Ajay Makwana <ajay@webplanex.net>
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class dataActions extends sfActions {

  /**
   * Executes index action
   *
   * @param sfRequest $request A request object
   */
  public function executeIndex(sfWebRequest $request) {
    $this->forward('default', 'module');
  }

  public function executeGetdata(sfWebRequest $request) {
    $this->forward404Unless($request->hasParameter('key'));
    $key = $request->getParameter('key');
		$item = Doctrine::getTable('AnaItems')->findByItemKey($key);
   // $this->forward404Unless($item = Doctrine::getTable('AnaItems')->findByItemKey($key));
    
    $id_item = $item[0]->getId();
    
    $countries = Doctrine_Core::getTable('AnaItemAnalytics')->createQuery('ai')
                    ->select('ai.id, ai.id_item, ai.country_name,count(ai.id) as count_by_country')
                    ->where('ai.id_item = ?', $id_item)
                    ->groupBy('ai.country_name')->execute();

    $data = array();
    $data["COLUMNS"] = array("CONTEXT", "X_BAR_LABEL", "X_BAR_VALUE", "TOOL_TIP_TITLE", "DRILL_DATA");
		$default = array(
									"COLUMNS"=>array("CONTEXT", "X_BAR_LABEL","X_BAR_VALUE", "TOOL_TIP_TITLE"),
									'DATA'=>array(array("NO Data", "No Data", 0 ,  "No Data ^")) 
		   					);

    if (sizeof($countries) > 0) {
      // LEVEL - 1
      $iCount = 0;
      foreach ($countries as $country) {
        //default array
        $stateArr = AnaItemAnalyticsTable::getState($id_item, $country->getCountryName());
        $data["DATA"][] = array('Country', $country->getCountryName(), $country->getCountByCountry(), $country->getCountryName() . " ^ ", $stateArr);
      }
      $arr['country'] = $data;
			if(sizeof($arr['country']) == 0 ){
				$arr['country'] = $default;
			}
      //: by device
      $arr['device'] = AnaItemAnalyticsTable::getDevice($id_item);
			if(sizeof($arr['device']) == 0 ){
				$arr['device'] = $default;
			}
      
      //: by browsers
      $arr['browsers'] = AnaItemAnalyticsTable::getBrowser($id_item);
			if(sizeof($arr['browsers']) == 0 ){
				$arr['browsers'] = $default;
			}
    }else{
			$arr['browsers'] = $arr['device'] = $arr['country'] = $default;
		}
		echo json_encode($arr);
    return sfView::NONE;
  }
	
	
	/**
	** get all with wp key.
	**/
	public function executeScanalytics(sfWebRequest $request) {
    $this->forward404Unless($request->hasParameter('key'));
    $key = $request->getParameter('key');
		if($request->getParameter("orderby") != "") 
			$orderby = $request->getParameter("orderby");
		else
			$orderby = "id";
			
		$item = Doctrine::getTable('AnaItems')->findByItemKey($key);
    $id_item = $item[0]->getId();
    $analyticsArr = Doctrine_Core::getTable('AnaItemAnalytics')->createQuery('ai')
                    ->select('ai.id, ai.scan_type, ai.id_item, ai.latitude, ai.longitude, ab.browser_name, ad.device_name')
										->leftJoin('ai.AnaBrowsers ab')
										->leftJoin('ai.AnaDevices ad')
                    ->where('ai.id_item = ?', $id_item)
										->orderBy("ai.".$orderby." ASC")
                    ->execute();

    $data = array();
		if( sizeof( $analyticsArr ) > 0){
			foreach( $analyticsArr  as $analytics){
				 $tmpArr = array();
				 $tmpArr['id'] = $analytics->getId();
				 $tmpArr['id_item'] = $analytics->getIdItem();
				 $tmpArr['latitude'] = $analytics->getLatitude();
				 $tmpArr['longitude'] = $analytics->getLongitude();
				 $tmpArr['created_at'] = $analytics->getCreatedAt();
				 $tmpArr['country_name'] = $analytics->getCountryName();
				 $tmpArr['city_name'] = $analytics->getCityName();
				 $tmpArr['scan_type'] = $analytics->getScanType();
				 $data[] = $tmpArr;
			} 
		}
    
		if( $request->hasParameter("print") ){
			echo "<pre>";
			print_r($data);
			echo "<pre>";
			exit;
		}
		echo json_encode($data);
    return sfView::NONE;
  }

	/**
	** get single recored with wp key.
	**/
	public function executeFindwpanalyticsbyid(sfWebRequest $request) {
    $this->forward404Unless($request->hasParameter('key'));
		$key = $request->getParameter('key');
		$id = $request->getParameter('id');
		$item = Doctrine::getTable('AnaItems')->findByItemKey($key);
    $id_item = $item[0]->getId();
    $analyticsArr = Doctrine_Core::getTable('AnaItemAnalytics')->createQuery('ai')
                    ->select('ai.id, ai.scan_type, ai.id_item, ai.latitude, ai.longitude, ab.browser_name, ad.device_name')
										->leftJoin('ai.AnaBrowsers ab')
										->leftJoin('ai.AnaDevices ad')
                    ->where('ai.id_item = ?', $id_item)
										->andWhere('ai.id = ?', $id)
										->limit(1)
                    ->execute();

    $data = array();
		if( sizeof( $analyticsArr ) > 0){
			foreach($analyticsArr as $analytics){
			 $tmpArr['id'] = $analytics->getId();
			 $tmpArr['id_item'] = $analytics->getIdItem();
			 $tmpArr['latitude'] = $analytics->getLatitude();
			 $tmpArr['longitude'] = $analytics->getLongitude();
			 $tmpArr['scan_type'] = $analytics->getScanType();
			 $tmpArr['device'] = $analytics->getAnaDevices()->getDeviceName();
			 $tmpArr['browser'] = $analytics->getAnaBrowsers()->getBrowserName();
			 $tmpArr['created_at'] = date('d/m/Y H:i', strtotime($analytics->getCreatedAt()));
			 $data  = $tmpArr;
			}
		} 
    
		if( $request->hasParameter("print") ){
			echo "<pre>";
			print_r($data);
			echo "<pre>";
			exit;
		}
		echo json_encode($data);
    return sfView::NONE;
  }



	/**
	** map chart.
	**/
	public function executeMap(sfWebRequest $request) {
		$this->forward404Unless($request->hasParameter('key'));
		$key = $request->getParameter('key');
		$item = Doctrine::getTable('AnaItems')->findByItemKey($key);
		$mapAnalytics['locations'] = array();
		if( sizeof($item) > 0){
			$item = $item[0];
			$countries = Doctrine_Core::getTable('AnaItemAnalytics')->createQuery('ai')
											->where('ai.id_item = ?', $item->getId())
											->groupBy('ai.country_name')->execute();
			
			if( sizeof($countries) > 0 ){
				foreach($countries as $country){
					$mapAnalytics['locations'][] = array('latitude'=>$country->getLatitude(), 'longitude'=>$country->getLongitude(),'country'=>$country->getCountryName(), 'created'=>$country->getCreatedAt(), 'id'=>$country->getIdItem(), 'scan_type'=>$country->getScanType());							
				}
			}		
		}
		echo json_encode($mapAnalytics);
		return sfView::NONE;
	}

	/**
	**
	**/
	public function executeCountrymap(sfWebRequest $request) {
		$this->forward404Unless($request->hasParameter('key'));
		$this->forward404Unless($request->hasParameter('country'));
		$key = $request->getParameter('key');
		$itemArr = Doctrine::getTable('AnaItems')->findByItemKey($key);
		$mapAnalytics['locations'] = array();
		if( sizeof($itemArr) > 0 ) {
			$data = Doctrine_Core::getTable('AnaItemAnalytics')->createQuery('ai')
									->select('ai.*, GROUP_CONCAT( ab.browser_name ) as browser_name, GROUP_CONCAT( ad.device_name ) as device_name')
									->leftJoin('ai.AnaBrowsers ab')
									->leftJoin('ai.AnaDevices ad')
									->where('ai.country_name = ?', $request->getParameter('country'))
									->andWhere('ai.id_item = ?', $itemArr[0]->getId())
									->groupBy('ai.latitude, ai.longitude')
									->execute();
			
			if( sizeof($data) > 0 ){
				foreach($data as $item){
					$arr = array('latitude'=>$item->getLatitude(), 'longitude'=>$item->getLongitude(),
												'country'=>$item->getCountryName(), 
												'scan_type'=>$item->getScanType(), 
												'created'=>date('d/m/Y H:i', strtotime($item->getCreatedAt()))
												);	
					if(is_object($item->getAnaDevices())){
							$deviceArr = $item->getDeviceName();
							$deviceArr = explode(',',$deviceArr);
							$deviceArr = array_count_values($deviceArr);
							if( sizeof($deviceArr)> 0){
								foreach($deviceArr as $key=>$value){
									$arr['device'][] = array("key"=>$key,"value"=>$value);
								}
							}else{
								$arr['device'][] = array("key"=>"Unknown Device","value"=>"1");				
							}
					}else{
							$arr['device'][] = array("key"=>"Unknown Device","value"=>"1");				
					}
							
					if(is_object($item->getAnaBrowsers())){
							$browserArr = $item->getBrowserName();
							$browserArr = explode(',',$browserArr);
							$browserArr = array_count_values($browserArr);
							if( sizeof($browserArr) > 0){
								foreach($browserArr as $key=>$value){
									$arr['browser'][] = array("key"=>$key,"value"=>$value);
								}
							}else{
								$arr['browser'][] = array("key"=>"Unknown browser","value"=>"1");			
							}
					}else{
							$arr['browser'][] = array("key"=>"Unknown browser","value"=>"1");			
					}
					$mapAnalytics['locations'][] = $arr;
				}
			}
		}
		if($request->hasParameter("p")){
			echo "<pre>";
			print_r($mapAnalytics);
			exit;
		}
			
		echo json_encode($mapAnalytics);
		return sfView::NONE;		
	}


	/*public function executeCountrymap(sfWebRequest $request) {
		$this->forward404Unless($request->hasParameter('key'));
		$this->forward404Unless($request->hasParameter('country'));
		$key = $request->getParameter('key');
		$itemArr = Doctrine::getTable('AnaItems')->findByItemKey($key);
		$mapAnalytics['locations'] = array();
		if( sizeof($itemArr) > 0 ) {
			$data = Doctrine_Core::getTable('AnaItemAnalytics')->createQuery('ai')
									->select('ai.*, ab.browser_name, ad.device_name')
									->leftJoin('ai.AnaBrowsers ab')
									->leftJoin('ai.AnaDevices ad')
									->where('ai.country_name = ?', $request->getParameter('country'))
									->andWhere('ai.id_item = ?', $itemArr[0]->getId())
									->execute();
									
			if( sizeof($data) > 0 ){
				foreach($data as $item){
					$arr = array('latitude'=>$item->getLatitude(), 'longitude'=>$item->getLongitude(),
												'country'=>$item->getCountryName(), 
												'created'=>date('d/m/Y H:i', strtotime($item->getCreatedAt()))
												);	
					if(is_object($item->getAnaDevices()))
							$arr['device'] = $item->getAnaDevices()->getDeviceName();
					else
							$arr['device'] = "Unknown device";	
							
					if(is_object($item->getAnaBrowsers()))
							$arr['browser'] = $item->getAnaBrowsers()->getBrowserName();
					else
							$arr['device'] = "Unknown Browser";			
	
					$mapAnalytics['locations'][] = $arr;
				}
			}
		}
		echo json_encode($mapAnalytics);
		return sfView::NONE;		
	}*/
}
