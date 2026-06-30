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

  protected $userArr = array();

  public function preExecute() {
    $this->userArr = array('user_type' => $this->getUser()->getCredentials(), 'id_user' => $this->getUser()->getAttribute('id'));
  }

  /**
   * Executes index action
   *
   * @param sfRequest $request A request object
   */
  public function executeIndex(sfWebRequest $request) {
    $this->pager = new sfDoctrinePager('AnaItems', sfConfig::get('app_paging_perpage', 10));

    //Get all projects if user has admin credentials
    if ($this->getUser()->hasCredential('admin')) {
						$query = Doctrine_Core::getTable('AnaItems')
										->createQuery('i')
										//->select('i.id,i.id_project,i.url, count(aia.id_item) as no_of_clicks')
										->select('i.*, count(aia.id_item) as no_of_clicks')
										->leftJoin('i.AnaProjects ap')
										->leftJoin('i.AnaItemAnalytics aia ON aia.id_item=i.id')
										->groupBy('i.id')
										->orderby('i.id DESC');
      $this->pager->setQuery($query);
			
    } elseif ($this->getUser()->hasCredential('user')) { //Get only limited projects if user has user credentials
						$query = Doctrine_Core::getTable('AnaItems')
										->createQuery('i')
										//->select('i.id,i.id_project,i.url, count(aia.id_item) as no_of_clicks')
										->select('i.*, count(aia.id_item) as no_of_clicks')
										->leftJoin('i.AnaProjects ap')
										->leftJoin('i.AnaItemAnalytics aia')
										->where('ap.id_user = ?', $this->getUser()->getAttribute('id'))
										->groupBy('i.id')
										->orderby('i.id DESC');
      $this->pager->setQuery($query);
    }


    $this->pager->setPage($request->getParameter('page', 1));
    //$this->pager->setMaxPerPage($resultsPerPage);
    $this->pager->init();
    $this->cpage = $request->getParameter('page');
    $this->items = $this->pager->getResults();
    if (sizeof($this->items) == 0 && $this->cpage > 1)
      $this->redirect('item/index?page=' . ($this->cpage - 1));
  }

  /**
   * * new item.
   * */
  public function executeNew(sfWebRequest $request) {
    $this->form = new AnaItemsForm(null, $this->userArr);
  }

  public function executeCreate(sfWebRequest $request) {
    $this->form = new AnaItemsForm(null, $this->userArr);
    if ($request->isMethod('post') == true) {
      $this->processForm($request, $this->form, "new");
    }
    $this->setTemplate('new');
  }

  public function executeEdit(sfWebRequest $request) {
    $this->forward404unless($request->hasParameter('id'));
    $this->item = Doctrine::getTable('AnaItems')->find($request->getParameter('id'));
    $this->forward404unless($this->item);
    $this->form = new AnaItemsForm($this->item, $this->userArr);
  }

  public function executeUpdate(sfWebRequest $request) {
    $this->forward404Unless($request->isMethod('put'));
    $this->forward404unless($request->hasParameter('id'));
    $this->item = Doctrine::getTable('AnaItems')->find($request->getParameter('id'));
    $this->forward404unless($this->item);
    $this->form = new AnaItemsForm($this->item, $this->userArr);
    $this->processForm($request, $this->form, "edit");
    $this->setTemplate('edit');
  }

  protected function processForm(sfWebRequest $request, sfForm $form, $type) {
    $form->bind(
            $request->getParameter($form->getName())
    );
    if ($form->isValid()) {
      $item = $form->save();
      if ($type == "edit")
        $this->getUser()->setFlash("success", "Item has been updated successfully.");
      else
        $this->getUser()->setFlash("success", "Item has been created successfully.");

      $this->redirect('item/edit?id=' . $item->getId());
    }
  }

  /**
   * * remove cients.
   * */
  public function executeRemove(sfWebRequest $request) {
    $this->forward404Unless($request->getParameter('id'));
    $client = Doctrine::getTable('AnaItems')->find($request->getParameter('id'));
    $client->delete();
    $this->getUser()->setFlash("success", "Item has been removed successfully.");
    $this->redirect("item/index?page=" . $request->getParameter("page"));
  }

  /**
   * * remove cients.
   * */
  public function executeChangeStatus(sfWebRequest $request) {
    $this->forward404Unless($request->hasParameter('id'));
    $this->forward404Unless($request->hasParameter('status'));
    $client = Doctrine::getTable('AnaItems')->find($request->getParameter('id'));
    $client->setStatus($request->getParameter("status"));
    $client->save();
    $this->getUser()->setFlash("success", "Item status has been updated successfully.");
    $this->redirect("item/index?page=" . $request->getParameter("page"));
  }

  /**
   * * multiple action : active/inactive/remove/
   * */
  public function executeAction(sfWebRequest $request) {
    $this->forward404Unless($request->hasParameter('action'));
    $action = $request->getParameter('process');
    $all_ides = $request->getParameter("all_ides");

    if (sizeof($all_ides) == 0) {
      $this->getUser()->setFlash("error", "Please select atleast single item to perform this action.");
    } else {
      for ($i = 0; $i < sizeof($all_ides); $i++) {
        if ($action == "actived") {
          $client = Doctrine::getTable('AnaItems')->find($all_ides[$i]);
          $client->setStatus("1");
          $client->save();
        }
        if ($action == "inactived") {
          $client = Doctrine::getTable('AnaItems')->find($all_ides[$i]);
          $client->setStatus(0);
          $client->save();
        }
        if ($action == "removed") {
          $client = Doctrine::getTable('AnaItems')->find($all_ides[$i]);
          $client->delete();
        }
      }
      $this->getUser()->setFlash("success", "All items has been " . $action . " successfully.");
    }
    $this->redirect("item/index?page=" . $request->getParameter("page"));
  }

  public function executeAnalyticsremove(sfWebRequest $request) {
    $this->forward404Unless($request->hasParameter('id'));
    $this->forward404Unless($request->hasParameter('itemid'));
    $client = Doctrine::getTable('AnaItemAnalytics')->find($request->getParameter('id'));
    $client->delete();
    $this->getUser()->setFlash("success", "Record has been removed successfully.");
    $this->redirect('item/showanalytics?id=' . $request->getParameter("itemid") . '&page=' . $request->getParameter("page"));
  }

  /**
   * * multiple analytics : remove
   * */
  public function executeAnalyticsaction(sfWebRequest $request) {
    //  $this->forward404Unless($request->hasParameter('action'));
    $this->forward404Unless($request->hasParameter('itemid'));
    $action = $request->getParameter('process');
    $all_ides = $request->getParameter("all_ides");
    if (sizeof($all_ides) == 0) {
      $this->getUser()->setFlash("error", "Please select atleast single item to perform this action.");
    } else {
      for ($i = 0; $i < sizeof($all_ides); $i++) {
        if ($action == "removed") {
          $client = Doctrine::getTable('AnaItemAnalytics')->find($all_ides[$i]);
          $client->delete();
        }
      }
      $this->getUser()->setFlash("success", "All items has been " . $action . " successfully.");
    }

    $this->redirect('item/showanalytics?id=' . $request->getParameter("itemid") . '&page=' . $request->getParameter("page"));
  }

  public function executeShowanalytics(sfWebRequest $request) {
    $this->forward404Unless($request->hasParameter('id'));
    $this->forward404Unless($this->item = Doctrine::getTable('AnaItems')->find($request->getParameter('id')));
    $this->itemId = $request->getParameter('id');

    $this->pager = new sfDoctrinePager('AnaItemAnalytics', sfConfig::get('app_paging_perpage', 10));
    $this->pager->setQuery(Doctrine_Core::getTable('AnaItemAnalytics')->createQuery('ai')->leftJoin('ai.AnaDevices ad')->leftJoin('ai.AnaBrowsers ab')->leftJoin('ai.AnaPlatforms ap')->where('ai.id_item = ?', $request->getParameter('id'))->orderby('ai.id DESC'));

    $this->pager->setPage($request->getParameter('page', 1));
    //$this->pager->setMaxPerPage($resultsPerPage);
    $this->pager->init();
    $this->cpage = $request->getParameter('page');
    $this->rows = $this->pager->getResults();

    if (sizeof($this->rows) == 0 && $this->cpage > 1)
      $this->redirect('item/showanalytics?page=' . ($this->cpage - 1));
  }

  /**
   * * show chart.
   * */
  public function executeChart(sfWebRequest $request) {
    $this->forward404Unless($request->hasParameter('id'));
		$this->itemId = $request->getParameter('id');
    $countries = Doctrine_Core::getTable('AnaItemAnalytics')->createQuery('ai')
                    ->select('ai.id, ai.id_item, ai.country_name,count(ai.id) as count_by_country')
                    ->where('ai.id_item = ?', $request->getParameter('id'))
                    ->groupBy('ai.country_name')->execute();

    $data = array();
    $data["COLUMNS"] = array("CONTEXT", "X_BAR_LABEL", "X_BAR_VALUE", "TOOL_TIP_TITLE", "DRILL_DATA");

    if (sizeof($countries) > 0) {
      // LEVEL - 1
      $iCount = 0;
      foreach ($countries as $country) {
        //default array
        $stateArr = $this->getState($request->getParameter('id'), $country->getCountryName());
        $data["DATA"][] = array('Country', $country->getCountryName(), $country->getCountByCountry(), $country->getCountryName() . " ^ ", $stateArr);
      }
      $this->jsonData = json_encode($data);
			
			//: by device
			$this->devices = $this->getDevice($this->itemId);
			$this->jsonDevices = json_encode($this->devices);
			
			//: by browsers
			$this->browsers = $this->getBrowser($this->itemId);
			$this->jsonBrowsers = json_encode($this->browsers);
			
    }
		
		
		
  }

	/**
	** get devices
	**/
  protected function getDevice($id) {
    $devices = Doctrine_Core::getTable('AnaItemAnalytics')->createQuery('ai')
            ->select('ai.id, ai.id_item, ai.city_name, ai.country_name, count(ai.id) as count_by_device, ad.device_name')
            ->leftJoin('ai.AnaDevices ad')
            ->where('ai.id_item = ?', $id)
            ->groupBy('ai.id_device')
            ->execute();

    if (sizeof($devices) > 0) {
      $data['COLUMNS'] = array("CONTEXT", "X_BAR_LABEL", "X_BAR_VALUE", "TOOL_TIP_TITLE");

      foreach ($devices as $device) {
        $data['DATA'][] = array('Device', $device->getIdDevice() > 0 ? $device->getAnaDevices()->getDeviceName() : '', $device->getCountByDevice(), $device->getIdDevice() > 0 ? $device->getAnaDevices()->getDeviceName() . " ^ " : '');
      }
      return $data;
    }
    return null;
  }
	
	/**
	** get browsers
	**/
	protected function getBrowser($id) {
    $browsers = Doctrine_Core::getTable('AnaItemAnalytics')->createQuery('ai')
            ->select('ai.id, ai.id_item, count(ai.id) as count_by_browser, ab.browser_name')
            ->leftJoin('ai.AnaBrowsers ab')
            ->where('ai.id_item = ?', $id)
            ->groupBy('ai.id_browser')
            ->execute();

    if (sizeof($browsers) > 0) {
      $data['COLUMNS'] = array("CONTEXT", "X_BAR_LABEL", "X_BAR_VALUE", "TOOL_TIP_TITLE");

      foreach ($browsers as $browser) {
        $data['DATA'][] = array('Browser', $browser->getIdBrowser() > 0 ? $browser->getAnaBrowsers()->getBrowserName() : '', $browser->getCountByBrowser(), $browser->getIdBrowser() > 0 ? $browser->getAnaBrowsers()->getBrowserName() . " ^ " : '');
      }
      return $data;
    }
    return null;
  }


	/**
	** get state.
	**/
	protected function getState($id, $countryName) {
		$states = Doctrine_Core::getTable('AnaItemAnalytics')->createQuery('ai')
										->select('ai.id, ai.id_item, ai.region_name, ai.country_name, count(ai.id) as count_by_region')
										->where('ai.id_item = ?', $id)
										->andWhere('ai.country_name = ?', $countryName)
										->groupBy('ai.region_name')->execute();
		if (sizeof($states) > 0) {
			$data['COLUMNS'] = array("CONTEXT", "X_BAR_LABEL", "X_BAR_VALUE", "TOOL_TIP_TITLE", "DRILL_DATA");
			foreach ($states as $state) {
				$cityArr = $this->getCity($id, $state->getRegionName());
				$data['DATA'][] = array('State', $state->getRegionName(), $state->getCountByRegion(), $state->getRegionName() . " ^ ", $cityArr);
			}
			return $data;
		}
		return null;
	}



	/**
	** get city
	**/ 
	protected function getCity($id, $stateName, $isDevice = false) {
		$cities = Doctrine_Core::getTable('AnaItemAnalytics')->createQuery('ai')
										->select('ai.id, ai.id_item, ai.city_name, ai.region_name, count(ai.id) as count_by_city')
										->where('ai.id_item = ?', $id)
										->andWhere('ai.region_name = ?', $stateName)
										->groupBy('ai.city_name')->execute();

		if (sizeof($cities) > 0) {
			$data['COLUMNS'] = array("CONTEXT", "X_BAR_LABEL", "X_BAR_VALUE", "TOOL_TIP_TITLE");
			if($isDevice) {
				array_push($data['COLUMNS'],"DRILL_DATA");
			}
			foreach ($cities as $city) {
				$deviceArr = $this->getDevice($id, $city->getCityName());
				$data['DATA'][] = array('City', $city->getCityName(), $city->getCountByCity(), $city->getCityName() . " ^ ", $deviceArr);
			}
			return $data;
		}
		return null;
	}

}
