<?php

/**
 * dashboard components.
 *
 * @package    property code
 * @subpackage dashboard
 * @author     Ajay Makwana <ajay@webplanex.net>
 */
class dashboardComponents extends sfComponents {

//   statesticks
  public function executeDashboardstatesticks() {

    if ($this->getUser()->hasCredential('admin')) {
      $this->userCount = Doctrine_Query::create()->from('AnaUsers')->where('user_type = ?',"user")->count();
      $this->projectCount = Doctrine_Query::create()->from('AnaProjects')->count();
      $this->itemCount = Doctrine_Query::create()->from('AnaItems')->count();
    } else {
      $this->projectCount = Doctrine_Query::create()->from('AnaProjects')->where('id_user=?', $this->getUser()->getAttribute('id'))->count();
      $this->itemCount = Doctrine::getTable('AnaItems')->createQuery('ai')->leftJoin('ai.AnaProjects ap')->where('ap.id_user=?', $this->getUser()->getAttribute('id'))->count();
//      $this->itemCount = 0;
    }
  }

}

?>
