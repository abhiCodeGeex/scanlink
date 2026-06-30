<?php

/**
 * project actions.
 *
 * @package    analytics
 * @subpackage project
 * @author     Ajay Makwana <ajay@webplanex.net>
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class projectActions extends sfActions {

  /**
   * Executes index action
   *
   * @param sfRequest $request A request object
   */
  protected $userArr = array();

  public function preExecute() {
    $this->userArr = array('user_type' => $this->getUser()->getCredentials(), 'id_user' => $this->getUser()->getAttribute('id'));
  }

  public function executeIndex(sfWebRequest $request) {
    $this->pager = new sfDoctrinePager('AnaProjects', sfConfig::get('app_paging_perpage', 10));

    //Get all projects if user has admin credentials
    if ($this->getUser()->hasCredential('admin')) {
      $this->pager->setQuery(Doctrine_Core::getTable('AnaProjects')->createQuery('p')->leftJoin('p.AnaUsers u')->orderby('p.id DESC'));
    } elseif ($this->getUser()->hasCredential('user')) { //Get only limited projects if user has user credentials
      $this->pager->setQuery(Doctrine_Core::getTable('AnaProjects')->createQuery('p')->leftJoin('p.AnaUsers u')->where('p.id_user=?', $this->getUser()->getAttribute('id'))->orderby('p.id DESC'));
    }

    $this->pager->setPage($request->getParameter('page', 1));
    //$this->pager->setMaxPerPage($resultsPerPage);
    $this->pager->init();
    $this->cpage = $request->getParameter('page');
    $this->projects = $this->pager->getResults();
  }

  public function executeNew(sfWebRequest $request) {
    $this->form = new AnaProjectsForm(null, $this->userArr);
  }

  public function executeCreate(sfWebRequest $request) {
    $this->form = new AnaProjectsForm(null, $this->userArr);
    if ($request->isMethod('post') == true) {
      $this->processForm($request, $this->form, 'add');
    }
    $this->setTemplate('new');
  }

  public function executeEdit(sfWebRequest $request) {
    $this->forward404unless($request->hasParameter('id'));
    $this->project = Doctrine::getTable('AnaProjects')->find($request->getParameter('id'));
    $this->forward404unless($this->project);
    $this->form = new AnaProjectsForm($this->project, $this->userArr);
  }

  public function executeUpdate(sfWebRequest $request) {
    $this->forward404Unless($request->isMethod('put'));
    $this->forward404unless($request->hasParameter('id'));
    $this->project = Doctrine::getTable('AnaProjects')->find($request->getParameter('id'));
    $this->forward404unless($this->project);
    $this->form = new AnaProjectsForm($this->project, $this->userArr);
    $this->processForm($request, $this->form, 'edit');
    $this->setTemplate('edit');
  }

  /**
   * * remove cients.
   * */
  public function executeRemove(sfWebRequest $request) {
    $this->forward404Unless($request->getParameter('id'));
    $project = Doctrine::getTable('AnaProjects')->find($request->getParameter('id'));
    $project->delete();
    $this->getUser()->setFlash("success", "Project has been removed successfully.");
    $this->redirect("project/index?page=" . $request->getParameter("page"));
  }

  protected function processForm(sfWebRequest $request, sfForm $form, $action) {
    $form->bind(
            $request->getParameter($form->getName())
    );
    if ($form->isValid()) {
      $project = $form->save();
      if ($action == 'add') {
        $this->getUser()->setFlash("success", "Project has been add successfully.");
      } elseif ($action == 'edit') {
        $this->getUser()->setFlash("success", "Project has been edit successfully.");
      }
      $this->redirect('project/edit?id=' . $project->getId());
    }
  }

  public function executeChangeStatus(sfWebRequest $request) {
    $this->forward404Unless($request->hasParameter('id'));
    $this->forward404Unless($request->hasParameter('status'));
    $project = Doctrine::getTable('AnaProjects')->find($request->getParameter('id'));
    $project->setStatus($request->getParameter("status"));
    $project->save();
    $this->getUser()->setFlash("success", "Project status has been updated successfully.");
    $this->redirect("project/index?page=" . $request->getParameter("page"));
  }

  /**
   * * multiple action : active/inactive/remove/
   * */
  public function executeAction(sfWebRequest $request) {
    $this->forward404Unless($request->hasParameter('action'));
    $action = $request->getParameter('process');
    $projects = $request->getParameter("projects");

    if (sizeof($projects) == 0) {
      $this->getUser()->setFlash("error", "Please select atleast single client to perform this action.");
    } else {
      for ($i = 0; $i < sizeof($projects); $i++) {
        if ($action == "actived") {
          $project = Doctrine::getTable('AnaProjects')->find($projects[$i]);
          $project->setStatus("1");
          $project->save();
        }
        if ($action == "inactived") {
          $project = Doctrine::getTable('AnaProjects')->find($projects[$i]);
          $project->setStatus(0);
          $project->save();
        }
        if ($action == "removed") {
          $project = Doctrine::getTable('AnaProjects')->find($projects[$i]);
          $project->delete();
        }
      }
      $this->getUser()->setFlash("success", "All projects has been " . $action . " successfully.");
    }
    $this->redirect("project/index?page=" . $request->getParameter("page"));
  }
}