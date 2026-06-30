<?php

/**
 * AnaProjects filter form base class.
 *
 * @package    analytics
 * @subpackage filter
 * @author     Ajay Makwana <ajay@webplanex.net>
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseAnaProjectsFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id_user'      => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('AnaUsers'), 'add_empty' => true)),
      'project_name' => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'description'  => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'url'          => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'project_key'  => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'status'       => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'created_at'   => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'updated_at'   => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
    ));

    $this->setValidators(array(
      'id_user'      => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('AnaUsers'), 'column' => 'id')),
      'project_name' => new sfValidatorPass(array('required' => false)),
      'description'  => new sfValidatorPass(array('required' => false)),
      'url'          => new sfValidatorPass(array('required' => false)),
      'project_key'  => new sfValidatorPass(array('required' => false)),
      'status'       => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'created_at'   => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'updated_at'   => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
    ));

    $this->widgetSchema->setNameFormat('ana_projects_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'AnaProjects';
  }

  public function getFields()
  {
    return array(
      'id'           => 'Number',
      'id_user'      => 'ForeignKey',
      'project_name' => 'Text',
      'description'  => 'Text',
      'url'          => 'Text',
      'project_key'  => 'Text',
      'status'       => 'Boolean',
      'created_at'   => 'Date',
      'updated_at'   => 'Date',
    );
  }
}
