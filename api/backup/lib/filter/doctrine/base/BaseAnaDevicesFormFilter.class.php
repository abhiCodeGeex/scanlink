<?php

/**
 * AnaDevices filter form base class.
 *
 * @package    analytics
 * @subpackage filter
 * @author     Ajay Makwana <ajay@webplanex.net>
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseAnaDevicesFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'match_word'  => new sfWidgetFormFilterInput(),
      'device_name' => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'match_word'  => new sfValidatorPass(array('required' => false)),
      'device_name' => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('ana_devices_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'AnaDevices';
  }

  public function getFields()
  {
    return array(
      'id'          => 'Number',
      'match_word'  => 'Text',
      'device_name' => 'Text',
    );
  }
}
