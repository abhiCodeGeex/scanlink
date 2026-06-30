<?php

/**
 * AnaUserLogin filter form base class.
 *
 * @package    analytics
 * @subpackage filter
 * @author     Ajay Makwana <ajay@webplanex.net>
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseAnaUserLoginFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id_user'    => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('AnaUsers'), 'add_empty' => true)),
      'ip_add'     => new sfWidgetFormFilterInput(),
      'login_time' => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
    ));

    $this->setValidators(array(
      'id_user'    => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('AnaUsers'), 'column' => 'id')),
      'ip_add'     => new sfValidatorPass(array('required' => false)),
      'login_time' => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
    ));

    $this->widgetSchema->setNameFormat('ana_user_login_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'AnaUserLogin';
  }

  public function getFields()
  {
    return array(
      'id'         => 'Number',
      'id_user'    => 'ForeignKey',
      'ip_add'     => 'Text',
      'login_time' => 'Date',
    );
  }
}
