<?php

/**
 * AnaItemAnalytics filter form base class.
 *
 * @package    analytics
 * @subpackage filter
 * @author     Ajay Makwana <ajay@webplanex.net>
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseAnaItemAnalyticsFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id_item'         => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('AnaItems'), 'add_empty' => true)),
      'country_code'    => new sfWidgetFormFilterInput(),
      'country_name'    => new sfWidgetFormFilterInput(),
      'region_name'     => new sfWidgetFormFilterInput(),
      'city_name'       => new sfWidgetFormFilterInput(),
      'zipcode'         => new sfWidgetFormFilterInput(),
      'latitude'        => new sfWidgetFormFilterInput(),
      'longitude'       => new sfWidgetFormFilterInput(),
      'ip_add'          => new sfWidgetFormFilterInput(),
      'timezone'        => new sfWidgetFormFilterInput(),
      'id_browser'      => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('AnaBrowsers'), 'add_empty' => true)),
      'browser_version' => new sfWidgetFormFilterInput(),
      'id_platform'     => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('AnaPlatforms'), 'add_empty' => true)),
      'id_device'       => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('AnaDevices'), 'add_empty' => true)),
      'scan_type'       => new sfWidgetFormFilterInput(),
      'created_at'      => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'updated_at'      => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
    ));

    $this->setValidators(array(
      'id_item'         => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('AnaItems'), 'column' => 'id')),
      'country_code'    => new sfValidatorPass(array('required' => false)),
      'country_name'    => new sfValidatorPass(array('required' => false)),
      'region_name'     => new sfValidatorPass(array('required' => false)),
      'city_name'       => new sfValidatorPass(array('required' => false)),
      'zipcode'         => new sfValidatorPass(array('required' => false)),
      'latitude'        => new sfValidatorPass(array('required' => false)),
      'longitude'       => new sfValidatorPass(array('required' => false)),
      'ip_add'          => new sfValidatorPass(array('required' => false)),
      'timezone'        => new sfValidatorPass(array('required' => false)),
      'id_browser'      => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('AnaBrowsers'), 'column' => 'id')),
      'browser_version' => new sfValidatorPass(array('required' => false)),
      'id_platform'     => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('AnaPlatforms'), 'column' => 'id')),
      'id_device'       => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('AnaDevices'), 'column' => 'id')),
      'scan_type'       => new sfValidatorPass(array('required' => false)),
      'created_at'      => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'updated_at'      => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
    ));

    $this->widgetSchema->setNameFormat('ana_item_analytics_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'AnaItemAnalytics';
  }

  public function getFields()
  {
    return array(
      'id'              => 'Number',
      'id_item'         => 'ForeignKey',
      'country_code'    => 'Text',
      'country_name'    => 'Text',
      'region_name'     => 'Text',
      'city_name'       => 'Text',
      'zipcode'         => 'Text',
      'latitude'        => 'Text',
      'longitude'       => 'Text',
      'ip_add'          => 'Text',
      'timezone'        => 'Text',
      'id_browser'      => 'ForeignKey',
      'browser_version' => 'Text',
      'id_platform'     => 'ForeignKey',
      'id_device'       => 'ForeignKey',
      'scan_type'       => 'Text',
      'created_at'      => 'Date',
      'updated_at'      => 'Date',
    );
  }
}
