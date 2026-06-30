<?php

/**
 * device actions.
 *
 * @package    analytics
 * @subpackage device
 * @author     Ajay Makwana <ajay@webplanex.net>
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class deviceActions extends sfActions {

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
    $this->pager = new sfDoctrinePager('AnaDevices', sfConfig::get('app_paging_perpage', 10));
    $this->pager->setQuery(Doctrine_Core::getTable('AnaDevices')->createQuery('b')->orderby('b.id DESC'));
    $this->pager->setPage($request->getParameter('page', 1));
    //$this->pager->setMaxPerPage($resultsPerPage);
    $this->pager->init();
    $this->cpage = $request->getParameter('page');
    $this->devices = $this->pager->getResults();
    if (sizeof($this->items) == 0 && $this->cpage > 1)
      $this->redirect('device/index?page=' . ($this->cpage - 1));
  }

  /**
   * * new device.
   * */
  public function executeNew(sfWebRequest $request) {
    $this->form = new AnaDevicesForm(null, $this->userArr);
  }

  public function executeCreate(sfWebRequest $request) {
    $this->form = new AnaDevicesForm(null, $this->userArr);
    if ($request->isMethod('post') == true) {
      $this->processForm($request, $this->form, "new");
    }
    $this->setTemplate('new');
  }

  public function executeEdit(sfWebRequest $request) {
    $this->forward404unless($request->hasParameter('id'));
    $this->device = Doctrine::getTable('AnaDevices')->find($request->getParameter('id'));
    $this->forward404unless($this->device);
    $this->form = new AnaDevicesForm($this->device, $this->userArr);
  }

  public function executeUpdate(sfWebRequest $request) {
    $this->forward404Unless($request->isMethod('put'));
    $this->forward404unless($request->hasParameter('id'));
    $this->device = Doctrine::getTable('AnaDevices')->find($request->getParameter('id'));
    $this->forward404unless($this->device);
    $this->form = new AnaDevicesForm($this->device, $this->userArr);
    $this->processForm($request, $this->form, "edit");
    $this->setTemplate('edit');
  }

  protected function processForm(sfWebRequest $request, sfForm $form, $type) {
    $form->bind(
            $request->getParameter($form->getName())
    );
    if ($form->isValid()) {
      $device = $form->save();
      if ($type == "edit")
        $this->getUser()->setFlash("success", "Device has been updated successfully.");
      else
        $this->getUser()->setFlash("success", "Device has been created successfully.");

      $this->redirect('device/edit?id=' . $device->getId());
    }
  }

  /**
   * * remove cients.
   * */
  public function executeRemove(sfWebRequest $request) {
    $this->forward404Unless($request->getParameter('id'));
    $device = Doctrine::getTable('AnaDevices')->find($request->getParameter('id'));
    $device->delete();
    $this->getUser()->setFlash("success", "Device has been removed successfully.");
    $this->redirect("device/index?page=" . $request->getParameter("page"));
  }

  /**
   * * remove cients.
   * */
  public function executeChangeStatus(sfWebRequest $request) {
    $this->forward404Unless($request->hasParameter('id'));
    $this->forward404Unless($request->hasParameter('status'));
    $device = Doctrine::getTable('AnaDevices')->find($request->getParameter('id'));
    $device->setStatus($request->getParameter("status"));
    $device->save();
    $this->getUser()->setFlash("success", "Device status has been updated successfully.");
    $this->redirect("device/index?page=" . $request->getParameter("page"));
  }

  /**
   * * multiple action : active/inactive/remove/
   * */
  public function executeAction(sfWebRequest $request) {
    $this->forward404Unless($request->hasParameter('action'));
    $action = $request->getParameter('process');
    $all_ides = $request->getParameter("all_ides");

    if (sizeof($all_ides) == 0) {
      $this->getUser()->setFlash("error", "Please select atleast single device to perform this action.");
    } else {
      for ($i = 0; $i < sizeof($all_ides); $i++) {
        if ($action == "actived") {
          $device = Doctrine::getTable('AnaDevices')->find($all_ides[$i]);
          $device->setStatus("1");
          $device->save();
        }
        if ($action == "inactived") {
          $device = Doctrine::getTable('AnaDevices')->find($all_ides[$i]);
          $device->setStatus(0);
          $device->save();
        }
        if ($action == "removed") {
          $device = Doctrine::getTable('AnaDevices')->find($all_ides[$i]);
          $device->delete();
        }
      }
      $this->getUser()->setFlash("success", "All device has been " . $action . " successfully.");
    }
    $this->redirect("device/index?page=" . $request->getParameter("page"));
  }

}
