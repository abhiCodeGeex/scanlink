<?php

/**
 * AnaItemAnalytics form base class.
 *
 * @method AnaItemAnalytics getObject() Returns the current form's model object
 *
 * @package    analytics
 * @subpackage form
 * @author     Ajay Makwana <ajay@webplanex.net>
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseAnaItemAnalyticsForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'              => new sfWidgetFormInputHidden(),
      'id_item'         => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('AnaItems'), 'add_empty' => false)),
      'country_code'    => new sfWidgetFormInputText(),
      'country_name'    => new sfWidgetFormInputText(),
      'region_name'     => new sfWidgetFormInputText(),
      'city_name'       => new sfWidgetFormInputText(),
      'zipcode'         => new sfWidgetFormInputText(),
      'latitude'        => new sfWidgetFormInputText(),
      'longitude'       => new sfWidgetFormInputText(),
      'ip_add'          => new sfWidgetFormInputText(),
      'timezone'        => new sfWidgetFormInputText(),
      'id_browser'      => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('AnaBrowsers'), 'add_empty' => true)),
      'browser_version' => new sfWidgetFormInputText(),
      'id_platform'     => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('AnaPlatforms'), 'add_empty' => true)),
      'id_device'       => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('AnaDevices'), 'add_empty' => true)),
      'scan_type'       => new sfWidgetFormInputText(),
      'created_at'      => new sfWidgetFormDateTime(),
      'updated_at'      => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'              => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'id_item'         => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('AnaItems'))),
      'country_code'    => new sfValidatorString(array('max_length' => 20, 'required' => false)),
      'country_name'    => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'region_name'     => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'city_name'       => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'zipcode'         => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'latitude'        => new sfValidatorString(array('max_length' => 50, 'required' => false)),
      'longitude'       => new sfValidatorString(array('max_length' => 50, 'required' => false)),
      'ip_add'          => new sfValidatorString(array('max_length' => 50, 'required' => false)),
      'timezone'        => new sfValidatorString(array('max_length' => 50, 'required' => false)),
      'id_browser'      => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('AnaBrowsers'), 'required' => false)),
      'browser_version' => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'id_platform'     => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('AnaPlatforms'), 'required' => false)),
      'id_device'       => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('AnaDevices'), 'required' => false)),
      'scan_type'       => new sfValidatorPass(array('required' => false)),
      'created_at'      => new sfValidatorDateTime(),
      'updated_at'      => new sfValidatorDateTime(),
    ));

    $this->widgetSchema->setNameFormat('ana_item_analytics[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'AnaItemAnalytics';
  }

}
