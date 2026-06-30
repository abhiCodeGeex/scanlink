<?php

/**
 * AnaItems form.
 *
 * @package    analytics
 * @subpackage form
 * @author     Ajay Makwana <ajay@webplanex.net>
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class AnaItemsForm extends BaseAnaItemsForm {

  public function configure() {
    unset($this['id'], $this['created_at'], $this['updated_at'], $this['status'], $this['item_key']);


    if (is_array($this->getOption('user_type')) && in_array('admin', $this->getOption('user_type'))) {
      $array = array(
          'model' => 'AnaProjects',
          'add_empty' => true,
      );
    } else {
      $array = array(
          'model' => 'AnaProjects',
          'query' => Doctrine_Core::getTable('AnaProjects')
                  ->createQuery('p')
                  ->select('p.id, p.project_name')
                  ->where('p.id_user=?', $this->getOption('id_user'))
                  ->orderBy('p.project_name'),
          'add_empty' => true,
      );
    }
    $this->widgetSchema['id_project'] = new sfWidgetFormDoctrineChoice($array);
  }

  /*
   * @method      :           doSave
   * @author      :           Ajay Makwana
   * @desc        :           Override doSave method to set activations key
   */

  protected function doSave($con = null) {
    // check for new client
    if ($this->isNew()) {
      $this->getObject()->setItemKey(md5($this->getObject()->getTitle() . date('Y-m-dh:i:s')));
      $this->getObject()->setStatus(1);
    }
    parent::doSave($con);
  }

}
