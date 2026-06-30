<?php

/**
 * AnaProjects form.
 *
 * @package    analytics
 * @subpackage form
 * @author     Ajay Makwana <ajay@webplanex.net>
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class AnaProjectsForm extends BaseAnaProjectsForm {

  public function configure() {
    unset($this['id'], $this['created_at'], $this['updated_at'], $this['project_key']);

    if (is_array($this->getOption('user_type')) && in_array('user', $this->getOption('user_type'))) {
      unset($this['id_user']);
    } else {
      $this->widgetSchema['id_user'] = new sfWidgetFormDoctrineChoice(
                      array(
                          'model' => 'AnaUsers',
                          'add_empty' => true,
                          'table_method' => 'getClientList'
                      )
      );
    }

    $this->getWidgetSchema()->setNameFormat('project[%s]');
    $this->validatorSchema['url'] = new urlValidator(array('required' => true), array('invalid' => 'Please enter only valid url'));
  }

  public function doSave($conn = null) {
    if ($this->isNew()) {
      $this->getObject()->setIdUser($this->getOption('id_user'));
      $this->getObject()->setProjectKey(md5($this->getObject()->getUrl() . date('Y-m-dh:i:s')));
    }
    parent::doSave($conn);
  }

}
