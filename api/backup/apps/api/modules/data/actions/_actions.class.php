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

}
