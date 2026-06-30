<?php

/**
 * client actions.
 *
 * @package    analytics
 * @subpackage client
 * @author     Ajay Makwana <ajay@webplanex.net>
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class clientActions extends sfActions {

  /**
   * Executes index action
   *
   * @param sfRequest $request A request object
   */
  public function executeIndex(sfWebRequest $request) {
    $resultsPerPage = 10;
    $this->pager = new sfDoctrinePager('AnaUsers', sfConfig::get('app_max_jobs_on_homepage'));
    $this->pager->setQuery(Doctrine_Core::getTable('AnaUsers')->createQuery('u')->where('u.user_type = ?', 'user')->orderby('u.id DESC'));
    $this->pager->setPage($request->getParameter('page', 1));
    $this->pager->setMaxPerPage($resultsPerPage);
    $this->pager->init();
    $this->cpage = $request->getParameter('page');
    $this->clients = $this->pager->getResults();
    if (sizeof($this->clients) == 0 && $this->cpage > 1)
      $this->redirect('client/index?page=' . ($this->cpage - 1));
  }

  public function executeNew(sfWebRequest $request) {
    $this->form = new AnaUsersForm();
  }

  public function executeCreate(sfWebRequest $request) {
    $this->form = new AnaUsersForm();
    if ($request->isMethod('post') == true) {
      $this->processForm($request, $this->form, 'add');
    }
    $this->setTemplate('new');
  }

  public function executeEdit(sfWebRequest $request) {
    $this->forward404unless($request->hasParameter('id'));
    $this->client = Doctrine::getTable('AnaUsers')->find($request->getParameter('id'));
    $this->forward404Unless($this->client);
    $this->form = new AnaUsersForm($this->client);
  }

  public function executeUpdate(sfWebRequest $request) {
    $this->forward404Unless($request->isMethod('put'));
    $this->forward404Unless($request->hasParameter('id'));
    $this->client = Doctrine::getTable('AnaUsers')->find($request->getParameter('id'));
    $this->forward404Unless($this->client);
    $this->form = new AnaUsersForm($this->client);
    $this->processForm($request, $this->form, 'edit');
    $this->setTemplate('edit');
  }

  protected function processForm(sfWebRequest $request, sfForm $form, $action = null) {
    $form->bind(
            $request->getParameter($form->getName())
    );
    if ($form->isValid()) {
      $client = $form->save();
      if ($action == 'add') {
        $this->getUser()->setFlash("success", "Client has been added successfully.");
      } else if ($action == 'edit') {
        $this->getUser()->setFlash("success", "Client has been edited successfully.");
      }
      $this->redirect('client/edit?id=' . $client->getId());
    }
  }

  /**
   * * remove clients.
   * */
  public function executeRemove(sfWebRequest $request) {
    $this->forward404Unless($request->getParameter('id'));
    $client = Doctrine::getTable('AnaUsers')->find($request->getParameter('id'));
    $client->delete();
    $this->getUser()->setFlash("success", "Client has been removed successfully.");
    $this->redirect("client/index?page=" . $request->getParameter("page"));
  }

  /**
   * * remove Clients.
   * */
  public function executeChangeStatus(sfWebRequest $request) {
    $this->forward404Unless($request->hasParameter('id'));
    $this->forward404Unless($request->hasParameter('status'));
    $client = Doctrine::getTable('AnaUsers')->find($request->getParameter('id'));
    $client->setStatus($request->getParameter("status"));
    $client->save();
    $this->getUser()->setFlash("success", "Client status has been updated successfully.");
    $this->redirect("client/index?page=" . $request->getParameter("page"));
  }

  /**
   * * multiple action : active/inactive/remove/
   * */
  public function executeAction(sfWebRequest $request) {
    $this->forward404Unless($request->hasParameter('action'));
    $action = $request->getParameter('process');
    $clients = $request->getParameter("clients");

    if (sizeof($clients) == 0) {
      $this->getUser()->setFlash("error", "Please select atleast single client to perform this action.");
    } else {
      for ($i = 0; $i < sizeof($clients); $i++) {
        if ($action == "actived") {
          $client = Doctrine::getTable('AnaUsers')->find($clients[$i]);
          $client->setStatus("1");
          $client->save();
        }
        if ($action == "inactived") {
          $client = Doctrine::getTable('AnaUsers')->find($clients[$i]);
          $client->setStatus(0);
          $client->save();
        }
        if ($action == "removed") {
          $client = Doctrine::getTable('AnaUsers')->find($clients[$i]);
          $client->delete();
        }
      }
      $this->getUser()->setFlash("success", "Clients has been " . $action . " successfully.");
    }
    $this->redirect("client/index?page=" . $request->getParameter("page"));
  }
	
	/**
	** login report.
	**/
	public function executeLoginreport(sfWebRequest $request) {
		$resultsPerPage = 10;
    $this->pager = new sfDoctrinePager('AnaUserLogin', sfConfig::get('app_max_jobs_on_homepage'));
		$this->search = '';
		if($request->hasParameter("search"))
			$this->search = $request->getParameter("search");
			
		$dQuery = Doctrine_Query::create()->select('u.*, ap.username')->from('AnaUserLogin u')->leftJoin('u.AnaUsers ap');
		if (!$this->getUser()->hasCredential('admin')){
			$dQuery->where('u.id_user = ?',$this->getUser()->getAttribute('id'));
		}
		
		if ($this->getUser()->hasCredential('admin') && $this->search !== ""){
			$dQuery->andWhere('ap.username like  ?','%'. $this->search.'%');
		}
		$this->pager->setQuery($dQuery);
    $this->pager->setPage($request->getParameter('page', 1));
    $this->pager->setMaxPerPage($resultsPerPage);
    $this->pager->init();
    $this->cpage = $request->getParameter('page');
    $this->reports = $this->pager->getResults();
		
    if (sizeof($this->reports) == 0 && $this->cpage > 1)
      $this->redirect('client/loginreport?page=' . ($this->cpage - 1));
	}
	
	/**
	** multiple action : active/inactive/remove/
	**/
	public function executeReportaction(sfWebRequest $request) {
		$this->forward404Unless($request->hasParameter('process'));
		$action = $request->getParameter('process');
		$all_ides = $request->getParameter("all_ides");
		
		if( sizeof($all_ides) == 0 ){
			$this->getUser()->setFlash("error","Please select atleast single item to perform this action.");
		}else{
				for($i = 0; $i < sizeof($all_ides); $i++){
					if($action == "removed"){
						$client = Doctrine::getTable('AnaUserLogin')->find($all_ides[$i]);	
						$client->delete();	
					}
				}
				$this->getUser()->setFlash("success","All items has been ".$action. " successfully.");
		}
		$this->redirect("client/loginreport?page=".$request->getParameter("page"));
	}
	
	/**
   * * remove cients.
   * */
  public function executeRemovereport(sfWebRequest $request) {
    $this->forward404Unless($request->getParameter('id'));
    $client = Doctrine::getTable('AnaUserLogin')->find($request->getParameter('id'));
    $client->delete();
    $this->getUser()->setFlash("success", "Item has been removed successfully.");
    $this->redirect("client/loginreport?page=" . $request->getParameter("page"));
  }
  public function executeChangepassword(sfWebRequest $request) {
		 $this->userdetail = Doctrine::getTable('AnaUsers')->find($this->getUser()->getAttribute('id'));
		 $this->changePasswordForm = new changepassForm();
  }
  
  public function executeSavePassword(sfWebRequest $request){
		$this->userdetail = Doctrine::getTable('AnaUsers')->find($this->getUser()->getAttribute('id'));
		$this->changePasswordForm = new changepassForm();
		
		if($request->isMethod('post')) {
			$this->changePasswordForm->bind($request->getParameter('changepass'));
     		if($this->changePasswordForm->isValid()) {
				$formData = $this->changePasswordForm->getValues();
      			$data = array();
				$data['password'] = md5($formData['new_password']);
				$proPlanObj = Doctrine::getTable('AnaUsers')->find($this->getUser()->getAttribute('id'));
				$proPlanObj->fromArray($data);
				$proPlanObj->save();
				$this->getUser()->setFlash("success","Password changed successfully.");
				$this->redirect("dashboard/index");
      		}
		}
		$this->setTemplate('changepassword');
  }

}
