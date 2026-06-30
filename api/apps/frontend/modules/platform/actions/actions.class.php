<?php

/**
 * platform actions.
 *
 * @package    analytics
 * @subpackage platform
 * @author     Ajay Makwana <ajay@webplanex.net>
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class platformActions extends sfActions {

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
    $this->pager = new sfDoctrinePager('AnaPlatforms', sfConfig::get('app_paging_perpage', 10));
    $this->pager->setQuery(Doctrine_Core::getTable('AnaPlatforms')->createQuery('b')->orderby('b.id DESC'));
    $this->pager->setPage($request->getParameter('page', 1));
    //$this->pager->setMaxPerPage($resultsPerPage);
    $this->pager->init();
    $this->cpage = $request->getParameter('page');
    $this->platforms = $this->pager->getResults();
    if (sizeof($this->items) == 0 && $this->cpage > 1)
      $this->redirect('platform/index?page=' . ($this->cpage - 1));
  }

  /**
   * * new platform.
   * */
  public function executeNew(sfWebRequest $request) {
    $this->form = new AnaPlatformsForm(null, $this->userArr);
  }

  public function executeCreate(sfWebRequest $request) {
    $this->form = new AnaPlatformsForm(null, $this->userArr);
    if ($request->isMethod('post') == true) {
      $this->processForm($request, $this->form, "new");
    }
    $this->setTemplate('new');
  }

  public function executeEdit(sfWebRequest $request) {
    $this->forward404unless($request->hasParameter('id'));
    $this->platform = Doctrine::getTable('AnaPlatforms')->find($request->getParameter('id'));
    $this->forward404unless($this->platform);
    $this->form = new AnaPlatformsForm($this->platform, $this->userArr);
  }

  public function executeUpdate(sfWebRequest $request) {
    $this->forward404Unless($request->isMethod('put'));
    $this->forward404unless($request->hasParameter('id'));
    $this->platform = Doctrine::getTable('AnaPlatforms')->find($request->getParameter('id'));
    $this->forward404unless($this->platform);
    $this->form = new AnaPlatformsForm($this->platform, $this->userArr);
    $this->processForm($request, $this->form, "edit");
    $this->setTemplate('edit');
  }

  protected function processForm(sfWebRequest $request, sfForm $form, $type) {
    $form->bind(
            $request->getParameter($form->getName())
    );
    if ($form->isValid()) {
      $platform = $form->save();
      if ($type == "edit")
        $this->getUser()->setFlash("success", "Platform has been updated successfully.");
      else
        $this->getUser()->setFlash("success", "Platform has been created successfully.");

      $this->redirect('platform/edit?id=' . $platform->getId());
    }
  }

  /**
   * * remove cients.
   * */
  public function executeRemove(sfWebRequest $request) {
    $this->forward404Unless($request->getParameter('id'));
    $platform = Doctrine::getTable('AnaPlatforms')->find($request->getParameter('id'));
    $platform->delete();
    $this->getUser()->setFlash("success", "Platform has been removed successfully.");
    $this->redirect("platform/index?page=" . $request->getParameter("page"));
  }

  /**
   * * remove cients.
   * */
  public function executeChangeStatus(sfWebRequest $request) {
    $this->forward404Unless($request->hasParameter('id'));
    $this->forward404Unless($request->hasParameter('status'));
    $platform = Doctrine::getTable('AnaPlatforms')->find($request->getParameter('id'));
    $platform->setStatus($request->getParameter("status"));
    $platform->save();
    $this->getUser()->setFlash("success", "Platform status has been updated successfully.");
    $this->redirect("platform/index?page=" . $request->getParameter("page"));
  }

  /**
   * * multiple action : active/inactive/remove/
   * */
  public function executeAction(sfWebRequest $request) {
    $this->forward404Unless($request->hasParameter('action'));
    $action = $request->getParameter('process');
    $all_ides = $request->getParameter("all_ides");

    if (sizeof($all_ides) == 0) {
      $this->getUser()->setFlash("error", "Please select atleast single platform to perform this action.");
    } else {
      for ($i = 0; $i < sizeof($all_ides); $i++) {
        if ($action == "actived") {
          $platform = Doctrine::getTable('AnaPlatforms')->find($all_ides[$i]);
          $platform->setStatus("1");
          $platform->save();
        }
        if ($action == "inactived") {
          $platform = Doctrine::getTable('AnaPlatforms')->find($all_ides[$i]);
          $platform->setStatus(0);
          $platform->save();
        }
        if ($action == "removed") {
          $platform = Doctrine::getTable('AnaPlatforms')->find($all_ides[$i]);
          $platform->delete();
        }
      }
      $this->getUser()->setFlash("success", "All platform has been " . $action . " successfully.");
    }
    $this->redirect("platform/index?page=" . $request->getParameter("page"));
  }

}
