<?php

/**
 * AnaProjects form base class.
 *
 * @method AnaProjects getObject() Returns the current form's model object
 *
 * @package    analytics
 * @subpackage form
 * @author     Ajay Makwana <ajay@webplanex.net>
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseAnaProjectsForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'           => new sfWidgetFormInputHidden(),
      'id_user'      => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('AnaUsers'), 'add_empty' => false)),
      'project_name' => new sfWidgetFormInputText(),
      'description'  => new sfWidgetFormTextarea(),
      'url'          => new sfWidgetFormInputText(),
      'project_key'  => new sfWidgetFormInputText(),
      'status'       => new sfWidgetFormInputCheckbox(),
      'created_at'   => new sfWidgetFormDateTime(),
      'updated_at'   => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'           => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'id_user'      => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('AnaUsers'))),
      'project_name' => new sfValidatorString(array('max_length' => 255)),
      'description'  => new sfValidatorString(array('max_length' => 2000)),
      'url'          => new sfValidatorString(array('max_length' => 255)),
      'project_key'  => new sfValidatorString(array('max_length' => 50)),
      'status'       => new sfValidatorBoolean(array('required' => false)),
      'created_at'   => new sfValidatorDateTime(),
      'updated_at'   => new sfValidatorDateTime(),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'AnaProjects', 'column' => array('project_key')))
    );

    $this->widgetSchema->setNameFormat('ana_projects[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'AnaProjects';
  }

}
