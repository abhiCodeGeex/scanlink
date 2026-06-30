<?php

/**
 * browser actions.
 *
 * @package    analytics
 * @subpackage browser
 * @author     Ajay Makwana <ajay@webplanex.net>
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class browserActions extends sfActions {

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
    $this->pager = new sfDoctrinePager('AnaBrowsers', sfConfig::get('app_paging_perpage', 10));
    $this->pager->setQuery(Doctrine_Core::getTable('AnaBrowsers')->createQuery('b')->orderby('b.id DESC'));
    $this->pager->setPage($request->getParameter('page', 1));
    //$this->pager->setMaxPerPage($resultsPerPage);
    $this->pager->init();
    $this->cpage = $request->getParameter('page');
    $this->browsers = $this->pager->getResults();
    if (sizeof($this->items) == 0 && $this->cpage > 1)
      $this->redirect('browser/index?page=' . ($this->cpage - 1));
  }

  /**
   * * new item.
   * */
  public function executeNew(sfWebRequest $request) {
    $this->form = new AnaBrowsersForm(null, $this->userArr);
  }

  public function executeCreate(sfWebRequest $request) {
    $this->form = new AnaBrowsersForm(null, $this->userArr);
    if ($request->isMethod('post') == true) {
      $this->processForm($request, $this->form, "new");
    }
    $this->setTemplate('new');
  }

  public function executeEdit(sfWebRequest $request) {
    $this->forward404unless($request->hasParameter('id'));
    $this->browser = Doctrine::getTable('AnaBrowsers')->find($request->getParameter('id'));
    $this->forward404unless($this->browser);
    $this->form = new AnaBrowsersForm($this->browser, $this->userArr);
  }

  public function executeUpdate(sfWebRequest $request) {
    $this->forward404Unless($request->isMethod('put'));
    $this->forward404unless($request->hasParameter('id'));
    $this->item = Doctrine::getTable('AnaBrowsers')->find($request->getParameter('id'));
    $this->forward404unless($this->item);
    $this->form = new AnaBrowsersForm($this->item, $this->userArr);
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
        $this->getUser()->setFlash("success", "Browser has been updated successfully.");
      else
        $this->getUser()->setFlash("success", "Browser has been created successfully.");

      $this->redirect('browser/edit?id=' . $item->getId());
    }
  }

  /**
   * * remove cients.
   * */
  public function executeRemove(sfWebRequest $request) {
    $this->forward404Unless($request->getParameter('id'));
    $browser = Doctrine::getTable('AnaBrowsers')->find($request->getParameter('id'));
    $browser->delete();
    $this->getUser()->setFlash("success", "Browser has been removed successfully.");
    $this->redirect("browser/index?page=" . $request->getParameter("page"));
  }

  /**
   * * remove cients.
   * */
  public function executeChangeStatus(sfWebRequest $request) {
    $this->forward404Unless($request->hasParameter('id'));
    $this->forward404Unless($request->hasParameter('status'));
    $browser = Doctrine::getTable('AnaBrowsers')->find($request->getParameter('id'));
    $browser->setStatus($request->getParameter("status"));
    $browser->save();
    $this->getUser()->setFlash("success", "Browser status has been updated successfully.");
    $this->redirect("browser/index?page=" . $request->getParameter("page"));
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
          $browser = Doctrine::getTable('AnaBrowsers')->find($all_ides[$i]);
          $browser->setStatus("1");
          $browser->save();
        }
        if ($action == "inactived") {
          $browser = Doctrine::getTable('AnaBrowsers')->find($all_ides[$i]);
          $browser->setStatus(0);
          $browser->save();
        }
        if ($action == "removed") {
          $browser = Doctrine::getTable('AnaBrowsers')->find($all_ides[$i]);
          $browser->delete();
        }
      }
      $this->getUser()->setFlash("success", "All browser has been " . $action . " successfully.");
    }
    $this->redirect("browser/index?page=" . $request->getParameter("page"));
  }

}
